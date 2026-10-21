<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
       public function up(): void
   {
       Schema::table('tickets', function (Blueprint $table) {
           $table->string('reporter_name', 150)->nullable()->after('customer_name');
           $table->string('reporter_contact', 50)->nullable()->after('reporter_name');
       });
   }

   public function down(): void
   {
       Schema::table('tickets', function (Blueprint $table) {
           $table->dropColumn(['reporter_name', 'reporter_contact']);
       });
   }
};
