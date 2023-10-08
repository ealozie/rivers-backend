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
            $table->dateTime('phone_number_verified_at')->nullable();
            $table->string('phone_number_verification_code')->nullable();
            // $table->string('registration_status')->default('pending');
            // $table->string('facial_biometric_status')->default('pending');
            // $table->string('facial_biometric_image_url')->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
