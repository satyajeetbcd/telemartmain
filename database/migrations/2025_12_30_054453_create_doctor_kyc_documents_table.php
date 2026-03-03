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
        Schema::create('doctor_kyc_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->enum('document_type', ['aadhar', 'degree', 'pan'])->default('degree');
            $table->string('document_name')->nullable(); // e.g., "MBBS Degree", "MD Cardiology"
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();

            $table->index(['doctor_id', 'document_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_kyc_documents');
    }
};
