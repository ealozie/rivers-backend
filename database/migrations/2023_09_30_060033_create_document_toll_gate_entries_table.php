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
        Schema::create('document_toll_gate_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('toll_gate_category_id');
            $table->string('reference')->nullable();
            $table->date('date_issued')->nullable();
            $table->string('year_id')->nullable();
            $table->foreignId('added_by')->nullable();
            $table->text('reasons')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_toll_gate_entries');
    }
};
