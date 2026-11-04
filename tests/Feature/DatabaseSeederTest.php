<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Ticket;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_seeder_adds_opd_master_data_without_demo_tickets(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(52, Customer::where('type', 'opd')->count());
        $this->assertSame(0, Customer::where('type', '!=', 'opd')->count());
        $this->assertSame(0, Ticket::withTrashed()->count());
    }
}
