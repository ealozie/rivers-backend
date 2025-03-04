<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssessmentStoreRequest;
use App\Http\Requests\AssessmentUpdateRequest;
use App\Http\Resources\AssessmentResource;
use App\Http\Resources\CommercialVehicleResource;
use App\Http\Resources\CooperateResource;
use App\Http\Resources\IndividualResource;
use App\Http\Resources\PropertyResource;
use App\Http\Resources\ShopResource;
use App\Http\Resources\SignageResource;
use App\Http\Resources\UserResource;
use App\Imports\BulkAssessmentImport;
use App\Imports\BulkAssessmentWithoutIDImport;
use App\Models\Assessment;
use App\Models\CommercialVehicle;
use App\Models\Cooperate;
use App\Models\Individual;
use App\Models\Property;
use App\Models\Shop;
use App\Models\Signage;
use App\Models\User;
use App\Traits\AssessmentAuthorizable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

/**
 * @tags Assessment Service
 */
class AssessmentController extends Controller
{
    //use AssessmentAuthorizable;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assessments = Assessment::latest()->paginate();
        $total_paid = Assessment::where('payment_status', 'paid')->count();
        $total_unpaid = Assessment::where('payment_status', 'pending')->count();
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_paid' => $total_paid,
                'total_unpaid' => $total_unpaid,
                'assessments' => AssessmentResource::collection($assessments),
            ]
        ], 200);
        //return AssessmentResource::collection($assessments);
    }

    /**
     * Assessments Statistics resource.
     */
    public function assessments_statistics()
    {
        $current_year = date('Y');
        $total_paid = Assessment::whereYear('created_at', $current_year)
                    ->where('payment_status', 'paid')
                    ->count();
        $total_unpaid = Assessment::whereYear('created_at', $current_year)
                    ->where('payment_status', 'pending')
                    ->count();
        $total_approved = Assessment::whereYear('created_at', $current_year)
                    ->where('status', 'approved')
                    ->count();
        $total_cancelled = Assessment::whereYear('created_at', $current_year)
                    ->where('status', 'cancelled')
                    ->count();
        $total_assessments = Assessment::whereYear('created_at', $current_year)->count();
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_approved_assessments' => $total_approved,
                'total_cancelled_assessments' => $total_cancelled,
                'total_paid_assessments' => $total_paid,
                'total_unpaid_assessments' => $total_unpaid,
                'total_assessments' => $total_assessments,
            ]
        ], 200);
        //return AssessmentResource::collection($assessments);
    }

    /**
     * Assessments by agency listing resource.
     */
    public function assessments_by_agency_id($agency_id)
    {
        $assessments = Assessment::where('agency_id', $agency_id)->paginate();
        return AssessmentResource::collection($assessments);
    }


    /**
     * Assessments by agency and user id listing resource.
     */
    public function assessments_by_agency_user($agency_id, $user_id)
    {
        $assessments = Assessment::where('agency_id', $agency_id)
        ->where('agency_id', $agency_id)->paginate();
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
                    'vehicle' => new CommercialVehicleResource($vehicle)
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
        if (!isset($validatedData['status'])) {
            $validatedData['status'] = 'pending';
        }
        $validatedData['payment_status'] = 'pending';
        $validatedData['added_by'] = $auth_user->id ?? 0;
        $validatedData['assessment_reference'] = time() . rand(1000, 9999);
        $validatedData['entity_id'] = $validatedData['assessment_entity_id'];
        $entity_id = $validatedData['assessment_entity_id'];
        $entity_type = $validatedData['entity_type'];

        if ($entity_type == 'property') {
            $property = Property::where('property_id', $entity_id)->first();
            if ($property) {
                $assessment = $property->assessments()->create($validatedData);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Property not found',
                ], 404);
            }

        }

        if ($entity_type == 'shop') {
            $shop = Shop::where('shop_id', $entity_id)->first();
            if ($shop) {
                $assessment = $shop->assessments()->create($validatedData);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Shop not found',
                ], 404);
            }

        }

        if ($entity_type == 'individual') {
            $individual = Individual::where('individual_id', $entity_id)->first();
            if ($individual) {
                $assessment = $individual->assessments()->create($validatedData);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Individual not found',
                ], 404);
            }
        }

        if ($entity_type == 'cooperate') {
            $cooperate = Cooperate::where('cooperate_id', $entity_id)->first();
            if ($cooperate) {
                $assessment = $cooperate->assessments()->create($validatedData);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cooperate not found',
                ], 404);
            }
        }

        if ($entity_type == 'signage') {
            $signage = Signage::where('signage_id', $entity_id)->first();
            if ($signage) {
                $assessment = $signage->assessments()->create($validatedData);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Signage not found',
                ], 404);
            }
        }

        if ($entity_type == 'vehicle') {
            $vehicle = CommercialVehicle::where('vehicle_id', $entity_id)->first();
            if ($vehicle) {
                $assessment = $vehicle->assessments()->create($validatedData);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vehicle not found',
                ], 404);
            }
        }

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
        // $validatedData['full_name'] = $user->name;
        // $validatedData['email'] = $user->email;
        // $validatedData['phone_number'] = $user->phone_number;
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
        $assessment->save();
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
     * Get Assessments by Entity Unique 10 digit ID.
     */
    public function assessment_by_entity_id(string $entity_id)
    {
        $assessment = Assessment::where('entity_id', $entity_id)->get();
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
     * Assessments payment status verification by entity ID.
     */
    public function assessment_payment_verification(string $entity_id)
    {
        $status = false;
        $assessment = Assessment::where('entity_id', $entity_id)
        ->where('payment_status', 'pending')
        ->get();
        if (count($assessment)) {
            $status = true;
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Assessment retrieved successfully.',
            'data' => [
                'payment_status' => $status,
            ]
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

    /**
     * Process Bulk Assessment with IDs
     */
    public function bulk_assessment_store(Request $request)
    {
        $request->validate([
            'assessment_file' => 'required|mimes:csv,xlx,xlsx|max:2048',
        ]);
        $user = $request->user();
        $path = $request->file('assessment_file');
        $file_name = $path->store('assessments');
        $file_storage_path = storage_path('app/' . $file_name);
        Excel::import(new BulkAssessmentImport($user), $file_storage_path);
        $assessments = Assessment::latest()->get();
        return AssessmentResource::collection($assessments);
    }

    /**
     * Process Bulk Assessment without IDs
     */
    public function bulk_assessment_without_id_store(Request $request)
    {
        $request->validate([
            'assessment_file' => 'required|mimes:csv,xlx,xlsx|max:2048',
        ]);
        $user = $request->user();
        $path = $request->file('assessment_file');
        $file_name = $path->store('assessments');
        $file_storage_path = storage_path('app/' . $file_name);
        Excel::import(new BulkAssessmentWithoutIDImport($user), $file_storage_path);
        $assessments = Assessment::latest()->get();
        return AssessmentResource::collection($assessments);
    }

    /**
     * Advanced Search in resource.
     *
     * Query paramters `full_name` or `phone_number`.<br>
     * Additonal Query paramters `agency_id`, `revenue_item_id`, `assessment_year_id`, `date_from and date_to`
     */
    public function search(Request $request)
    {
        $per_page = 20;

        if ($request->has('full_name')) {
            $query_request = $request->get('full_name');
            $ticket_response = Assessment::where('full_name', 'like', "%$query_request%")->paginate($per_page);
        }
        if ($request->has('phone_number')) {
            $query_request = $request->get('phone_number');
            $ticket_response = Assessment::where('phone_number', $query_request)->paginate($per_page);
        }
        if ($request->has('agency_id')) {
            $query_request = $request->get('agency_id');
            $ticket_response = Assessment::where('agency_id', $query_request)->paginate($per_page);
        }
        if ($request->has('revenue_item_id')) {
            $query_request = $request->get('revenue_item_id');
            $ticket_response = Assessment::where('revenue_item_id', $query_request)->paginate($per_page);
        }
        if ($request->has('assessment_year_id')) {
            $query_request = $request->get('assessment_year_id');
            $ticket_response = Assessment::where('assessment_year_id', $query_request)->paginate($per_page);
        }
        if ($request->has('due_date')) {
            $query_request = $request->get('due_date');
            $ticket_response = Assessment::where('due_date', $query_request)->paginate($per_page);
        }
        if ($request->has('date_from') && $request->has('date_to')) {
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
            $ticket_response = Assessment::whereBetween('created_at', [$date_from, $date_to])->paginate($per_page);
        }
        if (!isset($ticket_response)){
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request.'
            ]);
        }
        return AssessmentResource::collection($ticket_response);
    }
}
