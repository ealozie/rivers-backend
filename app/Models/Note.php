<?php

namespace App\Models;

use App\Models\Individual;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = ['id'];


    public function admin()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function noteable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user()
    {
        $user = Individual::where('user_id', $this->added_by)->first();
        return $user;
        //return $this->belongsTo(User::class, 'added_by');
    }
}
