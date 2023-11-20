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
        fwrite($logFile, $requestData1 . "\n");
        fclose($logFile);
        $logFile = fopen(storage_path('logs/monify_payment_webhook2.log'), 'a');
        fwrite($logFile, $requestData2 . "\n");
        fclose($logFile);
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
