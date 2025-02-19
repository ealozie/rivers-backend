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
        Schema::table('signages', function (Blueprint $table) {
            $table->foreignId('property_id')->nullable()->after('signage_id');
            $table->string('signage_location')->nullable()->after('property_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signages', function (Blueprint $table) {
            //
        });
    }
};
