<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Cooperate;
use App\Models\Individual;
use App\Models\User;
use App\Traits\SendSMS;
use Illuminate\Http\Request;

/**
 * @tags User Enumeration (Individual & Cooperate) Verification Service
 */
class UserVerificationController extends Controller
{
    use SendSMS;
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
        $cooperate = Cooperate::where('phone_number', $validatedData['phone_number'])->first();
        if ($cooperate) {
            $user = User::find($cooperate->user_id);
        } else {
            $individual = Individual::where('phone_number', $validatedData['phone_number'])->first();
            if (!$individual) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Phone number not found.'
                ], 404);
            }
            $user = User::find($individual->user_id);
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

    /**
     * Phone Number Confirmation code Request.
     */
    public function phone_number_confirmation_code(Request $request, string $phone_number, ?string $account_type = null)
    {
        // if (!in_array($account_type, ['individual', 'cooperate'])) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Invalid Account type, must be individual or cooperate.'
        //     ], 200);
        // }
        // if ($account_type === 'cooperate') {
        //     $cooperate = Cooperate::where('phone_number', $phone_number)->first();
        //     if (!$cooperate) {
        //         return response()->json([
        //             'status' => 'error',
        //             'message' => 'Phone number not found.'
        //         ], 404);
        //     }
        //     $user = User::find($cooperate->user_id);
        // }
        // if ($account_type === 'individual') {
        //     $individual = Individual::where('phone_number', $phone_number)->first();
        //     if (!$individual) {
        //         return response()->json([
        //             'status' => 'error',
        //             'message' => 'Phone number not found.'
        //         ], 404);
        //     }
        //     $user = User::find($individual->user_id);
        // }
        $cooperate = Cooperate::where('phone_number', $phone_number)->first();
        if ($cooperate) {
            $user = User::find($cooperate->user_id);
            $phone_number = $cooperate->phone_number;
        } else {
            $individual = Individual::where('phone_number', $phone_number)->first();
            if (!$individual) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Phone number not found.'
                ], 404);
            }
            $user = User::find($individual->user_id);
            $phone_number = $individual->phone_number;
        }
        $name = $user->name;
        // $phone_number = $user->phone_number;
        $mobile_number = ltrim($phone_number, "0");
        $message = "Hello {$name},\nYour phone number verification code is " . $user->phone_number_verification_code;
        try {
            $this->send_sms_process_message("+234" . $mobile_number, $message);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Verification code has been successfully sent.',
        ], 200);
    }

    /**
     * Confirm Phone Number.
     */
    public function phone_number_confirmation_store(Request $request)
    {
        $validatedData = $request->validate([
            'phone_number' => 'required|min:11|max:11',
            'phone_number_verification_code' => 'required|min:6|max:6',
        ]);
        $cooperate = Cooperate::where('phone_number', $validatedData['phone_number'])->first();
        if ($cooperate) {
            $user = User::find($cooperate->user_id);
        } else {
            $individual = Individual::where('phone_number', $validatedData['phone_number'])->first();
            if (!$individual) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Phone number not found.'
                ], 404);
            }
            $user = User::find($individual->user_id);
        }
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
                'phone_number_verified_at' => now()
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
