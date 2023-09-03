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
        Schema::create('individuals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('bvn')->nullable();
            $table->string('nin')->nullable();
            $table->string('tin')->nullable();
            $table->string('registration_option');
            $table->foreignId('nationality_id');
            $table->foreignId('title_id');
            $table->string('surname');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('gender');
            $table->foreignId('marital_status_id');
            $table->integer('number_of_kids')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->foreignId('blood_group_id');
            $table->foreignId('geno_type_id');
            $table->foreignId('state_id');
            $table->foreignId('local_government_area_id');
            $table->foreignId('occupation_id');
            $table->double('income_range')->nullable();
            $table->foreignId('demand_notice_category_id')->nullable();
            $table->string('property_abssin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('individuals');
    }
};
