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


    public function added_by_user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket_categories()
    {
        return $this->hasMany(TicketAgentCategory::class);
    }
}
