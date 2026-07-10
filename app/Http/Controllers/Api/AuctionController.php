<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuctionResource;
use App\Http\Resources\BidResource;
use App\Models\Auction;
use App\Models\AuctionWatchlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AuctionController extends Controller
{
    /**
     * List auctions with filtering & pagination.
     */
    #[OA\Get(
        path: '/api/auctions',
        summary: 'Get Available Auctions',
        description: 'Returns a paginated list of available auctions (live and scheduled by default). Requires Bearer Token.',
        security: [['bearerAuth' => []]],
        tags: ['Auctions'],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', required: false, description: 'Filter by status (e.g. live, scheduled, ended)', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Page number', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, description: 'Items per page', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful Response'
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated'
            )
        ]
    )]
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

        return $this->successResponse(
            AuctionResource::collection($auctions->items()),
            null,
            200,
            [
                'current_page' => $auctions->currentPage(),
                'last_page'    => $auctions->lastPage(),
                'total'        => $auctions->total(),
                'per_page'     => $auctions->perPage(),
            ]
        );
    }

    /**
     * Get auction details.
     */
    #[OA\Get(
        path: '/api/auctions/{auction}',
        summary: 'Get Auction Details',
        description: 'Returns the full details of a specific auction. Optionally uses Bearer Token to return user-specific context (is_watching, user_highest_bid).',
        security: [['bearerAuth' => []]],
        tags: ['Auctions'],
        parameters: [
            new OA\Parameter(name: 'auction', in: 'path', required: true, description: 'Auction ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful Response'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden - KYC not approved'),
            new OA\Response(response: 404, description: 'Auction Not Found')
        ]
    )]
    public function show(Request $request, Auction $auction): JsonResponse
    {
        $user = $request->user();

        // Check if user is approved (KYC) just like in the bidder dashboard
        if ($user->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => __('Please complete identity verification to view auctions.'),
            ], 403);
        }

        // Load relations including bids.user just like bidder dashboard
        $auction->load(['vehicle.images', 'vehicle.primaryImage', 'winner', 'highestBid', 'bids' => function ($query) {
            $query->where('status', 'active')->with('user:id,first_name,last_name,profile_photo');
        }]);
        $auction->increment('views_count');

        // Attach user context
        $userId = $user->id;
        $auction->is_watching = AuctionWatchlist::where('auction_id', $auction->id)
            ->where('user_id', $userId)->exists();
        $auction->user_highest_bid = $auction->bids()
            ->where('user_id', $userId)
            ->max('amount');
        $auction->has_deposited = $auction->deposits()
            ->where('user_id', $userId)
            ->where('status', 'held')
            ->exists();

        return $this->successResponse(new AuctionResource($auction));
    }

    /**
     * Get auction bid history.
     */
    #[OA\Get(
        path: '/api/auctions/{auction}/bids',
        summary: 'Get Auction Bids',
        description: 'Returns a paginated list of active bids for a specific auction. Requires Bearer Token.',
        security: [['bearerAuth' => []]],
        tags: ['Auctions'],
        parameters: [
            new OA\Parameter(name: 'auction', in: 'path', required: true, description: 'Auction ID', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Page number', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, description: 'Items per page', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful Response'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Forbidden - KYC not approved'),
            new OA\Response(response: 404, description: 'Auction Not Found')
        ]
    )]
    public function bids(Request $request, Auction $auction): JsonResponse
    {
        $user = $request->user();

        if ($user->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => __('Please complete identity verification to view auction bids.'),
            ], 403);
        }

        $bids = $auction->bids()
            ->with('user:id,first_name,last_name,profile_photo')
            ->where('status', 'active')
            ->paginate($request->input('per_page', 20));

        return $this->successResponse(
            BidResource::collection($bids->items()),
            null,
            200,
            [
                'current_page' => $bids->currentPage(),
                'last_page'    => $bids->lastPage(),
                'total'        => $bids->total(),
                'per_page'     => $bids->perPage(),
            ]
        );
    }

    /**
     * Toggle watchlist (add/remove).
     */
    #[OA\Post(
        path: '/api/auctions/{auction}/watch',
        summary: 'Toggle Watchlist',
        description: 'Adds or removes the specified auction from the user\'s watchlist.',
        security: [['bearerAuth' => []]],
        tags: ['Auctions'],
        parameters: [
            new OA\Parameter(name: 'auction', in: 'path', required: true, description: 'Auction ID', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful Response'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Auction Not Found')
        ]
    )]
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

        return $this->successResponse(['watching' => $watching], $message);
    }

    /**
     * Get user's watchlist.
     */
    #[OA\Get(
        path: '/api/auctions/watchlist',
        summary: 'Get Watchlist',
        description: 'Returns a paginated list of auctions that the user has added to their watchlist.',
        security: [['bearerAuth' => []]],
        tags: ['Auctions'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Page number', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, description: 'Items per page', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful Response'),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function watchlist(Request $request): JsonResponse
    {
        $auctions = Auction::with(['vehicle.primaryImage', 'highestBid'])
            ->whereHas('watchlist', fn ($q) => $q->where('user_id', $request->user()->id))
            ->paginate($request->input('per_page', 12));

        return $this->successResponse(
            AuctionResource::collection($auctions->items()),
            null,
            200,
            [
                'current_page' => $auctions->currentPage(),
                'last_page'    => $auctions->lastPage(),
                'total'        => $auctions->total(),
            ]
        );
    }

    /**
     * List auctions created by the current user.
     */
    #[OA\Get(
        path: '/api/auctions/my',
        summary: 'Get My Auctions',
        description: 'Returns a paginated list of auctions created by the current user.',
        security: [['bearerAuth' => []]],
        tags: ['Auctions'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Page number', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, description: 'Items per page', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful Response'),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function myAuctions(Request $request): JsonResponse
    {
        $auctions = Auction::where('created_by', $request->user()->id)
            ->with(['vehicle.images', 'highestBid'])
            ->withCount('bids')
            ->latest()
            ->paginate($request->input('per_page', 12));

        return $this->successResponse(
            AuctionResource::collection($auctions->items()),
            null,
            200,
            [
                'current_page' => $auctions->currentPage(),
                'last_page'    => $auctions->lastPage(),
                'total'        => $auctions->total(),
            ]
        );
    }

    /**
     * Create a new auction.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'start_price' => 'required|numeric|min:0',
            'reserve_price' => 'nullable|numeric|min:0',
            'min_bid_increment' => 'required|numeric|min:1',
            'buy_now_price' => 'nullable|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location_ar' => 'nullable|string|max:255',
            'location_en' => 'nullable|string|max:255',
            'auto_extend_minutes' => 'nullable|integer|min:0'
        ]);

        $vehicle = \App\Models\Vehicle::findOrFail($validated['vehicle_id']);

        // Authorize vehicle ownership and check if approved
        \Illuminate\Support\Facades\Gate::authorize('update', $vehicle); // Ensures they own it
        if ($vehicle->status !== 'approved') {
            return $this->errorResponse(__('Vehicle must be approved before creating an auction.'), 422);
        }

        $validated['created_by'] = $request->user()->id;
        $validated['status'] = 'draft'; // Always draft initially
        $validated['deposit_required'] = $request->has('deposit_required') ? $request->boolean('deposit_required') : ($validated['deposit_amount'] > 0);

        $auction = Auction::create($validated);

        return $this->successResponse(
            new AuctionResource($auction),
            __('Auction created successfully as a draft. It is pending admin review.'),
            201
        );
    }

    /**
     * Update an existing draft auction.
     */
    public function update(Request $request, Auction $auction): JsonResponse
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $auction);

        $validated = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'start_price' => 'required|numeric|min:0',
            'reserve_price' => 'nullable|numeric|min:0',
            'min_bid_increment' => 'required|numeric|min:1',
            'buy_now_price' => 'nullable|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location_ar' => 'nullable|string|max:255',
            'location_en' => 'nullable|string|max:255',
            'auto_extend_minutes' => 'nullable|integer|min:0'
        ]);

        $validated['deposit_required'] = $request->has('deposit_required') ? $request->boolean('deposit_required') : ($validated['deposit_amount'] > 0);
        $validated['status'] = 'draft';

        $auction->update($validated);

        return $this->successResponse(
            new AuctionResource($auction),
            __('Auction updated successfully. It remains in draft status pending review.')
        );
    }

    /**
     * Delete an auction.
     */
    public function destroy(Request $request, Auction $auction): JsonResponse
    {
        \Illuminate\Support\Facades\Gate::authorize('delete', $auction);

        $auction->delete();

        return $this->successResponse(null, __('Auction deleted successfully.'));
    }

    /**
     * Upload images for an auction.
     */
    public function uploadImages(Request $request, Auction $auction): JsonResponse
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $auction);

        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('images')) {
            $existingCount = $auction->images()->count();
            foreach ($request->file('images') as $index => $imageFile) {
                $path = $imageFile->store('auctions', 'public');
                $auction->images()->create([
                    'image_path' => $path,
                    'is_primary' => ($existingCount === 0 && $index === 0),
                    'sort_order' => $existingCount + $index
                ]);
            }
            
            if (!$auction->primaryImage && $auction->images()->count() > 0) {
                $firstImage = $auction->images()->first();
                $firstImage->update(['is_primary' => true]);
            }
        }

        return $this->successResponse(
            new AuctionResource($auction->load('images')),
            __('Images uploaded successfully.')
        );
    }
    
    /**
     * Delete an image from an auction.
     */
    public function deleteImage(Request $request, Auction $auction, $imageId): JsonResponse
    {
        \Illuminate\Support\Facades\Gate::authorize('update', $auction);
        
        $image = $auction->images()->findOrFail($imageId);
        \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
        $image->delete();
        
        return $this->successResponse(null, __('Image deleted successfully.'));
    }
}
