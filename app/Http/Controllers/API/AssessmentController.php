<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssessmentStoreRequest;
use App\Http\Requests\AssessmentUpdateRequest;
use App\Http\Resources\AssessmentResource;
use App\Models\Assessment;
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
     * Store a newly created resource in storage.
     */
    public function store(AssessmentStoreRequest $request)
    {
        $validatedData = $request->validated();
        $auth_user = $request->user();
        $user = User::where('email', $validatedData['email'])->first();
        if ($user) {
            $validatedData['user_id'] = $user->id;
        }
        $validatedData['status'] = 'pending';
        $validatedData['payment_status'] = 'pending';
        $validatedData['added_by'] = $auth_user->id ?? 0;
        $validatedData['assessment_reference'] = 'ASSESSMENT-' . time() . '-' . rand(1000, 9999);
        $assessment = Assessment::create($validatedData);
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
}
