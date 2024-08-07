<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Jobs\ProcessISWPaymentTransaction;
use App\Models\Payment;
use App\Models\User;
use AWS\CRT\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as FacadesLog;

/**
 * @tags Payment Service
 */
class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::all();
        return PaymentResource::collection($payments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $requestData = $request->all();
        $requestData['user_id'] = $request->user()->id;
        Payment::create($requestData);
        return response()->json([
            'status' => 'success',
            'message' => 'Payment logged successfully.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Get Payments by User ID or User Unique ID.
     *
     * Additional Query parameter are `limit` and `offset`. <br>
     */
    public function show_by_user_id(Request $request, string $user_id_or_unique_id)
    {
        $limit = 10;
        $offset = 0;

        if ($request->has('limit')) {
            $limit = $request->get('limit');
        }
        if ($request->has('offset')) {
            $offset = $request->get('offset');
        }

        $user = User::where('unique_id', $user_id_or_unique_id)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User ID not found.',
            ], 404);
        }
        $payments = Payment::where('user_id', $user->id)->latest()->offset($offset)->limit($limit)->get();
        if (!count($payments)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment not found.',
            ], 404);
        }
        $total_payments = Payment::where('user_id', $user->id)->count();
        return response()->json([
            'status' => 'success',
            'message' => 'Payments retrieved successfully.',
            'total_number_of_records' => (int) $total_payments,
            'data' => PaymentResource::collection($payments)
        ]);
    }

    /**
     * Get Payment By Reference or Receipt number.
     */
    public function show_by_reference_number(string $reference_number)
    {
        $payment = Payment::where('reference_number', $reference_number)->orWhere('receipt_number', $reference_number)->first();
        if (!$payment) {
            return response()->json(['status' => 'error', 'message' => 'Payment not found',], 404);
        }
        return new PaymentResource($payment);
    }

    /**
     * InterSwitch Payment Webhook.
     */
    public function payment_webhoook_for_wallet(Request $request)
    {
        $requestData = $request->getContent();
        //Log data
        $logFile = fopen(storage_path('logs/isw_payment_webhook.log'), 'a');
        fwrite($logFile, $requestData . "\n");
        fclose($logFile);
        FacadesLog::info($requestData);
        if ($request->hasHeader('X-Interswitch-Signature')) {
            $secret_key = env('ISW_WEBHOOK_PAYMENT_SECRET_KEY');
            $signature = hash_hmac('sha512', $requestData, $secret_key);
            //verify this signature with the one sent in the header
            if ($signature == $request->header('X-Interswitch-Signature')) {
                $requestObject = json_decode($requestData);
                FacadesLog::info('We arrived here');
                ProcessISWPaymentTransaction::dispatch($requestObject);
                FacadesLog::info('We Are good to move from here');
                return response()->json();
            }
        }
        
    }

    /**
     * Generate Reference number for InterSwitch Payment.
     */
    public function payment_generate_reference(Request $request)
    {
        $validateData = $request->validate([
            'amount' => 'required',
            'payment_gateway' => 'required',
        ]);
        try {
            $payment = new Payment();
            $payment->reference_number = 'ref_smcpt_'.mt_rand(11111, 99999).date('dY').mt_rand(11, 99);
            $payment->payment_gateway = $validateData['payment_gateway'];
            $payment->amount = $validateData['amount'];
            $payment->save();
            return response()->json([
            'status' => 'success',
            'data' => [
                'payment_reference_number' => $payment->reference_number,
            ]
        ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing your request. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify InterSwitch Payment using Reference number.
     * 
     * Query parameter `ref_number` is required.<br>
     * Authentication Token is required.
     */

    public function payment_reference_verification(Request $request)
    {
        if ($request->has('ref_number')) {
            $reference_number = $request->get('ref_number');
            $payment = Payment::where('reference_number', $reference_number)->first();
            if (!$payment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Reference number not found'
                ], 404);
            }
            if ($payment->transaction_status == '00') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Transaction was succesful.'
                ]);
            } else if ($payment->transaction_status == 'PAID') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Transaction was succesful.'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction status cannot be determined.'
                ], 404);
            }
        } else {
            return response()->json([
                    'status' => 'error',
                    'message' => 'ref_number query is required'
                ], 404);
        }
    }

    /**
     * Advanced Search in resource.
     *
     * Query paramters `payer_name` or `reference_number`.<br>
     * Additonal Query paramters `transaction_id`, `retrieval_reference_number`, `payer_phone_number`, `receipt_number`, `payment_channel`, `date_from and date_to`
     */
    public function search(Request $request)
    {
        $per_page = 20;
        
        if ($request->has('payer_name')) {
            $query_request = $request->get('payer_name');
            $response = Payment::where('payer_name', 'like', "%$query_request%")->paginate($per_page);
        }
        if ($request->has('reference_number')) {
            $query_request = $request->get('reference_number');
            $response = Payment::where('reference_number', $query_request)->paginate($per_page);
        }
        if ($request->has('transaction_id')) {
            $query_request = $request->get('transaction_id');
            $response = Payment::where('transaction_id', $query_request)->paginate($per_page);
        }
        if ($request->has('retrieval_reference_number')) {
            $query_request = $request->get('retrieval_reference_number');
            $response = Payment::where('retrieval_reference_number', $query_request)->paginate($per_page);
        }
        if ($request->has('payer_phone_number')) {
            $query_request = $request->get('payer_phone_number');
            $response = Payment::where('payer_phone_number', $query_request)->paginate($per_page);
        }
        if ($request->has('receipt_number')) {
            $query_request = $request->get('receipt_number');
            $response = Payment::where('receipt_number', $query_request)->paginate($per_page);
        }
        if ($request->has('payment_channel')) {
            $query_request = $request->get('payment_channel');
            $response = Payment::where('payment_channel', $query_request)->paginate($per_page);
        }        
        if ($request->has('date_from') && $request->has('date_to')) {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            $response = Payment::whereBetween('created_at', [$date_from, $date_to])->paginate($per_page);
        }
        if (!isset($response)){
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request.'
            ]);
        }
        return PaymentResource::collection($response);;
    }
}
