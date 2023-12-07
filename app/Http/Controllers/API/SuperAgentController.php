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

/**
 * @tags Super Agents Service
 */
class SuperAgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $per_page = 20;
        $super_agents_ids = User::role('super_agent')->pluck('id')->toArray();
        $ticket_agents = TicketAgent::whereIn('user_id', $super_agents_ids)->paginate($per_page);
        return TicketAgentListResource::collection($ticket_agents);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'agent_type' => 'required',
            'discount' => 'required',
            'agent_ticket_categories' => 'required|array|min:1',
        ]);
        

        $user = User::find($validatedData['user_id']);
        $user->assignRole('super_agent');
        $validatedData['added_by'] = $request->user()->id;
        $validatedData['user_id'] = $user->id;
        $validatedData['wallet_balance'] = 0;
        $validatedData['agent_status'] = 'inactive';
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
            'discount' => 'sometimes|numeric',
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

    /**
     * Restore to super agent or assign as super agent.
     */
    public function restore_super_agent(string $id)
    {
        $validatedData = $request->validate([
            'discount' => 'somtimes|numeric',
            'agent_ticket_categories' => 'sometimes|array|min:1',
        ]);
        $ticket_agent = TicketAgent::find($agent->id);
        if ($ticket_agent) {
            TicketAgentCategory::where('ticket_agent_id', $agent->id)
                ->delete();
            $user = User::find($agent->user_id);
            $user->role = 'super_agent';
            $user->save();
            $user->roles()->detach();
            $agent->discount = $validatedData['discount'];
            $agent->agent_type = 'super_agent';
            $agent->super_agent_id = null;
            $agent->save();
            $user->assignRole('super_agent');
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'Ticket agent not found.'
            ], 200);
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
        return new TicketAgentListResource($ticket_agent);
        
    }
}
