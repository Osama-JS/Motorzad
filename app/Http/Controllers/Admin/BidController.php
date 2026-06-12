<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use Illuminate\Http\Request;

class BidController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => Bid::count(),
            'auto_bids' => Bid::where('is_auto_bid', true)->count(),
            'active_bids' => Bid::where('status', 'active')->count(),
        ];

        return view('admin.bids.index', compact('stats'));
    }

    public function getData(Request $request)
    {
        $bids = Bid::with(['user', 'auction.vehicle'])->latest()->get();

        return response()->json([
            'data' => $bids->map(function ($bid) {
                // User Column
                $userHtml = '
                    <div class="d-flex align-items-center gap-2">
                        <div class="d-inline-flex align-items-center justify-content-center bg-light text-dark rounded-circle font-weight-bold" style="width:32px; height:32px; font-size:0.85rem;">' . mb_substr($bid->user->name, 0, 1) . '</div>
                        <div>
                            <strong>' . e($bid->user->name) . '</strong>
                            <br>
                            <span class="text-muted" style="font-size:0.75rem;">' . e($bid->user->email) . '</span>
                        </div>
                    </div>';

                // Auction Column
                $auctionUrl = route('admin.auctions.show', $bid->auction_id);
                $auctionHtml = '
                    <a href="' . $auctionUrl . '" class="text-decoration-none font-weight-bold text-dark">
                        ' . e($bid->auction->title) . '
                        <br>
                        <small class="text-muted">' . ($bid->auction->vehicle ? e($bid->auction->vehicle->title) : 'N/A') . '</small>
                    </a>';

                // Amount Column
                $amountHtml = '<span class="font-weight-bold text-primary" style="font-size:1.05rem;">' . number_format($bid->amount, 2) . '</span>';

                // Type Column
                $typeHtml = $bid->is_auto_bid 
                    ? '<span class="badge" style="background:#faf5ff; color:#a855f7; border:1px solid #f3e8ff; font-size:0.75rem; padding:4px 8px; border-radius:50px;"><i class="fa-solid fa-robot"></i> ' . (app()->getLocale() === 'ar' ? 'تلقائي' : 'Auto') . '</span>'
                    : '<span class="badge" style="background:#f0fdf4; color:#16a34a; border:1px solid #dcfce7; font-size:0.75rem; padding:4px 8px; border-radius:50px;"><i class="fa-solid fa-user"></i> ' . (app()->getLocale() === 'ar' ? 'يدوي' : 'Manual') . '</span>';

                // Status Column
                $statusBadge = match($bid->status) {
                    'active' => '<span class="badge bg-success px-3 py-1 rounded-pill">' . (app()->getLocale() === 'ar' ? 'نشط' : 'Active') . '</span>',
                    'outbid' => '<span class="badge bg-secondary px-3 py-1 rounded-pill">' . (app()->getLocale() === 'ar' ? 'تخطي' : 'Outbid') . '</span>',
                    'winner' => '<span class="badge bg-warning text-dark px-3 py-1 rounded-pill"><i class="fa-solid fa-trophy"></i> ' . (app()->getLocale() === 'ar' ? 'فائز' : 'Winner') . '</span>',
                    default => '<span class="badge bg-light text-dark border px-3 py-1 rounded-pill">' . e($bid->status) . '</span>'
                };

                // Time Column
                $timeHtml = '
                    <span class="text-dark font-weight-bold" style="font-size:0.85rem;">' . $bid->created_at->diffForHumans() . '</span>
                    <br>
                    <span class="text-muted" style="font-size:0.75rem;">' . $bid->created_at->format('Y-m-d H:i:s') . '</span>';

                // IP Column
                $ipHtml = '<code>' . e($bid->ip_address ?? '-') . '</code>';

                return [
                    'id' => $bid->id,
                    'user' => $userHtml,
                    'auction' => $auctionHtml,
                    'amount' => $amountHtml,
                    'type' => $typeHtml,
                    'status' => $statusBadge,
                    'time' => $timeHtml,
                    'ip' => $ipHtml,
                ];
            })
        ]);
    }
}
