<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sms_templates', function (Blueprint $table) {
            $table->string('type')->default('general')->after('slug');
            $table->string('subject')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('sms_templates', function (Blueprint $table) {
            $table->dropColumn(['type', 'subject']);
        });
    }
};
