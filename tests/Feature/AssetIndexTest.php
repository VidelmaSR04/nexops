<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_assets_page_displays_broken_asset_count(): void
    {
        $this->actingAs(User::factory()->create());

        Asset::create([
            'asset_code' => 'AST-001',
            'name' => 'Damaged Router',
            'status' => 'Rusak',
        ]);

        $this->get(route('assets.index'))
            ->assertOk()
            ->assertSee('Damaged Router')
            ->assertSee('Rusak');
    }
}
