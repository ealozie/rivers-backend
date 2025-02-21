<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndividualRelativeStoreRequest;
use App\Http\Requests\IndividualRelativeUpdateRequest;
use App\Http\Resources\IndividualRelativeResource;
use App\Models\Individual;
use App\Models\IndividualRelative;
use App\Models\User;
use App\Traits\SendSMS;
use Illuminate\Http\Request;

class IndividualRelativeController extends Controller
{
    use SendSMS;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $individual_relatives = IndividualRelative::paginate();
        return IndividualRelativeResource::collection($individual_relatives);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IndividualRelativeStoreRequest $request)
    {
        $requestData = $request->validated();
        if (IndividualRelative::check_for_duplicates($requestData['entity_id'], $requestData['relative_id'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'This relationship already exists.
                '], 200);
        }
        $requestData['verification_code'] = 1234;
        $individual_relative = IndividualRelative::create($requestData);
        $individual = Individual::where('individual_id', $individual_relative->relative_id)->first();
        $user = User::find($individual->user_id);
        if ($user) {
            $phone_number = $user->phone_number;
            $mobile_number = ltrim($phone_number, "0");
            $name = $user->first_name;
            $message = "Hello {$name}, your relative verification code is: " . $requestData['verification_code'];
            $this->send_sms_process_message("+234" . $mobile_number, $message);
        }
        return new IndividualRelativeResource($individual_relative);
    }

    /**
     * Get Relative by Individual or relative ID.
     */
    public function get_relatives($individual_id)
    {
        $individual_relatives = IndividualRelative::where('individual_id', $individual_id)->orWhere('relative_id', $individual_id)->get();
        return IndividualRelativeResource::collection($individual_relatives);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $individual_relative = IndividualRelative::findOrFail($id);
        return new IndividualRelativeResource($individual_relative);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IndividualRelativeUpdateRequest $request, string $id)
    {
        $individual_relative = IndividualRelative::findOrFail($id);
        $individual_relative->update($request->validated());
        return new IndividualRelativeResource($individual_relative);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $individual_relative = IndividualRelative::findOrFail($id);
        $individual_relative->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Individual Relative deleted successfully',
        ], 200);
    }
}
