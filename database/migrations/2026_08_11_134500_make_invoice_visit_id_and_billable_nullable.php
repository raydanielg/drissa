<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE invoices DROP FOREIGN KEY invoices_visit_id_foreign');
        DB::statement('ALTER TABLE invoices MODIFY visit_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE invoices ADD CONSTRAINT invoices_visit_id_foreign FOREIGN KEY (visit_id) REFERENCES visits(id) ON DELETE RESTRICT');

        DB::statement('ALTER TABLE invoice_items MODIFY billable_type VARCHAR(255) NULL');
        DB::statement('ALTER TABLE invoice_items MODIFY billable_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE invoice_items MODIFY billable_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE invoice_items MODIFY billable_type VARCHAR(255) NOT NULL');

        DB::statement('ALTER TABLE invoices DROP FOREIGN KEY invoices_visit_id_foreign');
        DB::statement('ALTER TABLE invoices MODIFY visit_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE invoices ADD CONSTRAINT invoices_visit_id_foreign FOREIGN KEY (visit_id) REFERENCES visits(id) ON DELETE RESTRICT');
    }
};
