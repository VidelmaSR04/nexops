<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationsCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_crud_tracks_stock_by_warehouse(): void
    {
        $this->actingAs(User::factory()->create());
        $warehouse = $this->createWarehouse('WH-001', 'Gudang Utama');

        $this->post(route('inventory.store'), [
            'sku' => 'MANUAL-SKU',
            'name' => 'Router Test',
            'category' => 'Perangkat Jaringan',
            'unit' => 'Unit',
            'min_stock' => 2,
            'quantity' => 5,
            'warehouse' => $warehouse->name,
        ])->assertRedirect(route('inventory.index'));

        $item = InventoryItem::where('sku', 'MANUAL-SKU')->firstOrFail();
        $this->assertDatabaseHas('warehouse_stocks', [
            'inventory_item_id' => $item->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 5,
        ]);
        $this->get(route('inventory.index'))
            ->assertOk()
            ->assertSee($item->sku)
            ->assertSee('Router Test');

        $this->put(route('inventory.update', $item->sku), [
            'sku' => 'MANUAL-UPDATED',
            'name' => 'Router Updated',
            'category' => 'Perangkat Jaringan',
            'unit' => 'Unit',
            'min_stock' => 3,
            'quantity' => 8,
            'warehouse' => $warehouse->name,
        ])->assertRedirect(route('inventory.index'));

        $this->assertSame('Router Updated', $item->fresh()->name);
        $this->assertSame(8, (int) $item->stocks()->sum('quantity'));

        WarehouseStock::where('inventory_item_id', $item->id)
            ->where('warehouse_id', $warehouse->id)
            ->update(['quantity' => 0]);
        $movement = StockMovement::create([
            'inventory_item_id' => $item->id,
            'warehouse_id' => $warehouse->id,
            'type' => 'Masuk',
            'quantity' => 5,
        ]);

        $this->delete(route('inventory.destroy', $item->fresh()->sku))
            ->assertRedirect(route('inventory.index'));

        $this->assertSoftDeleted('inventory_items', ['id' => $item->id]);
        $this->assertDatabaseHas('warehouse_stocks', [
            'inventory_item_id' => $item->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 0,
        ]);
        $this->assertDatabaseHas('stock_movements', ['id' => $movement->id]);
        $this->assertSame(0, (int) $item->stocks()->sum('quantity'));
    }

    public function test_asset_crud_generates_code_and_connects_to_warehouse_or_customer(): void
    {
        $this->actingAs(User::factory()->create());
        $warehouse = $this->createWarehouse('WH-001', 'Gudang Utama');
        $item = InventoryItem::create([
            'sku' => 'PRD-001',
            'name' => 'ONT Product',
            'unit' => 'Unit',
            'min_stock' => 1,
            'status' => 'Aktif',
        ]);
        $customer = Customer::create([
            'customer_code' => 'CST0001',
            'name' => 'Customer Test',
            'status' => 'Aktif',
        ]);

        $this->post(route('assets.store'), [
            'name' => 'ONT Invalid',
            'category' => 'Modem/ONT',
            'inventory_item_id' => $item->id,
            'warehouse_id' => $warehouse->id,
            'customer_id' => $customer->id,
            'status' => 'Aktif',
        ])->assertSessionHasErrors(['warehouse_id', 'customer_id']);

        $this->post(route('assets.store'), [
            'asset_code' => 'MANUAL-ASSET',
            'name' => 'ONT Test',
            'serial_number' => 'SN-ASSET-01',
            'category' => 'Modem/ONT',
            'inventory_item_id' => $item->id,
            'warehouse_id' => $warehouse->id,
            'status' => 'Aktif',
            'warranty_months' => 12,
        ])->assertRedirect(route('assets.index'));

        $asset = Asset::where('asset_code', 'AST-001')->firstOrFail();
        $this->assertSame($warehouse->id, $asset->warehouse_id);
        $this->assertSame($item->id, $asset->inventory_item_id);
        $this->get(route('assets.index'))
            ->assertOk()
            ->assertSee($asset->asset_code)
            ->assertSee('ONT Test');

        $this->put(route('assets.update', $asset->asset_code), [
            'asset_code' => 'MANUAL-UPDATED',
            'name' => 'ONT Updated',
            'serial_number' => 'SN-ASSET-02',
            'category' => 'Modem/ONT',
            'customer_id' => $customer->id,
            'status' => 'Maintenance',
            'warranty_months' => 24,
        ])->assertRedirect(route('assets.index'));

        $this->assertSame($customer->id, $asset->fresh()->customer_id);
        $this->assertNull($asset->fresh()->warehouse_id);
        $this->assertSame('24 Bulan', $asset->fresh()->warranty);

        $this->delete(route('assets.destroy', $asset->asset_code))
            ->assertRedirect(route('assets.index'));
        $this->assertSoftDeleted('assets', ['id' => $asset->id]);
    }

    public function test_warehouse_crud_and_stock_movements_enforce_location_availability(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('warehouse.store'), [
            'code' => 'MANUAL-WAREHOUSE',
            'name' => 'Gudang Asal',
            'address' => 'Jl. Test 1',
            'area' => 'Situbondo',
            'capacity' => 20,
            'status' => 'Aktif',
        ])->assertRedirect(route('warehouse.index'));

        $source = Warehouse::where('code', 'MANUAL-WAREHOUSE')->firstOrFail();
        $this->get(route('warehouse.index'))
            ->assertOk()
            ->assertSee('id="edit-status"', false);

        $destination = $this->createWarehouse('WH-002', 'Gudang Tujuan', 20);
        $item = InventoryItem::create([
            'sku' => 'PRD-001',
            'name' => 'Kabel Test',
            'unit' => 'Roll',
            'min_stock' => 1,
            'status' => 'Aktif',
        ]);

        $this->post(route('warehouse.movements.store', $source->code), [
            'inventory_item_id' => $item->id,
            'type' => 'Masuk',
            'quantity' => 10,
            'reference' => 'PO-001',
        ])->assertRedirect(route('warehouse.index'));

        $this->post(route('warehouse.movements.store', $source->code), [
            'inventory_item_id' => $item->id,
            'type' => 'Transfer',
            'quantity' => 4,
            'to_warehouse_id' => $destination->id,
        ])->assertRedirect(route('warehouse.index'));

        $this->assertSame(6, (int) WarehouseStock::where('warehouse_id', $source->id)->value('quantity'));
        $this->assertSame(4, (int) WarehouseStock::where('warehouse_id', $destination->id)->value('quantity'));
        $this->get(route('warehouse.index'))
            ->assertOk()
            ->assertSee($source->code)
            ->assertSee('Gudang Asal');

        $this->put(route('warehouse.update', $source->code), [
            'code' => $source->code,
            'name' => $source->name,
            'address' => $source->address,
            'area' => $source->area,
            'capacity' => 20,
            'status' => 'Maintenance',
        ])->assertRedirect(route('warehouse.index'));
        $this->assertSame('Maintenance', $source->fresh()->status);

        $this->put(route('warehouse.update', $source->code), [
            'code' => 'WH-002',
            'name' => $source->name,
            'address' => $source->address,
            'area' => $source->area,
            'capacity' => 5,
            'status' => 'Maintenance',
        ])->assertSessionHasErrors('code');

        $this->delete(route('warehouse.destroy', $source->code))
            ->assertSessionHas('error', 'Lokasi masih memiliki aset atau stok, pindahkan atau hapus datanya terlebih dahulu.');
    }

    public function test_warehouse_update_status_and_asset_restriction(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $warehouse = $this->createWarehouse('WH-STAT', 'Gudang Status Test');

        // Update status to Maintenance
        $this->patchJson(route('warehouse.update-status', $warehouse->code), [
            'status' => 'Maintenance',
        ])->assertOk()
            ->assertJsonPath('message', 'Status lokasi berhasil diperbarui.');

        $this->assertSame('Maintenance', $warehouse->fresh()->status);
        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => Warehouse::class,
            'subject_id' => $warehouse->id,
            'action' => 'status_changed',
            'user_id' => $user->id,
        ]);

        // Create an active asset linked to warehouse
        Asset::create([
            'asset_code' => 'AST-WH-01',
            'name' => 'Router di Gudang',
            'warehouse_id' => $warehouse->id,
            'status' => 'Aktif',
        ]);

        // Cannot change to Nonaktif because of active asset
        $this->patchJson(route('warehouse.update-status', $warehouse->code), [
            'status' => 'Nonaktif',
        ])->assertStatus(422)
            ->assertJsonPath('message', 'Status tidak dapat diubah menjadi Nonaktif karena masih ada aset aktif di lokasi ini.');

        $this->assertNotSame('Nonaktif', $warehouse->fresh()->status);

        // Validation error on invalid status
        $this->patchJson(route('warehouse.update-status', $warehouse->code), [
            'status' => 'StatusPalsu',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }

    public function test_warehouse_crud_via_ui_json_endpoints(): void
    {
        $this->actingAs(User::factory()->create());

        // Create via POST
        $this->postJson(route('warehouse.store'), [
            'code' => 'WH-UI-01',
            'name' => 'Dinas Pendidikan',
            'address' => 'Jl. Pemuda No. 10',
            'status' => 'Aktif',
        ])->assertOk()
            ->assertJsonPath('message', 'Lokasi berhasil ditambahkan.');

        $this->assertDatabaseHas('warehouses', [
            'code' => 'WH-UI-01',
            'name' => 'Dinas Pendidikan',
            'status' => 'Aktif',
        ]);

        // Edit via PUT
        $this->putJson(route('warehouse.update', 'WH-UI-01'), [
            'code' => 'WH-UI-01',
            'name' => 'Dinas Pendidikan Situbondo',
            'address' => 'Jl. Pemuda No. 12',
            'status' => 'Aktif',
        ])->assertOk()
            ->assertJsonPath('message', 'Lokasi berhasil diperbarui.');

        $this->assertDatabaseHas('warehouses', [
            'code' => 'WH-UI-01',
            'name' => 'Dinas Pendidikan Situbondo',
        ]);

        // Delete via DELETE
        $this->deleteJson(route('warehouse.destroy', 'WH-UI-01'))
            ->assertOk()
            ->assertJsonPath('message', 'Lokasi berhasil dihapus.');

        $this->assertSoftDeleted('warehouses', [
            'code' => 'WH-UI-01',
        ]);
    }

    public function test_asset_edit_form_persists_changes_and_displays_opd(): void
    {
        $this->actingAs(User::factory()->create());
        $customer = Customer::create([
            'customer_code' => 'CST-OPD-01',
            'name' => 'Dinas Kominfo',
            'status' => 'Aktif',
        ]);

        $asset = Asset::create([
            'asset_code' => 'AST-EDIT-01',
            'name' => 'Switch Core',
            'customer_id' => $customer->id,
            'status' => 'Aktif',
            'serial_number' => 'SN-SW-01',
        ]);

        // Verify index view has OPD key and shows customer name
        $response = $this->get(route('assets.index'))->assertOk();
        $response->assertSee('Dinas Kominfo');
        $response->assertViewHas('assets', function (array $assets): bool {
            $item = collect($assets)->firstWhere('id', 'AST-EDIT-01');

            return $item !== null && $item['opd'] === 'Dinas Kominfo';
        });

        // Edit asset via PUT JSON endpoint (used by fetch)
        $this->putJson(route('assets.update', $asset->asset_code), [
            'name' => 'Switch Core Updated',
            'category' => 'Perangkat Jaringan',
            'customer_id' => $customer->id,
            'status' => 'Maintenance',
            'serial_number' => 'SN-SW-01',
        ])->assertOk()
            ->assertJsonPath('message', 'Data aset berhasil diperbarui.');

        $this->assertSame('Switch Core Updated', $asset->fresh()->name);
        $this->assertSame('Maintenance', $asset->fresh()->status);

        // Update status via PATCH JSON endpoint (used by action-status-select fetch)
        $this->patchJson(route('assets.update-status', $asset->asset_code), [
            'status' => 'Rusak',
        ])->assertOk()
            ->assertJsonPath('message', 'Status aset berhasil diperbarui.');

        $this->assertSame('Rusak', $asset->fresh()->status);
    }

    public function test_inventory_serial_example_persists_via_edit(): void
    {
        $this->actingAs(User::factory()->create());
        $item = InventoryItem::create([
            'sku' => 'INV-SN-01',
            'name' => 'Router Mikrotik',
            'unit' => 'Unit',
            'min_stock' => 1,
            'status' => 'Aktif',
            'serial_example' => 'SN-OLD-001',
        ]);

        $this->putJson(route('inventory.update', $item->sku), [
            'sku' => $item->sku,
            'name' => $item->name,
            'serial_example' => 'ZTE001234',
        ])->assertOk();

        $this->assertSame('ZTE001234', $item->fresh()->serial_example);

        $response = $this->get(route('inventory.index'))->assertOk();
        $response->assertSee('ZTE001234');
        $response->assertViewHas('products', function (array $products): bool {
            $product = collect($products)->firstWhere('id', 'INV-SN-01');

            return $product !== null && $product['serial_example'] === 'ZTE001234';
        });
    }

    private function createWarehouse(string $code, string $name, int $capacity = 100): Warehouse
    {
        return Warehouse::create([
            'code' => $code,
            'name' => $name,
            'capacity' => $capacity,
            'status' => 'Aktif',
            'area' => 'Situbondo',
            'address' => 'Jl. Test',
        ]);
    }
}
