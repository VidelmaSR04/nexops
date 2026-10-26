<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use App\Models\Ticket;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Lokasi OPD (sebelumnya Warehouse).
 * Tabel yang dipakai tetap `warehouses`: code = Kode Lokasi, name = Nama OPD, address = Alamat.
 */
class WarehouseController extends Controller
{
    public function index()
    {
        $warehouseModels = Warehouse::orderBy('code')->get();

        // Jumlah aset per lokasi (satu query, bukan per baris)
        $assetCounts = Asset::selectRaw('warehouse_id, COUNT(*) as total')
            ->whereNotNull('warehouse_id')
            ->groupBy('warehouse_id')
            ->pluck('total', 'warehouse_id');

        // Riwayat perbaikan: tiket yang customer_name-nya sama dengan nama OPD
        $names = $warehouseModels->pluck('name')->filter()->unique()->all();
        $ticketsByOpd = Ticket::whereIn('customer_name', $names)
            ->latest()
            ->get()
            ->groupBy('customer_name');

        $warehouses = $warehouseModels->map(function ($w) use ($assetCounts, $ticketsByOpd) {
            $repairs = ($ticketsByOpd[$w->name] ?? collect())
                ->take(5)
                ->map(fn ($t) => [
                    'ticket' => $t->ticket_number,
                    'date' => $t->created_at?->format('d M Y') ?? '-',
                    'complaint' => $t->title ?? '-',
                    'technician' => $t->assigned_tech_name ?: 'Belum ditugaskan',
                    'status' => $t->status ?? '-',
                ])
                ->values()
                ->toArray();

            return [
                'id' => $w->code,
                'name' => $w->name,
                'location' => $w->address ?? '-',
                'status' => $w->status ?? 'Aktif',
                'assets_count' => (int) ($assetCounts[$w->id] ?? 0),
                'repairs' => $repairs,
            ];
        })->toArray();

        $highest = Warehouse::withTrashed()->pluck('code')->map(function ($code) {
            return preg_match('/^WH-(\d+)$/', $code, $matches) ? (int) $matches[1] : 0;
        })->max() ?? 0;
        $warehousePreview = 'WH-'.str_pad((string) ($highest + 1), 3, '0', STR_PAD_LEFT);

        return view('warehouse', [
            'warehouses' => $warehouses,
            'warehousePreview' => $warehousePreview,
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate($this->rules());

        try {
            $warehouse = new Warehouse;
            $warehouse->forceFill($this->attributes($data, true))->save();
        } catch (\Throwable $e) {
            Log::error('WarehouseController@store: '.$e->getMessage());

            return $this->fail($request, 'Gagal menyimpan lokasi.');
        }

        return $this->ok($request, 'Lokasi berhasil ditambahkan.', $warehouse);
    }

    public function update(Request $request, string $code): JsonResponse|RedirectResponse
    {
        $warehouse = Warehouse::where('code', $code)->firstOrFail();
        $data = $request->validate($this->rules($warehouse->id));

        if ($data['status'] === 'Nonaktif') {
            $hasActiveAssets = Asset::where('warehouse_id', $warehouse->id)
                ->where('status', 'Aktif')
                ->exists();
            if ($hasActiveAssets) {
                return $this->fail($request, 'Status tidak dapat diubah menjadi Nonaktif karena masih ada aset aktif di lokasi ini.', 422);
            }
        }

        try {
            $warehouse->forceFill($this->attributes($data))->save();
        } catch (\Throwable $e) {
            Log::error('WarehouseController@update: '.$e->getMessage());

            return $this->fail($request, 'Gagal memperbarui lokasi.');
        }

        return $this->ok($request, 'Lokasi berhasil diperbarui.', $warehouse);
    }

    public function updateStatus(Request $request, string $code): JsonResponse|RedirectResponse
    {
        $warehouse = Warehouse::where('code', $code)->firstOrFail();
        $data = $request->validate([
            'status' => 'required|in:Aktif,Maintenance,Nonaktif',
        ]);

        if ($data['status'] === 'Nonaktif') {
            $hasActiveAssets = Asset::where('warehouse_id', $warehouse->id)
                ->where('status', 'Aktif')
                ->exists();
            if ($hasActiveAssets) {
                return $this->fail($request, 'Status tidak dapat diubah menjadi Nonaktif karena masih ada aset aktif di lokasi ini.', 422);
            }
        }

        $oldStatus = $warehouse->status;
        $warehouse->status = $data['status'];
        $warehouse->save();

        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'user_name' => $request->user()?->name,
            'subject_type' => Warehouse::class,
            'subject_id' => $warehouse->id,
            'action' => 'status_changed',
            'description' => "Status lokasi {$warehouse->name} diubah dari {$oldStatus} menjadi {$data['status']}.",
            'properties' => [
                'old_status' => $oldStatus,
                'new_status' => $data['status'],
            ],
        ]);

        return $this->ok($request, 'Status lokasi berhasil diperbarui.', $warehouse);
    }

