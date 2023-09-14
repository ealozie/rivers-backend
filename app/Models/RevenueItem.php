<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevenueItem extends Model
{
    use HasFactory;


    public function revenue_type()
    {
        return $this->belongsTo(RevenueType::class);
    }
}
