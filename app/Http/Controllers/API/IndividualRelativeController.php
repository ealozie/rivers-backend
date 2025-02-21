<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndividualRelativeStoreRequest;
use App\Http\Requests\IndividualRelativeUpdateRequest;
use App\Http\Resources\IndividualRelativeResource;
use App\Models\IndividualRelative;
use Illuminate\Http\Request;

class IndividualRelativeController extends Controller
{
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
        $individual_relative = IndividualRelative::create($request->validated());
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
