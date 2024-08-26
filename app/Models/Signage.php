<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Model;

class Signage extends Model
{
    use HasFactory;

    protected $fillable = [
        'height_in_meters',
        'width_in_meters',
        'longitude',
        'latitude',
        'street_name',
        'street_number',
        'city',
        'local_government_area_id',
        'user_id',
        'notes',
        'added_by',
    ];

    //Belongs to local government area
    public function local_government_area()
    {
        return $this->belongsTo(LocalGovernmentArea::class);
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
