<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\TicketAgent;
use App\Models\TicketAgentWallet;
use App\Models\User;
use App\Traits\SendSMS;
use Illuminate\Http\Request;

/**
 * @tags Ticket Wallet Fund Transfer Service
 */
class WalletFundTransferController extends Controller
{
    use SendSMS;
    /**
     * Initiate the transfer request.
     *
     * Authorization header is required to be set to Bearer `<token>`
     */
    public function __invoke(Request $request)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric',
            'phone_number' => 'required|string|min:11|max:11|exists:users,phone_number',
        ], [
            'phone_number.exists' => 'Sorry! Phone number does not exist. Please check and try again.',
        ]);
        $user = $request->user();
        $ticket_agent = TicketAgent::where('user_id', $user->id)->first();
        if (!$ticket_agent) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not allowed to process tickets. Contact the administrator for assistance.',
            ], 403);
        }

        if ($user->phone_number == $validatedData['phone_number']) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not allowed to transfer fund to yourself.',
            ], 403);
        }

        //Check if wallet value is enough to transfer
        $wallet_balance = $ticket_agent->wallet_balance;
        $amount = $validatedData['amount'];
        if (!$ticket_agent->can_transfer_wallet_fund) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have the ability to transfer wallet funds.',
            ], 403);
        }

        if ($wallet_balance < $amount) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient funds in wallet.',
                'data' => [
                    'wallet_balance' => number_format($wallet_balance, 2),
                    'transfer_amount' => number_format($amount, 2),
                ]
            ], 403);
        }
        $ticket_agent->wallet_balance -= $amount;
        $ticket_agent->save();
        $phone_number = $validatedData['phone_number'];
        $account = User::where('phone_number', $phone_number)->first();
        $recipient = TicketAgent::where('user_id', $account->id)->first();
        $recipient->wallet_balance += $amount;
        $recipient->save();
        $ticket_agent_wallet = new TicketAgentWallet();
        $ticket_agent_wallet->ticket_agent_id = $ticket_agent->id;
        $ticket_agent_wallet->user_id = $user->id;
        $ticket_agent_wallet->amount = $amount;
        $ticket_agent_wallet->transaction_type = 'debit';
        $ticket_agent_wallet->type = 'transfer';
        $ticket_agent_wallet->transaction_status = 'active';
        $ticket_agent_wallet->added_by = $user->id;
        $ticket_agent_wallet->beneficiary_id = $recipient->id;
        $reference = date('isYd');
        $ticket_agent_wallet->transaction_reference_number = $reference;
        $ticket_agent_wallet->save();
        //Log recipient wallet
        $recipient_wallet = new TicketAgentWallet();
        $recipient_wallet->ticket_agent_id = $recipient->id;
        $recipient_wallet->user_id = $account->id;
        $recipient_wallet->amount = $amount;
        $recipient_wallet->transaction_type = 'credit';
        $recipient_wallet->type = 'transfer';
        $recipient_wallet->transaction_status = 'active';
        $recipient_wallet->added_by = $user->id;
        $ticket_agent_wallet->beneficiary_id = $recipient->id;
        $recipient_wallet->transaction_reference_number = $reference;
        $recipient_wallet->save();

        //Send SMS to recipient
        $mobile_number = ltrim($phone_number, "0");
        $message = "Your wallet has been credited with NGN" . number_format($amount, 2) . " by " . $user->name . ". Your new wallet balance is NGN" . number_format($recipient->wallet_balance, 2) . ".";
        $this->send_sms_process_message("+234" . $mobile_number, $message);

        //Send SMS to user
        $mobile_number = ltrim($user->phone_number, "0");
        $message = "Your wallet has been debited with NGN" . number_format($amount, 2) . ". Your new wallet balance is NGN" . number_format($ticket_agent->wallet_balance, 2) . ".";
        $this->send_sms_process_message("+234" . $mobile_number, $message);

        return response()->json([
            'status' => 'success',
            'message' => 'Wallet fund transfer successful.',
            'data' => [
                'recipient' => $account->name,
                'amount' => number_format($amount, 2),
            ]
        ], 200);
    }
}
