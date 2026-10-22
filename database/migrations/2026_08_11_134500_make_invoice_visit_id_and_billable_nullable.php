<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropForeign(['visit_id']);
                $table->unsignedBigInteger('visit_id')->nullable()->change();
                $table->foreign('visit_id')->references('id')->on('visits')->restrictOnDelete();
            });

            Schema::table('invoice_items', function (Blueprint $table) {
                $table->string('billable_type')->nullable()->change();
                $table->unsignedBigInteger('billable_id')->nullable()->change();
            });

            return;
        }

        DB::statement('ALTER TABLE invoices DROP FOREIGN KEY invoices_visit_id_foreign');
        DB::statement('ALTER TABLE invoices MODIFY visit_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE invoices ADD CONSTRAINT invoices_visit_id_foreign FOREIGN KEY (visit_id) REFERENCES visits(id) ON DELETE RESTRICT');

        DB::statement('ALTER TABLE invoice_items MODIFY billable_type VARCHAR(255) NULL');
        DB::statement('ALTER TABLE invoice_items MODIFY billable_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropForeign(['visit_id']);
                $table->unsignedBigInteger('visit_id')->nullable(false)->change();
                $table->foreign('visit_id')->references('id')->on('visits')->restrictOnDelete();
            });

            Schema::table('invoice_items', function (Blueprint $table) {
                $table->unsignedBigInteger('billable_id')->nullable(false)->change();
                $table->string('billable_type')->nullable(false)->change();
            });

            return;
        }

        DB::statement('ALTER TABLE invoice_items MODIFY billable_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE invoice_items MODIFY billable_type VARCHAR(255) NOT NULL');

        DB::statement('ALTER TABLE invoices DROP FOREIGN KEY invoices_visit_id_foreign');
        DB::statement('ALTER TABLE invoices MODIFY visit_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE invoices ADD CONSTRAINT invoices_visit_id_foreign FOREIGN KEY (visit_id) REFERENCES visits(id) ON DELETE RESTRICT');
    }
};
