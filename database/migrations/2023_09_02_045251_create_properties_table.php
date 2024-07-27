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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('property_category_id');
            $table->integer('number_of_floors')->default(0);
            $table->foreignId('property_type_id');
            $table->integer('number_of_beds')->default(0);
            $table->integer('number_of_rooms')->default(0);
            $table->double('plot_size')->default(0);
            $table->foreignId('property_use_id');
            $table->foreignId('demand_notice_category_id');
            //latitude and longitude
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            //has borehole
            $table->boolean('has_borehole')->default(false);
            //has sewage
            $table->boolean('has_sewage')->default(false);
            //is connected to power
            $table->boolean('is_connected_to_power')->default(false);
            //has fence
            $table->boolean('has_fence')->default(false);
            //notes
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
