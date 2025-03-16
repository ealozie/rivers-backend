<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Cooperate;
use App\Models\Individual;
use App\Models\TicketAgent;
use App\Models\User;
//use App\Models\VansosmsLog;
use App\Traits\SendSMS;
//use App\Traits\VansoSMS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    use SendSMS;
    //use VansoSMS;

    /**
     * Login.
     *
     * Allow user login into the application.
     */
    public function __invoke(Request $request)
    {
        // $message = "Hello Nathaniel this is a test sms.Let me know if your received it.Thank you";
        // $this->send_sms_process_message("+2349038562651", $message);
        $request->validate([
            'phone_number' => ['required'],
            'password' => ['required'],
            'user_type' => 'required|in:agent,admin,individual,cooperate'
        ]);

        $phone_number = $request->phone_number;
        $password = $request->password;
        $user_type = $request->user_type;

        // Determine lookup based on user type
        if (in_array($user_type, ['admin', 'agent'])) {
            // Admins & Agents have phone numbers in users table
            $user = User::where('phone_number', $phone_number)->first();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Your account was not found."
                ], 401);
            }
            if (!$user->hasRole(['admin', 'agent'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => "The account type is not associated with your credentials."
                ], 401);
            }
        } elseif ($user_type === 'individual') {
            // Find the related user ID from individuals table
            $individual = Individual::where('phone_number', $phone_number)->first();
            if (!$individual) {
                return response()->json([
                    'status' => 'error',
                    'message' => "The account type is not an individual."
                ], 401);
            }
            if ($individual->approval_status == 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => "Your account approval is still pending."
                ], 401);
            }
            $user = $individual ? User::find($individual->user_id) : null;
            if (!$user->hasRole('individual')) {
                return response()->json([
                    'status' => 'error',
                    'message' => "The account type is not associated with your credentials."
                ], 401);
            }
        } elseif ($user_type === 'cooperate') {
            // Find the related user ID from cooperates table
            $cooperate = Cooperate::where('phone_number', $phone_number)->first();
            if (!$cooperate) {
                return response()->json([
                    'status' => 'error',
                    'message' => "The account type is not a cooperate."
                ], 401);
            }
            if ($cooperate->approval_status == 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => "Your account approval is still pending."
                ], 401);
            }
            $user = $cooperate ? User::find($cooperate->user_id) : null;
            if (!$user->hasRole('cooperate')) {
                return response()->json([
                    'status' => 'error',
                    'message' => "The account type is not associated with your credentials."
                ], 401);
            }
        }
        if ($user && Hash::check($password, $user->password)) {
            //check if the user is an agent and is account is enabled.
            Auth::login($user);
            $user = Auth::user();
            // if (Auth::user()->hasRole('agent')) {
            //     $ticket_agent = TicketAgent::where('user_id', $user->id)->first();
            //     if (!$ticket_agent) {
            //         return response()->json([
            //             'status' => 'error',
            //             'message' => 'You are not an agent.'], 404);
            //     }
            //     if ($ticket_agent->agent_status != 'active' ) {
            //         return response()->json([
            //             'status' => 'error',
            //             'message' => 'Account is currently inactive.'
            //         ], 401);
            //     }
            // }
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
                    'role' => $user->getRoleNames(),
                    'formatted_permissions' => array_map(function($item){
                        return $item['name'];
                   }, $user->getPermissionsViaRoles()->toArray()),
                    'permissions' => $user->getPermissionsViaRoles(),
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
