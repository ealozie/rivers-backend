<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = ['id'];


    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
