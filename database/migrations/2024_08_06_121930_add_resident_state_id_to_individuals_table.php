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
            $table->foreignId('residence_state_id')->nullable()->after('state_id');
            $table->foreignId('residence_local_government_area_id')->nullable()->after('local_government_area_id');
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