    public function storeMovement(Request $request, Warehouse $warehouse): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'inventory_item_id' => [
                'required',
                'integer',
                Rule::exists('inventory_items', 'id')->whereNull('deleted_at'),
            ],
            'type' => ['required', Rule::in(['Masuk', 'Keluar', 'Transfer'])],
            'quantity' => 'required|integer|min:1',
            'to_warehouse_id' => [
                'required_if:type,Transfer',
                'nullable',
                'integer',
                Rule::exists('warehouses', 'id')->whereNull('deleted_at'),
            ],
            'reference' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        if ($data['type'] === 'Transfer' && (int) $data['to_warehouse_id'] === $warehouse->id) {
            throw ValidationException::withMessages([
                'to_warehouse_id' => 'Lokasi tujuan harus berbeda dari lokasi asal.',
            ]);
        }

        try {
            DB::transaction(function () use ($request, $warehouse, $data) {
                $locationIds = [$warehouse->id];
                if ($data['type'] === 'Transfer') {
                    $locationIds[] = (int) $data['to_warehouse_id'];
                }

                $lockedWarehouses = Warehouse::whereIn('id', $locationIds)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($locationIds as $locationId) {
                    $lockedWarehouse = $lockedWarehouses->get($locationId);
                    if (! $lockedWarehouse) {
                        throw ValidationException::withMessages([
                            'warehouse' => 'Lokasi tidak ditemukan.',
                        ]);
                    }
                    if ($lockedWarehouse->status === 'Nonaktif') {
                        throw ValidationException::withMessages([
                            $locationId === $warehouse->id ? 'warehouse' : 'to_warehouse_id' => 'Pergerakan stok tidak dapat dilakukan pada lokasi berstatus Nonaktif.',
                        ]);
                    }
                }

                $item = InventoryItem::whereKey($data['inventory_item_id'])
                    ->lockForUpdate()
                    ->firstOrFail();
                $oldTotal = (int) WarehouseStock::where('inventory_item_id', $item->id)->sum('quantity');
                $oldLevel = InventoryItem::stockLevel($oldTotal, (int) $item->min_stock, $item->status);

                $sourceStock = WarehouseStock::where('warehouse_id', $warehouse->id)
                    ->where('inventory_item_id', $item->id)
                    ->lockForUpdate()
                    ->first();

                if ($data['type'] === 'Masuk') {
                    if (! $sourceStock) {
                        $sourceStock = WarehouseStock::create([
                            'warehouse_id' => $warehouse->id,
                            'inventory_item_id' => $item->id,
                            'quantity' => 0,
                            'min_quantity' => $item->min_stock,
                        ]);
                        $sourceStock = WarehouseStock::whereKey($sourceStock->id)->lockForUpdate()->firstOrFail();
                    }

                    $sourceStock->forceFill([
                        'quantity' => $sourceStock->quantity + $data['quantity'],
                    ])->save();
                } else {
                    if (! $sourceStock || $sourceStock->quantity < $data['quantity']) {
                        throw ValidationException::withMessages([
                            'quantity' => 'Stok tidak mencukupi.',
                        ]);
                    }

                    $sourceStock->forceFill([
                        'quantity' => $sourceStock->quantity - $data['quantity'],
                    ])->save();

                    if ($data['type'] === 'Transfer') {
                        $destinationId = (int) $data['to_warehouse_id'];
                        $destinationStock = WarehouseStock::firstOrCreate(
                            [
                                'warehouse_id' => $destinationId,
                                'inventory_item_id' => $item->id,
                            ],
                            [
                                'quantity' => 0,
                                'min_quantity' => $item->min_stock,
                            ]
                        );
                        $destinationStock = WarehouseStock::whereKey($destinationStock->id)
                            ->lockForUpdate()
                            ->firstOrFail();
                        $destinationStock->forceFill([
                            'quantity' => $destinationStock->quantity + $data['quantity'],
                        ])->save();
                    }
                }

                $newTotal = (int) WarehouseStock::where('inventory_item_id', $item->id)->sum('quantity');
                $item->logWorsenedStockLevel($oldLevel, $newTotal, $request->user());

                $user = $request->user();
                $movement = new StockMovement;
                $movement->forceFill([
                    'inventory_item_id' => $item->id,
                    'warehouse_id' => $warehouse->id,
                    'to_warehouse_id' => $data['type'] === 'Transfer' ? $data['to_warehouse_id'] : null,
                    'type' => $data['type'],
                    'quantity' => $data['quantity'],
                    'reference' => $data['reference'] ?? null,
                    'user_id' => $user?->id,
                    'user_name' => $user?->name,
                    'note' => $data['note'] ?? null,
                ])->save();

                ActivityLog::create([
                    'user_id' => $user?->id,
                    'user_name' => $user?->name,
                    'subject_type' => Warehouse::class,
                    'subject_id' => $warehouse->id,
                    'action' => 'stock_moved',
                    'description' => "{$data['type']} {$data['quantity']} {$item->unit} {$item->name} di {$warehouse->name}.",
                    'properties' => [
                        'stock_movement_id' => $movement->id,
                        'inventory_item_id' => $item->id,
                        'warehouse_id' => $warehouse->id,
                        'to_warehouse_id' => $data['to_warehouse_id'] ?? null,
                        'type' => $data['type'],
                        'quantity' => $data['quantity'],
                    ],
                ]);
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('WarehouseController@storeMovement: '.$e->getMessage());

            return $this->fail($request, 'Gagal mencatat pergerakan stok.');
        }

        return $this->ok($request, 'Pergerakan stok berhasil dicatat.');
    }

    public function destroy(Request $request, string $code): JsonResponse|RedirectResponse
    {
        $warehouse = Warehouse::where('code', $code)->firstOrFail();

        // Lindungi data: lokasi yang masih dipakai tidak boleh dihapus
        $hasAssets = Asset::where('warehouse_id', $warehouse->id)->exists();
        $hasStocks = $warehouse->stocks()->exists();

        if ($hasAssets || $hasStocks) {
            return $this->fail(
                $request,
                'Lokasi masih memiliki aset atau stok, pindahkan atau hapus datanya terlebih dahulu.',
                422
            );
        }

        try {
            $warehouse->delete();
        } catch (\Throwable $e) {
            Log::error('WarehouseController@destroy: '.$e->getMessage());

            return $this->fail($request, 'Gagal menghapus lokasi.');
        }

        return $this->ok($request, 'Lokasi berhasil dihapus.');
    }

    // ------------------------------------------------------------------
    // Helper
    // ------------------------------------------------------------------

    private function rules(?int $ignoreId = null): array
    {
        return [
            'code' => 'required|string|max:30|unique:warehouses,code'.($ignoreId ? ','.$ignoreId : ''),
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'status' => 'required|in:Aktif,Maintenance,Nonaktif',
        ];
    }

    /** forceFill dipakai agar tidak bergantung pada $fillable. */
    private function attributes(array $data, bool $creating = false): array
    {
        $attrs = [
            'code' => $data['code'],
            'name' => $data['name'],
            'address' => $data['address'],
            'status' => $data['status'],
        ];

        // Kolom lama gudang: beri nilai aman saat membuat lokasi baru (tidak menimpa data lama)
        if ($creating && Schema::hasColumn('warehouses', 'capacity')) {
            $attrs['capacity'] = 0;
        }

        return $attrs;
    }

    private function ok(Request $request, string $message, ?Warehouse $warehouse = null): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'warehouse' => $warehouse ? [
                    'id' => $warehouse->code,
                    'name' => $warehouse->name,
                    'location' => $warehouse->address,
                    'status' => $warehouse->status,
                ] : null,
            ]);
        }

        return redirect()->route('warehouse.index')->with('success', $message);
    }

    private function fail(Request $request, string $message, int $status = 500): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        return back()->withInput()->with('error', $message);
    }
}
