<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Model;

class Individual extends Model
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

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'property_id');
    }

    public function demand_notices(): MorphMany
    {
        return $this->morphMany(DemandNotice::class, 'demand_noticeable');
    }

    public function individual_notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function account_manager(): MorphOne
    {
        return $this->morphOne(AccountManager::class, 'accountable');
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class);
    }
    public function title()
    {
        return $this->belongsTo(Title::class);
    }

    public function marital_status()
    {
        return $this->belongsTo(MaritalStatus::class);
    }

    public function geno_type()
    {
        return $this->belongsTo(GenoType::class);
    }

    public function local_government_area()
    {
        return $this->belongsTo(LocalGovernmentArea::class);
    }

    public function residence_local_government_area()
    {
        return $this->belongsTo(LocalGovernmentArea::class, 'residence_local_government_area_id');
    }

    public function demand_notice_category()
    {
        return $this->belongsTo(DemandNoticeCategory::class);
    }

    public function occupation()
    {
        return $this->belongsTo(Occupation::class);
    }

    public function blood_group()
    {
        return $this->belongsTo(BloodGroup::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function residence_state()
    {
        return $this->belongsTo(State::class, 'residence_state_id');
    }

    public function assessments(): MorphMany
    {
        return $this->morphMany(Assessment::class, 'assessmentable');
    }
}
