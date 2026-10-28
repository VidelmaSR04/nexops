<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_items', 'condition')) {
                $table->string('condition', 30)->default('Baik');
            }

            if (!Schema::hasColumn('inventory_items', 'availability')) {
                $table->string('availability', 30)->default('Tersedia');
            }
        });
    }

    public function down(): void
    {
        $columns = array_filter(
            ['condition', 'availability'],
            fn (string $column): bool => Schema::hasColumn('inventory_items', $column)
        );

        if ($columns === []) {
            return;
        }

        Schema::table('inventory_items', function (Blueprint $table) use ($columns) {
            $table->dropColumn(array_values($columns));
        });
    }
};
