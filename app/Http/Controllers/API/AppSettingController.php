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
    public function __invoke(Request $request)
    {
        $app_logo = AppSetting::where('key', 'APP_LOGO')->first();
        $app_name = AppSetting::where('key', 'APP_NAME')->first();

        return response()->json([
            'app_logo' => $app_logo->value,
            'app_name' => $app_name->value,
        ]);
    }
}
