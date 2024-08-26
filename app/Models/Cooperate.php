<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Model;

class Cooperate extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cooperate_notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function account_manager(): MorphOne
    {
        return $this->morphOne(AccountManager::class, 'accountable');
    }

    public function business_type()
    {
        return $this->belongsTo(BusinessType::class);
    }

    public function local_government_area()
    {
        return $this->belongsTo(LocalGovernmentArea::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function settlement_type()
    {
        return $this->belongsTo(SettlementType::class);
    }

    public function business_category()
    {
        return $this->belongsTo(BusinessCategory::class);
    }

    public function business_sub_category()
    {
        return $this->belongsTo(BusinessSubCategory::class);
    }

    public function business_level()
    {
        return $this->belongsTo(BusinessLevel::class);
    }
    public function demand_notice_category()
    {
        return $this->belongsTo(DemandNoticeCategory::class);
    }

    public function assessments(): MorphMany
    {
        return $this->morphMany(Assessment::class, 'assessmentable');
    }
}
