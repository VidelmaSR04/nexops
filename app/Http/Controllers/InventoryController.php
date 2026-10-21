<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\InventoryItem;
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
        $products = InventoryItem::with(['category', 'stocks.warehouse'])
            ->orderBy('sku')
            ->get()
            ->map(fn ($item) => $this->formatItem($item))
            ->toArray();

        $summary = [
            'total_products' => InventoryItem::count(),
            'total_stock'    => WarehouseStock::sum('quantity'),
            'low_stock'      => collect($products)->where('status', 'Low Stock')->count(),
            'warehouse'      => Warehouse::count(),
        ];

        return view('inventory', [
            'products'   => $products,
            'summary'    => $summary,
            'categories' => Category::orderBy('name')->pluck('name')->toArray(),
            'locations'  => Warehouse::orderBy('name')->pluck('name')->toArray(),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate($this->rules());

        try {
            $item = DB::transaction(function () use ($data) {
                $item = new InventoryItem();
                $item->forceFill($this->itemAttributes($data))->save();

                $this->syncStock($item, $data);

                return $item;
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('InventoryController@store: ' . $e->getMessage());
            return $this->fail($request, 'Gagal menyimpan peralatan.');
        }

        return $this->ok($request, 'Peralatan berhasil ditambahkan.', $item);
    }

    public function update(Request $request, $id): JsonResponse|RedirectResponse
    {
        $item = $this->findBySkuOrId($id);
        $data = $request->validate($this->rules($item->id));

        try {
            DB::transaction(function () use ($item, $data) {
                $item->forceFill($this->itemAttributes($data, $item))->save();
                $this->syncStock($item, $data);
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('InventoryController@update: ' . $e->getMessage());
            return $this->fail($request, 'Gagal memperbarui peralatan.');
        }

        return $this->ok($request, 'Peralatan berhasil diperbarui.', $item);
    }

    public function destroy(Request $request, $id): JsonResponse|RedirectResponse
    {
        $item = $this->findBySkuOrId($id);

        try {
            DB::transaction(function () use ($item) {
                $item->stocks()->delete();
                $item->movements()->delete();
                $item->delete();
            });
        } catch (\Throwable $e) {
            Log::error('InventoryController@destroy: ' . $e->getMessage());
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
            'sku'            => 'required|string|max:50|unique:inventory_items,sku' . ($ignoreId ? ',' . $ignoreId : ''),
            'name'           => 'required|string|max:255',
            'category'       => 'nullable|string|max:100',
            'unit'           => 'nullable|string|max:30',
            'quantity'       => 'nullable|integer|min:0',
            'min_stock'      => 'nullable|integer|min:0',
            'warehouse'      => 'nullable|string|max:150',
            'status'         => 'nullable|in:Aktif,Nonaktif,Low Stock,Habis',
            'condition'      => ['nullable', Rule::in(InventoryItem::CONDITIONS)],
            'availability'   => ['nullable', Rule::in(InventoryItem::AVAILABILITIES)],
            'serial_example' => 'nullable|string|max:100',
            'description'    => 'nullable|string|max:2000',
        ];
    }

    /** Atribut tabel inventory_items. forceFill dipakai agar tidak bergantung pada $fillable. */
    private function itemAttributes(array $data, ?InventoryItem $item = null): array
    {
        $attrs = [
            'sku'          => $data['sku'],
            'name'         => $data['name'],
            'unit'         => $data['unit'] ?? 'Unit',
            'min_stock'    => $data['min_stock'] ?? 0,
            'status'       => $data['status'] ?? 'Aktif',
            'condition'    => $data['condition'] ?? $item?->condition ?? 'Baik',
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
    private function syncStock(InventoryItem $item, array $data): void
    {
        if (empty($data['warehouse']) || ! isset($data['quantity'])) {
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
            ['quantity' => $data['quantity'], 'min_quantity' => $data['min_stock'] ?? 0]
        );
    }

    private function findBySkuOrId($id): InventoryItem
    {
        return InventoryItem::where('sku', $id)->orWhere('id', $id)->firstOrFail();
    }

    private function formatItem(InventoryItem $item): array
    {
        $totalStock   = $item->stocks->sum('quantity');
        $primaryStock = $item->stocks->sortByDesc('quantity')->first();
        $primaryWh    = $primaryStock?->warehouse;

        $status = $item->status;
        if ($totalStock == 0) {
            $status = 'Habis';
        } elseif ($totalStock <= $item->min_stock) {
            $status = 'Low Stock';
        }

        return [
            'id'             => $item->sku,
            'name'           => $item->name,
            'category'       => $item->category?->name ?? '-',
            'unit'           => $item->unit,
            'total_stock'    => $totalStock,
            'min_stock'      => $item->min_stock,
            'status'         => $status,
            'condition'      => $item->condition,
            'availability'   => $item->availability,
            'location'       => $primaryWh?->name ?? '-',
            'address'        => $primaryWh?->address ?? '-',
            'serial_example' => $item->serial_example ?? '-',
            'description'    => $item->description ?? '-',
            'warehouses'     => $item->stocks->map(fn ($s) => [
                'name'  => $s->warehouse?->name ?? '-',
                'stock' => $s->quantity,
                'min'   => $s->min_quantity,
            ])->values()->toArray(),
            'movements'      => $item->movements()->latest()->take(5)->get()->map(fn ($m) => [
                'date' => $m->created_at?->format('d M Y H:i') ?? '-',
                'type' => $m->type,
                'qty'  => $m->quantity,
                'ref'  => $m->reference ?? '-',
                'user' => $m->user_name ?? '-',
            ])->toArray(),
            // Belum ada relasi ke aset dan tiket, jadi 0 (bukan angka contoh).
            'related'        => ['active_assets' => 0, 'open_tickets' => 0],
        ];
    }

    private function ok(Request $request, string $message, ?InventoryItem $item = null): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'item'    => $item ? $this->formatItem($item->fresh(['category', 'stocks.warehouse'])) : null,
            ]);
        }

        return redirect()->route('inventory.index')->with('success', $message);
    }

    private function fail(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 500);
        }

        return back()->withInput()->with('error', $message);
    }
}
