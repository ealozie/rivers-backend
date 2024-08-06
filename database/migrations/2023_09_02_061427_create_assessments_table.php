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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revenue_item_id');
            $table->foreignId('agency_id');
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone_number');
            $table->string('contact_address')->nullable();
            $table->double('amount')->nullable();
            $table->string('assessment_reference')->nullable();
            $table->string('receipt_number')->nullable();
            $table->foreignId('assessment_year_id');
            $table->string('status')->default('pending');
            $table->string('payment_status')->default('pending');
            $table->foreignId('added_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
