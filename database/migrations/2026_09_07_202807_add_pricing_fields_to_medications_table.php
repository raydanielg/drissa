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
        Schema::table('medications', function (Blueprint $table) {
            $table->decimal('purchase_price', 10, 2)->default(0)->after('unit_price');
            $table->string('batch_no')->nullable()->after('form');
            $table->string('manufacturer')->nullable()->after('batch_no');
            $table->string('category')->nullable()->after('manufacturer');
            $table->text('description')->nullable()->after('category');
        });

        // Migrate existing unit_price data to purchase_price as default
        \Illuminate\Support\Facades\DB::statement('UPDATE medications SET purchase_price = unit_price WHERE purchase_price = 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->dropColumn(['purchase_price', 'batch_no', 'manufacturer', 'category', 'description']);
        });
    }
};
