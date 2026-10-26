<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\InventoryItem;
use App\Models\Ticket;
use App\Models\TicketMaterial;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarehouseStockMovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_masuk_menambah_stok_dan_mencatat_pergerakan_dan_aktivitas(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        [$warehouse, $item] = $this->createLocationAndItem();

        $this->postJson(route('warehouse.movements.store', $warehouse->code), [
            'inventory_item_id' => $item->id,
            'type' => 'Masuk',
            'quantity' => 5,
            'reference' => 'PO-001',
            'note' => 'Penerimaan barang',
        ])->assertOk()
            ->assertJsonPath('message', 'Pergerakan stok berhasil dicatat.');

        $this->assertDatabaseHas('warehouse_stocks', [
            'warehouse_id' => $warehouse->id,
            'inventory_item_id' => $item->id,
            'quantity' => 5,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'warehouse_id' => $warehouse->id,
            'inventory_item_id' => $item->id,
            'type' => 'Masuk',
            'quantity' => 5,
            'reference' => 'PO-001',
            'user_id' => $user->id,
            'user_name' => $user->name,
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'stock_moved',
            'user_id' => $user->id,
            'subject_type' => Warehouse::class,
            'subject_id' => $warehouse->id,
        ]);
    }

    public function test_keluar_mengurangi_stok_yang_mencukupi(): void
    {
        $this->actingAs(User::factory()->create());
        [$warehouse, $item] = $this->createLocationAndItem();
        $this->createStock($warehouse, $item, 8);

        $this->postJson(route('warehouse.movements.store', $warehouse->code), [
            'inventory_item_id' => $item->id,
            'type' => 'Keluar',
            'quantity' => 3,
        ])->assertOk();

        $this->assertSame(5, (int) WarehouseStock::where('warehouse_id', $warehouse->id)
            ->where('inventory_item_id', $item->id)
            ->value('quantity'));
        $this->assertDatabaseHas('stock_movements', [
            'warehouse_id' => $warehouse->id,
            'type' => 'Keluar',
            'quantity' => 3,
        ]);
    }

    public function test_keluar_records_activity_only_when_stock_level_worsens(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        [$warehouse, $item] = $this->createLocationAndItem();
        $item->forceFill(['min_stock' => 5])->save();
        $this->createStock($warehouse, $item, 10);

        foreach ([6, 1] as $quantity) {
            $this->postJson(route('warehouse.movements.store', $warehouse->code), [
                'inventory_item_id' => $item->id,
                'type' => 'Keluar',
                'quantity' => $quantity,
            ])->assertOk();
        }

        $this->assertDatabaseCount('activity_logs', 3);
        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => InventoryItem::class,
            'subject_id' => $item->id,
            'action' => 'low_stock',
            'user_id' => $user->id,
            'description' => 'Stok Kabel Test menipis: 4 Unit (minimum 5)',
        ]);

        $this->postJson(route('warehouse.movements.store', $warehouse->code), [
            'inventory_item_id' => $item->id,
            'type' => 'Keluar',
            'quantity' => 3,
        ])->assertOk();

        $this->assertDatabaseCount('activity_logs', 5);
        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => InventoryItem::class,
            'subject_id' => $item->id,
            'action' => 'out_of_stock',
            'user_id' => $user->id,
            'description' => 'Stok Kabel Test habis: 0 Unit (minimum 5)',
        ]);
        $this->assertSame(2, ActivityLog::where('subject_type', InventoryItem::class)
            ->where('subject_id', $item->id)
            ->count());
    }

    public function test_keluar_dengan_stok_kurang_ditolak_tanpa_perubahan_data(): void
    {
        $this->actingAs(User::factory()->create());
        [$warehouse, $item] = $this->createLocationAndItem();
        $this->createStock($warehouse, $item, 2);

        $this->postJson(route('warehouse.movements.store', $warehouse->code), [
            'inventory_item_id' => $item->id,
            'type' => 'Keluar',
            'quantity' => 3,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['quantity'])
            ->assertJsonPath('errors.quantity.0', 'Stok tidak mencukupi.');

        $this->assertSame(2, (int) WarehouseStock::where('warehouse_id', $warehouse->id)
            ->where('inventory_item_id', $item->id)
            ->value('quantity'));
        $this->assertDatabaseCount('stock_movements', 0);
        $this->assertDatabaseCount('activity_logs', 0);
    }

    public function test_transfer_memindahkan_stok_dan_mencatat_satu_pergerakan(): void
    {
        $this->actingAs(User::factory()->create());
        [$source, $item] = $this->createLocationAndItem();
        $destination = $this->createWarehouse('LOC-002', 'Lokasi Tujuan');
        $this->createStock($source, $item, 9);

        $this->postJson(route('warehouse.movements.store', $source->code), [
            'inventory_item_id' => $item->id,
            'type' => 'Transfer',
            'quantity' => 4,
            'to_warehouse_id' => $destination->id,
        ])->assertOk();

        $this->assertSame(5, (int) WarehouseStock::where('warehouse_id', $source->id)
            ->where('inventory_item_id', $item->id)
            ->value('quantity'));
        $this->assertSame(4, (int) WarehouseStock::where('warehouse_id', $destination->id)
            ->where('inventory_item_id', $item->id)
            ->value('quantity'));
        $this->assertDatabaseCount('stock_movements', 1);
        $this->assertDatabaseHas('stock_movements', [
            'warehouse_id' => $source->id,
            'to_warehouse_id' => $destination->id,
            'inventory_item_id' => $item->id,
            'type' => 'Transfer',
            'quantity' => 4,
        ]);
    }

    public function test_lokasi_nonaktif_ditolak_sebagai_asal_dan_tujuan(): void
    {
        $this->actingAs(User::factory()->create());
        [$source, $item] = $this->createLocationAndItem();
        $destination = $this->createWarehouse('LOC-002', 'Lokasi Tujuan');
        $this->createStock($source, $item, 5);

        $source->update(['status' => 'Nonaktif']);
        $this->postJson(route('warehouse.movements.store', $source->code), [
            'inventory_item_id' => $item->id,
            'type' => 'Keluar',
            'quantity' => 1,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['warehouse']);

        $source->update(['status' => 'Aktif']);
        $destination->update(['status' => 'Nonaktif']);
        $this->postJson(route('warehouse.movements.store', $source->code), [
            'inventory_item_id' => $item->id,
            'type' => 'Transfer',
            'quantity' => 1,
            'to_warehouse_id' => $destination->id,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['to_warehouse_id']);

        $this->assertSame(5, (int) WarehouseStock::where('warehouse_id', $source->id)
            ->where('inventory_item_id', $item->id)
            ->value('quantity'));
        $this->assertDatabaseCount('stock_movements', 0);
        $this->assertDatabaseCount('activity_logs', 0);
    }

    public function test_inventory_item_with_stock_cannot_be_deleted(): void
    {
        $this->actingAs(User::factory()->create());
        [$warehouse, $item] = $this->createLocationAndItem();
        $stock = $this->createStock($warehouse, $item, 1);

        $this->deleteJson(route('inventory.destroy', $item->sku))
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Peralatan tidak dapat dihapus karena masih memiliki stok.');

        $this->assertNotSoftDeleted('inventory_items', ['id' => $item->id]);
        $this->assertDatabaseHas('warehouse_stocks', [
            'id' => $stock->id,
            'quantity' => 1,
        ]);
    }

    public function test_inventory_update_changes_stock_only_at_selected_warehouse(): void
    {
        $this->actingAs(User::factory()->create());
        [$warehouseA, $item] = $this->createLocationAndItem();
        $warehouseB = $this->createWarehouse('LOC-002', 'Lokasi B');
        $this->createStock($warehouseA, $item, 5);
        $this->createStock($warehouseB, $item, 7);

        $this->putJson(route('inventory.update', $item->sku), [
            'sku' => $item->sku,
            'name' => $item->name,
            'unit' => $item->unit,
            'min_stock' => $item->min_stock,
            'warehouse' => $warehouseA->name,
            'quantity' => 11,
        ])->assertOk();

        $this->assertSame(11, (int) WarehouseStock::where('warehouse_id', $warehouseA->id)
            ->where('inventory_item_id', $item->id)
            ->value('quantity'));
        $this->assertSame(7, (int) WarehouseStock::where('warehouse_id', $warehouseB->id)
            ->where('inventory_item_id', $item->id)
            ->value('quantity'));
    }

    public function test_inventory_update_saves_each_submitted_warehouse_and_records_stock_adjustments(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        [$warehouseA, $item] = $this->createLocationAndItem();
        $warehouseB = $this->createWarehouse('LOC-002', 'Lokasi B');
        $warehouseC = $this->createWarehouse('LOC-003', 'Lokasi C');
        $warehouseD = $this->createWarehouse('LOC-004', 'Lokasi D');
        $warehouseE = $this->createWarehouse('LOC-005', 'Lokasi E');
        $this->createStock($warehouseA, $item, 5);
        $this->createStock($warehouseB, $item, 7);
        $this->createStock($warehouseE, $item, 6);

        $this->putJson(route('inventory.update', $item->sku), [
            'sku' => $item->sku,
            'name' => $item->name,
            'min_stock' => $item->min_stock,
            'stocks' => [
                ['warehouse_id' => $warehouseA->id, 'quantity' => 8],
                ['warehouse_id' => $warehouseB->id, 'quantity' => 2],
                ['warehouse_id' => $warehouseC->id, 'quantity' => 4],
                ['warehouse_id' => $warehouseD->id, 'quantity' => 0],
            ],
        ])->assertOk();

        $this->assertSame(8, (int) WarehouseStock::where('warehouse_id', $warehouseA->id)
            ->where('inventory_item_id', $item->id)
            ->value('quantity'));
        $this->assertSame(2, (int) WarehouseStock::where('warehouse_id', $warehouseB->id)
            ->where('inventory_item_id', $item->id)
            ->value('quantity'));
        $this->assertDatabaseHas('warehouse_stocks', [
            'warehouse_id' => $warehouseC->id,
            'inventory_item_id' => $item->id,
            'quantity' => 4,
            'min_quantity' => $item->min_stock,
        ]);
        $this->assertDatabaseMissing('warehouse_stocks', [
            'warehouse_id' => $warehouseD->id,
            'inventory_item_id' => $item->id,
        ]);
        $this->assertSame(6, (int) WarehouseStock::where('warehouse_id', $warehouseE->id)
            ->where('inventory_item_id', $item->id)
            ->value('quantity'));
        $this->assertSame(20, (int) $item->stocks()->sum('quantity'));

        $this->assertDatabaseHas('stock_movements', [
            'inventory_item_id' => $item->id,
            'warehouse_id' => $warehouseA->id,
            'type' => 'Masuk',
            'quantity' => 3,
            'reference' => 'Penyesuaian manual',
            'note' => 'Penyesuaian stok dari form edit: 5 menjadi 8',
            'user_id' => $user->id,
            'user_name' => $user->name,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'inventory_item_id' => $item->id,
            'warehouse_id' => $warehouseB->id,
            'type' => 'Keluar',
            'quantity' => 5,
            'reference' => 'Penyesuaian manual',
            'note' => 'Penyesuaian stok dari form edit: 7 menjadi 2',
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'inventory_item_id' => $item->id,
            'warehouse_id' => $warehouseC->id,
            'type' => 'Masuk',
            'quantity' => 4,
            'note' => 'Penyesuaian stok dari form edit: 0 menjadi 4',
        ]);
        $this->assertDatabaseCount('stock_movements', 3);
    }

    public function test_inventory_update_does_not_record_movement_when_quantity_is_unchanged(): void
    {
        $this->actingAs(User::factory()->create());
        [$warehouse, $item] = $this->createLocationAndItem();
        $this->createStock($warehouse, $item, 5);

        $this->putJson(route('inventory.update', $item->sku), [
            'sku' => $item->sku,
            'name' => $item->name,
            'stocks' => [
                ['warehouse_id' => $warehouse->id, 'quantity' => 5],
            ],
        ])->assertOk();

        $this->assertDatabaseCount('stock_movements', 0);
        $this->assertSame(5, (int) WarehouseStock::where('warehouse_id', $warehouse->id)
            ->where('inventory_item_id', $item->id)
            ->value('quantity'));
    }

    public function test_inventory_update_records_low_stock_activity_for_stock_and_minimum_changes(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        [$warehouse, $item] = $this->createLocationAndItem();
        $this->createStock($warehouse, $item, 10);

        $this->putJson(route('inventory.update', $item->sku), [
            'sku' => $item->sku,
            'name' => $item->name,
            'min_stock' => 1,
            'stocks' => [
                ['warehouse_id' => $warehouse->id, 'quantity' => 1],
            ],
        ])->assertOk();
        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => InventoryItem::class,
            'subject_id' => $item->id,
            'action' => 'low_stock',
            'user_id' => $user->id,
            'description' => 'Stok Kabel Test menipis: 1 Unit (minimum 1)',
        ]);

        $this->putJson(route('inventory.update', $item->sku), [
            'sku' => $item->sku,
            'name' => $item->name,
            'min_stock' => 2,
        ])->assertOk();
        $this->assertDatabaseCount('activity_logs', 1);

        $safeItem = InventoryItem::create([
            'sku' => 'PRD-SAFE',
            'name' => 'Barang Aman',
            'unit' => 'Unit',
            'min_stock' => 0,
            'status' => 'Aktif',
        ]);
        $this->createStock($warehouse, $safeItem, 1);
        $this->putJson(route('inventory.update', $safeItem->sku), [
            'sku' => $safeItem->sku,
            'name' => $safeItem->name,
            'min_stock' => 2,
        ])->assertOk();

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => InventoryItem::class,
            'subject_id' => $safeItem->id,
            'action' => 'low_stock',
            'user_id' => $user->id,
            'description' => 'Stok Barang Aman menipis: 1 Unit (minimum 2)',
        ]);
        $this->assertDatabaseCount('activity_logs', 2);
    }

    public function test_inventory_update_rejects_invalid_stock_rows_without_changing_stock(): void
    {
        $this->actingAs(User::factory()->create());
        [$warehouse, $item] = $this->createLocationAndItem();
        $deletedWarehouse = $this->createWarehouse('LOC-002', 'Gudang Dihapus');
        $deletedWarehouseId = $deletedWarehouse->id;
        $deletedWarehouse->delete();
        $this->createStock($warehouse, $item, 5);

        $invalidRequests = [
            [
                'stocks' => [
                    ['warehouse_id' => 999999, 'quantity' => 8],
                ],
                'errors' => ['stocks.0.warehouse_id'],
            ],
            [
                'stocks' => [
                    ['warehouse_id' => $deletedWarehouseId, 'quantity' => 8],
                ],
                'errors' => ['stocks.0.warehouse_id'],
            ],
            [
                'stocks' => [
                    ['warehouse_id' => $warehouse->id, 'quantity' => -1],
                ],
                'errors' => ['stocks.0.quantity'],
            ],
            [
                'stocks' => [
                    ['warehouse_id' => $warehouse->id, 'quantity' => 8],
                    ['warehouse_id' => $warehouse->id, 'quantity' => 9],
                ],
                'errors' => ['stocks.1.warehouse_id'],
            ],
        ];

        foreach ($invalidRequests as $invalidRequest) {
            $this->putJson(route('inventory.update', $item->sku), [
                'sku' => $item->sku,
                'name' => 'Nama tidak boleh tersimpan',
                'stocks' => $invalidRequest['stocks'],
            ])->assertUnprocessable()
                ->assertJsonValidationErrors($invalidRequest['errors']);
        }

        $this->assertSame(5, (int) WarehouseStock::where('warehouse_id', $warehouse->id)
            ->where('inventory_item_id', $item->id)
            ->value('quantity'));
        $this->assertSame($item->name, $item->fresh()->name);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_inventory_related_counts_active_assets_and_unique_open_tickets(): void
    {
        $this->actingAs(User::factory()->create());
        [, $item] = $this->createLocationAndItem();

        Asset::create([
            'asset_code' => 'AST-001',
            'name' => 'Aset Aktif',
            'inventory_item_id' => $item->id,
            'status' => 'Aktif',
        ]);
        Asset::create([
            'asset_code' => 'AST-002',
            'name' => 'Aset Maintenance',
            'inventory_item_id' => $item->id,
            'status' => 'Maintenance',
        ]);

        $openTicket = Ticket::create([
            'ticket_number' => 'TK-OPEN',
            'title' => 'Tiket terbuka',
            'status' => 'In Progress',
        ]);
        $closedTicket = Ticket::create([
            'ticket_number' => 'TK-CLOSED',
            'title' => 'Tiket tertutup',
            'status' => 'Closed',
        ]);
        TicketMaterial::create([
            'ticket_id' => $openTicket->id,
            'inventory_item_id' => $item->id,
            'quantity' => 1,
            'unit' => 'Unit',
        ]);
        TicketMaterial::create([
            'ticket_id' => $openTicket->id,
            'inventory_item_id' => $item->id,
            'quantity' => 2,
            'unit' => 'Unit',
        ]);
        TicketMaterial::create([
            'ticket_id' => $closedTicket->id,
            'inventory_item_id' => $item->id,
            'quantity' => 1,
            'unit' => 'Unit',
        ]);

        $this->get(route('inventory.index'))
            ->assertOk()
            ->assertViewHas('products', function (array $products): bool {
                $product = collect($products)->firstWhere('id', 'PRD-001');

                return $product !== null && $product['related'] === [
                    'active_assets' => 1,
                    'open_tickets' => 1,
                ];
            });
    }

    public function test_inventory_status_is_derived_from_stock_and_computed_values_are_rejected(): void
    {
        $this->actingAs(User::factory()->create());
        [$warehouse, $item] = $this->createLocationAndItem();
        $stock = $this->createStock($warehouse, $item, 0);

        $this->get(route('inventory.index'))
            ->assertOk()
            ->assertViewHas('products', function (array $products): bool {
                $product = collect($products)->firstWhere('id', 'PRD-001');

                return $product !== null && $product['status'] === 'Habis';
            });

        $stock->update(['quantity' => 1]);
        $this->get(route('inventory.index'))
            ->assertOk()
            ->assertViewHas('products', function (array $products): bool {
                $product = collect($products)->firstWhere('id', 'PRD-001');

                return $product !== null && $product['status'] === 'Low Stock';
            });

        foreach (['Low Stock', 'Habis'] as $computedStatus) {
            $this->putJson(route('inventory.update', $item->sku), [
                'sku' => $item->sku,
                'name' => $item->name,
                'status' => $computedStatus,
            ])->assertUnprocessable()
                ->assertJsonValidationErrors(['status']);
        }

        $this->assertSame('Aktif', $item->fresh()->status);
    }

    private function createLocationAndItem(): array
    {
        $warehouse = $this->createWarehouse('LOC-001', 'Lokasi Asal');
        $item = InventoryItem::create([
            'sku' => 'PRD-001',
            'name' => 'Kabel Test',
            'unit' => 'Unit',
            'min_stock' => 1,
            'status' => 'Aktif',
        ]);

        return [$warehouse, $item];
    }

    private function createWarehouse(string $code, string $name): Warehouse
    {
        return Warehouse::create([
            'code' => $code,
            'name' => $name,
            'address' => 'Jl. Test',
            'status' => 'Aktif',
        ]);
    }

    private function createStock(Warehouse $warehouse, InventoryItem $item, int $quantity): WarehouseStock
    {
        return WarehouseStock::create([
            'warehouse_id' => $warehouse->id,
            'inventory_item_id' => $item->id,
            'quantity' => $quantity,
            'min_quantity' => 1,
        ]);
    }

    public function test_stok_awal_tanpa_lokasi_ditolak_dan_item_tidak_tersimpan(): void
    {
        $this->actingAs(User::factory()->create());

        $this->postJson(route('inventory.store'), [
            'sku' => 'INV-STOK-01',
            'name' => 'Tanpa Lokasi',
            'category' => 'Perangkat Jaringan',
            'unit' => 'Unit',
            'quantity' => 2,
            'min_stock' => 1,
            'condition' => 'Baru',
            'availability' => 'Tersedia',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['warehouse'])
            ->assertJsonPath('errors.warehouse.0', 'Pilih lokasi penyimpanan untuk stok awal.');

        $this->assertDatabaseMissing('inventory_items', ['sku' => 'INV-STOK-01']);
        $this->assertDatabaseMissing('warehouse_stocks', ['inventory_item_id' => 1]);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_stok_awal_berhasil_membuat_warehouse_stock_dan_satu_movement_masuk(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $warehouse = $this->createWarehouse('WH-STOK', 'Gudang Stok Awal');

        $this->postJson(route('inventory.store'), [
            'sku' => 'INV-STOK-02',
            'name' => 'Dengan Stok Awal',
            'category' => 'Perangkat Jaringan',
            'unit' => 'Unit',
            'quantity' => 5,
            'min_stock' => 2,
            'warehouse' => $warehouse->name,
            'condition' => 'Baru',
            'availability' => 'Tersedia',
        ])->assertOk();

        $item = InventoryItem::where('sku', 'INV-STOK-02')->firstOrFail();
        $this->assertDatabaseHas('warehouse_stocks', [
            'inventory_item_id' => $item->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 5,
            'min_quantity' => 2,
        ]);
        $this->assertDatabaseCount('stock_movements', 1);
        $this->assertDatabaseHas('stock_movements', [
            'inventory_item_id' => $item->id,
            'warehouse_id' => $warehouse->id,
            'type' => 'Masuk',
            'quantity' => 5,
            'reference' => 'Stok awal',
            'user_id' => $user->id,
            'user_name' => $user->name,
        ]);
    }

    public function test_lokasi_nonaktif_ditolak_untuk_stok_awal(): void
    {
        $this->actingAs(User::factory()->create());
        $warehouse = $this->createWarehouse('WH-NONAKTIF', 'Gudang Nonaktif');
        $warehouse->update(['status' => 'Nonaktif']);

        $this->postJson(route('inventory.store'), [
            'sku' => 'INV-STOK-03',
            'name' => 'Lokasi Nonaktif',
            'category' => 'Perangkat Jaringan',
            'unit' => 'Unit',
            'quantity' => 3,
            'min_stock' => 1,
            'warehouse' => $warehouse->name,
            'condition' => 'Baru',
            'availability' => 'Tersedia',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['warehouse']);

        $this->assertDatabaseMissing('inventory_items', ['sku' => 'INV-STOK-03']);
    }

    public function test_sku_lebih_dari_30_karakter_ditolak_422_bukan_500(): void
    {
        $this->actingAs(User::factory()->create());

        $this->postJson(route('inventory.store'), [
            'sku' => str_repeat('A', 31),
            'name' => 'SKU Terlalu Panjang',
            'category' => 'Perangkat Jaringan',
            'unit' => 'Unit',
            'quantity' => 0,
            'condition' => 'Baru',
            'availability' => 'Tersedia',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['sku']);
    }

    public function test_ubah_stok_di_lokasi_nonaktif_ditolak_dan_stok_tidak_berubah(): void
    {
        $this->actingAs(User::factory()->create());
        [$warehouse, $item] = $this->createLocationAndItem();
        $warehouse->update(['status' => 'Nonaktif']);
        $this->createStock($warehouse, $item, 5);

        $this->putJson(route('inventory.update', $item->sku), [
            'sku' => $item->sku,
            'name' => $item->name,
            'stocks' => [
                ['warehouse_id' => $warehouse->id, 'quantity' => 9],
            ],
        ])->assertUnprocessable()
            ->assertJsonValidationErrors([
                'stocks.0.quantity' => 'Lokasi '.$warehouse->name.' berstatus Nonaktif, stok tidak dapat diubah.',
            ]);

        $this->assertSame(5, (int) WarehouseStock::where('warehouse_id', $warehouse->id)
            ->where('inventory_item_id', $item->id)
            ->value('quantity'));
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_kirim_ulang_nilai_stok_sama_di_lokasi_nonaktif_diterima(): void
    {
        $this->actingAs(User::factory()->create());
        [$warehouse, $item] = $this->createLocationAndItem();
        $warehouse->update(['status' => 'Nonaktif']);
        $this->createStock($warehouse, $item, 5);

        $this->putJson(route('inventory.update', $item->sku), [
            'sku' => $item->sku,
            'name' => $item->name,
            'stocks' => [
                ['warehouse_id' => $warehouse->id, 'quantity' => 5],
            ],
        ])->assertOk();

        $this->assertSame(5, (int) WarehouseStock::where('warehouse_id', $warehouse->id)
            ->where('inventory_item_id', $item->id)
            ->value('quantity'));
    }

    public function test_sku_bekas_item_terhapus_mendapat_pesan_khusus(): void
    {
        $this->actingAs(User::factory()->create());
        $deleted = InventoryItem::create([
            'sku' => 'BEKAS-001',
            'name' => 'Sudah Dihapus',
            'unit' => 'Unit',
        ]);
        $deleted->delete();

        $this->postJson(route('inventory.store'), [
            'sku' => 'BEKAS-001',
            'name' => 'Item Baru',
            'category' => 'Perangkat Jaringan',
            'unit' => 'Unit',
            'quantity' => 0,
            'condition' => 'Baru',
            'availability' => 'Tersedia',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['sku'])
            ->assertJsonPath('errors.sku.0', 'SKU ini sudah dipakai oleh peralatan yang telah dihapus. Gunakan SKU lain.');

        $this->assertDatabaseMissing('inventory_items', ['sku' => 'BEKAS-001', 'deleted_at' => null]);
    }

    public function test_hapus_item_dengan_aset_maintenance_ditolak_422(): void
    {
        $this->actingAs(User::factory()->create());
        [, $item] = $this->createLocationAndItem();

        Asset::create([
            'asset_code' => 'AST-MAINT',
            'name' => 'Aset Maintenance',
            'inventory_item_id' => $item->id,
            'status' => 'Maintenance',
        ]);

        $this->deleteJson(route('inventory.destroy', $item->sku))
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Peralatan tidak dapat dihapus karena masih ditautkan ke aset.');

        $this->assertNotSoftDeleted('inventory_items', ['id' => $item->id]);
    }

    public function test_find_by_sku_or_id_sku_numerik_tidak_mengambil_item_ber_id(): void
    {
        $this->actingAs(User::factory()->create());
        $skuItem = InventoryItem::create([
            'sku' => '5',
            'name' => 'Item dengan SKU 5',
            'unit' => 'Unit',
        ]);
        $idItem = InventoryItem::create([
            'sku' => 'PRD-005',
            'name' => 'Item dengan ID 5',
            'unit' => 'Unit',
        ]);
        $idItem->forceFill(['id' => 5])->save();

        $found = $this->invokeFindBySkuOrId('5');

        $this->assertSame($skuItem->id, $found->id);
        $this->assertNotSame($idItem->id, $found->id);
    }

    public function test_ubah_min_stock_memperbarui_min_quantity_semua_warehouse_stock(): void
    {
        $this->actingAs(User::factory()->create());
        [$warehouseA, $item] = $this->createLocationAndItem();
        $warehouseB = $this->createWarehouse('LOC-002', 'Lokasi B');
        $this->createStock($warehouseA, $item, 5);
        $this->createStock($warehouseB, $item, 7);

        $this->putJson(route('inventory.update', $item->sku), [
            'sku' => $item->sku,
            'name' => $item->name,
            'min_stock' => 10,
            'stocks' => [
                ['warehouse_id' => $warehouseA->id, 'quantity' => 5],
                ['warehouse_id' => $warehouseB->id, 'quantity' => 7],
            ],
        ])->assertOk();

        $this->assertDatabaseHas('warehouse_stocks', [
            'inventory_item_id' => $item->id,
            'warehouse_id' => $warehouseA->id,
            'min_quantity' => 10,
        ]);
        $this->assertDatabaseHas('warehouse_stocks', [
            'inventory_item_id' => $item->id,
            'warehouse_id' => $warehouseB->id,
            'min_quantity' => 10,
        ]);
    }

    private function invokeFindBySkuOrId($id)
    {
        $controller = app(\App\Http\Controllers\InventoryController::class);
        $reflection = new \ReflectionMethod($controller, 'findBySkuOrId');
        $reflection->setAccessible(true);

        return $reflection->invoke($controller, $id);
    }
}
