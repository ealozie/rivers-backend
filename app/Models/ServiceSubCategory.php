<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceSubCategory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    public function service_category()
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function service_provider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }
}
