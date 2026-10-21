<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryConditionAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_condition_and_availability_can_be_saved_and_displayed_separately(): void
    {
        $this->actingAs(User::factory()->create());

        $this->postJson(route('inventory.store'), [
            'sku' => 'INV-001',
            'name' => 'Router Test',
            'category' => 'Perangkat Jaringan',
            'unit' => 'Unit',
            'quantity' => 2,
            'min_stock' => 1,
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
}
