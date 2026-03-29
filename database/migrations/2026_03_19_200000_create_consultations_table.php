<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->string('consultation_number')->unique();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_followup')->default(false);
            $table->json('chief_complaints')->nullable();
            $table->json('patient_history')->nullable();
            $table->json('personal_history')->nullable();
            $table->json('family_history')->nullable();
            $table->json('allergies')->nullable();
            $table->json('medications')->nullable();
            $table->text('query')->nullable();
            $table->string('location_preference')->nullable();
            $table->string('state')->nullable();
            $table->string('opd')->nullable();
            $table->json('health_records')->nullable();
            $table->string('status')->default('pending'); // pending, in_review, completed, cancelled
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_id', 'created_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
