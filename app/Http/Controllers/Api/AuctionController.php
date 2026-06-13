<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuctionResource;
use App\Models\Auction;
use App\Models\AuctionWatchlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    /**
     * List auctions with filtering & pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Auction::with(['vehicle.images', 'highestBid'])
            ->withCount('bids');

        // ── Filters ──────────────────────────────────────────────────────
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: show live and scheduled only
            $query->whereIn('status', ['live', 'scheduled']);
        }

        if ($request->filled('featured')) {
            $query->where('is_featured', true);
        }

        if ($request->filled('make')) {
            $query->whereHas('vehicle', fn ($q) => $q->where(fn ($sub) => $sub->where('make_ar', 'like', '%' . $request->make . '%')->orWhere('make_en', 'like', '%' . $request->make . '%')));
        }

        if ($request->filled('price_min')) {
            $query->where('start_price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('start_price', '<=', $request->price_max);
        }

        // ── Sorting ────────────────────────────────────────────────────────
        $sort = $request->input('sort', 'end_time');
        $direction = $request->input('direction', 'asc');
        $query->orderBy($sort, $direction);

        $auctions = $query->paginate($request->input('per_page', 12));

        // Attach user context if authenticated
        $userId = auth('sanctum')->id();
        if ($userId) {
            $watchedIds = AuctionWatchlist::where('user_id', $userId)
                ->pluck('auction_id')->toArray();

            $auctions->getCollection()->transform(function ($auction) use ($watchedIds) {
                $auction->is_watching = in_array($auction->id, $watchedIds);
                return $auction;
            });
        }

        return response()->json([
            'success' => true,
            'data'    => AuctionResource::collection($auctions->items()),
            'meta'    => [
                'current_page' => $auctions->currentPage(),
                'last_page'    => $auctions->lastPage(),
                'total'        => $auctions->total(),
                'per_page'     => $auctions->perPage(),
            ],
        ]);
    }

    /**
     * Get auction details.
     */
    public function show(Request $request, Auction $auction): JsonResponse
    {
        $auction->load(['vehicle.images', 'vehicle.primaryImage', 'winner', 'highestBid']);
        $auction->increment('views_count');

        // Attach user context
        $userId = auth('sanctum')->id();
        if ($userId) {
            $auction->is_watching = AuctionWatchlist::where('auction_id', $auction->id)
                ->where('user_id', $userId)->exists();
            $auction->user_highest_bid = $auction->bids()
                ->where('user_id', $userId)
                ->max('amount');
            $auction->has_deposited = $auction->deposits()
                ->where('user_id', $userId)
                ->where('status', 'held')
                ->exists();
        }

        return response()->json([
            'success' => true,
            'data'    => new AuctionResource($auction),
        ]);
    }

    /**
     * Get auction bid history.
     */
    public function bids(Request $request, Auction $auction): JsonResponse
    {
        $bids = $auction->bids()
            ->with('user:id,first_name,last_name,profile_photo')
            ->where('status', 'active')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => $bids->items() ? array_map(fn ($bid) => [
                'id'         => $bid->id,
                'amount'     => $bid->amount,
                'bidder'     => [
                    'id'       => $bid->user->id,
                    'name'     => $bid->user->full_name,
                    'photo'    => $bid->user->profile_photo_url,
                ],
                'created_at' => $bid->created_at->toISOString(),
            ], $bids->items()) : [],
            'meta'    => [
                'current_page' => $bids->currentPage(),
                'last_page'    => $bids->lastPage(),
                'total'        => $bids->total(),
            ],
        ]);
    }

    /**
     * Toggle watchlist (add/remove).
     */
    public function toggleWatch(Request $request, Auction $auction): JsonResponse
    {
        $userId = $request->user()->id;

        $existing = AuctionWatchlist::where('auction_id', $auction->id)
            ->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
            $watching = false;
            $message  = __('Removed from watchlist.');
        } else {
            AuctionWatchlist::create([
                'auction_id' => $auction->id,
                'user_id'    => $userId,
            ]);
            $watching = true;
            $message  = __('Added to watchlist.');
        }

        return response()->json([
            'success'    => true,
            'watching'   => $watching,
            'message'    => $message,
        ]);
    }

    /**
     * Get user's watchlist.
     */
    public function watchlist(Request $request): JsonResponse
    {
        $auctions = Auction::with(['vehicle.primaryImage', 'highestBid'])
            ->whereHas('watchlist', fn ($q) => $q->where('user_id', $request->user()->id))
            ->paginate($request->input('per_page', 12));

        return response()->json([
            'success' => true,
            'data'    => AuctionResource::collection($auctions->items()),
            'meta'    => [
                'current_page' => $auctions->currentPage(),
                'last_page'    => $auctions->lastPage(),
                'total'        => $auctions->total(),
            ],
        ]);
    }
}
