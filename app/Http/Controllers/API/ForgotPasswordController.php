<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetCode;
use App\Models\User;
use App\Notifications\PasswordResetNotification;
use App\Traits\SendSMS;
use Illuminate\Http\Request;

/**
 * @tags Authentication Service
 */
class ForgotPasswordController extends Controller
{
    use SendSMS;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Initiate Password Reset Request.
     *
     * Allow user reset password in the application.
     */
    public function store(Request $request)
    {
        $validated_data = $request->validate([
            'phone_number' => 'required|exists:users,phone_number',
        ]);

        try {
            $user = User::where('phone_number', $request->phone_number)->first();
            $validated_data['email'] = $user->email;
            $validated_data['confirmation_code'] = mt_rand(100000, 999999);
            $password_reset_code = PasswordResetCode::create($validated_data);

            //Send SMS
            $message = "Hi {$user->name},\nYour password reset code is {$password_reset_code->confirmation_code}.";
            $mobile_number = ltrim($user->phone_number, "0");
            $this->send_sms_process_message("+234" . $mobile_number, $message);

            $user->notify(new PasswordResetNotification($password_reset_code, $user));

            return response()->json([
                'status' => 'success',
                'message' => 'Password reset code has been sent to your phone number and email address.',
                'data' => [
                    'user_id' => $user->id,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password reset code could not be sent, please try again.',
                'data' => [
                    'error' => $e->getMessage(),
                ]
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update Password.
     *
     * Allow user update password in the application.
     */
    public function update(Request $request, string $id)
    {
        $request_data = $request->validate([
            'confirmation_code' => 'required|string|exists:password_reset_codes',
            'password' => 'required|string|min:6|confirmed',
        ]);
        $password_code = PasswordResetCode::where('confirmation_code', $request_data['confirmation_code'])->where('is_active', true)->first();
        if (!$password_code) {
            return response()->json([
                'status' => 'error',
                'message' => 'Confirmation code has expired, please request again.'
            ], 404);
        }
        $password_code->is_active = false;
        $password_code->save();
        $user = User::where('phone_number', $password_code->phone_number)->first();
        $user->update(['password' => bcrypt($request_data['password'])]);
        return response()->json([
            'status' => 'success',
            'message' => 'Password has been successfully reset.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
