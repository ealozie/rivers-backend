<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssessmentStoreRequest;
use App\Http\Requests\AssessmentUpdateRequest;
use App\Http\Resources\AssessmentResource;
use App\Http\Resources\CooperateResource;
use App\Http\Resources\IndividualResource;
use App\Http\Resources\PropertyResource;
use App\Http\Resources\ShopResource;
use App\Http\Resources\SignageResource;
use App\Http\Resources\UserResource;
use App\Models\Assessment;
use App\Models\CommercialVehicle;
use App\Models\Cooperate;
use App\Models\Individual;
use App\Models\Property;
use App\Models\Shop;
use App\Models\Signage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @tags Assessment Service
 */
class AssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assessments = Assessment::paginate();
        return AssessmentResource::collection($assessments);
    }

    /**
     * Verify Assessment Entity ID.
     */
    public function validate_assessment_entity_id(Request $request)
    {
        $validatedData = $request->validate([
            'assessment_entity_id' => 'required',
        ]);
        $entity_id = $validatedData['assessment_entity_id'][0];
        $assessment_entity_id = $validatedData['assessment_entity_id'];
        if ($entity_id == 1) {
            $individual = Individual::where('individual_id', $assessment_entity_id)->first();
            if (!$individual) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No record found'
                ]);
            }
            $user = User::find($individual->user_id);
            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => new UserResource($user),
                    'individual' => new IndividualResource($individual)
                ]
            ]);
        } elseif ($entity_id == 2) {
            $cooperate = Cooperate::where('cooperate_id', $assessment_entity_id)->first();
            if (!$cooperate) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No record found'
                ]);
            }
            $user = User::find($cooperate->user_id);
            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => new UserResource($user),
                    'cooperate' => new CooperateResource($cooperate)
                ]
            ]);
        } elseif ($entity_id == 3) {
            $shop = Shop::where('shop_id', $assessment_entity_id)->first();
            if (!$shop) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No record found'
                ]);
            }
            $user = User::find($shop->user_id);
            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => new UserResource($user),
                    'shop' => new ShopResource($shop)
                ]
            ]);
        } elseif ($entity_id == 4) {
            $property = Property::where('property_id', $assessment_entity_id)->first();
            if (!$property) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No record found'
                ]);
            }
            $user = User::find($property->user_id);
            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => new UserResource($user),
                    'property' => new PropertyResource($property)
                ]
            ]);
        } elseif ($entity_id == 5) {
            $signage = Signage::where('signage_id', $assessment_entity_id)->first();
            if (!$signage) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No record found'
                ]);
            }
            $user = User::find($signage->user_id);
            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => new UserResource($user),
                    'signage' => new SignageResource($signage)
                ]
            ]);
        } elseif ($entity_id == 6) {
            $vehicle = CommercialVehicle::where('vehicle_id', $assessment_entity_id)->first();
            if (!$vehicle) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No record found'
                ]);
            }
            $user = User::find($vehicle->user_id);
            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => new UserResource($user),
                    'vehicle' => new SignageResource($vehicle)
                ]
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid assessment entity ID'
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AssessmentStoreRequest $request)
    {
        $validatedData = $request->validated();
        $auth_user = $request->user();
        // $user = User::where('email', $validatedData['email'])->first();
        // if ($user) {
        //     $validatedData['user_id'] = $user->id;
        // }
        $user = User::where('unique_id', $validatedData['user_id'])->first();
        $validatedData['status'] = 'pending';
        $validatedData['user_id'] = $user->id;
        $validatedData['payment_status'] = 'pending';
        $validatedData['added_by'] = $auth_user->id ?? 0;
        $validatedData['assessment_reference'] = 'ASSESSMENT-' . time() . '-' . rand(1000, 9999);
        $validatedData['entity_id'] = $validatedData['assessment_entity_id'];
        $assessment = Assessment::create($validatedData);
        return new AssessmentResource($assessment);
    }

    /**
     * Return assessment by Receipt number or Assessment Reference number.
     */
    public function indentifier($indentifier)
    {
        $assessment = Assessment::where('assessment_reference', $indentifier)->orWhere('receipt_number', $indentifier)->first();
        return new AssessmentResource($assessment);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $assessment = Assessment::find($id);
        if (!$assessment) {
            return response()->json(['status' => 'error', 'message' => 'Assessment not found',], 404);
        }
        return new AssessmentResource($assessment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AssessmentUpdateRequest $request, string $id)
    {
        $validatedData = $request->validated();
        $auth_user = $request->user();
        $user = User::find($validatedData['user_id']);
        $validatedData['full_name'] = $user->name;
        $validatedData['email'] = $user->email;
        $validatedData['phone_number'] = $user->phone_number;
        $validatedData['added_by'] = $auth_user->id ?? 0;
        $assessment = Assessment::find($id);
        $assessment->update($validatedData);
        return new AssessmentResource($assessment);
    }

    /**
     * Cancel the specified resource.
     */
    public function destroy(string $id)
    {
        $assessment = Assessment::find($id);
        $assessment->status = 'cancelled';
        return response()->json(['status' => 'success', 'message' => 'Assessment Cancelled successfully',], 200);
    }

    /**
     * Get Assessments by User ID or User Unique ID.
     */
    public function show_by_user_id(string $user_id_or_unique_id)
    {
        $user = User::where('unique_id', $user_id_or_unique_id)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User ID not found.',
            ], 404);
        }
        $assessment = Assessment::where('entity_id', $user->unique_id)->get();
        if (!count($assessment)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Assessment not found.',
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Assessment retrieved successfully.',
            'data' => AssessmentResource::collection($assessment)
        ]);
    }

    /**
     * Get Assessment By Reference or Receipt number.
     */
    public function show_by_reference_number(string $reference_number)
    {
        $assessment = Assessment::where('assessment_reference', $reference_number)->orWhere('receipt_number', $reference_number)->first();
        if (!$assessment) {
            return response()->json(['status' => 'error', 'message' => 'Assessment not found',], 404);
        }
        return new AssessmentResource($assessment);
    }

    /**
     * Get Assessment By Phone Number.
     */
    public function show_by_phone_number(string $phone_number)
    {
        $assessment = Assessment::where('phone_number', $phone_number)->first();
        if (!$assessment) {
            return response()->json(['status' => 'error', 'message' => 'Assessment not found',], 404);
        }
        return new AssessmentResource($assessment);
    }
}
