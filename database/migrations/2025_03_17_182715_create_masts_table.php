<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    /*
    1. sn
2. Standalone/Property
3. PropertyID (if 2 is property)
4. State
5. LGA
6. Street Name
7. Street Number
8. City
9. Mast Name
10. Mast Use (Telco/TV/Radio/Security/Others)
11. OwnerID (nor mandatory)
12. Connected to power (yes/no)
13. Connected to a diesel/solar power generator (yes/no)
14. Longitude
15. Latitude
16. Pictures (3 with one mandatory)
17. Note
    */
    public function up(): void
    {
        Schema::create('masts', function (Blueprint $table) {
            $table->id();
            $table->string('mast_location');
            $table->string('property_id')->nullable();
            $table->foreignId('state_id')->nullable();
            $table->foreignId('local_government_area_id')->nullable();
            $table->string('street_name')->nullable();
            $table->string('street_number')->nullable();
            $table->string('city')->nullable();
            $table->string('mast_name');
            $table->string('mast_use');
            $table->foreignId('owner_id')->nullable();
            $table->boolean('connected_to_power')->default(false);
            $table->boolean('connected_to_diesel_solar_power_generator')->default(false);
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masts');
    }
};
