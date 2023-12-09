<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Payment;
use App\Models\TicketAgent;
use App\Models\TicketAgentWallet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
        //$requestData1 = $request->all();
        $requestDataContent = $request->getContent();
        $setting = AppSetting::where('key', 'MONIFY_SECRET_KEY')->first();
        $secret_key = $setting->value;
        $signature = $_SERVER['HTTP_MONNIFY_SIGNATURE'];
        $logFile = fopen(storage_path('logs/isw_payment_webhook.log'), 'a');
        fwrite($logFile, $signature . "\n");
        fclose($logFile);
        //Log::info($signature);
        // $text = '{"eventData":{"product":{"reference":"ref_smcpt_5714820202369","type":"MOBILE_SDK"},"transactionReference":"MNFY|85|20231120233848|001348","paymentReference":"ref_smcpt_5714820202369","paidOn":"2023-11-20 23:38:55.163","paymentDescription":"","metaData":{},"paymentSourceInformation":[],"destinationAccountInformation":{},"amountPaid":100,"totalPayable":100,"cardDetails":{"last4":"1111","expMonth":"12","maskedPan":"411111******1111","expYear":"25","bin":"411111","reusable":false},"paymentMethod":"CARD","currency":"NGN","settlementAmount":"90.00","paymentStatus":"PAID","customer":{"name":"Joseph Nathaniel","email":"gibahjoe@gmail.com"}},"eventType":"SUCCESSFUL_TRANSACTION"}';
        ;
        //return $requestData['eventData']['transactionReference'];
        try {
        if ($signature) {
            $computed_signature = hash_hmac('sha512', $requestDataContent, $secret_key);
            $requestData = json_decode($requestDataContent, true);

                if ($computed_signature == $signature) {
                    //Working code
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

                // Add wallet information
                $ticket_agent_wallet = new TicketAgentWallet();
            $ticket_agent_wallet->ticket_agent_id = $ticket_agent->id;
            $ticket_agent_wallet->user_id = $payment->id;
            $ticket_agent_wallet->amount = $amount;
            $ticket_agent_wallet->transaction_type = 'credit';
            $ticket_agent_wallet->transaction_status = 'active';
            $ticket_agent_wallet->added_by = $payment->id;
            $ticket_agent_wallet->transaction_reference_number = $payment->transaction_id;
            $ticket_agent_wallet->save();
                //End wallet
            }
        }
        return response()->json();
        //Ends working code
                }
                 
        // $logFile = fopen(storage_path('logs/moni_payment_webhook.log'), 'a');
        // fwrite($logFile, $requestData . "\n");
        // fclose($logFile);
        // FacadesLog::info($requestData);
            
        //     if ($computed_signature == $signature) {
        //         $payment_ref = $requestData['eventData']['product']['reference'];
        // $payment = Payment::where('reference_number', $payment_ref)->first();
        // if ($payment) {
        //     $payment->transaction_id = $requestData['eventData']['transactionReference'];
        //     $payment->transaction_date = $requestData['eventData']['paidOn'];
        //     $payment->transaction_status = $requestData['eventData']['paymentStatus'];
        //     $payment->transaction_status = $requestData['eventData']['paymentStatus'];
        //     $payment->payer_name = $requestData['eventData']['customer']['name'];
        //     $payment->payer_address = $requestData['eventData']['customer']['email'];
        //     $payment->save();
        //     $ticket_agent = TicketAgent::where('user_id', $payment->user_id)->first();
        //         $amount = $requestData['eventData']['amountPaid'];
        //     if (!$payment->is_credited) {
        //         $ticket_agent->increment('wallet_balance', $amount);
        //         $payment->is_credited = true;
        //         $payment->save();
        //     }

        //}
            //}

        }

        } catch (Exception $e) {
        //     $logFile = fopen(storage_path('logs/monipont_payment_webhook.log'), 'a');
        // fwrite($logFile,  $requestData. "\n");
        // fclose($logFile);
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
        $base_url = 'https://api.monnify.com';
        $bank_list = '/api/v1/banks';
        $auth_endpoint = '/api/v1/auth/login';

        $settlement_endpoint = '/api/v2/disbursements/single';
        $api_key = 'MK_PROD_QVNTGFBC82';
        $client_secret = 'MRZUBUHKMBNY1LXGDKXLA8XYZFE9BJY6';
        $token = base64_encode("$api_key:$client_secret");
        $reference = 'ref_smcpt_'.mt_rand(11111, 99999).date('dY').mt_rand(11, 99);
        $login_response = Http::withHeaders([
           'Authorization' => "Basic $token", 
        ])->post($base_url.$auth_endpoint);
        if ($login_response->ok()) {
           $access_token = $login_response->object()->responseBody->accessToken;
           //  $bank_response = Http::withHeaders([
           // 'Authorization' => "Bearer $access_token", 
           //  ])->get($base_url.$bank_list);
            $transaction_response = Http::withHeaders([
            'Authorization' => "Bearer $access_token",
            ])->post("$base_url.$settlement_endpoint", [
            'amount' => 200,
            'reference' => $reference,
            'narrative' => 'Ticket',
            'destinationBankCode' => '50515',
            'destinationAccountNumber' => '5423959840',
            'currency' => 'NGN',
            'sourceAccountNumber' => '8049776383'
            ]);
            return $transaction_response->object();
            //return $bank_response->object();
        }
                

    }
}
