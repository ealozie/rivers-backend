<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandNoticeCategoryItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    public function demand_notice_category()
    {
        return $this->belongsTo(DemandNoticeCategory::class);
    }

    public function revenue_item()
    {
        return $this->belongsTo(RevenueItem::class, 'revenue_item_id');
    }

    public function added_by_user()
    {
        return $this->belongsTo(User::class, 'added_by', 'id');
    }
}
