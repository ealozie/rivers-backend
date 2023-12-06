<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketAgentListResource;
use App\Http\Resources\TicketAgentResource;
use App\Http\Resources\UserResource;
use App\Models\TicketAgent;
use App\Models\TicketAgentCategory;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SuperAgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$per_page = 20;
        $super_agents = User::role('super_agent')->get();
        return UserResource::collection($super_agents);
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
            'discount' => 'required',
            'agent_ticket_categories' => 'required|array|min:1',
        ]);
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
        $user->phone_number_verified_at = now();
        //Generate a random password
        $password = 123456;
        $user->phone_number = $validatedData['phone_number'];
        $user->role = 'super_agent';
        $user->status = 1;
        $user->password = Hash::make($password);
        $user->phone_number_verification_code =
            mt_rand(111111, 999999);
        $user_unique_id  = '9' . date('hi') . mt_rand(11111, 99999);
        $user->unique_id = $user_unique_id;
        $user->save();
        $user->assignRole('super_agent');

        $validatedData['added_by'] = $request->user()->id;
        $validatedData['user_id'] = $user->id;
        $validatedData['wallet_balance'] = 0;
        $validatedData['agent_status'] = 'pending';
        $validatedData['can_transfer_wallet_fund'] = 1;
        $validatedData['can_fund_wallet'] = 1;
        $validatedData['agent_type'] = 'super_agent';
        try {
            $agent = TicketAgent::create($validatedData);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
        foreach ($validatedData['agent_ticket_categories'] as $category) {
            $ticket_agent_category = new TicketAgentCategory();
            $ticket_agent_category->ticket_agent_id = $agent->id;
            $ticket_agent_category->ticket_category_id = $category;
            $ticket_agent_category->discount = 0;
            $ticket_agent_category->added_by = $request->user()->id;
            $ticket_agent_category->status = 'active';
            $ticket_agent_category->save();
        }
        $ticket_agent = TicketAgent::find($agent->id);
        return new TicketAgentListResource($ticket_agent);
        //return response()->json(['status' => 'success', 'message' => 'User has been successfully added.', 'data' => new UserResource($user)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ticket_agent = TicketAgent::find($id);
        return new TicketAgentListResource($ticket_agent);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'discount' => 'somtimes|numeric',
            'agent_status' => 'sometimes|string',
            'can_transfer_wallet_fund' => 'sometimes|boolean',
            'can_fund_wallet' => 'sometimes|boolean',
            'agent_ticket_categories' => 'sometimes|array|min:1',
        ]);
        $agent = TicketAgent::find($id);
        if (!$agent) {
            return response()->json(['status' => 'error', 'message' => 'Agent ID not found.']);
        }
        $agent->update($validatedData);
        $sub_agents = TicketAgent::where('added_by', $agent->user_id)->update([
            'discount' => $agent->discount,
            'agent_status' => $agent->agent_status,
        ]);
        //return $validatedData;
        if (isset($validatedData['agent_ticket_categories']) && count($validatedData['agent_ticket_categories'])) {
            TicketAgentCategory::where('ticket_agent_id', $agent->id)
                ->delete();
            foreach ($validatedData['agent_ticket_categories'] as $category) {
                $ticket_agent_category = new TicketAgentCategory();
                $ticket_agent_category->ticket_agent_id = $agent->id;
                $ticket_agent_category->ticket_category_id = $category;
                $ticket_agent_category->discount = 0;
                $ticket_agent_category->added_by = $request->user()->id;
                $ticket_agent_category->status = 'active';
                $ticket_agent_category->save();
            }
        }
        $ticket_agent = TicketAgent::find($agent->id);
        return new TicketAgentListResource($ticket_agent);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ticket_agent = TicketAgent::find($agent->id);
        if ($ticket_agent) {
            TicketAgentCategory::where('ticket_agent_id', $agent->id)
                ->delete();
            $user = User::find($agent->user_id);
            $user->role = 'individual';
            $user->save();
            $user->roles()->detach();
            $agent->discount = 0;
            $agent->save();
        TicketAgent::where('super_agent_id', $user->id)->update([
            'super_agent_id' => null
        ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket agent not found.'
            ], 404);
        }
        
    }
}
