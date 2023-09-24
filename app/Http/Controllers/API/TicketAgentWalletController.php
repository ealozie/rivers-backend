<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketAgentWalletResource;
use App\Models\TicketAgentWallet;
use Illuminate\Http\Request;

/**
 * @tags Ticket Agent Wallet Transactions Service
 */
class TicketAgentWalletController extends Controller
{
    /**
     * Get all resource.
     *
     * Authorization header is required to be set to Bearer `<token>` <br>
     * Return list of all ticket agent wallet transactions by the current authenticated agent. <br>
     * Additional Query parameter are `limit` and `offset`
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            $per_page = 20;
            $ticket_agent_wallet = TicketAgentWallet::paginate($per_page);
            if ($request->has('all')) {
                $ticket_agent_wallet = TicketAgentWallet::latest()->get();
            }
            return TicketAgentWalletResource::collection($ticket_agent_wallet);
        }

        $limit = 10;
        $offset = 0;
        if ($request->has('limit')) {
            $limit = $request->get('limit');
        }
        if ($request->has('offset')) {
            $offset = $request->get('offset');
        }
        $ticket_agent_wallets = TicketAgentWallet::where('user_id', $user->id)->latest()->offset($offset)->limit($limit)->get();
        $total_number_of_records = TicketAgentWallet::where('user_id', $user->id)->count();

        if (!count($ticket_agent_wallets)) {
            return response()->json([
                'status' => 'success',
                'data' => [
                'ticket_wallet_data' => [],
                'total_number_of_records' => (int) $total_number_of_records
            ],
            ], 200);
        }
        return response()->json([
            'status' => 'success',
            'data' => [
                'ticket_wallet_data' => TicketAgentWalletResource::collection($ticket_agent_wallets),
                'total_number_of_records' => (int) $total_number_of_records
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function update(Request $request, string $id)
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
