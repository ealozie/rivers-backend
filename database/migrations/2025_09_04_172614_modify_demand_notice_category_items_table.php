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
        Schema::table('demand_notice_category_items', function (Blueprint $table) {
            $table->dropColumn(['revenue_code', 'agency_code']);
            $table->foreignId('revenue_item_id')->nullable()->before('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demand_notice_category_items', function (Blueprint $table) {
            $table->dropColumn('revenue_item_id');
            $table->string('revenue_code');
            $table->string('agency_code');
        });
    }
};
