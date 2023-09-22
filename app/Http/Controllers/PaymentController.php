<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentResource;
use App\Models\Payment;
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
}
