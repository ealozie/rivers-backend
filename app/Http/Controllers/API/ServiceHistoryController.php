<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceHistoryRequestStore;
use App\Http\Resources\ServiceHistoryResource;
use App\Models\ServiceHistory;
use Illuminate\Http\Request;

/**
 * @tags Service History Service
 */
class ServiceHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $service_history = ServiceHistory::all();
        return ServiceHistoryResource::collection($service_history);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceHistoryRequestStore $request)
    {
        $requestData = $request->validated();
        $requestData['created_by'] = auth()->id();
        $service_history = ServiceHistory::create($requestData);
        return new ServiceHistoryResource($service_history);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service_history = ServiceHistory::find($id);
        return new ServiceHistoryResource($service_history);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $requestData = $request->validated();
        $service_history = ServiceHistory::find($id);
        $service_history->update($requestData);
        return new ServiceHistoryResource($service_history);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service_history = ServiceHistory::find($id);
        $service_history->delete();
        return response()->json(['message' => 'Service history deleted successfully']);
    }
}
