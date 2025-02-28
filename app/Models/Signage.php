<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Model;

class Signage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function street()
    {
        return $this->belongsTo(Street::class);
    }

    //Belongs to local government area
    public function local_government_area()
    {
        return $this->belongsTo(LocalGovernmentArea::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    public function demand_notices(): MorphMany
    {
        return $this->morphMany(DemandNotice::class, 'demand_noticeable');
    }

    public function signage_notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function account_manager(): MorphOne
    {
        return $this->morphOne(AccountManager::class, 'accountable');
    }

    //Belongs to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Belongs to user
    public function added_by_user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function assessments(): MorphMany
    {
        return $this->morphMany(Assessment::class, 'assessmentable');
    }
}
