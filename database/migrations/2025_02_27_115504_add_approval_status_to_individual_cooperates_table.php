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
        // Schema::table('individuals', function (Blueprint $table) {
        //     $table->string('approval_status')->after('user_id')->default('pending');
        // });
        // Schema::table('shops', function (Blueprint $table) {
        //     $table->string('approval_status')->after('user_id')->default('pending');
        // });
        // Schema::table('cooperates', function (Blueprint $table) {
        //     $table->string('approval_status')->after('user_id')->default('pending');
        //     //$table->string('email_address')->after('phone_number')->nullable();
        // });
        // Schema::table('commercial_vehicles', function (Blueprint $table) {
        //     $table->string('approval_status')->after('user_id')->default('pending');
        // });
        // Schema::table('signages', function (Blueprint $table) {
        //     $table->string('approval_status')->after('user_id')->default('pending');
        // });
        // Schema::table('properties', function (Blueprint $table) {
        //     $table->string('approval_status')->after('user_id')->default('pending');
        // });
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
