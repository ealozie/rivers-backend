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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('log_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('log_date')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->string('payer_name')->nullable();
            $table->string('payer_phone_number')->nullable();
            $table->string('payer_address')->nullable();
            $table->string('receipt_number')->nullable();
            $table->string('type_code')->nullable();
            $table->string('amount')->nullable();
            $table->string('transaction_status')->nullable();
            $table->string('method')->nullable();
            $table->string('payment_channel')->nullable();
            $table->string('payment_channel_id')->nullable();
            $table->string('deposit_slip_number')->nullable();
            $table->date('cheque_value_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->foreignId('bank_id')->nullable();
            $table->string('product_name')->nullable();
            $table->string('payment_type_name')->nullable();
            $table->string('product_id')->nullable();
            $table->string('branch_id')->nullable();
            $table->string('branch_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
