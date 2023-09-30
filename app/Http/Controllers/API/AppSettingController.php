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
            $settings[$app_setting->key] = $app_setting->value;
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
