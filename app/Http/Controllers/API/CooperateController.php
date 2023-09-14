<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CooperateStoreRequest;
use App\Http\Requests\CooperateUpdateRequest;
use App\Http\Resources\CooperateResource;
use App\Http\Resources\UserResource;
use App\Models\Cooperate;
use App\Models\User;
use App\Traits\SendSMS;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * @tags Cooperate Registration Service
 */
class CooperateController extends Controller
{
    use SendSMS;
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
    public function store(CooperateStoreRequest $request)
    {
        $validatedData = $request->validated();

        try {
            //Validate the user Email address
            $api_key = "j7uIbrpMCgLbmiMSHBDNu";
            $email = $validatedData['email'];
            $url = "https://apps.emaillistverify.com/api/verifyEmail?secret=" . $api_key . "&email=" . $email;
            $response = Http::get($url);
            if ($response->failed()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Connection to email verification service failed',
                ], 500);
            }
            if ($response->body() != 'ok') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid email address',
                    'data' => $response->body(),
                ], 422);
            }
            //generate unique 10 digit number for user without repeating;
            $users = User::select('unique_id')->pluck('unique_id');
            $users_unique_ids = $users->toArray();
            $unique_id = random_int(1000000000, 9999999999);
            while (in_array($unique_id, $users_unique_ids)) {
                $unique_id = random_int(1000000000, 9999999999);
            }
            $user = new User();
            $user->name = $validatedData['business_name'];
            $user->email = $validatedData['email'];
            $user->email_verified_at = now();
            //Generate a random password
            $password = Str::Password(8);
            $user->phone_number = $validatedData['phone_number'];
            $user->role = 'cooperate';
            $user->password = Hash::make($password);
            $user->phone_number_verification_code =
                mt_rand(111111, 999999);
            $user->unique_id = $unique_id;
            $user->save();
            //Send Phone Number Verification Code
            $phone_number = $user->phone_number;
            $mobile_number = ltrim($phone_number, "0");
            $name = $validatedData['business_name'];
            $message = "Hello {$name},\nYour phone number verification code is " . $user->phone_number_verification_code;
            $this->send_sms_process_message("+234" . $mobile_number, $message);;
            $validatedData['user_id'] = $user->id;
            $validatedData['demand_notice_category_id'] = 0;
            if (isset($validatedData['picture_path']) && $request->hasFile('picture_path')) {
                $path = $request->file('picture_path')->store('cooperates', 'public');
                $validatedData['picture_path'] = "/storage/" . $path;
            }
            $cooperate = Cooperate::create($validatedData);
            $user->assignRole('cooperate');
            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => new UserResource($user),
                    'cooperate' => new CooperateResource($cooperate),
                    'password' => $password
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cooperate registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
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
    public function update(CooperateUpdateRequest $request, string $id)
    {
        try {
            $validatedData = $request->validated();
            if (isset($validatedData['picture_path']) && $request->hasFile('picture_path')) {
                $path = $request->file('picture_path')->store('cooperates', 'public');
                $validatedData['picture_path'] = "/storage/" . $path;
            }
            $cooperate = Cooperate::findOrFail($id);
            $cooperate->update($validatedData);
            return response()->json([
                'status' => 'success',
                'data' => new CooperateResource($cooperate),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cooperate update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
