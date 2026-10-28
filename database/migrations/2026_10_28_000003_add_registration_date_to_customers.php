<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('customers', 'registration_date')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->date('registration_date')->nullable()->after('customer_code');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('customers', 'registration_date')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('registration_date');
            });
        }
    }
};
