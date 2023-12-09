<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppSettingResource;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

/**
 * @tags App Settings
 */
class AppSettingController extends Controller
{
    /**
     * Return app settings key and value.
     *
     * Authorization header is required to be set to Bearer `<token>` <br>
     */
    public function index()
    {
        $settings = [];
        $app_settings = AppSetting::all();
        foreach ($app_settings as $app_setting) {
            $settings[$app_setting->key] = [
                'id' => $app_setting->id,
                'value' => $app_setting->value,
            ];
        }
        return response()->json([
            'data' => $settings
        ]);
        //return AppSettingResource::collection($app_settings);
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
        $validatedData = $request->validate([
            'APP_NAME' => 'required|string',
            'ALLOW_TICKET_VENDING' => 'required|boolean',
            'ALLOW_TICKET_BULK_VENDING' => 'required|boolean',
            'ALLOW_TICKET_ENFORCEMENT' => 'required|boolean',
            'ALLOW_WALLET_FUND_TRANSFER' => 'required|boolean',
            'ALLOW_TICKETING_ON_SATURDAY' => 'required|boolean',
            'ALLOW_TICKETING_ON_SUNDAY' => 'required|boolean',
            'APP_LOGO' => 'required|string',
            "FORCE_ASSESSMENT_FIFO" => 'required|boolean',
            "DOCUMENT_VERIFICATION_FEES" => 'required|numeric',
            "PAYMENT_VERIFICATION_FEES" => 'required|numeric',
            "DOCUMENT_DOWNLOAD_TIMELINE" => 'required|numeric',
            "DOCUMENT_DOWNLOAD_FEES" => 'required|numeric',
            "RECEIPT_TEMPLATE" => 'required|string',
            "TICKET_REVENUE_ITEM" => 'required|numeric',
            "ORGANIZATION_NAME" => 'required|string',
            "CONTACT_NUMBER" => 'required|string',
            "CONTACT_ADDRESS" => 'required|string',
            "CONTACT_EMAIL" => 'required|email',
            "QT_MERCHANT_CODE" => 'required|string',
            "QT_PAY_ITEM_ID" => 'required|string',
            "QT_DATA_REF" => 'required|string',
            "QT_CLIENT_ID" => 'required|string',
            "QT_SECRET_KEY" => 'required|string',
            "QT_MERCHANT_ID" => 'required|string',
            "QT_ALIAS" => 'required|string',
            "QT_N_SECRET_KEY" => 'required|string',
            "VANSO_SENDER_ID" => 'required|string',
            "VANSO_USERNAME" => 'required|string',
            "VANSO_PASSWORD" => 'required|string',
            "MONIFY_API_KEY" => 'required|string',
            "MONIFY_SECRET_KEY" => 'required|string',
            "MONIFY_BASE_URL" => 'required|string',
            "MONIFY_WALLET_ACCOUNT" => 'required|string',
            "MONIFY_CONTRACT_CODE" => 'required|string',
            "MONIFY_MODE" => 'required|string|in:TEST,LIVE',
            "ALLOW_LOCATION_TRACKING_VENDING" => 'required|string',
            "ALLOW_LOCATION_TRACKING_VALIDATE" => 'required|string',
            "SHOW_ANNOUCEMENT" => 'required|string',
            "ANNOUCEMENT_HEADING" => 'required|string',
            "ENABLE_SUPER_AGENT" => 'required|string',
            "ANNOUCEMENT_URL" => 'required|string',
            ]);
        foreach ($validatedData as $key => $value) {
            $app_setting = AppSetting::where('key', $key)->first();
            if ($app_setting) {
                $app_setting->value = $value;
                $app_setting->save();
            }
        }
        return response()->json([
            'status' => 'success',
            'message' => 'App settings updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
