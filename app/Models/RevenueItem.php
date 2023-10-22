<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevenueItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public function revenue_type()
    {
        return $this->belongsTo(RevenueType::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
