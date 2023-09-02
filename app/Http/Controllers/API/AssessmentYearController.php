<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AssessmentYearResource;
use App\Models\AssessmentYear;
use Illuminate\Http\Request;

/**
 * @tags Assessment Year Service
 */
class AssessmentYearController extends Controller
{
    /**
     * Return all assessment year.
     */
    public function __invoke(Request $request)
    {
        $assessment_years = AssessmentYear::latest()->get();
        return response()->json([
            'status' => 'success',
            'data' => AssessmentYearResource::collection($assessment_years)
        ]);
    }
}
