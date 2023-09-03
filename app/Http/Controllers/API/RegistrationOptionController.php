<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @tags Registration Options Service
 */
class RegistrationOptionController extends Controller
{
    /**
     * Return all the registration options.
     */
    public function __invoke(Request $request)
    {
        $registration_options = [['name' => 'nin', 'description' => 'National Identification Number'], ['name' => 'tin', 'description' => 'Tax Identification Number'], ['name' => 'bvn', 'description' => 'Bank Verification Number'], ['name' => 'no_id', 'description' => 'No Identification']];
        return response()->json(['status' => 'success', 'data' => $registration_options]);
    }
}
