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
        Schema::create('ticket_bulk_vendings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_category_id');
            $table->string('plate_number');
            $table->double('amount', 8, 2);
            $table->foreignId('ticket_agent_id');
            $table->foreignId('user_id');
            $table->dateTime('expired_at')->nullable();
            $table->integer('duration');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_bulk_vendings');
    }
};
