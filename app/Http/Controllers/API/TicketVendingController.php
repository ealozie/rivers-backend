<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketVendingCollection;
use App\Http\Resources\TicketVendingResource;
use App\Models\CentralSystemLGA;
use App\Models\CommercialVehicle;
use App\Models\TicketAgent;
use App\Models\TicketAgentCategory;
use App\Models\TicketAgentWallet;
use App\Models\TicketCategory;
use App\Models\TicketVending;
use App\Models\User;
use App\Traits\SendSMS;
use Axiom\Rules\LocationCoordinates;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


/**
 * @tags Ticket Vending Service
 */
class TicketVendingController extends Controller
{
    use SendSMS;
    /**
     * Get all resource.
     *
     * Authorization header is required to be set to Bearer `<token>` <br>
     * Return list of all ticket vending that been vended by current authenticated agent. <br>
     * Additional Query parameter are `limit` and `offset`. <br>
     * Additional Query parameter `?query=all`, return all resources
     * without pagination.
     */
    public function index(Request $request)
    {
        $limit = 20;
        $offset = 0;
        if ($request->has('limit')) {
            $limit = $request->get('limit');
        }
        if ($request->has('offset')) {
            $offset = $request->get('offset');
        }
        // return response()->json([
        //         'status' => 'success',
        //         'data' => [
        //             'ticket_vending_data' => [],
        //             'total_number_of_records' => (int) 0
        //         ],
        //     ], 200);
        $ticket_vending = TicketVending::where('user_id', $request->user()->id)
            ->latest()
            ->offset($offset)
            ->limit($limit)
            ->get();
        $total_number_of_records = TicketVending::where('user_id', $request->user()->id)->count();
        $user = $request->user();
        if ($user->hasRole(['admin', 'guest'])) {
            $limit = 2000;
            $ticket_vending = TicketVending::latest()
                ->offset($offset)
                ->limit($limit)
                ->get();
            if ($request->has('query') && $request->get('query') === 'all') {
                $ticket_vending = TicketVending::latest()->get();
            }
            $total_number_of_records = TicketVending::count();
        }
        if ($user->hasRole('super_agent')) {
            $sub_agents = TicketAgent::where('super_agent_id', $request->user()->id)
                ->orWhere('user_id', $request->user()->id)
                ->pluck('user_id')
                ->toArray();
            $ticket_vending = TicketVending::latest()
                ->offset($offset)
                ->limit($limit)
                ->whereIn('user_id', $sub_agents)
                ->get();
            if ($request->has('query') && $request->get('query') === 'all') {
                $ticket_vending = TicketVending::whereIn('user_id', $sub_agents)
                    ->latest()
                    ->get();
            }
            $total_number_of_records = TicketVending::whereIn('user_id', $sub_agents)->count();
        }

        if (!count($ticket_vending)) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'ticket_vending_data' => [],
                    'total_number_of_records' => (int) $total_number_of_records
                ],
            ], 200);
        }
        return response()->json([
            'status' => 'success',
            'data' => [
                'ticket_vending_data' => TicketVendingResource::collection($ticket_vending),
                'total_number_of_records' => (int) $total_number_of_records
            ]
        ]);
    }

    /**
     * Get plate number recent records.
     *
     * Authorization header is required to be set to Bearer `<token>`
     *
     */
    public function plate_number_recent_records(Request $request) : JsonResponse
    {
        $requestData = $request->validate([
            'plate_number' => 'required|string|min:5|max:10',
        ]);
        $raw_query = "
        WITH arrayRecord AS (
            SELECT * FROM ticket_vendings WHERE plate_number = ?
        )
        SELECT * FROM arrayRecord WHERE id = (
            SELECT MAX(id) FROM arrayRecord
        )";
        $plate_number = $requestData['plate_number'];
        $vending_record = DB::select($raw_query, [$plate_number]);
        //$vending_record = TicketVending::where('plate_number', $requestData['plate_number'])->count();

        if (!count($vending_record)) {
            return response()->json([
                'status' => 'error',
                'Plate number not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Retrieved successfully',
            'data' => [
                'plate_number' => $vending_record[0]->plate_number,
                'ticket_category_id' => $vending_record[0]->ticket_category_id,
                //'ticket_category' => $vending_record?->ticket_category?->category_name ?? '',
                'phone_number' => $vending_record[0]->phone_number,
                'owner_name' => $vending_record[0]->owner_name,
                'created_at' => $vending_record[0]->created_at,
            ]
        ], 200);
        // return response()->json([
        //    'status' => 'success',
        //    'message' => 'Retrieved successfully',
        //    'data' => [
        //        'plate_number' => $vending_record->plate_number,
        //        'ticket_categor_id' => $vending_record->ticket_category_id,
        //        'ticket_category' => $vending_record?->ticket_category?->category_name ?? '',
        //        'phone_number' => $vending_record->phone_number,
        //        'owner_name' => $vending_record->owner_name,
        //        'created_at' => $vending_record->created_at,
        //    ]
        // ], 200);
    }

    /**
     * Store a resource.
     *
     * Authorization header is required to be set to Bearer `<token>` <br> And only ticket agent can vend ticket.
     *
     * `geo_location_coordinates` is optional but if provided. The given value should be a comma-separated set of latitude and longitude coordinates. Example `4.6604761,7.9411649`.
     */
    public function store(Request $request) : JsonResponse
    {
        $requestData = $request->validate([
            'plate_number' => 'required|string|min:5|max:10',
            'phone_number' => 'required|string|min:11|max:11',
            'ticket_category_id' => 'required|string',
            'owner_name' => 'required|string|min:3|max:10',
            //'ticket_type' => 'required|in:SINGLE,WEEK,MONTH',
            'quantity' => 'sometimes|numeric',
            'geo_location_coordinates' => ['sometimes', new LocationCoordinates],
        ]);
        if (isset($requestData['quantity'])) {
            $quantity = $requestData['quantity'];
        } else {
            $quantity = 1;
        }
        $phone_number = $requestData['phone_number'];
        $ticket_category_id = $requestData['ticket_category_id'];
        $plate_number = $requestData['plate_number'];
        //Check if user is an agent
        $user = $request->user();
        $ticket_agent = TicketAgent::where('user_id', $user->id)->first();
        if (!$ticket_agent) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not allowed to process tickets. Contact the administrator for assistance.',
            ], 403);
        }

        if ($ticket_agent->agent_status !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Account has been placed on hold.'
            ], 401);
        }

        //Check if category accept multiple and if agent has purchased for that day.
        $ticket_category = TicketCategory::find($ticket_category_id);
        if (!$ticket_category->allow_single_vending) {
            return response()->json([
                'status' => 'error',
                'message' => 'Single vending not allowed for this ticket category.'
            ], 500);
        }
        $category_name = $ticket_category->category_name;
        //Check if agent is allowed to vend ticket
        /* $ticket_agent_category = TicketAgentCategory::where('ticket_agent_id', $ticket_agent->id)
        ->where('ticket_category_id', $ticket_category_id)
        ->first();
        if (!$ticket_agent_category) {
            return response()->json([
                'status' => 'error',
                'message' => "You cannot vend for {$category_name} ticket  category.",
            ], 403);
        }*/

        if (!$ticket_category->allow_multiple_ticket_purchase) {
            $ticket_vending = TicketVending::where('plate_number', $plate_number)
                ->where('ticket_category_id', $ticket_category_id)
                ->whereDate('created_at', date('Y-m-d'))
                ->first();
            if ($ticket_vending) {
                return response()->json([
                    'status' => 'error',
                    'message' => $plate_number . ' has already purchased ticket for this category today.',
                    "data" => new TicketVendingResource($ticket_vending),
                ], 403);
            }
        }

        //Check if agent has enough balance
        //Check if agent has discount for this ticket then calculate the ticket price
        $ticket_price = $ticket_category->amount * $quantity;
        $ticket_actual_price = $ticket_category->amount * $quantity;
        $discount_amount = 0;
        $actual_ticket_amount = 0;
        if ($ticket_agent->discount) {
            $discount_value = $ticket_category->amount * ($ticket_agent->discount / 100);
            $price = $ticket_category->amount - $discount_value;
            $ticket_price = round($price) * $quantity;
            $discount_amount = $ticket_price;
        }
        if ($ticket_agent->account_type === "Save4ME") {
            $ticket_price = $ticket_category->amount * $quantity;
            $actual_ticket_amount = $ticket_price;
        } else {
            $actual_ticket_amount = $ticket_price;
        }
        if ($ticket_agent->wallet_balance < $ticket_price) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have enough balance to purchase this ticket.',
                'data' => [
                    'wallet_balance' => number_format($ticket_agent->wallet_balance, 2),
                    'ticket_price_before_discount' => number_format($ticket_actual_price, 2),
                    'discount' => $ticket_agent->discount . '%',
                    'ticket_price_after_discount' => number_format($ticket_price, 2),
                ]
            ], 403);
        }

        // if ($ticket_agent->account_type == "Save4ME") {
        //     $ticket_price = $ticket_category->amount * $quantity;
        // }
        //return $discount_value;
        //Proceed to vend or create a record for this
        $ticket_price = $discount_amount;
        try {
            DB::beginTransaction();
            $ticket_vending = new TicketVending();
            $ticket_vending->plate_number = $plate_number;
            $ticket_vending->ticket_category_id = $ticket_category_id;
            $ticket_vending->amount = $ticket_price;
            $ticket_vending->quantity = $quantity;
            $ticket_vending->discounted_price = $ticket_price;
            $ticket_vending->owner_name = $requestData['owner_name'];
            $ticket_vending->ticket_amount = $ticket_actual_price;
            $ticket_vending->agent_discount = $ticket_agent->discount;
            $ticket_vending->ticket_agent_id = $ticket_agent->id;
            $ticket_vending->user_id = $user->id;
            $ticket_vending->phone_number = $phone_number;
            if (isset($request->geo_location_coordinates)) {
                $coodinates = explode(",", $request->geo_location_coordinates);
                $ticket_vending->latitude = $coodinates[0];
                $ticket_vending->longitude = $coodinates[1];
            }
            $ticket_vending->expired_at = $ticket_category->expired_at;
            $ticket_vending->ticket_status = 'active';
            $ticket_vending->ticket_reference_number = random_int(11111, 99999) . date('dmYhis');
            $ticket_vending->save();
            //Debit wallet balance and log the transaction
            $ticket_agent->wallet_balance = $ticket_agent->wallet_balance - $actual_ticket_amount;
            $ticket_agent->save();
            if ($user) {
                $lga = CentralSystemLGA::find($user->local_government_area_id);
                if ($lga) {
                    $ticket_vending->local_government_area_id = $user->local_government_area_id;
                    $ticket_vending->zone = $lga->lga_zone;
                    $ticket_vending->save();
                }
            }
            if ($ticket_agent->account_type === "Save4ME") {
                $ticket_price = $ticket_category->amount * $quantity;
                $total_discount = $discount_value * $quantity;
                $ticket_agent->increment('savings_balance', $total_discount);
            }
            //Log transaction
            $ticket_agent_wallet = new TicketAgentWallet();
            $ticket_agent_wallet->ticket_agent_id = $ticket_agent->id;
            $ticket_agent_wallet->user_id = $user->id;
            $ticket_agent_wallet->amount = $ticket_price;
            $ticket_agent_wallet->transaction_type = 'debit';
            $ticket_agent_wallet->transaction_status = 'active';
            $ticket_agent_wallet->added_by = $user->id;
            $ticket_agent_wallet->transaction_reference_number = $ticket_vending->ticket_reference_number;
            $ticket_agent_wallet->save();
            DB::commit();

            //return 'hello Na';
            //Log to Central system
            $ticket_created_date = Carbon::parse($ticket_vending->created_at);
            // Check if the created date is Saturday
            if ($ticket_created_date->dayOfWeek === Carbon::SATURDAY) {
                $ticket_expired_date = $ticket_created_date->addDays(2);
            } else {
                $ticket_expired_date = $ticket_created_date->addDay();
            }
            //$final_expired_date = $ticket_expired_date->toDateString();
            $final_expired_date = $ticket_expired_date->format('l, F j, Y');


            //Send SMS to user
            $mobile_number = ltrim($phone_number, "0");
            $ticket_category_name = $ticket_category->category_name;
            $amount = number_format($ticket_actual_price, 2);
            $expires_at = date('h:ia', strtotime($ticket_category->expired_at));
            $owner_name = $requestData['owner_name'];
            $ticket_created_date = Carbon::parse($ticket_vending->created_at);
            $ticket_expired_date = $ticket_created_date->addDay();
            $current_date = $ticket_expired_date->format('d-m-Y');
            $current_date = date('d-m-Y');
            $message = "Hello {$owner_name}, your {$ticket_category_name} ticket purchase for {$plate_number} (N{$amount}) was successful. Expires at {$current_date}, {$expires_at}. Thank you.";
            // $message = "Hello, your ticket has been successfully purchased. Your ticket reference number is " . $ticket_vending->ticket_reference_number . ". Thank you for using AKSG-IRS.";
            //return $mobile_number;
            $this->send_sms_process_message("+234" . $mobile_number, $message);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing your request. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Ticket purchased successfully.',
            'data' => [
                'ticket' => new TicketVendingResource($ticket_vending),
            ]
        ], 200);
    }

    /**
     * Display specific resource.
     *
     * Authorization header is required to be set to Bearer `<token>` <br> And only ticket agent can vend ticket.
     */
    public function show(string $id) : JsonResponse
    {
        $ticket_vending = TicketVending::find($id);
        if (!$ticket_vending) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket vending not found.',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Ticket vending retrieved successfully.',
            'data' => new TicketVendingResource($ticket_vending),
        ], 200);
    }

    /**
     * Ticket Vending Statistics for Agent.
     */
    public function ticket_statistics(Request $request) : JsonResponse
    {
        Carbon::setWeekStartsAt(Carbon::MONDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);
        $user = $request->user();
        $tickets_today = TicketVending::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->count();
        $tickets_today_amount = TicketVending::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->sum('ticket_amount');

        //$tickets_this_week = TicketVending::where('user_id', $user->id)->whereDate('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $now = Carbon::now();
        // $tickets_this_week = TicketVending::where('user_id', $user->id)->whereBetween("created_at", [
        //     $now->startOfWeek()->format('Y-m-d'),
        //     $now->endOfWeek()->format('Y-m-d')
        // ])->count();
        // $tickets_this_week_amount = TicketVending::where('user_id', $user->id)->whereBetween("created_at", [
        //     $now->startOfWeek()->format('Y-m-d'),
        //     $now->endOfWeek()->format('Y-m-d')
        // ])->sum('ticket_amount');
        //$tickets_this_week_amount = TicketVending::where('user_id', $user->id)->whereDate('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('ticket_amount');
        $tickets_this_month = TicketVending::where('user_id', $user->id)->whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->count();
        $tickets_this_month_amount = TicketVending::where('user_id', $user->id)->whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->sum('ticket_amount');

        // $tickets_last_month_amount = TicketVending::where('user_id', $user->id)
        //     ->whereBetween('created_at', [
        //         Carbon::now()->subMonth()->startOfMonth(),
        //         Carbon::now()->subMonth()->endOfMonth()
        //     ])->sum('ticket_amount');
        // $tickets_last_month = TicketVending::where('user_id', $user->id)
        //     ->whereBetween('created_at', [
        //         Carbon::now()->subMonth()->startOfMonth(),
        //         Carbon::now()->subMonth()->endOfMonth()
        //     ])->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'tickets_today' => [
                    'total_tickets' => (int) $tickets_today,
                    'total_amount' => (float) $tickets_today_amount,
                    // 'total_tickets' => (int) 0,
                    // 'total_amount' => (float) 0.00
                ],
                'tickets_this_week' => [
                    // 'total_tickets' => (int) $tickets_this_week,
                    // 'total_amount' => (float) $tickets_this_week_amount
                    'total_tickets' => (int) 0,
                    'total_amount' => (float) 0.00

                ],
                'tickets_this_month' => [
                    'total_tickets' => (int) $tickets_this_month,
                    'total_amount' => (float) $tickets_this_month_amount
                    //'total_tickets' => (int) 0,
                    //'total_amount' => (float) 0.00
                ],
                // 'tickets_last_month' => [
                //     'total_tickets' => (int) $tickets_last_month,
                //     'total_amount' => (float) $tickets_last_month_amount
                // ]
            ]
        ], 200);
    }

    /**
     * Ticket Vending Statistics for Agent(Daily).
     */
    public function daily_ticket_vending_statistics(Request $request) : JsonResponse
    {
        $current_time = Carbon::now();
        $init_6am_morning = Carbon::createFromTime(8, 0, 0);
        $init_11_59am_morning = Carbon::createFromTime(11, 59, 59);
        if ($current_time->between($init_6am_morning, $init_11_59am_morning)) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'tickets_today' => [
                        'total_tickets' => (string) 'NA',
                        'total_amount' => (string) 'NA'
                    ],
                ]
            ], 200);
        }
        $user = $request->user();
        Carbon::setWeekStartsAt(Carbon::MONDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);
        $user = $request->user();
        $tickets_today = TicketVending::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->count();
        $tickets_today_amount = TicketVending::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->sum('ticket_amount');
        return response()->json([
            'status' => 'success',
            'data' => [
                'tickets_today' => [
                    'total_tickets' => (int) $tickets_today,
                    'total_amount' => (float) $tickets_today_amount
                ],
            ]
        ], 200);
    }

    /**
     * Ticket Vending Statistics for Agent (Weekly).
     */
    public function weekly_ticket_vending_statistics(Request $request) : JsonResponse
    {
        $user = $request->user();
        $tickets_this_week = TicketVending::where('user_id', $user->id)->whereDate('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $now = Carbon::now();
        $tickets_this_week = TicketVending::where('user_id', $user->id)->whereBetween("created_at", [
            $now->startOfWeek()->format('Y-m-d'),
            $now->endOfWeek()->format('Y-m-d')
        ])->count();
        $tickets_this_week_amount = TicketVending::where('user_id', $user->id)->whereBetween("created_at", [
            $now->startOfWeek()->format('Y-m-d'),
            $now->endOfWeek()->format('Y-m-d')
        ])->sum('ticket_amount');
        return response()->json([
            'status' => 'success',
            'data' => [
                'tickets_this_week' => [
                    'total_tickets' => (int) $tickets_this_week,
                    'total_amount' => (float) $tickets_this_week_amount
                ]
            ]
        ], 200);
    }

    /**
     * Ticket Vending Statistics for Agent(Monthly).
     */
    public function monthly_ticket_vending_statistics(Request $request) : JsonResponse
    {
        $current_time = Carbon::now();
        $init_6am_morning = Carbon::createFromTime(8, 0, 0);
        $init_11_59am_morning = Carbon::createFromTime(11, 59, 59);
        if ($current_time->between($init_6am_morning, $init_11_59am_morning)) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'tickets_this_month' => [
                        'total_tickets' => (string) 'NA',
                        'total_amount' => (string) 'NA'
                    ],
                ]
            ], 200);
        }
        $user = $request->user();
        $tickets_this_month = TicketVending::where('user_id', $user->id)->whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->count();
        $tickets_this_month_amount = TicketVending::where('user_id', $user->id)->whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->sum('ticket_amount');
        return response()->json([
            'status' => 'success',
            'data' => [
                'tickets_this_month' => [
                    'total_tickets' => (int) $tickets_this_month,
                    'total_amount' => (float) $tickets_this_month_amount
                ],
            ]
        ], 200);
    }

    /**
     * Ticket Vending Statistics for admin (Daily).
     */
    public function daily_ticket_total_statistics(Request $request) : JsonResponse
    {
        Carbon::setWeekStartsAt(Carbon::MONDAY);
        Carbon::setWeekEndsAt(Carbon::SUNDAY);
        $tickets_today = TicketVending::whereDate('created_at', Carbon::today())->count();
        $tickets_today_amount = TicketVending::whereDate('created_at', Carbon::today())->sum('ticket_amount');
        $discount_total_amount_today = TicketVending::whereDate('created_at', Carbon::today())->sum('amount');
        $now = Carbon::now();
        return response()->json([
            'status' => 'success',
            'data' => [
                'tickets_today' => [
                    'total_tickets' => (int) $tickets_today,
                    'total_amount' =>  number_format($tickets_today_amount, 2),
                    'discount_total_amount' => number_format($discount_total_amount_today, 2)
                ],
                'meta_data' => [
                    'today' => $now,
                ]
            ]
        ], 200);
    }
    /**
     * Ticket Vending Statistics for admin (Weekly).
     */
    public function weekly_ticket_total_statistics(Request $request) : JsonResponse
    {
        Carbon::setWeekStartsAt(Carbon::MONDAY);
        Carbon::setWeekEndsAt(Carbon::SUNDAY);
        $now = Carbon::now();
        $tickets_this_week = TicketVending::whereBetween("created_at", [
            $now->startOfWeek()->startOfDay()->format('Y-m-d\TH:i:s.u\Z'),
            $now->endOfWeek()->endOfDay()->format('Y-m-d\TH:i:s.u\Z')
        ])->count();
        $tickets_this_week_amount = TicketVending::whereBetween("created_at", [
            $now->startOfWeek()->startOfDay()->format('Y-m-d\TH:i:s.u\Z'),
            $now->endOfWeek()->endOfDay()->format('Y-m-d\TH:i:s.u\Z')
        ])->sum('ticket_amount');
        $discount_tickets_this_week_amount = TicketVending::whereBetween("created_at", [
            $now->startOfWeek()->startOfDay()->format('Y-m-d\TH:i:s.u\Z'),
            $now->endOfWeek()->endOfDay()->format('Y-m-d\TH:i:s.u\Z')
        ])->sum('amount');

        return response()->json([
            'status' => 'success',
            'data' => [
                'tickets_this_week' => [
                    'total_tickets' => (int) $tickets_this_week,
                    'total_amount' => number_format($tickets_this_week_amount, 2),
                    'discount_total_amount' => number_format($discount_tickets_this_week_amount, 2)

                ],
                'meta_data' => [
                    'week_start' => $now->startOfWeek()->startOfDay()->format('Y-m-d\TH:i:s.u\Z'),
                    'week_end' => $now->endOfWeek()->endOfDay()->format('Y-m-d\TH:i:s.u\Z'),
                ]
            ]
        ], 200);
    }
    /**
     * Ticket Vending Statistics for admin (Monthly).
     */
    public function monthly_ticket_total_statistics(Request $request) : JsonResponse
    {
        Carbon::setWeekStartsAt(Carbon::MONDAY);
        Carbon::setWeekEndsAt(Carbon::SUNDAY);
        $now = Carbon::now();
        $tickets_this_month = TicketVending::whereBetween('created_at', [
            Carbon::now()->startOfMonth()->startOfDay()->format('Y-m-d\TH:i:s.u\Z'),
            Carbon::now()->endOfMonth()->endOfDay()->format('Y-m-d\TH:i:s.u\Z')
        ])->count();
        $tickets_this_month_amount = TicketVending::whereBetween('created_at', [
            Carbon::now()->startOfMonth()->startOfDay()->format('Y-m-d\TH:i:s.u\Z'),
            Carbon::now()->endOfMonth()->endOfDay()->format('Y-m-d\TH:i:s.u\Z')
        ])->sum('ticket_amount');
        $discount_tickets_this_month_amount = TicketVending::whereBetween('created_at', [
            Carbon::now()->startOfMonth()->startOfDay()->format('Y-m-d\TH:i:s.u\Z'),
            Carbon::now()->endOfMonth()->endOfDay()->format('Y-m-d\TH:i:s.u\Z')
        ])->sum('amount');

        return response()->json([
            'status' => 'success',
            'data' => [
                'tickets_this_month' => [
                    'total_tickets' => (int) $tickets_this_month,
                    'total_amount' => number_format($tickets_this_month_amount, 2),
                    'discount_total_amount' => number_format($discount_tickets_this_month_amount, 2)
                ],
                'meta_data' => [
                    'month_start' => Carbon::now()->startOfMonth()->startOfDay()->format('Y-m-d\TH:i:s.u\Z'),
                    'month_end' => Carbon::now()->endOfMonth()->endOfDay()->format('Y-m-d\TH:i:s.u\Z'),
                ]
            ]
        ], 200);
    }

    /**
     * Ticket Vending Statistics for admin (Last Month).
     */
    public function last_month_ticket_total_statistics(Request $request) : JsonResponse
    {
        Carbon::setWeekStartsAt(Carbon::MONDAY);
        Carbon::setWeekEndsAt(Carbon::SUNDAY);
        $tickets_last_month_amount = TicketVending::whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth()->startOfDay()->format('Y-m-d\TH:i:s.u\Z'),
            Carbon::now()->subMonth()->endOfMonth()->endOfDay()->format('Y-m-d\TH:i:s.u\Z')
        ])->sum('ticket_amount');
        $discount_tickets_last_month_amount = TicketVending::whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth()->startOfDay()->format('Y-m-d\TH:i:s.u\Z'),
            Carbon::now()->subMonth()->endOfMonth()->endOfDay()->format('Y-m-d\TH:i:s.u\Z')
        ])->sum('amount');
        $tickets_last_month = TicketVending::whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth()->startOfDay()->format('Y-m-d\TH:i:s.u\Z'),
            Carbon::now()->subMonth()->endOfMonth()->endOfDay()->format('Y-m-d\TH:i:s.u\Z')
        ])->count();
        return response()->json([
            'status' => 'success',
            'data' => [
                'tickets_last_month' => [
                    'total_tickets' => (int) $tickets_last_month,
                    'total_amount' => number_format($tickets_last_month_amount, 2),
                    'discount_total_amount' => number_format($discount_tickets_last_month_amount, 2)
                ],
                'meta_data' => [
                    'last_month_start' => Carbon::now()->subMonth()->startOfMonth()->startOfDay()->format('Y-m-d\TH:i:s.u\Z'),
                    'last_month_end' => Carbon::now()->subMonth()->endOfMonth()->endOfDay()->format('Y-m-d\TH:i:s.u\Z'),
                ]
            ]
        ], 200);
    }

    /**
     * Today's Ticket Collection Statistics.
     */
    public function today_collection() : JsonResponse
    {
        //Carbon::setWeekStartsAt(Carbon::MONDAY);;
        $tickets_today_amount = TicketVending::whereDate('created_at', Carbon::today())->sum('ticket_amount');
        $ticket_categories = TicketCategory::where('category_status', 'active')->get();
        $today_collection = [];
        foreach ($ticket_categories as $key => $category) {
            $tickets_today_amount_in_category = TicketVending::whereDate('created_at', Carbon::today())->where('ticket_category_id', $category->id)->sum('ticket_amount');
            $tickets_total = TicketVending::whereDate('created_at', Carbon::today())->where('ticket_category_id', $category->id)->count();
            $today_collection[$key]['id'] = $key + 1;
            $today_collection[$key]['category_name'] = $category->category_name;
            $today_collection[$key]['ticket_amount'] = number_format($tickets_today_amount_in_category, 2);
            $today_collection[$key]['total_tickets'] = $tickets_total;
            if ($tickets_today_amount) {
                $today_collection[$key]['percentage'] = number_format($tickets_today_amount_in_category / $tickets_today_amount * 0.01, 2) . '%';
            } else {
                $today_collection[$key]['percentage'] = '0%';
            }
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Retrieved successfully.',
            'type' => 'today',
            'data' => $today_collection
        ]);
    }

    /**
     * Weekly's Ticket Collection Statistics.
     */
    public function weekly_collection() : JsonResponse
    {
        Carbon::setWeekStartsAt(Carbon::MONDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);
        $now = Carbon::now();
        $tickets_today_amount = TicketVending::whereBetween("created_at", [
            $now->startOfWeek()->format('Y-m-d'),
            $now->endOfWeek()->format('Y-m-d')
        ])->sum('ticket_amount');
        $ticket_categories = TicketCategory::where('category_status', 'active')->get();
        $today_collection = [];
        foreach ($ticket_categories as $key => $category) {
            $tickets_today_amount_in_category = TicketVending::whereBetween("created_at", [
                $now->startOfWeek()->format('Y-m-d'),
                $now->endOfWeek()->format('Y-m-d')
            ])->where('ticket_category_id', $category->id)->sum('ticket_amount');
            $tickets_total = TicketVending::whereBetween("created_at", [
                $now->startOfWeek()->format('Y-m-d'),
                $now->endOfWeek()->format('Y-m-d')
            ])->where('ticket_category_id', $category->id)->count();
            //return $tickets_today_amount_in_category;
            $today_collection[$key]['id'] = $key + 1;
            $today_collection[$key]['category_name'] = $category->category_name;
            $today_collection[$key]['ticket_amount'] = number_format($tickets_today_amount_in_category, 2);
            $today_collection[$key]['tickets_total'] = $tickets_total;
            $today_collection[$key]['percentage'] = $tickets_today_amount ? number_format($tickets_today_amount_in_category / $tickets_today_amount * 0.01, 2) . '%' : 0 . '%';
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Retrieved successfully.',
            'type' => 'weekly',
            'data' => $today_collection
        ]);
    }

    /**
     * Monthly's Ticket Collection Statistics.
     */
    public function monthly_collection() : JsonResponse
    {
        //Carbon::setWeekStartsAt(Carbon::MONDAY);
        $tickets_today_amount = TicketVending::whereBetween("created_at", [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->sum('ticket_amount');
        $ticket_categories = TicketCategory::where('category_status', 'active')->get();
        $today_collection = [];
        foreach ($ticket_categories as $key => $category) {
            $tickets_today_amount_in_category = TicketVending::whereBetween("created_at", [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])->where('ticket_category_id', $category->id)->sum('ticket_amount');
            $tickets_total = TicketVending::whereBetween("created_at", [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])->where('ticket_category_id', $category->id)->count();
            //return $tickets_today_amount_in_category;
            $today_collection[$key]['id'] = $key + 1;
            $today_collection[$key]['category_name'] = $category->category_name;
            $today_collection[$key]['ticket_amount'] = number_format($tickets_today_amount_in_category, 2);
            $today_collection[$key]['tickets_total'] = $tickets_total;
            $today_collection[$key]['percentage'] = $tickets_today_amount ? number_format($tickets_today_amount_in_category / $tickets_today_amount * 0.01, 2) . '%' : 0 . '%';
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Retrieved successfully.',
            'type' => 'monthly',
            'data' => $today_collection
        ]);
    }

    /**
     * Get Tickets by Agent ID resource.
     *
     * Authorization header is required to be set to Bearer `<token>` <br>
     * Return list of all ticket vending that been vended by the agent. <br>
     * Additional Query parameter are `limit` and `offset`. <br>
     * Additional Query parameter `?query=all`, return all resources
     * without pagination.
     */
    public function tickets_by_agent(Request $request, $ticket_agent_id) : JsonResponse
    {
        $limit = 1000;
        $offset = 0;
        if ($request->has('limit')) {
            $limit = $request->get('limit');
        }
        if ($request->has('offset')) {
            $offset = $request->get('offset');
        }
        $ticket_vending = TicketVending::where('ticket_agent_id', $ticket_agent_id)->latest()->offset($offset)->limit($limit)->get();
        if ($request->has('query') && $request->get('query') === 'all') {
            $ticket_vending = TicketVending::where('ticket_agent_id', $ticket_agent_id)->latest()->get();
        }
        $total_number_of_records = TicketVending::where('ticket_agent_id', $ticket_agent_id)->count();
        if (!count($ticket_vending)) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'ticket_vending_data' => [],
                    'total_number_of_records' => (int) $total_number_of_records
                ],
            ], 200);
        }
        return response()->json([
            'status' => 'success',
            'data' => [
                'ticket_vending_data' => TicketVendingResource::collection($ticket_vending),
                'total_number_of_records' => (int) $total_number_of_records
            ]
        ]);
    }

    /**
     * Advanced Search in resource.
     *
     * Query parameters `plate_number` or `ticket_category_id`.<br>
     * Additional Query parameters `phone_number`, `ticket_reference_number`, `ticket_agent_id`, `date_from and date_to`
     */
    public function search(Request $request)
    {
        $ticket_vending_query = TicketVending::query();
        $ticket_vending_query->when($request->has('plate_number'), function ($query) use ($request) {
            return $query->where('plate_number', $request->get('plate_number'));
        });
        $ticket_vending_query->when($request->has('ticket_category_id'), function ($query) use ($request) {
            return $query->where('ticket_category_id', $request->get('ticket_category_id'));
        });
        $ticket_vending_query->when($request->has('phone_number'), function ($query) use ($request) {
            return $query->where('phone_number', $request->get('phone_number'));
        });
        $ticket_vending_query->when($request->has('ticket_reference_number'), function ($query) use ($request) {
            return $query->where('ticket_reference_number', $request->get('ticket_reference_number'));
        });
        $ticket_vending_query->when($request->has('ticket_agent_id'), function ($query) use ($request) {
            return $query->where('ticket_agent_id', $request->get('ticket_agent_id'));
        });
        $ticket_vending_query->when($request->has('date_from') && $request->has('date_to'), function ($query) use ($request) {
            return $query->whereBetween('created_at', [$request->get('date_from'), $request->get('date_to')]);
        });
        $ticket_vending_response = $ticket_vending_query->get();

        if (!isset($ticket_vending_response)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request.'
            ]);
        }
        return TicketVendingResource::collection($ticket_vending_response);
    }

    /**
     * Get Ticket sales based on Local Government.
     *
     * Query parameter for sales date range `date_from and date_to`.
     * Additional query parameters limit `limit=5`, `sort_order=asc|desc`, `sort_column=total_lga_sales_amount|total_tickets`
     */
    public function sales_by_local_government(Request $request) : JsonResponse
    {
        //Get all local Governments
        $sort_column = $request->get('sort_column');
        $sort_order = $request->get('sort_order');
        if (!$request->has('sort_column') || !$request->has('sort_order')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sort column and sort order is required'
            ], 500);
        }
        if (!in_array($sort_order, ['desc', 'asc'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid sort order.'
            ], 500);
        }
        if (!in_array($sort_column, ['total_lga_sales_amount', 'total_tickets'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid sort column.'
            ], 500);
        }
        if ($request->has('date_from') && $request->has('date_to')) {
            $date_from = Carbon::parse($request->get('date_from'))->startOfDay()->format('Y-m-d\TH:i:s.u\Z');
            $date_to = Carbon::parse($request->get('date_to'))->endOfDay()->format('Y-m-d\TH:i:s.u\Z');
            if ($request->has('limit')) {
                $limit = $request->get('limit');
                $vending_records = TicketVending::whereBetween('ticket_vendings.created_at', [$date_from, $date_to])->join('central_system_l_g_a_s', 'central_system_l_g_a_s.id', '=', 'ticket_vendings.local_government_area_id')
                    ->select('central_system_l_g_a_s.id as local_government_area_id', 'central_system_l_g_a_s.name as local_government_name', DB::raw('COUNT(*) as total_tickets'), DB::raw('SUM(ticket_amount) as total_lga_sales_amount'))
                    ->groupBy('local_government_area_id')
                    ->orderBy($sort_column, $sort_order)
                    //->orderBy('total_agent_sales_amount', 'desc')
                    ->take($limit)
                    ->get();
            } else {
                $vending_records = TicketVending::whereBetween('ticket_vendings.created_at', [$date_from, $date_to])->join('central_system_l_g_a_s', 'central_system_l_g_a_s.id', '=', 'ticket_vendings.local_government_area_id')
                    ->select('central_system_l_g_a_s.id as local_government_area_id', 'central_system_l_g_a_s.name as local_government_name', DB::raw('COUNT(*) as total_tickets'), DB::raw('SUM(ticket_amount) as total_lga_sales_amount'))
                    ->groupBy('local_government_area_id')
                    ->orderBy($sort_column, $sort_order)
                    ->get();
            }
        } else {
            $vending_records = TicketVending::whereDate('ticket_vendings.created_at', Carbon::today())->join('central_system_l_g_a_s', 'central_system_l_g_a_s.id', '=', 'ticket_vendings.local_government_area_id')
                ->select('central_system_l_g_a_s.id as local_government_area_id', 'central_system_l_g_a_s.name as local_government_name', DB::raw('COUNT(*) as total_tickets'), DB::raw('SUM(ticket_amount) as total_lga_sales_amount'))
                ->groupBy('local_government_area_id')
                ->orderBy($sort_column, $sort_order)
                ->get();
        }
        $formatted_records = $vending_records->map(function ($record) {
            $record->formatted_total_lga_sales_amount = "₦" . number_format($record->total_lga_sales_amount, 2);
            return $record;
        });
        return response()->json(['status' => 'success', 'data' => $formatted_records]);
    }

    /**
     * Get Ticket sales based on Local Government and Category.
     *
     * Query parameter for sales date range `date_from and date_to, local_government_area_id`.
     */
    public function sales_by_local_government_and_ticket_categories(Request $request) :JsonResponse
    {
        $local_government_area_id = $request->get('local_government_area_id');
        //$ticket_category_id = $request->get('ticket_category_id');
        if (!$local_government_area_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Local government ID is required.'
            ], 500);
        }
        $local_government = CentralSystemLGA::find($local_government_area_id);
        if (!$local_government) {
            return response()->json([
                'status' => 'error',
                'message' => 'Local government ID not found.'
            ], 404);
        }
        if ($request->has('date_from') && $request->has('date_to')) {
            $date_from = Carbon::parse($request->get('date_from'))->startOfDay()->format('Y-m-d\TH:i:s.u\Z');
            $date_to = Carbon::parse($request->get('date_to'))->endOfDay()->format('Y-m-d\TH:i:s.u\Z');
            $date_range = $date_from . ' - ' . $date_to;
            $ticket_vendings = TicketVending::with('ticket_category')
                ->select('ticket_category_id', DB::raw('SUM(ticket_amount) as total_amount'), DB::raw('COUNT(*) as total_tickets'))
                ->whereBetween('ticket_vendings.created_at', [$date_from, $date_to])->where('local_government_area_id', $local_government_area_id)->groupBy('ticket_category_id')
                ->orderBy('total_amount', 'desc')
                ->get();
        } else {
            $date_range = Carbon::today();
            $ticket_vendings = TicketVending::with('ticket_category')->select('ticket_category_id', DB::raw('SUM(ticket_amount) as total_amount'), DB::raw('COUNT(*) as total_tickets'))
                ->whereDate('ticket_vendings.created_at', $date_range)->where('local_government_area_id', $local_government_area_id)->groupBy('ticket_category_id')
                ->orderBy('total_amount', 'desc')
                ->get();
        }
        $results = [];
        foreach ($ticket_vendings as $sale) {
            $result = [
                'category_name' => $sale->ticket_category->category_name,
                'total_amount' => "₦" . number_format($sale->total_amount, 2),
                'total_tickets' => $sale->total_tickets
            ];
            $results[] = $result;
        }
        return response()->json([
            'status' => 'success',
            'data' => [
                'local_government_area' => $local_government->name ?? '',
                'date' => $date_range,
                'output' => $results
            ],
        ]);
    }

    /**
     * Get Ticket sales statistics based on Local Government and Category.
     *
     * Query parameter for sales date range `date_from and date_to`.
     */
    public function sales_statistics_by_local_government_and_ticket_categories(Request $request) :JsonResponse
    {
        if ($request->has('date_from') && $request->has('date_to')) {
            $date_from = Carbon::parse($request->get('date_from'))->startOfDay()->format('Y-m-d\TH:i:s.u\Z');
            $date_to = Carbon::parse($request->get('date_to'))->endOfDay()->format('Y-m-d\TH:i:s.u\Z');
            $ticket_vendings = TicketVending::with('ticket_category', 'local_government')
                ->select('local_government_area_id', 'ticket_category_id', DB::raw('SUM(ticket_amount) as total_amount'), DB::raw('COUNT(*) as total_tickets'))
                ->whereBetween('ticket_vendings.created_at', [$date_from, $date_to])->groupBy('local_government_area_id', 'ticket_category_id')
                ->orderBy('total_amount', 'desc')
                ->get();
        } else {
            $ticket_vendings = TicketVending::with('ticket_category', 'local_government')
                ->select('local_government_area_id', 'ticket_category_id', DB::raw('SUM(ticket_amount) as total_amount'), DB::raw('COUNT(*) as total_tickets'))
                ->whereDate('ticket_vendings.created_at', Carbon::today())
                ->groupBy('local_government_area_id', 'ticket_category_id')
                ->orderBy('total_amount', 'desc')
                ->get();
        }
        $results = [];
        foreach ($ticket_vendings as $sale) {
            //return $sale;
            if ($sale->local_government_area_id) {
                $local_government = $sale->local_government->name;
                $category_name = $sale->ticket_category->category_name;
                $totat_amount = $sale->total_amount;
                $total_tickets = $sale->total_tickets;
                if (!isset($results[$local_government])) {
                    $results[$local_government] = [];
                }
                $results[$local_government][$category_name] = [
                    'amount' => "₦" . number_format($totat_amount, 2),
                    'total_tickets' => $total_tickets,
                ];
            }
        }
        return response()->json([
            'status' => 'success',
            'data' => $results,
        ]);
    }

    /**
     * Get Ticket sales based on Zones and Category.
     *
     * Query parameter for sales date range `date_from and date_to, zone`.
     */
    public function sales_by_zone_and_ticket_categories(Request $request) : JsonResponse
    {
        $zone = $request->get('zone');
        //$ticket_category_id = $request->get('ticket_category_id');
        if (!$zone) {
            return response()->json([
                'status' => 'error',
                'message' => 'Zone is required.'
            ], 500);
        }
        //return CentralSystemLGA::select('lga_zone')->pluck('lga_zone')->toArray();
        $lga_zone = CentralSystemLGA::where('lga_zone', $zone)->first();
        if (!$lga_zone) {
            return response()->json([
                'status' => 'error',
                'message' => 'Zone not found.'
            ], 404);
        }
        if ($request->has('date_from') && $request->has('date_to')) {
            $date_from = Carbon::parse($request->get('date_from'))->startOfDay()->format('Y-m-d\TH:i:s.u\Z');
            $date_to = Carbon::parse($request->get('date_to'))->endOfDay()->format('Y-m-d\TH:i:s.u\Z');
            $date_range = $date_from . ' - ' . $date_to;
            $ticket_vendings = TicketVending::with('ticket_category')
                ->select('ticket_category_id', DB::raw('SUM(ticket_amount) as total_amount'), DB::raw('COUNT(*) as total_tickets'))
                ->whereBetween('ticket_vendings.created_at', [$date_from, $date_to])->where('zone', $zone)->groupBy('ticket_category_id')
                ->orderBy('total_amount', 'desc')
                ->get();
        } else {
            $date_range = Carbon::today();
            $ticket_vendings = TicketVending::with('ticket_category')->select('ticket_category_id', DB::raw('SUM(ticket_amount) as total_amount'), DB::raw('COUNT(*) as total_tickets'))
                ->whereDate('ticket_vendings.created_at', $date_range)->where('zone', $zone)->groupBy('ticket_category_id')
                ->orderBy('total_amount', 'desc')
                ->get();
        }
        $results = [];
        foreach ($ticket_vendings as $sale) {
            $result = [
                'category_name' => $sale->ticket_category->category_name,
                'total_amount' => "₦" . number_format($sale->total_amount, 2),
                'total_tickets' => $sale->total_tickets
            ];
            $results[] = $result;
        }
        return response()->json([
            'status' => 'success',
            'data' => [
                'zone_name' => $zone,
                'date' => $date_range,
                'output' => $results
            ],
        ]);
    }

    /**
     * Get Ticket sales statistics based on Zones and Category.
     *
     * Query parameter for sales date range `date_from and date_to`.
     */
    public function sales_statistics_by_zone_and_ticket_categories(Request $request) :JsonResponse
    {
        if ($request->has('date_from') && $request->has('date_to')) {
            $date_from = Carbon::parse($request->get('date_from'))->startOfDay()->format('Y-m-d\TH:i:s.u\Z');
            $date_to = Carbon::parse($request->get('date_to'))->endOfDay()->format('Y-m-d\TH:i:s.u\Z');
            $ticket_vendings = TicketVending::with('ticket_category', 'lga_zone')
                ->select('zone', 'ticket_category_id', DB::raw('SUM(ticket_amount) as total_amount'), DB::raw('COUNT(*) as total_tickets'))
                ->whereBetween('ticket_vendings.created_at', [$date_from, $date_to])
                ->groupBy('zone', 'ticket_category_id')
                ->orderBy('total_amount', 'desc')
                ->get();
        } else {
            $ticket_vendings = TicketVending::with('ticket_category', 'lga_zone')
                ->select('zone', 'ticket_category_id', DB::raw('SUM(ticket_amount) as total_amount'), DB::raw('COUNT(*) as total_tickets'))
                ->whereDate('ticket_vendings.created_at', Carbon::today())
                ->groupBy('zone', 'ticket_category_id')
                ->orderBy('total_amount', 'desc')
                ->get();
        }
        $results = [];
        foreach ($ticket_vendings as $sale) {
            //return $sale;
            if ($sale->zone) {
                $zone = $sale->zone;
                $category_name = $sale->ticket_category->category_name;
                $totat_amount = $sale->total_amount;
                $total_tickets = $sale->total_tickets;
                if (!isset($results[$zone])) {
                    $results[$zone] = [];
                }
                $results[$zone][$category_name] = [
                    'amount' => "₦" . number_format($totat_amount, 2),
                    'total_tickets' => $total_tickets,
                ];
            }
        }
        return response()->json([
            'status' => 'success',
            'data' => $results,
        ]);
    }
}
