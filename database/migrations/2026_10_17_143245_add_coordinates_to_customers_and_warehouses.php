<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['customers', 'warehouses'] as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                if (!Schema::hasColumn($table, 'latitude')) {
                    $t->decimal('latitude', 10, 7)->nullable();
                }
                if (!Schema::hasColumn($table, 'longitude')) {
                    $t->decimal('longitude', 10, 7)->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['customers', 'warehouses'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn(['latitude', 'longitude']);
            });
        }
    }
};
