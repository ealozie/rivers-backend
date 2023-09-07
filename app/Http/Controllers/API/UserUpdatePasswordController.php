<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @tags Password Update Service
 *
 * Authorization header is required to be set to Bearer `<token>`
 */
class UserUpdatePasswordController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request_data = $request->validate([
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|string|min:6'
        ]);
        if ($request_data['password_confirmation'] !== $request_data['password']) {
            return response()->json(['status' => 'error', 'message' => 'Password does not match'], 422);
        }
        $user = User::find($request->user()->id);
        $user->update(['password' => bcrypt($request_data['password'])]);
        return response()->json([
            'status' => 'success',
            'message' => 'Password has been successfully updated.'
        ]);
    }
}
