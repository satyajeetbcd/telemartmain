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
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('zoom_meeting_status')->nullable()->after('zoom_meeting_created_at');
            $table->timestamp('zoom_meeting_started_at')->nullable()->after('zoom_meeting_status');
            $table->timestamp('zoom_meeting_ended_at')->nullable()->after('zoom_meeting_started_at');
            $table->timestamp('zoom_participant_doctor_joined_at')->nullable()->after('zoom_meeting_ended_at');
            $table->timestamp('zoom_participant_patient_joined_at')->nullable()->after('zoom_participant_doctor_joined_at');
            $table->unsignedInteger('zoom_meeting_duration_minutes')->nullable()->after('zoom_participant_patient_joined_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn([
                'zoom_meeting_status',
                'zoom_meeting_started_at',
                'zoom_meeting_ended_at',
                'zoom_participant_doctor_joined_at',
                'zoom_participant_patient_joined_at',
                'zoom_meeting_duration_minutes',
            ]);
        });
    }
};
