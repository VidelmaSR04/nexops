<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Ticket;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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
                    'ticket'     => $t->ticket_number,
                    'date'       => $t->created_at?->format('d M Y') ?? '-',
                    'complaint'  => $t->title ?? '-',
                    'technician' => $t->assigned_tech_name ?: 'Belum ditugaskan',
                    'status'     => $t->status ?? '-',
                ])
                ->values()
                ->toArray();

            return [
                'id'           => $w->code,
                'name'         => $w->name,
                'location'     => $w->address ?? '-',
                'status'       => $w->status ?? 'Aktif',
                'assets_count' => (int) ($assetCounts[$w->id] ?? 0),
                'repairs'      => $repairs,
            ];
        })->toArray();

        return view('warehouse', ['warehouses' => $warehouses]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate($this->rules());

        try {
            $warehouse = new Warehouse();
            $warehouse->forceFill($this->attributes($data, true))->save();
        } catch (\Throwable $e) {
            Log::error('WarehouseController@store: ' . $e->getMessage());
            return $this->fail($request, 'Gagal menyimpan lokasi.');
        }

        return $this->ok($request, 'Lokasi berhasil ditambahkan.', $warehouse);
    }

    public function update(Request $request, string $code): JsonResponse|RedirectResponse
    {
        $warehouse = Warehouse::where('code', $code)->firstOrFail();
        $data = $request->validate($this->rules($warehouse->id));

        try {
            $warehouse->forceFill($this->attributes($data))->save();
        } catch (\Throwable $e) {
            Log::error('WarehouseController@update: ' . $e->getMessage());
            return $this->fail($request, 'Gagal memperbarui lokasi.');
        }

        return $this->ok($request, 'Lokasi berhasil diperbarui.', $warehouse);
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
            Log::error('WarehouseController@destroy: ' . $e->getMessage());
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
            'code'    => 'required|string|max:30|unique:warehouses,code' . ($ignoreId ? ',' . $ignoreId : ''),
            'name'    => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'status'  => 'required|in:Aktif,Maintenance,Nonaktif',
        ];
    }

    /** forceFill dipakai agar tidak bergantung pada $fillable. */
    private function attributes(array $data, bool $creating = false): array
    {
        $attrs = [
            'code'    => $data['code'],
            'name'    => $data['name'],
            'address' => $data['address'],
            'status'  => $data['status'],
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
                'message'   => $message,
                'warehouse' => $warehouse ? [
                    'id'       => $warehouse->code,
                    'name'     => $warehouse->name,
                    'location' => $warehouse->address,
                    'status'   => $warehouse->status,
                ] : null,
            ]);
        }

        return back()->with('success', $message);
    }

    private function fail(Request $request, string $message, int $status = 500): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        return back()->withInput()->with('error', $message);
    }
}
