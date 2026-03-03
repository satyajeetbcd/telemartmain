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
        Schema::table('users', function (Blueprint $table) {
            // Add foreign keys for state and city
            $table->foreignId('state_id')->nullable()->after('address')->constrained('states')->onDelete('set null');
            $table->foreignId('city_id')->nullable()->after('state_id')->constrained('cities')->onDelete('set null');
            
            // Keep existing city and state text fields for backward compatibility
            // They can be removed later if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->dropForeign(['city_id']);
            $table->dropColumn(['state_id', 'city_id']);
        });
    }
};
