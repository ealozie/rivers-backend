<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketCategoryStoreRequest;
use App\Http\Requests\TicketCategoryUpdateRequest;
use App\Http\Resources\TicketCategoryResource;
use App\Models\TicketCategory;
use App\Models\TicketVending;
use Illuminate\Http\Request;

/**
 * @tags Ticket Categories Service
 */
class TicketCategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ticket_categories = TicketCategory::all();
        return response()->json([
            'status' => 'success',
            'data' => TicketCategoryResource::collection($ticket_categories)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TicketCategoryStoreRequest $request)
    {
        $requestData = $request->validated();
        $requestData['added_by'] = $request->user()->id;
        $ticket_category = TicketCategory::create($requestData);
        return response()->json([
            'status' => 'success',
            'data' => new TicketCategoryResource($ticket_category)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ticket_category = TicketCategory::findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => new TicketCategoryResource($ticket_category)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TicketCategoryUpdateRequest $request, string $id)
    {
        $ticket_category = TicketCategory::findOrFail($id);
        $requestData = $request->validated();
        $requestData['added_by'] = $request->user()->id;
        $ticket_category->update($requestData);
        return response()->json([
            'status' => 'success',
            'data' => new TicketCategoryResource($ticket_category)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ticket_category = TicketCategory::findOrFail($id);
        //check if its in ticket vending table
        $ticket_vending = TicketVending::where('ticket_category_id', $id)->first();
        if ($ticket_vending) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket category cannot be deleted because it is in use'
            ], 400);
        }
        $ticket_category->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Ticket category deleted successfully'
        ], 200);
    }
}
