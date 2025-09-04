<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandNoticeItem extends Model
{
    use HasFactory;

    //belongs to agency
    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_code', 'agency_code');
    }

    //belongs to revenue
    public function revenueItem()
    {
        return $this->belongsTo(RevenueItem::class,'revenue_item_id');
    }
    

}
