<?php

namespace Tests\Feature;

use App\Models\Technician;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechnicianBulkDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_selected_technicians_are_soft_deleted(): void
    {
        $this->actingAs(User::factory()->create());
        $first = $this->createTechnician('TK001', 'Teknisi Pertama');
        $second = $this->createTechnician('TK002', 'Teknisi Kedua');
        $unselected = $this->createTechnician('TK003', 'Teknisi Ketiga');

        $this->deleteJson(route('technicians.bulk-destroy'), [
            'ids' => [$first->id, $second->id],
        ])
            ->assertOk()
            ->assertJsonPath('deleted_count', 2);

        $this->assertSoftDeleted('technicians', ['id' => $first->id]);
        $this->assertSoftDeleted('technicians', ['id' => $second->id]);
        $this->assertDatabaseHas('technicians', ['id' => $unselected->id, 'deleted_at' => null]);
    }

    public function test_bulk_delete_rejects_empty_selection(): void
    {
        $this->actingAs(User::factory()->create());

        $this->deleteJson(route('technicians.bulk-destroy'), ['ids' => []])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['ids']);
    }

    private function createTechnician(string $code, string $name): Technician
    {
        return Technician::create([
            'nik' => $code,
            'name' => $name,
            'position' => 'Teknisi',
            'area' => 'Dinas Komunikasi dan Informatika',
            'status' => 'Available',
        ]);
    }
}
