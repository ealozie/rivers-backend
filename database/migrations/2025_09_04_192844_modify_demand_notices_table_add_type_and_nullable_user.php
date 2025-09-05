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
        Schema::table('demand_notices', function (Blueprint $table) {
            $table->string('demand_notice_type')->nullable()->after('demand_notice_category_id');
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demand_notices', function (Blueprint $table) {
            $table->dropColumn('demand_notice_type');
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
