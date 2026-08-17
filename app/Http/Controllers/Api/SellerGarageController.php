<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuctionResource;
use App\Http\Resources\VehicleResource;
use App\Models\Auction;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class SellerGarageController extends Controller
{
    use ApiResponse;

    /**
     * Get seller's submitted vehicles (listings)
     */
    #[OA\Get(
        path: '/api/seller/garage/listings',
        summary: 'Get Seller Listings (Vehicles)',
        description: 'Returns the seller\'s vehicles that are pending, approved, or rejected (excluding drafts). Includes statistics.',
        security: [['bearerAuth' => []]],
        tags: ['Seller Garage'],
        responses: [
            new OA\Response(response: 200, description: 'Successful Response'),
            new OA\Response(response: 403, description: 'Unauthorized')
        ]
    )]
    public function listings(Request $request): JsonResponse
    {
        $user = $request->user();

        $vehicles = Vehicle::with('images')
            ->where('submitted_by', $user->id)
            ->whereIn('status', ['pending', 'approved', 'rejected'])
            ->latest()
            ->paginate(15);
            
        $stats = [
            'total' => Vehicle::where('submitted_by', $user->id)->whereIn('status', ['pending', 'approved', 'rejected'])->count(),
            'pending' => Vehicle::where('submitted_by', $user->id)->where('status', 'pending')->count(),
            'approved' => Vehicle::where('submitted_by', $user->id)->where('status', 'approved')->count(),
            'rejected' => Vehicle::where('submitted_by', $user->id)->where('status', 'rejected')->count(),
            'draft' => Vehicle::where('submitted_by', $user->id)->where('status', 'draft')->count(),
        ];

        return $this->successResponse([
            'vehicles' => VehicleResource::collection($vehicles->items()),
            'stats' => $stats,
            'meta' => [
                'current_page' => $vehicles->currentPage(),
                'last_page'    => $vehicles->lastPage(),
                'total'        => $vehicles->total(),
                'per_page'     => $vehicles->perPage(),
            ]
        ]);
    }

    /**
     * Get seller's draft vehicles
     */
    #[OA\Get(
        path: '/api/seller/garage/drafts',
        summary: 'Get Seller Drafts',
        description: 'Returns the seller\'s vehicles that are saved as drafts.',
        security: [['bearerAuth' => []]],
        tags: ['Seller Garage'],
        responses: [
            new OA\Response(response: 200, description: 'Successful Response'),
            new OA\Response(response: 403, description: 'Unauthorized')
        ]
    )]
    public function drafts(Request $request): JsonResponse
    {
        $user = $request->user();

        $vehicles = Vehicle::with('images')
            ->where('submitted_by', $user->id)
            ->where('status', 'draft')
            ->latest()
            ->paginate(15);

        return $this->successResponse([
            'vehicles' => VehicleResource::collection($vehicles->items()),
            'meta' => [
                'current_page' => $vehicles->currentPage(),
                'last_page'    => $vehicles->lastPage(),
                'total'        => $vehicles->total(),
                'per_page'     => $vehicles->perPage(),
            ]
        ]);
    }

    /**
     * Store a newly created vehicle (or draft).
     */
    #[OA\Post(
        path: '/api/seller/garage/vehicles',
        summary: 'Create a new vehicle (or draft)',
        description: 'Creates a vehicle. Use action=draft to save as draft, action=submit to submit for approval.',
        security: [['bearerAuth' => []]],
        tags: ['Seller Garage'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'make_ar', type: 'string'),
                        new OA\Property(property: 'make_en', type: 'string'),
                        new OA\Property(property: 'model_ar', type: 'string'),
                        new OA\Property(property: 'model_en', type: 'string'),
                        new OA\Property(property: 'year', type: 'integer'),
                        new OA\Property(property: 'action', type: 'string', description: 'draft or submit'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Successful Response'),
            new OA\Response(response: 422, description: 'Validation Error')
        ]
    )]
    public function storeVehicle(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'make_ar' => 'required|string|max:100',
            'make_en' => 'required|string|max:100',
            'model_ar' => 'required|string|max:100',
            'model_en' => 'required|string|max:100',
            'year' => 'required|integer|min:1901|max:' . (date('Y') + 1),
            'color_ar' => 'nullable|string|max:50',
            'color_en' => 'nullable|string|max:50',
            'vin_number' => 'nullable|string|max:50',
            'mileage' => 'nullable|integer|min:0',
            'fuel_type' => 'nullable|string|max:50',
            'transmission' => 'nullable|string|max:50',
            'engine_capacity' => 'nullable|string|max:50',
            'cylinders' => 'nullable|integer|min:1',
            'condition' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'damage_points' => 'nullable|string',
            'action' => 'required|in:draft,submit',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'primary_image_index' => 'nullable|integer'
        ]);

        $status = $request->action === 'submit' ? 'approved' : 'draft';
        $validated['status'] = $status;
        $validated['submitted_by'] = $user->id;

        if (!empty($validated['damage_points'])) {
            $validated['damage_points'] = json_decode($validated['damage_points'], true);
        } else {
            $validated['damage_points'] = null;
        }

        if (!empty($validated['vehicle_id'])) {
            $vehicle = Vehicle::where('id', $validated['vehicle_id'])->where('submitted_by', $user->id)->firstOrFail();
            $vehicle->update($validated);
        } else {
            $vehicle = Vehicle::create($validated);
        }

        $primaryIndex = (int) $request->input('primary_image_index', 0);
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('vehicles', 'public');
                VehicleImage::create([
                    'vehicle_id' => $vehicle->id,
                    'image_path' => $path,
                    'is_primary' => ($index === $primaryIndex),
                    'sort_order' => $index
                ]);
            }
        }

        return $this->successResponse(
            new VehicleResource($vehicle->load('images')),
            __('Vehicle saved successfully.'),
            201
        );
    }

    /**
     * Update an existing vehicle
     */
    #[OA\Post(
        path: '/api/seller/garage/vehicles/{id}',
        summary: 'Update an existing vehicle',
        description: 'Updates a vehicle. Use POST with _method=PUT to support multipart/form-data for image uploads.',
        security: [['bearerAuth' => []]],
        tags: ['Seller Garage'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful Response'),
            new OA\Response(response: 422, description: 'Validation Error')
        ]
    )]
    public function updateVehicle(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $vehicle = Vehicle::where('id', $id)->where('submitted_by', $user->id)->firstOrFail();

        $validated = $request->validate([
            'make_ar' => 'required|string|max:100',
            'make_en' => 'required|string|max:100',
            'model_ar' => 'required|string|max:100',
            'model_en' => 'required|string|max:100',
            'year' => 'required|integer|min:1901|max:' . (date('Y') + 1),
            'color_ar' => 'nullable|string|max:50',
            'color_en' => 'nullable|string|max:50',
            'vin_number' => 'nullable|string|max:50',
            'mileage' => 'nullable|integer|min:0',
            'fuel_type' => 'nullable|string|max:50',
            'transmission' => 'nullable|string|max:50',
            'engine_capacity' => 'nullable|string|max:50',
            'cylinders' => 'nullable|integer|min:1',
            'condition' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'damage_points' => 'nullable|string',
            'existing_images' => 'nullable|string', // JSON array of objects with serverId and order
            'action' => 'required|in:draft,submit',
        ]);

        $status = $request->input('action') === 'draft' ? 'draft' : 'approved';
        $validated['status'] = $status;

        if (!empty($validated['damage_points'])) {
            $validated['damage_points'] = json_decode($validated['damage_points'], true);
        } else {
            $validated['damage_points'] = null;
        }

        $vehicle->update($validated);

        // Handle existing images
        $keptExistingImages = [];
        if (!empty($validated['existing_images'])) {
            $existingImagesOrder = json_decode($validated['existing_images'], true) ?? [];
            foreach ($existingImagesOrder as $img) {
                $keptExistingImages[] = $img['serverId'];
            }
            
            // Delete images that are not kept
            $imagesToDelete = VehicleImage::where('vehicle_id', $vehicle->id)
                ->whereNotIn('id', $keptExistingImages)
                ->get();
            
            foreach($imagesToDelete as $delImg) {
                Storage::disk('public')->delete($delImg->image_path);
                $delImg->delete();
            }

            // Update sort order for kept existing images
            foreach ($existingImagesOrder as $img) {
                VehicleImage::where('id', $img['serverId'])->update(['sort_order' => $img['order'], 'is_primary' => false]);
            }
        } else {
            // Delete all existing images if none kept
            $allImages = VehicleImage::where('vehicle_id', $vehicle->id)->get();
            foreach($allImages as $delImg) {
                Storage::disk('public')->delete($delImg->image_path);
                $delImg->delete();
            }
        }

        // Add new images
        $primaryIndex = (int) $request->input('primary_image_index', 0);
        if ($request->hasFile('images')) {
            $existingCount = count($keptExistingImages);
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('vehicles', 'public');
                VehicleImage::create([
                    'vehicle_id' => $vehicle->id,
                    'image_path' => $path,
                    'is_primary' => (($index + $existingCount) === $primaryIndex),
                    'sort_order' => $index + $existingCount
                ]);
            }
        } else {
            // Ensure at least one image is primary if exists
            $firstImg = VehicleImage::where('vehicle_id', $vehicle->id)->orderBy('sort_order')->first();
            if ($firstImg) {
                $firstImg->update(['is_primary' => true]);
            }
        }

        return $this->successResponse(
            new VehicleResource($vehicle->load('images')),
            __('Vehicle updated successfully.')
        );
    }

    /**
     * Store the auction proposed by the seller.
     */
    #[OA\Post(
        path: '/api/seller/garage/auctions',
        summary: 'Create an auction for a vehicle',
        description: 'Creates an auction for a specific approved vehicle.',
        security: [['bearerAuth' => []]],
        tags: ['Seller Garage'],
        responses: [
            new OA\Response(response: 200, description: 'Successful Response'),
            new OA\Response(response: 422, description: 'Validation Error')
        ]
    )]
    public function storeAuction(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_price' => 'required|numeric|min:0',
            'reserve_price' => 'nullable|numeric|gte:start_price',
            'buy_now_price' => 'nullable|numeric|gte:reserve_price',
            'min_bid_increment' => 'required|numeric|min:1',
            'location_ar' => 'required|string|max:255',
            'location_en' => 'required|string|max:255',
            'start_time' => 'required|date|after_or_equal:today',
            'end_time' => 'required|date|after:start_time',
            'bidding_mode' => 'required|in:open,strict',
            'auto_extend_minutes' => 'nullable|integer|min:0',
        ]);

        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('submitted_by', $user->id)
            ->where('status', 'approved')
            ->first();

        if (!$vehicle) {
            return $this->errorResponse(__('Vehicle not found or not approved.'), 404);
        }

        if (Auction::where('vehicle_id', $vehicle->id)->exists()) {
            return $this->errorResponse(__('An auction for this vehicle already exists.'), 400);
        }

        $deposit_required = $validated['bidding_mode'] === 'strict';
        $deposit_amount = $deposit_required ? ($validated['start_price'] * 0.10) : 0;

        $startTime = Carbon::parse($validated['start_time']);
        $status = $startTime->isPast() ? 'live' : 'scheduled';

        $auction = Auction::create([
            'vehicle_id' => $vehicle->id,
            'created_by' => $user->id,
            'title_ar' => 'مزاد: ' . $vehicle->make_ar . ' ' . $vehicle->model_ar . ' ' . $vehicle->year,
            'title_en' => 'Auction: ' . $vehicle->make_en . ' ' . $vehicle->model_en . ' ' . $vehicle->year,
            'description_ar' => $vehicle->description_ar,
            'description_en' => $vehicle->description_en,
            'location_ar' => $validated['location_ar'],
            'location_en' => $validated['location_en'],
            'start_price' => $validated['start_price'],
            'reserve_price' => $validated['reserve_price'],
            'buy_now_price' => $validated['buy_now_price'],
            'min_bid_increment' => $validated['min_bid_increment'],
            'start_time' => $startTime,
            'end_time' => Carbon::parse($validated['end_time']),
            'auto_extend_minutes' => $validated['auto_extend_minutes'] ?? 0,
            'deposit_required' => $deposit_required,
            'deposit_amount' => $deposit_amount,
            'status' => $status,
        ]);

        // Notify bidders
        $bidders = \App\Models\User::role('bidder')->where('id', '!=', $user->id)->get();
        if ($bidders->isNotEmpty()) {
            \Illuminate\Support\Facades\Notification::send($bidders, new \App\Notifications\GeneralNotification(
                'مزاد جديد متاح!',
                'تم إضافة مزاد جديد: ' . $auction->title_ar . '. سارع بالمزايدة الآن!',
                ['database'],
                url('/bidder/auctions/' . $auction->id)
            ));
        }

        return $this->successResponse(
            new AuctionResource($auction->load('vehicle')),
            __('Auction created successfully.')
        );
    }

    /**
     * Update an auction.
     */
    #[OA\Put(
        path: '/api/seller/garage/auctions/{id}',
        summary: 'Update an auction',
        description: 'Updates an existing auction that has not ended yet.',
        security: [['bearerAuth' => []]],
        tags: ['Seller Garage'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful Response'),
            new OA\Response(response: 422, description: 'Validation Error')
        ]
    )]
    public function updateAuction(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $auction = Auction::where('id', $id)
            ->where('created_by', $user->id)
            ->whereNotIn('status', ['ended', 'completed', 'cancelled'])
            ->first();

        if (!$auction) {
            return $this->errorResponse(__('Auction not found or cannot be edited.'), 404);
        }

        $validated = $request->validate([
            'start_price' => 'required|numeric|min:0',
            'reserve_price' => 'nullable|numeric|gte:start_price',
            'buy_now_price' => 'nullable|numeric|gte:reserve_price',
            'min_bid_increment' => 'required|numeric|min:1',
            'location_ar' => 'required|string|max:255',
            'location_en' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'bidding_mode' => 'required|in:open,strict',
            'auto_extend_minutes' => 'nullable|integer|min:0',
        ]);

        $deposit_required = $validated['bidding_mode'] === 'strict';
        $deposit_amount = $deposit_required ? ($validated['start_price'] * 0.10) : 0;

        $startTime = Carbon::parse($validated['start_time']);
        $status = $startTime->isPast() ? 'live' : 'scheduled';
        if ($auction->status === 'live' && !$startTime->isPast()) {
            $status = 'scheduled';
        } elseif ($auction->status === 'live' && $startTime->isPast()) {
            $status = 'live';
        }

        $auction->update([
            'location_ar' => $validated['location_ar'],
            'location_en' => $validated['location_en'],
            'start_price' => $validated['start_price'],
            'reserve_price' => $validated['reserve_price'],
            'buy_now_price' => $validated['buy_now_price'],
            'min_bid_increment' => $validated['min_bid_increment'],
            'start_time' => $startTime,
            'end_time' => Carbon::parse($validated['end_time']),
            'auto_extend_minutes' => $validated['auto_extend_minutes'] ?? 0,
            'deposit_required' => $deposit_required,
            'deposit_amount' => $deposit_amount,
            'status' => $status,
        ]);

        return $this->successResponse(
            new AuctionResource($auction->load('vehicle')),
            __('Auction updated successfully.')
        );
    }

    /**
     * Decode VIN (Mock)
     */
    #[OA\Post(
        path: '/api/seller/garage/decode-vin',
        summary: 'Decode VIN',
        description: 'Decodes a VIN number to get vehicle details.',
        security: [['bearerAuth' => []]],
        tags: ['Seller Garage'],
        responses: [
            new OA\Response(response: 200, description: 'Successful Response')
        ]
    )]
    public function decodeVin(Request $request): JsonResponse
    {
        $request->validate([
            'vin' => 'required|string|min:10|max:17'
        ]);

        $vin = strtoupper($request->vin);

        try {
            $response = \Illuminate\Support\Facades\Http::get("https://vpic.nhtsa.dot.gov/api/vehicles/DecodeVin/{$vin}?format=json");

            if ($response->successful()) {
                $results = $response->json()['Results'] ?? [];
                
                $data = [
                    'make' => null,
                    'model' => null,
                    'year' => null,
                    'engine_capacity' => null,
                    'fuel_type' => null,
                    'country_of_origin' => null,
                    'transmission' => null,
                ];

                foreach ($results as $item) {
                    $variable = $item['Variable'];
                    $value = $item['Value'];

                    if (empty($value)) continue;

                    switch ($variable) {
                        case 'Make':
                            $data['make'] = $value;
                            break;
                        case 'Model':
                            $data['model'] = $value;
                            break;
                        case 'Model Year':
                            $data['year'] = (int)$value;
                            break;
                        case 'Displacement (L)':
                            $data['engine_capacity'] = $value . 'L';
                            break;
                        case 'Fuel Type - Primary':
                            $data['fuel_type'] = $value;
                            break;
                        case 'Plant Country':
                            $data['country_of_origin'] = $value;
                            break;
                        case 'Transmission Style':
                            $data['transmission'] = $value;
                            break;
                    }
                }

                return $this->successResponse($data, 'VIN decoded successfully');
            }

            return $this->errorResponse(__('Unable to fetch data from VIN database.'), 422);

        } catch (\Exception $e) {
            return $this->errorResponse(__('Error: ') . $e->getMessage(), 500);
        }
    }

    /**
     * Generate Description using AI (Mock)
     */
    #[OA\Post(
        path: '/api/seller/garage/generate-description',
        summary: 'Generate AI Description',
        description: 'Generates a vehicle description based on provided specs.',
        security: [['bearerAuth' => []]],
        tags: ['Seller Garage'],
        responses: [
            new OA\Response(response: 200, description: 'Successful Response')
        ]
    )]
    public function generateDescription(Request $request): JsonResponse
    {
        $make = $request->input('make_ar', '');
        $model = $request->input('model_ar', '');
        $makeEn = $request->input('make_en', '');
        $modelEn = $request->input('model_en', '');
        $year = $request->input('year', '');
        $mileage = $request->input('mileage', '');
        $fuel = $request->input('fuel_type', '');
        $transmission = $request->input('transmission', '');

        // --- Arabic Template Generation ---
        $arTitle = "فرصة مميزة: {$make} {$model} موديل {$year}";
        $arBody = "نقدم لكم سيارة {$make} {$model} الأنيقة والاعتمادية موديل {$year}. ";
        
        if ($mileage) {
            $arBody .= "السيارة قطعت مسافة {$mileage} كم فقط، مما يجعلها بحالة ممتازة للاستخدام الفوري. ";
        }
        if ($fuel || $transmission) {
            $arBody .= "تتميز هذه المركبة بأداء استثنائي بفضل ";
            $parts = [];
            if ($transmission) $parts[] = "ناقل الحركة الـ {$transmission}";
            if ($fuel) $parts[] = "محركها الذي يعمل بـ {$fuel}";
            $arBody .= implode(' و', $parts) . ". ";
        }
        $arBody .= "تم فحص السيارة وهي جاهزة للمزايدة. لا تفوت فرصة امتلاك هذه المركبة الرائعة بسعر منافس!";
        
        $descriptionAr = "🌟 **" . $arTitle . "** 🌟\n\n" . $arBody;


        // --- English Template Generation ---
        $enTitle = "Exclusive Opportunity: {$year} {$makeEn} {$modelEn}";
        $enBody = "Presenting the elegant and reliable {$year} {$makeEn} {$modelEn}. ";
        
        if ($mileage) {
            $enBody .= "With a low mileage of just {$mileage} km, this vehicle is in excellent condition and ready for the road. ";
        }
        if ($fuel || $transmission) {
            $enBody .= "It boasts exceptional performance thanks to its ";
            $parts = [];
            if ($transmission) $parts[] = "{$transmission} transmission";
            if ($fuel) $parts[] = "{$fuel} engine";
            $enBody .= implode(' and ', $parts) . ". ";
        }
        $enBody .= "The car has been inspected and is ready for auction. Don't miss the chance to own this amazing vehicle at a competitive price!";
        
        $descriptionEn = "🌟 **" . $enTitle . "** 🌟\n\n" . $enBody;

        // Simulate API delay to make it feel like AI generation
        sleep(2);

        return $this->successResponse([
            'description_ar' => $descriptionAr,
            'description_en' => $descriptionEn
        ]);
    }
}
