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
            $table->string('zoom_meeting_id')->nullable()->after('cancelled_at');
            $table->string('zoom_meeting_uuid')->nullable()->after('zoom_meeting_id');
            $table->text('zoom_join_url')->nullable()->after('zoom_meeting_uuid');
            $table->text('zoom_start_url')->nullable()->after('zoom_join_url');
            $table->string('zoom_meeting_password')->nullable()->after('zoom_start_url');
            $table->timestamp('zoom_meeting_created_at')->nullable()->after('zoom_meeting_password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn([
                'zoom_meeting_id',
                'zoom_meeting_uuid',
                'zoom_join_url',
                'zoom_start_url',
                'zoom_meeting_password',
                'zoom_meeting_created_at',
            ]);
        });
    }
};
