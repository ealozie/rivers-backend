<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;

class CommercialVehicle extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    //Belong to a vehicle category
    public function vehicle_category()
    {
        return $this->belongsTo(VehicleCategory::class);
    }

    //Belong to a vehicle manufacturer
    public function vehicle_manufacturer()
    {
        return $this->belongsTo(VehicleManufacturer::class);
    }

    //Belong to a vehicle model
    public function vehicle_model()
    {
        return $this->belongsTo(VehicleModel::class);
    }

    //Belongs to a ticket category
    public function ticket_category()
    {
        return $this->belongsTo(TicketCategory::class);
    }

    public function assessments(): MorphMany
    {
        return $this->morphMany(Assessment::class, 'assessmentable');
    }


}
