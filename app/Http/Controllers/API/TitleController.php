<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TitleResource;
use App\Models\Title;
use Illuminate\Http\Request;

/**
 * @tags Titles Service
 */
class TitleController extends Controller
{
    /**
     * Return all titles.
     */
    public function __invoke(Request $request)
    {
        $titles = Title::all();
        return response()->json(['status' => 'success', 'data' => TitleResource::collection($titles)]);
    }
}
