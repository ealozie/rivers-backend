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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_type_id');
            $table->foreignId('user_id');
            $table->string('reference')->nullable();
            $table->text('content')->nullable();
            $table->string('status')->default('pending');
            $table->date('date_issued')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('year_id')->nullable();
            $table->foreignId('added_by')->nullable();
            $table->string('channel_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
