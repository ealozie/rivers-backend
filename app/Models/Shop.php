<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function business_category()
    {
        return $this->belongsTo(BusinessCategory::class);
    }

    public function business_sub_category()
    {
        return $this->belongsTo(BusinessSubCategory::class);
    }

    public function local_government_area()
    {
        return $this->belongsTo(LocalGovernmentArea::class);
    }

    public function classification()
    {
        return $this->belongsTo(Classification::class);
    }

    public function market_name()
    {
        return $this->belongsTo(MarketName::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
