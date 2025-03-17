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
        Schema::create('signages', function (Blueprint $table) {
            $table->id();
            //height
            $table->double('height_in_meters');
            //width
            $table->double('width_in_meters');
            //longitude
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            //latitude
            $table->string('street_name')->nullable();
            $table->string('street_number')->nullable();
            $table->string('city')->nullable();
            $table->foreignId('local_government_area_id');
            $table->foreignId('user_id')->nullable();
            $table->foreignId('added_by');
            //note
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signages');
    }
};
