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
            $table->string('street_number')->nullable()->after('user_id');
            $table->string('street_name')->nullable()->after('user_id');
            $table->string('city')->nullable()->after('user_id');
            $table->string('landmark')->nullable()->after('user_id');
            $table->foreignId('local_government_area_id')->after('user_id')->nullable();
            //state_id
            $table->foreignId('state_id')->references('id')->after('user_id');
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
