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
            new OA\Response(
                response: 200, 
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            'vehicles' => [
                                [
                                    'id' => 1,
                                    'make_ar' => 'تويوتا',
                                    'make_en' => 'Toyota',
                                    'model_ar' => 'كامري',
                                    'model_en' => 'Camry',
                                    'year' => 2022,
                                    'color_ar' => 'أبيض',
                                    'color_en' => 'White',
                                    'vin_number' => '1HGCM82633AXXXXXX',
                                    'mileage' => 45000,
                                    'fuel_type' => 'petrol',
                                    'transmission' => 'automatic',
                                    'engine_capacity' => '2.5L',
                                    'cylinders' => 4,
                                    'condition' => 'excellent',
                                    'description_ar' => 'سيارة نظيفة جداً',
                                    'description_en' => 'Very clean car',
                                    'status' => 'approved',
                                    'damage_points' => null,
                                    'primary_image_url' => 'https://example.com/storage/vehicles/1.jpg',
                                    'images' => [
                                        [
                                            'id' => 101,
                                            'url' => 'https://example.com/storage/vehicles/1.jpg',
                                            'is_primary' => true,
                                            'sort_order' => 0
                                        ]
                                    ],
                                    'created_at' => '2023-10-01T10:00:00.000000Z',
                                    'updated_at' => '2023-10-01T10:00:00.000000Z'
                                ]
                            ],
                            'stats' => [
                                'total' => 5,
                                'pending' => 1,
                                'approved' => 3,
                                'rejected' => 0,
                                'draft' => 1
                            ],
                            'meta' => [
                                'current_page' => 1,
                                'last_page' => 1,
                                'total' => 1,
                                'per_page' => 15
                            ]
                        ]
                    ]
                )
            ),
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
            new OA\Response(
                response: 200, 
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            'vehicles' => [
                                [
                                    'id' => 2,
                                    'make_ar' => 'هوندا',
                                    'make_en' => 'Honda',
                                    'model_ar' => 'اكورد',
                                    'model_en' => 'Accord',
                                    'year' => 2021,
                                    'status' => 'draft',
                                    'primary_image_url' => null
                                ]
                            ],
                            'meta' => [
                                'current_page' => 1,
                                'last_page' => 1,
                                'total' => 1,
                                'per_page' => 15
                            ]
                        ]
                    ]
                )
            ),
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
     * Get a specific vehicle by ID
     */
    #[OA\Get(
        path: '/api/seller/garage/vehicles/{id}',
        summary: 'Get Vehicle by ID',
        description: 'Returns the details of a specific vehicle belonging to the seller.',
        security: [['bearerAuth' => []]],
        tags: ['Seller Garage'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
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
                            'make_ar' => 'تويوتا',
                            'make_en' => 'Toyota',
                            'model_ar' => 'كامري',
                            'model_en' => 'Camry',
                            'year' => 2022,
                            'color_ar' => 'أبيض',
                            'color_en' => 'White',
                            'vin_number' => '1HGCM82633AXXXXXX',
                            'mileage' => 45000,
                            'fuel_type' => 'petrol',
                            'transmission' => 'automatic',
                            'engine_capacity' => '2.5L',
                            'cylinders' => 4,
                            'condition' => 'excellent',
                            'description_ar' => 'سيارة نظيفة جداً',
                            'description_en' => 'Very clean car',
                            'status' => 'approved',
                            'damage_points' => null,
                            'is_published_for_auction' => true,
                            'primary_image_url' => 'https://example.com/storage/vehicles/1.jpg',
                            'images' => [
                                [
                                    'id' => 101,
                                    'url' => 'https://example.com/storage/vehicles/1.jpg',
                                    'is_primary' => true,
                                    'sort_order' => 0
                                ]
                            ],
                            'created_at' => '2023-10-01T10:00:00.000000Z',
                            'updated_at' => '2023-10-01T10:00:00.000000Z'
                        ]
                    ]
                )
            ),
            new OA\Response(response: 403, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function showVehicle(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $vehicle = Vehicle::with('images')
            ->where('submitted_by', $user->id)
            ->where('id', $id)
            ->first();

        if (!$vehicle) {
            return $this->errorResponse(__('Vehicle not found.'), 404);
        }

        return $this->successResponse(
            new VehicleResource($vehicle),
            __('Vehicle retrieved successfully.')
        );
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
                        new OA\Property(property: 'color_ar', type: 'string', description: 'اللون (عربي)'),
                        new OA\Property(property: 'color_en', type: 'string', description: 'اللون (إنجليزي)'),
                        new OA\Property(property: 'vin_number', type: 'string', description: 'رقم الهيكل'),
                        new OA\Property(property: 'mileage', type: 'integer', description: 'الممشى'),
                        new OA\Property(property: 'fuel_type', type: 'string', enum: ['petrol', 'diesel', 'electric', 'hybrid', 'other'], description: 'نوع الوقود (بنزين، ديزل، كهرباء، هجين، أخرى)'),
                        new OA\Property(property: 'transmission', type: 'string', enum: ['automatic', 'manual', 'cvt'], description: 'ناقل الحركة (أوتوماتيك، عادي، تتابعي)'),
                        new OA\Property(property: 'engine_capacity', type: 'string', description: 'سعة المحرك'),
                        new OA\Property(property: 'cylinders', type: 'integer', description: 'عدد السلندرات'),
                        new OA\Property(property: 'condition', type: 'string', enum: ['new', 'excellent', 'good', 'fair', 'damaged'], description: 'حالة السيارة'),
                        new OA\Property(property: 'description_ar', type: 'string', description: 'وصف السيارة (عربي)'),
                        new OA\Property(property: 'description_en', type: 'string', description: 'وصف السيارة (إنجليزي)'),
                        new OA\Property(property: 'damage_points', type: 'string', description: 'نقاط الضرر (JSON array)'),
                        new OA\Property(property: 'primary_image_index', type: 'integer', description: 'فهرس الصورة الرئيسية'),
                        new OA\Property(property: 'images[]', type: 'array', items: new OA\Items(type: 'string', format: 'binary'), description: 'صور السيارة'),
                        new OA\Property(property: 'action', type: 'string', description: 'draft or submit'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201, 
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => 'Vehicle saved successfully.',
                        'data' => [
                            'id' => 10,
                            'make_ar' => 'فورد',
                            'make_en' => 'Ford',
                            'model_ar' => 'موستانج',
                            'model_en' => 'Mustang',
                            'year' => 2023,
                            'color_ar' => 'أسود',
                            'color_en' => 'Black',
                            'vin_number' => '1FA6P8CF8N5XXXXXX',
                            'mileage' => 12000,
                            'fuel_type' => 'petrol',
                            'transmission' => 'automatic',
                            'engine_capacity' => '5.0L',
                            'cylinders' => 8,
                            'condition' => 'excellent',
                            'description_ar' => 'سيارة رياضية ممتازة',
                            'description_en' => 'Excellent sports car',
                            'status' => 'approved',
                            'damage_points' => null,
                            'primary_image_url' => 'https://example.com/storage/vehicles/10.jpg',
                            'images' => [
                                [
                                    'id' => 102,
                                    'url' => 'https://example.com/storage/vehicles/10.jpg',
                                    'is_primary' => true,
                                    'sort_order' => 0
                                ]
                            ],
                            'created_at' => '2023-10-01T10:00:00.000000Z',
                            'updated_at' => '2023-10-01T10:00:00.000000Z'
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 422, 
                description: 'Validation Error',
                content: new OA\JsonContent(
                    example: [
                        'message' => 'The given data was invalid.',
                        'errors' => [
                            'make_ar' => ['The make ar field is required.'],
                            'year' => ['The year must be at least 1901.']
                        ]
                    ]
                )
            )
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
            'fuel_type' => 'nullable|string|in:petrol,diesel,electric,hybrid,other',
            'transmission' => 'nullable|string|in:automatic,manual,cvt',
            'engine_capacity' => 'nullable|string|max:50',
            'cylinders' => 'nullable|integer|min:1',
            'condition' => 'nullable|string|in:new,excellent,good,fair,damaged',
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
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: '_method', type: 'string', default: 'PUT', description: 'Method spoofing for PUT request'),
                        new OA\Property(property: 'make_ar', type: 'string'),
                        new OA\Property(property: 'make_en', type: 'string'),
                        new OA\Property(property: 'model_ar', type: 'string'),
                        new OA\Property(property: 'model_en', type: 'string'),
                        new OA\Property(property: 'year', type: 'integer'),
                        new OA\Property(property: 'color_ar', type: 'string', description: 'اللون (عربي)'),
                        new OA\Property(property: 'color_en', type: 'string', description: 'اللون (إنجليزي)'),
                        new OA\Property(property: 'vin_number', type: 'string', description: 'رقم الهيكل'),
                        new OA\Property(property: 'mileage', type: 'integer', description: 'الممشى'),
                        new OA\Property(property: 'fuel_type', type: 'string', enum: ['petrol', 'diesel', 'electric', 'hybrid', 'other'], description: 'نوع الوقود (بنزين، ديزل، كهرباء، هجين، أخرى)'),
                        new OA\Property(property: 'transmission', type: 'string', enum: ['automatic', 'manual', 'cvt'], description: 'ناقل الحركة (أوتوماتيك، عادي، تتابعي)'),
                        new OA\Property(property: 'engine_capacity', type: 'string', description: 'سعة المحرك'),
                        new OA\Property(property: 'cylinders', type: 'integer', description: 'عدد السلندرات'),
                        new OA\Property(property: 'condition', type: 'string', description: 'حالة السيارة'),
                        new OA\Property(property: 'description_ar', type: 'string', description: 'وصف السيارة (عربي)'),
                        new OA\Property(property: 'description_en', type: 'string', description: 'وصف السيارة (إنجليزي)'),
                        new OA\Property(property: 'damage_points', type: 'string', description: 'نقاط الضرر (JSON array)'),
                        new OA\Property(property: 'primary_image_index', type: 'integer', description: 'فهرس الصورة الرئيسية'),
                        new OA\Property(property: 'images[]', type: 'array', items: new OA\Items(type: 'string', format: 'binary'), description: 'الصور الجديدة'),
                        new OA\Property(property: 'existing_images', type: 'string', description: 'JSON array of kept images with serverId and order'),
                        new OA\Property(property: 'new_images_order', type: 'string', description: 'JSON array specifying sort order of new images'),
                        new OA\Property(property: 'action', type: 'string', description: 'draft or submit'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200, 
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => 'Vehicle updated successfully.',
                        'data' => [
                            'id' => 10,
                            'make_ar' => 'فورد',
                            'make_en' => 'Ford',
                            'model_ar' => 'موستانج',
                            'model_en' => 'Mustang',
                            'year' => 2023,
                            'color_ar' => 'أسود',
                            'color_en' => 'Black',
                            'vin_number' => '1FA6P8CF8N5XXXXXX',
                            'mileage' => 12000,
                            'fuel_type' => 'petrol',
                            'transmission' => 'automatic',
                            'engine_capacity' => '5.0L',
                            'cylinders' => 8,
                            'condition' => 'excellent',
                            'description_ar' => 'سيارة رياضية ممتازة',
                            'description_en' => 'Excellent sports car',
                            'status' => 'approved',
                            'damage_points' => null,
                            'primary_image_url' => 'https://example.com/storage/vehicles/10_updated.jpg',
                            'images' => [
                                [
                                    'id' => 102,
                                    'url' => 'https://example.com/storage/vehicles/10_updated.jpg',
                                    'is_primary' => true,
                                    'sort_order' => 0
                                ]
                            ],
                            'created_at' => '2023-10-01T10:00:00.000000Z',
                            'updated_at' => '2023-10-01T10:05:00.000000Z'
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 422, 
                description: 'Validation Error',
                content: new OA\JsonContent(
                    example: [
                        'message' => 'The given data was invalid.',
                        'errors' => [
                            'make_ar' => ['The make ar field is required.'],
                            'year' => ['The year must be at least 1901.']
                        ]
                    ]
                )
            )
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
            'fuel_type' => 'nullable|string|in:petrol,diesel,electric,hybrid,other',
            'transmission' => 'nullable|string|in:automatic,manual,cvt',
            'engine_capacity' => 'nullable|string|max:50',
            'cylinders' => 'nullable|integer|min:1',
            'condition' => 'nullable|string|in:new,excellent,good,fair,damaged',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'damage_points' => 'nullable|string',
            'existing_images' => 'nullable|string', // JSON array of objects with serverId and order
            'new_images_order' => 'nullable|string',
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
        if ($request->has('existing_images') && $validated['existing_images'] !== null) {
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
        }

        // Handle new images
        if ($request->hasFile('images')) {
            $newImagesOrder = json_decode($validated['new_images_order'] ?? '[]', true) ?? [];
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('vehicles', 'public');
                $sortOrder = $newImagesOrder[$index]['order'] ?? $index + 99;
                
                VehicleImage::create([
                    'vehicle_id' => $vehicle->id,
                    'image_path' => $path,
                    'is_primary' => false,
                    'sort_order' => $sortOrder
                ]);
            }
        }

        // Set primary image
        $primaryIndex = (int) $request->input('primary_image_index', 0);
        $primaryImage = VehicleImage::where('vehicle_id', $vehicle->id)
            ->where('sort_order', $primaryIndex)
            ->first();
            
        if ($primaryImage) {
            $primaryImage->update(['is_primary' => true]);
        } elseif (VehicleImage::where('vehicle_id', $vehicle->id)->exists()) {
            VehicleImage::where('vehicle_id', $vehicle->id)->orderBy('sort_order')->first()->update(['is_primary' => true]);
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
     * Submit a draft vehicle to make it published.
     */
    #[OA\Post(
        path: '/api/seller/garage/vehicles/{id}/submit',
        summary: 'Submit a draft vehicle',
        description: 'Changes the status of a draft vehicle to approved/submitted.',
        security: [['bearerAuth' => []]],
        tags: ['Seller Garage'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200, 
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => 'Vehicle submitted successfully.',
                        'data' => [
                            'id' => 10,
                            'make_ar' => 'فورد',
                            'make_en' => 'Ford',
                            'model_ar' => 'موستانج',
                            'model_en' => 'Mustang',
                            'year' => 2023,
                            'color_ar' => 'أسود',
                            'color_en' => 'Black',
                            'vin_number' => '1FA6P8CF8N5XXXXXX',
                            'mileage' => 12000,
                            'fuel_type' => 'petrol',
                            'transmission' => 'automatic',
                            'engine_capacity' => '5.0L',
                            'cylinders' => 8,
                            'condition' => 'excellent',
                            'description_ar' => 'سيارة رياضية ممتازة',
                            'description_en' => 'Excellent sports car',
                            'status' => 'approved',
                            'damage_points' => null,
                            'primary_image_url' => 'https://example.com/storage/vehicles/10.jpg',
                            'images' => [
                                [
                                    'id' => 102,
                                    'url' => 'https://example.com/storage/vehicles/10.jpg',
                                    'is_primary' => true,
                                    'sort_order' => 0
                                ]
                            ],
                            'created_at' => '2023-10-01T10:00:00.000000Z',
                            'updated_at' => '2023-10-01T10:05:00.000000Z'
                        ]
                    ]
                )
            ),
            new OA\Response(response: 403, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function submitDraft(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $vehicle = Vehicle::where('id', $id)
            ->where('submitted_by', $user->id)
            ->first();

        if (!$vehicle) {
            return $this->errorResponse(__('Vehicle not found.'), 404);
        }

        if ($vehicle->status !== 'draft') {
            return $this->errorResponse(__('This vehicle is not a draft.'), 400);
        }

        $vehicle->update(['status' => 'approved']);

        return $this->successResponse(
            new VehicleResource($vehicle->load('images')),
            __('Vehicle submitted successfully.')
        );
    }

    /**
     * Get seller's auctions
     */
    #[OA\Get(
        path: '/api/seller/garage/auctions',
        summary: 'Get Seller Auctions',
        description: 'Returns a list of auctions created by the seller.',
        security: [['bearerAuth' => []]],
        tags: ['Seller Garage'],
        responses: [
            new OA\Response(
                response: 200, 
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            'auctions' => [
                                [
                                    'id' => 50,
                                    'title_ar' => 'مزاد: فورد موستانج 2023',
                                    'title_en' => 'Auction: Ford Mustang 2023',
                                    'start_price' => 150000,
                                    'status' => 'scheduled',
                                    'start_time' => '2023-12-01T10:00:00.000000Z',
                                    'end_time' => '2023-12-10T10:00:00.000000Z',
                                    'vehicle' => [
                                        'id' => 10,
                                        'make_ar' => 'فورد',
                                        'make_en' => 'Ford',
                                        'model_ar' => 'موستانج',
                                        'model_en' => 'Mustang',
                                        'year' => 2023,
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
                    ]
                )
            ),
            new OA\Response(response: 403, description: 'Unauthorized')
        ]
    )]
    public function myAuctions(Request $request): JsonResponse
    {
        $user = $request->user();

        $auctions = Auction::with('vehicle.images')
            ->where('created_by', $user->id)
            ->latest()
            ->paginate(15);

        return $this->successResponse([
            'auctions' => AuctionResource::collection($auctions->items()),
            'meta' => [
                'current_page' => $auctions->currentPage(),
                'last_page'    => $auctions->lastPage(),
                'total'        => $auctions->total(),
                'per_page'     => $auctions->perPage(),
            ]
        ]);
    }

    /**
     * Get a specific auction created by the seller
     */
    #[OA\Get(
        path: '/api/seller/garage/auctions/{id}',
        summary: 'Get Seller Auction Details',
        description: 'Returns the details of a specific auction created by the seller.',
        security: [['bearerAuth' => []]],
        tags: ['Seller Garage'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200, 
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => 'Auction retrieved successfully.',
                        'data' => [
                            'id' => 50,
                            'title_ar' => 'مزاد: فورد موستانج 2023',
                            'title_en' => 'Auction: Ford Mustang 2023',
                            'description_ar' => 'وصف المزاد',
                            'description_en' => 'Auction Description',
                            'location_ar' => 'الرياض',
                            'location_en' => 'Riyadh',
                            'start_price' => 150000,
                            'reserve_price' => null,
                            'buy_now_price' => null,
                            'min_bid_increment' => 1000,
                            'status' => 'scheduled',
                            'deposit_required' => false,
                            'deposit_amount' => 0,
                            'start_time' => '2023-12-01T10:00:00.000000Z',
                            'end_time' => '2023-12-10T10:00:00.000000Z',
                            'vehicle' => [
                                'id' => 10,
                                'make_ar' => 'فورد',
                                'make_en' => 'Ford',
                                'model_ar' => 'موستانج',
                                'model_en' => 'Mustang',
                                'year' => 2023,
                                'color_ar' => 'أسود',
                                'color_en' => 'Black',
                                'vin_number' => '1FA6P8CF8N5XXXXXX',
                            ],
                            'created_at' => '2023-10-01T10:00:00.000000Z',
                            'updated_at' => '2023-10-01T10:05:00.000000Z'
                        ]
                    ]
                )
            ),
            new OA\Response(response: 403, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function showAuction(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $auction = Auction::with('vehicle.images')
            ->where('created_by', $user->id)
            ->where('id', $id)
            ->first();

        if (!$auction) {
            return $this->errorResponse(__('Auction not found.'), 404);
        }

        return $this->successResponse(
            new AuctionResource($auction),
            __('Auction retrieved successfully.')
        );
    }

    /**
     * Get bids for a specific auction
     */
    #[OA\Get(
        path: '/api/seller/garage/auctions/{id}/bids',
        summary: 'Get Auction Bids',
        description: 'Returns a paginated list of bids for a specific auction.',
        security: [['bearerAuth' => []]],
        tags: ['Seller Garage'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            'bids' => [
                                [
                                    'id' => 1,
                                    'amount' => 160000,
                                    'status' => 'active',
                                    'created_at' => '2023-10-01T10:05:00.000000Z',
                                    'user' => [
                                        'id' => 5,
                                        'name' => 'محمد أحمد',
                                        'phone' => '+966500000000'
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
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Auction not found')
        ]
    )]
    public function auctionBids(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $auction = Auction::where('created_by', $user->id)->find($id);

        if (!$auction) {
            return $this->errorResponse(__('Auction not found.'), 404);
        }

        $bids = $auction->bids()->with('user:id,name,phone,avatar')->latest()->paginate(15);

        return $this->successResponse([
            'bids' => $bids->items(),
            'meta' => [
                'current_page' => $bids->currentPage(),
                'last_page'    => $bids->lastPage(),
                'total'        => $bids->total(),
                'per_page'     => $bids->perPage(),
            ]
        ]);
    }

    /**
     * Accept a specific bid manually to close the auction
     */
    #[OA\Post(
        path: '/api/seller/garage/auctions/{id}/bids/{bidId}/accept',
        summary: 'Accept Bid manually',
        description: 'Accepts a specific bid and marks the auction as sold/completed.',
        security: [['bearerAuth' => []]],
        tags: ['Seller Garage'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'bidId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => 'Bid accepted successfully. The auction is now closed and sold.',
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Auction already closed'),
            new OA\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function acceptBid(Request $request, $id, $bidId): JsonResponse
    {
        $user = $request->user();

        $auction = Auction::where('created_by', $user->id)->find($id);

        if (!$auction) {
            return $this->errorResponse(__('Auction not found.'), 404);
        }

        if (in_array($auction->status, ['completed', 'sold', 'cancelled'])) {
            return $this->errorResponse(__('This auction is already closed.'), 400);
        }

        $bid = $auction->bids()->find($bidId);

        if (!$bid) {
            return $this->errorResponse(__('Bid not found.'), 404);
        }

        // Accept the bid and mark auction as sold
        $auction->update([
            'status' => 'sold',
            'winner_id' => $bid->user_id,
            'winning_bid_amount' => $bid->amount,
            'sold_at' => now(),
        ]);
        
        $bid->update(['status' => 'accepted']);

        // Here we could also dispatch an event or send notifications to the winner 
        // and other bidders that the auction has ended.

        return $this->successResponse(
            null,
            __('Bid accepted successfully. The auction is now closed and sold.')
        );
    }

    /**
     * End an auction early (cancel or close it manually)
     */
    #[OA\Post(
        path: '/api/seller/garage/auctions/{id}/end',
        summary: 'End Auction Early',
        description: 'Allows the seller to manually stop an active or scheduled auction before its timer ends.',
        security: [['bearerAuth' => []]],
        tags: ['Seller Garage'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => 'Auction ended successfully.',
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Auction is already closed'),
            new OA\Response(response: 404, description: 'Not Found')
        ]
    )]
    public function endEarly(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $auction = Auction::where('created_by', $user->id)->find($id);

        if (!$auction) {
            return $this->errorResponse(__('Auction not found.'), 404);
        }

        if (in_array($auction->status, ['completed', 'sold', 'cancelled'])) {
            return $this->errorResponse(__('This auction is already closed.'), 400);
        }

        $auction->update([
            'status' => 'cancelled', // Setting to cancelled since it ended without a winner through the platform
            'end_time' => now(),
        ]);

        return $this->successResponse(
            null,
            __('Auction ended successfully.')
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
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['vehicle_id', 'start_price', 'min_bid_increment', 'location_ar', 'location_en', 'start_time', 'end_time', 'bidding_mode'],
                properties: [
                    new OA\Property(property: 'vehicle_id', type: 'integer'),
                    new OA\Property(property: 'start_price', type: 'number'),
                    new OA\Property(property: 'reserve_price', type: 'number', nullable: true),
                    new OA\Property(property: 'buy_now_price', type: 'number', nullable: true),
                    new OA\Property(property: 'min_bid_increment', type: 'number'),
                    new OA\Property(property: 'location_ar', type: 'string'),
                    new OA\Property(property: 'location_en', type: 'string'),
                    new OA\Property(property: 'start_time', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'end_time', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'bidding_mode', type: 'string', enum: ['open', 'strict']),
                    new OA\Property(property: 'auto_extend_minutes', type: 'integer', nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200, 
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => 'Auction created successfully.',
                        'data' => [
                            'id' => 50,
                            'title_ar' => 'مزاد: فورد موستانج 2023',
                            'title_en' => 'Auction: Ford Mustang 2023',
                            'start_price' => 150000,
                            'status' => 'scheduled',
                            'start_time' => '2023-12-01T10:00:00.000000Z',
                            'end_time' => '2023-12-10T10:00:00.000000Z',
                            'vehicle' => [
                                'id' => 10,
                                'make' => 'Ford',
                                'model' => 'Mustang',
                                'year' => 2023
                            ]
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 422, 
                description: 'Validation Error',
                content: new OA\JsonContent(
                    example: [
                        'message' => 'The given data was invalid.',
                        'errors' => [
                            'start_price' => ['The start price field is required.'],
                            'end_time' => ['The end time must be a date after start time.']
                        ]
                    ]
                )
            )
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
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['start_price', 'min_bid_increment', 'location_ar', 'location_en', 'start_time', 'end_time', 'bidding_mode'],
                properties: [
                    new OA\Property(property: 'start_price', type: 'number'),
                    new OA\Property(property: 'reserve_price', type: 'number', nullable: true),
                    new OA\Property(property: 'buy_now_price', type: 'number', nullable: true),
                    new OA\Property(property: 'min_bid_increment', type: 'number'),
                    new OA\Property(property: 'location_ar', type: 'string'),
                    new OA\Property(property: 'location_en', type: 'string'),
                    new OA\Property(property: 'start_time', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'end_time', type: 'string', format: 'date-time'),
                    new OA\Property(property: 'bidding_mode', type: 'string', enum: ['open', 'strict']),
                    new OA\Property(property: 'auto_extend_minutes', type: 'integer', nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200, 
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => 'Auction updated successfully.',
                        'data' => [
                            'id' => 50,
                            'title_ar' => 'مزاد: فورد موستانج 2023',
                            'title_en' => 'Auction: Ford Mustang 2023',
                            'start_price' => 145000,
                            'status' => 'scheduled',
                            'start_time' => '2023-12-01T10:00:00.000000Z',
                            'end_time' => '2023-12-10T10:00:00.000000Z'
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 422, 
                description: 'Validation Error',
                content: new OA\JsonContent(
                    example: [
                        'message' => 'The given data was invalid.',
                        'errors' => [
                            'start_price' => ['The start price field is required.'],
                            'end_time' => ['The end time must be a date after start time.']
                        ]
                    ]
                )
            )
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
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['vin'],
                properties: [
                    new OA\Property(property: 'vin', type: 'string', description: 'The 17-character Vehicle Identification Number')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200, 
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => 'VIN decoded successfully',
                        'data' => [
                            'make' => 'TOYOTA',
                            'model' => 'CAMRY',
                            'year' => 2022,
                            'engine_capacity' => '2.5L',
                            'fuel_type' => 'Gasoline',
                            'country_of_origin' => 'JAPAN',
                            'transmission' => 'Automatic'
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 422, 
                description: 'Validation Error',
                content: new OA\JsonContent(
                    example: [
                        'message' => 'The given data was invalid.',
                        'errors' => [
                            'vin' => ['The vin field is required.']
                        ]
                    ]
                )
            )
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
            new OA\Response(
                response: 200, 
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            'description_ar' => '🌟 **فرصة مميزة: تويوتا كامري موديل 2022** 🌟\n\nنقدم لكم سيارة تويوتا كامري الأنيقة والاعتمادية موديل 2022. السيارة قطعت مسافة 45000 كم فقط، مما يجعلها بحالة ممتازة للاستخدام الفوري. تتميز هذه المركبة بأداء استثنائي بفضل محركها الذي يعمل بـ البنزين. تم فحص السيارة وهي جاهزة للمزايدة. لا تفوت فرصة امتلاك هذه المركبة الرائعة بسعر منافس!',
                            'description_en' => '🌟 **Exclusive Opportunity: 2022 Toyota Camry** 🌟\n\nPresenting the elegant and reliable 2022 Toyota Camry. With a low mileage of just 45000 km, this vehicle is in excellent condition and ready for the road. It boasts exceptional performance thanks to its Gasoline engine. The car has been inspected and is ready for auction. Don\'t miss the chance to own this amazing vehicle at a competitive price!'
                        ]
                    ]
                )
            )
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
