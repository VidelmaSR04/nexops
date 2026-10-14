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

    public function test_inventory_crud_generates_sku_and_tracks_stock_by_warehouse(): void
    {
        $this->actingAs(User::factory()->create());
        $warehouse = $this->createWarehouse('WH-001', 'Gudang Utama');

        $this->post(route('inventory.store'), [
            'sku' => 'MANUAL-SKU',
            'name' => 'Router Test',
            'category' => 'Perangkat Jaringan',
            'unit' => 'Unit',
            'min_stock' => 2,
            'stock' => 5,
            'warehouse_id' => $warehouse->id,
        ])->assertRedirect(route('inventory.index'));

        $item = InventoryItem::where('sku', 'PRD-001')->firstOrFail();
        $this->assertDatabaseHas('warehouse_stocks', [
            'inventory_item_id' => $item->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 5,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'inventory_item_id' => $item->id,
            'type' => 'Masuk',
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
            'stock' => 8,
            'warehouse_id' => $warehouse->id,
        ])->assertRedirect(route('inventory.index'));

        $this->assertSame('Router Updated', $item->fresh()->name);
        $this->assertSame(8, (int) $item->stocks()->sum('quantity'));
        $this->assertSame('Masuk', StockMovement::latest('id')->firstOrFail()->type);

        $this->delete(route('inventory.destroy', $item->sku))
            ->assertRedirect(route('inventory.index'));

        $this->assertSoftDeleted('inventory_items', ['id' => $item->id]);
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

    public function test_warehouse_crud_and_stock_movements_enforce_capacity_and_availability(): void
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

        $source = Warehouse::where('code', 'WH-001')->firstOrFail();
        $this->get(route('warehouse.index'))
            ->assertOk()
            ->assertSee('id="edit-status" name="status"', false);

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
            'name' => $source->name,
            'address' => $source->address,
            'area' => $source->area,
            'capacity' => 20,
            'status' => 'Maintenance',
        ])->assertRedirect(route('warehouse.index'));
        $this->assertSame('Maintenance', $source->fresh()->status);

        $this->put(route('warehouse.update', $source->code), [
            'code' => 'MANUAL-UPDATED',
            'name' => $source->name,
            'address' => $source->address,
            'area' => $source->area,
            'capacity' => 5,
            'status' => 'Maintenance',
        ])->assertSessionHasErrors('capacity');

        $this->delete(route('warehouse.destroy', $source->code))
            ->assertSessionHasErrors('warehouse');
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
