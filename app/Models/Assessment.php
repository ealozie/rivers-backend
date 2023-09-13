<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone_number',
        'contact_address',
        'amount',
        'assessment_reference',
        'assessment_year_id',
        'status',
        'payment_status',
        'added_by',
    ];
}
