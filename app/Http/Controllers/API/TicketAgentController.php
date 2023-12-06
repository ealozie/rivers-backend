<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketAgentListResource;
use App\Http\Resources\TicketAgentWalletResource;
use App\Models\AppSetting;
use App\Models\TicketAgent;
use App\Models\TicketAgentCategory;
use App\Models\TicketAgentWallet;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @tags Ticket Agents Service
 */
class TicketAgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $per_page = 20;
        $ticket_agents = TicketAgent::paginate($per_page);
        if (Auth::user()->hasRole('super_agent')) {
            $user_id = $request->user()->id;
            $ticket_agents = TicketAgent::where('super_agent_id', $user_id)->paginate($per_page);
        }
        return TicketAgentListResource::collection($ticket_agents);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'super_agent_id' => 'sometimes|exists:users,id',
            'agent_type' => 'required',
            'discount' => 'required',
            'agent_status' => 'required',
            'can_transfer_wallet_fund' => 'required|boolean',
            'can_fund_wallet' => 'required|boolean',
            'agent_ticket_categories' => 'required|array|min:1',
            //'role' => 'required|in:agent'
        ]);
        $agent = TicketAgent::where('user_id', $validatedData['user_id'])->first();
        if ($agent) {
            return response()->json(['status' => 'success', 'message' => 'User is already an agent.']);
        }
        $validatedData['added_by'] = $request->user()->id;
        $validatedData['wallet_balance'] = 0;
        $validatedData['agent_status'] = 'inactive';
        if (isset($validatedData['super_agent_id'])) {
            $user = User::find($validatedData['super_agent_id']);
            if (!$user->hasRole('super_agent')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Agent is not a super agent.',
                ], 500);
            }
            $super_agent = TicketAgent::where('user_id', $validatedData['super_agent_id'])->first();
            $validatedData['can_transfer_wallet_fund'] = 0;
            $validatedData['can_fund_wallet'] = 0;
            $validatedData['discount'] = $super_agent->discount;
            $validatedData['super_agent_id'] = $validatedData['super_agent_id'];
            $validatedData['added_by'] = $request->user()->id;
        }
        
        try {
            $agent = TicketAgent::create($validatedData);
            $user = User::find($validatedData['user_id']);
            $user->assignRole('agent');
            $user->role = 'agent';
            $user->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
        //$user = User::find($validatedData['user_id']);
        //$user->assignRole($validatedData['role']);
        if (isset($validatedData['super_agent_id'])) {
            $super_agent = TicketAgent::where('user_id', $validatedData['super_agent_id'])->first();
            $super_agent_categories = TicketAgentCategory::where('ticket_agent_id', $super_agent->id)->get();
            foreach ($super_agent_categories as $category) {
                $ticket_agent_category = new TicketAgentCategory();
                $ticket_agent_category->ticket_agent_id = $agent->id;
                $ticket_agent_category->ticket_category_id = $category->ticket_category_id;
                $ticket_agent_category->discount = 0;
                //$ticket_agent_category->super_agent_id = $super_agent->user_id;
                $ticket_agent_category->added_by = $request->user()->id;
                $ticket_agent_category->status = 'active';
                $ticket_agent_category->save();
            }
        } else {
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
            'agent_type' => 'required',
            'discount' => 'required',
            'agent_status' => 'required',
            'can_transfer_wallet_fund' => 'required',
            'can_fund_wallet' => 'required',
            'agent_ticket_categories' => 'sometimes|array|min:1',
        ]);
        $agent = TicketAgent::find($id);
        if (!$agent) {
            return response()->json(['status' => 'error', 'message' => 'Agent ID not found.']);
        }
        $agent->update($validatedData);
        if (count($validatedData['agent_ticket_categories'])) {
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
        
    }

    /**
     * Get Ticket Wallet Transactions by Agent ID.
     */
    public function ticket_agent_transactions(Request $request, $agent_id)
    {
        //check if agent ID exist
        $agent = TicketAgent::find($agent_id);
        if (!$agent) {
            return response()->json(['status' => 'error', 'message' => 'Agent ID not found.']);
        }
        $agent_transactions = TicketAgentWallet::where('ticket_agent_id', $agent_id)->get();
        return TicketAgentWalletResource::collection($agent_transactions);
    }

    /**
     * Change agent's Super agent.
     */
    public function change_agent_super_agent(Request $request, $agent_id)
    {
        $validatedData = $request->validate([
            'super_agent_id' => 'required',
        ]);
        $agent = TicketAgent::find($agent_id);
        if (!$agent) {
            return response()->json(['status' => 'error', 'message' => 'Agent ID not found.']);
        }
        $super_agent = TicketAgent::find($validatedData['super_agent_id']);
        if (!$super_agent) {
            return response()->json(['status' => 'error', 'message' => 'Super agent ID not found.']);
        }
        TicketAgentCategory::where('ticket_agent_id', $agent->id)
                ->delete();
        $agent->super_agent_id = $super_agent->user_id;
        $agent->discount = $super_agent->discount;
        $agent->save();
        $super_agent_categories = TicketAgentCategory::where('ticket_agent_id', $super_agent->id)->get();
            foreach ($super_agent_categories as $category) {
                $ticket_agent_category = new TicketAgentCategory();
                $ticket_agent_category->ticket_agent_id = $agent->id;
                $ticket_agent_category->ticket_category_id = $category->ticket_category_id;
                $ticket_agent_category->discount = 0;
                //$ticket_agent_category->super_agent_id = $super_agent->user_id;
                $ticket_agent_category->added_by = $request->user()->id;
                $ticket_agent_category->status = 'active';
                $ticket_agent_category->save();
            }
        return response()->json(['status' => 'success', 'message' => "Agent's super agent has been successfully changed."]);
    }

    /**
     * Remove agent from super agent.
     */
    public function remove_agent_super_agent(Request $request, $agent_id)
    {
        $agent = TicketAgent::find($agent_id);
        if (!$agent) {
            return response()->json(['status' => 'error', 'message' => 'Agent ID not found.']);
        }
        TicketAgentCategory::where('ticket_agent_id', $agent->id)
                ->delete();
        $agent->super_agent_id = null;
        $agent->discount = 0;
        $agent->save();
        return response()->json(['status' => 'success', 'message' => "Agent has been successfully removed."]);
    }
}
