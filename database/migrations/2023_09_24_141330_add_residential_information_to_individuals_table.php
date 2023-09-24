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
            $table->string('street_number')->nullable()->after('state_id');
            $table->string('street_name')->nullable()->after('state_id');
            $table->string('city')->nullable()->after('state_id');
            $table->string('landmark')->nullable()->after('state_id');
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
