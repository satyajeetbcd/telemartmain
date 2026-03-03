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
        // Modify the enum to include pending_approval
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `status` ENUM('active', 'inactive', 'pending_kyc', 'pending_approval') DEFAULT 'pending_kyc'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum (but we can't easily revert users with pending_approval status)
        // So we'll set them to pending_kyc first
        DB::table('users')
            ->where('status', 'pending_approval')
            ->update(['status' => 'pending_kyc']);
            
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `status` ENUM('active', 'inactive', 'pending_kyc') DEFAULT 'pending_kyc'");
    }
};
