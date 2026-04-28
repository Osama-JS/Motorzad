<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users_count' => User::count(),
            'roles_count' => Role::count(),
            'permissions_count' => Permission::count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }
}
