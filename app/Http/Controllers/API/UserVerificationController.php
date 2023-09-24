<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @tags User Enumeration (Individual & Cooperate) Verification Service
 */
class UserVerificationController extends Controller
{
    /**
     * Initial Registration request.
     */
    public function initial_registration_request(Request $request)
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

    /**
     * Phone Number Verification Request.
     */
    public function user_phone_number_confirmation(Request $request)
    {
        $validatedData = $request->validate([
            'phone_number' => 'required|min:11|max:11',
            'phone_number_verification_code' => 'required|min:6|max:6',
        ]);

        $user = User::where('phone_number', $validatedData['phone_number'])->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Phone number not found.'
            ], 404);
        }

        if ($user->phone_number_verification_code != $validatedData['phone_number_verification_code']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid token provided.'
            ], 404);
        }

        $user->update([
                'last_login_at' => now()
            ]);
        $token = $user->createToken('igr_system_auth_token')->plainTextToken;
            return response()->json([
                'status' => 'success',
                'message' => 'Phone Number has been successfully verified.',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'user' => new UserResource($user),
                ]
            ], 200);
    }
}
