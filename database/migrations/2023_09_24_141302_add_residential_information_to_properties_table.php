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
        Schema::table('properties', function (Blueprint $table) {
            $table->string('street_number')->nullable()->after('plot_size');
            $table->string('street_name')->nullable()->after('plot_size');
            $table->string('city')->nullable()->after('plot_size');
            $table->string('landmark')->nullable()->after('plot_size');
            $table->foreignId('state_id')->after('plot_size');
            $table->foreignId('local_government_area_id')->after('plot_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            //
        });
    }
};
