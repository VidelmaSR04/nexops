<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use App\Models\Customer;
use Database\Seeders\OpdSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_view_update_change_status_and_delete_ticket(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('tickets.store'), [
            'customer_name' => 'Customer Example',
            'location' => 'Situbondo',
            'reporter_name' => 'Reporter Example',
            'reporter_contact' => '08123456789',
            'priority' => 'High',
            'title' => 'Connection issue',
            'description' => 'Customer reports intermittent connection.',
            'assigned_tech_name' => '-',
            'asset_name' => '',
        ])->assertRedirect(route('tickets.index'));

        $ticket = Ticket::firstOrFail();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'title' => 'Connection issue',
            'reporter_name' => 'Reporter Example',
            'reporter_contact' => '08123456789',
        ]);

        $this->get(route('tickets.index'))
            ->assertOk()
            ->assertSee($ticket->ticket_number)
            ->assertDontSee('TK-1025');

        $this->get(route('tickets.create'))->assertOk();
        $this->get(route('tickets.show', $ticket->id))->assertOk();
        $this->get(route('tickets.edit', $ticket->id))->assertOk();

        $this->put(route('tickets.update', $ticket->id), [
            'customer_name' => 'Customer Example',
            'location' => 'Besuki',
            'reporter_name' => 'Updated Reporter',
            'reporter_contact' => '08111111111',
            'priority' => 'Critical',
            'title' => 'Updated connection issue',
            'description' => 'Updated description.',
            'assigned_tech_name' => '-',
            'asset_name' => '',
        ])->assertRedirect(route('tickets.index'));

        $ticket->refresh();
        $this->assertSame('Updated connection issue', $ticket->title);
        $this->assertSame('Besuki', $ticket->location);
        $this->assertSame('Updated Reporter', $ticket->reporter_name);
        $this->assertSame('08111111111', $ticket->reporter_contact);

        $this->patch(route('tickets.update-status', $ticket->id), [
            'status' => 'Resolved',
        ])->assertRedirect(route('tickets.index'));

        $this->assertSame('Resolved', $ticket->fresh()->status);

        $this->delete(route('tickets.destroy', $ticket->id))
            ->assertRedirect(route('tickets.index'));

        $this->assertSoftDeleted('tickets', ['id' => $ticket->id]);
    }

    public function test_seeded_opd_can_be_selected_and_saved_on_a_ticket(): void
    {
        $this->actingAs(User::factory()->create());
        $this->seed(OpdSeeder::class);

        $opd = Customer::where('type', 'opd')->firstOrFail();
        $opdCount = Customer::where('type', 'opd')->count();
        $this->seed(OpdSeeder::class);
        $this->assertSame($opdCount, Customer::where('type', 'opd')->count());

        $this->get(route('tickets.index'))
            ->assertOk()
            ->assertSee($opd->name);
        $this->get(route('tickets.create'))
            ->assertOk()
            ->assertSee('name="customer_id"', false)
            ->assertSee($opd->name);

        $this->post(route('tickets.store'), [
            'customer_id' => $opd->id,
            'location' => 'Situbondo',
            'reporter_name' => 'Pelapor OPD',
            'reporter_contact' => '081234567890',
            'priority' => 'Normal',
            'title' => 'Gangguan jaringan OPD',
            'assigned_tech_name' => '-',
        ])->assertRedirect(route('tickets.index'));

        $this->assertDatabaseHas('tickets', [
            'customer_id' => $opd->id,
            'customer_name' => $opd->name,
            'reporter_name' => 'Pelapor OPD',
            'reporter_contact' => '081234567890',
        ]);

        $ticket = Ticket::latest('id')->firstOrFail();
        $newOpd = Customer::where('type', 'opd')->where('id', '!=', $opd->id)->firstOrFail();

        $this->get(route('tickets.edit', $ticket->id))
            ->assertOk()
            ->assertSee($opd->name);

        $this->put(route('tickets.update', $ticket->id), [
            'customer_id' => $newOpd->id,
            'location' => 'Besuki',
            'reporter_name' => 'Pelapor Baru',
            'reporter_contact' => '081111111111',
            'priority' => 'High',
            'title' => 'Perubahan OPD tiket',
            'assigned_tech_name' => '-',
        ])->assertRedirect(route('tickets.index'));

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'customer_id' => $newOpd->id,
            'customer_name' => $newOpd->name,
            'reporter_name' => 'Pelapor Baru',
            'reporter_contact' => '081111111111',
        ]);
    }

    public function test_customer_page_can_save_opd_reporter_details(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('customers.store'), [
            'name' => 'Dinas Uji OPD',
            'type' => 'opd',
            'reporter_name' => 'Nama Pelapor',
            'reporter_contact' => '081234567890',
        ])->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', [
            'name' => 'Dinas Uji OPD',
            'type' => 'opd',
            'reporter_name' => 'Nama Pelapor',
            'reporter_contact' => '081234567890',
        ]);

        $customer = Customer::where('name', 'Dinas Uji OPD')->firstOrFail();
        $this->put(route('customers.update', $customer->id), [
            'name' => 'Dinas Uji OPD',
            'type' => 'opd',
            'reporter_name' => 'Pelapor Diperbarui',
            'reporter_contact' => '081111111111',
            'status' => 'Aktif',
        ])->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'reporter_name' => 'Pelapor Diperbarui',
            'reporter_contact' => '081111111111',
        ]);

        $this->get(route('customers.index'))
            ->assertOk()
            ->assertSee('Dinas Uji OPD')
            ->assertSee('Pelapor Diperbarui');
    }
}
