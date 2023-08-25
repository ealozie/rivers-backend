<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAgentCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_agent_id',
        'ticket_category_id',
        'discount',
        'added_by',
        'status',
    ];

    public function ticket_category()
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id');
    }
}
