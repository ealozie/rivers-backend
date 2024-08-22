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
        Schema::create('cooperates', function (Blueprint $table) {
            $table->id();
            $table->string('rc_number')->unique();
            $table->string('business_name');
            $table->foreignId('business_type_id');
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->string('year_of_registration')->nullabe();
            $table->date('date_of_business_commencement')->nullable();
            $table->string('website')->nullable();
            $table->string('tin_number')->nullable();
            $table->foreignId('settlement_type_id');
            $table->foreignId('business_category_id');
            $table->foreignId('business_sub_category_id');
            $table->foreignId('business_level_id');
            $table->foreignId('demand_notice_category_id');
            $table->integer('number_of_staff');
            $table->string('monthly_turnover', 50);
            $table->string('phone_number', 50);
            $table->string('picture_path')->nullable();
            $table->boolean('has_signage')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('user_id');
            $table->foreignId('added_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cooperates');
    }
};
