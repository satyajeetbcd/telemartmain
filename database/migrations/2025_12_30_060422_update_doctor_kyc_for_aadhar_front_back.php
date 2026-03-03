<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, modify the enum to include new types
        DB::statement("ALTER TABLE `doctor_kyc_documents` MODIFY COLUMN `document_type` ENUM('aadhar', 'aadhar_front', 'aadhar_back', 'degree', 'pan') DEFAULT 'degree'");
        
        // Then update existing 'aadhar' records to 'aadhar_front' if any exist
        DB::table('doctor_kyc_documents')
            ->where('document_type', 'aadhar')
            ->update(['document_type' => 'aadhar_front']);

        // Finally, remove 'aadhar' from enum
        DB::statement("ALTER TABLE `doctor_kyc_documents` MODIFY COLUMN `document_type` ENUM('aadhar_front', 'aadhar_back', 'degree', 'pan') DEFAULT 'degree'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert aadhar_front back to aadhar
        DB::table('doctor_kyc_documents')
            ->where('document_type', 'aadhar_front')
            ->update(['document_type' => 'aadhar']);

        DB::table('doctor_kyc_documents')
            ->where('document_type', 'aadhar_back')
            ->delete();

        // Revert enum
        DB::statement("ALTER TABLE `doctor_kyc_documents` MODIFY COLUMN `document_type` ENUM('aadhar', 'degree', 'pan') DEFAULT 'degree'");
    }
};
