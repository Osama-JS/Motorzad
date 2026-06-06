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
            'amount' => 'required|numeric|min:0',
        ]);

        $currentPrice  = $auction->current_price;
        $minimumBid    = $currentPrice + $auction->min_bid_increment;

        if ($request->amount < $minimumBid) {
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

        // ── Place Bid ──────────────────────────────────────────────────────

        DB::transaction(function () use ($request, $auction, $user) {
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
                'auction_id' => $auction->id,
                'user_id'    => $user->id,
                'amount'     => $request->amount,
                'status'     => 'active',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Update bids count
            $auction->increment('bids_count');

            // Auto-extend if bid placed in last X minutes
            $extendThreshold = now()->subMinutes($auction->auto_extend_minutes);
            if ($auction->end_time->lessThanOrEqualTo(now()->addMinutes($auction->auto_extend_minutes))) {
                $auction->update([
                    'end_time' => now()->addMinutes($auction->auto_extend_minutes),
                ]);
            }

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
