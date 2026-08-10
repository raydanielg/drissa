<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ultrasound_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->nullable()->constrained('visits')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('ordered_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending'); // pending|processing|completed
            $table->text('clinical_notes')->nullable();
            $table->text('findings')->nullable();
            $table->text('impression')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ultrasound_orders');
    }
};
