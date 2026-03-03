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
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->string('aadhar_card_number', 12)->nullable()->after('date_of_birth');
            $table->text('address')->nullable()->after('aadhar_card_number');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code', 10)->nullable()->after('state');
            $table->string('country')->nullable()->default('India')->after('postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth',
                'aadhar_card_number',
                'address',
                'city',
                'state',
                'postal_code',
                'country',
            ]);
        });
    }
};
