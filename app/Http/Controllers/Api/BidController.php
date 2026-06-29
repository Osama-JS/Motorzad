<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Bid;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BidController extends Controller
{
    public function __construct(protected WalletService $walletService) {}

    /**
     * Place a bid on an auction.
     */
    public function store(Request $request, Auction $auction): JsonResponse
    {
        $user = $request->user();

        // ── Validations ────────────────────────────────────────────────────

        // 0. Must not be paused
        if ($auction->is_paused) {
            return response()->json([
                'success' => false,
                'message' => __('This auction is currently paused by admin.'),
            ], 422);
        }

        // 0.5 Must not be blocked from this auction
        $isBlocked = \Illuminate\Support\Facades\DB::table('auction_blocklists')
            ->where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($isBlocked) {
            return response()->json([
                'success' => false,
                'message' => __('You have been blocked from participating in this auction.'),
            ], 403);
        }

        // 1. Must be live
        if ($auction->status !== 'live') {
            return response()->json([
                'success' => false,
                'message' => __('This auction is not currently live.'),
            ], 422);
        }

        // 2. Must not have ended
        if (now()->isAfter($auction->end_time)) {
            return response()->json([
                'success' => false,
                'message' => __('This auction has already ended.'),
            ], 422);
        }

        // 3. User must be approved and have kyc level >= 1
        if ($user->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => __('Your account must be approved to participate in auctions.'),
            ], 403);
        }

        // 4. Validate bid amount
        $request->validate([
            'amount'       => 'required|numeric|min:0',
            'is_auto_bid'  => 'nullable|boolean',
            'max_auto_bid' => 'nullable|numeric|gte:amount',
        ]);

        $currentPrice  = $auction->current_price;
        $minimumBid    = $currentPrice + $auction->min_bid_increment;

        if ($request->amount < $minimumBid && !$auction->bids()->where('user_id', $user->id)->where('status', 'active')->exists()) {
            return response()->json([
                'success'     => false,
                'message'     => __('Your bid must be at least :amount.', ['amount' => number_format($minimumBid, 2)]),
                'minimum_bid' => $minimumBid,
            ], 422);
        }

        // 5. Check deposit requirement
        if ($auction->deposit_required) {
            $hasDeposit = $auction->deposits()
                ->where('user_id', $user->id)
                ->where('status', 'held')
                ->exists();

            if (!$hasDeposit) {
                return response()->json([
                    'success' => false,
                    'message' => __('You must pay the deposit to participate in this auction.'),
                    'deposit_required' => true,
                    'deposit_amount'   => $auction->deposit_amount,
                ], 422);
            }
        }

        // 6. Check user auto_bid setting if trying to auto bid
        if ($request->boolean('is_auto_bid') && !$user->auto_bid_enabled) {
            return response()->json([
                'success' => false,
                'message' => __('You must enable auto-bidding in your account settings first.'),
            ], 422);
        }

        // ── Place Bid ──────────────────────────────────────────────────────

        DB::transaction(function () use ($request, $auction, $user) {
            $isAutoBid = $request->boolean('is_auto_bid');
            $maxAutoBid = $isAutoBid ? $request->max_auto_bid : null;
            $newBidAmount = $request->amount;

            // Get the current highest active bid (if any)
            $currentHighestBid = $auction->bids()->where('status', 'active')->first();

            // Setup proxy war variables
            $rivalBid = null;
            if ($currentHighestBid && $currentHighestBid->user_id !== $user->id && $currentHighestBid->is_auto_bid) {
                $rivalBid = $currentHighestBid;
            }

            if ($rivalBid) {
                $userMax = $isAutoBid ? $maxAutoBid : $newBidAmount;
                $rivalMax = $rivalBid->max_auto_bid;

                if ($userMax > $rivalMax) {
                    // User wins proxy war
                    $newBidAmount = min($rivalMax + $auction->min_bid_increment, $userMax);
                    
                    // Mark rival as outbid
                    $rivalBid->update(['status' => 'outbid']);
                } else {
                    // Rival wins proxy war
                    $rivalNewAmount = min($userMax + $auction->min_bid_increment, $rivalMax);
                    
                    // User's bid gets placed but immediately outbid
                    $auction->bids()->where('status', 'active')->where('user_id', $user->id)->update(['status' => 'outbid']);
                    Bid::create([
                        'auction_id' => $auction->id,
                        'user_id'    => $user->id,
                        'amount'     => $newBidAmount,
                        'is_auto_bid'=> $isAutoBid,
                        'max_auto_bid'=> $maxAutoBid,
                        'status'     => 'outbid',
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                    $auction->increment('bids_count');

                    // Rival's new winning bid
                    $auction->bids()->where('status', 'active')->where('user_id', $rivalBid->user_id)->update(['status' => 'outbid']);
                    Bid::create([
                        'auction_id' => $auction->id,
                        'user_id'    => $rivalBid->user_id,
                        'amount'     => $rivalNewAmount,
                        'is_auto_bid'=> true,
                        'max_auto_bid'=> $rivalMax,
                        'status'     => 'active',
                        'ip_address' => 'system',
                        'user_agent' => 'proxy-bid',
                    ]);
                    $auction->increment('bids_count');

                    // Auto-extend logic
                    $this->handleAutoExtend($auction);
                    return; // Early return because proxy logic handled the new state
                }
            }

            // Normal flow (or User won proxy war)
            // Mark previous active bids as outbid
            $auction->bids()
                ->where('status', 'active')
                ->where('user_id', '!=', $user->id)
                ->update(['status' => 'outbid']);

            // Mark user's own previous bid as outbid
            $auction->bids()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->update(['status' => 'outbid']);

            // Create new bid
            $bid = Bid::create([
                'auction_id'   => $auction->id,
                'user_id'      => $user->id,
                'amount'       => $newBidAmount,
                'is_auto_bid'  => $isAutoBid,
                'max_auto_bid' => $maxAutoBid,
                'status'       => 'active',
                'ip_address'   => $request->ip(),
                'user_agent'   => $request->userAgent(),
            ]);

            // Update bids count
            $auction->increment('bids_count');

            // Auto-extend
            $this->handleAutoExtend($auction);

            return $bid;
        });

        $auction->refresh();

        return response()->json([
            'success'       => true,
            'message'       => __('Bid placed successfully!'),
            'current_price' => $auction->current_price,
            'bids_count'    => $auction->bids_count,
            'end_time'      => $auction->end_time->toISOString(),
            'time_remaining'=> $auction->time_remaining,
        ], 201);
    }

    /**
     * Handle auto-extension of auction time if bid is within threshold.
     */
    protected function handleAutoExtend(Auction $auction): void
    {
        $extendMinutes = $auction->auto_extend_minutes ?? 2;
        if ($extendMinutes <= 0) {
            return;
        }
        if ($auction->end_time->lessThanOrEqualTo(now()->addMinutes($extendMinutes))) {
            $auction->update([
                'end_time' => now()->addMinutes($extendMinutes),
            ]);
        }
    }

    /**
     * Update an active bid (for modifying amount or auto-bid settings).
     */
    public function update(Request $request, Auction $auction, Bid $bid): JsonResponse
    {
        $user = $request->user();

        if ($bid->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => __('Unauthorized.')], 403);
        }

        if ($bid->status !== 'active') {
            return response()->json(['success' => false, 'message' => __('You can only update an active bid.')], 422);
        }

        if ($auction->status !== 'live' || now()->isAfter($auction->end_time)) {
            return response()->json(['success' => false, 'message' => __('Auction is not live.')], 422);
        }

        $request->validate([
            'amount'       => 'nullable|numeric|min:' . $bid->amount,
            'is_auto_bid'  => 'nullable|boolean',
            'max_auto_bid' => 'nullable|numeric|gte:amount',
        ]);

        $newAmount = $request->input('amount', $bid->amount);
        $isAutoBid = $request->input('is_auto_bid', $bid->is_auto_bid);
        $maxAutoBid = $isAutoBid ? $request->input('max_auto_bid', $bid->max_auto_bid) : null;

        // If they are just updating their proxy, we just save it.
        // If they are increasing their explicit bid amount, we update that.
        $bid->update([
            'amount'       => $newAmount,
            'is_auto_bid'  => $isAutoBid,
            'max_auto_bid' => $maxAutoBid,
        ]);

        // If their new amount is higher, they are just bidding against themselves.
        // It does not trigger proxy war unless another user outbids them later.

        return response()->json([
            'success' => true,
            'message' => __('Bid updated successfully.'),
            'bid'     => $bid,
        ]);
    }

    /**
     * Get the current user's bids.
     */
    public function myBids(Request $request): JsonResponse
    {
        $bids = Bid::where('user_id', $request->user()->id)
            ->with(['auction.vehicle.primaryImage'])
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $bids->items() ? array_map(fn ($bid) => [
                'id'      => $bid->id,
                'amount'  => $bid->amount,
                'status'  => $bid->status,
                'auction' => [
                    'id'            => $bid->auction->id,
                    'title'         => $bid->auction->title,
                    'current_price' => $bid->auction->current_price,
                    'status'        => $bid->auction->status,
                    'end_time'      => $bid->auction->end_time?->toISOString(),
                    'image_url'     => $bid->auction->vehicle?->primary_image_url,
                ],
                'created_at' => $bid->created_at->toISOString(),
            ], $bids->items()) : [],
            'meta' => [
                'current_page' => $bids->currentPage(),
                'last_page'    => $bids->lastPage(),
                'total'        => $bids->total(),
            ],
        ]);
    }

    /**
     * Get auctions won by current user.
     */
    public function wonAuctions(Request $request): JsonResponse
    {
        $auctions = Auction::where('winner_id', $request->user()->id)
            ->with(['vehicle.primaryImage'])
            ->latest()
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $auctions->items() ? array_map(fn ($auction) => [
                'id'                 => $auction->id,
                'title'              => $auction->title,
                'winning_bid_amount' => $auction->winning_bid_amount,
                'sold_at'            => $auction->sold_at?->toISOString(),
                'image_url'          => $auction->vehicle?->primary_image_url,
                'vehicle'            => [
                    'make'  => $auction->vehicle?->make,
                    'model' => $auction->vehicle?->model,
                    'year'  => $auction->vehicle?->year,
                ],
            ], $auctions->items()) : [],
            'meta' => [
                'current_page' => $auctions->currentPage(),
                'last_page'    => $auctions->lastPage(),
                'total'        => $auctions->total(),
            ],
        ]);
    }
}
