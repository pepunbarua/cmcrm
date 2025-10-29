<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode(' ', $permission->name)[1] ?? 'other';
        });
        
        return view('roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->givePermissionTo($request->permissions);
        
        activity()
            ->performedOn($role)
            ->causedBy(auth()->user())
            ->log('Created new role: ' . $role->name);
        
        return response()->json([
            'success' => true,
            'message' => 'Role created successfully!',
            'redirect' => route('roles.index')
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->name = $request->name;
        $role->save();
        
        $role->syncPermissions($request->permissions);
        
        activity()
            ->performedOn($role)
            ->causedBy(auth()->user())
            ->log('Updated role: ' . $role->name);
        
        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully!',
            'redirect' => route('roles.index')
        ]);
    }

    public function destroy(Role $role)
    {
        // Prevent deleting default roles
        if (in_array($role->name, ['admin', 'manager', 'photographer'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete default system roles!'
            ], 400);
        }
        
        activity()
            ->performedOn($role)
            ->causedBy(auth()->user())
            ->log('Deleted role: ' . $role->name);
        
        $role->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully!'
        ]);
    }
}
