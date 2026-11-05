<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Technician;
use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\DemoTicketSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoTicketSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_tickets_use_existing_master_data_and_can_be_seeded_repeatedly(): void
    {
        $customers = [];
        foreach (range(1, 4) as $index) {
            $customers[] = Customer::create([
                'customer_code' => 'CST-DEMO-'.str_pad((string) $index, 4, '0', STR_PAD_LEFT),
                'name' => 'OPD Demo '.$index,
                'type' => 'opd',
                'status' => 'Aktif',
            ]);
        }

        $customerUser = User::factory()->create([
            'role' => 'customer',
            'customer_id' => $customers[2]->id,
        ]);
        $admin = User::factory()->create(['role' => 'admin']);

        foreach (range(1, 2) as $index) {
            Technician::create([
                'nik' => 'TECH-DEMO-'.$index,
                'name' => 'Teknisi Demo '.$index,
                'status' => 'Available',
            ]);
        }

        $this->seed(DemoTicketSeeder::class);
        $this->seed(DemoTicketSeeder::class);

        $tickets = Ticket::query()
            ->where('ticket_number', 'like', 'TK-DEMO-%')
            ->with(['customer', 'activeTechnicians'])
            ->orderBy('ticket_number')
            ->get();

        $this->assertCount(4, $tickets);
        $this->assertSame(4, Customer::count());
        $this->assertSame(['Assigned', 'In Progress', 'Open', 'Resolved'], $tickets->pluck('status')->sort()->values()->all());
        $this->assertSame(
            [$customers[2]->id],
            $tickets->pluck('customer_id')->unique()->values()->all()
        );

        foreach ($tickets as $ticket) {
            $this->assertNotNull($ticket->customer);
            $this->assertSame('opd', $ticket->customer->type);
            $this->assertSame($ticket->customer->name, $ticket->customer_name);
        }

        $this->assertSame(1, $tickets[1]->activeTechnicians->count());
        $this->assertSame(1, $tickets[2]->activeTechnicians->count());

        $customerResponse = $this->actingAs($customerUser)
            ->get(route('customer.tickets.index'))
            ->assertOk();
        foreach ($tickets as $ticket) {
            $customerResponse->assertSee($ticket->ticket_number);
        }

        $technicianResponse = $this->actingAs($admin)
            ->getJson('/api/technicians')
            ->assertOk();
        $assignedTicketNumbers = collect($technicianResponse->json('data.technicians'))
            ->flatMap(fn (array $technician): array => $technician['active'])
            ->pluck('id')
            ->all();

        $this->assertContains('TK-DEMO-0002', $assignedTicketNumbers);
        $this->assertContains('TK-DEMO-0003', $assignedTicketNumbers);
    }
}
