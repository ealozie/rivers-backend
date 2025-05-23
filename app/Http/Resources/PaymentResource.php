<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
            'payment_method' => $this->payment_method,
            'payment_log_id' => $this->payment_log_id,
            'customer_reference' => $this->customer_reference,
            'amount' => $this->amount,
            'payment_reference' => $this->payment_reference,
            'channel_name' => $this->channel_name,
            'location' => $this->location,
            'payment_date' => $this->payment_date,
            'institution' => $this->institution,
            'institution_name' => $this->institution_name,
            'branch_name' => $this->branch_name,
            'bank_name' => $this->bank_name,
            'customer_name' => $this->customer_name,
            'receipt_no' => $this->receipt_no,
            'item_name' => $this->item_name,
            'item_code' => $this->item_code,
            'lead_bank' => $this->lead_bank,
            'bank_code' => $this->bank_code,
            'customer_phone_number' => $this->customer_phone_number,
            'teller' => $this->teller,
            'created_at' => $this->created_at,
        ];
    }
}
