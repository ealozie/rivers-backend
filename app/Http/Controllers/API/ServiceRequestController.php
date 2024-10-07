<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequestStore;
use App\Http\Requests\ServiceRequestUpdate;
use App\Http\Resources\ServiceRequestResource;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @tags Service Request Service
 */
class ServiceRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $service_requests = ServiceRequest::all();
        return ServiceRequestResource::collection($service_requests);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceRequestStore $request)
    {
        $requestData = $request->validated();
        $requestData['request_id'] = time();
        $requestData['status'] = 'pending';
        $user = User::where('unique_id', $requestData['user_id'])->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $requestData['user_id'] = $user->id;
        $service_request = ServiceRequest::create($requestData);
        return new ServiceRequestResource($service_request);


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service_request = ServiceRequest::findOrFail($id);
        return new ServiceRequestResource($service_request);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceRequestUpdate $request, string $id)
    {
        $service_request = ServiceRequest::findOrFail($id);
        $requestData = $request->validated();
        $service_request->update($requestData);
        return new ServiceRequestResource($service_request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $service_request = ServiceRequest::findOrFail($id);
        $service_request->delete();
        return response()->json(['message' => 'Service Request deleted successfully']);
    }
}
