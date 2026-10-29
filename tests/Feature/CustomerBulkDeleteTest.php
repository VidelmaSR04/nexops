<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerBulkDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_selected_opd_customers_can_be_soft_deleted_without_deleting_unselected_records(): void
    {
        $this->actingAs(User::factory()->create());

        $firstOpd = $this->createCustomer('OPD001', 'Dinas Pertama', 'opd');
        $secondOpd = $this->createCustomer('OPD002', 'Dinas Kedua', 'opd');
        $unselectedOpd = $this->createCustomer('OPD003', 'Dinas Ketiga', 'opd');
        $individual = $this->createCustomer('CST0001', 'Pelanggan Individu', 'individual');

        $this->deleteJson(route('customers.bulk-destroy'), [
            'ids' => [$firstOpd->id, $secondOpd->id],
        ])
            ->assertOk()
            ->assertJsonPath('deleted_count', 2);

        $this->assertSoftDeleted('customers', ['id' => $firstOpd->id]);
        $this->assertSoftDeleted('customers', ['id' => $secondOpd->id]);
        $this->assertDatabaseHas('customers', ['id' => $unselectedOpd->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('customers', ['id' => $individual->id, 'deleted_at' => null]);
    }

    public function test_bulk_delete_rejects_non_opd_and_empty_selections(): void
    {
        $this->actingAs(User::factory()->create());
        $individual = $this->createCustomer('CST0001', 'Pelanggan Individu', 'individual');

        $this->deleteJson(route('customers.bulk-destroy'), ['ids' => [$individual->id]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['ids.0']);

        $this->deleteJson(route('customers.bulk-destroy'), ['ids' => []])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['ids']);

        $this->assertDatabaseHas('customers', ['id' => $individual->id, 'deleted_at' => null]);
    }

    private function createCustomer(string $code, string $name, string $type): Customer
    {
        return Customer::create([
            'customer_code' => $code,
            'name' => $name,
            'type' => $type,
            'status' => 'Aktif',
        ]);
    }
}
