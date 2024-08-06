<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\TicketAgent;
use App\Models\User;
use App\Traits\SendSMS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @tags Authentication Service
 */
class LoginController extends Controller
{
    use SendSMS;

    /**
     * Login.
     *
     * Allow user login into the application.
     */
    public function __invoke(Request $request)
    {
        // $message = "Hello Emmanuel, this is a test sms. Let me know if your received it. Thank you";
        // $this->send_sms_process_message("+2348036494588", $message);

        $credentials = $request->validate([
            'phone_number' => ['required'],
            'password' => ['required'],
        ]);
        if (Auth::attempt($credentials)) {
            //check if the user is an agent and is account is enabled.
            $user = Auth::user();
            if (Auth::user()->hasRole('agent')) {
                $ticket_agent = TicketAgent::where('user_id', $user->id)->first();
                if (!$ticket_agent) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You are not an agent.'], 404);
                }
                if ($ticket_agent->agent_status != 'active' ) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Account is currently inactive.'
                    ], 401);
                }
            }
            $user->tokens()->delete();
            $token = $user->createToken('igr_system_auth_token')->plainTextToken;
            $user->update([
                'last_login_at' => now()
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'User logged in successfully',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'user' => new UserResource($user),
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }
    }

}
