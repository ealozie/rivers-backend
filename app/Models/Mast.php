<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Mast extends Model
{
    use HasFactory;

    protected $fillable = [
        "mast_location",
        "property_id",
        "state_id",
        "local_government_area_id",
        "street_name",
        "street_number",
        "city",
        "mast_name",
        "mast_use",
        "owner_id",
        "connected_to_power",
        "connected_to_diesel_solar_power_generator",
        "longitude",
        "latitude",
        "note",
        "created_by",
        "approval_status",
        "mast_id",
    ];

    public function local_government_area()
    {
        return $this->belongsTo(LocalGovernmentArea::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, "owner_id");
    }

    public function pictures()
    {
        return $this->hasMany(MastPicture::class);
    }

    public function created_user()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function property()
    {
        return $this->belongsTo(Property::class, "property_id", "property_id");
    }

    public function assessments(): MorphMany
    {
        return $this->morphMany(Assessment::class, 'assessmentable');
    }

    public function account_manager(): MorphOne
    {
        return $this->morphOne(AccountManager::class, 'accountable');
    }
}
