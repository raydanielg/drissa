<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE manager_reports MODIFY `type` ENUM('daily','weekly','monthly') NOT NULL");

            return;
        }

        Schema::table('manager_reports', function (Blueprint $table) {
            $table->enum('type', ['daily', 'weekly', 'monthly'])->change();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE manager_reports MODIFY `type` ENUM('daily','weekly') NOT NULL");

            return;
        }

        Schema::table('manager_reports', function (Blueprint $table) {
            $table->enum('type', ['daily', 'weekly'])->change();
        });
    }
};
