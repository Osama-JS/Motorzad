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
            new OA\Response(response: 200, description: "Successful response")
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
            new OA\Response(response: 200, description: "Successful response")
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
            new OA\Response(response: 200, description: "Successful response")
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
            new OA\Response(response: 200, description: "Successful response")
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
    #[OA\Response(response: 200, description: "Successful response")]
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
