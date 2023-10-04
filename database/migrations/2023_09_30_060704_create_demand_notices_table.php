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
        Schema::create('demand_notices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('year_id');
            $table->foreignId('generated_by')->nullable();
            $table->foreignId('served_by')->nullable();
            $table->date('date_served')->nullable();
            //latitude and longitude
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->string('status')->default('pending');
            $table->boolean('has_been_served')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demand_notices');
    }
};
