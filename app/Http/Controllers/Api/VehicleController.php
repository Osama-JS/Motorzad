<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VehicleController extends Controller
{
    /**
     * List vehicles submitted by the current user.
     */
    public function index(Request $request): JsonResponse
    {
        $vehicles = Vehicle::where('submitted_by', $request->user()->id)
            ->with('images')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $vehicles
        ]);
    }

    /**
     * Store a newly created vehicle.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'make_ar' => 'required|string|max:100',
            'make_en' => 'required|string|max:100',
            'model_ar' => 'required|string|max:100',
            'model_en' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color_ar' => 'required|string|max:50',
            'color_en' => 'required|string|max:50',
            'vin_number' => 'required|string|max:50|unique:vehicles,vin_number',
            'mileage' => 'required|integer|min:0',
            'plate_number' => 'nullable|string|max:50',
            'country_of_origin' => 'nullable|string|max:100',
            'fuel_type' => 'required|string|max:50',
            'transmission' => 'required|string|max:50',
            'engine_capacity' => 'nullable|string|max:50',
            'cylinders' => 'required|integer|min:1',
            'condition' => 'required|string',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'issues_ar' => 'nullable|string',
            'issues_en' => 'nullable|string',
        ]);

        $validated['submitted_by'] = $request->user()->id;
        $validated['status'] = 'pending'; // Always pending for new vehicles

        $vehicle = Vehicle::create($validated);

        return response()->json([
            'success' => true,
            'message' => __('Vehicle submitted successfully. It is pending admin review.'),
            'data' => $vehicle
        ], 201);
    }

    /**
     * Display the specified vehicle.
     */
    public function show(Request $request, Vehicle $vehicle): JsonResponse
    {
        Gate::authorize('view', $vehicle);

        $vehicle->load(['images']);

        return response()->json([
            'success' => true,
            'data' => $vehicle
        ]);
    }

    /**
     * Update the specified vehicle.
     */
    public function update(Request $request, Vehicle $vehicle): JsonResponse
    {
        Gate::authorize('update', $vehicle);

        $validated = $request->validate([
            'make_ar' => 'required|string|max:100',
            'make_en' => 'required|string|max:100',
            'model_ar' => 'required|string|max:100',
            'model_en' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color_ar' => 'required|string|max:50',
            'color_en' => 'required|string|max:50',
            'vin_number' => 'required|string|max:50|unique:vehicles,vin_number,' . $vehicle->id,
            'mileage' => 'required|integer|min:0',
            'plate_number' => 'nullable|string|max:50',
            'country_of_origin' => 'nullable|string|max:100',
            'fuel_type' => 'required|string|max:50',
            'transmission' => 'required|string|max:50',
            'engine_capacity' => 'nullable|string|max:50',
            'cylinders' => 'required|integer|min:1',
            'condition' => 'required|string',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'issues_ar' => 'nullable|string',
            'issues_en' => 'nullable|string',
        ]);

        // When updated, reset status to pending if it was rejected
        $validated['status'] = 'pending';

        $vehicle->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('Vehicle updated successfully and is pending review.'),
            'data' => $vehicle
        ]);
    }

    /**
     * Remove the specified vehicle.
     */
    public function destroy(Request $request, Vehicle $vehicle): JsonResponse
    {
        Gate::authorize('delete', $vehicle);

        $vehicle->delete();

        return response()->json([
            'success' => true,
            'message' => __('Vehicle deleted successfully.')
        ]);
    }

    /**
     * Upload images for a vehicle.
     */
    public function uploadImages(Request $request, Vehicle $vehicle): JsonResponse
    {
        Gate::authorize('update', $vehicle);

        $request->validate([
            'images' => 'required|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('images')) {
            $existingCount = $vehicle->images()->count();
            foreach ($request->file('images') as $index => $imageFile) {
                $path = $imageFile->store('vehicles', 'public');
                $vehicle->images()->create([
                    'image_path' => $path,
                    'is_primary' => ($existingCount === 0 && $index === 0),
                    'sort_order' => $existingCount + $index
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => __('Images uploaded successfully.'),
            'data' => $vehicle->load('images')
        ]);
    }
    
    /**
     * Delete an image from a vehicle.
     */
    public function deleteImage(Request $request, Vehicle $vehicle, $imageId): JsonResponse
    {
        Gate::authorize('update', $vehicle);
        
        $image = $vehicle->images()->findOrFail($imageId);
        \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
        $image->delete();
        
        return response()->json([
            'success' => true,
            'message' => __('Image deleted successfully.')
        ]);
    }
}
