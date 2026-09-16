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
        $liveAuctions = \App\Models\Auction::where('status', 'live')->count();
        $totalRevenue = \App\Models\WalletTransaction::where('type', 'credit')->sum('amount');
        
        $activeSellers = \App\Models\User::role('seller')->count();
        $activeBidders = \App\Models\User::role('bidder')->count();

        $pendingKyc = \App\Models\KycRequest::where('status', 'pending')->count();
        $pendingVehicles = \App\Models\Vehicle::where('status', 'pending')->count();
        $pendingWithdrawals = \App\Models\WithdrawalRequest::where('status', 'pending')->count();
        $actionRequired = $pendingKyc + $pendingVehicles + $pendingWithdrawals;

        $stats = [
            'users_count' => User::count(),
            'roles_count' => Role::count(),
            'permissions_count' => Permission::count(),
            'pages_count' => Page::count(),
            'live_auctions' => $liveAuctions,
            'total_revenue' => $totalRevenue,
            'active_sellers' => $activeSellers,
            'active_bidders' => $activeBidders,
            'action_required' => $actionRequired,
            'pending_kyc' => $pendingKyc,
            'pending_vehicles' => $pendingVehicles,
            'pending_withdrawals' => $pendingWithdrawals,
        ];

        // 1. Bids Chart (Last 7 Days)
        $bidsData = \App\Models\Bid::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->pluck('count', 'date');

        $bidsChart = ['labels' => [], 'data' => []];
        for ($i = 6; $i >= 0; $i--) {
            $dateObj = now()->subDays($i);
            $date = $dateObj->format('Y-m-d');
            $bidsChart['labels'][] = $dateObj->format('M d');
            $bidsChart['data'][] = $bidsData[$date] ?? 0;
        }

        // 2. Vehicles Status (Donut Chart)
        $vehiclesStatus = \App\Models\Vehicle::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
            
        $vehiclesChart = [
            'labels' => [__('معتمدة/حالي'), __('بانتظار المراجعة'), __('مباعة'), __('مسودة/مرفوضة')],
            'data' => [
                ($vehiclesStatus['approved'] ?? 0) + ($vehiclesStatus['live'] ?? 0),
                $vehiclesStatus['pending'] ?? 0,
                $vehiclesStatus['sold'] ?? 0,
                ($vehiclesStatus['draft'] ?? 0) + ($vehiclesStatus['rejected'] ?? 0)
            ]
        ];

        // 3. User Acquisition (Last 6 Months)
        $usersData = \App\Models\User::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year', 'ASC')
            ->orderBy('month', 'ASC')
            ->get()
            ->keyBy(function($item) {
                return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
            });

        $usersChart = ['labels' => [], 'data' => []];
        for ($i = 5; $i >= 0; $i--) {
            $dateObj = now()->subMonths($i);
            $key = $dateObj->format('Y-m');
            $usersChart['labels'][] = $dateObj->format('M Y');
            $usersChart['data'][] = $usersData[$key]->count ?? 0;
        }

        // 4. Quick Action Tables Data
        $latestBids = \App\Models\Bid::with(['user', 'auction'])->latest()->take(5)->get();
        $pendingVehiclesList = \App\Models\Vehicle::with('seller')->where('status', 'pending')->latest()->take(5)->get();
        $recentWithdrawals = \App\Models\WithdrawalRequest::with('user')->where('status', 'pending')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'bidsChart', 'vehiclesChart', 'usersChart', 'latestBids', 'pendingVehiclesList', 'recentWithdrawals'));
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
