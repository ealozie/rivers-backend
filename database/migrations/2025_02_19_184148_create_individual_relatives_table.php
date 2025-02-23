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
        Schema::create('individual_relatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('individuals', 'individual_id')->cascadeOnDelete();
            $table->foreignId('relative_id')->constrained('individuals', 'individual_id')->cascadeOnDelete();
            $table->string('relationship');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('individual_relatives');
    }
};
