<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $unlinkedTickets = DB::table('tickets')
            ->whereNull('customer_id')
            ->get(['id', 'customer_name']);
        $names = $unlinkedTickets->pluck('customer_name')->filter()->unique()->values();
        $customersByName = $names->isEmpty()
            ? collect()
            : DB::table('customers')
                ->whereIn('name', $names)
                ->get(['id', 'name'])
                ->groupBy('name');

        foreach ($unlinkedTickets as $ticket) {
            $matchingCustomers = $customersByName->get($ticket->customer_name, collect());

            if ($matchingCustomers->count() === 1) {
                DB::table('tickets')
                    ->where('id', $ticket->id)
                    ->update(['customer_id' => $matchingCustomers->first()->id]);
            }
        }

        $unresolvedTicketIds = DB::table('tickets')
            ->whereNull('customer_id')
            ->orderBy('id')
            ->limit(20)
            ->pluck('id')
            ->all();

        if ($unresolvedTicketIds !== []) {
            throw new \RuntimeException(
                'Cannot require a customer for every ticket. Link tickets with a unique matching customer name first. '
                .'Unresolved ticket IDs: '.implode(', ', $unresolvedTicketIds)
            );
        }

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable(false)->change();
            $table->foreign('customer_id')->references('id')->on('customers')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->change();
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        });
    }
};
