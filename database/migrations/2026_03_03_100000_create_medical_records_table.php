<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->string('record_number')->unique();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->enum('record_type', [
                'consultation', 'lab_report', 'prescription', 'diagnosis',
                'discharge_summary', 'imaging', 'vaccination', 'surgical',
                'follow_up', 'other'
            ])->default('consultation');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('symptoms')->nullable();
            $table->json('vitals')->nullable();
            $table->text('prescription')->nullable();
            $table->text('treatment_plan')->nullable();
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->date('record_date');
            $table->date('follow_up_date')->nullable();
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_id', 'record_date']);
            $table->index(['doctor_id', 'record_date']);
            $table->index('record_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
