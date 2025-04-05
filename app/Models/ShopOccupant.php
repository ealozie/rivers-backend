<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopOccupant extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }

    public function occupant()
    {
        return $this->belongsTo(Individual::class, 'occupant_id', 'individual_id');
    }
}
