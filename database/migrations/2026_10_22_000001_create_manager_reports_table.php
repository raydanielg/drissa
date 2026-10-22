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
        Schema::create('manager_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['daily', 'weekly']);
            $table->date('report_date');
            $table->date('week_start')->nullable();
            $table->date('week_end')->nullable();
            $table->decimal('revenue_overall', 14, 2)->nullable();
            $table->decimal('supplement_sales_ppv', 14, 2)->nullable();
            $table->decimal('product_sales_costs', 14, 2)->nullable();
            $table->unsignedInteger('new_clients')->nullable();
            $table->unsignedInteger('returning_clients')->nullable();
            $table->decimal('lab_tools_expenses', 14, 2)->nullable();
            $table->decimal('office_expenses', 14, 2)->nullable();
            $table->json('department_reports')->nullable();
            $table->text('staff_performance')->nullable();
            $table->text('challenges')->nullable();
            $table->text('action_plan')->nullable();
            $table->text('meeting_reports')->nullable();
            $table->string('manager_signature')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'type', 'report_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manager_reports');
    }
};
