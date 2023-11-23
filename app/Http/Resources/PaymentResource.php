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
            'transaction_id' => $this->transaction_id,
            'reference_number' => $this->reference_number,
            'transaction_date' => $this->transaction_date,
            'transaction_status' => $this->transaction_status,
            'transaction_response' => $this->transaction_response,
            'payer_name' => $this->payer_name,
            'merchant_reference' => $this->merchant_reference,
            'retrieval_reference_number' => $this->retrieval_reference_number,
            'payer_phone_number' => $this->payer_phone_number,
            'payer_address' => $this->payer_address,
            'receipt_number' => $this->receipt_number,
            'payment_gateway' => $this->payment_gateway,
            'amount' => $this->amount,
            'method' => $this->method,
            'payment_channel' => $this->payment_channel,
            'bank_name' => $this->bank_name,
            'deposit_slip_number' => $this->deposit_slip_number,
            'user' => $this->user->name ?? '',
        ];
    }
}
