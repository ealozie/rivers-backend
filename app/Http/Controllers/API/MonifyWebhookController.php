<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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
        $requestData2 = $request->getContent();
        //Log data
        $logFile = fopen(storage_path('logs/monify_payment_webhook1.log'), 'a');
        fwrite($logFile, $requestData2 . "\n");
        fclose($logFile);

        $logFile1 = fopen(storage_path('logs/monify_payment_webhook2.log'), 'a');
        fwrite($logFile1, $requestData2 . "\n");
        fclose($logFile1);

        $setting = AppSetting::where('key', 'MONIFY_SECRET_KEY')->first();
        $secret_key = $setting->value;
        $signature1 = $_SERVER['HTTP_MONNIFY_SIGNATURE'];
        $signature2 = $request->header('HTTP_MONNIFY_SIGNATURE');
        $logFile3 = fopen(storage_path('logs/monify_payment_webhook3.log'), 'a');
        fwrite($logFile3, $signature1 . "\n");
        fclose($logFile3);
        $logFile4 = fopen(storage_path('logs/monify_payment_webhook4.log'), 'a');
        fwrite($logFile4, $signature2 . "\n");
        fclose($logFile4);
        // if ($request->hasHeader('HTTP_MONNIFY_SIGNATURE')) {
        //     $computed_signature = hash_hmac('sha512', $requestData, $secret_key);
        //     if ($computed_signature == $signature) {
        //         $requestObject = json_decode($requestData);
        //         ProcessISWPaymentTransaction::dispatch($requestObject);
        //         return response()->json();
        //     }

        // }
        return response()->json();
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
