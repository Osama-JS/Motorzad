<?php

namespace App\Http\Controllers\Admin;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        \App\Services\PermissionRegistry::ensureSystemPermissions();
        $rolesCount = Role::count();
        $totalUsers = Role::withCount('users')->get()->sum('users_count');
        $permissions = Permission::all();
        $groupedPermissions = \App\Services\PermissionRegistry::groupPermissions($permissions, app()->getLocale());
        return view('admin.roles.index', compact('rolesCount', 'totalUsers', 'permissions', 'groupedPermissions'));
    }

    public function getData(Request $request)
    {
        $query = Role::with(['permissions', 'users']);

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $perPage = $request->per_page ?? 10;
        $roles = $query->paginate($perPage);

        $isEn = app()->getLocale() === 'en';
        $authUser = auth()->user();
        $canManage = $authUser ? ($authUser->hasRole('admin') || $authUser->can('manage-roles')) : false;

        $data = [];
        foreach ($roles as $role) {
            $permsList = [];
            foreach ($role->permissions as $p) {
                $meta = \App\Services\PermissionRegistry::getMetadata($p->name);
                $label = $isEn ? ($meta['label_en'] ?? $p->name) : ($meta['label_ar'] ?? $p->name);
                $permsList[] = [
                    'name' => $p->name,
                    'label' => $label,
                ];
            }

            $data[] = [
                'id' => $role->id,
                'name' => $role->name,
                'is_system' => in_array($role->name, ['admin', 'seller', 'bidder']),
                'permissions' => $permsList,
                'users_count' => $role->users->count(),
                'can_manage' => $canManage
            ];
        }

        return response()->json([
            'success' => true,
            'can_manage' => $canManage,
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
            'is_system' => in_array($role->name, ['admin', 'seller', 'bidder']),
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
        $isSystemRole = in_array($role->name, ['admin', 'seller', 'bidder']);

        if (!$isSystemRole) {
            $request->validate([
                'name' => 'required|unique:roles,name,' . $role->id,
            ]);
            $role->update(['name' => $request->name]);
        }

        $request->validate([
            'permissions' => 'array'
        ]);

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
        if (in_array($role->name, ['admin', 'seller', 'bidder'])) {
            $msg = __('Cannot delete a core system role.');
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg
                ], 403);
            }
            return redirect()->back()->with('error', $msg);
        }

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
