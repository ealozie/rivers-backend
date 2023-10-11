<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketAgentWalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "ticket_agent" => new UserResource($this->user),
            "amount" => $this->amount,
            "transaction_type" => $this->transaction_type,
            "type" => $this->type,
            "transaction_reference_number" => $this->transaction_reference_number,
            "transaction_status" => $this->transaction_status,
            "transaction_by" => new UserResource($this->added_by_user),
            "beneficiary" => $this->beneficiary_id ? new UserResource($this->beneficiary) : '',
            "created_at" => $this->created_at,
        ];
    }
}
