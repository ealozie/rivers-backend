<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Jobs\ProcessISWPaymentTransaction;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

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
     */
    public function show_by_user_id(string $user_id_or_unique_id)
    {
        $user = User::where('id', $user_id_or_unique_id)->orWhere('unique_id', $user_id_or_unique_id)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User ID not found.',
            ], 404);
        }
        $property = Payment::where('user_id', $user->id)->get();
        if (!count($property)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment not found.',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Payments retrieved successfully.',
            'data' => PaymentResource::collection($property)
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

    public function payment_webhoook_for_wallet(Request $request)
    {
        $requestData = $request->getContent();
        if ($request->hasHeader('X-Interswitch-Signature')) {
            $secret_key = env('ISW_WEBHOOK_PAYMENT_SECRET_KEY');
            $signature = hash_hmac('sha256', $requestData, $secret_key);
            //verify this signature with the one sent in the header
            if ($signature == $request->header('X-Interswitch-Signature')) {
                $requestObject = json_decode($requestData);
                ProcessISWPaymentTransaction::dispatch($requestObject);
            }
        }
        return response()->json();
    }
}
