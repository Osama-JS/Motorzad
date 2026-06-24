<?php

namespace App\Http\Controllers\Admin;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $rolesCount = Role::count();
        $totalUsers = Role::withCount('users')->get()->sum('users_count');
        $permissions = Permission::all();
        return view('admin.roles.index', compact('rolesCount', 'totalUsers', 'permissions'));
    }

    public function getData(Request $request)
    {
        $query = Role::with(['permissions', 'users']);

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $perPage = $request->per_page ?? 10;
        $roles = $query->paginate($perPage);

        $data = [];
        foreach ($roles as $role) {
            $data[] = [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name')->toArray(),
                'users_count' => $role->users->count()
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => $roles->total(),
                'current_page' => $roles->currentPage(),
                'links' => $roles->linkCollection()->toArray()
            ]
        ]);
    }

    public function create()
    {
        // Not used with AJAX Modals
        return redirect()->route('admin.roles.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name]);
        if ($request->has('permissions')) {
            $permissions = array_map('intval', $request->permissions);
            $role->syncPermissions($permissions);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Role created successfully')
            ]);
        }

        return redirect()->route('admin.roles.index')->with('success', __('Role created successfully'));
    }

    public function show(Role $role)
    {
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return response()->json([
            'success' => true,
            'role' => $role,
            'permissions' => $rolePermissions
        ]);
    }

    public function edit(Role $role)
    {
        // Not used with AJAX Modals
        return redirect()->route('admin.roles.index');
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        $role->update(['name' => $request->name]);
        $permissions = $request->has('permissions') ? array_map('intval', $request->permissions) : [];
        $role->syncPermissions($permissions);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Role updated successfully')
            ]);
        }

        return redirect()->route('admin.roles.index')->with('success', __('Role updated successfully'));
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Cannot delete role with attached users.')
                ], 400);
            }
            return redirect()->back()->with('error', __('Cannot delete role with attached users.'));
        }

        $role->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Role deleted successfully')
            ]);
        }

        return redirect()->route('admin.roles.index')->with('success', __('Role deleted successfully'));
    }
}
