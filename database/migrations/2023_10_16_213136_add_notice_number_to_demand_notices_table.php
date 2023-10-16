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
            $table->string('demand_notice_number', 15)->after('id')->nullable();
            $table->date('enforcement_begins_at')->after('date_served')->nullable();
            $table->foreignId('demand_notice_category_id')->after('id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demand_notices', function (Blueprint $table) {
            //
        });
    }
};
