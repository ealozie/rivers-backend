<?php

namespace App\Http\Controllers\API;

use AWS\CRT\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentHistoryResource;
use App\Http\Resources\PaymentResource;
use App\Jobs\ProcessISWPaymentTransaction;
use App\Models\AppSetting;
use App\Models\Assessment;
use App\Models\CommercialVehicle;
use App\Models\Cooperate;
use App\Models\Individual;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Shop;
use App\Models\Signage;
use App\Models\TicketAgent;
use App\Models\TicketAgentWallet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as FacadesLog;

/**
 * @tags Payment Service
 */
class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $per_page = 100;
        $payments = Payment::latest()->paginate($per_page);
        return PaymentResource::collection($payments);
    }

    /**
     * Payment Collection Statistics.
     */
    public function payment_statistics(Request $request)
    {
        Carbon::setWeekStartsAt(Carbon::MONDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);
        $tickets_today = Payment::whereDate('created_at', Carbon::today())
            ->count();
        $tickets_today_amount = Payment::whereDate('created_at', Carbon::today())
            ->sum('amount');

        //$tickets_this_week = TicketVending::where('user_id', $user->id)->whereDate('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $now = Carbon::now();
        $tickets_this_week = Payment::whereBetween("created_at", [
            $now->startOfWeek()->format('Y-m-d'),
            $now->endOfWeek()->format('Y-m-d')
        ])->count();
        $tickets_this_week_amount = Payment::whereBetween("created_at", [
            $now->startOfWeek()->format('Y-m-d'),
            $now->endOfWeek()->format('Y-m-d')
        ])->sum('amount');
        $tickets_this_week_amount = Payment::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount');

        $tickets_this_month = Payment::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->count();
        $tickets_this_month_amount = Payment::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->sum('amount');
        $tickets_last_month_amount = Payment::whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth()
        ])->sum('amount');
        $tickets_last_month = Payment::whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth()
        ])->count();
        return response()->json([
            'status' => 'success',
            'data' => [
                'payment_today' => [
                    'total_payment_count' => (int) $tickets_today,
                    'total_amount' => (float) $tickets_today_amount,
                    // 'total_tickets' => (int) 0,
                    // 'total_amount' => (float) 0.00
                ],
                'payment_this_week' => [
                    'total_payment_count' => (int) $tickets_this_week,
                    'total_amount' => (float) $tickets_this_week_amount
                    // 'total_payment' => (int) 0,
                    // 'total_amount' => (float) 0.00

                ],
                'payment_this_month' => [
                    'total_payment_count' => (int) $tickets_this_month,
                    'total_amount' => (float) $tickets_this_month_amount
                    //'total_tickets' => (int) 0,
                    //'total_amount' => (float) 0.00
                ],
                'payment_last_month' => [
                    'total_payment_count' => (int) $tickets_last_month,
                    'total_amount' => (float) $tickets_last_month_amount
                ]
            ]
        ], 200);
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
                $count = Payment::daily()->count();
            } elseif ($type === 'monthly') {
                $count = Payment::monthly()->count();
            } elseif ($type === 'yearly') {
                $count = Payment::yearly()->count();
            }
        } else {
            $count = Payment::count();
        }
        return response()->json([
            'status' => 'success',
            'data' => ['count' => $count],
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $requestData = $request->all();
        $requestData['user_id'] = $request->user()->id;
        Payment::create($requestData);
        return response()->json([
            'status' => 'success',
            'message' => 'Payment logged successfully.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $id;
    }

    /**
     * Get Payment History by unique id and year.
     */
    public function payment_history($unique_id, $year)
    {
        $payments = Payment::where('customer_reference', $unique_id)
            ->where('payment_year', $year)->get();
        if (!count($payments)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No payment record found.',
                'data' => [],
            ]);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Payment record(s) found.',
            'data' => PaymentHistoryResource::collection($payments)
        ]);
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

    /**
     * Get Payments by User ID or User Unique ID.
     *
     * Additional Query parameter are `limit` and `offset`. <br>
     */
    public function show_by_user_id(Request $request, string $user_id_or_unique_id)
    {
        $limit = 10;
        $offset = 0;

        if ($request->has('limit')) {
            $limit = $request->get('limit');
        }
        if ($request->has('offset')) {
            $offset = $request->get('offset');
        }

        // $user = User::where('unique_id', $user_id_or_unique_id)->first();
        // if (!$user) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'User ID not found.',
        //     ], 404);
        // }
        $payments = Payment::where('customer_reference', $user_id_or_unique_id)->latest()->offset($offset)->limit($limit)->get();
        if (!count($payments)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment not found.',
            ], 404);
        }
        $total_payments = Payment::where('customer_reference', $user_id_or_unique_id)->count();
        return response()->json([
            'status' => 'success',
            'message' => 'Payments retrieved successfully.',
            'total_number_of_records' => (int) $total_payments,
            'data' => PaymentResource::collection($payments)
        ]);
    }

    /**
     * Get Payment By Customer Reference or Payment Reference.
     */
    public function show_by_reference_number(string $reference_number)
    {
        $payment = Payment::where('customer_reference', $reference_number)->orWhere('payment_reference', $reference_number)->first();
        if (!$payment) {
            return response()->json(['status' => 'error', 'message' => 'Payment not found',], 404);
        }
        return new PaymentResource($payment);
    }

    public static function sanitize_data($string)
    {
        //return preg_replace('/[^A-Za-z0-9@._-]/', '', $string);
        return preg_replace('/[^A-Za-z0-9@._ -]/', '', $string);
    }

    /**
     * Interswitch Payment Notification  & Customer Data Validation.
     */
    public function interswitch_payment_notification_data_validation(Request $request)
    {

        $app_settings = AppSetting::where('key', 'PAYMENT_IP_ADDRESS_ALLOWED')->first();
        if (!$app_settings) {
            return response()->json([
                'status' => 'error',
                'message' => "PAYMENT_IP_ADDRESS_ALLOWED key not found."
            ], 404);
        }
        logger("Ip Address Used: " . $request->ip());
        $ip_addresses = $app_settings->value;
        $valid_ip_addresses = explode(',', $ip_addresses);
        if (!in_array($request->ip(), $valid_ip_addresses)) {
            return response()->json([
                'status' => 'error',
                'message' => 'IP address is not authorized.'
            ], 404);
        }
        $request_xml_data = trim($request->getContent());
        try {
            logger($request_xml_data);
        } catch (\Exception $e) {
            logger('Failed to log.');
        }
        // Regular expression to check for special characters at the beginning or end of the XML
        if (preg_match('/^[^<>]*<\?xml/', $request_xml_data) && preg_match('/<\/[^<>]+>[^<>]*$/', $request_xml_data)) {
            $response = "
            <PaymentNotificationResponse>
                <Payments>
                    <Payment>
                        <Status>101</Status>
                    </Payment>
                </Payments>
            </PaymentNotificationResponse>";
            return response($response, status: 400)->header('Content-Type', 'text/xml');
        }
        /*Check for invalid XML Structure*/
        $document_object = new \DOMDocument();
        libxml_use_internal_errors(true);
        //return $document_object->loadXML($request_xml_data);
        if (!$document_object->loadXML($request_xml_data)) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            $error_messages = [];
            foreach ($errors as $error) {
                $error_messages[] = sprintf("Line %d: %s", $error->line, trim($error->message));
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid XML format',
                'data' => $error_messages,
            ], 400);
        }
        //Check for XML without opening and closing tags
        // if (preg_match('/<\w+>.*?<\/\w+>|<\w+\/>/', $request_xml_data)) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Invalid opening and closing tag.',
        //     ], 400);
        // }
        $xml_tag_check = new \SimpleXMLElement($request_xml_data);
        //return $xml_tag_check
        $namespaces = $xml_tag_check->getDocNamespaces(true);
        // Check if any namespaces exist
        // if (!empty($namespaces)) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Namespaces are present in the XML'
        //     ], 400);
        // }
        $requestData = simplexml_load_string($request_xml_data, "SimpleXMLElement", LIBXML_NOCDATA);
        $json_encoded_data = json_encode($requestData);
        //return $xml_tag_check->getName();
        $paymentData = json_decode($json_encoded_data, true);
        try {
            //return $paymentData;
            if ($xml_tag_check->getName() == 'PaymentNotificationRequest') {
                //Check if it tied to a customer;
                $customer_reference = $paymentData['Payments']['Payment']['CustReference'] ?? '';
                $amount = (float) $paymentData['Payments']['Payment']['Amount'];
                $merchant_reference = $paymentData['Payments']['Payment']['MerchantReference'] ?? '';
                //return $merchant_reference;

                $number_prefix = $customer_reference[0];
                $entity = null;
                switch ($number_prefix) {
                    case '9':
                        $entity = User::where('unique_id', $customer_reference)->first();
                        break;
                    case '1':
                        $entity = Individual::where('individual_id', $customer_reference)->first();
                        break;
                    case '2':
                        $entity = Cooperate::where('cooperate_id', $customer_reference)->first();
                        break;
                    case '3':
                        $entity = Shop::where('shop_id', $customer_reference)->first();
                        break;
                    case '4':
                        $entity = Property::where('property_id', $customer_reference)->first();
                        break;
                    case '5':
                        $entity = Signage::where('signage_id', $customer_reference)->first();
                        break;
                    case '6':
                        $entity = CommercialVehicle::where('vehicle_id', $customer_reference)->first();
                        break;
                    case 'A':
                        $entity = Assessment::where('assessment_reference', $customer_reference)->first();
                        break;
                    default:
                        $entity = null;
                        break;
                }
                //9 - agent
                //1 - individual
                //2 - cooperate
                //3 - shop
                //4 - Property
                //5 - Signage
                //6 - commercial vehicle

                $payment_log_id = $paymentData['Payments']['Payment']['PaymentLogId'];
                if (!$entity) {
                    $response = "
            <PaymentNotificationResponse>
                <Payments>
                    <Payment>
                        <PaymentLogId>{$payment_log_id}</PaymentLogId>
                        <Status>1</Status>
                    </Payment>
                </Payments>
            </PaymentNotificationResponse>";
                    return response($response, 200)->header('Content-Type', 'text/xml');
                }
                //Check when amount is zero and customer is invalid
                if (!$entity && $amount == 0.00) {
                    $response = "
            <PaymentNotificationResponse>
                <Payments>
                    <Payment>
                        <PaymentLogId>{$payment_log_id}</PaymentLogId>
                        <Status>1</Status>
                    </Payment>
                </Payments>
            </PaymentNotificationResponse>";
                    return response($response, 200)->header('Content-Type', 'text/xml');
                }
                // $email = self::sanitize_data($user->email);
                // $phone_number = self::sanitize_data($user->phone_number);
                // $names = explode(' ', $user->name);
                // if (count($names) > 1) {
                //     $first_name = self::sanitize_data($names[0]);
                //     $last_name = self::sanitize_data($names[1]);
                // } else {
                //     $first_name = self::sanitize_data($names[0]);
                //     $last_name = '';
                // }
                //Check when amount is zero and customer is invalid
                if ($amount <= 0) {
                    $response = "<PaymentNotificationResponse>
                <Payments>
                    <Payment>
                        <PaymentLogId>{$payment_log_id}</PaymentLogId>
                        <Status>1</Status>
                    </Payment>
                </Payments>
            </PaymentNotificationResponse>";
                    return response($response, 200)->header('Content-Type', 'text/xml');
                }
                //     if ($merchant_reference != 6405) {
                //     $response = "<PaymentNotificationResponse>
                //     <Payments>
                //         <Payment>
                //             <PaymentLogId>{$payment_log_id}</PaymentLogId>
                //             <Status>1</Status>
                //         </Payment>
                //     </Payments>
                // </PaymentNotificationResponse>";
                //         return response($response, 200)->header('Content-Type', 'text/xml');
                //     }
                //This will be a queued process.
                $payment = Payment::firstOrCreate(
                    [
                        'payment_log_id' => $paymentData['Payments']['Payment']['PaymentLogId'] ?? '',
                        'amount' => (float) $paymentData['Payments']['Payment']['Amount'] ?? '',
                        'payment_reference' => $paymentData['Payments']['Payment']['PaymentReference'] ?? '',
                        'receipt_no' => $paymentData['Payments']['Payment']['ReceiptNo'] ?? '',
                    ],
                    [
                        'service_url' => (isset($paymentData['ServiceUrl']) && !is_array($paymentData['ServiceUrl']))
                            ? $paymentData['ServiceUrl']
                            : '',
                        'service_username' => (isset($paymentData['ServiceUsername']) && !is_array($paymentData['ServiceUsername']))
                            ? $paymentData['ServiceUsername']
                            : '',
                        'service_password' => (isset($paymentData['ServicePassword']) && !is_array($paymentData['ServicePassword']))
                            ? $paymentData['ServicePassword']
                            : '',
                        'ftp_url' => (isset($paymentData['FtpUrl']) && !is_array($paymentData['FtpUrl']))
                            ? $paymentData['FtpUrl']
                            : '',
                        'is_repeated' => (isset($paymentData['Payments']['Payment']['IsRepeated']) && !is_array($paymentData['Payments']['Payment']['IsRepeated']))
                            ? $paymentData['Payments']['Payment']['IsRepeated']
                            : '',
                        'product_group_code' => (isset($paymentData['Payments']['Payment']['ProductGroupCode']) && !is_array($paymentData['Payments']['Payment']['ProductGroupCode']))
                            ? $paymentData['Payments']['Payment']['ProductGroupCode']
                            : '',
                        'customer_reference' => (isset($paymentData['Payments']['Payment']['CustReference']) && !is_array($paymentData['Payments']['Payment']['CustReference']))
                            ? $paymentData['Payments']['Payment']['CustReference']
                            : '',

                        'alternate_customer_reference' => (isset($paymentData['Payments']['Payment']['AlternateCustReference']) && !is_array($paymentData['Payments']['Payment']['AlternateCustReference']))
                            ? $paymentData['Payments']['Payment']['AlternateCustReference']
                            : '',
                        'payment_status' => (isset($paymentData['Payments']['Payment']['PaymentStatus']) && !is_array($paymentData['Payments']['Payment']['PaymentStatus']))
                            ? $paymentData['Payments']['Payment']['PaymentStatus']
                            : '',
                        'payment_method' => (isset($paymentData['Payments']['Payment']['PaymentMethod']) && !is_array($paymentData['Payments']['Payment']['PaymentMethod']))
                            ? $paymentData['Payments']['Payment']['PaymentMethod']
                            : '',
                        'terminal_id' => (isset($paymentData['Payments']['Payment']['TerminalId']) && !is_array($paymentData['Payments']['Payment']['TerminalId']))
                            ? $paymentData['Payments']['Payment']['TerminalId']
                            : '',
                        'channel_name' => (isset($paymentData['Payments']['Payment']['ChannelName']) && !is_array($paymentData['Payments']['Payment']['ChannelName']))
                            ? $paymentData['Payments']['Payment']['ChannelName']
                            : '',
                        'location' => (isset($paymentData['Payments']['Payment']['Location']) && !is_array($paymentData['Payments']['Payment']['Location']))
                            ? $paymentData['Payments']['Payment']['Location']
                            : '',
                        'is_reversal' => (isset($paymentData['Payments']['Payment']['IsReversal']) && !is_array($paymentData['Payments']['Payment']['IsReversal']))
                            ? $paymentData['Payments']['Payment']['IsReversal']
                            : '',
                        'payment_date' => (isset($paymentData['Payments']['Payment']['PaymentDate']) && !is_array($paymentData['Payments']['Payment']['PaymentDate']))
                            ? $paymentData['Payments']['Payment']['PaymentDate']
                            : '',
                        'settlement_date' => (isset($paymentData['Payments']['Payment']['SettlementDate']) && !is_array($paymentData['Payments']['Payment']['SettlementDate']))
                            ? $paymentData['Payments']['Payment']['SettlementDate']
                            : '',
                        'institution_id' => (isset($paymentData['Payments']['Payment']['InstitutionId']) && !is_array($paymentData['Payments']['Payment']['InstitutionId']))
                            ? $paymentData['Payments']['Payment']['InstitutionId']
                            : '',
                        'institution_name' => (isset($paymentData['Payments']['Payment']['InstitutionName']) && !is_array($paymentData['Payments']['Payment']['InstitutionName']))
                            ? $paymentData['Payments']['Payment']['InstitutionName']
                            : '',
                        'branch_name' => (isset($paymentData['Payments']['Payment']['BranchName']) && !is_array($paymentData['Payments']['Payment']['BranchName']))
                            ? $paymentData['Payments']['Payment']['BranchName']
                            : '',
                        'bank_name' => (isset($paymentData['Payments']['Payment']['BankName']) && !is_array($paymentData['Payments']['Payment']['BankName']))
                            ? $paymentData['Payments']['Payment']['BankName']
                            : '',
                        'fee_name' => (isset($paymentData['Payments']['Payment']['FeeName']) && !is_array($paymentData['Payments']['Payment']['FeeName']))
                            ? $paymentData['Payments']['Payment']['FeeName']
                            : '',
                        'customer_name' => (isset($paymentData['Payments']['Payment']['CustomerName']) && !is_array($paymentData['Payments']['Payment']['CustomerName']))
                            ? $paymentData['Payments']['Payment']['CustomerName']
                            : '',
                        'other_customer_info' => (isset($paymentData['Payments']['Payment']['OtherCustomerInfo']) && !is_array($paymentData['Payments']['Payment']['OtherCustomerInfo']))
                            ? $paymentData['Payments']['Payment']['OtherCustomerInfo']
                            : '',
                        'collections_account' => (isset($paymentData['Payments']['Payment']['CollectionsAccount']) && !is_array($paymentData['Payments']['Payment']['CollectionsAccount']))
                            ? $paymentData['Payments']['Payment']['CollectionsAccount']
                            : '',
                        'third_party_code' => (isset($paymentData['Payments']['Payment']['ThirdPartyCode']) && !is_array($paymentData['Payments']['Payment']['ThirdPartyCode']))
                            ? $paymentData['Payments']['Payment']['ThirdPartyCode']
                            : '',

                        'payments_items' => isset($paymentData['Payments']['Payment']['PaymentItems']) ? json_encode($paymentData['Payments']['Payment']['PaymentItems']) : '',
                        'item_name' => (isset($paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['ItemName']) && !is_array($paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['ItemName']))
                            ? $paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['ItemName']
                            : '',
                        'item_code' => (isset($paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['ItemCode']) && !is_array($paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['ItemCode']))
                            ? $paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['ItemCode']
                            : '',
                        'item_amount' => (float) $paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['ItemAmount'] ?? '',
                        'lead_bank_code' => (isset($paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['LeadBankCode']) && !is_array($paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['LeadBankCode']))
                            ? $paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['LeadBankCode']
                            : '',
                        'lead_bank_cbn_code' => (isset($paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['LeadBankCbnCode']) && !is_array($paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['LeadBankCbnCode']))
                            ? $paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['LeadBankCbnCode']
                            : '',
                        'lead_bank_name' => (isset($paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['LeadBankName']) && !is_array($paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['LeadBankName']))
                            ? $paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['LeadBankName']
                            : '',
                        'category_code' => (isset($paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['CategoryCode']) && !is_array($paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['CategoryCode']))
                            ? $paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['CategoryCode']
                            : '',
                        'category_name' => (isset($paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['CategoryName']) && !is_array($paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['CategoryName']))
                            ? $paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['CategoryName']
                            : '',
                        'item_quantity' => (isset($paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['ItemQuantity']) && !is_array($paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['ItemQuantity']))
                            ? $paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['ItemQuantity']
                            : '',

                        'bank_code' => (isset($paymentData['Payments']['Payment']['BankCode']) && !is_array($paymentData['Payments']['Payment']['BankCode']))
                            ? $paymentData['Payments']['Payment']['BankCode']
                            : '',
                        'customer_address' => (isset($paymentData['Payments']['Payment']['CustomerAddress']) && !is_array($paymentData['Payments']['Payment']['CustomerAddress']))
                            ? $paymentData['Payments']['Payment']['CustomerAddress']
                            : '',
                        'customer_phone_number' => (isset($paymentData['Payments']['Payment']['CustomerPhoneNumber']) && !is_array($paymentData['Payments']['Payment']['CustomerPhoneNumber']))
                            ? $paymentData['Payments']['Payment']['CustomerPhoneNumber']
                            : '',
                        'depositor_name' => (isset($paymentData['Payments']['Payment']['DepositorName']) && !is_array($paymentData['Payments']['Payment']['DepositorName']))
                            ? $paymentData['Payments']['Payment']['DepositorName']
                            : '',
                        'depositor_slip_number' => (isset($paymentData['Payments']['Payment']['DepositSlipNumber']) && !is_array($paymentData['Payments']['Payment']['DepositSlipNumber']))
                            ? $paymentData['Payments']['Payment']['DepositSlipNumber']
                            : '',
                        'payment_currency' => (isset($paymentData['Payments']['Payment']['PaymentCurrency']) && !is_array($paymentData['Payments']['Payment']['PaymentCurrency']))
                            ? $paymentData['Payments']['Payment']['PaymentCurrency']
                            : '',
                        'original_payment_log_id' => (isset($paymentData['Payments']['Payment']['OriginalPaymentLogId']) && !is_array($paymentData['Payments']['Payment']['OriginalPaymentLogId']))
                            ? $paymentData['Payments']['Payment']['OriginalPaymentLogId']
                            : '',
                        'original_payment_reference' => (isset($paymentData['Payments']['Payment']['OriginalPaymentReference']) && !is_array($paymentData['Payments']['Payment']['OriginalPaymentReference']))
                            ? $paymentData['Payments']['Payment']['OriginalPaymentReference']
                            : '',
                        'teller' => (isset($paymentData['Payments']['Payment']['Teller']) && !is_array($paymentData['Payments']['Payment']['Teller']))
                            ? $paymentData['Payments']['Payment']['Teller']
                            : '',
                    ]
                );
                //Update payment year;
                if (isset($paymentData['Payments']['Payment']['PaymentDate'])) {
                    $payment_date = Carbon::createFromFormat('m/d/Y H:i:s', $paymentData['Payments']['Payment']['PaymentDate']);
                    if ($payment_date) {
                        $payment->payment_year = $payment_date->year;
                        $payment->save();
                    }
                }
                //if its reversal
                if ($paymentData['Payments']['Payment']['IsReversal'] == 'True') {
                    //update the payment status to reversed
                    //Decrement the amount from the user wallet
                }
                if ($paymentData['Payments']['Payment']['PaymentStatus'] == '0' && $payment->is_credited == 0 && $number_prefix == '9') {
                    //update the payment status to paid
                    //Increment the amount to the user wallet
                    $user = User::where('unique_id', $payment->customer_reference)->first();
                    $local_government_item_code = AppSetting::where('key', 'LOCAL_GOVERNMENT_WALLET_ITEM_CODE')->first();
                    $state_government_item_code = AppSetting::where('key', 'STATE_GOVERNMENT_WALLET_ITEM_CODE')->first();
                    if ($user) {
                        $ticket_agent = TicketAgent::where('user_id', $user->id)->first();
                        if ($ticket_agent) {
                            //Add to Wallet
                            $ticket_agent_wallet = new TicketAgentWallet();
                            $ticket_agent_wallet->ticket_agent_id = $ticket_agent->id;
                            $ticket_agent_wallet->user_id = $user->id;
                            $ticket_agent_wallet->amount = $payment->amount;
                            $ticket_agent_wallet->transaction_type = 'credit';
                            if ($local_government_item_code->value == $paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['ItemCode']) {
                                $ticket_agent_wallet->type = 'localgovernment';
                                $ticket_agent->increment('lga_wallet_balance', $payment->amount);
                            } elseif ($state_government_item_code->value == $paymentData['Payments']['Payment']['PaymentItems']['PaymentItem']['ItemCode']) {
                                $ticket_agent_wallet->type = 'stategovernment';
                                $ticket_agent->increment('wallet_balance', $payment->amount);
                            }
                            $ticket_agent_wallet->transaction_status = 'paid';
                            $ticket_agent_wallet->added_by = $user->id;
                            $ticket_agent_wallet->beneficiary_id = $user->id;
                            $ticket_agent_wallet->transaction_reference_number = $payment->payment_reference;
                            $ticket_agent_wallet->save();
                            //Update the payment
                            $payment->is_credited = true;
                            $payment->save();
                        }
                    }
                }
                $payment_log_id = $paymentData['Payments']['Payment']['PaymentLogId'];
                //return xml response
                $response = "
            <PaymentNotificationResponse>
                <Payments>
                    <Payment>
                        <PaymentLogId>{$payment_log_id}</PaymentLogId>
                        <Status>0</Status>
                    </Payment>
                </Payments>
            </PaymentNotificationResponse>";
                return response($response, 200)->header('Content-Type', 'text/xml');
            }

            if ($xml_tag_check->getName() == 'CustomerInformationRequest') {
                $customer_reference = $paymentData['CustReference'] ?? '';
                $merchant_reference = $paymentData['MerchantReference'] ?? '';
                //Check if customer's reference is empty
                //return gettype($customer_reference);
                if (is_array($customer_reference)) {
                    $response_data = "<CustomerInformationResponse>
    <MerchantReference>6405</MerchantReference>
    <Customers>
        <Customer>
            <Status>1</Status>
            <CustReference></CustReference>
            <CustomerReferenceAlternate></CustomerReferenceAlternate>
            <FirstName></FirstName>
            <LastName></LastName>
            <Email></Email>
            <Phone></Phone>
            <ThirdPartyCode></ThirdPartyCode>
            <Amount>0.00</Amount>
        </Customer>
    </Customers>
</CustomerInformationResponse>";
                    return response($response_data, 200)->header('Content-Type', 'text/xml');
                }
                //return $merchant_reference;
                //$user = User::where('unique_id', $customer_reference)->first();
                $number_prefix = $customer_reference[0];
                $entity = null;
                switch ($number_prefix) {
                    case '9':
                        $entity = User::where('unique_id', $customer_reference)->first();
                        break;
                    case '1':
                        $entity = Individual::where('individual_id', $customer_reference)->first();
                        break;
                    case '2':
                        $entity = Cooperate::where('cooperate_id', $customer_reference)->first();
                        break;
                    case '3':
                        $entity = Shop::where('shop_id', $customer_reference)->first();
                        break;
                    case '4':
                        $entity = Property::where('property_id', $customer_reference)->first();
                        break;
                    case '5':
                        $entity = Signage::where('signage_id', $customer_reference)->first();
                        break;
                    case '6':
                        $entity = CommercialVehicle::where('vehicle_id', $customer_reference)->first();
                        break;
                    case 'A':
                        $entity = Assessment::where('assessment_reference', $customer_reference)->first();
                        break;
                    default:
                        $entity = null;
                        break;
                }
                if (!$entity) {
                    $response = "
                    <CustomerInformationResponse>
                        <MerchantReference>{$merchant_reference}</MerchantReference>
                        <Customers>
                            <Customer>
                                <Status>2</Status>
                                <CustReference>{$customer_reference}</CustReference>
                                <CustomerReferenceAlternate></CustomerReferenceAlternate>
                                <FirstName></FirstName>
                                <LastName></LastName>
                                <Email></Email>
                                <Phone></Phone>
                                <ThirdPartyCode></ThirdPartyCode>
                                <Amount>0.00</Amount>
                            </Customer>
                        </Customers>
                    </CustomerInformationResponse>";
                    return response($response, 200)->header('Content-Type', 'text/xml');
                }
                $merchant_reference_settings = AppSetting::where('key', 'MERCHANT_REFERENCE')->first()->value;
                if ($merchant_reference_settings != $merchant_reference) {
                    $response = "
                    <CustomerInformationResponse>
                        <MerchantReference>{$merchant_reference}</MerchantReference>
                        <Customers>
                            <Customer>
                                <Status>1</Status>
                                <CustReference>{$customer_reference}</CustReference>
                                <CustomerReferenceAlternate></CustomerReferenceAlternate>
                                <FirstName></FirstName>
                                <LastName></LastName>
                                <Email></Email>
                                <Phone></Phone>
                                <ThirdPartyCode></ThirdPartyCode>
                                <Amount>0.00</Amount>
                            </Customer>
                        </Customers>
                    </CustomerInformationResponse>";
                    return response($response, 200)->header('Content-Type', 'text/xml');
                }

                if ($number_prefix == '9' && $entity) {
                    $email = self::sanitize_data($entity->email);
                    $phone_number = self::sanitize_data($entity->phone_number);
                    $names = explode(' ', $entity->name);
                    if (count($names) > 1) {
                        $first_name = self::sanitize_data($names[0]);
                        $last_name = self::sanitize_data($names[1]);
                    } else {
                        $first_name = self::sanitize_data($names[0]);
                        $last_name = '';
                    }
                } else {
                    $email = "isw@katsina-revpay.com";
                    $phone_number = "08012345678";
                    if ($number_prefix == '6' && $entity) {
                        //Commercial Vehicle
                        $last_name = self::sanitize_data($entity->plate_number);
                        $first_name = $entity?->vehicle_manufacturer?->name ?? '';
                    }
                    if ($number_prefix == '5' && $entity) {
                        //Signage
                        $last_name = "Signage-" . self::sanitize_data($entity->street_number) . "-" . self::sanitize_data($entity->street_name);
                        $first_name = $entity?->local_government_area?->name ?? "";
                    }
                    if ($number_prefix == '4' && $entity) {
                        //Property
                        $last_name = "Property-" . self::sanitize_data($entity->street_number) . "-" . self::sanitize_data($entity->street_name);
                        $first_name = $entity?->local_government_area?->name ?? "";
                    }
                    if ($number_prefix == '3' && $entity) {
                        //Shop
                        $last_name = self::sanitize_data($entity->name);
                        $first_name = $entity?->market_name?->name ?? $entity->street_name;
                    }
                    if ($number_prefix == '2' && $entity) {
                        //Cooperate
                        $first_name = self::sanitize_data($entity->business_name);
                        $last_name = self::sanitize_data($entity->business_name);
                    }
                    if ($number_prefix == '1' && $entity) {
                        //Individual
                        $first_name = self::sanitize_data($entity->first_name);
                        $last_name = self::sanitize_data($entity->surname);
                    }
                    if ($number_prefix == 'A' && $entity) {
                        $email = $entity->email;
                        $phone_number = $entity->phone_number;
                        $names = explode(' ', $entity->full_name);
                        if (count($names) > 1) {
                            $last_name = self::sanitize_data($names[0]);
                            $first_name = self::sanitize_data($names[1]);
                        } else {
                            $first_name = self::sanitize_data($names[0]);
                            $last_name = '';
                        }
                    }
                }

                $response = "
                    <CustomerInformationResponse>
                        <MerchantReference>{$merchant_reference}</MerchantReference>
                        <Customers>
                            <Customer>
                                <Status>0</Status>
                                <CustReference>{$customer_reference}</CustReference>
                                <CustomerReferenceAlternate></CustomerReferenceAlternate>
                                <FirstName>{$first_name}</FirstName>
                                <LastName>{$last_name}</LastName>
                                <Email>{$email}</Email>
                                <Phone>{$phone_number}</Phone>
                                <ThirdPartyCode></ThirdPartyCode>
                                <Amount>0</Amount>
                            </Customer>
                        </Customers>
                    </CustomerInformationResponse>";
                return response($response, 200)->header('Content-Type', 'text/xml');

            }

        } catch (\Exception $e) {
            if ($xml_tag_check->getName() == 'PaymentNotificationRequest') {
                // $payment_log_id = $paymentData['Payments']['Payment']['PaymentLogId'];
                // $response = "
                // <PaymentNotificationResponse>
                //     <Payments>
                //         <Payment>
                //             <PaymentLogId>{$payment_log_id}</PaymentLogId>
                //             <Status>1</Status>
                //         </Payment>
                //     </Payments>
                // </PaymentNotificationResponse>";
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ]);
                //return response($response, 200)->header('Content-Type', 'text/xml');
            }
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * InterSwitch Payment Webhook.
     */
    public function payment_webhoook_for_wallet(Request $request)
    {
        $requestData = $request->getContent();
        //Log data
        $logFile = fopen(storage_path('logs/isw_payment_webhook.log'), 'a');
        fwrite($logFile, $requestData . "\n");
        fclose($logFile);
        FacadesLog::info($requestData);
        if ($request->hasHeader('X-Interswitch-Signature')) {
            $secret_key = env('ISW_WEBHOOK_PAYMENT_SECRET_KEY');
            $signature = hash_hmac('sha512', $requestData, $secret_key);
            //verify this signature with the one sent in the header
            if ($signature == $request->header('X-Interswitch-Signature')) {
                $requestObject = json_decode($requestData);
                FacadesLog::info('We arrived here');
                ProcessISWPaymentTransaction::dispatch($requestObject);
                FacadesLog::info('We Are good to move from here');
                return response()->json();
            }
        }

    }

    /**
     * Generate Reference number for InterSwitch Payment.
     */
    public function payment_generate_reference(Request $request)
    {
        $validateData = $request->validate([
            'amount' => 'required',
            'payment_gateway' => 'required',
        ]);
        try {
            $payment = new Payment();
            $payment->reference_number = 'ref_smcpt_' . mt_rand(11111, 99999) . date('dY') . mt_rand(11, 99);
            $payment->payment_gateway = $validateData['payment_gateway'];
            $payment->amount = $validateData['amount'];
            $payment->save();
            return response()->json([
                'status' => 'success',
                'data' => [
                    'payment_reference_number' => $payment->reference_number,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing your request. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify InterSwitch Payment using Reference number.
     *
     * Query parameter `ref_number` is required.<br>
     * Authentication Token is required.
     */

    public function payment_reference_verification(Request $request)
    {
        if ($request->has('ref_number')) {
            $reference_number = $request->get('ref_number');
            $payment = Payment::where('reference_number', $reference_number)->first();
            if (!$payment) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Reference number not found'
                ], 404);
            }
            if ($payment->transaction_status == '00') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Transaction was succesful.'
                ]);
            } else if ($payment->transaction_status == 'PAID') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Transaction was succesful.'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction status cannot be determined.'
                ], 404);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'ref_number query is required'
            ], 404);
        }
    }

    /**
     * Advanced Search in resource.
     *
     * Query paramters `customer_name` or `customer_reference`
     * Additonal Query paramters `payment_reference`, `customer_phone_number`, `receipt_number`, `payment_channel`, `date_from and date_to`,  `per_page=30`
     */
    public function search(Request $request)
    {
        $per_page = 20;
        if ($request->has('per_page')) {
            $per_page = $request->get('per_page');
        }

        if ($request->has('customer_name')) {
            $query_request = $request->get('customer_name');
            $response = Payment::where('customer_name', 'like', "%$query_request%")->paginate($per_page);
        }
        if ($request->has('reference_number')) {
            $query_request = $request->get('reference_number');
            $response = Payment::where('reference_number', $query_request)->paginate($per_page);
        }
        if ($request->has('transaction_id')) {
            $query_request = $request->get('transaction_id');
            $response = Payment::where('transaction_id', $query_request)->paginate($per_page);
        }
        if ($request->has('customer_reference')) {
            $query_request = $request->get('customer_reference');
            $response = Payment::where('customer_reference', $query_request)->paginate($per_page);
        }
        if ($request->has('payment_reference')) {
            $query_request = $request->get('payment_reference');
            $response = Payment::where('payment_reference', $query_request)->paginate($per_page);
        }
        if ($request->has('customer_phone_number')) {
            $query_request = $request->get('customer_phone_number');
            $response = Payment::where('customer_phone_number', $query_request)->paginate($per_page);
        }
        if ($request->has('receipt_number')) {
            $query_request = $request->get('receipt_number');
            $response = Payment::where('receipt_no', $query_request)->paginate($per_page);
        }
        if ($request->has('payment_channel')) {
            $query_request = $request->get('payment_channel');
            $response = Payment::where('channel_name', $query_request)->paginate($per_page);
        }
        if ($request->has('date_from') && $request->has('date_to')) {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            $response = Payment::whereBetween('created_at', [$date_from, $date_to])->paginate($per_page);
        }
        if (!isset($response)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request.'
            ]);
        }
        return PaymentResource::collection($response);
        ;
    }
}
