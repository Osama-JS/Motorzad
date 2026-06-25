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
        $query = Bid::with(['user', 'auction.vehicle']);

        // Filtering
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($qu) use ($search) {
                    $qu->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('auction', function($qa) use ($search) {
                    $qa->where('title', 'like', "%{$search}%")
                       ->orWhereHas('vehicle', function($qv) use ($search) {
                           $qv->where('title', 'like', "%{$search}%");
                       });
                });
            });
        }

        if ($request->filled('type') && $request->type !== 'all' && $request->type !== '') {
            $query->where('is_auto_bid', $request->type === 'auto');
        }

        if ($request->filled('status') && $request->status !== 'all' && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $perPage = $request->input('per_page', 10);
        $bids = $query->latest()->paginate($perPage);

        $locale = app()->getLocale();
        $data = $bids->map(function ($bid) use ($locale) {
            // User Column html
            $userHtml = '
                <div class="d-flex align-items-center gap-2">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light text-dark rounded-circle font-weight-bold" style="width:32px; height:32px; font-size:0.85rem;">' . mb_substr($bid->user->name, 0, 1) . '</div>
                    <div>
                        <strong>' . e($bid->user->name) . '</strong>
                        <br>
                        <span class="text-muted" style="font-size:0.75rem;">' . e($bid->user->email) . '</span>
                    </div>
                </div>';

            // Auction Column html
            $auctionUrl = route('admin.auctions.show', $bid->auction_id);
            $auctionHtml = '
                <a href="' . $auctionUrl . '" class="text-decoration-none font-weight-bold text-dark">
                    ' . e($bid->auction->title) . '
                    <br>
                    <small class="text-muted">' . ($bid->auction->vehicle ? e($bid->auction->vehicle->title) : 'N/A') . '</small>
                </a>';

            // Amount Column html
            $amountHtml = '<span class="font-weight-bold text-primary" style="font-size:1.05rem;">' . number_format($bid->amount, 2) . '</span>';

            // Type Column html
            $typeHtml = $bid->is_auto_bid 
                ? '<span class="badge" style="background:#faf5ff; color:#a855f7; border:1px solid #f3e8ff; font-size:0.75rem; padding:4px 8px; border-radius:50px;"><i class="fa-solid fa-robot"></i> ' . ($locale === 'ar' ? 'تلقائي' : 'Auto') . '</span>'
                : '<span class="badge" style="background:#f0fdf4; color:#16a34a; border:1px solid #dcfce7; font-size:0.75rem; padding:4px 8px; border-radius:50px;"><i class="fa-solid fa-user"></i> ' . ($locale === 'ar' ? 'يدوي' : 'Manual') . '</span>';

            // Status Column html
            $statusBadge = match($bid->status) {
                'active' => '<span class="badge bg-success px-3 py-1 rounded-pill">' . ($locale === 'ar' ? 'نشط' : 'Active') . '</span>',
                'outbid' => '<span class="badge bg-secondary px-3 py-1 rounded-pill">' . ($locale === 'ar' ? 'تخطي' : 'Outbid') . '</span>',
                'winner' => '<span class="badge bg-warning text-dark px-3 py-1 rounded-pill"><i class="fa-solid fa-trophy"></i> ' . ($locale === 'ar' ? 'فائز' : 'Winner') . '</span>',
                'cancelled' => '<span class="badge bg-danger text-white px-3 py-1 rounded-pill">' . ($locale === 'ar' ? 'ملغي' : 'Cancelled') . '</span>',
                default => '<span class="badge bg-light text-dark border px-3 py-1 rounded-pill">' . e($bid->status) . '</span>'
            };

            // Time Column html
            $timeHtml = '
                <span class="text-dark font-weight-bold" style="font-size:0.85rem;">' . $bid->created_at->diffForHumans() . '</span>
                <br>
                <span class="text-muted" style="font-size:0.75rem;">' . $bid->created_at->format('Y-m-d H:i:s') . '</span>';

            // IP Column html
            $ipHtml = '<code>' . e($bid->ip_address ?? '-') . '</code>';

            // We also return raw values so we can easily render grid view cards without regex parsing
            return [
                'id' => $bid->id,
                'user_name' => $bid->user->name,
                'user_email' => $bid->user->email,
                'user_avatar_initial' => mb_substr($bid->user->name, 0, 1),
                'auction_title' => $bid->auction->title,
                'auction_url' => $auctionUrl,
                'vehicle_title' => $bid->auction->vehicle ? $bid->auction->vehicle->title : 'N/A',
                'amount_formatted' => number_format($bid->amount, 2),
                'is_auto_bid' => $bid->is_auto_bid,
                'status' => $bid->status,
                'time_diff' => $bid->created_at->diffForHumans(),
                'time_formatted' => $bid->created_at->format('Y-m-d H:i:s'),
                'ip_address' => $bid->ip_address ?? '-',

                // Formatted html columns for the table view
                'user' => $userHtml,
                'auction' => $auctionHtml,
                'amount' => $amountHtml,
                'type' => $typeHtml,
                'status_badge' => $statusBadge,
                'time' => $timeHtml,
                'ip' => $ipHtml,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $bids->currentPage(),
                'last_page' => $bids->lastPage(),
                'total' => $bids->total(),
                'links' => $bids->linkCollection()->toArray()
            ]
        ]);
    }

    public function void(Bid $bid)
    {
        $auction = $bid->auction;

        // Wrap in transaction to rollback the price if needed
        \Illuminate\Support\Facades\DB::transaction(function () use ($bid, $auction) {
            // Update bid status to cancelled
            $bid->update(['status' => 'cancelled']);

            // If this was the winning/highest bid of the auction, we need to rollback the price
            $highestBid = Bid::where('auction_id', $auction->id)
                ->where('status', 'active')
                ->orderByDesc('amount')
                ->first();

            if ($highestBid) {
                // Restore winner and winning bid amount to next highest
                $auction->update([
                    'winner_id' => $highestBid->user_id,
                    'winning_bid_amount' => $highestBid->amount,
                ]);
            } else {
                // If there are no active bids left, reset winner and winning bid amount
                $auction->update([
                    'winner_id' => null,
                    'winning_bid_amount' => null,
                ]);
            }

            // Recalculate bids count
            $activeBidsCount = Bid::where('auction_id', $auction->id)
                ->where('status', 'active')
                ->count();
            $auction->update(['bids_count' => $activeBidsCount]);
        });

        return response()->json([
            'success' => true,
            'message' => __('Bid voided and auction price rolled back successfully.')
        ]);
    }
}
