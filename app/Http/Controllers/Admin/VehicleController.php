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
                    'approved' => '<span class="badge badge-success">معتمدة</span>',
                    'pending' => '<span class="badge badge-warning">قيد المراجعة</span>',
                    'rejected' => '<span class="badge badge-danger">مرفوضة</span>',
                    default => '<span class="badge badge-secondary">'.$vehicle->status.'</span>',
                };

                return [
                    'id' => $vehicle->id,
                    'image' => $vehicle->primary_image_url 
                                ? '<img src="' . $vehicle->primary_image_url . '" width="50" style="border-radius:8px; object-fit:cover; height:50px;" alt="">' 
                                : '<div style="width:50px;height:50px;background:#eee;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#999;font-size:10px;">لا توجد</div>',
                    'title' => '<strong>' . $vehicle->title . '</strong>',
                    'vin_number' => $vehicle->vin_number ?? 'N/A',
                    'status' => $statusBadge,
                    'actions' => '
                        <div class="actions-cell" style="display:flex; gap:5px; justify-content:center;">
                            <button onclick="editVehicle(' . $vehicle->id . ')" class="btn-icon-only edit" title="تعديل" style="background:var(--primary); color:white; border:none; padding:5px 8px; border-radius:4px; cursor:pointer;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                            <button onclick="deleteVehicle(' . $vehicle->id . ')" class="btn-icon-only delete" title="حذف" style="background:#ef4444; color:white; border:none; padding:5px 8px; border-radius:4px; cursor:pointer;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                        </div>'
                ];
            })
        ]);
    }

    public function show(Vehicle $vehicle)
    {
        return response()->json([
            'success' => true,
            'vehicle' => $vehicle
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'make' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:50',
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
        ]);

        $validated['submitted_by'] = auth()->id();
        if ($validated['status'] !== 'pending') {
            $validated['reviewed_by'] = auth()->id();
            $validated['reviewed_at'] = now();
        }

        Vehicle::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المركبة بنجاح'
        ]);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'make' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color' => 'nullable|string|max:50',
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
        ]);

        if ($validated['status'] !== $vehicle->status) {
            $validated['reviewed_by'] = auth()->id();
            $validated['reviewed_at'] = now();
        }

        $vehicle->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المركبة بنجاح'
        ]);
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المركبة بنجاح'
        ]);
    }
}
