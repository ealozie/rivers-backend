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
        Schema::create('ticket_agent_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_agent_id');
            $table->foreignId('ticket_category_id');
            $table->double('discount', 8, 2)->nullable();
            $table->foreignId('added_by');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_agent_categories');
    }
};
