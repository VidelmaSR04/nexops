<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Technician;
use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechnicianCustomerAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_technician_crud_and_validation_use_persisted_data(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->postJson('/api/technicians', $this->technicianData());
        $response->assertCreated()
            ->assertJsonPath('data.name', 'Technician One')
            ->assertJsonPath('data.latitude', -7.7062)
            ->assertJsonPath('data.longitude', 113.9294)
            ->assertJsonPath('data.location_is_simulated', false)
            ->assertJsonPath('data.tickets', 0);

        $technicianId = $response->json('data.id');

        $this->getJson("/api/technicians/{$technicianId}")
            ->assertOk()
            ->assertJsonPath('data.nik', 'TECH-001');

        $this->putJson("/api/technicians/{$technicianId}", [
            ...$this->technicianData(),
            'name' => 'Technician Updated',
        ])->assertOk()->assertJsonPath('data.name', 'Technician Updated');

        $this->patchJson("/api/technicians/{$technicianId}/status", [
            'status' => 'Offline',
        ])->assertOk()->assertJsonPath('data.status', 'Offline');

        $this->postJson('/api/technicians', $this->technicianData())
            ->assertUnprocessable()
            ->assertJsonValidationErrors('nik');

        $this->assertDatabaseHas('technicians', [
            'id' => $technicianId,
            'name' => 'Technician Updated',
            'status' => 'Offline',
            'latitude' => -7.7062,
            'longitude' => 113.9294,
        ]);

        $this->deleteJson("/api/technicians/{$technicianId}")
            ->assertOk()
            ->assertJsonPath('data.status', 'Offline');
        $this->assertDatabaseHas('technicians', ['id' => $technicianId, 'deleted_at' => null]);
    }

    public function test_technician_coordinates_must_be_a_valid_pair(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->postJson('/api/technicians', [
            ...$this->technicianData(),
            'nik' => 'TECH-002',
            'latitude' => null,
            'longitude' => null,
        ])->assertCreated()
            ->assertJsonPath('data.location_is_simulated', true);
        $this->assertGreaterThan(-7.7146, $response->json('data.latitude'));
        $this->assertLessThan(-7.6986, $response->json('data.latitude'));
        $this->assertGreaterThan(113.9198, $response->json('data.longitude'));
        $this->assertLessThan(113.9398, $response->json('data.longitude'));

        $this->postJson('/api/technicians', [
            ...$this->technicianData(),
            'nik' => 'TECH-003',
            'longitude' => null,
        ])->assertUnprocessable()->assertJsonValidationErrors('longitude');

        $this->postJson('/api/technicians', [
            ...$this->technicianData(),
            'nik' => 'TECH-004',
            'latitude' => 91,
        ])->assertUnprocessable()->assertJsonValidationErrors('latitude');
    }

    public function test_status_can_be_changed_from_detail_or_edit_while_assignments_remain_active(): void
    {
        $this->actingAs(User::factory()->create());
        $customer = Customer::create([
            ...$this->customerData(),
            'customer_code' => 'CST0001',
        ]);
        $technician = Technician::create($this->technicianData());
        $ticket = $this->ticketFor($customer);

        $this->postJson("/api/tickets/{$ticket->ticket_number}/assignments", [
            'technician_id' => $technician->id,
        ])->assertCreated();

        $this->patchJson("/api/technicians/{$technician->id}/status", [
            'status' => 'Offline',
        ])->assertOk()
            ->assertJsonPath('data.status', 'Offline')
            ->assertJsonPath('data.tickets', 1);

        $this->putJson("/api/technicians/{$technician->id}", [
            ...$this->technicianData(),
            'status' => 'Cuti',
        ])->assertOk()
            ->assertJsonPath('data.status', 'Cuti')
            ->assertJsonPath('data.tickets', 1);

        $this->assertDatabaseHas('ticket_assignments', [
            'ticket_id' => $ticket->id,
            'technician_id' => $technician->id,
            'is_active' => true,
        ]);
        $this->assertSame('Cuti', $technician->fresh()->status);
    }

    public function test_customer_crud_uses_reporter_contact_and_rejects_duplicate_name(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->postJson('/api/customers', $this->customerData());
        $response->assertCreated()
            ->assertJsonPath('data.custNo', 'CST0001')
            ->assertJsonPath('data.tickets_count', 0);

        $customerId = $response->json('data.id');

        $this->getJson("/api/customers/{$customerId}")
            ->assertOk()
            ->assertJsonPath('data.customer.name', 'Customer One')
            ->assertJsonPath('data.ticket_history', []);

        $this->putJson("/api/customers/{$customerId}", [
            ...$this->customerData(),
            'name' => 'Customer Updated',
        ])->assertOk()->assertJsonPath('data.name', 'Customer Updated');

        $this->postJson('/api/customers', [
            ...$this->customerData(),
            'name' => 'Customer Updated',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');

        $this->patchJson("/api/customers/{$customerId}/status", [
            'status' => 'Nonaktif',
        ])->assertOk()->assertJsonPath('data.status', 'Nonaktif');

        $this->assertDatabaseHas('customers', [
            'id' => $customerId,
            'name' => 'Customer Updated',
            'status' => 'Nonaktif',
            'deleted_at' => null,
        ]);
    }

    public function test_assignment_creation_reassignment_and_cancellation_preserve_history(): void
    {
        $this->actingAs(User::factory()->create());
        $customer = Customer::create([
            ...$this->customerData(),
            'customer_code' => 'CST0001',
        ]);
        $firstTechnician = Technician::create($this->technicianData());
        $secondTechnician = Technician::create([
            ...$this->technicianData(),
            'nik' => 'TECH-002',
            'name' => 'Technician Two',
        ]);
        $ticket = $this->ticketFor($customer);

        $this->postJson("/api/tickets/{$ticket->ticket_number}/assignments", [
            'technician_id' => $firstTechnician->id,
        ])->assertCreated()
            ->assertJsonPath('data.technician.id', $firstTechnician->id)
            ->assertJsonPath('data.ticket.customer.id', $customer->id);

        $this->assertSame('On Task', $firstTechnician->fresh()->status);
        $this->assertSame('Assigned', $ticket->fresh()->status);
        $this->assertSame(1, $firstTechnician->activeAssignments()->count());

        $this->postJson("/api/tickets/{$ticket->ticket_number}/assignments", [
            'technician_id' => $firstTechnician->id,
        ])->assertOk();
        $this->assertSame(1, $ticket->assignments()->count());

        $this->postJson("/api/tickets/{$ticket->ticket_number}/assignments", [
            'technician_id' => $secondTechnician->id,
        ])->assertCreated();

        $this->assertSame(2, $ticket->assignments()->count());
        $this->assertSame(1, $ticket->assignments()->where('is_active', true)->count());
        $this->assertSame('Available', $firstTechnician->fresh()->status);
        $this->assertSame('On Task', $secondTechnician->fresh()->status);
        $this->assertSame('Technician Two', $ticket->fresh()->assigned_tech_name);

        $activeAssignment = $ticket->assignments()->where('is_active', true)->firstOrFail();
        $this->deleteJson("/api/tickets/{$ticket->ticket_number}/assignments/{$activeAssignment->id}")
            ->assertOk();

        $this->assertSame(2, $ticket->assignments()->count());
        $this->assertSame(0, $ticket->assignments()->where('is_active', true)->count());
        $this->assertSame('Open', $ticket->fresh()->status);
        $this->assertNull($ticket->fresh()->assigned_tech_name);
        $this->assertSame('Available', $secondTechnician->fresh()->status);
        $this->assertSame(2, TicketAssignment::where('ticket_id', $ticket->id)
            ->whereNotNull('unassigned_at')
            ->count());
    }

    public function test_assignment_rejects_unavailable_technicians_and_closed_tickets(): void
    {
        $this->actingAs(User::factory()->create());
        $customer = Customer::create([
            ...$this->customerData(),
            'customer_code' => 'CST0001',
        ]);
        $technician = Technician::create([
            ...$this->technicianData(),
            'status' => 'Offline',
        ]);
        $ticket = $this->ticketFor($customer);

        $this->postJson("/api/tickets/{$ticket->ticket_number}/assignments", [
            'technician_id' => $technician->id,
        ])->assertUnprocessable()->assertJsonValidationErrors('technician_id');

        $ticket->update(['status' => 'Closed']);
        $technician->update(['status' => 'Available']);

        $this->postJson("/api/tickets/{$ticket->ticket_number}/assignments", [
            'technician_id' => $technician->id,
        ])->assertUnprocessable()->assertJsonValidationErrors('ticket_id');

        $this->assertSame(0, $ticket->assignments()->count());
    }

    public function test_technician_list_uses_ticket_customer_for_assignment_opd(): void
    {
        $this->actingAs(User::factory()->create());
        $customer = Customer::create([
            ...$this->customerData(),
            'customer_code' => 'CST0001',
        ]);
        $technician = Technician::create($this->technicianData());
        $assignedTicket = $this->ticketFor($customer);

        $this->postJson("/api/tickets/{$assignedTicket->ticket_number}/assignments", [
            'technician_id' => $technician->id,
        ])->assertCreated();

        $this->getJson('/api/technicians')
            ->assertOk()
            ->assertJsonPath('data.technicians.0.active.0.opd', $customer->name)
            ->assertJsonPath('data.assignableTickets.0.id', $assignedTicket->ticket_number);
    }

    private function technicianData(): array
    {
        return [
            'nik' => 'TECH-001',
            'name' => 'Technician One',
            'position' => 'Teknisi',
            'area' => 'Situbondo',
            'latitude' => -7.7062,
            'longitude' => 113.9294,
            'phone' => '081234567890',
            'email' => 'technician@example.test',
            'status' => 'Available',
            'joined_at' => '2025-01-01',
            'exp_years' => 1,
            'rating' => 4.5,
        ];
    }

    private function customerData(): array
    {
        return [
            'name' => 'Customer One',
            'type' => 'individual',
            'reporter_contact' => '081234567891',
            'email' => 'customer@example.test',
            'address' => 'Jl. Contoh 1',
            'area' => 'Situbondo',
            'package_name' => '50 Mbps',
            'status' => 'Aktif',
        ];
    }

    private function ticketFor(Customer $customer): Ticket
    {
        return Ticket::create([
            'ticket_number' => 'TK-TEST-001',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'title' => 'Internet down',
            'description' => 'Customer reports lost connection.',
            'location' => $customer->area,
            'priority' => 'Normal',
            'status' => 'Open',
        ]);
    }
}
