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
        Schema::create('clinic_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->foreignId('department_id')->nullable()->constrained();
            $table->string('type')->default('consultation'); // consultation|procedure|ward|emergency
            $table->string('status')->default('available'); // available|occupied|maintenance
            $table->text('description')->nullable();
            $table->integer('capacity')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_rooms');
    }
};
