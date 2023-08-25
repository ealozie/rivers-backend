<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAgent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'added_by',
        'agent_type',
        'wallet_balance',
        'agent_status',
        'discount',
    ];
}
