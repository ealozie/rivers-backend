<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function street()
    {
        return $this->belongsTo(Street::class);
    }

    public function demand_notices(): MorphMany
    {
        return $this->morphMany(DemandNotice::class, 'demand_noticeable');
    }

    public function propertyCategory()
    {
        return $this->belongsTo(PropertyCategory::class);
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function propertyUse()
    {
        return $this->belongsTo(PropertyUse::class);
    }

    public function propertyPictures()
    {
        return $this->hasMany(PropertyPicture::class);
    }

    public function demandNoticeCategory()
    {
        return $this->belongsTo(DemandNoticeCategory::class);
    }

    public function local_government_area()
    {
        return $this->belongsTo(LocalGovernmentArea::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function assessments(): MorphMany
    {
        return $this->morphMany(Assessment::class, 'assessmentable');
    }

    public function property_notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function account_manager(): MorphOne
    {
        return $this->morphOne(AccountManager::class, 'accountable');
    }

}
