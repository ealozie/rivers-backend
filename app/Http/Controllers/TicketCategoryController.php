<?php

namespace App\Http\Controllers;

use App\Http\Resources\TicketCategoryResource;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Ticket;

/**
 * @tags Ticket Category Service
 */
class TicketCategoryController extends Controller
{
    /**
     * Display listing of all resource.
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
