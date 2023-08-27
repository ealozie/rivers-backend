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
        Schema::create('ticket_vendings', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number');
            $table->foreignId('ticket_category_id');
            $table->double('amount', 8, 2);
            $table->double('ticket_amount', 8, 2);
            $table->double('agent_discount', 8, 2)->nullable();
            $table->foreignId('ticket_agent_id');
            $table->foreignId('user_id');
            $table->time('expired_at');
            $table->string('ticket_reference_number');
            $table->string('ticket_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_vendings');
    }
};
