<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class InventoryConditionAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_condition_and_availability_can_be_saved_and_displayed_separately(): void
    {
        $this->actingAs(User::factory()->create());

        $warehouse = Warehouse::create([
            'code' => 'WH-COND',
            'name' => 'Gudang Kondisi',
            'address' => 'Alamat',
            'status' => 'Aktif',
        ]);

        $this->postJson(route('inventory.store'), [
            'sku' => 'INV-001',
            'name' => 'Router Test',
            'category' => 'Perangkat Jaringan',
            'unit' => 'Unit',
            'quantity' => 2,
            'min_stock' => 1,
            'warehouse' => $warehouse->name,
            'condition' => 'Baru',
            'availability' => 'Sedang Digunakan',
        ])->assertOk()
            ->assertJsonPath('item.condition', 'Baru')
            ->assertJsonPath('item.availability', 'Sedang Digunakan');

        $item = InventoryItem::where('sku', 'INV-001')->firstOrFail();
        $this->assertDatabaseHas('inventory_items', [
            'id' => $item->id,
            'condition' => 'Baru',
            'availability' => 'Sedang Digunakan',
        ]);

        $this->putJson(route('inventory.update', $item->sku), [
            'sku' => 'INV-001',
            'name' => 'Router Test',
            'condition' => 'Perlu Perbaikan',
            'availability' => 'Sedang Diperbaiki',
        ])->assertOk()
            ->assertJsonPath('item.condition', 'Perlu Perbaikan')
            ->assertJsonPath('item.availability', 'Sedang Diperbaiki');

        $this->get(route('inventory.index'))
            ->assertOk()
            ->assertSee('Baru')
            ->assertSee('Baik')
            ->assertSee('Perlu Perbaikan')
            ->assertSee('Rusak')
            ->assertSee('Tersedia')
            ->assertSee('Sedang Digunakan')
            ->assertSee('Sedang Diperbaiki')
            ->assertSee('Hilang')
            ->assertSee('Status Ketersediaan');
    }

    public function test_inventory_items_default_to_good_and_available(): void
    {
        $this->actingAs(User::factory()->create());

        $this->postJson(route('inventory.store'), [
            'sku' => 'INV-003',
            'name' => 'Router Default',
        ])->assertOk();

        $this->assertDatabaseHas('inventory_items', [
            'sku' => 'INV-003',
            'condition' => 'Baik',
            'availability' => 'Tersedia',
        ]);
    }

    public function test_inventory_stock_level_rules(): void
    {
        $this->assertSame('Habis', InventoryItem::stockLevel(0, 5));
        $this->assertSame('Habis', InventoryItem::stockLevel(-1, 5));
        $this->assertSame('Menipis', InventoryItem::stockLevel(5, 5));
        $this->assertSame('Aman', InventoryItem::stockLevel(6, 5));
        $this->assertSame('Aman', InventoryItem::stockLevel(2, 0));
        $this->assertSame('Aman', InventoryItem::stockLevel(0, 5, 'Nonaktif'));
    }

    public function test_inventory_status_normalization_migration_resets_derived_statuses(): void
    {
        $this->createInventoryItem('INV-MIG-LOW')->forceFill(['status' => 'Low Stock'])->save();
        $this->createInventoryItem('INV-MIG-OUT')->forceFill(['status' => 'Habis'])->save();
        $this->createInventoryItem('INV-MIG-ACTIVE')->forceFill(['status' => 'Nonaktif'])->save();

        $migration = require database_path('migrations/2026_10_22_000000_normalize_inventory_item_stock_status.php');
        $migration->up();

        $this->assertSame('Aktif', DB::table('inventory_items')->where('sku', 'INV-MIG-LOW')->value('status'));
        $this->assertSame('Aktif', DB::table('inventory_items')->where('sku', 'INV-MIG-OUT')->value('status'));
        $this->assertSame('Nonaktif', DB::table('inventory_items')->where('sku', 'INV-MIG-ACTIVE')->value('status'));
    }

    public function test_inventory_summary_counts_low_and_out_of_stock_items(): void
    {
        $this->actingAs(User::factory()->create());
        $warehouse = Warehouse::create([
            'code' => 'WH-SUM',
            'name' => 'Gudang Summary',
            'address' => 'Alamat',
            'status' => 'Aktif',
        ]);
        $outOfStock = $this->createInventoryItem('INV-SUM-OUT');
        $lowStock = $this->createInventoryItem('INV-SUM-LOW');
        $lowStock->forceFill(['min_stock' => 3])->save();
        $safe = $this->createInventoryItem('INV-SUM-SAFE');
        $inactive = $this->createInventoryItem('INV-SUM-INACTIVE')->forceFill(['status' => 'Nonaktif']);
        $inactive->save();

        foreach ([[$lowStock, 2], [$safe, 10]] as [$item, $quantity]) {
            WarehouseStock::create([
                'warehouse_id' => $warehouse->id,
                'inventory_item_id' => $item->id,
                'quantity' => $quantity,
                'min_quantity' => 1,
            ]);
        }

        $this->get(route('inventory.index'))
            ->assertOk()
            ->assertViewHas('summary', function (array $summary): bool {
                return $summary['low_stock'] === 2 && $summary['out_of_stock'] === 1;
            });

        $this->assertSame('Aman', InventoryItem::stockLevel(0, 1, $inactive->status));
        $this->assertSame('Habis', InventoryItem::stockLevel(0, 1, $outOfStock->status));
    }

    public function test_inventory_rejects_condition_and_availability_outside_the_supported_options(): void
    {
        $this->actingAs(User::factory()->create());

        $this->postJson(route('inventory.store'), [
            'sku' => 'INV-002',
            'name' => 'Router Test',
            'condition' => 'Rusak Ringan',
            'availability' => 'Dipinjam',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['condition', 'availability']);
    }

    public function test_inventory_view_has_unique_ids_and_location_labels_for_each_item(): void
    {
        $this->actingAs(User::factory()->create());
        $warehouseA = Warehouse::create([
            'code' => 'WH-A',
            'name' => 'Gudang Situbondo',
            'address' => 'Alamat A',
            'status' => 'Aktif',
        ]);
        $warehouseB = Warehouse::create([
            'code' => 'WH-B',
            'name' => 'Gudang Besuki',
            'address' => 'Alamat B',
            'status' => 'Aktif',
        ]);
        Warehouse::create([
            'code' => 'WH-C',
            'name' => 'Gudang Nonaktif',
            'address' => 'Alamat C',
            'status' => 'Nonaktif',
        ]);

        $singleLocationItem = $this->createInventoryItem('INV-LOC-1');
        $singleLocationItem->forceFill(['min_stock' => 5])->save();
        $multipleLocationsItem = $this->createInventoryItem('INV-LOC-2');
        $noStockItem = $this->createInventoryItem('INV-LOC-3');

        WarehouseStock::create([
            'warehouse_id' => $warehouseA->id,
            'inventory_item_id' => $singleLocationItem->id,
            'quantity' => 4,
            'min_quantity' => 1,
        ]);
        WarehouseStock::create([
            'warehouse_id' => $warehouseA->id,
            'inventory_item_id' => $multipleLocationsItem->id,
            'quantity' => 9,
            'min_quantity' => 1,
        ]);
        WarehouseStock::create([
            'warehouse_id' => $warehouseB->id,
            'inventory_item_id' => $multipleLocationsItem->id,
            'quantity' => 3,
            'min_quantity' => 1,
        ]);

        $response = $this->get(route('inventory.index'))->assertOk();
        $response->assertSee('title="Gudang Situbondo: 9 · Gudang Besuki: 3"', false);
        $response->assertDontSee('id="stock-alert-banner"', false);
        $response->assertDontSee('data-stock-alert-item=', false);
        $response->assertDontSee('id="stock-alert-view-all"', false);
        $response->assertSee('id="filter-status"', false);
        $response->assertSee('id="filter-stock-level"', false);
        $response->assertSee('Semua Ketersediaan');
        $response->assertSee('Semua Level Stok');
        $response->assertSee('Perlu Restock (Menipis &amp; Habis)', false);
        $response->assertSee('>Menipis</option>', false);
        $response->assertSee('>Habis</option>', false);
        $response->assertSee('data-stock-level="Habis"', false);
        $response->assertSee('data-stock-level="Menipis"', false);
        $response->assertSee('Perlu restock dulu');
        $response->assertSee('Stok habis');
        $response->assertSee('segera restock');
        $response->assertSee('Min: 5 Unit');
        $response->assertSee('id="edit-warehouse-stocks"', false);
        $response->assertSee('Stok per Gudang');
        $response->assertDontSee('id="edit-warehouse"', false);
        $response->assertSee("input.disabled = warehouse.status === 'Nonaktif';", false);

        preg_match_all('/\bid="([^"]+)"/', $response->getContent(), $idMatches);
        $this->assertCount(count(array_unique($idMatches[1])), $idMatches[1], 'Semua ID elemen harus unik.');

        $response->assertViewHas('products', function (array $products) use ($warehouseA, $warehouseB): bool {
            $productsById = collect($products)->keyBy('id');

            return $productsById['INV-LOC-1']['location_label'] === 'Gudang Situbondo'
                && $productsById['INV-LOC-1']['warehouse_count'] === 1
                && $productsById['INV-LOC-2']['location_label'] === 'Gudang Situbondo +1 lokasi'
                && $productsById['INV-LOC-2']['warehouse_count'] === 2
                && $productsById['INV-LOC-2']['warehouses'][0]['id'] === $warehouseA->id
                && $productsById['INV-LOC-2']['warehouses'][1]['id'] === $warehouseB->id
                && $productsById['INV-LOC-3']['location_label'] === '-'
                && $productsById['INV-LOC-3']['warehouse_count'] === 0;
        });
    }

    private function createInventoryItem(string $sku): InventoryItem
    {
        return InventoryItem::create([
            'sku' => $sku,
            'name' => 'Barang '.$sku,
            'unit' => 'Unit',
            'min_stock' => 1,
            'status' => 'Aktif',
        ]);
    }
}
