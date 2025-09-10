<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ticket_agents', function (Blueprint $table) {
            $table->boolean('can_serve_notice')->default(false)->after('can_fund_wallet');
            $table->boolean('ticket_activity')->default(false)->after('can_serve_notice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_agents', function (Blueprint $table) {
            $table->dropColumn(['can_serve_notice', 'ticket_activity']);
        });
    }
};