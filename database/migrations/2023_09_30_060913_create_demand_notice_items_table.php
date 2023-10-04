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
        Schema::create('demand_notice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demand_notice_id');
            $table->string('agency_code', 20)->nullable();
            $table->string('revenue_code', 20)->nullable();
            $table->double('amount', 8, 2)->default(0);
            $table->string('payment_status')->default('pending');
            $table->string('payment_receipt_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demand_notice_items');
    }
};
