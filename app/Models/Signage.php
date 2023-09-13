<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
}
