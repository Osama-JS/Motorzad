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
    public function index(Request $request)
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

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('bidder.dashboard.partials.content', compact('stats', 'featuredAuctions', 'recentActivity'))->render()
            ]);
        }

        return view('bidder.dashboard', compact('stats', 'featuredAuctions', 'recentActivity'));
    }

    /**
     * Display notifications list page.
     */
    public function notifications(Request $request)
    {
        $user = auth()->user();
        $realCount = $user->notifications()->count();

        if ($realCount > 0) {
            $notifications = $user->notifications()->latest()->paginate(10);
            $usingMock = false;
        } else {
            // High fidelity mock notifications fallback
            $mockList = [
                [
                    'id' => 'mock-uuid-1',
                    'type' => 'KycApproved',
                    'data' => [
                        'title_ar' => 'تم توثيق الحساب بنجاح 🎉',
                        'title_en' => 'Account Verified Successfully 🎉',
                        'message_ar' => 'تهانينا! تم قبول مستندات التحقق من الهوية الخاصة بك. يمكنك الآن المزايدة بلا قيود.',
                        'message_en' => 'Congratulations! Your identity verification documents have been approved. You can now bid without limits.',
                        'action_url' => route('kyc.index'),
                        'icon' => 'fa-user-check',
                        'icon_color' => '#10b981',
                        'bg_color' => 'rgba(16, 185, 129, 0.08)'
                    ],
                    'read_at' => null,
                    'created_at' => now()->subMinutes(20),
                ],
                [
                    'id' => 'mock-uuid-2',
                    'type' => 'Outbid',
                    'data' => [
                        'title_ar' => 'تنبيه: تم تجاوز عرضك ⚠️',
                        'title_en' => 'Warning: You have been outbid ⚠️',
                        'message_ar' => 'قام مزايد آخر بتقديم عرض أعلى على مرسيدس جي كلاس G63 AMG. قم بزيادة عرضك للمحافظة على صدارتك.',
                        'message_en' => 'Another bidder has placed a higher bid on Mercedes G63 AMG. Increase your bid to remain in the lead.',
                        'action_url' => route('bidder.auctions.my-bids'),
                        'icon' => 'fa-gavel',
                        'icon_color' => '#f59e0b',
                        'bg_color' => 'rgba(245, 158, 11, 0.08)'
                    ],
                    'read_at' => null,
                    'created_at' => now()->subHours(2),
                ],
                [
                    'id' => 'mock-uuid-3',
                    'type' => 'DepositApproved',
                    'data' => [
                        'title_ar' => 'تأكيد عملية الإيداع 💰',
                        'title_en' => 'Deposit Request Approved 💰',
                        'message_ar' => 'تمت الموافقة على طلب الإيداع بقيمة 15,000 ريال سعودي وإضافته إلى رصيد محفظتك بنجاح.',
                        'message_en' => 'Your deposit request of 15,000 SAR has been approved and successfully added to your wallet balance.',
                        'action_url' => route('bidder.wallet.index'),
                        'icon' => 'fa-wallet',
                        'icon_color' => '#3b82f6',
                        'bg_color' => 'rgba(59, 130, 246, 0.08)'
                    ],
                    'read_at' => now()->subHours(1),
                    'created_at' => now()->subHours(4),
                ],
                [
                    'id' => 'mock-uuid-4',
                    'type' => 'AuctionWon',
                    'data' => [
                        'title_ar' => 'تهانينا الفوز بالمزاد! 🏆',
                        'title_en' => 'Congratulations! Auction Won! 🏆',
                        'message_ar' => 'لقد فزت بمزاد أودي RS7 كواترو 2023 بسعر نهائي 345,000 ريال سعودي. يرجى إتمام عملية الشراء.',
                        'message_en' => 'You have won the Audi RS7 Quattro 2023 auction for a final price of 345,000 SAR. Please proceed to complete the purchase.',
                        'action_url' => route('bidder.auctions.won'),
                        'icon' => 'fa-trophy',
                        'icon_color' => '#8b5cf6',
                        'bg_color' => 'rgba(139, 92, 246, 0.08)'
                    ],
                    'read_at' => now()->subDays(1),
                    'created_at' => now()->subDays(1),
                ]
            ];

            // Convert to array of objects to behave like models
            $formatted = [];
            foreach ($mockList as $item) {
                $formatted[] = (object)[
                    'id' => $item['id'],
                    'type' => $item['type'],
                    'data' => $item['data'],
                    'read_at' => $item['read_at'],
                    'created_at' => $item['created_at'],
                ];
            }

            $notifications = collect($formatted);
            $usingMock = true;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('bidder.notifications.partials.list', compact('notifications', 'usingMock'))->render()
            ]);
        }

        return view('bidder.notifications.index', compact('notifications', 'usingMock'));
    }

    /**
     * Mark notification as read.
     */
    public function markNotificationRead(Request $request, $id)
    {
        $user = auth()->user();

        if ($id === 'all') {
            $user->unreadNotifications->markAsRead();
            return response()->json(['success' => true]);
        }

        $notification = $user->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        // Mock success fallback
        if (str_starts_with($id, 'mock-')) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
    }

    /**
     * Get the current unread count and latest notification state for real-time polling.
     */
    public function getUnreadState(Request $request)
    {
        $user = auth()->user();
        $realUnreadCount = $user->unreadNotifications->count();
        $hasNotifications = $user->notifications()->exists();
        $unreadCount = $hasNotifications ? $realUnreadCount : 2;

        // Fetch the very latest unread notification to show a toast alert if new
        $latestUnread = $user->unreadNotifications()->latest()->first();
        $latestNotification = null;
        if ($latestUnread) {
            $latestNotification = [
                'id' => $latestUnread->id,
                'title' => $latestUnread->data['title_' . app()->getLocale()] ?? ($latestUnread->data['title'] ?? __('Notification')),
                'message' => $latestUnread->data['message_' . app()->getLocale()] ?? ($latestUnread->data['message'] ?? ($latestUnread->data['body'] ?? '')),
                'action_url' => $latestUnread->data['action_url'] ?? '#'
            ];
        }

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'latest_notification' => $latestNotification,
            'has_notifications' => $hasNotifications
        ]);
    }
}
