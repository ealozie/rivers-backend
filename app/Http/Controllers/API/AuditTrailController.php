<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('admin')) {
            $per_page = 20;
            $audit_trails = Audit::paginate($per_page);
            if ($request->has('query') && $request->get('query') == 'all') {
                $audit_trails = Audit::latest()->get();
            }
            return response()->json([
                'status' => 'success',
                'data' => $audit_trails,
            ]);
        }
    }
}
