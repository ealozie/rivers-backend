<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CommercialVehicle;
use App\Models\Cooperate;
use App\Models\Individual;
use App\Models\Property;
use App\Models\Shop;
use App\Models\Signage;
use App\Models\User;
use App\Traits\SendSMS;
use Illuminate\Http\Request;

class SMSNotificationController extends Controller
{
    use SendSMS;

    /**
     * Send SMS notification
     */
    public function send_sms_via_entity_type_and_id(Request $request)
    {
        $requestData = $request->validate([
            "entity_type" =>
                "required|in:shop,vehicle,property,signage,cooperate,individual",
            "entity_id" => "required",
            "message" => "required",
        ]);
        if ($requestData["entity_type"] == "shop") {
            $shop = Shop::where("shop_id", $requestData["entity_id"])->first();
            $user = User::findOrFail($shop->user_id);
        }
        if ($requestData["entity_type"] == "vehicle") {
            $vehicle = CommercialVehicle::where(
                "vehicle_id",
                $requestData["entity_id"]
            )->first();
            $user = User::findOrFail($vehicle->user_id);
        }
        if ($requestData["entity_type"] == "property") {
            $property = Property::where(
                "property_id",
                $requestData["entity_id"]
            )->first();
            $user = User::findOrFail($property->user_id);
        }
        if ($requestData["entity_type"] == "signage") {
            $signage = Signage::where(
                "signage_id",
                $requestData["entity_id"]
            )->first();
            $user = User::findOrFail($signage->user_id);
        }
        if ($requestData["entity_type"] == "individual") {
            $individual = Individual::where(
                "individual_id",
                $requestData["entity_id"]
            )->first();
            $user = User::findOrFail($individual->user_id);
        }
        if ($requestData["entity_type"] == "cooperate") {
            $cooperate = Cooperate::where(
                "cooperate_id",
                $requestData["entity_id"]
            )->first();
            $user = User::findOrFail($individual->user_id);
        }

        try {
            if ($user) {
                $phone_number = $user->phone_number;
                $mobile_number = ltrim($phone_number, "0");
                //$name = $user->name;
                $message = $requestData["message"];
                $this->send_sms_process_message(
                    "+234" . $mobile_number,
                    $message
                );
                return response()->json(
                    [
                        "status" => "success",
                        "message" =>
                            "SMS notification has been successfully sent.",
                    ],
                    200
                );
            }
        } catch (Exception $e) {
            return response()->json(
                [
                    "status" => "error",
                    "message" => $e->getMessage(),
                ],
                500
            );
        }
    }
}
