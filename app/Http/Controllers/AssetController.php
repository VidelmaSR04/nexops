<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::with(['category', 'inventoryItem', 'warehouse', 'customer', 'tickets', 'activityLogs' => fn ($query) => $query->latest()])->get()->map(function ($asset) {
            $location = $asset->customer?->name
                ?? $asset->location_name
                ?? $asset->warehouse?->name
                ?? 'Belum ditentukan';

            return [
                'id' => $asset->asset_code,
                'name' => $asset->name,
                'category' => $asset->category?->name ?? 'Perangkat Jaringan',
                'serial' => $asset->serial_number ?? '-',
                'inventory_item_id' => $asset->inventory_item_id,
                'location' => $location,
                'warehouse_id' => $asset->warehouse_id,
                'customer_id' => $asset->customer_id,
                'status' => $asset->status,
                'vendor' => $asset->vendor ?? '-',
                'purchase_date' => $asset->purchase_date?->format('d F Y') ?? '-',
                'purchase_date_value' => $asset->purchase_date?->format('Y-m-d'),
                'warranty' => $asset->warranty ?? '-',
                'warranty_months' => $asset->warranty_months,
                'description' => $asset->description ?? '',
                'specs' => $asset->specs ?? '',
                'icon' => $asset->icon ?? 'router',
                'history' => $asset->activityLogs->map(fn ($log) => [
                    'title' => (string) str($log->action)->replace('_', ' ')->title(),
                    'date' => $log->created_at?->format('d M Y H:i') ?? '-',
                    'desc' => $log->description ?? '',
                ])->values()->toArray(),
                'related_tickets' => $asset->tickets->count(),
                'related_devices' => $asset->inventoryItem?->assets()->where('id', '!=', $asset->id)->count() ?? 0,
                'docs' => [],
            ];
        })->toArray();

        $summary = [
            'total_asset' => Asset::count(),
            'active' => Asset::where('status', 'Aktif')->count(),
            'maintenance' => Asset::where('status', 'Maintenance')->count(),
            'broken' => Asset::where('status', 'Rusak')->count(),
        ];

        return view('assets', [
            'assets' => $assets,
            'summary' => $summary,
            'categories' => Category::orderBy('name')->pluck('name'),
            'warehouses' => Warehouse::where('status', 'Aktif')->orderBy('name')->get(['id', 'name']),
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'inventoryItems' => InventoryItem::with('category')->orderBy('name')->get(['id', 'category_id', 'sku', 'name']),
            'assetPreview' => $this->nextCode(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:80', 'unique:assets,serial_number'],
            'inventory_item_id' => ['nullable', 'integer', Rule::exists('inventory_items', 'id')->whereNull('deleted_at')],
            'category' => ['required', 'string', 'max:255'],
            'warehouse_id' => ['nullable', 'integer', Rule::exists('warehouses', 'id')->whereNull('deleted_at')],
            'customer_id' => ['nullable', 'integer', Rule::exists('customers', 'id')->whereNull('deleted_at')],
            'location_name' => ['nullable', 'string', 'max:100'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:Aktif,Maintenance,Rusak,Nonaktif'],
            'purchase_date' => ['nullable', 'date', 'before_or_equal:today'],
            'warranty_months' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'description' => ['nullable', 'string'],
            'specs' => ['nullable', 'string'],
        ]);

        $this->ensureSingleAssetLocation($validated);

        $inventoryItem = isset($validated['inventory_item_id']) ? InventoryItem::findOrFail($validated['inventory_item_id']) : null;
        if ($inventoryItem?->category && $validated['category'] !== $inventoryItem->category->name) {
            throw ValidationException::withMessages([
                'category' => 'Kategori aset harus sama dengan kategori produk inventory yang ditautkan.',
            ]);
        }
        $category = $inventoryItem?->category ?? Category::firstOrCreate(['name' => $validated['category']]);
        $customerId = $validated['customer_id'] ?? null;

        $asset = Asset::create([
            'asset_code' => $this->nextCode(),
            'name' => $validated['name'],
            'category_id' => $category->id,
            'inventory_item_id' => $inventoryItem?->id,
            'serial_number' => $validated['serial_number'] ?: null,
            'warehouse_id' => $customerId ? null : ($validated['warehouse_id'] ?? null),
            'customer_id' => $customerId,
            'location_name' => $customerId ? null : ($validated['location_name'] ?? null),
            'status' => $validated['status'],
            'vendor' => $validated['vendor'] ?? null,
            'purchase_date' => $validated['purchase_date'] ?? null,
            'warranty_months' => $validated['warranty_months'] ?? null,
            'warranty' => isset($validated['warranty_months']) ? $validated['warranty_months'].' Bulan' : null,
            'specs' => $validated['specs'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);
        $asset->activityLogs()->create([
            'user_id' => $request->user()?->id,
            'user_name' => $request->user()?->name,
            'action' => 'created',
            'description' => 'Aset didaftarkan.',
        ]);

        return redirect()->route('assets.index')->with('success', 'Aset berhasil ditambahkan.');
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:80', 'unique:assets,serial_number,'.$asset->id],
            'inventory_item_id' => ['nullable', 'integer', Rule::exists('inventory_items', 'id')->whereNull('deleted_at')],
            'category' => ['required', 'string', 'max:255'],
            'warehouse_id' => ['nullable', 'integer', Rule::exists('warehouses', 'id')->whereNull('deleted_at')],
            'customer_id' => ['nullable', 'integer', Rule::exists('customers', 'id')->whereNull('deleted_at')],
            'location_name' => ['nullable', 'string', 'max:100'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:Aktif,Maintenance,Rusak,Nonaktif'],
            'purchase_date' => ['nullable', 'date', 'before_or_equal:today'],
            'warranty_months' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'description' => ['nullable', 'string'],
            'specs' => ['nullable', 'string'],
        ]);

        $this->ensureSingleAssetLocation($validated);

        $inventoryItem = isset($validated['inventory_item_id']) ? InventoryItem::findOrFail($validated['inventory_item_id']) : null;
        if ($inventoryItem?->category && $validated['category'] !== $inventoryItem->category->name) {
            throw ValidationException::withMessages([
                'category' => 'Kategori aset harus sama dengan kategori produk inventory yang ditautkan.',
            ]);
        }
        $category = $inventoryItem?->category ?? Category::firstOrCreate(['name' => $validated['category']]);
        $customerId = $validated['customer_id'] ?? null;

        $asset->update([
            'name' => $validated['name'],
            'category_id' => $category->id,
            'inventory_item_id' => $inventoryItem?->id,
            'serial_number' => $validated['serial_number'] ?: null,
            'warehouse_id' => $customerId ? null : ($validated['warehouse_id'] ?? null),
            'customer_id' => $customerId,
            'location_name' => $customerId ? null : ($validated['location_name'] ?? null),
            'status' => $validated['status'],
            'vendor' => $validated['vendor'] ?? null,
            'purchase_date' => $validated['purchase_date'] ?? null,
            'warranty_months' => $validated['warranty_months'] ?? null,
            'warranty' => isset($validated['warranty_months']) ? $validated['warranty_months'].' Bulan' : null,
            'specs' => $validated['specs'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);
        $asset->activityLogs()->create([
            'user_id' => $request->user()?->id,
            'user_name' => $request->user()?->name,
            'action' => 'updated',
            'description' => 'Data aset diperbarui.',
        ]);

        return redirect()->route('assets.index')->with('success', 'Data aset berhasil diperbarui.');
    }

    public function destroy(Request $request, Asset $asset)
    {
        $asset->activityLogs()->create([
            'user_id' => $request->user()?->id,
            'user_name' => $request->user()?->name,
            'action' => 'deleted',
            'description' => 'Aset diarsipkan dari daftar aktif.',
        ]);
        $asset->delete();

        return redirect()->route('assets.index')->with('success', 'Aset berhasil dihapus.');
    }

    public function updateStatus(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:Aktif,Maintenance,Rusak,Nonaktif'],
        ]);

        $previousStatus = $asset->status;
        $asset->update(['status' => $validated['status']]);
        $asset->activityLogs()->create([
            'user_id' => $request->user()?->id,
            'user_name' => $request->user()?->name,
            'action' => 'status_changed',
            'description' => 'Status diubah dari '.$previousStatus.' menjadi '.$validated['status'].'.',
        ]);

        return redirect()->route('assets.index')->with('success', 'Status aset berhasil diperbarui.');
    }

    public function storeMaintenance(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'work_type' => ['required', 'string', 'max:50'],
            'technician' => ['required', 'string', 'max:80'],
            'note' => ['required', 'string', 'max:110'],
        ]);

        $asset->activityLogs()->create([
            'user_id' => $request->user()?->id,
            'user_name' => $request->user()?->name,
            'action' => 'maintenance',
            'description' => $validated['work_type'].' oleh '.$validated['technician'].': '.$validated['note'],
        ]);
        $asset->update(['status' => 'Maintenance']);

        return redirect()->route('assets.index')->with('success', 'Catatan maintenance tersimpan dan status aset diperbarui.');
    }

    private function nextCode(): string
    {
        $highest = Asset::withTrashed()->pluck('asset_code')->map(function ($code) {
            return preg_match('/^AST-(\d+)$/', $code, $matches) ? (int) $matches[1] : 0;
        })->max() ?? 0;

        return 'AST-'.str_pad((string) ($highest + 1), 3, '0', STR_PAD_LEFT);
    }

    private function ensureSingleAssetLocation(array $validated): void
    {
        if (isset($validated['warehouse_id'], $validated['customer_id'])) {
            throw ValidationException::withMessages([
                'warehouse_id' => 'Pilih gudang atau customer sebagai lokasi aset, jangan keduanya.',
                'customer_id' => 'Pilih gudang atau customer sebagai lokasi aset, jangan keduanya.',
            ]);
        }
    }
}
