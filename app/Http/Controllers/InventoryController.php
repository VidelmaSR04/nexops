<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InventoryController extends Controller
{
    public function index()
    {
        $products = InventoryItem::with(['category', 'stocks.warehouse', 'movements'])->get()->map(function ($item) {
            $totalStock = $item->stocks->sum('quantity');
            $primaryStock = $item->stocks->sortByDesc('quantity')->first();
            $primaryWh = $primaryStock?->warehouse;

            $status = $item->status;
            if ($totalStock == 0) {
                $status = 'Habis';
            } elseif ($totalStock <= $item->min_stock) {
                $status = 'Low Stock';
            }

            $whStocks = $item->stocks->map(function ($s) {
                return [
                    'id' => $s->warehouse_id,
                    'name' => $s->warehouse?->name ?? 'Gudang Utama',
                    'stock' => $s->quantity,
                    'min' => $s->min_quantity,
                ];
            })->toArray();

            $movements = $item->movements()->latest()->take(5)->get()->map(function ($m) {
                return [
                    'date' => $m->created_at ? $m->created_at->format('d M Y H:i') : '-',
                    'type' => $m->type,
                    'qty' => $m->quantity,
                    'ref' => $m->reference ?? '-',
                    'user' => $m->user_name ?? 'Staff',
                ];
            })->toArray();

            return [
                'id' => $item->sku,
                'name' => $item->name,
                'category' => $item->category?->name ?? 'Accessories',
                'unit' => $item->unit,
                'total_stock' => $totalStock,
                'min_stock' => $item->min_stock,
                'status' => $status,
                'location' => $primaryWh?->name ?? 'Gudang Utama',
                'address' => $primaryWh?->address ?? 'Jl. Raya Situbondo No. 12',
                'serial_example' => $item->serial_example ?? ($item->sku.'-SN'),
                'description' => $item->description ?? 'Deskripsi item inventaris NEXOPS.',
                'warehouses' => $whStocks,
                'movements' => $movements,
                'related' => [
                    'active_assets' => $item->assets()->where('status', 'Aktif')->count(),
                    'open_tickets' => $item->ticketMaterials()
                        ->whereHas('ticket', fn ($query) => $query->whereNotIn('status', ['Resolved', 'Closed']))
                        ->distinct('ticket_id')
                        ->count('ticket_id'),
                ],
            ];
        })->toArray();

        $summary = [
            'total_products' => InventoryItem::count(),
            'total_stock' => WarehouseStock::sum('quantity'),
            'low_stock' => collect($products)->where('status', 'Low Stock')->count(),
            'warehouse' => Warehouse::count(),
        ];

        $categories = Category::pluck('name')->toArray();
        $warehouses = Warehouse::where('status', 'Aktif')->orderBy('name')->get(['id', 'name']);

        return view('inventory', [
            'products' => $products,
            'summary' => $summary,
            'categories' => $categories,
            'warehouses' => $warehouses,
            'skuPreview' => $this->nextCode(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:20'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'warehouse_id' => ['required', 'integer', Rule::exists('warehouses', 'id')->whereNull('deleted_at')],
            'description' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $warehouse = Warehouse::findOrFail($validated['warehouse_id']);
            $stored = $warehouse->stocks()->whereHas('inventoryItem')->sum('quantity');
            if ($stored + $validated['stock'] > $warehouse->capacity) {
                throw ValidationException::withMessages([
                    'stock' => 'Stok awal melebihi kapasitas gudang yang dipilih.',
                ]);
            }

            $category = Category::firstOrCreate(['name' => $validated['category']]);
            $item = InventoryItem::create([
                'sku' => $this->nextCode(),
                'name' => $validated['name'],
                'category_id' => $category->id,
                'unit' => $validated['unit'],
                'min_stock' => $validated['min_stock'],
                'status' => $this->stockStatus($validated['stock'], $validated['min_stock']),
                'serial_example' => null,
                'description' => $validated['description'] ?? null,
            ]);

            $item->stocks()->create([
                'warehouse_id' => $validated['warehouse_id'],
                'quantity' => $validated['stock'],
                'min_quantity' => $validated['min_stock'],
            ]);

            if ($validated['stock'] > 0) {
                $item->movements()->create([
                    'warehouse_id' => $validated['warehouse_id'],
                    'type' => 'Masuk',
                    'quantity' => $validated['stock'],
                    'reference' => 'Stok awal '.$item->sku,
                    'user_id' => $request->user()?->id,
                    'user_name' => $request->user()?->name,
                    'note' => 'Stok awal saat produk ditambahkan.',
                ]);
            }
        });

        return redirect()->route('inventory.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, string $sku)
    {
        $item = InventoryItem::where('sku', $sku)->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:20'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'warehouse_id' => ['required', 'integer', Rule::exists('warehouses', 'id')->whereNull('deleted_at')],
            'description' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($item, $validated, $request) {
            $category = Category::firstOrCreate(['name' => $validated['category']]);
            $oldTotal = (int) $item->stocks()->sum('quantity');
            $newTotal = (int) $validated['stock'];
            $delta = $newTotal - $oldTotal;

            $item->update([
                'name' => $validated['name'],
                'category_id' => $category->id,
                'unit' => $validated['unit'],
                'min_stock' => $validated['min_stock'],
                'status' => $this->stockStatus($newTotal, $validated['min_stock']),
                'description' => $validated['description'] ?? null,
            ]);
            $item->stocks()->update(['min_quantity' => $validated['min_stock']]);

            if ($delta !== 0) {
                $warehouse = Warehouse::findOrFail($validated['warehouse_id']);
                $stock = $item->stocks()->firstOrCreate(
                    ['warehouse_id' => $validated['warehouse_id']],
                    ['quantity' => 0, 'min_quantity' => $validated['min_stock']]
                );

                if ($delta < 0 && $stock->quantity < abs($delta)) {
                    throw ValidationException::withMessages([
                        'stock' => 'Pengurangan stok melebihi stok yang tersedia di gudang penyesuaian.',
                    ]);
                }
                if ($delta > 0) {
                    $warehouseUsed = $warehouse->stocks()->whereHas('inventoryItem')->sum('quantity');
                    if ($warehouseUsed + $delta > $warehouse->capacity) {
                        throw ValidationException::withMessages([
                            'stock' => 'Kapasitas gudang tidak mencukupi untuk penambahan stok.',
                        ]);
                    }
                }

                $stock->increment('quantity', $delta);
                $item->movements()->create([
                    'warehouse_id' => $validated['warehouse_id'],
                    'type' => $delta > 0 ? 'Masuk' : 'Keluar',
                    'quantity' => abs($delta),
                    'reference' => 'Penyesuaian stok '.$item->sku,
                    'user_id' => $request->user()?->id,
                    'user_name' => $request->user()?->name,
                    'note' => 'Penyesuaian jumlah stok melalui halaman inventory.',
                ]);
            }
        });

        return redirect()->route('inventory.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Request $request, string $sku)
    {
        $item = InventoryItem::where('sku', $sku)->firstOrFail();

        DB::transaction(function () use ($item, $request) {
            foreach ($item->stocks()->where('quantity', '>', 0)->get() as $stock) {
                $item->movements()->create([
                    'warehouse_id' => $stock->warehouse_id,
                    'type' => 'Keluar',
                    'quantity' => $stock->quantity,
                    'reference' => 'Penghapusan produk '.$item->sku,
                    'user_id' => $request->user()?->id,
                    'user_name' => $request->user()?->name,
                    'note' => 'Stok dikeluarkan dari persediaan saat produk diarsipkan.',
                ]);
                $stock->update(['quantity' => 0]);
            }

            $item->update(['status' => 'Nonaktif']);
            $item->delete();
        });

        return redirect()->route('inventory.index')->with('success', 'Produk dihapus dan stoknya dicatat sebagai mutasi keluar.');
    }

    private function stockStatus(int $quantity, int $minimum): string
    {
        if ($quantity === 0) {
            return 'Habis';
        }

        return $quantity <= $minimum ? 'Low Stock' : 'Aktif';
    }

    private function nextCode(): string
    {
        $highest = InventoryItem::withTrashed()->pluck('sku')->map(function ($sku) {
            return preg_match('/^PRD-(\d+)$/', $sku, $matches) ? (int) $matches[1] : 0;
        })->max() ?? 0;

        return 'PRD-'.str_pad((string) ($highest + 1), 3, '0', STR_PAD_LEFT);
    }
}
