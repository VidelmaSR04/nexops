<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
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
            'priority' => 'Critical',
            'title' => 'Updated connection issue',
            'description' => 'Updated description.',
            'assigned_tech_name' => '-',
            'asset_name' => '',
        ])->assertRedirect(route('tickets.index'));

        $ticket->refresh();
        $this->assertSame('Updated connection issue', $ticket->title);
        $this->assertSame('Besuki', $ticket->location);

        $this->patch(route('tickets.update-status', $ticket->id), [
            'status' => 'Resolved',
        ])->assertRedirect(route('tickets.index'));

        $this->assertSame('Resolved', $ticket->fresh()->status);

        $this->delete(route('tickets.destroy', $ticket->id))
            ->assertRedirect(route('tickets.index'));

        $this->assertSoftDeleted('tickets', ['id' => $ticket->id]);
    }
}
