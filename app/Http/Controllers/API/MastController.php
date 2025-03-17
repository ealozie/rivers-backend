<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\MastStoreRequest;
use App\Http\Requests\MastUpdateRequest;
use App\Http\Resources\MastResource;
use App\Models\Cooperate;
use App\Models\Individual;
use App\Models\Mast;
use App\Models\MastPicture;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MastController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * Query Params: search, per_page
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $per_page = $request->query('per_page', 10);
        $masts = Mast::query();
        if ($search) {
            $masts = $masts->where('mast_name', 'like', "%$search%")
                ->orWhere('mast_use', 'like', "%$search%");
        }
        return MastResource::collection($masts->paginate($per_page));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MastStoreRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['created_by'] = $request->user()->id;
        if (isset($validatedData['owner_id'])) {
            $owner = User::where('unique_id', $validatedData['owner_id'])->first();
            if (!$owner) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Owner not found'
                ], 404);
            }
            $validatedData['owner_id'] = $owner->id;
        }
        DB::beginTransaction();
        try {
            $mast = Mast::create($validatedData);
            if ($request->hasFile('pictures') && count($validatedData['pictures'])) {
                $mast_images = $validatedData['pictures'];
                foreach ($mast_images as $mast_image) {
                    $path = $mast_image->store('mast_pictures', 'public');
                    $mast_picture = new MastPicture();
                    $mast_picture->mast_id = $mast->id;
                    $mast_picture->image_path = "/storage/" . $path;
                    $mast_picture->save();
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
        return new MastResource($mast);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mast = Mast::with('pictures')->find($id);
        if (!$mast) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mast not found'
            ], 404);
        }
        return new MastResource($mast);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MastUpdateRequest $request, string $id)
    {
        $validatedData = $request->validated();
        $mast = Mast::find($id);
        if (!$mast) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mast not found'
            ], 404);
        }
        if (isset($validatedData['owner_id'])) {
            $owner = User::where('unique_id', $validatedData['owner_id'])->first();
            if (!$owner) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Owner not found'
                ], 404);
            }
            $validatedData['owner_id'] = $owner->id;
        }
        DB::beginTransaction();
        try {
            $mast->update($validatedData);
            if ($request->hasFile('pictures') && count($validatedData['pictures'])) {
                $mast_images = $validatedData['pictures'];
                foreach ($mast_images as $mast_image) {
                    $path = $mast_image->store('mast_pictures', 'public');
                    $mast_picture = new MastPicture();
                    $mast_picture->mast_id = $mast->id;
                    $mast_picture->image_path = "/storage/" . $path;
                    $mast_picture->save();
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
        return new MastResource($mast);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $mast = Mast::find($id);
        if (!$mast) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mast not found'
            ], 404);
        }
        $mast->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Mast deleted successfully'
        ]);
    }

    /**
     * Link Account the specified resource.
     */
    public function link_account(Request $request, $mast_id)
    {
        $validatedData = $request->validate([
            'individual_id_or_cooperate_id' => 'required|min:10|max:10'
        ]);
        $mast = Mast::find($mast_id);
        if (!$mast) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mast ID not found.'
            ], 404);
        }
        $user_id_prefix = $validatedData['individual_id_or_cooperate_id'][0];
        if ($user_id_prefix == 2) {
            $cooperate = Cooperate::where('cooperate_id', $validatedData['individual_id_or_cooperate_id'])->first();
            if (!$cooperate) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cooperate ID not found.'
                ], 404);
            }
            $user = User::find($cooperate->user_id);
        } else if ($user_id_prefix == 1) {
            $individual = Individual::where('individual_id', $validatedData['individual_id_or_cooperate_id'])->first();
            //return $individual;
            if (!$individual) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Individual ID not found.'
                ], 404);
            }
            $user = User::find($individual->user_id);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User ID not found.'
            ], 404);
        }
        //return $user;
        DB::beginTransaction();
        try {
            $mast->update([
                'user_id' => $user->id
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Mast linked successfully.',
            'data' => new MastResource($mast)
        ], 200);
    }
}
