<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @tags Authentication Service
 */
class LogoutController extends Controller
{
    /**
     * Logout.
     *
     * Allow user logout of the application.
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
