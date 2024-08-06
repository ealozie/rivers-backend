<?php

namespace App\Jobs;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessISWPaymentTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    private $requestObject;

    public function __construct($requestObject)
    {
        $this->requestObject = $requestObject;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //Check before creating.
        
        //TRANSACTION.COMPLETED
        if ($this->requestObject->event === 'TRANSACTION.COMPLETED') {
            logger('We finally made it in here');
            logger("UUID: ". $this->requestObject->uuid);
            $payment = new Payment();
            $payment->uuid = $this->requestObject->uuid;
            $payment->transaction_date = $this->requestObject->data->transactionDate;
            $payment->amount = $this->requestObject->data->amount;
            $payment->bank_code = $this->requestObject->data->bankCode;
            $payment->reference_number = $this->requestObject->data->paymentReference;
            $payment->payment_channel = $this->requestObject->data->channel;
            $payment->transaction_status = $this->requestObject->data->responseCode;
            $payment->transaction_response = $this->requestObject->data->responseDescription;
            $payment->retrieval_reference_number = $this->requestObject->data->retrievalReferenceNumber;
            $payment->payment_id = $this->requestObject->data->paymentId;
            $payment->merchant_customer_id = $this->requestObject->data->merchantCustomerId;
            $payment->merchant_reference = $this->requestObject->data->merchantReference;
            $payment->payer_name = $this->requestObject->data->merchantCustomerName;
            $payment->payment_gateway = "InterSwitch";
            $payment->save();
        }
    }
}
