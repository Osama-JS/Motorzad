<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuctionResource;
use App\Models\Auction;
use App\Models\Faq;
use App\Models\Setting;
use App\Models\VehicleMake;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class GeneralController extends Controller
{
    /**
     * Get platform settings.
     */
    #[OA\Get(
        path: "/api/general/settings",
        summary: "Get General Settings",
        description: "Returns platform settings like terms, about us, etc.",
        tags: ["General"],
        responses: [
            new OA\Response(
                response: 200, 
                description: "Successful response",
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            'site_name_ar' => 'موترزاد',
                            'site_name_en' => 'Motorzad',
                            'support_email' => 'support@motorzad.com',
                            'support_phone' => '+966500000000',
                            'terms_ar' => 'شروط وأحكام المنصة...',
                            'terms_en' => 'Platform terms and conditions...',
                            'about_ar' => 'من نحن...',
                            'about_en' => 'About us...'
                        ]
                    ]
                )
            )
        ]
    )]
    public function settings(): JsonResponse
    {
        $settings = Setting::all()->pluck('value', 'key');
        
        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Get FAQs.
     */
    #[OA\Get(
        path: "/api/general/faqs",
        summary: "Get FAQs",
        description: "Returns a list of active Frequently Asked Questions.",
        tags: ["General"],
        responses: [
            new OA\Response(
                response: 200, 
                description: "Successful response",
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            [
                                'id' => 1,
                                'question_ar' => 'كيف يمكنني التسجيل في المزاد؟',
                                'question_en' => 'How can I register for the auction?',
                                'answer_ar' => 'يمكنك التسجيل عبر الضغط على زر إنشاء حساب وتعبئة بياناتك...',
                                'answer_en' => 'You can register by clicking the sign-up button and filling in your details...',
                                'is_active' => 1,
                                'created_at' => '2023-10-01T10:00:00.000000Z',
                                'updated_at' => '2023-10-01T10:00:00.000000Z'
                            ],
                            [
                                'id' => 2,
                                'question_ar' => 'ما هي طرق الدفع المتاحة؟',
                                'question_en' => 'What payment methods are available?',
                                'answer_ar' => 'نحن نقبل التحويل البنكي والبطاقات الائتمانية...',
                                'answer_en' => 'We accept bank transfers and credit cards...',
                                'is_active' => 1,
                                'created_at' => '2023-10-02T12:00:00.000000Z',
                                'updated_at' => '2023-10-02T12:00:00.000000Z'
                            ]
                        ]
                    ]
                )
            )
        ]
    )]
    public function faqs(): JsonResponse
    {
        $faqs = Faq::where('is_active', true)->latest()->get();
        
        return response()->json([
            'success' => true,
            'data' => $faqs
        ]);
    }

    /**
     * Get Vehicle Makes and Models for Dropdowns.
     */
    #[OA\Get(
        path: "/api/general/vehicle-options",
        summary: "Get Vehicle Dropdown Options",
        description: "Returns a list of active Vehicle Makes, Models, and standard Colors for dropdowns.",
        tags: ["General"],
        responses: [
            new OA\Response(
                response: 200, 
                description: "Successful response",
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            'makes' => [
                                [
                                    'id' => 1,
                                    'name_ar' => 'تويوتا',
                                    'name_en' => 'Toyota',
                                    'logo_url' => 'https://example.com/storage/makes/toyota.png',
                                    'models' => [
                                        [
                                            'id' => 10,
                                            'name_ar' => 'كامري',
                                            'name_en' => 'Camry'
                                        ],
                                        [
                                            'id' => 11,
                                            'name_ar' => 'كورولا',
                                            'name_en' => 'Corolla'
                                        ]
                                    ]
                                ]
                            ],
                            'colors' => [
                                [
                                    'ar' => 'أبيض',
                                    'en' => 'White'
                                ],
                                [
                                    'ar' => 'أسود',
                                    'en' => 'Black'
                                ]
                            ]
                        ]
                    ]
                )
            )
        ]
    )]
    public function vehicleOptions(): JsonResponse
    {
        $makes = VehicleMake::where('is_active', true)
            ->with(['models' => function($q) {
                $q->where('is_active', true);
            }])
            ->get();
            
        // We can also return common colors or hardcoded lists if needed.
        $colors = [
            ['ar' => 'أبيض', 'en' => 'White'],
            ['ar' => 'أسود', 'en' => 'Black'],
            ['ar' => 'فضي', 'en' => 'Silver'],
            ['ar' => 'رمادي', 'en' => 'Gray'],
            ['ar' => 'أحمر', 'en' => 'Red'],
            ['ar' => 'أزرق', 'en' => 'Blue'],
            ['ar' => 'بني', 'en' => 'Brown'],
            ['ar' => 'أخضر', 'en' => 'Green'],
            ['ar' => 'أصفر', 'en' => 'Yellow'],
            ['ar' => 'برتقالي', 'en' => 'Orange'],
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'makes' => $makes,
                'colors' => $colors,
            ]
        ]);
    }

    /**
     * Get Featured Auctions.
     */
    #[OA\Get(
        path: "/api/general/featured-auctions",
        summary: "Get Featured Auctions",
        description: "Returns a list of featured live auctions for the mobile app home screen.",
        tags: ["General"],
        responses: [
            new OA\Response(
                response: 200, 
                description: "Successful response",
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
                        ]
                    ]
                )
            )
        ]
    )]
    public function featuredAuctions(): JsonResponse
    {
        $auctions = Auction::with(['vehicle.images', 'highestBid'])
            ->withCount('bids')
            ->live()
            ->featured()
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => AuctionResource::collection($auctions)
        ]);
    }

    /**
     * Global Quick Search.
     */
    #[OA\Get(path: "/api/general/search", summary: "Global Quick Search", description: "Returns categorized search results for auctions, pages, and FAQs.", tags: ["General"])]
    #[OA\Parameter(name: "q", in: "query", required: true, description: "Search term")]
    #[OA\Response(
        response: 200, 
        description: "Successful response",
        content: new OA\JsonContent(
            example: [
                'success' => true,
                'data' => [
                    [
                        'category' => 'المزادات المتاحة',
                        'type' => 'auctions',
                        'items' => [
                            [
                                'id' => 1,
                                'title' => 'تويوتا كامري 2022',
                                'title_ar' => 'تويوتا كامري 2022',
                                'title_en' => 'Toyota Camry 2022',
                                'description' => 'سيارة بحالة ممتازة...',
                                'location' => 'الرياض',
                                'start_price' => 50000,
                                'current_price' => 53000,
                                'status' => 'live',
                                'vehicle' => [
                                    'id' => 10,
                                    'title' => 'Toyota Camry',
                                    'primary_image_url' => 'https://example.com/storage/auctions/camry-main.jpg'
                                ],
                                'created_at' => '2023-10-25T14:30:00.000000Z'
                            ]
                        ]
                    ],
                    [
                        'category' => 'الصفحات التعريفية',
                        'type' => 'pages',
                        'items' => [
                            [
                                'id' => 1,
                                'title' => 'الشروط والأحكام',
                                'slug' => 'terms-and-conditions'
                            ]
                        ]
                    ],
                    [
                        'category' => 'الأسئلة الشائعة',
                        'type' => 'faqs',
                        'items' => [
                            [
                                'id' => 1,
                                'question' => 'كيف يمكنني المشاركة في المزاد؟',
                                'answer' => 'يمكنك المشاركة من خلال التسجيل ودفع العربون المطلوب.'
                            ]
                        ]
                    ]
                ]
            ]
        )
    )]
    public function search(Request $request): JsonResponse
    {
        $term = $request->input('q');
        if (empty($term) || strlen($term) < 2) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $results = [];
        $locale = app()->getLocale();

        // 1. Live and Upcoming Auctions
        $auctions = Auction::with(['vehicle.images', 'highestBid'])
            ->whereNotIn('status', ['ended', 'sold', 'draft'])
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
                'type' => 'auctions',
                'items' => AuctionResource::collection($auctions)
            ];
        }

        // 2. Static Pages
        $pages = \App\Models\Page::where('is_active', true)
            ->where(function($q) use ($term) {
                $q->where('title_ar', 'like', "%{$term}%")
                  ->orWhere('title_en', 'like', "%{$term}%")
                  ->orWhere('content_ar', 'like', "%{$term}%")
                  ->orWhere('content_en', 'like', "%{$term}%");
            })
            ->take(3)
            ->get();

        if ($pages->isNotEmpty()) {
            $results[] = [
                'category' => $locale === 'ar' ? 'الصفحات التعريفية' : 'Information Pages',
                'type' => 'pages',
                'items' => $pages->map(function($page) {
                    return [
                        'id' => $page->id,
                        'title' => $page->title,
                        'slug' => $page->slug
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
                'type' => 'faqs',
                'items' => $faqs->map(function($faq) {
                    return [
                        'id' => $faq->id,
                        'question' => $faq->{'question_' . app()->getLocale()} ?? $faq->question_en,
                        'answer' => $faq->{'answer_' . app()->getLocale()} ?? $faq->answer_en
                    ];
                })
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }
}
