<?php

namespace Tests\Feature;

use App\Models\Technician;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechnicianProfileDefaultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_technician_gets_generated_code_fixed_position_and_fixed_area(): void
    {
        $this->actingAs(User::factory()->create());

        Technician::create([
            'nik' => 'OLD-001',
            'name' => 'Teknisi Lama',
            'position' => 'Teknisi',
            'area' => 'Situbondo',
            'status' => 'Available',
        ]);

        $this->postJson(route('technicians.store'), [
            'name' => 'Teknisi Baru',
            'phone' => '081234567890',
            'email' => 'teknisi@example.test',
            'status' => 'Available',
            'joined_at' => '2026-10-29',
            'exp_years' => 0,
            'nik' => 'MANUAL',
            'position' => 'Admin',
            'area' => 'Situbondo',
            'latitude' => -7.7,
            'longitude' => 113.9,
        ])
            ->assertCreated()
            ->assertJsonPath('data.nik', 'TK002')
            ->assertJsonPath('data.role', 'Teknisi')
            ->assertJsonPath('data.area', 'Dinas Komunikasi dan Informatika');

        $technician = Technician::where('name', 'Teknisi Baru')->firstOrFail();
        $this->assertNull($technician->latitude);
        $this->assertNull($technician->longitude);
    }

    public function test_technician_update_cannot_change_generated_code_position_or_area(): void
    {
        $this->actingAs(User::factory()->create());
        $technician = Technician::create([
            'nik' => 'TK014',
            'name' => 'Teknisi Lama',
            'position' => 'Teknisi',
            'area' => 'Situbondo',
            'status' => 'Available',
        ]);

        $this->putJson(route('technicians.update', $technician), [
            'name' => 'Teknisi Diperbarui',
            'nik' => 'TK999',
            'position' => 'Admin',
            'area' => 'Besuki',
            'status' => 'Available',
        ])
            ->assertOk()
            ->assertJsonPath('data.nik', 'TK014')
            ->assertJsonPath('data.role', 'Teknisi')
            ->assertJsonPath('data.area', 'Dinas Komunikasi dan Informatika');

        $this->assertSame('TK014', $technician->fresh()->nik);
        $this->assertSame('Teknisi', $technician->fresh()->position);
        $this->assertSame('Dinas Komunikasi dan Informatika', $technician->fresh()->area);
    }
}
