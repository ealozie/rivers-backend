<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Residential extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'state_id', 'local_government_area_id', 'street_name', 'street_number', 'landmark', 'city'];
}
