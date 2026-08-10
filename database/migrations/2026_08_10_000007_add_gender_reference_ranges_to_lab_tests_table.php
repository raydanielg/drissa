<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lab_tests', function (Blueprint $table) {
            $table->string('reference_range_male')->nullable()->after('reference_range');
            $table->string('reference_range_female')->nullable()->after('reference_range_male');
            $table->string('reference_range_pregnant')->nullable()->after('reference_range_female');
            $table->string('reference_range_safe')->nullable()->after('reference_range_pregnant');
        });
    }

    public function down(): void
    {
        Schema::table('lab_tests', function (Blueprint $table) {
            $table->dropColumn([
                'reference_range_male',
                'reference_range_female',
                'reference_range_pregnant',
                'reference_range_safe',
            ]);
        });
    }
};
