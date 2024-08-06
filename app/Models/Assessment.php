<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
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
        'revenue_item_id',
        'agency_id',
        'entity_id',
        'due_date',
    ];


    public function assessment_year()
    {
        return $this->belongsTo(AssessmentYear::class);
    }

    public function added_by_user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function revenue_item()
    {
        return $this->belongsTo(RevenueItem::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function assessmentable(): MorphTo
    {
        return $this->morphTo();
    }
}
