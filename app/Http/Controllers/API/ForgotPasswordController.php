<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetCode;
use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated_data = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $validated_data['confirmation_code'] = mt_rand(100000, 999999);
        $password_reset_code = PasswordResetCode::create($validated_data);

        $user = User::where('email', $request->email)->first();
        $user->notify(new PasswordResetNotification($password_reset_code, $user));
        return response(['message' => trans('passwords.sent'), 'user_id' => $user->id], 200);
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
        $user = User::where('email', $password_code->email)->first();
        $user->update(bcrypt($request_data['password']));
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
