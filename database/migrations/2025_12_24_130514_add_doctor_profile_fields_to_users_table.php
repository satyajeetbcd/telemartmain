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
            $table->string('phone')->nullable()->after('email');
            $table->string('specialization')->nullable()->after('phone');
            $table->text('qualifications')->nullable()->after('specialization');
            $table->text('bio')->nullable()->after('qualifications');
            $table->integer('experience_years')->nullable()->after('bio');
            $table->decimal('consultation_fee', 10, 2)->nullable()->after('experience_years');
            $table->string('license_number')->nullable()->after('consultation_fee');
            $table->string('profile_image')->nullable()->after('license_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'specialization',
                'qualifications',
                'bio',
                'experience_years',
                'consultation_fee',
                'license_number',
                'profile_image',
            ]);
        });
    }
};
