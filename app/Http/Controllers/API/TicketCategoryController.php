<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketCategoryResource;
use App\Models\TicketCategory;
use Illuminate\Http\Request;

/**
 * @tags Ticket Categories Service
 */

class TicketCategoryController extends Controller
{
    /**
     * Get all Ticket Categories.
     */
    public function __invoke(Request $request)
    {
        $ticket_categories = TicketCategory::all();
        return response()->json([
            'status' => 'success',
            'data' => TicketCategoryResource::collection($ticket_categories)
        ], 200);
    }
}
