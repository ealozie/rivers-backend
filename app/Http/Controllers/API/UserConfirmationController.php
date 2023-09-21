<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\SendSMS;
use Illuminate\Http\Request;

/**
 * @tags User Confirmation Service
 */
class UserConfirmationController extends Controller
{
    use SendSMS;

    /**
     * Confirm user Identity.
     */
    public function initial_user_identity_confirmation(Request $request)
    {
        $validatedData = $request->validate([
            'phone_number' => 'required|min:11|max:11',
        ]);

        $user = User::where('phone_number', $validatedData['phone_number'])->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Phone number not found.'
            ], 404);
        }

        try {
            $name = $user->name;
            $token = mt_rand(111111, 999999);
            $user->phone_number_verification_code = $token;
            $user->save();
            $mobile_number = ltrim($validatedData['phone_number'], "0");
            $message = "Hello {$name}, your confirmation token is " . $token . ". Thank you for using CIRES-IRS.";
            $this->send_sms_process_message("+234" . $mobile_number, $message);
            return response()->json([
                'status' => 'success',
                'message' => 'A confirmation token has been sent to your phone number. Please enter confirmation token to proceed with capturing.'
            ], 200);
        } catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing your request. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }


    public function user_identity_token_confirmation(Request $request)
    {
        $validatedData = $request->validate([
            'phone_number' => 'required|min:11|max:11',
            'comnfirmation_token' => 'required|min:6|max:6',
        ]);

        $user = User::where('phone_number', $validatedData['phone_number'])->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Phone number not found.'
            ], 404);
        }

        if ($user->phone_number_verification_code != $validatedData['comnfirmation_token']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid token provided.'
            ], 404);
        }

        $token = $user->createToken('igr_system_auth_token')->plainTextToken;
        $user->update([
                'last_login_at' => now()
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Token successfully confirmed.',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'user' => new UserResource($user),
                ]
            ], 200);
    }
}
