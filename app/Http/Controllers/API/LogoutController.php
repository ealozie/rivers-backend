<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //Logout an authenticated user
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'User logged out successfully',
        ], 200);
    }
}
