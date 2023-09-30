<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleManufacturer extends Model
{
    use HasFactory;

    //vehicle manufacturer has many vehicle models
    public function vehicle_models()
    {
        return $this->hasMany(VehicleModel::class);
    }
}
