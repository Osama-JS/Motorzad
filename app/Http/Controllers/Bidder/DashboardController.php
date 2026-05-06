<?php

namespace App\Http\Controllers\Bidder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the bidder dashboard.
     */
    public function index(): View
    {
        $user = auth()->user();

        // Dashboard statistics (demo data — replace with real queries later)
        $stats = [
            'active_bids'    => 5,
            'won_auctions'   => 3,
            'watchlist'      => 8,
            'total_bids'     => 24,
            'total_spent'    => 45200,
            'win_rate'       => 42,
            'wallet_balance' => 12500,
            'live_count'     => 7,
        ];

        // Featured auctions (demo data)
        $featuredAuctions = [
            [
                'id'            => 1,
                'title'         => __('Toyota Land Cruiser 2024 - Full Option'),
                'image'         => 'https://images.unsplash.com/photo-1625231334401-6162a5e0a0d9?w=600&h=400&fit=crop',
                'status'        => 'live',
                'time_left'     => '02:45:30',
                'location'      => __('Riyadh'),
                'bidders'       => 18,
                'current_price' => 185000,
            ],
            [
                'id'            => 2,
                'title'         => __('Mercedes-Benz G63 AMG 2023'),
                'image'         => 'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?w=600&h=400&fit=crop',
                'status'        => 'live',
                'time_left'     => '05:12:00',
                'location'      => __('Jeddah'),
                'bidders'       => 25,
                'current_price' => 420000,
            ],
            [
                'id'            => 3,
                'title'         => __('BMW X5 M Competition 2024'),
                'image'         => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=600&h=400&fit=crop',
                'status'        => 'upcoming',
                'time_left'     => __('Starts Tomorrow'),
                'location'      => __('Dammam'),
                'bidders'       => 0,
                'current_price' => 310000,
            ],
        ];

        // Recent activity (demo data)
        $recentActivity = [
            [
                'type'  => 'bid',
                'title' => __('You placed a bid'),
                'desc'  => __('Toyota Land Cruiser') . ' — 185,000 ' . __('SAR'),
                'time'  => __('2 min ago'),
            ],
            [
                'type'  => 'outbid',
                'title' => __('You were outbid'),
                'desc'  => __('Nissan Patrol 2023') . ' — 95,000 ' . __('SAR'),
                'time'  => __('15 min ago'),
            ],
            [
                'type'  => 'won',
                'title' => __('Auction won!'),
                'desc'  => __('Chevrolet Tahoe 2022') . ' — 125,000 ' . __('SAR'),
                'time'  => __('1 hour ago'),
            ],
            [
                'type'  => 'watch',
                'title' => __('Added to watchlist'),
                'desc'  => __('Ford Raptor 2024'),
                'time'  => __('3 hours ago'),
            ],
        ];

        return view('bidder.dashboard', compact('stats', 'featuredAuctions', 'recentActivity'));
    }
}
