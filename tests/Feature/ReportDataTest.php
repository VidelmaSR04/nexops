<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_and_live_data_endpoint_use_database_tickets_and_customers(): void
    {
        $this->actingAs(User::factory()->create());
        $customer = Customer::create([
            'customer_code' => 'OPD001',
            'name' => 'Dinas Contoh',
            'type' => 'opd',
            'status' => 'Aktif',
        ]);
        $ticket = Ticket::create([
            'ticket_number' => 'TK-9001',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'title' => 'Gangguan jaringan',
            'priority' => 'High',
            'status' => 'Open',
            'sla_due_at' => now()->addHours(4),
        ]);

        $this->get(route('reports.index'))
            ->assertOk()
            ->assertSee('Dinas Contoh')
            ->assertSee('TK-9001');

        $this->getJson(route('reports.data'))
            ->assertOk()
            ->assertJsonPath('data.0.id', 'TK-9001')
            ->assertJsonPath('data.0.opd', 'Dinas Contoh')
            ->assertJsonPath('data.0.category', 'Prioritas High');

        $this->assertDatabaseHas('tickets', ['id' => $ticket->id]);
    }
}
