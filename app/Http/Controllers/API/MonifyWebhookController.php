<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Payment;
use App\Models\TicketAgent;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @tags Monify Webhook Service
 */
class MonifyWebhookController extends Controller
{
    /**
     * Transaction Completion.
     */
    public function transaction_completion(Request $request)
    {
        $requestData1 = $request->all();
        $requestDataContent = $request->getContent();
        $setting = AppSetting::where('key', 'MONIFY_SECRET_KEY')->first();
        $secret_key = $setting->value;
        $signature = $_SERVER['HTTP_MONNIFY_SIGNATURE'];
        
        //Log::info($signature);
        try {
        if ($signature) {
            $computed_signature = hash_hmac('sha512', $requestDataContent, $secret_key);
            $requestData = $requestDataContent;
        //     $logFile = fopen(storage_path('logs/monipoint_payment_webhook.log'), 'a');
        // fwrite($logFile, $computed_signature . "\n");
        // fclose($logFile);
        $logFile = fopen(storage_path('logs/monipoint_payment_webhook.log'), 'a');
        fwrite($logFile, $requestData1 . "\n");
        fclose($logFile);
        $logFile1 = fopen(storage_path('logs/monipoint_data_payment_webhook.log'), 'a');
        fwrite($logFile1, $requestData->eventData->product . "\n");
        fclose($logFile1);
            if ($computed_signature == $signature) {
                $payment_ref = $requestData['eventData']['product']['reference'];
        $payment = Payment::where('reference_number', $payment_ref)->first();
        if ($payment) {
            $payment->transaction_id = $requestData['eventData']['transactionReference'];
            $payment->transaction_date = $requestData['eventData']['paidOn'];
            $payment->transaction_status = $requestData['eventData']['paymentStatus'];
            $payment->transaction_status = $requestData['eventData']['paymentStatus'];
            $payment->payer_name = $requestData['eventData']['customer']['name'];
            $payment->payer_address = $requestData['eventData']['customer']['email'];
            $payment->save();
            $ticket_agent = TicketAgent::where('user_id', $payment->user_id)->first();
                $amount = $requestData['eventData']['amountPaid'];
            if (!$payment->is_credited) {
                $ticket_agent->increment('wallet_balance', $amount);
                $payment->is_credited = true;
                $payment->save();
            }

        }
                return response()->json([], 200);
            }

        }

        } catch (Exception $e) {
            $logFile = fopen(storage_path('logs/monipont_payment_webhook.log'), 'a');
        fwrite($logFile,  $requestData. "\n");
        fclose($logFile);
        //FacadesLog::info($requestData);
        }
        //$signature2 = $request->header('HTTP_MONNIFY_SIGNATURE');
        
        //return json_decode($requestData, true);
        //return response()->json();
        //$computedHash = hash_hmac('sha512', $raw_request, $SECRET_KEY);
        //FacadesLog::info($requestData);
    }

    /**
     * Refund Completion.
     */
    public function refund_completion(Request $request)
    {
        // code...
    }

    /**
     * Disbursement.
     */
    public function disbursement(Request $request)
    {
        // code...
    }

    /**
     * Settlement.
     */
    public function settlement(Request $request)
    {
        // code...
    }
}
