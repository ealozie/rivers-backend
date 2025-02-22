<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountManagerStoreRequest;
use App\Http\Requests\AccountManagerUpdateRequest;
use App\Http\Resources\AccountManagerResource;
use App\Models\AccountManager;
use App\Models\CommercialVehicle;
use App\Models\Cooperate;
use App\Models\Individual;
use App\Models\Property;
use App\Models\Shop;
use App\Models\Signage;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @tags Account Manager Service
 */
class AccountManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $user_id = $user->id;
        if ($user->hasRole('admin')) {
            $account_manager = AccountManager::paginate();
            $total_account_manager = AccountManager::count();
        } else {
            $account_manager = AccountManager::where('user_id', $user_id)->paginate();
            $total_account_manager = AccountManager::where('user_id', $user_id)->count();
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_account_manager' => $total_account_manager,
                'account_manager' => AccountManagerResource::collection($account_manager),
            ]
        ], 200);
        //return AccountManagerResource::collection($account_manager);
    }

    /**
     * Get Account Manager entities by Account Manager User ID.
     */
    public function account_manager_entities(Request $request, $user_id)
    {
        $account_manager = AccountManager::where('user_id', $user_id)->get();
        return AccountManagerResource::collection($account_manager);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AccountManagerStoreRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['added_by'] = $request->user()->id;
        $user = User::where('unique_id', $validatedData['user_id'])->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }
        $validatedData['user_id'] = $user->id;
        $entity_id = $validatedData['entity_id'];
        if (isset($validatedData['entity_id']) && $validatedData['entity_type'] == 'shop') {
            $shop = Shop::where('shop_id', $entity_id)->first();
            if ($shop) {
                unset($validatedData['entity_type']);
                unset($validatedData['entity_id']);
                $account_manager = $shop->account_manager()->firstOrCreate($validatedData);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Shop not found',
                ], 404);
            }
        }
        if (isset($validatedData['entity_id']) && $validatedData['entity_type'] == 'individual') {
            $individual = Individual::where('individual_id', $entity_id)->first();
            if ($individual) {
                unset($validatedData['entity_type']);
                unset($validatedData['entity_id']);
                $account_manager = $individual->account_manager()->firstOrCreate($validatedData);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Individual not found',
                ], 404);
            }
        }

        if (isset($validatedData['entity_id']) && $validatedData['entity_type'] == 'property') {
            $property = Property::where('property_id', $entity_id)->first();
            if ($property) {
                unset($validatedData['entity_type']);
                unset($validatedData['entity_id']);
                $account_manager = $property->account_manager()->firstOrCreate($validatedData);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Property not found',
                ], 404);
            }
        }
        if (isset($validatedData['entity_id']) && $validatedData['entity_type'] == 'cooperate') {
            $cooperate = Cooperate::where('cooperate_id', $entity_id)->first();
            if ($cooperate) {
                unset($validatedData['entity_type']);
                unset($validatedData['entity_id']);
                $account_manager = $cooperate->account_manager()->firstOrCreate($validatedData);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cooperate not found',
                ], 404);
            }
        }

        if (isset($validatedData['entity_id']) && $validatedData['entity_type'] == 'signage') {
            $signage = Signage::where('signage_id', $entity_id)->first();
            if ($signage) {
                unset($validatedData['entity_type']);
                unset($validatedData['entity_id']);
                $account_manager = $signage->account_manager()->firstOrCreate($validatedData);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Signage not found',
                ], 404);
            }
        }

        if (isset($validatedData['entity_id']) && $validatedData['entity_type'] == 'vehicle') {
            $vehicle = CommercialVehicle::where('vehicle_id', $entity_id)->first();
            if ($vehicle) {
               unset($validatedData['entity_type']);
                unset($validatedData['entity_id']);
                $account_manager = $vehicle->account_manager()->firstOrCreate($validatedData);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vehicle not found',
                ], 404);
            }
        }
        return new AccountManagerResource($account_manager);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $account_manager = AccountManager::findOrFail($id);
        return new AccountManagerResource($account_manager);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AccountManagerUpdateRequest $request, string $id)
    {
        $validatedData = $request->validated();
        $account_manager = AccountManager::find($id);
        $account_manager->user_id = $validatedData['user_id'];
        $account_manager->save();
        return new AccountManagerResource($account_manager);
    }

    /**
     * Get entity manager resource by Entity ID.
     */
    public function account_manager_by_entity_id($entity_type, $entity_id)
    {
        $validatedData['entity_type'] = $entity_type;
        if (isset($entity_id) && $validatedData['entity_type'] == 'shop') {
            $shop = Shop::where('shop_id', $entity_id)->first();
            if ($shop) {
                $account_manager = $shop->account_manager;
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Shop not found',
                ], 404);
            }
        }
        if (isset($entity_id) && $validatedData['entity_type'] == 'individual') {
            $individual = Individual::where('individual_id', $entity_id)->first();
            if ($individual) {
                $account_manager = $individual->account_manager;
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Individual not found',
                ], 404);
            }
        }

        if (isset($entity_id) && $validatedData['entity_type'] == 'property') {
            $property = Property::where('property_id', $entity_id)->first();
            if ($property) {
                $account_manager = $property->account_manager;
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Property not found',
                ], 404);
            }
        }
        if (isset($entity_id) && $validatedData['entity_type'] == 'cooperate') {
            $cooperate = Cooperate::where('cooperate_id', $entity_id)->first();
            if ($cooperate) {
                $account_manager = $cooperate->account_manager;
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cooperate not found',
                ], 404);
            }
        }

        if (isset($entity_id) && $validatedData['entity_type'] == 'signage') {
            $signage = Signage::where('signage_id', $entity_id)->first();
            if ($signage) {
                $account_manager = $signage->account_manager;
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Signage not found',
                ], 404);
            }
        }

        if (isset($entity_id) && $validatedData['entity_type'] == 'vehicle') {
            $vehicle = CommercialVehicle::where('vehicle_id', $entity_id)->first();
            if ($vehicle) {
                $account_manager = $vehicle->account_manager;
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vehicle not found',
                ], 404);
            }
        }
        return new AccountManagerResource($account_manager);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        AccountManager::destroy($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Account manager has been deleted.'
        ], 200);
    }
}
