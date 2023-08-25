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
        Schema::create('ticket_agent_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_agent_id');
            $table->foreignId('user_id');
            $table->double('amount', 8, 2);
            $table->string('transaction_type'); //Credit or debit
            $table->string('transaction_reference_number');
            $table->string('transaction_status');
            $table->foreignId('added_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_agent_wallets');
    }
};
