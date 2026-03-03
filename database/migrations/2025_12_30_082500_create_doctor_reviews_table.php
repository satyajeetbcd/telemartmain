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
        Schema::create('doctor_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->integer('rating')->unsigned(); // 1-5 stars
            $table->text('comment')->nullable();
            $table->text('doctor_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->index(['doctor_id', 'is_visible']);
            $table->index(['patient_id']);
            $table->index(['appointment_id']);
            // Ensure a patient can only review a doctor once per appointment
            $table->unique(['doctor_id', 'appointment_id'], 'unique_doctor_appointment_review');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_reviews');
    }
};
