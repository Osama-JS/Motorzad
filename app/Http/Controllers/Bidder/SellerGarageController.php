<?php

namespace App\Http\Controllers\Bidder;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Auction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SellerGarageController extends Controller
{
    /**
     * Display the seller's submitted vehicles (My Listings).
     */
    public function index()
    {
        $user = auth()->user();
        
        if (!$user->hasRole('seller')) {
            abort(403, 'Unauthorized action.');
        }

        $vehicles = Vehicle::with('images')
            ->where('submitted_by', $user->id)
            ->whereIn('status', ['pending', 'approved', 'rejected'])
            ->latest()
            ->paginate(10);
            
        $stats = [
            'total' => Vehicle::where('submitted_by', $user->id)->whereIn('status', ['pending', 'approved', 'rejected'])->count(),
            'pending' => Vehicle::where('submitted_by', $user->id)->where('status', 'pending')->count(),
            'approved' => Vehicle::where('submitted_by', $user->id)->where('status', 'approved')->count(),
            'rejected' => Vehicle::where('submitted_by', $user->id)->where('status', 'rejected')->count(),
            'draft' => Vehicle::where('submitted_by', $user->id)->where('status', 'draft')->count(),
        ];

        return view('bidder.garage.index', compact('vehicles', 'stats'));
    }

    /**
     * Display the seller's draft vehicles.
     */
    public function drafts()
    {
        $user = auth()->user();

        if (!$user->hasRole('seller')) {
            abort(403, 'Unauthorized action.');
        }

        $vehicles = Vehicle::with('images')
            ->where('submitted_by', $user->id)
            ->where('status', 'draft')
            ->latest()
            ->paginate(10);

        return view('bidder.garage.drafts', compact('vehicles'));
    }

    /**
     * Show the form for creating a new vehicle.
     */
    public function create()
    {
        $user = auth()->user();

        if (!$user->hasRole('seller')) {
            abort(403, 'Unauthorized action.');
        }

        return view('bidder.garage.create');
    }

    /**
     * Store a newly created vehicle in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->hasRole('seller')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'make_ar' => 'required|string|max:100',
            'make_en' => 'required|string|max:100',
            'model_ar' => 'required|string|max:100',
            'model_en' => 'required|string|max:100',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'color_ar' => 'nullable|string|max:50',
            'color_en' => 'nullable|string|max:50',
            'vin_number' => 'nullable|string|max:50', // removed unique constraint for draft saving updates
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
            'action' => 'required|in:draft,submit', // to distinguish between save as draft or submit for review
        ]);

        $status = $request->action === 'submit' ? 'pending' : 'draft';
        $validated['status'] = $status;
        $validated['submitted_by'] = $user->id;

        // Parse damage points JSON back to array
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
                \App\Models\VehicleImage::create([
                    'vehicle_id' => $vehicle->id,
                    'image_path' => $path,
                    'is_primary' => ($index === $primaryIndex),
                    'sort_order' => $index
                ]);
            }
        }

        $message = $status === 'pending' 
            ? __('Vehicle submitted successfully. It is now pending admin approval.')
            : __('Vehicle saved as draft successfully.');

        return redirect()->route('bidder.garage.' . ($status === 'pending' ? 'index' : 'drafts'))
            ->with('success', $message);
    }

    /**
     * Decode VIN number and return vehicle details.
     */
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

    /**
     * Generate smart description (Mock AI / Smart Template Engine)
     */
    public function generateDescription(Request $request)
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

