<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
}
