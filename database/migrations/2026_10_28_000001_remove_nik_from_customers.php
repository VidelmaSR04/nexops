<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('customers', 'nik')) {
            return;
        }

        DB::table('customers')
            ->whereNotNull('nik')
            ->where('nik', '<>', '')
            ->delete();

        if (Schema::hasIndex('customers', ['nik'])) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropIndex(['nik']);
            });
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('nik');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('customers', 'nik')) {
            return;
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->string('nik', 30)->nullable()->index();
        });
    }
};
