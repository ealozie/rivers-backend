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
        Schema::create('service_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_category_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->foreignId('service_provider_id')->nullable();
            $table->double('fees', 12, 2)->default(0);
            $table->string('processing_time', 20)->nullable();
            $table->string('status', 20)->default('active');
            $table->string('landing_page_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_sub_categories');
    }
};
