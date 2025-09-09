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
        // Schema::create('ticket_vending_statistics', function (Blueprint $table) {
        //     $table->id();
        //     $table->double('ticket_amount', 15, 2);
        //     $table->double('ticket_discounted_amount', 15, 2);
        //     $table->integer('total_tickets');
        //     $table->date('ticket_date');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_vending_statistics');
    }
};
