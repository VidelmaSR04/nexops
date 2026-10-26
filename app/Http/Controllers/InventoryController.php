<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InventoryController extends Controller
{
    public function index()
    {
        $products = $this->itemsForDisplay()
            ->orderBy('sku')
            ->get()
            ->map(fn ($item) => $this->formatItem($item))
            ->toArray();
        $warehouses = Warehouse::orderBy('name')->get();
        $stockLevels = collect($products)->pluck('stock_level');

        $summary = [
            'total_products' => InventoryItem::count(),
            'total_stock' => WarehouseStock::sum('quantity'),
            'low_stock' => $stockLevels->filter(fn ($level) => in_array($level, ['Menipis', 'Habis'], true))->count(),
            'out_of_stock' => $stockLevels->filter(fn ($level) => $level === 'Habis')->count(),
            'warehouse' => $warehouses->count(),
        ];

        $highest = InventoryItem::withTrashed()->pluck('sku')->map(function ($sku) {
            return preg_match('/^PRD-(\d+)$/', $sku, $matches) ? (int) $matches[1] : 0;
        })->max() ?? 0;
        $skuPreview = 'PRD-'.str_pad((string) ($highest + 1), 3, '0', STR_PAD_LEFT);

        return view('inventory', [
            'products' => $products,
            'summary' => $summary,
            'categories' => Category::orderBy('name')->pluck('name')->toArray(),
            'locations' => $warehouses->pluck('name')->toArray(),
            'warehouses' => $warehouses,
            'skuPreview' => $skuPreview,
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate($this->rules());

        $quantity = (int) ($data['quantity'] ?? 0);
        $warehouseName = $data['warehouse'] ?? null;

        if ($quantity > 0 && empty($warehouseName)) {
            throw ValidationException::withMessages([
                'warehouse' => 'Pilih lokasi penyimpanan untuk stok awal.',
            ]);
        }

        if (! empty($warehouseName)) {
            $warehouse = Warehouse::where('name', $warehouseName)->first();
            if (! $warehouse) {
                throw ValidationException::withMessages([
                    'warehouse' => 'Lokasi penyimpanan tidak ditemukan.',
                ]);
            }
            if ($warehouse->status === 'Nonaktif') {
                throw ValidationException::withMessages([
                    'warehouse' => 'Lokasi penyimpanan berstatus Nonaktif, tidak dapat digunakan untuk stok awal.',
                ]);
            }
        }

        try {
            $item = DB::transaction(function () use ($data, $request) {
                $item = new InventoryItem;
                $item->forceFill($this->itemAttributes($data))->save();

                $this->syncStock($item, $data, $request->user());

                return $item;
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('InventoryController@store: '.$e->getMessage());

            return $this->fail($request, 'Gagal menyimpan peralatan.');
        }

        return $this->ok($request, 'Peralatan berhasil ditambahkan.', $item);
    }

    public function update(Request $request, $id): JsonResponse|RedirectResponse
    {
        $item = $this->findBySkuOrId($id);
        $rules = $this->rules($item->id);
        $rules += [
            'stocks' => ['nullable', 'array'],
            'stocks.*.warehouse_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('warehouses', 'id')->whereNull('deleted_at'),
            ],
            'stocks.*.quantity' => ['required', 'integer', 'min:0'],
        ];
        $data = $request->validate($rules);

        try {
            DB::transaction(function () use ($item, $data, $request) {
                $lockedItem = InventoryItem::whereKey($item->id)->lockForUpdate()->firstOrFail();
                $stockRows = WarehouseStock::where('inventory_item_id', $lockedItem->id)
                    ->orderBy('warehouse_id')
                    ->lockForUpdate()
                    ->get();
                $oldTotal = (int) $stockRows->sum('quantity');
                $oldLevel = InventoryItem::stockLevel($oldTotal, (int) $lockedItem->min_stock, $lockedItem->status);
                $oldMinStock = (int) $lockedItem->min_stock;
                $lockedItem->forceFill($this->itemAttributes($data, $lockedItem))->save();
                $newMinStock = (int) $lockedItem->min_stock;
                if ($newMinStock !== $oldMinStock) {
                    WarehouseStock::where('inventory_item_id', $lockedItem->id)
                        ->update(['min_quantity' => $newMinStock]);
                }

                if (isset($data['stocks'])) {
                    $warehouseIds = collect($data['stocks'])->pluck('warehouse_id')->unique()->map(fn ($id) => (int) $id);
                    $warehouses = Warehouse::whereIn('id', $warehouseIds)->get()->keyBy('id');

                    foreach ($data['stocks'] as $index => $stockData) {
                        $warehouseId = (int) $stockData['warehouse_id'];
                        $newQuantity = (int) $stockData['quantity'];
                        $wh = $warehouses->get($warehouseId);
                        if ($wh && $wh->status === 'Nonaktif') {
                            $current = $stockRows->firstWhere('warehouse_id', $warehouseId);
                            $currentQuantity = (int) ($current?->quantity ?? 0);
                            if ($currentQuantity !== $newQuantity) {
                                throw ValidationException::withMessages([
                                    'stocks.'.$index.'.quantity' => 'Lokasi '.$wh->name.' berstatus Nonaktif, stok tidak dapat diubah.',
                                ]);
                            }
                        }
                    }

                    $this->syncStockAdjustments($lockedItem, $data['stocks'], $request->user());
                } elseif (! empty($data['warehouse']) && isset($data['quantity'])) {
                    $warehouse = Warehouse::where('name', $data['warehouse'])->first();
                    if (! $warehouse) {
                        throw ValidationException::withMessages([
                            'warehouse' => 'Lokasi penyimpanan tidak ditemukan.',
                        ]);
                    }
                    if ($warehouse->status === 'Nonaktif') {
                        $current = $stockRows->firstWhere('warehouse_id', $warehouse->id);
                        $currentQuantity = (int) ($current?->quantity ?? 0);
                        if ($currentQuantity !== (int) $data['quantity']) {
                            throw ValidationException::withMessages([
                                'quantity' => 'Lokasi '.$warehouse->name.' berstatus Nonaktif, stok tidak dapat diubah.',
                            ]);
                        }
                    }

                    $this->syncStockAdjustments($lockedItem, [[
                        'warehouse_id' => $warehouse->id,
                        'quantity' => $data['quantity'],
                    ]], $request->user());
                }

                $newTotal = (int) WarehouseStock::where('inventory_item_id', $lockedItem->id)->sum('quantity');
                $lockedItem->logWorsenedStockLevel($oldLevel, $newTotal, $request->user());
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('InventoryController@update: '.$e->getMessage());

            return $this->fail($request, 'Gagal memperbarui peralatan.');
        }

        return $this->ok($request, 'Peralatan berhasil diperbarui.', $item);
    }

    public function destroy(Request $request, $id): JsonResponse|RedirectResponse
    {
        $item = $this->findBySkuOrId($id);

        if ((int) $item->stocks()->sum('quantity') > 0) {
            return $this->fail($request, 'Peralatan tidak dapat dihapus karena masih memiliki stok.', 422);
        }

        if ($item->assets()->exists()) {
            return $this->fail($request, 'Peralatan tidak dapat dihapus karena masih ditautkan ke aset.', 422);
        }

        try {
            $item->delete();
        } catch (\Throwable $e) {
            Log::error('InventoryController@destroy: '.$e->getMessage());

            return $this->fail($request, 'Gagal menghapus peralatan.');
        }

        return $this->ok($request, 'Peralatan berhasil dihapus.');
    }

    // ------------------------------------------------------------------
    // Helper
    // ------------------------------------------------------------------

    private function rules(?int $ignoreId = null): array
    {
        return [
            'sku' => [
                'required',
                'string',
                'max:30',
                Rule::unique('inventory_items', 'sku')->withoutTrashed()->ignore($ignoreId),
                function ($attribute, $value, $fail) {
                    $trashed = InventoryItem::withTrashed()
                        ->where('sku', $value)
                        ->whereNotNull('deleted_at')
                        ->first();
                    if ($trashed) {
                        $fail('SKU ini sudah dipakai oleh peralatan yang telah dihapus. Gunakan SKU lain.');
                    }
                },
            ],
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:20',
            'quantity' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'warehouse' => 'nullable|string|max:150',
            'status' => 'nullable|in:Aktif,Nonaktif',
            'condition' => ['nullable', Rule::in(InventoryItem::CONDITIONS)],
            'availability' => ['nullable', Rule::in(InventoryItem::AVAILABILITIES)],
            'serial_example' => 'nullable|string|max:80',
            'description' => 'nullable|string|max:2000',
        ];
    }

    /** Atribut tabel inventory_items. forceFill dipakai agar tidak bergantung pada $fillable. */
    private function itemAttributes(array $data, ?InventoryItem $item = null): array
    {
        $attrs = [
            'sku' => $data['sku'],
            'name' => $data['name'],
            'unit' => $data['unit'] ?? 'Unit',
            'min_stock' => $data['min_stock'] ?? $item?->min_stock ?? 0,
            'status' => $data['status'] ?? $item?->status ?? 'Aktif',
            'condition' => $data['condition'] ?? $item?->condition ?? 'Baik',
            'availability' => $data['availability'] ?? $item?->availability ?? 'Tersedia',
        ];

        if (array_key_exists('serial_example', $data)) {
            $attrs['serial_example'] = $data['serial_example'];
        }
        if (array_key_exists('description', $data)) {
            $attrs['description'] = $data['description'];
        }
        if (! empty($data['category'])) {
            $attrs['category_id'] = Category::firstOrCreate(['name' => $data['category']])->id;
        }

        return $attrs;
    }

    /** Simpan jumlah unit di lokasi penyimpanan yang dipilih. */
    private function syncStock(InventoryItem $item, array $data, $user): void
    {
        if (empty($data['warehouse']) || ! isset($data['quantity'])) {
            return;
        }

        $quantity = (int) $data['quantity'];
        if ($quantity <= 0) {
            return;
        }

        $warehouse = Warehouse::where('name', $data['warehouse'])->first();
        if (! $warehouse) {
            throw ValidationException::withMessages([
                'warehouse' => 'Lokasi penyimpanan tidak ditemukan.',
            ]);
        }

        $item->stocks()->updateOrCreate(
            ['warehouse_id' => $warehouse->id],
            ['quantity' => $quantity, 'min_quantity' => $data['min_stock'] ?? 0]
        );

        StockMovement::create([
            'inventory_item_id' => $item->id,
            'warehouse_id' => $warehouse->id,
            'type' => 'Masuk',
            'quantity' => $quantity,
            'reference' => 'Stok awal',
            'note' => 'Stok awal saat item dibuat.',
            'user_id' => $user?->id,
            'user_name' => $user?->name,
        ]);
    }

    private function syncStockAdjustments(InventoryItem $item, array $stocks, $user): void
    {
        if ($stocks === []) {
            return;
        }

        $warehouseIds = collect($stocks)->pluck('warehouse_id')->map(fn ($id) => (int) $id);
        $existingStocks = $item->stocks()
            ->whereIn('warehouse_id', $warehouseIds)
            ->orderBy('warehouse_id')
            ->lockForUpdate()
            ->get()
            ->keyBy('warehouse_id');

        foreach ($stocks as $stockData) {
            $warehouseId = (int) $stockData['warehouse_id'];
            $newQuantity = (int) $stockData['quantity'];
            $stock = $existingStocks->get($warehouseId);
            $oldQuantity = (int) ($stock?->quantity ?? 0);

            if ($oldQuantity === $newQuantity) {
                continue;
            }

            if ($stock) {
                $stock->forceFill(['quantity' => $newQuantity])->save();
            } elseif ($newQuantity > 0) {
                $item->stocks()->create([
                    'warehouse_id' => $warehouseId,
                    'quantity' => $newQuantity,
                    'min_quantity' => $item->min_stock,
                ]);
            } else {
                continue;
            }

            StockMovement::create([
                'inventory_item_id' => $item->id,
                'warehouse_id' => $warehouseId,
                'type' => $newQuantity > $oldQuantity ? 'Masuk' : 'Keluar',
                'quantity' => abs($newQuantity - $oldQuantity),
                'reference' => 'Penyesuaian manual',
                'note' => "Penyesuaian stok dari form edit: {$oldQuantity} menjadi {$newQuantity}",
                'user_id' => $user?->id,
                'user_name' => $user?->name,
            ]);
        }
    }

    private function findBySkuOrId($id): InventoryItem
    {
        $item = InventoryItem::where('sku', $id)->first();
        if ($item) {
            return $item;
        }
        if (ctype_digit((string) $id)) {
            $item = InventoryItem::where('id', $id)->first();
            if ($item) {
                return $item;
            }
        }
        return InventoryItem::where('sku', $id)->orWhere('id', $id)->firstOrFail();
    }

    private function formatItem(InventoryItem $item): array
    {
        $totalStock = $item->stocks->sum('quantity');
        $primaryStock = $item->stocks->sortByDesc('quantity')->first();
        $primaryWh = $primaryStock?->warehouse;
        $stockedLocations = $item->stocks->where('quantity', '>', 0);
        $warehouseCount = $stockedLocations->count();
        $locationLabel = $warehouseCount > 0
            ? $stockedLocations->sortByDesc('quantity')->first()->warehouse?->name ?? '-'
            : '-';

        if ($warehouseCount > 1 && $locationLabel !== '-') {
            $locationLabel .= ' +'.($warehouseCount - 1).' lokasi';
        }

        $stockLevel = InventoryItem::stockLevel($totalStock, (int) $item->min_stock, $item->status);
        $restockNeeded = max((int) $item->min_stock - $totalStock, 0);
        $status = match ($stockLevel) {
            'Habis' => 'Habis',
            'Menipis' => 'Low Stock',
            default => in_array($item->status, ['Aktif', 'Nonaktif'], true) ? $item->status : 'Aktif',
        };

        return [
            'id' => $item->sku,
            'name' => $item->name,
            'category' => $item->category?->name ?? '-',
            'unit' => $item->unit,
            'total_stock' => $totalStock,
            'min_stock' => $item->min_stock,
            'status' => $status,
            'stock_level' => $stockLevel,
            'restock_needed' => $restockNeeded,
            'condition' => $item->condition,
            'availability' => $item->availability,
            'location' => $primaryWh?->name ?? '-',
            'location_label' => $locationLabel,
            'warehouse_count' => $warehouseCount,
            'address' => $primaryWh?->address ?? '-',
            'serial_example' => $item->serial_example ?? '-',
            'description' => $item->description ?? '-',
            'warehouses' => $item->stocks->map(fn ($s) => [
                'id' => $s->warehouse_id,
                'name' => $s->warehouse?->name ?? '-',
                'stock' => $s->quantity,
                'min' => $s->min_quantity,
            ])->values()->toArray(),
            'movements' => $item->movements->map(fn ($m) => [
                'date' => $m->created_at?->format('d M Y H:i') ?? '-',
                'type' => $m->type,
                'qty' => $m->quantity,
                'ref' => $m->reference ?? '-',
                'user' => $m->user_name ?? '-',
            ])->toArray(),
            'related' => [
                'active_assets' => (int) $item->active_assets_count,
                'open_tickets' => (int) $item->open_tickets_count,
            ],
        ];
    }

    private function itemsForDisplay()
    {
        return InventoryItem::with([
            'category',
            'stocks.warehouse',
            'movements' => fn ($query) => $query->latest()->limit(5),
        ])
            ->withCount([
                'assets as active_assets_count' => fn ($query) => $query->where('status', 'Aktif'),
            ])
            ->addSelect([
                'open_tickets_count' => DB::table('ticket_materials')
                    ->join('tickets', 'tickets.id', '=', 'ticket_materials.ticket_id')
                    ->selectRaw('COUNT(DISTINCT ticket_materials.ticket_id)')
                    ->whereColumn('ticket_materials.inventory_item_id', 'inventory_items.id')
                    ->whereNotIn('tickets.status', ['Resolved', 'Closed'])
                    ->whereNull('tickets.deleted_at'),
            ]);
    }

    private function ok(Request $request, string $message, ?InventoryItem $item = null): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'item' => $item
                    ? $this->formatItem($this->itemsForDisplay()->whereKey($item->id)->firstOrFail())
                    : null,
            ]);
        }

        return redirect()->route('inventory.index')->with('success', $message);
    }

    private function fail(Request $request, string $message, int $status = 500): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        return back()->withInput()->with('error', $message);
    }
}
