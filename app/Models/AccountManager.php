<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class AccountManager extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function accountable(): MorphTo
    {
        return $this->morphTo();
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function account_added_by()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
