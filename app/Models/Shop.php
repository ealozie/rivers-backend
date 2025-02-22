<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function business_category()
    {
        return $this->belongsTo(BusinessCategory::class);
    }

    public function street()
    {
        return $this->belongsTo(Street::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function shop_notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function demand_notices(): MorphMany
    {
        return $this->morphMany(DemandNotice::class, 'demand_noticeable');
    }

    public function account_manager(): MorphOne
    {
        return $this->morphOne(AccountManager::class, 'accountable');
    }

    public function business_sub_category()
    {
        return $this->belongsTo(BusinessSubCategory::class);
    }

    public function local_government_area()
    {
        return $this->belongsTo(LocalGovernmentArea::class);
    }

    public function classification()
    {
        return $this->belongsTo(Classification::class);
    }

    public function market_name()
    {
        return $this->belongsTo(MarketName::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assessments(): MorphMany
    {
        return $this->morphMany(Assessment::class, 'assessmentable');
    }
}
