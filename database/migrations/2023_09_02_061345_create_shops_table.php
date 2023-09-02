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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('shop_number')->nullable();
            $table->string('zone')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('market_name_id');
            $table->string('street_name')->nullable();
            $table->string('street_number')->nullable();
            $table->string('city')->nullable();
            $table->foreignId('local_government_area_id');
            $table->foreignId('business_category_id');
            $table->foreignId('business_sub_category_id');
            $table->foreignId('classification_id');
            $table->foreignId('user_id');
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('added_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
