<?php

namespace App\Http\Controllers\Bidder;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\AuctionWatchlist;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    /**
     * Display a list of auctions with filtering and search.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Safety check: ensure user is verified (approved)
        if ($user->status !== 'approved') {
            return redirect()->route('kyc.index')
                ->with('error', __('Please complete identity verification to access auctions.'));
        }

        $search = $request->input('search');
        $tab = $request->input('tab', 'live'); // live, upcoming, ended, watchlist

        // Check if there are auctions in the DB
        $dbCount = Auction::count();

        // Fetch values for drop-downs
        if ($dbCount > 0) {
            $makes = \App\Models\Vehicle::select('make_ar', 'make_en')->distinct()->get()->map(function($v) {
                return app()->getLocale() === 'ar' ? ($v->make_ar ?: $v->make_en) : ($v->make_en ?: $v->make_ar);
            })->filter()->unique()->values();
            
            $locations = Auction::select('location_ar', 'location_en')->distinct()->get()->map(function($v) {
                return app()->getLocale() === 'ar' ? ($v->location_ar ?: $v->location_en) : ($v->location_en ?: $v->location_ar);
            })->filter()->unique()->values();
            if ($locations->isEmpty()) {
                $locations = collect(['الرياض', 'جدة', 'الدمام']);
            }
        } else {
            $makes = collect(['Mercedes-Benz', 'Porsche', 'BMW', 'Land Rover', 'Audi', 'Lexus', 'Toyota']);
            $locations = collect(['الرياض', 'جدة', 'الدمام']);
        }

        if ($dbCount > 0) {
            $query = Auction::with(['vehicle', 'vehicle.images', 'highestBid']);

            // Apply search
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('title_ar', 'like', "%{$search}%")
                      ->orWhere('title_en', 'like', "%{$search}%")
                      ->orWhereHas('vehicle', function($vq) use ($search) {
                          $vq->where('make_ar', 'like', "%{$search}%")
                            ->orWhere('make_en', 'like', "%{$search}%")
                            ->orWhere('model_ar', 'like', "%{$search}%")
                            ->orWhere('model_en', 'like', "%{$search}%")
                            ->orWhere('year', 'like', "%{$search}%");
                      });
                });
            }

            // Apply Advanced Filters
            if ($request->filled('make')) {
                $query->whereHas('vehicle', function($q) use ($request) {
                    $q->where('make_en', $request->make)
                      ->orWhere('make_ar', $request->make);
                });
            }

            if ($request->filled('location')) {
                $query->where(function($q) use ($request) {
                    $q->where('location_ar', 'like', "%{$request->location}%")
                      ->orWhere('location_en', 'like', "%{$request->location}%");
                });
            }

            if ($request->filled('year_from')) {
                $query->whereHas('vehicle', function($q) use ($request) {
                    $q->where('year', '>=', intval($request->year_from));
                });
            }

            if ($request->filled('year_to')) {
                $query->whereHas('vehicle', function($q) use ($request) {
                    $q->where('year', '<=', intval($request->year_to));
                });
            }

            if ($request->filled('condition')) {
                $query->whereHas('vehicle', function($q) use ($request) {
                    $q->where('condition', $request->condition);
                });
            }

            // Apply tabs
            if ($tab === 'live') {
                $query->where('status', 'live')
                      ->where('start_time', '<=', now())
                      ->where('end_time', '>=', now());
            } elseif ($tab === 'upcoming') {
                $query->where(function($q) {
                    $q->where('status', 'scheduled')
                      ->orWhere('start_time', '>', now());
                });
            } elseif ($tab === 'ended') {
                $query->where(function($q) {
                    $q->where('status', 'ended')
                      ->orWhere('status', 'sold')
                      ->orWhere('end_time', '<', now());
                });
            } elseif ($tab === 'watchlist') {
                $query->whereHas('watchlist', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }

            $auctions = $query->latest()->paginate(9)->withQueryString();
            $usingMock = false;
        } else {
            // Fallback mock auctions for high fidelity design representation
            $mockAuctions = $this->getMockAuctions();

            // Filter mock data
            if (!empty($search)) {
                $mockAuctions = array_filter($mockAuctions, function($auc) use ($search) {
                    return stripos($auc['title_ar'], $search) !== false ||
                           stripos($auc['title_en'], $search) !== false ||
                           stripos($auc['make'], $search) !== false ||
                           stripos($auc['model'], $search) !== false;
                });
            }

            // Apply advanced filters on mock
            if ($request->filled('make')) {
                $mockAuctions = array_filter($mockAuctions, function($auc) use ($request) {
                    return strcasecmp($auc['make'], $request->make) === 0;
                });
            }
            if ($request->filled('location')) {
                $mockAuctions = array_filter($mockAuctions, function($auc) use ($request) {
                    return stripos($auc['location'], $request->location) !== false;
                });
            }
            if ($request->filled('year_from')) {
                $mockAuctions = array_filter($mockAuctions, function($auc) use ($request) {
                    return $auc['year'] >= intval($request->year_from);
                });
            }
            if ($request->filled('year_to')) {
                $mockAuctions = array_filter($mockAuctions, function($auc) use ($request) {
                    return $auc['year'] <= intval($request->year_to);
                });
            }
            if ($request->filled('condition')) {
                $mockAuctions = array_filter($mockAuctions, function($auc) use ($request) {
                    return $auc['condition'] === $request->condition;
                });
            }

            if ($tab === 'live') {
                $mockAuctions = array_filter($mockAuctions, function($auc) {
                    return $auc['status'] === 'live';
                });
            } elseif ($tab === 'upcoming') {
                $mockAuctions = array_filter($mockAuctions, function($auc) {
                    return $auc['status'] === 'upcoming';
                });
            } elseif ($tab === 'ended') {
                $mockAuctions = array_filter($mockAuctions, function($auc) {
                    return $auc['status'] === 'ended';
                });
            } elseif ($tab === 'watchlist') {
                // Return a subset as watched for design demo
                $mockAuctions = array_slice($mockAuctions, 0, 2);
            }

            $auctions = collect(array_values($mockAuctions));
            $usingMock = true;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('bidder.auctions.partials.grid', compact('auctions', 'tab', 'search', 'usingMock'))->render()
            ]);
        }

        return view('bidder.auctions.index', compact('auctions', 'tab', 'search', 'usingMock', 'makes', 'locations'));
    }

    /**
     * Display details of a single auction.
     */
    public function show($id)
    {
        $user = auth()->user();
        if ($user->status !== 'approved') {
            return redirect()->route('kyc.index')
                ->with('error', __('Please complete identity verification to view auctions.'));
        }

        // Try to load from database first
        $auction = Auction::with(['vehicle', 'vehicle.images', 'highestBid', 'bids.user'])->find($id);

        if ($auction) {
            $isWatched = $auction->watchlist()->where('user_id', $user->id)->exists();
            return view('bidder.auctions.show', compact('auction', 'isWatched', 'user'));
        }

        // Fallback to mock single view
        $mockAuctions = $this->getMockAuctions();
        $mockIndex = array_search($id, array_column($mockAuctions, 'id'));

        if ($mockIndex !== false) {
            $auctionData = $mockAuctions[$mockIndex];
            $isWatched = ($id == 1 || $id == 3); // Demo status
            return view('bidder.auctions.show-mock', compact('auctionData', 'isWatched', 'user'));
        }

        abort(404, 'Auction not found');
    }

    /**
     * Submit a bid on an auction.
     */
    public function placeBid(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => __('Please complete identity verification to place bids.')
            ], 403);
        }

        $amount = $request->input('amount');
        $isAutoBid = $request->boolean('is_auto_bid');
        $maxAutoBid = $isAutoBid ? floatval($request->input('max_auto_bid')) : null;
        
        // Find in DB
        $auction = Auction::find($id);
        if ($auction) {
            if ($auction->is_paused) {
                return response()->json([
                    'success' => false,
                    'message' => __('This auction is currently paused by admin.')
                ], 200);
            }
            
            // Check if blocked from this auction
            $isBlocked = \Illuminate\Support\Facades\DB::table('auction_blocklists')
                ->where('auction_id', $auction->id)
                ->where('user_id', $user->id)
                ->exists();
            if ($isBlocked) {
                return response()->json([
                    'success' => false,
                    'message' => __('You have been blocked from participating in this auction.')
                ], 200);
            }
            
            if ($auction->status !== 'live' || now()->gt($auction->end_time)) {
                return response()->json([
                    'success' => false,
                    'message' => __('This auction is not accepting bids.')
                ], 200);
            }

            $currentPrice = $auction->current_price;
            $minBid = $currentPrice + $auction->min_bid_increment;

            if ($amount < $minBid && !$auction->bids()->where('user_id', $user->id)->where('status', 'active')->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Bid must be at least :amount SAR.', ['amount' => number_format($minBid)])
                ], 200);
            }

            if ($isAutoBid && !$maxAutoBid) {
                return response()->json([
                    'success' => false,
                    'message' => __('Please specify a maximum auto bid limit.')
                ], 200);
            }
            if ($isAutoBid && $maxAutoBid < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => __('Maximum auto bid limit must be greater than or equal to the bid amount.')
                ], 200);
            }

            // Wallet Check: User must have enough available balance for the bid amount
            $wallet = $user->wallet;
            $newTotalRequired = $isAutoBid ? $maxAutoBid : $amount;
            
            // If the user already has an active bid on this auction, we only need the difference
            $currentActiveBid = $auction->bids()->where('user_id', $user->id)->where('status', 'active')->first();
            $currentActiveBidAmount = 0;
            if ($currentActiveBid) {
                $currentActiveBidAmount = $currentActiveBid->is_auto_bid ? max($currentActiveBid->amount, $currentActiveBid->max_auto_bid) : $currentActiveBid->amount;
            }
            
            $additionalRequired = max(0, $newTotalRequired - $currentActiveBidAmount);
            
            if (!$wallet || $wallet->available_balance < $additionalRequired) {
                return response()->json([
                    'success' => false,
                    'message' => __('Insufficient available balance. You need at least :amount SAR available in your wallet to place this bid.', ['amount' => number_format($additionalRequired)])
                ], 200);
            }

            // Place Bid with Proxy War Logic
            $result = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $auction, $user, $isAutoBid, $maxAutoBid, $amount) {
                $newBidAmount = $amount;

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
                            'success' => true,
                            'new_price' => $rivalNewAmount,
                            'bids_count' => $auction->bids_count,
                            'time_left_seconds' => $auction->time_remaining,
                            'end_time' => $auction->end_time->toISOString(),
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
                    'success' => true,
                    'new_price' => $newBidAmount,
                    'bids_count' => $auction->bids_count,
                    'time_left_seconds' => $auction->time_remaining,
                    'end_time' => $auction->end_time->toISOString(),
                    'message' => $isAutoBid 
                        ? __('Automatic proxy bid setup successfully at :amount (Max Limit: :max)!', ['amount' => number_format($newBidAmount), 'max' => number_format($maxAutoBid)]) 
                        : __('Your bid has been placed successfully!')
                ];
            });

            return response()->json($result);
        }

        // Handle mock bidding
        $mockAuctions = $this->getMockAuctions();
        $mockIndex = array_search($id, array_column($mockAuctions, 'id'));

        if ($mockIndex !== false) {
            $auc = $mockAuctions[$mockIndex];
            $currentPrice = $auc['current_price'];
            $minBid = $currentPrice + $auc['min_bid_increment'];

            if ($amount < $minBid) {
                return response()->json([
                    'success' => false,
                    'message' => __('Bid must be at least :amount SAR.', ['amount' => number_format($minBid)])
                ], 200);
            }

            if ($isAutoBid && !$maxAutoBid) {
                return response()->json([
                    'success' => false,
                    'message' => __('Please specify a maximum auto bid limit.')
                ], 200);
            }
            if ($isAutoBid && $maxAutoBid < $amount) {
                return response()->json([
                    'success' => false,
                    'message' => __('Maximum auto bid limit must be greater than or equal to the bid amount.')
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => $isAutoBid 
                    ? __('Automatic proxy bid setup successfully (DEMO) at :amount (Max Limit: :max)!', ['amount' => number_format($amount), 'max' => number_format($maxAutoBid)])
                    : __('Your bid has been placed successfully (DEMO)!'),
                'new_price' => $amount,
                'bids_count' => $auc['bids_count'] + 1,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('Auction not found')
        ], 404);
    }

    protected function handleAutoExtend(Auction $auction): void
    {
        $extendMinutes = $auction->auto_extend_minutes ?? 2;
        if ($auction->end_time->lessThanOrEqualTo(now()->addMinutes($extendMinutes))) {
            $auction->update([
                'end_time' => now()->addMinutes($extendMinutes),
            ]);
        }
    }

    /**
     * Toggle Watchlist.
     */
    public function toggleWatchlist(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $auction = Auction::find($id);
        if ($auction) {
            $watchlist = AuctionWatchlist::where('auction_id', $id)->where('user_id', $user->id)->first();
            if ($watchlist) {
                $watchlist->delete();
                $watched = false;
            } else {
                AuctionWatchlist::create(['auction_id' => $id, 'user_id' => $user->id]);
                $watched = true;
            }
            return response()->json(['success' => true, 'watched' => $watched]);
        }

        // Mock watchlist toggle
        return response()->json(['success' => true, 'watched' => !$request->input('currently_watched')]);
    }

    /**
     * Display a list of auctions the bidder has bid on.
     */
    public function myBids(Request $request)
    {
        $user = auth()->user();
        if ($user->status !== 'approved') {
            return redirect()->route('kyc.index')
                ->with('error', __('Please complete identity verification to view your bids.'));
        }

        $status = $request->input('status', 'all'); // all, winning, outbid, won, lost

        // Check if user has real bids
        $realBidsCount = Bid::where('user_id', $user->id)->count();

        if ($realBidsCount > 0) {
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

            $auctions = $query->latest()->paginate(10)->withQueryString();

            // Calculate user_max_bid and bidder_status for each item
            foreach ($auctions as $auction) {
                $userMaxBid = $auction->bids->first()->amount ?? 0;
                $auction->user_max_bid = $userMaxBid;

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
            
            $usingMock = false;
        } else {
            // Mock data representing different states for presentation
            $mockAuctions = $this->getMockAuctions();
            
            // Add custom fields representing different bidder statuses
            // Auction 1 (Live, user is winning)
            $mockAuctions[0]['bidder_status'] = 'winning';
            $mockAuctions[0]['user_max_bid'] = 585000;
            
            // Auction 2 (Live, user is outbid)
            $mockAuctions[1]['bidder_status'] = 'outbid';
            $mockAuctions[1]['user_max_bid'] = 495000; // current is 510000
            
            // Auction 3 (Live, user is winning)
            $mockAuctions[2]['bidder_status'] = 'winning';
            $mockAuctions[2]['user_max_bid'] = 435000;
            
            // Auction 5 (Ended, user won)
            $mockAuctions[4]['bidder_status'] = 'won';
            $mockAuctions[4]['user_max_bid'] = 345000;
            
            // Create an Auction 6 to represent 'Lost'
            $mockAuctions[] = [
                'id' => 6,
                'title_ar' => 'لكزس LX600 Signature 2023 - فل كامل',
                'title_en' => 'Lexus LX600 Signature 2023 - Full Option',
                'make' => 'Lexus',
                'model' => 'LX600',
                'year' => 2023,
                'color' => 'أسود / Black',
                'mileage' => 15000,
                'location' => 'الدمام',
                'start_price' => 450000,
                'current_price' => 520000,
                'min_bid_increment' => 5000,
                'deposit_amount' => 10000,
                'bids_count' => 14,
                'start_time' => now()->subDays(5),
                'end_time' => now()->subDays(1),
                'status' => 'ended',
                'transmission' => 'automatic',
                'fuel_type' => 'petrol',
                'engine_capacity' => '3.5L V6 Twin-Turbo',
                'condition' => 'excellent',
                'image' => 'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=800&fit=crop',
                'description_ar' => 'لكزس LX600 فئة سيجنتشر المميزة أعلى مواصفات مع شاشات خلفية، حالة الوكالة تماماً.',
                'description_en' => 'Lexus LX600 Signature grade, top specs with rear screens, showroom condition.',
                'bidder_status' => 'lost',
                'user_max_bid' => 490000 // current/winning is 520000
            ];

            // Only show auctions we bid on (all except upcoming id 4)
            $filteredMock = array_filter($mockAuctions, function($auc) {
                return isset($auc['bidder_status']);
            });

            // Calculate overall counts for widgets on mock
            $totalCount = count($filteredMock);
            $winningCount = count(array_filter($filteredMock, function($x){ return $x['bidder_status'] === 'winning'; }));
            $outbidCount = count(array_filter($filteredMock, function($x){ return $x['bidder_status'] === 'outbid'; }));
            $wonCount = count(array_filter($filteredMock, function($x){ return $x['bidder_status'] === 'won'; }));
            $lostCount = count(array_filter($filteredMock, function($x){ return $x['bidder_status'] === 'lost'; }));

            // Apply filter status on mock
            if ($status !== 'all') {
                $filteredMock = array_filter($filteredMock, function($auc) use ($status) {
                    return $auc['bidder_status'] === $status;
                });
            }

            $auctions = collect(array_values($filteredMock));
            $usingMock = true;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('bidder.auctions.partials.my-bids-list', compact('auctions', 'usingMock'))->render()
            ]);
        }

        return view('bidder.auctions.my-bids', compact('auctions', 'usingMock', 'status', 'totalCount', 'winningCount', 'outbidCount', 'wonCount', 'lostCount'));
    }

    /**
     * Display a list of auctions won by the bidder.
     */
    public function wonAuctions(Request $request)
    {
        $user = auth()->user();
        if ($user->status !== 'approved') {
            return redirect()->route('kyc.index')
                ->with('error', __('Please complete identity verification to view your won auctions.'));
        }

        $paymentStatusFilter = $request->input('payment_status'); // pending, paid

        // Check if user has real won auctions
        $realWonCount = Auction::where('winner_id', $user->id)->count();
        $bankAccounts = \App\Models\BankAccount::where('is_active', true)->get();

        // If no bank accounts in DB, create a mock collection for premium display
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

        if ($realWonCount > 0) {
            $query = Auction::with(['vehicle', 'vehicle.images', 'highestBid', 'order'])
                ->where('winner_id', $user->id);

            // Apply payment status filter
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

            $auctions = $query->latest()->paginate(10)->withQueryString();
            $usingMock = false;
        } else {
            // Fallback mock won auctions
            $mockAuctions = $this->getMockAuctions();
            
            $wonMock = [];
            // Mock 1: Pending payment
            $audi = $mockAuctions[4];
            $audi['id'] = 5;
            $audi['bidder_status'] = 'won';
            $audi['user_max_bid'] = 345000;
            $audi['winning_bid_amount'] = 345000;
            $audi['payment_status'] = 'pending';
            $wonMock[] = $audi;

            // Mock 2: Paid/Completed
            $porsche = $mockAuctions[1];
            $porsche['id'] = 2;
            $porsche['title_ar'] = 'بورش 911 كاريرا GTS 2023 - بحالة الوكالة';
            $porsche['title_en'] = 'Porsche 911 Carrera GTS 2023 - Like New';
            $porsche['bidder_status'] = 'won';
            $porsche['user_max_bid'] = 450000;
            $porsche['winning_bid_amount'] = 450000;
            $porsche['payment_status'] = 'paid';
            $wonMock[] = $porsche;

            // Filter mocks
            if (!empty($paymentStatusFilter)) {
                $wonMock = array_filter($wonMock, function($auc) use ($paymentStatusFilter) {
                    return $auc['payment_status'] === $paymentStatusFilter;
                });
            }

            $auctions = collect(array_values($wonMock));
            $usingMock = true;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('bidder.auctions.partials.won-auctions-list', compact('auctions', 'usingMock', 'bankAccounts', 'paymentStatusFilter'))->render()
            ]);
        }

        return view('bidder.auctions.won', compact('auctions', 'usingMock', 'bankAccounts', 'paymentStatusFilter'));
    }

    /**
     * Helper to return premium mockup auctions.
     */
    private function getMockAuctions(): array
    {
        return [
            [
                'id' => 1,
                'title_ar' => 'مرسيدس جي كلاس G63 AMG 2024 - مواصفات خليجية',
                'title_en' => 'Mercedes G63 AMG 2024 - GCC Specs',
                'make' => 'Mercedes-Benz',
                'model' => 'G63 AMG',
                'year' => 2024,
                'color' => 'الرمادي المطفي / Matt Grey',
                'mileage' => 2400,
                'location' => 'الرياض',
                'start_price' => 520000,
                'current_price' => 585000,
                'min_bid_increment' => 5000,
                'deposit_amount' => 10000,
                'bids_count' => 19,
                'start_time' => now()->subHours(4),
                'end_time' => now()->addHours(3)->addMinutes(45),
                'status' => 'live',
                'transmission' => 'automatic',
                'fuel_type' => 'petrol',
                'engine_capacity' => '4.0L V8 BiTurbo',
                'condition' => 'excellent',
                'image' => 'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?w=800&fit=crop',
                'description_ar' => 'مرسيدس جي كلاس إصدار خاص تحت الضمان مع كامل المواصفات الفاخرة، مقاعد الكانتارا الجلدية، نظام المساعدة على القيادة، كاميرات 360 درجة.',
                'description_en' => 'Special Edition G-Class under warranty with complete premium options, Alcantara leather seats, drive assistance system, 360 cameras.',
                'damage_points' => [
                    [
                        'part' => 'rear_bumper',
                        'type' => 'scratch',
                        'note' => 'خدش بسيط جداً / Very minor scratch',
                        'label_ar' => 'صدام خلفي',
                        'label_en' => 'Rear Bumper'
                    ]
                ],
            ],
            [
                'id' => 2,
                'title_ar' => 'بورش 911 كاريرا GTS 2024 - مثل الجديدة',
                'title_en' => 'Porsche 911 Carrera GTS 2024 - Mint Condition',
                'make' => 'Porsche',
                'model' => '911 Carrera GTS',
                'year' => 2024,
                'color' => 'الأحمر القرمزي / Carmine Red',
                'mileage' => 850,
                'location' => 'جدة',
                'start_price' => 480000,
                'current_price' => 510000,
                'min_bid_increment' => 5000,
                'deposit_amount' => 15000,
                'bids_count' => 12,
                'start_time' => now()->subHours(2),
                'end_time' => now()->addHours(8),
                'status' => 'live',
                'transmission' => 'automatic',
                'fuel_type' => 'petrol',
                'engine_capacity' => '3.0L Flat-6',
                'condition' => 'new',
                'image' => 'https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?w=800&fit=crop',
                'description_ar' => 'بورش 911 كاريرا جي تي إس الرياضية الرائعة خالية من الرش والتعديل، طبقة حماية كاملة هيكل خارجي وداخلي.',
                'description_en' => 'Stunning sports Porsche 911 Carrera GTS, original paint, full body PPF protection inside and out.',
                'damage_points' => [],
            ],
            [
                'id' => 3,
                'title_ar' => 'بي ام دبليو M8 كومبيتيشن 2023 - حزمة الكاربون فايبر',
                'title_en' => 'BMW M8 Competition 2023 - Carbon Package',
                'make' => 'BMW',
                'model' => 'M8 Competition',
                'year' => 2023,
                'color' => 'الأسود الملكي / Sapphire Black',
                'mileage' => 12000,
                'location' => 'الدمام',
                'start_price' => 390000,
                'current_price' => 435000,
                'min_bid_increment' => 2500,
                'deposit_amount' => 8000,
                'bids_count' => 8,
                'start_time' => now()->subHours(12),
                'end_time' => now()->addMinutes(42),
                'status' => 'live',
                'transmission' => 'automatic',
                'fuel_type' => 'petrol',
                'engine_capacity' => '4.4L V8 TwinPower',
                'condition' => 'excellent',
                'image' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&fit=crop',
                'description_ar' => 'بي إم دبليو M8 كومبيتيشن مجهزة بحزمة كاربون فايبر كاملة وصوت رياضي معدل بالوكالة. السيارة بحالة ممتازة وخالية من الصدمات.',
                'description_en' => 'BMW M8 Competition featuring full factory carbon package and M Performance exhaust. Excellent shape, accident free.',
                'damage_points' => [
                    [
                        'part' => 'left_door_front',
                        'type' => 'scratch',
                        'note' => 'خدش سطحي بسيط / Superficial scratch',
                        'label_ar' => 'باب أمامي أيسر',
                        'label_en' => 'Left Front Door'
                    ]
                ],
            ],
            [
                'id' => 4,
                'title_ar' => 'لاند روفر رينج روفر سبورت 2024 - أوتوبيوجرافي',
                'title_en' => 'Range Rover Sport 2024 - Autobiography',
                'make' => 'Land Rover',
                'model' => 'Range Rover Sport',
                'year' => 2024,
                'color' => 'الأبيض اللؤلؤي / Pearl White',
                'mileage' => 1500,
                'location' => 'الرياض',
                'start_price' => 440000,
                'current_price' => 440000,
                'min_bid_increment' => 5000,
                'deposit_amount' => 12000,
                'bids_count' => 0,
                'start_time' => now()->addHours(24),
                'end_time' => now()->addHours(48),
                'status' => 'upcoming',
                'transmission' => 'automatic',
                'fuel_type' => 'hybrid',
                'engine_capacity' => '3.0L L6 Hybrid',
                'condition' => 'new',
                'image' => 'https://images.unsplash.com/photo-1606220838315-056192d5e927?w=800&fit=crop',
                'description_ar' => 'رينج روفر سبورت إصدار أوتوبيوجرافي الجديد كلياً، قمة الفخامة والتكنولوجيا الهجينة المتطورة.',
                'description_en' => 'All-new Range Rover Sport Autobiography edition, the peak of luxury and advanced hybrid technology.',
                'damage_points' => [],
            ],
            [
                'id' => 5,
                'title_ar' => 'أودي RS7 كواترو 2023 - لون مميز',
                'title_en' => 'Audi RS7 Quattro 2023 - Exclusive Color',
                'make' => 'Audi',
                'model' => 'RS7',
                'year' => 2023,
                'color' => 'الأخضر ميتاليك / Sonoma Green',
                'mileage' => 19500,
                'location' => 'جدة',
                'start_price' => 310000,
                'current_price' => 345000,
                'min_bid_increment' => 2500,
                'deposit_amount' => 6000,
                'bids_count' => 21,
                'start_time' => now()->subDays(3),
                'end_time' => now()->subHours(2),
                'status' => 'ended',
                'transmission' => 'automatic',
                'fuel_type' => 'petrol',
                'engine_capacity' => '4.0L V8 TFSI',
                'condition' => 'good',
                'image' => 'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?w=800&fit=crop',
                'description_ar' => 'أودي RS7 الأداء الخارق متناسق وجميل جداً، صيانة دورية بالوكالة مع تمديد للضمان.',
                'description_en' => 'High-performance Audi RS7 in stunning Sonoma Green, full service history at agency, extended warranty.',
                'damage_points' => [
                    [
                        'part' => 'front_bumper',
                        'type' => 'dent',
                        'note' => 'صدمة خفيفة بدون تأثير على الرديتر / Minor dent, no radiator impact',
                        'label_ar' => 'صدام أمامي',
                        'label_en' => 'Front Bumper'
                    ],
                    [
                        'part' => 'right_fender_front',
                        'type' => 'repainted',
                        'note' => 'رش تجميلي بدون معجون / Cosmetic repaint only',
                        'label_ar' => 'رفرف أمامي أيمن',
                        'label_en' => 'Right Front Fender'
                    ]
                ],
            ]
        ];
    }

    /**
     * Perform a global autocomplete search for the bidder header.
     */
    public function globalSearch(Request $request)
    {
        $term = $request->input('q');
        if (empty($term) || strlen($term) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];
        $locale = app()->getLocale();

        // 1. Live and Upcoming Auctions
        $dbCount = Auction::count();
        if ($dbCount > 0) {
            $auctions = Auction::with(['vehicle', 'vehicle.images'])
                ->where('status', '!=', 'ended')
                ->where('status', '!=', 'sold')
                ->where(function($q) use ($term) {
                    $q->where('title_ar', 'like', "%{$term}%")
                      ->orWhere('title_en', 'like', "%{$term}%")
                      ->orWhereHas('vehicle', function($vq) use ($term) {
                          $vq->where('make_ar', 'like', "%{$term}%")
                            ->orWhere('make_en', 'like', "%{$term}%")
                            ->orWhere('model_ar', 'like', "%{$term}%")
                            ->orWhere('model_en', 'like', "%{$term}%")
                            ->orWhere('year', 'like', "%{$term}%");
                      });
                })
                ->take(5)
                ->get();

            if ($auctions->isNotEmpty()) {
                $results[] = [
                    'category' => $locale === 'ar' ? 'المزادات المتاحة' : 'Available Auctions',
                    'items' => $auctions->map(function($auc) {
                        return [
                            'title' => $auc->title,
                            'subtitle' => number_format($auc->current_price) . ' SAR - ' . __($auc->status),
                            'url' => route('bidder.auctions.show', $auc->id),
                            'image' => $auc->primary_image_url,
                            'icon' => 'fa-gavel'
                        ];
                    })
                ];
            }
        } else {
            // Search in mock auctions for high fidelity demo
            $mockAuctions = $this->getMockAuctions();
            $filtered = array_filter($mockAuctions, function($auc) use ($term) {
                return stripos($auc['title_ar'], $term) !== false ||
                       stripos($auc['title_en'], $term) !== false ||
                       stripos($auc['make'], $term) !== false ||
                       stripos($auc['model'], $term) !== false;
            });

            if (!empty($filtered)) {
                $results[] = [
                    'category' => $locale === 'ar' ? 'المزادات المتاحة (تجريبي)' : 'Available Auctions (Demo)',
                    'items' => array_map(function($auc) {
                        return [
                            'title' => app()->getLocale() === 'ar' ? $auc['title_ar'] : $auc['title_en'],
                            'subtitle' => number_format($auc['current_price']) . ' SAR - ' . __($auc['status']),
                            'url' => route('bidder.auctions.show', $auc['id']),
                            'image' => $auc['image'],
                            'icon' => 'fa-gavel'
                        ];
                    }, array_slice($filtered, 0, 5))
                ];
            }
        }

        // 2. Static Pages (e.g. Terms of Use, Privacy Policy, Info)
        $pages = \App\Models\Page::where('is_active', true)
            ->where(function($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('content', 'like', "%{$term}%");
            })
            ->take(3)
            ->get();

        if ($pages->isNotEmpty()) {
            $results[] = [
                'category' => $locale === 'ar' ? 'الصفحات التعريفية' : 'Information Pages',
                'items' => $pages->map(function($page) {
                    return [
                        'title' => $page->title,
                        'subtitle' => __('صفحة تعريفية للموقع'),
                        'url' => route('page.show', $page->slug),
                        'image' => null,
                        'icon' => 'fa-file-alt'
                    ];
                })
            ];
        }

        // 3. FAQs
        $faqs = \App\Models\Faq::where('is_active', true)
            ->where(function($q) use ($term) {
                $q->where('question_ar', 'like', "%{$term}%")
                  ->orWhere('question_en', 'like', "%{$term}%")
                  ->orWhere('answer_ar', 'like', "%{$term}%")
                  ->orWhere('answer_en', 'like', "%{$term}%");
            })
            ->take(3)
            ->get();

        if ($faqs->isNotEmpty()) {
            $results[] = [
                'category' => $locale === 'ar' ? 'الأسئلة الشائعة' : 'FAQs',
                'items' => $faqs->map(function($faq) {
                    return [
                        'title' => $locale === 'ar' ? $faq->question_ar : $faq->question_en,
                        'subtitle' => __('مركز الدعم والمساعدة'),
                        'url' => route('resources') . '#faq-' . $faq->id,
                        'image' => null,
                        'icon' => 'fa-question-circle'
                    ];
                })
            ];
        }

        return response()->json(['results' => $results]);
    }
}
