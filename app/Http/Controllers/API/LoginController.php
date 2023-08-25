<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('igr_system_auth_token')->plainTextToken;
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
