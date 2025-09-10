<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DemandNoticeItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'demand_notice_id',
        'year_id',
        'revenue_item_id',
        'amount',
        'payment_status',
        'payment_receipt_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    //belongs to demand notice
    public function demandNotice()
    {
        return $this->belongsTo(DemandNotice::class);
    }

    //belongs to assessment year
    public function year()
    {
        return $this->belongsTo(AssessmentYear::class);
    }

    //belongs to revenue item
    public function revenueItem()
    {
        return $this->belongsTo(RevenueItem::class, 'revenue_item_id');
    }
}
