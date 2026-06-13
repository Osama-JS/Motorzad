<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => Vehicle::count(),
            'approved' => Vehicle::where('status', 'approved')->count(),
            'pending' => Vehicle::where('status', 'pending')->count(),
            'rejected' => Vehicle::where('status', 'rejected')->count(),
        ];
        
        return view('admin.vehicles.index', compact('stats'));
    }

    public function getData(Request $request)
    {
        $vehicles = Vehicle::with(['submittedBy'])->latest()->get();

        return response()->json([
            'data' => $vehicles->map(function($vehicle) {
                $statusBadge = match($vehicle->status) {
                    'approved' => '<span class="status-indicator status-live" style="background:#dcfce7; color:#15803d; padding:6px 12px; border-radius:50px; font-weight:600; font-size:0.8rem; display:inline-flex; align-items:center; gap:6px;"><i class="fa-solid fa-circle-check" style="font-size:0.75rem;"></i> '.__('Approved').'</span>',
                    'pending' => '<span class="status-indicator status-scheduled" style="background:#fef3c7; color:#b45309; padding:6px 12px; border-radius:50px; font-weight:600; font-size:0.8rem; display:inline-flex; align-items:center; gap:6px;"><i class="fa-solid fa-clock" style="font-size:0.75rem;"></i> '.__('Pending').'</span>',
                    'rejected' => '<span class="status-indicator status-cancelled" style="background:#fee2e2; color:#b91c1c; padding:6px 12px; border-radius:50px; font-weight:600; font-size:0.8rem; display:inline-flex; align-items:center; gap:6px;"><i class="fa-solid fa-circle-xmark" style="font-size:0.75rem;"></i> '.__('Rejected').'</span>',
                    default => '<span class="status-indicator status-draft" style="background:#f1f5f9; color:#475569; padding:6px 12px; border-radius:50px; font-weight:600; font-size:0.8rem; display:inline-flex; align-items:center; gap:6px;"><i class="fa-solid fa-circle" style="font-size:0.5rem;"></i> '.__($vehicle->status).'</span>',
                };

                $quickActions = '';
                if ($vehicle->status === 'pending') {
                    $quickActions = '
                        <button onclick="approveVehicle(' . $vehicle->id . ')" class="btn btn-sm text-white d-inline-flex align-items-center gap-1 px-3 py-1.5 rounded-pill" style="background:#10b981; border:none; font-size:0.8rem; font-weight:700; transition:all 0.2s;" title="قبول"><i class="fa-solid fa-check" style="font-size:0.75rem;"></i> '.__('Approve').'</button>
                        <button onclick="rejectVehicle(' . $vehicle->id . ')" class="btn btn-sm text-white d-inline-flex align-items-center gap-1 px-3 py-1.5 rounded-pill" style="background:#ef4444; border:none; font-size:0.8rem; font-weight:700; transition:all 0.2s;" title="رفض"><i class="fa-solid fa-xmark" style="font-size:0.75rem;"></i> '.__('Reject').'</button>
                    ';
                }

                $imageHtml = $vehicle->primary_image_url 
                                ? '<img src="' . $vehicle->primary_image_url . '" width="60" style="border-radius:10px; object-fit:cover; height:45px; border: 1px solid var(--border);" alt="">' 
                                : '<div style="width:60px;height:45px;background:#eee;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#999;font-size:10px;border: 1px solid var(--border);">' . __('No Image') . '</div>';

                return [
                    'id' => $vehicle->id,
                    'image' => $imageHtml,
                    'title' => '<strong>' . $vehicle->title . '</strong>',
                    'vin_number' => $vehicle->vin_number ?? 'N/A',
                    'status' => $statusBadge,
                    'actions' => '
                        <div class="actions-cell" style="display:flex; gap:6px; justify-content:center; align-items:center;">
                            ' . $quickActions . '
                            <a href="' . route('admin.vehicles.show', $vehicle->id) . '" class="btn btn-sm text-white d-inline-flex align-items-center gap-1 px-3 py-1.5 rounded-pill" style="background:#0ea5e9; border:none; font-size:0.8rem; font-weight:700; transition:all 0.2s;" title="' . __('View') . '"><i class="fa-solid fa-eye" style="font-size:0.75rem;"></i> ' . __('View') . '</a>
                            <a href="' . route('admin.vehicles.edit', $vehicle->id) . '" class="btn btn-sm text-white d-inline-flex align-items-center gap-1 px-3 py-1.5 rounded-pill" style="background:var(--primary); border:none; font-size:0.8rem; font-weight:700; transition:all 0.2s;" title="' . __('Edit') . '"><i class="fa-solid fa-pen-to-square" style="font-size:0.75rem;"></i> ' . __('Edit') . '</a>
                            <button onclick="deleteVehicle(' . $vehicle->id . ')" class="btn btn-sm text-white d-inline-flex align-items-center gap-1 px-3 py-1.5 rounded-pill" style="background:#ef4444; border:none; font-size:0.8rem; font-weight:700; transition:all 0.2s;" title="' . __('Delete') . '"><i class="fa-solid fa-trash" style="font-size:0.75rem;"></i> ' . __('Delete') . '</button>
                        </div>'
                ];
            })
        ]);
    }

    public function create()
    {
        return view('admin.vehicles.create');
    }

    public function edit(Vehicle $vehicle)
    {
        $vehicle->load('images');
        return view('admin.vehicles.edit', compact('vehicle'));
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['images', 'submittedBy']);
        return view('admin.vehicles.show', compact('vehicle'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'make_ar' => 'required|string|max:100',
            'make_en' => 'required|string|max:100',
            'model_ar' => 'required|string|max:100',
            'model_en' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color_ar' => 'nullable|string|max:50',
            'color_en' => 'nullable|string|max:50',
            'vin_number' => 'nullable|string|max:50|unique:vehicles,vin_number',
            'mileage' => 'nullable|integer|min:0',
            'plate_number' => 'nullable|string|max:50',
            'country_of_origin' => 'nullable|string|max:100',
            'fuel_type' => 'nullable|string|max:50',
            'transmission' => 'nullable|string|max:50',
            'engine_capacity' => 'nullable|string|max:50',
            'cylinders' => 'nullable|integer|min:1',
            'condition' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'primary_image_index' => 'nullable|integer',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'issues_ar' => 'nullable|string',
            'issues_en' => 'nullable|string',
            'damage_points' => 'nullable|string',
        ]);

        if (isset($validated['damage_points']) && is_string($validated['damage_points'])) {
            $validated['damage_points'] = json_decode($validated['damage_points'], true);
        }

        if (!isset($validated['features'])) {
            $validated['features'] = [];
        }

        $validated['submitted_by'] = auth()->id();
        if ($validated['status'] !== 'pending') {
            $validated['reviewed_by'] = auth()->id();
            $validated['reviewed_at'] = now();
        }

        $vehicle = Vehicle::create($validated);

        if ($request->hasFile('images')) {
            $primaryIndex = $request->input('primary_image_index', 0);
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('vehicles', 'public');
                \App\Models\VehicleImage::create([
                    'vehicle_id' => $vehicle->id,
                    'image_path' => $path,
                    'is_primary' => ($index == $primaryIndex),
                    'sort_order' => $index
                ]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم إضافة المركبة بنجاح'
            ]);
        }
        return redirect()->route('admin.vehicles.index')->with('success', 'تم إضافة المركبة بنجاح');
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'make_ar' => 'required|string|max:100',
            'make_en' => 'required|string|max:100',
            'model_ar' => 'required|string|max:100',
            'model_en' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color_ar' => 'nullable|string|max:50',
            'color_en' => 'nullable|string|max:50',
            'vin_number' => 'nullable|string|max:50|unique:vehicles,vin_number,' . $vehicle->id,
            'mileage' => 'nullable|integer|min:0',
            'plate_number' => 'nullable|string|max:50',
            'country_of_origin' => 'nullable|string|max:100',
            'fuel_type' => 'nullable|string|max:50',
            'transmission' => 'nullable|string|max:50',
            'engine_capacity' => 'nullable|string|max:50',
            'cylinders' => 'nullable|integer|min:1',
            'condition' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'primary_image_index' => 'nullable|integer',
            'rejection_reason' => 'nullable|string|max:1000',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'issues_ar' => 'nullable|string',
            'issues_en' => 'nullable|string',
            'damage_points' => 'nullable|string',
        ]);

        if (isset($validated['damage_points']) && is_string($validated['damage_points'])) {
            $validated['damage_points'] = json_decode($validated['damage_points'], true);
        }

        if (!isset($validated['features'])) {
            $validated['features'] = [];
        }

        if ($validated['status'] !== $vehicle->status) {
            $validated['reviewed_by'] = auth()->id();
            $validated['reviewed_at'] = now();
        }

        if ($validated['status'] !== 'rejected') {
            $validated['rejection_reason'] = null;
        }

        $vehicle->update($validated);

        if ($request->hasFile('images')) {
            $hasPrimary = \App\Models\VehicleImage::where('vehicle_id', $vehicle->id)->where('is_primary', true)->exists();
            $primaryIndex = $request->input('primary_image_index', -1);
            
            if (!$hasPrimary && $primaryIndex === -1) {
                $primaryIndex = 0;
            }

            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('vehicles', 'public');
                $isPrimary = ($index == $primaryIndex);
                if ($isPrimary) {
                    \App\Models\VehicleImage::where('vehicle_id', $vehicle->id)->update(['is_primary' => false]);
                }

                \App\Models\VehicleImage::create([
                    'vehicle_id' => $vehicle->id,
                    'image_path' => $path,
                    'is_primary' => $isPrimary || (!$hasPrimary && $index == 0),
                    'sort_order' => $index + 100
                ]);
                $hasPrimary = true;
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث المركبة بنجاح'
            ]);
        }
        return redirect()->route('admin.vehicles.index')->with('success', 'تم تحديث المركبة بنجاح');
    }

    public function destroy(Vehicle $vehicle)
    {
        // Delete all images first
        foreach ($vehicle->images as $image) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($image->image_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }

        $vehicle->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المركبة بنجاح'
        ]);
    }

    public function deleteImage(\App\Models\VehicleImage $image)
    {
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($image->image_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($image->image_path);
        }
        
        $vehicleId = $image->vehicle_id;
        $wasPrimary = $image->is_primary;
        
        $image->delete();
        
        if ($wasPrimary) {
            $nextImage = \App\Models\VehicleImage::where('vehicle_id', $vehicleId)->first();
            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'تم حذف الصورة بنجاح'
        ]);
    }

    public function setPrimaryImage(\App\Models\VehicleImage $image)
    {
        \App\Models\VehicleImage::where('vehicle_id', $image->vehicle_id)->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);
        
        return response()->json([
            'success' => true,
            'message' => 'تم تعيين الصورة كصورة أساسية'
        ]);
    }

    public function approve(Vehicle $vehicle)
    {
        $vehicle->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم قبول واعتماد المركبة بنجاح'
        ]);
    }

    public function reject(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $vehicle->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'rejection_reason' => $request->rejection_reason
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم رفض المركبة وتدوين سبب الرفض بنجاح'
        ]);
    }

    public function decodeVin(Request $request)
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

                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => __('Unable to fetch data from VIN database.')
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error: ') . $e->getMessage()
            ], 500);
        }
    }

    public function reorderImages(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:vehicle_images,id'
        ]);

        foreach ($request->order as $index => $id) {
            \App\Models\VehicleImage::where('id', $id)->update([
                'sort_order' => $index
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => __('Images reordered successfully.')
        ]);
    }

    public function updateImage(Request $request, \App\Models\VehicleImage $image)
    {
        $request->validate([
            'image_data' => 'required|string'
        ]);

        try {
            $imgData = $request->image_data;
            if (preg_match('/^data:image\/(\w+);base64,/', $imgData, $type)) {
                $imgData = substr($imgData, strpos($imgData, ',') + 1);
                $type = strtolower($type[1]);
            } else {
                return response()->json(['success' => false, 'message' => 'Invalid image data'], 422);
            }

            $decodedImg = base64_decode($imgData);
            if ($decodedImg === false) {
                return response()->json(['success' => false, 'message' => 'Base64 decode failed'], 422);
            }

            \Illuminate\Support\Facades\Storage::disk('public')->put($image->image_path, $decodedImg);

            return response()->json([
                'success' => true,
                'message' => __('Image updated successfully.')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error: ') . $e->getMessage()
            ], 500);
        }
    }
}
