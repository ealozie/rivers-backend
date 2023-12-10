<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketAgentResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
        if ($user->hasRole('admin')) {
            $users = User::all();
            return response()->json([
                'status' => 'success',
                'data' => [
                    'users' => UserResource::collection($users),
                ],
            ]);
        }
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => new UserResource($user),
                'agent' => $user->agent ? new TicketAgentResource($user->agent) : ''
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required',
            'surname' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|unique:users,phone_number',
            'local_government_area_id' => 'required|exists:local_government_areas,id',
        ]);
       

        $user = new User();
        $user->name = $validatedData['first_name'] . ' ' . $validatedData['surname'];
        $user->email = $validatedData['email'];
        $user->email_verified_at = now();
        $user->phone_number_verified_at = now();
        //Generate a random password
        $password = 123456;
        $user->phone_number = $validatedData['phone_number'];
        $user->role = 'individual';
        $user->status = 1;
        $user->password = Hash::make($password);
        $user->phone_number_verification_code =
            mt_rand(111111, 999999);
        $user_unique_id  = '9' . date('hi') . mt_rand(11111, 99999);
        $user->unique_id = $user_unique_id;
        $user->save();
        return response()->json([
            'status' => 'success',
            'message' => 'User successfully created.',
            'data' => $user,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $user = User::find($id);
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => new UserResource($user),
                'agent' => $user->agent ? new TicketAgentResource($user->agent) : ''
            ],
        ]);
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
     * 
     * Query parameter is `email_phone_number` `e.g: http://igr-system.test/api/v1/user/email-phone-number?email_phone_number=08034325697`
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
