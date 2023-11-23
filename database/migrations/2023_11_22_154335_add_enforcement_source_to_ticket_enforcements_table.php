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
        Schema::table('ticket_enforcements', function (Blueprint $table) {
            $table->string('enforcement_source')->default('plate_number')->after('plate_number');
            $table->string('phone_number')->nullable()->after('plate_number');
            $table->string('plate_number')->nullable()->change();
            $table->string('plate_number')->nullable()->change();
            $table->decimal('longitude', 10, 7)->nullable()->after('status');
            $table->decimal('latitude', 10, 7)->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_enforcements', function (Blueprint $table) {
            //
        });
    }
};
