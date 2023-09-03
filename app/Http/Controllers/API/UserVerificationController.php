<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserVerificationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $validatedData = $request->validate(['registration_option' => 'required|in:nin,tin,bvn,no_id']);
        if ($validatedData['registration_option'] == 'nin') {
            //make a API calls to validate nin and prefill form with data
            return response()->json(['status' => 'error', 'message' => 'NIN validation is not yet implemented.'], 501);
        } else if ($validatedData['registration_option'] == 'tin') {
            //make a API calls to validate tin and prefill form with data
            return response()->json(['status' => 'error', 'message' => 'TIN validation is not yet implemented.'], 501);
        } else if ($validatedData['registration_option'] == 'bvn') {
            //make a API calls to validate bvn and prefill form with data
            return response()->json(['status' => 'error', 'message' => 'BVN validation is not yet implemented.'], 501);
        } else if ($validatedData['registration_option'] == 'no_id') {
            //make a API calls to validate no_id and prefill form with data
        }
    }
}
