<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuctionResource;
use App\Http\Resources\BidResource;
use App\Models\Auction;
use App\Models\AuctionWatchlist;
use App\Models\Bid;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            new OA\Parameter(name: 'search', in: 'query', required: false, description: 'Search term (title, make, model)', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'make', in: 'query', required: false, description: 'Filter by vehicle make', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'location', in: 'query', required: false, description: 'Filter by location', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'condition', in: 'query', required: false, description: 'Filter by condition (e.g. new, used)', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'year_min', in: 'query', required: false, description: 'Minimum year', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'year_max', in: 'query', required: false, description: 'Maximum year', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'price_min', in: 'query', required: false, description: 'Minimum start price', schema: new OA\Schema(type: 'number')),
            new OA\Parameter(name: 'price_max', in: 'query', required: false, description: 'Maximum start price', schema: new OA\Schema(type: 'number')),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Page number', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, description: 'Items per page', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            [
                                'id' => 1,
                                'title' => 'تويوتا كامري 2022',
                                'title_ar' => 'تويوتا كامري 2022',
                                'title_en' => 'Toyota Camry 2022',
                                'description' => 'سيارة بحالة ممتازة وخالية من الصدمات...',
                                'location' => 'الرياض',
                                'start_price' => 50000,
                                'current_price' => 53000,
                                'min_bid_increment' => 500,
                                'buy_now_price' => 70000,
                                'reserve_met' => true,
                                'deposit_required' => true,
                                'deposit_amount' => 1000,
                                'start_time' => '2023-11-01T10:00:00.000000Z',
                                'end_time' => '2023-11-10T10:00:00.000000Z',
                                'time_remaining' => 777600,
                                'is_live' => true,
                                'status' => 'live',
                                'is_featured' => true,
                                'bids_count' => 6,
                                'views_count' => 350,
                                'vehicle' => [
                                    'id' => 10,
                                    'title' => 'Toyota Camry',
                                    'make' => 'Toyota',
                                    'model' => 'Camry',
                                    'year' => 2022,
                                    'mileage' => 45000,
                                    'color' => 'أبيض',
                                    'condition' => 'used',
                                    'fuel_type' => 'petrol',
                                    'transmission' => 'automatic',
                                    'primary_image_url' => 'https://example.com/storage/auctions/camry-main.jpg',
                                    'images' => [
                                        [
                                            'id' => 25,
                                            'url' => 'https://example.com/storage/auctions/camry-main.jpg',
                                            'is_primary' => true
                                        ]
                                    ]
                                ],
                                'winner' => null,
                                'winning_bid_amount' => null,
                                'user_highest_bid' => 52000,
                                'is_watching' => true,
                                'has_deposited' => true,
                                'created_at' => '2023-10-25T14:30:00.000000Z'
                            ]
                        ],
                        'meta' => [
                            'current_page' => 1,
                            'last_page' => 5,
                            'total' => 52,
                            'per_page' => 12
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(
                    example: [
                        'message' => 'Unauthenticated.'
                    ]
                )
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

        // Search in title (auction), make, and model (vehicle)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title_ar', 'like', '%' . $search . '%')
                  ->orWhere('title_en', 'like', '%' . $search . '%')
                  ->orWhereHas('vehicle', function ($vq) use ($search) {
                      $vq->where('make_ar', 'like', '%' . $search . '%')
                         ->orWhere('make_en', 'like', '%' . $search . '%')
                         ->orWhere('model_ar', 'like', '%' . $search . '%')
                         ->orWhere('model_en', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->filled('make')) {
            $query->whereHas('vehicle', fn ($q) => $q->where(fn ($sub) => $sub->where('make_ar', 'like', '%' . $request->make . '%')->orWhere('make_en', 'like', '%' . $request->make . '%')));
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('condition')) {
            $query->whereHas('vehicle', fn ($q) => $q->where('condition', $request->condition));
        }

        if ($request->filled('year_min')) {
            $query->whereHas('vehicle', fn ($q) => $q->where('year', '>=', $request->year_min));
        }

        if ($request->filled('year_max')) {
            $query->whereHas('vehicle', fn ($q) => $q->where('year', '<=', $request->year_max));
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
            new OA\Response(
                response: 200, 
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            'id' => 1,
                            'title' => 'تويوتا كامري 2022',
                            'title_ar' => 'تويوتا كامري 2022',
                            'title_en' => 'Toyota Camry 2022',
                            'description' => 'سيارة بحالة ممتازة وخالية من الصدمات...',
                            'location' => 'الرياض',
                            'start_price' => 50000,
                            'current_price' => 53000,
                            'min_bid_increment' => 500,
                            'buy_now_price' => 70000,
                            'reserve_met' => true,
                            'deposit_required' => true,
                            'deposit_amount' => 1000,
                            'start_time' => '2023-11-01T10:00:00.000000Z',
                            'end_time' => '2023-11-10T10:00:00.000000Z',
                            'time_remaining' => 777600,
                            'is_live' => true,
                            'status' => 'live',
                            'is_featured' => true,
                            'bids_count' => 6,
                            'views_count' => 350,
                            'vehicle' => [
                                'id' => 10,
                                'title' => 'Toyota Camry',
                                'make' => 'Toyota',
                                'model' => 'Camry',
                                'year' => 2022,
                                'mileage' => 45000,
                                'color' => 'أبيض',
                                'condition' => 'used',
                                'fuel_type' => 'petrol',
                                'transmission' => 'automatic',
                                'primary_image_url' => 'https://example.com/storage/auctions/camry-main.jpg',
                                'images' => [
                                    [
                                        'id' => 25,
                                        'url' => 'https://example.com/storage/auctions/camry-main.jpg',
                                        'is_primary' => true
                                    ]
                                ]
                            ],
                            'winner' => null,
                            'winning_bid_amount' => null,
                            'user_highest_bid' => 52000,
                            'is_watching' => true,
                            'has_deposited' => true,
                            'bids' => [
                                [
                                    'id' => 1,
                                    'amount' => 53000,
                                    'status' => 'active',
                                    'is_auto_bid' => false,
                                    'bidder' => [
                                        'id' => 5,
                                        'name' => 'محمد أحمد',
                                        'photo' => 'https://example.com/storage/avatars/default.png'
                                    ],
                                    'created_at' => '2023-11-05T12:00:00.000000Z'
                                ]
                            ],
                            'created_at' => '2023-10-25T14:30:00.000000Z'
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(example: ['message' => 'Unauthenticated.'])
            ),
            new OA\Response(
                response: 403, 
                description: 'Forbidden - KYC not approved',
                content: new OA\JsonContent(example: ['success' => false, 'message' => 'Please complete identity verification to view auctions.'])
            ),
            new OA\Response(
                response: 404, 
                description: 'Auction Not Found',
                content: new OA\JsonContent(example: ['success' => false, 'message' => 'Record not found.'])
            )
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
            new OA\Response(
                response: 200, 
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            [
                                'id' => 1,
                                'amount' => 53000,
                                'status' => 'active',
                                'is_auto_bid' => false,
                                'bidder' => [
                                    'id' => 5,
                                    'name' => 'محمد أحمد',
                                    'photo' => 'https://example.com/storage/avatars/default.png'
                                ],
                                'created_at' => '2023-11-05T12:00:00.000000Z'
                            ],
                            [
                                'id' => 2,
                                'amount' => 52000,
                                'status' => 'active',
                                'is_auto_bid' => false,
                                'bidder' => [
                                    'id' => 6,
                                    'name' => 'علي صالح',
                                    'photo' => 'https://example.com/storage/avatars/ali.png'
                                ],
                                'created_at' => '2023-11-05T11:50:00.000000Z'
                            ]
                        ],
                        'meta' => [
                            'current_page' => 1,
                            'last_page' => 2,
                            'total' => 30,
                            'per_page' => 20
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(example: ['message' => 'Unauthenticated.'])
            ),
            new OA\Response(
                response: 403, 
                description: 'Forbidden - KYC not approved',
                content: new OA\JsonContent(example: ['success' => false, 'message' => 'Please complete identity verification to view auctions.'])
            ),
            new OA\Response(
                response: 404, 
                description: 'Auction Not Found',
                content: new OA\JsonContent(example: ['success' => false, 'message' => 'Record not found.'])
            )
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
            new OA\Response(
                response: 200, 
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            'watching' => true
                        ],
                        'message' => 'Added to watchlist.'
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(example: ['message' => 'Unauthenticated.'])
            ),
            new OA\Response(
                response: 404, 
                description: 'Auction Not Found',
                content: new OA\JsonContent(example: ['success' => false, 'message' => 'Record not found.'])
            )
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
            new OA\Response(
                response: 200, 
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            [
                                'id' => 1,
                                'title' => 'تويوتا كامري 2022',
                                'title_ar' => 'تويوتا كامري 2022',
                                'title_en' => 'Toyota Camry 2022',
                                'description' => 'سيارة بحالة ممتازة وخالية من الصدمات...',
                                'location' => 'الرياض',
                                'start_price' => 50000,
                                'current_price' => 53000,
                                'min_bid_increment' => 500,
                                'buy_now_price' => 70000,
                                'reserve_met' => true,
                                'deposit_required' => true,
                                'deposit_amount' => 1000,
                                'start_time' => '2023-11-01T10:00:00.000000Z',
                                'end_time' => '2023-11-10T10:00:00.000000Z',
                                'time_remaining' => 777600,
                                'is_live' => true,
                                'status' => 'live',
                                'is_featured' => true,
                                'bids_count' => 6,
                                'views_count' => 350,
                                'vehicle' => [
                                    'id' => 10,
                                    'title' => 'Toyota Camry',
                                    'make' => 'Toyota',
                                    'model' => 'Camry',
                                    'year' => 2022,
                                    'mileage' => 45000,
                                    'color' => 'أبيض',
                                    'condition' => 'used',
                                    'fuel_type' => 'petrol',
                                    'transmission' => 'automatic',
                                    'primary_image_url' => 'https://example.com/storage/auctions/camry-main.jpg',
                                    'images' => [
                                        [
                                            'id' => 25,
                                            'url' => 'https://example.com/storage/auctions/camry-main.jpg',
                                            'is_primary' => true
                                        ]
                                    ]
                                ],
                                'winner' => null,
                                'winning_bid_amount' => null,
                                'created_at' => '2023-10-25T14:30:00.000000Z'
                            ]
                        ],
                        'meta' => [
                            'current_page' => 1,
                            'last_page' => 1,
                            'total' => 1,
                            'per_page' => 12
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(example: ['message' => 'Unauthenticated.'])
            )
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
            new OA\Response(
                response: 200, 
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            [
                                'id' => 1,
                                'title' => 'تويوتا كامري 2022',
                                'title_ar' => 'تويوتا كامري 2022',
                                'title_en' => 'Toyota Camry 2022',
                                'description' => 'سيارة بحالة ممتازة وخالية من الصدمات...',
                                'location' => 'الرياض',
                                'start_price' => 50000,
                                'current_price' => 53000,
                                'min_bid_increment' => 500,
                                'buy_now_price' => 70000,
                                'reserve_met' => true,
                                'deposit_required' => true,
                                'deposit_amount' => 1000,
                                'start_time' => '2023-11-01T10:00:00.000000Z',
                                'end_time' => '2023-11-10T10:00:00.000000Z',
                                'time_remaining' => 777600,
                                'is_live' => true,
                                'status' => 'live',
                                'is_featured' => true,
                                'bids_count' => 6,
                                'views_count' => 350,
                                'vehicle' => [
                                    'id' => 10,
                                    'title' => 'Toyota Camry',
                                    'make' => 'Toyota',
                                    'model' => 'Camry',
                                    'year' => 2022,
                                    'mileage' => 45000,
                                    'color' => 'أبيض',
                                    'condition' => 'used',
                                    'fuel_type' => 'petrol',
                                    'transmission' => 'automatic',
                                    'primary_image_url' => 'https://example.com/storage/auctions/camry-main.jpg',
                                    'images' => [
                                        [
                                            'id' => 25,
                                            'url' => 'https://example.com/storage/auctions/camry-main.jpg',
                                            'is_primary' => true
                                        ]
                                    ]
                                ],
                                'winner' => null,
                                'winning_bid_amount' => null,
                                'created_at' => '2023-10-25T14:30:00.000000Z'
                            ]
                        ],
                        'meta' => [
                            'current_page' => 1,
                            'last_page' => 1,
                            'total' => 1,
                            'per_page' => 12
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(example: ['message' => 'Unauthenticated.'])
            )
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

    /**
     * Place a bid on an auction.
     */
    #[OA\Post(
        path: '/api/auctions/{auction}/bid',
        summary: 'Place a bid',
        description: 'Place a new bid or proxy bid on a live auction.',
        security: [['bearerAuth' => []]],
        tags: ['Auctions'],
        parameters: [
            new OA\Parameter(name: 'auction', in: 'path', required: true, description: 'Auction ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['amount'],
                properties: [
                    new OA\Property(property: 'amount', type: 'number', description: 'Bid amount'),
                    new OA\Property(property: 'is_auto_bid', type: 'boolean', description: 'Enable auto/proxy bidding'),
                    new OA\Property(property: 'max_auto_bid', type: 'number', description: 'Maximum amount for proxy bidding'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201, 
                description: 'Bid placed successfully',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            'new_price' => 53000,
                            'current_price' => 53000,
                            'bids_count' => 7,
                            'end_time' => '2023-11-10T10:00:00.000000Z',
                            'time_left_seconds' => 777600,
                            'time_remaining' => 777600,
                            'bid_status' => 'success'
                        ],
                        'message' => 'Your bid has been placed successfully!'
                    ]
                )
            ),
            new OA\Response(
                response: 422, 
                description: 'Validation error or business logic failure',
                content: new OA\JsonContent(example: ['success' => false, 'message' => 'Your bid must be at least 55000.', 'minimum_bid' => 55000])
            ),
            new OA\Response(
                response: 403, 
                description: 'Unauthorized or blocked',
                content: new OA\JsonContent(example: ['success' => false, 'message' => 'You have been blocked from participating in this auction.'])
            )
        ]
    )]
    public function placeBid(Request $request, Auction $auction): JsonResponse
    {
        $user = $request->user();

        // ── Validations ────────────────────────────────────────────────────

        // 0. Must not be paused
        if ($auction->is_paused) {
            return $this->errorResponse(__('This auction is currently paused by admin.'), 422);
        }

        // 0.5 Must not be blocked from this auction
        $isBlocked = \Illuminate\Support\Facades\DB::table('auction_blocklists')
            ->where('auction_id', $auction->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($isBlocked) {
            return $this->errorResponse(__('You have been blocked from participating in this auction.'), 403);
        }

        // 1. Must be live
        if ($auction->status !== 'live') {
            return $this->errorResponse(__('This auction is not currently live.'), 422);
        }

        // 2. Must not have ended
        if (now()->isAfter($auction->end_time)) {
            return $this->errorResponse(__('This auction has already ended.'), 422);
        }

        // 3. User must be approved and have kyc level >= 1
        if ($user->status !== 'approved') {
            return $this->errorResponse(__('Your account must be approved to participate in auctions.'), 403);
        }

        // 4. Validate bid amount
        $request->validate([
            'amount'       => 'required|numeric|min:0',
            'is_auto_bid'  => 'nullable|boolean',
            'max_auto_bid' => 'required_if:is_auto_bid,true|numeric|gte:amount',
        ]);

        $currentPrice  = $auction->current_price;
        $minimumBid    = $currentPrice + $auction->min_bid_increment;

        if ($request->amount < $minimumBid && !$auction->bids()->where('user_id', $user->id)->where('status', 'active')->exists()) {
            return $this->errorResponse(__('Your bid must be at least :amount.', ['amount' => number_format($minimumBid, 2)]), 422, [
                'minimum_bid' => $minimumBid,
            ]);
        }

        $isAutoBid = $request->boolean('is_auto_bid');
        $maxAutoBid = $isAutoBid ? $request->max_auto_bid : null;

        // 6. Wallet Check: User must have enough available balance for the bid amount
        $wallet = $user->wallet;
        $newTotalRequired = $isAutoBid ? $maxAutoBid : $request->amount;
        
        $currentActiveBid = $auction->bids()->where('user_id', $user->id)->where('status', 'active')->first();
        $currentActiveBidAmount = 0;
        if ($currentActiveBid) {
            $currentActiveBidAmount = $currentActiveBid->is_auto_bid ? max($currentActiveBid->amount, $currentActiveBid->max_auto_bid) : $currentActiveBid->amount;
        }
        
        $additionalRequired = max(0, $newTotalRequired - $currentActiveBidAmount);
        
        if (!$wallet || $wallet->available_balance < $additionalRequired) {
            return $this->errorResponse(__('Insufficient available balance. You need at least :amount SAR available in your wallet to place this bid.', ['amount' => number_format($additionalRequired)]), 422);
        }

        // ── Place Bid ──────────────────────────────────────────────────────

        $result = DB::transaction(function () use ($request, $auction, $user) {
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

                    // Update auction values
                    $auction->update([
                        'winning_bid_amount' => $rivalNewAmount,
                        'winner_id' => $rivalBid->user_id
                    ]);

                    // Auto-extend logic
                    $this->handleAutoExtend($auction);
                    
                    return [
                        'status' => 'outbid_immediately',
                        'new_price' => $rivalNewAmount,
                        'message' => __('Your bid was placed, but you have been immediately outbid by an automatic proxy bid!')
                    ];
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

            // Update bids count and winning bid
            $auction->increment('bids_count');
            $auction->update([
                'winning_bid_amount' => $newBidAmount,
                'winner_id' => $user->id
            ]);

            // Auto-extend
            $this->handleAutoExtend($auction);

            return [
                'status' => 'success',
                'new_price' => $newBidAmount,
                'message' => $isAutoBid 
                    ? __('Automatic proxy bid setup successfully at :amount (Max Limit: :max)!', ['amount' => number_format($newBidAmount), 'max' => number_format($maxAutoBid)]) 
                    : __('Your bid has been placed successfully!')
            ];
        });

        $auction->refresh();

        return $this->successResponse([
            'new_price'         => $result['new_price'],
            'current_price'     => $auction->current_price,
            'bids_count'        => $auction->bids_count,
            'end_time'          => $auction->end_time->toISOString(),
            'time_left_seconds' => $auction->time_remaining,
            'time_remaining'    => $auction->time_remaining,
            'bid_status'        => $result['status']
        ], $result['message'], 201);
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
    #[OA\Put(
        path: '/api/auctions/{auction}/bids/{bid}',
        summary: 'Update a bid',
        description: 'Modify an active bid amount or auto-bid settings.',
        security: [['bearerAuth' => []]],
        tags: ['Auctions'],
        parameters: [
            new OA\Parameter(name: 'auction', in: 'path', required: true, description: 'Auction ID', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'bid', in: 'path', required: true, description: 'Bid ID', schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'amount', type: 'number', description: 'New bid amount'),
                    new OA\Property(property: 'is_auto_bid', type: 'boolean', description: 'Enable auto/proxy bidding'),
                    new OA\Property(property: 'max_auto_bid', type: 'number', description: 'Maximum amount for proxy bidding'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Bid updated successfully'),
            new OA\Response(
                response: 422, 
                description: 'Validation error',
                content: new OA\JsonContent(example: ['success' => false, 'message' => 'You can only update an active bid.'])
            ),
            new OA\Response(
                response: 403, 
                description: 'Unauthorized',
                content: new OA\JsonContent(example: ['success' => false, 'message' => 'Unauthorized.'])
            )
        ]
    )]
    public function updateBid(Request $request, Auction $auction, Bid $bid): JsonResponse
    {
        $user = $request->user();

        if ($bid->user_id !== $user->id) {
            return $this->errorResponse(__('Unauthorized.'), 403);
        }

        if ($bid->status !== 'active') {
            return $this->errorResponse(__('You can only update an active bid.'), 422);
        }

        if ($auction->status !== 'live' || now()->isAfter($auction->end_time)) {
            return $this->errorResponse(__('Auction is not live.'), 422);
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

        return $this->successResponse([
            'bid' => $bid
        ], __('Bid updated successfully.'));
    }


    #[OA\Get(path: '/api/my/bids', summary: 'Get My Bids', description: 'Returns a paginated list of auctions the current user bid on, with bidder status and stats.', security: [['bearerAuth' => []]], tags: ['Auctions'])]
    #[OA\Parameter(name: 'status', in: 'query', required: false, description: 'Filter by bidder status: winning, outbid, won, lost')]
    #[OA\Parameter(name: 'page', in: 'query', required: false, description: 'Page number')]
    #[OA\Parameter(name: 'per_page', in: 'query', required: false, description: 'Items per page')]
    #[OA\Response(
        response: 200, 
        description: 'Successful Response',
        content: new OA\JsonContent(
            example: [
                'success' => true,
                'data' => [
                    [
                        'id' => 1,
                        'title' => 'تويوتا كامري 2022',
                        'title_ar' => 'تويوتا كامري 2022',
                        'title_en' => 'Toyota Camry 2022',
                        'current_price' => 55000,
                        'status' => 'live',
                        'end_time' => '2023-11-10T10:00:00.000000Z',
                        'user_highest_bid' => 55000,
                        'bidder_status' => 'winning',
                        'vehicle' => [
                            'id' => 10,
                            'title' => 'Toyota Camry',
                            'primary_image_url' => 'https://example.com/storage/auctions/camry-main.jpg'
                        ]
                    ]
                ],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 1,
                    'statistics' => [
                        'total_count' => 10,
                        'winning_count' => 3,
                        'outbid_count' => 2,
                        'won_count' => 1,
                        'lost_count' => 4
                    ]
                ]
            ]
        )
    )]
    public function myBids(Request $request): JsonResponse
    {
        $user = $request->user();
        $status = $request->input('status', 'all'); // all, winning, outbid, won, lost

        // Calculate overall counts for the widgets
        $totalCount = Auction::whereHas('bids', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->count();
        
        $winningCount = Auction::whereHas('bids', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('status', 'live')
          ->where('start_time', '<=', now())
          ->where('end_time', '>=', now())
          ->where('winner_id', $user->id)
          ->count();

        $outbidCount = Auction::whereHas('bids', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('status', 'live')
          ->where('start_time', '<=', now())
          ->where('end_time', '>=', now())
          ->where(function($q) use ($user) {
              $q->where('winner_id', '!=', $user->id)
                ->orWhereNull('winner_id');
          })->count();

        $wonCount = Auction::whereHas('bids', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where(function($q) {
            $q->where('status', 'ended')
              ->orWhere('status', 'sold')
              ->orWhere('end_time', '<', now());
        })->where('winner_id', $user->id)->count();

        $lostCount = Auction::whereHas('bids', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where(function($q) {
            $q->where('status', 'ended')
              ->orWhere('status', 'sold')
              ->orWhere('end_time', '<', now());
        })->where(function($q) use ($user) {
            $q->where('winner_id', '!=', $user->id)
              ->orWhereNull('winner_id');
        })->count();

        // Main query
        $query = Auction::with(['vehicle', 'vehicle.images', 'highestBid', 'bids' => function($q) use ($user) {
            $q->where('user_id', $user->id)->latest();
        }])
        ->whereHas('bids', function($q) use ($user) {
            $q->where('user_id', $user->id);
        });

        // Apply filter status
        if ($status === 'winning') {
            $query->where('status', 'live')
                  ->where('start_time', '<=', now())
                  ->where('end_time', '>=', now())
                  ->where('winner_id', $user->id);
        } elseif ($status === 'outbid') {
            $query->where('status', 'live')
                  ->where('start_time', '<=', now())
                  ->where('end_time', '>=', now())
                  ->where(function($q) use ($user) {
                      $q->where('winner_id', '!=', $user->id)
                        ->orWhereNull('winner_id');
                  });
        } elseif ($status === 'won') {
            $query->where(function($q) {
                $q->where('status', 'ended')
                  ->orWhere('status', 'sold')
                  ->orWhere('end_time', '<', now());
            })->where('winner_id', $user->id);
        } elseif ($status === 'lost') {
            $query->where(function($q) {
                $q->where('status', 'ended')
                  ->orWhere('status', 'sold')
                  ->orWhere('end_time', '<', now());
            })->where(function($q) use ($user) {
                $q->where('winner_id', '!=', $user->id)
                  ->orWhereNull('winner_id');
            });
        }

        $auctions = $query->latest()->paginate($request->input('per_page', 15));

        // Calculate user_max_bid and bidder_status for each item
        foreach ($auctions as $auction) {
            $userMaxBid = 0;
            if ($auction->bids->isNotEmpty()) {
                $userMaxBid = $auction->bids->first()->is_auto_bid ? max($auction->bids->first()->amount, $auction->bids->first()->max_auto_bid) : $auction->bids->first()->amount;
            }
            $auction->user_highest_bid = $userMaxBid;

            // Determine Bidder Status
            if ($auction->status === 'live' && now()->between($auction->start_time, $auction->end_time)) {
                if ($auction->winner_id == $user->id) {
                    $auction->bidder_status = 'winning';
                } else {
                    $auction->bidder_status = 'outbid';
                }
            } else {
                if ($auction->winner_id == $user->id) {
                    $auction->bidder_status = 'won';
                } else {
                    $auction->bidder_status = 'lost';
                }
            }
        }

        return $this->successResponse(
            AuctionResource::collection($auctions->items()),
            null,
            200,
            [
                'current_page' => $auctions->currentPage(),
                'last_page'    => $auctions->lastPage(),
                'total'        => $auctions->total(),
                'statistics'   => [
                    'total_count'   => $totalCount,
                    'winning_count' => $winningCount,
                    'outbid_count'  => $outbidCount,
                    'won_count'     => $wonCount,
                    'lost_count'    => $lostCount,
                ]
            ]
        );
    }

    /**
     * Get auctions won by current user.
     */
    #[OA\Get(path: '/api/my/won', summary: 'Get Won Auctions', description: 'Returns a paginated list of auctions that the current user has won, with payment status filtering and bank accounts for transfer.', security: [['bearerAuth' => []]], tags: ['Auctions'])]
    #[OA\Parameter(name: 'payment_status', in: 'query', required: false, description: 'Filter by payment status: pending, paid')]
    #[OA\Parameter(name: 'page', in: 'query', required: false, description: 'Page number')]
    #[OA\Parameter(name: 'per_page', in: 'query', required: false, description: 'Items per page')]
    #[OA\Response(
        response: 200, 
        description: 'Successful Response',
        content: new OA\JsonContent(
            example: [
                'success' => true,
                'data' => [
                    'auctions' => [
                        [
                            'id' => 1,
                            'title' => 'تويوتا كامري 2022',
                            'title_ar' => 'تويوتا كامري 2022',
                            'title_en' => 'Toyota Camry 2022',
                            'description' => 'سيارة بحالة ممتازة...',
                            'location' => 'الرياض',
                            'start_price' => 50000,
                            'current_price' => 55000,
                            'winning_bid_amount' => 55000,
                            'status' => 'ended',
                            'vehicle' => [
                                'id' => 10,
                                'title' => 'Toyota Camry',
                                'primary_image_url' => 'https://example.com/storage/auctions/camry-main.jpg',
                            ]
                        ]
                    ],
                    'bank_accounts' => [
                        [
                            'bank_name' => 'البنك الأهلي السعودي (SNB)',
                            'beneficiary_name' => 'شركة موترزاد للمزادات',
                            'iban' => 'SA8910000001234567890123'
                        ],
                        [
                            'bank_name' => 'مصرف الراجحي (Al Rajhi)',
                            'beneficiary_name' => 'شركة موترزاد للمزادات',
                            'iban' => 'SA4580000009876543210987'
                        ]
                    ]
                ],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 1,
                    'per_page' => 15
                ]
            ]
        )
    )]
    public function wonAuctions(Request $request): JsonResponse
    {
        $paymentStatusFilter = $request->input('payment_status');

        $query = Auction::where('winner_id', $request->user()->id)
            ->with(['vehicle.primaryImage', 'order', 'highestBid']);

        if (!empty($paymentStatusFilter)) {
            if ($paymentStatusFilter === 'pending') {
                $query->where(function($q) {
                    $q->whereHas('order', function($oq) {
                        $oq->where('payment_status', 'pending');
                    })->orWhereDoesntHave('order');
                });
            } elseif ($paymentStatusFilter === 'paid') {
                $query->whereHas('order', function($oq) {
                    $oq->where('payment_status', 'paid');
                });
            }
        }

        $auctions = $query->latest()->paginate($request->input('per_page', 15));

        $bankAccounts = \App\Models\BankAccount::where('is_active', true)->get();

        if ($bankAccounts->isEmpty()) {
            $bankAccounts = collect([
                (object)[
                    'bank_name' => 'البنك الأهلي السعودي (SNB)',
                    'beneficiary_name' => 'شركة موترزاد للمزادات',
                    'iban' => 'SA8910000001234567890123',
                ],
                (object)[
                    'bank_name' => 'مصرف الراجحي (Al Rajhi)',
                    'beneficiary_name' => 'شركة موترزاد للمزادات',
                    'iban' => 'SA4580000009876543210987',
                ]
            ]);
        }

        return $this->successResponse(
            [
                'auctions' => AuctionResource::collection($auctions->items()),
                'bank_accounts' => $bankAccounts
            ],
            null,
            200,
            [
                'current_page' => $auctions->currentPage(),
                'last_page'    => $auctions->lastPage(),
                'total'        => $auctions->total(),
            ]
        );
    }
}
