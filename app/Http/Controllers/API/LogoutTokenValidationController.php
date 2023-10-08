<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class LogoutTokenValidationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //get the token from the request
        $token = $request->bearerToken();
        $access_token = PersonalAccessToken::findToken($token);
        if (!$access_token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token not found.',
            ], 403);
        }
    }
}
