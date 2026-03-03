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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_number')->unique(); // Unique appointment ID
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('pending');
            $table->text('reason')->nullable(); // Reason for visit
            $table->text('notes')->nullable(); // Doctor's notes
            $table->text('prescription')->nullable(); // Prescription details
            $table->decimal('consultation_fee', 10, 2)->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
