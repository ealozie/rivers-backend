<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketAgentResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Hash;
use App\Traits\UserAuthorizable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * @tags User Service
 */
class UserController extends Controller
{

    //use UserAuthorizable;

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
                'permissions' => array_map(function($item){
                    return $item['name'];
                }, $user->getPermissionsViaRoles()->toArray())
            ],
        ]);
    }

     /**
     * Display a List of Account Officers resource.
     */
    public function account_officers(Request $request)
    {
        $account_managers = User::role('account_officer')->get();
        return response()->json([
            'status' => 'success',
            'data' => [
                'users' => UserResource::collection($account_managers),
            ],
        ], 200);
    }

    

     /**
     * Advanced Search in resource.
     *
     * Query paramters `phone_number` or `email`.<br>
     * Additonal Query paramters `status`, `local_government_id`, `role`
     */
    public function search(Request $request)
    {
        $user_query = User::query();
        $user_query->when($request->has('email'), function ($query) use ($request) {
            $email = $request->get('email');
            return $query->where('email', 'like' ,"%{$email}%");
        });
        $user_query->when($request->has('phone_number'), function ($query) use ($request) {
            $phone_number = $request->get('phone_number');
            return $query->where('phone_number', 'like' ,"%{$phone_number}%");
        });
        $user_query->when($request->has('local_government_id'), function ($query) use ($request) {
            return $query->where('local_government_area_id', $request->get('local_government_id'));
        });
        $user_query->when($request->has('status'), function ($query) use ($request) {
            return $query->where('status', $request->get('status'));
        });
        $user_response = $user_query->get();

        if ($request->has('role')) {
            $user_response = User::role($request->get('role'))->get();
        }
        if (!isset($user_response)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request.'
            ]);
        }
        return UserResource::collection($user_response);
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
            'role' => 'required',
        ]);


        $user = new User();
        //$user->name = $validatedData['first_name'] . ' ' . $validatedData['surname'];
        $user->email = $validatedData['email'];
        $user->email_verified_at = now();
        $user->phone_number_verified_at = now();
        //Generate a random password
        $password = 123456;
        $user->phone_number = $validatedData['phone_number'];
        $user->role = $validatedData['role'];
        $user->status = 1;
        $user->password = Hash::make($password);
        $user->phone_number_verification_code =
            mt_rand(111111, 999999);
        $user_unique_id  = '9' . date('hi') . mt_rand(11111, 99999);
        $user->unique_id = $user_unique_id;
        $user->save();
        $user->assignRole($validatedData['role']);
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
     * Assign Permissions to User.
     */
    public function assign_permission(Request $request, string $user_id)
    {
        $validatedData = $request->validate([
            'permissions' => 'required|array|min:1',
        ]);

        $user = User::where('unique_id', $user_id)->first();
        if (!$user) {
            return response()->json([
            'status' => 'error',
            'message' => 'User not found.',
            'data' => [],
        ], 404);
        }
        $user->syncPermissions($validatedData['permissions']);
        return response()->json([
            'status' => 'success',
            'message' => 'Permission(s) has been assigned.',
            'data' => [
                'permissions' => $user->getDirectPermissions(),
            ],
        ]);
    }

    /**
     * Assign Role to User.
     */
    public function assign_role(Request $request, string $user_id)
    {
        $validatedData = $request->validate([
            'role' => 'required',
        ]);
        $user = User::where('unique_id', $user_id)->first();
        if (!$user) {
            return response()->json([
            'status' => 'error',
            'message' => 'User not found.',
            'data' => [],
        ], 404);
        }
        $user->syncRoles($validatedData['role']);
        return response()->json([
            'status' => 'success',
            'message' => 'Role(s) has been assigned.',
            'data' => [
                'roles' => $user->getDirectRoles(),
            ],
        ]);
    }

    /**
     * Revoke User's Permissions.
     */
    public function revoke_permission(Request $request, string $user_id)
    {

        $user = User::where('unique_id', $user_id)->first();
        if (!$user) {
            return response()->json([
            'status' => 'error',
            'message' => 'User not found.',
            'data' => [],
        ], 404);
        }
        $user->syncPermissions([]);

        return response()->json([
            'status' => 'success',
            'message' => 'Permission(s) has been revoke.',
            'data' => [
                'permissions' => $user->getDirectPermissions(),
            ],
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $requestData = $request->validate([
            'roles' => 'nullable|array|min:1',
            'mda_biller_id' => 'nullable',
            'local_government_area_id' => 'nullable|exists:local_government_areas,id',
        ]);
        $user = User::findOrFail($id);
        if (isset($requestData['roles']) && count($requestData['roles'])) {
            $user->syncRoles($requestData['roles']);
        }
        if (isset($requestData['mda_biller_id'])) {
            $user->mda_biller_id = $requestData['mda_biller_id'];
            $user->save();
        }
        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully.',
        ], 200);
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
