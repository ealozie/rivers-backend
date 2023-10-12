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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * @tags Individual Registration Service
 */
class IndividualController extends Controller
{
    use SendSMS;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->only('index');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $per_page = 20;
        if ($user->hasRole('admin')) {
            $individual_registrations = Individual::with('user')->paginate($per_page);
            return IndividualResource::collection($individual_registrations);
        }
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

        $user = new User();
        $user->name = $validatedData['first_name'] . ' ' . $validatedData['surname'];
        $user->email = $validatedData['email'];
        $user->email_verified_at = now();
        //Generate a random password
        $password = 123456;
        $user->phone_number = $validatedData['phone_number'];
        $user->role = 'individual';
        $user->status = 0;
        $user->password = Hash::make($password);
        $user->phone_number_verification_code =
            mt_rand(111111, 999999);
        $user->save();
        //$user->unique_id = time() + $user->id + mt_rand(11111, 99999);
        //$user->save();
        //Send Phone Number Verification Code
        $phone_number = $user->phone_number;
        $mobile_number = ltrim($phone_number, "0");
        $name = $validatedData['first_name'];
        $message = "Hello {$name},\nYour phone number verification code is " . $user->phone_number_verification_code;
        $this->send_sms_process_message("+234" . $mobile_number, $message);;
        $validatedData['user_id'] = $user->id;
        $validatedData['demand_notice_category_id'] = 0;

        $token = $user->createToken('igr_system_auth_token')->plainTextToken;
        $validatedData['email_address'] = $validatedData['email'];
        $individual = Individual::create($validatedData);
        $user->assignRole('individual');
        return response()->json([
            'status' => 'success',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
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
        $individual = Individual::find($id);
        if (!$individual) {
            return response()->json([
                'status' => 'error',
                'message' => 'Individual not found',
            ], 404);
        }
        return new IndividualResource($individual);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IndividualUpdateRequest $request, string $id)
    {
        $validatedData = $request->validated();
        $individual = Individual::find($id);
        $user = User::where('id', $individual->user_id)->first();
        $user->name = $validatedData['first_name'] . " " . $validatedData['middle_name'] . " " . $validatedData['surname'];
        $user->save();
        $individual->update($validatedData);
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => new UserResource($user),
                'individual' => new IndividualResource($individual),
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
