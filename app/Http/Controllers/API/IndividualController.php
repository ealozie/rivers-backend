<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndividualStoreRequest;
use App\Http\Requests\IndividualUpdateRequest;
use App\Http\Resources\IndividualResource;
use App\Http\Resources\UserResource;
use App\Models\Individual;
use App\Models\User;
use App\Traits\SendSMS;
use Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * @tags Individual Registration Service
 */
class IndividualController extends Controller
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
    public function store(IndividualStoreRequest $request)
    {
        $validatedData = $request->validated();

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
        $user->name = $validatedData['first_name'] . ' ' . $validatedData['surname'];
        $user->email = $validatedData['email'];
        $user->email_verified_at = now();
        //Generate a random password
        $password = Str::Password(8);
        $user->phone_number = $validatedData['phone_number'];
        $user->role = 'individual';
        $user->password = Hash::make($password);
        $user->phone_number_verification_code =
            mt_rand(111111, 999999);
        $user->unique_id = $unique_id;
        $user->save();
        //Send Phone Number Verification Code
        $phone_number = $user->phone_number;
        $mobile_number = ltrim($phone_number, "0");
        $name = $validatedData['first_name'];
        $message = "Hello {$name},\nYour phone number verification code is " . $user->phone_number_verification_code;
        $this->send_sms_process_message("+234" . $mobile_number, $message);;
        $validatedData['user_id'] = $user->id;
        $validatedData['demand_notice_category_id'] = 0;
        $individual = Individual::create($validatedData);
        $user->assignRole('individual');
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => new UserResource($user),
                'individual' => new IndividualResource($individual),
                'password' => $password
            ]
        ]);
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
    public function update(IndividualUpdateRequest $request, string $id)
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
}
