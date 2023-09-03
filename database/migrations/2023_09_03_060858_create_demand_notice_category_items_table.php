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
        Schema::create('demand_notice_category_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demand_notice_category_id');
            $table->string('revenue_code');
            $table->string('agency_code');
            $table->double('amount', 8, 2);
            $table->string('status')->default('active');
            $table->foreignId('added_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demand_notice_category_items');
    }
};
