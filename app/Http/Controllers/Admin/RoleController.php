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
            $permissionsHtml = "<div style=\"display:flex; flex-wrap:wrap; gap:0.3rem;\">";
            foreach ($role->permissions->take(3) as $perm) {
                $permissionsHtml .= "<span class=\"badge bg-primary text-white\">{$perm->name}</span>";
            }
            if ($role->permissions->count() > 3) {
                $permissionsHtml .= "<span class=\"badge\" style=\"background:rgba(100,116,139,0.1); color:var(--text-muted);\">+" . ($role->permissions->count() - 3) . "</span>";
            }
            $permissionsHtml .= "</div>";

            $actions = "<div class=\"dropdown action-dropdown\">
                <button class=\"btn btn-sm btn-icon border-0 shadow-none dropdown-toggle\" type=\"button\" data-bs-toggle=\"dropdown\">
                    <svg width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"text-muted\"><circle cx=\"12\" cy=\"12\" r=\"1\"></circle><circle cx=\"12\" cy=\"5\" r=\"1\"></circle><circle cx=\"12\" cy=\"19\" r=\"1\"></circle></svg>
                </button>
                <ul class=\"dropdown-menu dropdown-menu-end border-0 shadow-sm py-2\">
                    <li><a class=\"dropdown-item text-primary\" href=\"javascript:void(0)\" onclick=\"editRole({$role->id})\"><svg width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"me-2\"><path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"></path><path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"></path></svg>" . __('Edit') . "</a></li>
                    <li><hr class=\"dropdown-divider\"></li>
                    <li><a class=\"dropdown-item text-danger\" href=\"javascript:void(0)\" onclick=\"deleteRole({$role->id})\"><svg width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"me-2\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path></svg>" . __('Delete') . "</a></li>
                </ul>
            </div>";

            $data[] = [
                'id' => $role->id,
                'name' => "<strong>{$role->name}</strong>",
                'permissions' => $permissionsHtml,
                'users_count' => "<span style=\"color:var(--text-secondary);\">{$role->users->count()}</span>",
                'actions' => $actions
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
