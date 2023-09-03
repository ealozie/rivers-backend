<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @tags User Facial Biometric Service
 */
class UserFacialBiometricController extends Controller
{
    /**
     * Update User Facial Biometric
     */
    public function __invoke(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'facial_biometric_image_url' => 'required',
        ]);

        //Verify with AWS first before proceed to update

        $user = User::find($validatedData['user_id']);
        $user->facial_biometric_image_url = $validatedData['facial_biometric_image_url'];
        $user->facial_biometric_status = 'verified';
        $user->registration_status = 'completed';
        $user->save();
        return response()->json(['status' => 'success', 'message' => 'Facial Biometric Updated Successfully']);
    }
}
