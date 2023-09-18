<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketAgentResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @tags User Service
 */
class UserController extends Controller
{
    /**
     * Display a User resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => new UserResource($user),
                'agent' => new TicketAgentResource($user->agent)
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    /**
     * Verify a User via unique ID.
     */
    public function user_verification(Request $request)
    {
        $request->validate([
            'unique_id' => 'required',
        ]);
        $user = User::where('unique_id', $request->unique_id)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'User was successfully found.',
            'data' => [
                'user' => new UserResource($user),
            ],
        ]);
    }

    /**
     * Get User by Email or Phone Number.
     */
    public function email_phone_number(Request $request)
    {
        if ($request->has('email_phone_number')) {
            $email_phone_number = $request->get('email_phone_number');
            $user = User::where('email', $email_phone_number)
                ->orWhere('phone_number', $email_phone_number)
                ->first();
            return response()->json([
                'status' => 'success',
                'message' => 'User was successfully found.',
                'data' => [
                    'user' => new UserResource($user),
                ],
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Email or Phone Number is required',
            ], 422);
        }
    }
}