        return response()->json([
            'success' => true,
            'description_ar' => $descriptionAr,
            'description_en' => $descriptionEn
        ]);
    }

    /**
     * Auto-save vehicle drafts
     */
    public function autoSave(Request $request)
    {
        $user = auth()->user();

        if (!$user->hasRole('seller')) {
            return response()->json(['success' => false], 403);
        }

        $validated = $request->except(['_token', 'images']);
        $validated['status'] = 'draft';
        $validated['submitted_by'] = $user->id;

        if (!empty($validated['damage_points'])) {
            $validated['damage_points'] = json_decode($validated['damage_points'], true);
        }

        // If vehicle_id is provided, update it. Otherwise create a new draft.
        if (!empty($validated['vehicle_id'])) {
            $vehicle = Vehicle::where('id', $validated['vehicle_id'])
                ->where('submitted_by', $user->id)
                ->first();
                
            if ($vehicle) {
                $vehicle->update($validated);
                return response()->json(['success' => true, 'vehicle_id' => $vehicle->id, 'action' => 'updated']);
            }
        }

        // Generate a placeholder title if it's not provided yet (since make/model might be empty initially)
        if (empty($validated['make_en']) && empty($validated['model_en'])) {
            $validated['make_en'] = 'Draft';
            $validated['model_en'] = 'Vehicle';
        }

        $vehicle = Vehicle::create($validated);
        return response()->json(['success' => true, 'vehicle_id' => $vehicle->id, 'action' => 'created']);
    }

    /**
     * Show the form for creating an auction for an approved vehicle.
     */
    public function createAuction($vehicle_id)
    {
        $user = auth()->user();

        // Ensure the vehicle belongs to the seller and is approved
        $vehicle = Vehicle::where('id', $vehicle_id)
            ->where('submitted_by', $user->id)
            ->where('status', 'approved')
            ->firstOrFail();

        // Check if an auction already exists
        if (Auction::where('vehicle_id', $vehicle->id)->exists()) {
            return redirect()->route('bidder.garage.index')->with('error', __('تم إنشاء طلب مزاد لهذه السيارة مسبقاً.'));
        }

        return view('bidder.garage.auction_create', compact('vehicle'));
    }

    /**
     * Store the auction proposed by the seller.
     */
    public function storeAuction(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_price' => 'required|numeric|min:0',
            'reserve_price' => 'nullable|numeric|gte:start_price',
            'buy_now_price' => 'nullable|numeric|gte:reserve_price',
            'start_time' => 'required|date|after_or_equal:today',
            'end_time' => 'required|date|after:start_time',
            'bidding_mode' => 'required|in:open,strict',
        ]);

        $vehicle = Vehicle::where('id', $validated['vehicle_id'])
            ->where('submitted_by', $user->id)
            ->where('status', 'approved')
            ->firstOrFail();

        if (Auction::where('vehicle_id', $vehicle->id)->exists()) {
            return redirect()->route('bidder.garage.index')->with('error', __('تم إنشاء طلب مزاد لهذه السيارة مسبقاً.'));
        }

        // Determine deposit amount based on mode
        $deposit_required = $validated['bidding_mode'] === 'strict';
        $deposit_amount = $deposit_required ? ($validated['start_price'] * 0.10) : 0; // 10% for strict mode

        $auction = Auction::create([
            'vehicle_id' => $vehicle->id,
            'created_by' => $user->id,
            'title_ar' => 'مزاد: ' . $vehicle->make_ar . ' ' . $vehicle->model_ar . ' ' . $vehicle->year,
            'title_en' => 'Auction: ' . $vehicle->make_en . ' ' . $vehicle->model_en . ' ' . $vehicle->year,
            'description_ar' => $vehicle->description_ar,
            'description_en' => $vehicle->description_en,
            'start_price' => $validated['start_price'],
            'reserve_price' => $validated['reserve_price'],
            'buy_now_price' => $validated['buy_now_price'],
            'start_time' => Carbon::parse($validated['start_time']),
            'end_time' => Carbon::parse($validated['end_time']),
            'deposit_required' => $deposit_required,
            'deposit_amount' => $deposit_amount,
            'status' => 'draft', // Needs Admin Approval to move to 'scheduled'
        ]);

        return redirect()->route('bidder.garage.index')->with('success', __('تم إنشاء المزاد بنجاح وهو الآن بانتظار جدولة الإدارة.'));
    }
}
