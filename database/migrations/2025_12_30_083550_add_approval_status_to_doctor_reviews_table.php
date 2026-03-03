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
        Schema::table('doctor_reviews', function (Blueprint $table) {
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('is_visible');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('approval_status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejection_reason')->nullable()->after('approved_at');
            
            $table->index(['approval_status', 'is_visible']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctor_reviews', function (Blueprint $table) {
            $table->dropIndex(['approval_status', 'is_visible']);
            $table->dropColumn(['approval_status', 'approved_by', 'approved_at', 'rejection_reason']);
        });
    }
};
