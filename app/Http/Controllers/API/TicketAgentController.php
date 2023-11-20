<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketAgentListResource;
use App\Http\Resources\TicketAgentWalletResource;
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
    public function index()
    {
        $per_page = 20;
        $ticket_agents = TicketAgent::paginate($per_page);
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
            'agent_status' => 'required',
            'can_transfer_wallet_fund' => 'required',
            'can_fund_wallet' => 'required',
            'agent_ticket_categories' => 'required|array|min:1',
        ]);
        $agent = TicketAgent::where('user_id', $validatedData['user_id'])->first();
        if ($agent) {
            return response()->json(['status' => 'success', 'message' => 'User is already an agent.']);
        }
        $validatedData['added_by'] = $request->user()->id;
        $validatedData['wallet_balance'] = 0;
        $agent = TicketAgent::create($validatedData);
        $user = User::find($validatedData['user_id']);
        $user->assignRole('agent');
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
        //
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
}
