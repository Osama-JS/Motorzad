<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissionsCount = Permission::count();
        $totalRolesLinked = Permission::with('roles')->get()->sum(function($p) { return $p->roles->count(); });
        
        return view('admin.permissions.index', compact('permissionsCount', 'totalRolesLinked'));
    }

    public function getData(Request $request)
    {
        $query = Permission::with('roles');

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $perPage = $request->per_page ?? 10;
        $permissions = $query->paginate($perPage);

        $data = [];
        foreach ($permissions as $permission) {
            $data[] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'roles_count' => $permission->roles->count()
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => $permissions->total(),
                'current_page' => $permissions->currentPage(),
                'links' => $permissions->linkCollection()->toArray()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->name]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Permission created successfully')
            ]);
        }

        return redirect()->route('admin.permissions.index')->with('success', __('Permission created successfully'));
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Permission deleted successfully')
            ]);
        }

        return redirect()->route('admin.permissions.index')->with('success', __('Permission deleted successfully'));
    }
}
