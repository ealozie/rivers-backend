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
        Schema::create('ticket_enforcements', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number');
            $table->foreignId('ticket_category_id');
            $table->text('response');
            $table->foreignId('ticket_agent_id');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_enforcements');
    }
};
