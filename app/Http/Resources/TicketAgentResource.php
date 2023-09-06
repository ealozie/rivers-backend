<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketAgentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'agent_type' => $this->agent_type,
            'discount' => (int) $this->discount,
            'wallet_balance' => (double) $this->wallet_balance,
            'can_transfer_wallet_fund' => $this->can_transfer_wallet_fund,
            'status' => $this->agent_status,
        ];
    }
}
