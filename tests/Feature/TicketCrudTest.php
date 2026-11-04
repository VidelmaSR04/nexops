<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\Technician;
use App\Models\User;
use App\Models\Customer;
use Database\Seeders\OpdSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_creation_rejects_customer_name_without_an_opd_relation(): void
    {
        $this->actingAs(User::factory()->create());

        $this->postJson(route('tickets.store'), [
            'customer_name' => 'Nama bebas tanpa OPD',
            'location' => 'Situbondo',
            'reporter_name' => 'Pelapor',
            'priority' => 'Normal',
            'title' => 'Tiket tanpa OPD',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('customer_id');

        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_user_can_create_view_update_change_status_and_delete_ticket(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $this->seed(OpdSeeder::class);
        $opd = Customer::where('type', 'opd')->firstOrFail();

        $this->post(route('tickets.store'), [
            'customer_id' => $opd->id,
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
            'customer_id' => $opd->id,
            'created_by' => $admin->id,
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
            'customer_id' => $opd->id,
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

    public function test_admin_can_assign_a_ticket_from_its_detail_using_the_assignment_controller(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $opd = Customer::create([
            'customer_code' => 'OPD9001',
            'name' => 'Dinas Contoh',
            'type' => 'opd',
            'status' => 'Aktif',
        ]);
        $technician = Technician::create([
            'nik' => 'TECH-9001',
            'name' => 'Teknisi Contoh',
            'position' => 'Teknisi',
            'area' => 'Situbondo',
            'status' => 'Available',
        ]);

        $this->post(route('tickets.store'), [
            'customer_id' => $opd->id,
            'location' => 'Kantor OPD',
            'reporter_name' => 'Pelapor Contoh',
            'priority' => 'Normal',
            'title' => 'Gangguan jaringan',
            'description' => 'Koneksi terputus.',
            'assigned_tech_name' => '-',
        ])->assertRedirect(route('tickets.index'));

        $ticket = Ticket::firstOrFail();
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'customer_id' => $opd->id,
            'created_by' => $admin->id,
        ]);

        $this->get(route('tickets.show', $ticket->id))
            ->assertOk()
            ->assertSee('Assignment Teknisi')
            ->assertSee('Teknisi Contoh');

        $this->postJson(route('tickets.assignments.store', $ticket->id), [
            'technician_id' => $technician->id,
        ])->assertCreated()
            ->assertJsonPath('data.technician_id', $technician->id);

        $this->assertDatabaseHas('ticket_assignments', [
            'ticket_id' => $ticket->id,
            'technician_id' => $technician->id,
            'is_active' => true,
        ]);
        $this->assertSame('Assigned', $ticket->fresh()->status);
        $this->assertSame('On Task', $technician->fresh()->status);
    }

    public function test_deleting_an_opd_keeps_existing_ticket_history_and_customer_foreign_key(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $opd = Customer::create([
            'customer_code' => 'OPD9002',
            'name' => 'Dinas Riwayat',
            'type' => 'opd',
            'status' => 'Aktif',
        ]);
        $ticket = Ticket::create([
            'ticket_number' => 'TK-OPD-DELETE-1',
            'customer_id' => $opd->id,
            'customer_name' => $opd->name,
            'title' => 'Tiket historis',
            'priority' => 'Normal',
            'status' => 'Open',
        ]);

        $this->delete(route('customers.destroy', $opd->id))
            ->assertRedirect(route('customers.index'));

        $this->assertSoftDeleted('customers', ['id' => $opd->id]);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'customer_id' => $opd->id,
            'customer_name' => 'Dinas Riwayat',
        ]);
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
            'reporter_name' => 'Pelapor Baru',
            'reporter_contact' => '081111111111',
        ]);
    }

    public function test_customer_page_can_save_opd_reporter_details(): void
    {
        $this->actingAs(User::factory()->create());
        $this->seed(OpdSeeder::class);
        $this->assertDatabaseCount('customers', 52);
        $customer = Customer::where('name', 'Dinas Komunikasi dan Informatika')->firstOrFail();
        $validOpdDetails = [
            'name' => $customer->name,
            'type' => 'opd',
            'reporter_name' => 'Nama Pelapor',
            'area' => 'Situbondo',
            'address' => 'Jl. Basuki Rahmat, Kabupaten Situbondo',
            'latitude' => '-7.706',
            'longitude' => '114.009',
            'registration_date' => '2026-10-28',
            'package_name' => '50 Mbps',
            'status' => 'Aktif',
        ];

        $this->from(route('customers.index'))->post(route('customers.store'), [
            ...$validOpdDetails,
            'reporter_contact' => '081234567890123',
        ])->assertSessionHasErrors('reporter_contact');

        $this->from(route('customers.index'))->post(route('customers.store'), [
            ...$validOpdDetails,
            'reporter_contact' => '0812ABC567',
        ])->assertSessionHasErrors('reporter_contact');

        $this->from(route('customers.index'))->post(route('customers.store'), [
            ...$validOpdDetails,
            'reporter_contact' => '081234567890',
            'package_name' => 'Lainnya',
        ])->assertSessionHasErrors('package_name');

        $this->post(route('customers.store'), [
            'name' => $customer->name,
            'type' => 'opd',
            'reporter_name' => 'Nama Pelapor',
            'reporter_contact' => '081234567890',
            'area' => 'Situbondo',
            'address' => 'Jl. Basuki Rahmat, Kabupaten Situbondo',
            'latitude' => '-7.706',
            'longitude' => '114.009',
            'registration_date' => '2026-10-28',
            'package_name' => '50 Mbps',
            'status' => 'Aktif',
        ])->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Dinas Komunikasi dan Informatika',
            'type' => 'opd',
            'reporter_name' => 'Nama Pelapor',
            'reporter_contact' => '081234567890',
            'package_name' => '50 Mbps',
        ]);
        $this->assertSame('2026-10-28', $customer->fresh()->registration_date?->format('Y-m-d'));

        $customer->refresh();
        $this->assertEqualsWithDelta(-7.706, (float) $customer->latitude, 0.000001);
        $this->assertEqualsWithDelta(114.009, (float) $customer->longitude, 0.000001);
        $this->assertSame('OPD0001', $customer->customer_code);

        $this->post(route('customers.store'), [
            'name' => $customer->name,
            'reporter_name' => 'Pelapor Kedua',
            'reporter_contact' => '081122223333',
            'area' => 'Situbondo',
            'address' => 'Alamat pelanggan kedua',
            'latitude' => '-7.706',
            'longitude' => '114.009',
            'registration_date' => '2026-10-28',
            'package_name' => '50 Mbps',
            'status' => 'Aktif',
        ])->assertRedirect(route('customers.index'));

        $additionalOpd = Customer::where('type', 'opd')
            ->where('name', $customer->name)
            ->where('id', '!=', $customer->id)
            ->firstOrFail();
        $this->assertSame(2, Customer::where('type', 'opd')->where('name', $customer->name)->count());
        $this->assertSame('Nama Pelapor', $customer->fresh()->reporter_name);
        $this->assertSame('Jl. Basuki Rahmat, Kabupaten Situbondo', $customer->fresh()->address);
        $this->assertSame('Pelapor Kedua', $additionalOpd->reporter_name);
        $this->assertNotSame($customer->customer_code, $additionalOpd->customer_code);
        $this->assertSame('OPD0002', $additionalOpd->customer_code);

        $this->put(route('customers.update', $customer->id), [
            'name' => 'Nama OPD Tidak Boleh Berubah',
            'customer_code' => 'OPD9999',
            'type' => 'opd',
            'area' => 'Besuki',
            'address' => 'Alamat hasil perubahan',
            'latitude' => '-7.715',
            'longitude' => '114.010',
            'registration_date' => '2026-10-29',
            'package_name' => '1 Gbps',
            'status' => 'Aktif',
        ])->assertRedirect(route('customers.index'));

        $customer->refresh();
        $expectedCode = 'OPD0001';
        $this->assertSame($expectedCode, $customer->customer_code);
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => 'Dinas Komunikasi dan Informatika',
            'customer_code' => $expectedCode,
            'reporter_name' => 'Nama Pelapor',
            'reporter_contact' => '081234567890',
            'area' => 'Besuki',
            'package_name' => '1 Gbps',
        ]);
        $this->assertSame('2026-10-29', $customer->fresh()->registration_date?->format('Y-m-d'));

        $customer->update(['package_name' => '4000']);
        $this->put(route('customers.update', $customer->id), [
            'area' => 'Besuki',
            'address' => 'Alamat hasil perubahan',
            'latitude' => '-7.715',
            'longitude' => '114.010',
            'registration_date' => '2026-10-29',
            'package_name' => '4000',
            'status' => 'Aktif',
        ])->assertRedirect(route('customers.index'));

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'package_name' => '4000',
        ]);

        $this->get(route('customers.index'))
            ->assertOk()
            ->assertSee('Dinas Komunikasi dan Informatika')
            ->assertSee('Nama Pelapor');
    }
}
