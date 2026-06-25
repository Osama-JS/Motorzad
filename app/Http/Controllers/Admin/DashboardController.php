<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Page;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users_count' => User::count(),
            'roles_count' => Role::count(),
            'permissions_count' => Permission::count(),
            'pages_count' => Page::count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }

    public function globalSearch(Request $request)
    {
        $term = $request->input('q');
        if (empty($term) || strlen($term) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];

        // 1. Users
        $users = User::where('is_deleted', false)
            ->where(function($q) use ($term) {
                $q->where('first_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%");
            })->take(5)->get();
        
        if ($users->isNotEmpty()) {
            $results[] = [
                'category' => app()->getLocale() === 'ar' ? 'المستخدمين' : 'Users',
                'items' => $users->map(function($user) {
                    return [
                        'title' => $user->name ?: ($user->first_name . ' ' . $user->last_name),
                        'subtitle' => $user->email,
                        'url' => route('admin.users.index') . '?search=' . urlencode($user->email),
                        'icon' => 'fa-user'
                    ];
                })
            ];
        }

        // 2. Auctions
        $auctions = \App\Models\Auction::where('title', 'like', "%{$term}%")->take(5)->get();
        if ($auctions->isNotEmpty()) {
            $results[] = [
                'category' => app()->getLocale() === 'ar' ? 'المزادات' : 'Auctions',
                'items' => $auctions->map(function($auction) {
                    return [
                        'title' => $auction->title,
                        'subtitle' => $auction->status,
                        'url' => route('admin.auctions.show', $auction->id),
                        'icon' => 'fa-gavel'
                    ];
                })
            ];
        }

        // 3. Vehicles
        $vehicles = \App\Models\Vehicle::where('title', 'like', "%{$term}%")
            ->orWhere('make', 'like', "%{$term}%")
            ->orWhere('model', 'like', "%{$term}%")
            ->orWhere('vin', 'like', "%{$term}%")
            ->take(5)->get();
        if ($vehicles->isNotEmpty()) {
            $results[] = [
                'category' => app()->getLocale() === 'ar' ? 'السيارات' : 'Vehicles',
                'items' => $vehicles->map(function($vehicle) {
                    return [
                        'title' => $vehicle->title,
                        'subtitle' => ($vehicle->make ? $vehicle->make . ' ' . $vehicle->model : $vehicle->vin),
                        'url' => route('admin.vehicles.edit', $vehicle->id),
                        'icon' => 'fa-car'
                    ];
                })
            ];
        }

        // 4. Bids
        $bids = \App\Models\Bid::with(['user', 'auction'])
            ->whereHas('user', function($q) use ($term) {
                $q->where('name', 'like', "%{$term}%");
            })->orWhereHas('auction', function($q) use ($term) {
                $q->where('title', 'like', "%{$term}%");
            })->take(5)->get();
        if ($bids->isNotEmpty()) {
            $results[] = [
                'category' => app()->getLocale() === 'ar' ? 'المزايدات' : 'Bids',
                'items' => $bids->map(function($bid) {
                    return [
                        'title' => ($bid->user ? $bid->user->name : 'N/A') . ' - ' . number_format($bid->amount, 2),
                        'subtitle' => ($bid->auction ? $bid->auction->title : 'N/A'),
                        'url' => route('admin.bids.index') . '?search=' . urlencode($bid->user ? $bid->user->name : ''),
                        'icon' => 'fa-coins'
                    ];
                })
            ];
        }

        return response()->json(['results' => $results]);
    }
}
