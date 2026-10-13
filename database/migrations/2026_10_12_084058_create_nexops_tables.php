<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                // admin | manager | warehouse | technician
                $table->string('role', 20)->default('admin')->after('password');
            });
        }

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code', 30)->unique();        // contoh: CST0001
            $table->string('nik', 30)->nullable()->index();
            $table->string('name');
            $table->string('type', 20)->default('individual');    // individual | business
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('area', 60)->nullable()->index();      // Situbondo, Besuki, Lapangan, dst.
            $table->string('package_name')->nullable();           // 50 Mbps, 100 Mbps, dst.
            $table->string('monthly_bill', 50)->nullable();       // Rp350.000
            $table->string('status', 20)->default('Aktif')->index(); // Aktif | Baru | Nonaktif
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('technicians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('nik', 30)->unique();
            $table->string('name');
            $table->string('position', 50)->default('Teknisi');   // Teknisi | Teknisi Senior
            $table->string('area', 60)->nullable()->index();      // Situbondo, Besuki, Lapangan
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('status', 30)->default('Available')->index(); // On Task | Available | Offline | Cuti
            $table->date('joined_at')->nullable();
            $table->unsignedTinyInteger('exp_years')->default(1);
            $table->decimal('rating', 2, 1)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();                 // WH-001
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('area', 60)->nullable();
            $table->unsignedInteger('capacity')->default(0);
            $table->string('status', 30)->default('Aktif');       // Aktif | Maintenance | Nonaktif
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('manager_name')->nullable();
            $table->string('manager_role')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();                     // Modem/ONT, Kabel, Perangkat FO, ...
            $table->timestamps();
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 30)->unique();                  // PRD-001
            $table->string('name');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('unit', 20)->default('Unit');
            $table->unsignedInteger('min_stock')->default(0);
            $table->string('status', 30)->default('Aktif');       // Aktif | Low Stock | Habis
            $table->string('serial_example', 80)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Stok per gudang (total stok = SUM quantity)
        Schema::create('warehouse_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('min_quantity')->default(0);
            $table->timestamps();
            $table->unique(['warehouse_id', 'inventory_item_id']);
        });

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code', 30)->unique();           // AST-001
            $table->string('name');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('serial_number', 80)->nullable()->unique();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();  // lokasi jika di gudang
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();   // lokasi jika terpasang di pelanggan
            $table->string('location_name', 100)->nullable();    // lokasi tekstual (misal: Ruang Server, Kantor)
            $table->string('status', 30)->default('Aktif')->index(); // Aktif | Maintenance | Rusak | Nonaktif
            $table->string('vendor')->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('warranty', 50)->nullable();          // 12 Bulan, 24 Bulan, Expired
            $table->unsignedSmallInteger('warranty_months')->nullable();
            $table->string('icon', 30)->nullable();
            $table->text('specs')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 20)->unique();        // TK-1025
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name')->nullable();          // fallback nama customer
            $table->foreignId('asset_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('assigned_tech_name')->nullable();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('location', 100)->nullable();
            $table->string('priority', 20)->default('Normal')->index(); // Low | Normal | High | Critical
            $table->string('status', 30)->default('Open')->index();     // Open | Assigned | In Progress | Waiting | Resolved | Closed
            $table->timestamp('sla_due_at')->nullable()->index();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Relasi penugasan tiket teknisi
        Schema::create('ticket_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technician_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('unassigned_at')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('ticket_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_name')->nullable();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('unit', 20)->default('pcs');
            $table->timestamps();
        });

        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('file_name');
            $table->string('file_path')->nullable();
            $table->unsignedInteger('file_size')->default(0);     // byte
            $table->string('file_size_human', 30)->nullable();   // 1.4 MB
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->foreignId('to_warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->string('type', 20)->index();                  // Masuk | Keluar | Transfer
            $table->unsignedInteger('quantity');
            $table->foreignId('ticket_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference')->nullable();              // PO-2026-089, Ticket #TK-1025
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->nullableMorphs('subject');
            $table->string('action', 50);                         // created | updated | assigned | status_changed | ...
            $table->string('description')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 30)->default('general');      // general | notification | system | security
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device', 100)->nullable();
            $table->string('location', 100)->nullable();
            $table->string('status', 20)->default('Success');
            $table->timestamp('logged_in_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_histories');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('ticket_attachments');
        Schema::dropIfExists('ticket_materials');
        Schema::dropIfExists('ticket_assignments');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('warehouse_stocks');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('technicians');
        Schema::dropIfExists('customers');
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
