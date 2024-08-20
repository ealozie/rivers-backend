<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

/**
 * @tags Permissions Service
 */

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::select('id', 'name', 'guard_name', 'label')->orderBy('name', 'ASC')->get()->groupBy('label');
        return $permissions;
    }

    /**
     * Display a roles listing of the resource.
     */
    public function role_index()
    {
        $roles = Role::all();
        return $roles;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Store Permission assigned to role.
     */
    public function store_permission_to_roles(Request $request)
    {
        $validatedData = $request->validate([
            'role' => 'required',
            'permissions' => 'required|array|min:2',
        ]);

        $role = Role::where('name', $validatedData['role'])->first();
        if (!$role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role not found.',
            ], 404);
        }

        try {
            $role->syncPermissions($validatedData['permissions']);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
                'status' => 'success',
                'message' => 'Permissions assigned successfully.'
            ], 200);
    }


    /**
     * Revoke or update Permission assigned to role.
     */
    public function remove_permission_from_role(Request $request)
    {
        $validatedData = $request->validate([
            'role' => 'required',
            'permissions' => 'required|array',
        ]);

        $role = Role::where('name', $validatedData['role'])->first();
        if (!$role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Role not found.',
            ], 404);
        }

        try {
            $role->revokePermissionTo($validatedData['permissions']);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
                'status' => 'success',
                'message' => 'Permissions has been updated.'
            ], 200);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
