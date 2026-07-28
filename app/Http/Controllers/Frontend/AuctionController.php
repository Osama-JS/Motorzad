<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    /**
     * Display a list of auctions with filtering and search for public.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $tab = $request->input('tab', 'live'); // live, upcoming, ended

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
            }

            $auctions = collect(array_values($mockAuctions));
            $usingMock = true;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('frontend.auctions.partials.grid', compact('auctions', 'tab', 'search', 'usingMock'))->render()
            ]);
        }

        // Compute counts for hero stats
        if ($dbCount > 0) {
            $totalLive = Auction::where('status', 'live')->where('start_time', '<=', now())->where('end_time', '>=', now())->count();
            $totalUpcoming = Auction::where(function($q) { $q->where('status', 'scheduled')->orWhere('start_time', '>', now()); })->count();
            $totalEnded = Auction::where(function($q) { $q->where('status', 'ended')->orWhere('status', 'sold')->orWhere('end_time', '<', now()); })->count();
            $totalBids = \App\Models\Bid::count();
        } else {
            $allMock = $this->getMockAuctions();
            $totalLive = count(array_filter($allMock, fn($a) => $a['status'] === 'live'));
            $totalUpcoming = count(array_filter($allMock, fn($a) => $a['status'] === 'upcoming'));
            $totalEnded = count(array_filter($allMock, fn($a) => $a['status'] === 'ended'));
            $totalBids = array_sum(array_column($allMock, 'bids_count'));
        }

        return view('frontend.auctions.index', compact('auctions', 'tab', 'search', 'usingMock', 'makes', 'locations', 'totalLive', 'totalUpcoming', 'totalEnded', 'totalBids'));
    }

    /**
     * Display details of a single auction for public.
     */
    public function show($id)
    {
        // Try to load from database first
        $auction = Auction::with(['vehicle', 'vehicle.images', 'highestBid', 'bids.user'])->find($id);
        $user = auth()->user();

        if ($auction) {
            $isWatched = $user ? $auction->watchlist()->where('user_id', $user->id)->exists() : false;
            return view('frontend.auctions.show', compact('auction', 'isWatched', 'user'));
        }

        // Fallback to mock single view
        $mockAuctions = $this->getMockAuctions();
        $mockIndex = array_search($id, array_column($mockAuctions, 'id'));

        if ($mockIndex !== false) {
            $auctionData = $mockAuctions[$mockIndex];
            $isWatched = ($id == 1 || $id == 3); // Demo status
            return view('frontend.auctions.show-mock', compact('auctionData', 'isWatched', 'user'));
        }

        abort(404, 'Auction not found');
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
            ]
        ];
    }
}
