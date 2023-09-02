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
        Schema::create('commercial_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('plate_number');
            $table->foreignId('vehicle_category_id');
            $table->foreignId('vehicle_manufacturer_id');
            $table->foreignId('vehicle_model_id');
            $table->string('chassis_number')->nullable();
            $table->string('engine_number')->nullable();
            $table->foreignId('ticket_category_id')->nullable();
            $table->string('capacity')->nullable();
            $table->string('routes')->nullable();
            $table->string('driver_id')->nullable();
            $table->string('driver_license_number')->nullable();
            $table->string('driver_license_expiry_date')->nullable();
            $table->string('driver_license_image')->nullable();
            $table->integer('permit_renewal_count')->nullable();
            $table->string('permit_number')->nullable();
            $table->string('permit_expiry_date')->nullable();
            $table->string('permit_image')->nullable();
            $table->text('note')->nullable();
            $table->string('status')->nullable();
            $table->foreignId('added_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commercial_vehicles');
    }
};
