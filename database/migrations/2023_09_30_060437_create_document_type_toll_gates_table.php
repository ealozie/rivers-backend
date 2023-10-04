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
        Schema::create('document_type_toll_gates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_type_id');
            $table->foreignId('toll_gate_category_id');
            $table->foreignId('toll_gate_payment_revenue_item_id');
            $table->double('toll_gate_payment_amount', 8, 2)->default(0);
            $table->foreignId('current_year_id')->nullable();
            $table->foreignId('year_id')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_type_toll_gates');
    }
};
