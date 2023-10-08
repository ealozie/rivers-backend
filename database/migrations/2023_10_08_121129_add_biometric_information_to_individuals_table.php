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
        Schema::table('individuals', function (Blueprint $table) {
            $table->string('phone_number')->default('pending')->after('city');
            $table->string('email_address')->default('pending')->after('city');
            $table->string('registration_status')->default('pending')->after('city');
            $table->string('facial_biometric_status')->default('pending')->after('city');
            $table->string('facial_biometric_image_url')->default('pending')->after('city');
            $table->string('facial_confirmation_token')->nullable()->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('individuals', function (Blueprint $table) {
            //
        });
    }
};
