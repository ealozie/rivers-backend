<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Street extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function local_government_area()
    {
        return $this->belongsTo(LocalGovernmentArea::class);
    }
}
