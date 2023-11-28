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
        Schema::table('ticket_bulk_vendings', function (Blueprint $table) {
            $table->decimal('longitude', 10, 7)->nullable()->after('status');
            $table->decimal('latitude', 10, 7)->nullable()->after('status');
            $table->string('phone_number')->nullable()->after('plate_number');
            $table->double('discounted_price', 8, 2)->nullable()->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_bulk_vendings', function (Blueprint $table) {
            //
        });
    }
};
