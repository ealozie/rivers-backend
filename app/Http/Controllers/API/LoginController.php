<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @tags Authentication Service
 */
class LoginController extends Controller
{

    /**
     * Login.
     *
     * Allow user login into the application.
     */
    public function __invoke(Request $request)
    {
        $credentials = $request->validate([
            //'email' => ['required', 'email'],
            'phone_number' => ['required'],
            'password' => ['required'],
        ]);
        $credentials['status'] = 1;

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('igr_system_auth_token')->plainTextToken;
            $user->update([
                'last_login_at' => now()
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'User logged in successfully',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'user' => $user,
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }
    }
}
