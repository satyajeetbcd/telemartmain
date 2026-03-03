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
        Schema::create('doctor_availability_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('slot_duration')->default(30); // Duration in minutes
            $table->integer('break_duration')->default(0); // Break between slots in minutes
            $table->boolean('is_available')->default(true);
            $table->date('specific_date')->nullable(); // For one-time availability overrides
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['doctor_id', 'day_of_week', 'is_available'], 'doc_avail_day_idx');
            $table->index(['doctor_id', 'specific_date'], 'doc_avail_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_availability_slots');
    }
};
