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
        Schema::create('revenue_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id');
            $table->string('revenue_name');
            $table->string('revenue_code');
            $table->double('fixed_fee', 8, 2);
            $table->string('unique_code')->unique();
            $table->foreignId('revenue_type_id');
            $table->foreignId('added_by');
            //notes
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenue_items');
    }
};
