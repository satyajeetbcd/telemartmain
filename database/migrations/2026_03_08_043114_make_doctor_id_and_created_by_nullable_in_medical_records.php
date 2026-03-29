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
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
            $table->dropForeign(['created_by']);

            $table->unsignedBigInteger('doctor_id')->nullable()->change();
            $table->unsignedBigInteger('created_by')->nullable()->change();

            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
            $table->dropForeign(['created_by']);

            $table->unsignedBigInteger('doctor_id')->nullable(false)->change();
            $table->unsignedBigInteger('created_by')->nullable(false)->change();

            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
