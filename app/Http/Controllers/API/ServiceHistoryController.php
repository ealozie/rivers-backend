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
        $requestData['added_by'] = auth()->id();
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
     * Service History by service request ID (Tracking number) Resource.
     */
    public function service_history_by_request(Request $request, $request_id)
    {
        $service_request = ServiceRequest::where('request_id', $request_id)->frist();
        if (!$service_request) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request ID not found.'
            ], 404);
        }
        $service_history = ServiceHistory::where('service_request_id', $request_id)->get();
        return ServiceHistoryResource::collection($service_history);
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
