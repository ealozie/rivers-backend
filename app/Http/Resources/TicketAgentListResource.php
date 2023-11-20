<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketAgentListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'agent_type' => $this->agent_type,
            'discount' => (int) $this->discount,
            'wallet_balance' => (double) $this->wallet_balance,
            'can_transfer_wallet_fund' => $this->can_tranfer_status($this->can_transfer_wallet_fund),
            'can_fund_wallet' => $this->can_fund_wallet_status($this->can_fund_wallet),
            'agent_status' => $this->agent_status,
            'user' => new UserResource($this->user),
            'agent_ticket_categories' => TicketAgentCategoryResource::collection($this->ticket_categories),
            'added_by' => $this->added_by_user->name ?? '',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function can_tranfer_status($status):bool
    {
        if ($status) {
            return true;
        } else {
            return false;
        }
    }
    public function can_fund_wallet_status($status):bool
    {
        if ($status) {
            return true;
        } else {
            return false;
        }
    }
}
