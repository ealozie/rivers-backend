<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopStoreRequest;
use App\Http\Requests\ShopUpdateRequest;
use App\Http\Resources\DemandNoticeAPIResource;
use App\Http\Resources\DemandNoticeItemResource;
use App\Http\Resources\DemandNoticeResource;
use App\Http\Resources\ShopResource;
use App\Models\AppSetting;
use App\Models\AssessmentYear;
use App\Models\Cooperate;
use App\Models\DemandNotice;
use App\Models\DemandNoticeCategoryItem;
use App\Models\DemandNoticeItem;
use App\Models\Individual;
use App\Models\Shop;
use App\Models\TicketAgent;
use App\Models\User;
use App\Traits\ShopAuthorizable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    //use ShopAuthorizable;
    /**
     * Display a listing of the resource.
     *
     * Query paramters `offset` and `limit`.<br>
     * Additonal Query paramters `name`, `shop_type`, `shop_category_id`, `shop_number`, `shop_id`, `date_from and date_to`
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->hasRole('admin')) {
            $shops = Shop::all();
            return response()->json([
                'status' => 'success',
                'message' => 'Shops retrieved successfully.',
                'data' => ShopResource::collection($shops)
            ], 200);
        } else {
            $limit = 20;
            $offset = 0;
            if ($request->has('limit')) {
                $limit = $request->get('limit');
            }
            if ($request->has('offset')) {
                $offset = $request->get('offset');
            }
            $shop_query = Shop::query();
            $shop_query->when($request->has('name'), function ($query) use ($request) {
                return $query->where('name', 'like', "%{$request->get('name')}%");
            });
            $shop_query->when($request->has('shop_type'), function ($query) use ($request) {
                return $query->where('shop_type', $request->get('shop_type'));
            });
            $shop_query->when($request->has('shop_category_id'), function ($query) use ($request) {
                return $query->where('shop_category_id', $request->get('shop_category_id'));
            });
            $shop_query->when($request->has('shop_number'), function ($query) use ($request) {
                return $query->where('shop_number', 'like', "%{$request->get('shop_number')}%");
            });
            // $shop_query->when($request->has('shop_type'), function ($query) use ($request) {
            //     return $query->where('shop_type', $request->get('shop_type'));
            // });
            $shop_query->when($request->has('shop_id'), function ($query) use ($request) {
                return $query->where('shop_id', $request->get('shop_id'));
            });
            $shop_query->when($request->has('date_from') && $request->has('date_to'), function ($query) use ($request) {
                return $query->whereBetween('created_at', [$request->get('date_from'), $request->get('date_to')]);
            });
            $shop_response = $shop_query->where('added_by', $user->id)
                ->offset($offset)
                ->limit($limit)
                ->get();
            $shop_total = Shop::where('added_by', $user->id)->count();
            if (!isset($shop_response)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid request.'
                ], 500);
            }
            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_shops' => $shop_total,
                    'shops' => ShopResource::collection($shop_response),
                ]
            ], 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShopStoreRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['shop_id'] = '3' . date('hi') . mt_rand(11111, 99999);
        $user_id = $validatedData['user_id'];
        $number_prefix = (int) $validatedData['user_id'][0];
        $entity = null;
        switch ($number_prefix) {
            case 1:
                $entity = Individual::where('individual_id', $user_id)->first();
                break;
            case 2:
                $entity = Cooperate::where('cooperate_id', $user_id)->first();
                break;
            default:
                $entity = null;
                break;
        }
        if (!$entity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Entity ID not found.',
            ], 404);
        }
        $user = User::findOrFail($entity->user_id);
        $validatedData['user_id'] = $user->id;
        $validatedData['added_by'] = $request->user()->id;
        //return $validatedData;
        DB::beginTransaction();
        try {
            $shop = Shop::create($validatedData);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Shop not added.',
                'error' => $e->getMessage()
            ], 500);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Shop added successfully.',
            'data' => new ShopResource($shop)
        ]);
    }

    /**
     * Count of the resource.
     *
     * Additional Query paramters `type=daily|monthly|yearly`.
     */
    public function item_count(Request $request)
    {
        if ($request->has('type')) {
            $type = $request->get('type');
            if ($type === 'daily') {
                $count = Shop::daily()->count();
            } elseif ($type === 'monthly') {
                $count = Shop::monthly()->count();
            } elseif ($type === 'yearly') {
                $count = Shop::yearly()->count();
            }
        } else {
            $count = Shop::count();
        }
        return response()->json([
            'status' => 'success',
            'data' => ['count' => $count],
        ], 200);
    }


    /**
     * Shop Payment Items for the specified resource.
     */
    public function shop_payments(Request $request, string $shop_id, $year_id)
    {
        $shop = Shop::where('shop_id', $shop_id)->first();
        if (!$shop) {
            return response()->json([
                'status' => 'error',
                'message' => 'Shop not found.',
            ], 404);
        }
        $demand_notice = DemandNotice::where('demand_notice_category_id', $shop->demand_notice_category_id)
            ->where('year_id', $year_id)
            ->where('demand_noticeable_type', get_class($shop))
            ->where('demand_noticeable_id', $shop->id)
            ->first();
        if (!$demand_notice) {
            return response()->json([
                'status' => 'error',
                'message' => 'No payment found.',
            ], 404);
        }
        $demand_notice_item = DemandNoticeItem::where('demand_notice_id', $demand_notice->id)->where('year_id', $year_id)->get();
        return response()->json([
            'shop' => [
                'shop_id' => $shop->shop_id,
                'name' => $shop->name,
                'location' => $shop->location,
            ],
            'demand_notice_items' => DemandNoticeItemResource::collection($demand_notice_item),
        ], 200);
    }

    /**
     * Verify Payment Receipt for the specified resources.
     */
    public function payment_by_receipt_number(Request $request, $receipt_number)
    {
        $payment_item = DemandNoticeItem::where('payment_receipt_number', $receipt_number)->first();
        return new DemandNoticeItemResource($payment_item);
    }


    /**
     * Payment for the specified resources.
     */
    public function payment(Request $request)
    {
        $requestData = $request->validate([
            'payable_ids' => 'required|array',
            'payable_ids*' => 'required',
            'shop_id' => 'required',
            'year_id' => 'required',
        ]);
        //return $requestData['payable_ids'];
        $payment_amount = DemandNoticeItem::whereIn('id', $requestData['payable_ids'])->sum('amount');
        //return $requestData['payable_ids'][0];
        $demand_notice_item = DemandNoticeItem::findOrFail($requestData['payable_ids'][0]);
        $user = $request->user();
        $ticket_agent = TicketAgent::where('user_id', $user->id)->first();
        if (!$ticket_agent) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not allowed to process this payment. Contact the administrator for assistance.',
            ], 403);
        }
        if ($ticket_agent->agent_status !== 'active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Your account has been placed on hold.'
            ], 401);
        }
        //check user wallet
        if ($ticket_agent->wallet_balance < $payment_amount) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient wallet balance.',
                'data' => [
                    'wallet_balance' => number_format($ticket_agent->wallet_balance, 2),
                    'payment_amount' => number_format($payment_amount, 2),
                ]
            ], 403);
        }

        DB::beginTransaction();
        try {
            $demand_notice = DemandNotice::findOrFail($demand_notice_item->demand_notice_id);
            $year = AssessmentYear::find($demand_notice_item->year_id);
            //$receipt_number = 'REVPAY-'.time();
            $receipt_number_payable = AppSetting::where('key', 'RECEIPT_NUMBER_PREFIX')->first() ? AppSetting::where('key', 'RECEIPT_NUMBER_PREFIX')->first()->value . time() : time();
            $shop = Shop::find($demand_notice->demand_noticeable_id);
            $response = $shop->payments()->create([
                'user_id' => $user->id,
                'receipt_number' => $receipt_number_payable,
                'amount' => $payment_amount,
                'year' => $year->year,
                'is_partial_payment' => false,
                'status' => 'completed',
            ]);

            if (count($requestData['payable_ids'])) {
                foreach ($requestData['payable_ids'] as $payable_item_id) {
                    $demand_notice_item = DemandNoticeItem::where('id', $payable_item_id)->update([
                        'payment_receipt_number' => $receipt_number_payable,
                        'payment_status' => 'paid',
                    ]);
                }
            }
            // $demand_notice_item = DemandNoticeItem::whereIn('id', $requestData['payable_ids'])->update([
            //     'payment_receipt_number' => 
            //     'payment_status' => 'paid',
            // ]);
            $ticket_agent->decrement('wallet_balance', $payment_amount);
            DB::commit();
            $demand_notice_item_paids = DemandNoticeItem::where('demand_notice_id', $demand_notice->id)->where('payment_status', 'paid')->count();
            $demand_notice_item_count = DemandNoticeItem::where('demand_notice_id', $demand_notice->id)->count();
            if ($demand_notice_item_paids === $demand_notice_item_count) {
                $demand_notice->status == 'Full-Payment';
                $demand_notice->save();
            } else {
                $demand_notice->status == 'Part-Payment';
                $demand_notice->save();
            }
            // $mobile_number = ltrim($individual->user->phone_number, "0");
            // $owner_name = $shop->name;
            // $amount = $payment_amount;
            // $current_date = $response->created_at->format('F d, Y');
            // $message = "Hello {$owner_name}, your payment of (N{$amount}) was successful. {$current_date}. Thank you.";
            // $this->send_sms_process_message("+234" . $mobile_number, $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Payment was successful.',
        ], 200);
    }

    /**
     * Payment Validation the specified resource.
     */
    public function payment_validation(Request $request)
    {
        $requestData = $request->validate([
            'shop_id' => 'required|min:10|max:10',
            'year_id' => 'required|integer',
        ]);
        $shop = Shop::where('shop_id', $requestData['shop_id'])
            ->first();
        if (!$shop) {
            return response()->json([
                'status' => 'error',
                'message' => 'Shop not found.',
            ], 404);
        }
        if (!$shop->demand_notice_category_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Shop not associated with a demand notice category.',
            ], 404);
        }
        $demand_notice = DemandNotice::where('demand_notice_category_id', $shop->demand_notice_category_id)
            ->where('year_id', $requestData['year_id'])
            ->where('demand_noticeable_type', get_class($shop))
            ->where('demand_noticeable_id', $shop->id)
            ->first();
        if (!$demand_notice) {
            $requestData['demand_notice_number'] = 'DN-' . date('Y') . '-' . date('md') . '-' . date('hi') . random_int(100000, 999999);
            $requestData['generated_by'] = $request->user()->id;
            $requestData['user_id'] = $request->user()->id;
            $requestData['demand_notice_category_id'] = $shop->demand_notice_category_id;
            $demand_notice = $shop->demand_notices()->create($requestData);
            $demand_notice_category_items = DemandNoticeCategoryItem::where('demand_notice_category_id', $shop->demand_notice_category_id)->get();
            foreach ($demand_notice_category_items as $demand_notice_category_item) {
                $demand_notice_item = new DemandNoticeItem();
                // new updates
                $demand_notice_item->demand_notice_id = $demand_notice->id;
                $demand_notice_item->year_id = $requestData['year_id'];
                $demand_notice_item->revenue_item_id = $demand_notice_category_item->revenue_item_id;
                $demand_notice_item->amount = $demand_notice_category_item->amount;
                $demand_notice_item->payment_status = 'pending';
                $demand_notice_item->save();
                
            }
        }
        $demand_notice_items = $demand_notice->demand_notice_items()->where('payment_status', 'pending')->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Payable items.',
            'data' => [
                'payables' => DemandNoticeItemResource::collection($demand_notice_items),
                'shop' => new ShopResource($shop),
            ]
        ], 200);
    }

    /**
     * Payment Receipt for the specified resource.
     */
    public function payment_receipt(Request $request, string $shop_id)
    {
        $shop = Shop::where('shop_id', $shop_id)
            ->first();
        if (!$shop) {
            return response()->json([
                'status' => 'error',
                'message' => 'Shop not found.',
            ], 404);
        }
        $per_page = 20;
        $user = $request->user();
        if ($user->hasRole('admin')) {
            $demand_notices = DemandNotice::where('demand_noticeable_type', get_class($shop))
                ->where('demand_noticeable_id', $shop->id)
                ->paginate();
            return DemandNoticeResource::collection($demand_notices);
        } else {
            $limit = 20;
            $offset = 0;
            if ($request->has('limit')) {
                $limit = $request->get('limit');
            }
            if ($request->has('offset')) {
                $offset = $request->get('offset');
            }
            $demand_notices = DemandNotice::query();
            $demand_notices->where('demand_noticeable_type', get_class($shop));
            $demand_notices->where('demand_noticeable_id', $shop->id);
            $demand_notices_count = DemandNotice::where('demand_noticeable_type', get_class($shop))->where('demand_noticeable_id', $shop->id)->count();
            if (!isset($demand_notices)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid request.'
                ], 500);
            }
            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_demand_notice_count' => $demand_notices_count,
                    'demand_notices' => DemandNoticeAPIResource::collection($demand_notices->offset($offset)->limit($limit)->get()),
                ]
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shop = Shop::find($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Shop retrieved successfully.',
            'data' => new ShopResource($shop)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShopUpdateRequest $request, string $id)
    {
        $validatedData = $request->validated();
        $shop = Shop::find($id);
        $shop->update($validatedData);
        return response()->json([
            'status' => 'success',
            'message' => 'Shop updated successfully.',
            'data' => new ShopResource($shop)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Get Shop by User ID or User Unique ID.
     */
    public function show_by_user_id(string $user_id_or_unique_id)
    {
        $user = User::where('id', $user_id_or_unique_id)->orWhere('unique_id', $user_id_or_unique_id)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User ID not found.',
            ], 404);
        }
        $shop = Shop::where('user_id', $user->id)->get();
        if (!count($shop)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Shop not found.',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Shop retrieved successfully.',
            'data' => ShopResource::collection($shop)
        ]);
    }

    /**
     * Advanced Search in resource.
     *
     * Query paramters `name` or `shop_number`.<br>
     * Additonal Query paramters `market_name_id`, `local_government_area_id`, `business_category_id`, `business_sub_category_id`, `classification_id`, `shop_id`, `date_from and date_to`
     */
    public function search(Request $request)
    {
        $per_page = 20;
        if ($request->has('name')) {
            $query_request = $request->get('name');
            $individual_registrations = Shop::with('user')->where('name', 'like', "%{$query_request}%")->paginate($per_page);
        }
        if ($request->has('shop_number')) {
            $query_request = $request->get('shop_number');
            $individual_registrations = Shop::with('user')->where('shop_number', 'like', "%{$query_request}%")->paginate($per_page);
        }
        if ($request->has('market_name_id')) {
            $query_request = $request->get('market_name_id');
            $individual_registrations = Shop::with('user')->where('market_name_id', $query_request)->paginate($per_page);
        }
        if ($request->has('local_government_area_id')) {
            $query_request = $request->get('local_government_area_id');
            $individual_registrations = Shop::with('user')->where('local_government_area_id', $query_request)->paginate($per_page);
        }
        if ($request->has('business_category_id')) {
            $query_request = $request->get('business_category_id');
            $individual_registrations = Shop::with('user')->where('business_category_id', $query_request)->paginate($per_page);
        }
        if ($request->has('business_sub_category_id')) {
            $query_request = $request->get('business_sub_category_id');
            $individual_registrations = Shop::with('user')->where('business_sub_category_id', $query_request)->paginate($per_page);
        }
        if ($request->has('classification_id')) {
            $query_request = $request->get('classification_id');
            $individual_registrations = Shop::with('user')->where('classification_id', $query_request)->paginate($per_page);
        }

        if ($request->has('shop_id')) {
            $query_request = $request->get('shop_id');
            $individual_registrations = Shop::with('user')->where('shop_id', $query_request)->paginate($per_page);
        }
        if ($request->has('date_from') && $request->has('date_to')) {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            $individual_registrations = Shop::with('user')->whereBetween('created_at', [$date_from, $date_to])->paginate($per_page);
        }
        if (!isset($individual_registrations)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request.'
            ]);
        }
        return ShopResource::collection($individual_registrations);
    }
}
