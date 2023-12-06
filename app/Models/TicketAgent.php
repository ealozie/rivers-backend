<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TicketAgent extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'user_id',
        'added_by',
        'agent_type',
        'wallet_balance',
        'agent_status',
        'discount',
        'can_fund_wallet',
        'can_transfer_wallet_fund',
        'super_agent_id',
    ];


    public function added_by_user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function super_agent()
    {
        return $this->belongsTo(User::class, 'super_agent_id');
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
