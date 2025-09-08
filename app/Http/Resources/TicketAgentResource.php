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
            'agent_id' => $this->id,
            'agent_type' => $this->agent_type,
            'discount' => (int) $this->discount,
            'wallet_balance' => (float) $this->wallet_balance,
            'enable_shop' => (bool) $this->enable_shop ? true : false,
            'can_transfer_wallet_fund' => $this->can_tranfer_status($this->can_transfer_wallet_fund),
            'can_fund_wallet' => $this->can_fund_wallet_status($this->can_fund_wallet),
            'status' => $this->agent_status,
            'savings_balance' => $this->savings_balance,
            'account_type' => $this->account_type,
            'account_name' => $this->account_name,
            'can_validate_plate_number' => $this->can_validate_plate_number,
            'wema_reserve_account' => $this->wema_reserve_account,
            'moniepoint_reserve_account' => $this->moniepoint_reserve_account,
            'allow_reserved_account' => (bool) $this->allow_reserved_account ? true : false,
        ];
    }

    public function can_tranfer_status($status): bool
    {
        if ($status) {
            return true;
        } else {
            return false;
        }
    }

    public function can_fund_wallet_status($status)
    {
        if ($status) {
            return true;
        } else {
            return false;
        }
    }
}
