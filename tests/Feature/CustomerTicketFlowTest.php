<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Technician;
use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTicketFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_link_customer_accounts_to_an_opd(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $customer = $this->createOpd('OPD1001');
        $user = User::factory()->create(['role' => 'customer']);

        $this->postJson(route('customers.users.store', $customer), [
            'user_id' => $user->id,
        ])->assertOk()
            ->assertJsonPath('data.id', $user->id);

        $this->assertSame($customer->id, $user->fresh()->customer_id);
        $this->assertCount(1, $customer->users);
        $this->get(route('customers.index'))
            ->assertOk()
            ->assertSee('Akun Pelapor Terhubung');

        $otherOpd = $this->createOpd('OPD1008');
        $this->postJson(route('customers.users.store', $otherOpd), [
            'user_id' => $user->id,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('user_id');
        $this->assertSame($customer->id, $user->fresh()->customer_id);
    }

    public function test_customer_ticket_submission_uses_the_logged_in_users_opd_and_assigns_least_loaded_matching_technician(): void
    {
        $customer = $this->createOpd('OPD1002', ['area' => 'Kec. Situbondo']);
        $user = $this->createCustomerUser($customer);
        $moreLoaded = $this->createTechnician('Besuki');
        $leastLoaded = $this->createTechnician('Situbondo');
        $otherArea = $this->createTechnician('Besuki');

        $this->addActiveAssignment($moreLoaded, $customer, 'TK-LOAD-1');
        $this->addActiveAssignment($moreLoaded, $customer, 'TK-LOAD-2');
        $this->addActiveAssignment($leastLoaded, $customer, 'TK-LOAD-3');

        $this->actingAs($user)->postJson(route('customer.tickets.store'), [
            'customer_id' => 99999,
            'reporter_name' => 'Pegawai OPD',
            'reporter_contact' => '08123456789',
            'issue_category' => 'Koneksi Putus',
            'title' => 'Jaringan kantor terputus',
            'description' => 'Koneksi utama tidak dapat digunakan.',
            'location' => 'Ruang server',
            'priority' => 'High',
        ])->assertCreated()
            ->assertJsonPath('data.customer_id', $customer->id)
            ->assertJsonPath('data.created_by', $user->id)
            ->assertJsonPath('data.issue_category', 'Koneksi Putus')
            ->assertJsonPath('data.status', 'Assigned');

        $ticket = Ticket::where('title', 'Jaringan kantor terputus')->firstOrFail();
        $this->assertDatabaseHas('ticket_assignments', [
            'ticket_id' => $ticket->id,
            'technician_id' => $leastLoaded->id,
            'is_active' => true,
        ]);
        $this->assertSame('Available', $otherArea->fresh()->status);
    }

    public function test_customer_ticket_remains_open_when_no_available_technician_matches_area(): void
    {
        $customer = $this->createOpd('OPD1003', ['area' => 'Besuki']);
        $user = $this->createCustomerUser($customer);
        $this->createTechnician('Situbondo');

        $this->actingAs($user)->postJson(route('customer.tickets.store'), $this->ticketData())
            ->assertCreated()
            ->assertJsonPath('data.status', 'Open')
            ->assertJsonPath('data.assigned_tech_name', null);

        $ticket = Ticket::firstOrFail();
        $this->assertDatabaseMissing('ticket_assignments', [
            'ticket_id' => $ticket->id,
            'is_active' => true,
        ]);
    }

    public function test_unlinked_or_inactive_opd_accounts_cannot_create_tickets(): void
    {
        $unlinkedUser = User::factory()->create(['role' => 'customer']);
        $this->actingAs($unlinkedUser)->postJson(route('customer.tickets.store'), $this->ticketData())
            ->assertUnprocessable()
            ->assertJsonValidationErrors('customer');

        $inactiveCustomer = $this->createOpd('OPD1004', ['status' => 'Nonaktif']);
        $inactiveUser = $this->createCustomerUser($inactiveCustomer);
        $this->actingAs($inactiveUser)->postJson(route('customer.tickets.store'), $this->ticketData())
            ->assertUnprocessable()
            ->assertJsonValidationErrors('customer');

        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_customer_can_only_see_own_tickets_and_cannot_access_staff_ticket_routes(): void
    {
        $customer = $this->createOpd('OPD1005');
        $anotherCustomer = $this->createOpd('OPD1006');
        $user = $this->createCustomerUser($customer);
        $ownTicket = $this->createTicket($customer, 'TK-OWN-1');
        $this->createTicket($anotherCustomer, 'TK-OTHER-1');

        $this->actingAs($user)
            ->getJson('/api/my/tickets')
            ->assertOk()
            ->assertJsonCount(1, 'data.tickets')
            ->assertJsonPath('data.tickets.0.id', $ownTicket->id);

        $this->get(route('customer.tickets.index'))
            ->assertOk()
            ->assertSee('Tiket Saya')
            ->assertSee($ownTicket->ticket_number);
        $this->get(route('tickets.index'))->assertForbidden();
        $this->getJson('/api/tickets')->assertForbidden();
    }

    public function test_finishing_a_ticket_releases_its_technician_for_future_auto_routing(): void
    {
        $customer = $this->createOpd('OPD1007');
        $technician = $this->createTechnician('Situbondo');
        $ticket = $this->createTicket($customer, 'TK-DONE-1', ['status' => 'Assigned']);
        TicketAssignment::create([
            'ticket_id' => $ticket->id,
            'technician_id' => $technician->id,
            'is_active' => true,
        ]);
        $technician->update(['status' => 'On Task']);

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->patchJson(route('tickets.update-status', $ticket->id), ['status' => 'Resolved'])
            ->assertOk();

        $this->assertSame('Available', $technician->fresh()->status);
    }

    public function test_admin_ticket_list_uses_the_linked_opd_name_over_a_stale_text_snapshot(): void
    {
        $customer = $this->createOpd('OPD1009', ['name' => 'Nama OPD Terkini']);
        $ticket = $this->createTicket($customer, 'TK-OPD-NAME-1', [
            'customer_name' => 'Nama OPD Lama',
        ]);

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('tickets.index'))
            ->assertOk()
            ->assertViewHas('rawTickets', function (array $tickets) use ($ticket): bool {
                return collect($tickets)->firstWhere('db_id', $ticket->id)['customer'] === 'Nama OPD Terkini';
            });
    }

    public function test_ticket_created_through_staff_api_is_listed_in_the_admin_web_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = $this->createOpd('OPD1011');

        $response = $this->actingAs($admin)->postJson('/api/tickets', [
            'customer_id' => $customer->id,
            'location' => 'Ruang server',
            'reporter_name' => 'Pegawai OPD',
            'reporter_contact' => '08123456789',
            'priority' => 'High',
            'title' => 'Tiket dari REST API',
            'description' => 'Gangguan dibuat melalui endpoint staff.',
            'assigned_tech_name' => '-',
        ])->assertCreated()
            ->assertJsonPath('data.customer_id', $customer->id)
            ->assertJsonPath('data.created_by', $admin->id);

        $ticket = Ticket::findOrFail($response->json('data.id'));

        $this->get(route('tickets.index'))
            ->assertOk()
            ->assertSee($ticket->ticket_number)
            ->assertSee('Tiket dari REST API')
            ->assertSee($customer->name);
    }

    public function test_staff_ticket_creation_skips_ticket_numbers_already_used_by_seeded_tickets(): void
    {
        $customer = $this->createOpd('OPD1010');
        $this->createTicket($customer, 'TK-1018');
        $this->createTicket($customer, 'TK-1017');

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->post(route('tickets.store'), [
                'customer_id' => $customer->id,
                'location' => 'Ruang kerja',
                'reporter_name' => 'Pegawai OPD',
                'reporter_contact' => '08123456789',
                'priority' => 'Normal',
                'title' => 'Tiket baru setelah data contoh',
                'description' => 'Validasi nomor tiket unik.',
                'assigned_tech_name' => '-',
            ])
            ->assertRedirect(route('tickets.index'));

        $this->assertDatabaseHas('tickets', [
            'ticket_number' => 'TK-1019',
            'title' => 'Tiket baru setelah data contoh',
        ]);
    }

    private function createOpd(string $code, array $attributes = []): Customer
    {
        return Customer::create([
            'customer_code' => $code,
            'name' => 'OPD '.$code,
            'type' => 'opd',
            'area' => 'Situbondo',
            'status' => 'Aktif',
            ...$attributes,
        ]);
    }

    private function createCustomerUser(Customer $customer): User
    {
        $user = User::factory()->create(['role' => 'customer']);
        $user->customer()->associate($customer)->save();

        return $user;
    }

    private function createTechnician(string $area): Technician
    {
        static $counter = 0;
        $counter++;

        return Technician::create([
            'nik' => 'TECH-'.str_pad((string) $counter, 3, '0', STR_PAD_LEFT),
            'name' => 'Teknisi '.$counter,
            'area' => $area,
            'status' => 'Available',
        ]);
    }

    private function createTicket(Customer $customer, string $number, array $attributes = []): Ticket
    {
        return Ticket::create([
            'ticket_number' => $number,
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'title' => 'Ticket '.$number,
            'description' => 'Test issue',
            'location' => 'Ruang server',
            'priority' => 'Normal',
            'status' => 'Open',
            ...$attributes,
        ]);
    }

    private function addActiveAssignment(Technician $technician, Customer $customer, string $ticketNumber): void
    {
        $ticket = $this->createTicket($customer, $ticketNumber);
        TicketAssignment::create([
            'ticket_id' => $ticket->id,
            'technician_id' => $technician->id,
            'is_active' => true,
        ]);
    }

    private function ticketData(): array
    {
        return [
            'reporter_name' => 'Pegawai OPD',
            'reporter_contact' => '08123456789',
            'issue_category' => 'Internet Lambat',
            'title' => 'Internet lambat',
            'description' => 'Kecepatan internet menurun.',
            'location' => 'Ruang kerja',
            'priority' => 'Normal',
        ];
    }
}
