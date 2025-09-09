<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentHistoryResource extends JsonResource
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
            'payment_date' => $this->payment_date,
            'payment_status' => $this->payment_status,
            'payment_reference' => $this->payment_reference,
            'customer_name' => $this->customer_name,
            'customer_reference' => $this->customer_reference,
            'receipt_number' => $this->receipt_no,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'item_name' => $this->item_name,
            'item_code' => $this->item_code,
        ];
    }
}
