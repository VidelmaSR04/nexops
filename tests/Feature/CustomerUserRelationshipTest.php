<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerUserRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_have_multiple_users_and_users_can_belong_to_one_customer(): void
    {
        $customer = Customer::create([
            'customer_code' => 'OPD9001',
            'name' => 'Dinas Contoh',
            'type' => 'opd',
        ]);
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();

        $firstUser->customer()->associate($customer)->save();
        $secondUser->customer()->associate($customer)->save();

        $this->assertTrue($firstUser->customer->is($customer));
        $this->assertTrue($secondUser->customer->is($customer));
        $this->assertEqualsCanonicalizing(
            [$firstUser->id, $secondUser->id],
            $customer->users()->pluck('users.id')->all(),
        );
    }

    public function test_user_can_have_no_customer_and_customer_deletion_sets_user_customer_id_to_null(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create([
            'customer_code' => 'OPD9002',
            'name' => 'Dinas Contoh Lain',
            'type' => 'opd',
        ]);
        $user->customer()->associate($customer)->save();

        $customer->forceDelete();

        $this->assertNull($user->fresh()->customer_id);
    }

    public function test_api_login_and_user_profile_return_person_name_and_opd_name(): void
    {
        $customer = Customer::create([
            'customer_code' => 'OPD9003',
            'name' => 'Dinas Komunikasi dan Informatika',
            'type' => 'opd',
            'area' => 'Situbondo',
        ]);

        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@diskominfo.go.id',
            'password' => bcrypt('secret123'),
            'role' => 'customer',
        ]);
        $user->customer()->associate($customer)->save();

        $loginResponse = $this->postJson('/api/login', [
            'email' => 'budi@diskominfo.go.id',
            'password' => 'secret123',
        ]);

        $loginResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.name', 'Budi Santoso')
            ->assertJsonPath('data.user.role', 'customer')
            ->assertJsonPath('data.user.customer.id', $customer->id)
            ->assertJsonPath('data.user.customer.name', 'Dinas Komunikasi dan Informatika')
            ->assertJsonPath('data.user.customer.customer_code', 'OPD9003');

        $token = $loginResponse->json('data.token');

        $profileResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/user');

        $profileResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Budi Santoso')
            ->assertJsonPath('data.customer.name', 'Dinas Komunikasi dan Informatika');
    }
}
