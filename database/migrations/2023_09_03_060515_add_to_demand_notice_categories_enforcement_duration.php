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
        Schema::table('demand_notice_categories', function (Blueprint $table) {
            $table->integer('enforcement_duration');
            $table->text('html_content')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('added_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demand_notice_categories', function (Blueprint $table) {
            //
        });
    }
};
