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
        Schema::create('ticket_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('agent_type');
            $table->string('agent_status');
            $table->double('discount', 8, 2);
            $table->foreignId('added_by');
            $table->double('wallet_balance', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_agents');
    }
};
