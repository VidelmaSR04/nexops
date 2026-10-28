<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('customers', 'phone')) {
            return;
        }

        if (Schema::hasColumn('customers', 'reporter_contact')) {
            DB::table('customers')
                ->whereNotNull('phone')
                ->where('phone', '<>', '')
                ->where(function ($query) {
                    $query->whereNull('reporter_contact')
                        ->orWhere('reporter_contact', '');
                })
                ->update(['reporter_contact' => DB::raw('phone')]);
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('customers', 'phone')) {
            return;
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->string('phone', 30)->nullable();
        });

        if (Schema::hasColumn('customers', 'reporter_contact')) {
            DB::table('customers')
                ->whereNotNull('reporter_contact')
                ->update(['phone' => DB::raw('reporter_contact')]);
        }
    }
};
