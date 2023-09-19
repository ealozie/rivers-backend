<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Individual extends Model
{
    use HasFactory;
    protected $guarded = ['password'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
