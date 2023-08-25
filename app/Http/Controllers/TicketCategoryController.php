<?php

namespace App\Http\Controllers;

use App\Http\Resources\TicketCategoryResource;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Ticket;

class TicketCategoryController extends Controller
{
    /**
     * Handle the incoming request.
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
