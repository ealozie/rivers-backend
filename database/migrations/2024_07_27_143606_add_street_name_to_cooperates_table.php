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
        Schema::table('cooperates', function (Blueprint $table) {
            $table->string('street_number', 20)->nullable()->after('user_id');
            $table->string('street_name', 50)->nullable()->after('user_id');
            $table->string('city', 20)->nullable()->after('user_id');
            $table->string('landmark', 50)->nullable()->after('user_id');
            $table->foreignId('local_government_area_id')->after('user_id')->nullable();
            $table->foreignId('state_id')->references('id')->after('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cooperates', function (Blueprint $table) {
            //
        });
    }
};
