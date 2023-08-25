<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use LaracraftTech\LaravelDateScopes\DateScopes;

class TicketVending extends Model
{
    use HasFactory, DateScopes;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ticket_category()
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id');
    }
}
