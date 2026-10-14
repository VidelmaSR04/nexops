<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::with(['stocks.inventoryItem.category'])
            ->orderBy('code')
            ->get()
            ->map(function ($warehouse) {
                $stocks = $warehouse->stocks->filter(fn ($stock) => $stock->inventoryItem);
                $usedStock = $stocks->sum('quantity');

                $stockItems = $stocks->map(function ($stock) {
                    return [
                        'sku' => $stock->inventoryItem->sku,
                        'name' => $stock->inventoryItem->name,
                        'category' => $stock->inventoryItem->category?->name ?? 'General',
                        'qty' => $stock->quantity,
                        'unit' => $stock->inventoryItem->unit,
                    ];
                })->values()->toArray();

                $history = StockMovement::with('inventoryItem')
                    ->where(function ($query) use ($warehouse) {
                        $query->where('warehouse_id', $warehouse->id)
                            ->orWhere('to_warehouse_id', $warehouse->id);
                    })
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(function ($movement) use ($warehouse) {
                        $isIncomingTransfer = $movement->type === 'Transfer' && (int) $movement->to_warehouse_id === $warehouse->id;
                        $title = $isIncomingTransfer ? 'Transfer masuk' : match ($movement->type) {
                            'Masuk' => 'Penerimaan',
                            'Keluar' => 'Pengeluaran',
                            'Transfer' => 'Transfer',
                            default => 'Mutasi stok',
                        };

                        return [
                            'title' => $title.' '.($movement->inventoryItem?->name ?? 'Barang'),
                            'date' => $movement->created_at?->format('d M Y H:i') ?? '-',
                            'desc' => $movement->quantity.' item · '.($movement->reference ?? 'Tanpa referensi').' · '.($movement->user_name ?? 'Staff'),
                        ];
                    })->values()->toArray();

                return [
                    'id' => $warehouse->code,
                    'name' => $warehouse->name,
                    'location' => $warehouse->address ?? $warehouse->area ?? '-',
                    'area' => $warehouse->area ?? '-',
                    'capacity' => $warehouse->capacity,
                    'available_stock' => max(0, $warehouse->capacity - $usedStock),
                    'used_stock' => $usedStock,
                    'status' => $warehouse->status,
                    'manager' => $warehouse->manager_name ?? '-',
                    'manager_role' => $warehouse->manager_role ?? 'Warehouse Staff',
                    'categories_count' => $stocks->pluck('inventoryItem.category_id')->filter()->unique()->count(),
                    'description' => $warehouse->description ?? '',
                    'stock_items' => $stockItems,
                    'history' => $history,
                    'docs' => [],
                ];
            })
            ->toArray();

        $totalCapacity = Warehouse::sum('capacity');
        $totalUsed = WarehouseStock::whereHas('inventoryItem')->sum('quantity');

        $summary = [
            'total_warehouses' => count($warehouses),
            'total_capacity' => $totalCapacity,
            'available_stock' => max(0, $totalCapacity - $totalUsed),
            'used_stock' => $totalUsed,
        ];

        return view('warehouse', [
            'warehouses' => $warehouses,
            'summary' => $summary,
            'locations' => Warehouse::query()->whereNotNull('area')->distinct()->orderBy('area')->pluck('area'),
            'statuses' => ['Aktif', 'Maintenance', 'Nonaktif'],
            'warehouseOptions' => Warehouse::orderBy('name')->get(['id', 'name']),
            'inventoryItems' => InventoryItem::orderBy('name')->get(['id', 'sku', 'name', 'unit']),
            'warehousePreview' => $this->nextCode(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'area' => ['required', 'string', 'max:60'],
            'capacity' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:Aktif,Maintenance,Nonaktif'],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        Warehouse::create([
            'code' => $this->nextCode(),
            'name' => $validated['name'],
            'address' => $validated['address'],
            'area' => $validated['area'],
            'capacity' => $validated['capacity'],
            'status' => $validated['status'],
            'manager_name' => $validated['manager_name'] ?? null,
            'manager_role' => $validated['manager_name'] ? 'Warehouse Staff' : null,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('warehouse.index')->with('success', 'Gudang berhasil ditambahkan.');
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'area' => ['required', 'string', 'max:60'],
            'capacity' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:Aktif,Maintenance,Nonaktif'],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $currentStock = $warehouse->stocks()->whereHas('inventoryItem')->sum('quantity');
        if ($validated['capacity'] < $currentStock) {
            throw ValidationException::withMessages([
                'capacity' => 'Kapasitas tidak boleh lebih kecil dari total stok yang sedang tersimpan ('.$currentStock.').',
            ]);
        }

        $warehouse->update([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'area' => $validated['area'],
            'capacity' => $validated['capacity'],
            'status' => $validated['status'],
            'manager_name' => $validated['manager_name'] ?? null,
            'manager_role' => $validated['manager_name'] ? 'Warehouse Staff' : null,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('warehouse.index')->with('success', 'Data gudang berhasil diperbarui.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $hasInventory = $warehouse->stocks()
            ->where('quantity', '>', 0)
            ->whereHas('inventoryItem')
            ->exists();
        $hasAssets = $warehouse->assets()->exists();

        if ($hasInventory || $hasAssets) {
            throw ValidationException::withMessages([
                'warehouse' => 'Gudang tidak dapat dihapus karena masih memiliki stok inventaris atau aset. Pindahkan stok/aset terlebih dahulu.',
            ]);
        }

        $warehouse->delete();

        return redirect()->route('warehouse.index')->with('success', 'Gudang berhasil dihapus.');
    }

    public function updateStatus(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Aktif,Maintenance,Nonaktif'],
        ]);

        $warehouse->update(['status' => $validated['status']]);

        return redirect()->route('warehouse.index')->with('success', 'Status gudang berhasil diperbarui.');
    }

    public function storeMovement(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'inventory_item_id' => ['required', 'integer', Rule::exists('inventory_items', 'id')->whereNull('deleted_at')],
            'type' => ['required', 'in:Masuk,Keluar,Transfer'],
            'quantity' => ['required', 'integer', 'min:1'],
            'to_warehouse_id' => ['required_if:type,Transfer', 'nullable', 'integer', Rule::exists('warehouses', 'id')->whereNull('deleted_at')],
            'reference' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
        ]);

        $item = InventoryItem::findOrFail($validated['inventory_item_id']);
        $quantity = (int) $validated['quantity'];

        DB::transaction(function () use ($request, $warehouse, $item, $validated, $quantity) {
            $sourceStock = WarehouseStock::firstOrCreate(
                ['warehouse_id' => $warehouse->id, 'inventory_item_id' => $item->id],
                ['quantity' => 0, 'min_quantity' => $item->min_stock]
            );

            if (in_array($validated['type'], ['Keluar', 'Transfer'], true) && $sourceStock->quantity < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok gudang tidak mencukupi untuk mutasi yang diminta.',
                ]);
            }

            $destinationId = null;
            if ($validated['type'] === 'Transfer') {
                $destinationId = (int) ($validated['to_warehouse_id'] ?? 0);
                if ($destinationId === $warehouse->id) {
                    throw ValidationException::withMessages([
                        'to_warehouse_id' => 'Gudang tujuan harus berbeda dari gudang asal.',
                    ]);
                }

                $destination = Warehouse::findOrFail($destinationId);
                $destinationUsed = $destination->stocks()->whereHas('inventoryItem')->sum('quantity');
                if ($destinationUsed + $quantity > $destination->capacity) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Kapasitas gudang tujuan tidak mencukupi.',
                    ]);
                }

                $destinationStock = WarehouseStock::firstOrCreate(
                    ['warehouse_id' => $destination->id, 'inventory_item_id' => $item->id],
                    ['quantity' => 0, 'min_quantity' => $item->min_stock]
                );
                $sourceStock->decrement('quantity', $quantity);
                $destinationStock->increment('quantity', $quantity);
            } elseif ($validated['type'] === 'Masuk') {
                $warehouseUsed = $warehouse->stocks()->whereHas('inventoryItem')->sum('quantity');
                if ($warehouseUsed + $quantity > $warehouse->capacity) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Kapasitas gudang tidak mencukupi untuk stok masuk.',
                    ]);
                }

                $sourceStock->increment('quantity', $quantity);
            } else {
                $sourceStock->decrement('quantity', $quantity);
            }

            StockMovement::create([
                'inventory_item_id' => $item->id,
                'warehouse_id' => $warehouse->id,
                'to_warehouse_id' => $destinationId,
                'type' => $validated['type'],
                'quantity' => $quantity,
                'reference' => $validated['reference'] ?? null,
                'user_id' => $request->user()?->id,
                'user_name' => $request->user()?->name,
                'note' => $validated['note'] ?? null,
            ]);

            $total = (int) $item->stocks()->sum('quantity');
            $item->update([
                'status' => $total === 0 ? 'Habis' : ($total <= $item->min_stock ? 'Low Stock' : 'Aktif'),
            ]);
        });

        return redirect()->route('warehouse.index')->with('success', 'Mutasi stok berhasil dicatat.');
    }

    private function nextCode(): string
    {
        $highest = Warehouse::withTrashed()->pluck('code')->map(function ($code) {
            return preg_match('/^WH-(\d+)$/', $code, $matches) ? (int) $matches[1] : 0;
        })->max() ?? 0;

        return 'WH-'.str_pad((string) ($highest + 1), 3, '0', STR_PAD_LEFT);
    }
}
