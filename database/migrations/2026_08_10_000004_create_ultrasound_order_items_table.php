<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ultrasound_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ultrasound_order_id')->constrained('ultrasound_orders')->cascadeOnDelete();
            $table->foreignId('ultrasound_service_id')->constrained('ultrasound_services')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ultrasound_order_items');
    }
};
