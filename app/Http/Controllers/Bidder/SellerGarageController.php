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
    public function create(Request $request)
    {
        $user = auth()->user();

        if (!$user->hasRole('seller')) {
            abort(403, 'Unauthorized action.');
        }

        $vehicle = null;
        if ($request->has('id')) {
            $vehicle = Vehicle::with('images')->where('id', $request->id)
                ->where('submitted_by', $user->id)
                ->first();
        }

        return view('bidder.garage.create', compact('vehicle'));
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
            'year' => 'required|integer|min:1901|max:' . (date('Y') + 1),
            'color_ar' => 'nullable|string|max:50',
            'color_en' => 'nullable|string|max:50',
            'vin_number' => 'nullable|string|max:50', // removed unique constraint for draft saving updates
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
            'action' => 'required|in:draft,submit', // to distinguish between save as draft or submit for review
        ]);

        $status = $request->action === 'submit' ? 'approved' : 'draft';
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

        $message = $status === 'approved' 
            ? __('تم إضافة السيارة واعتمادها بنجاح وهي جاهزة لإنشاء مزاد لها.')
            : __('Vehicle saved as draft successfully.');

        return redirect()->route('bidder.garage.' . ($status === 'approved' ? 'index' : 'drafts'))
            ->with('success', $message);
    }

    /**
     * Show the form for editing an existing vehicle.
     */
    public function edit($id)
    {
        $user = auth()->user();

        if (!$user->hasRole('seller')) {
            abort(403, 'Unauthorized action.');
        }

        $vehicle = Vehicle::with('images')->where('id', $id)
            ->where('submitted_by', $user->id)
            ->firstOrFail();

        $isEdit = true;
        return view('bidder.garage.create', compact('vehicle', 'isEdit'));
    }

    /**
     * Update the specified vehicle in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user->hasRole('seller')) {
            abort(403, 'Unauthorized action.');
        }

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
            'existing_images' => 'nullable|string',
            'new_images_order' => 'nullable|string',
        ]);

        // If the user wants to submit for approval, update status. Otherwise keep it.
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
            $imagesToDelete = \App\Models\VehicleImage::where('vehicle_id', $vehicle->id)
                ->whereNotIn('id', $keptExistingImages)
                ->get();
            
            foreach($imagesToDelete as $delImg) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($delImg->image_path);
                $delImg->delete();
            }

            // Update sort order for kept existing images
            foreach ($existingImagesOrder as $img) {
                \App\Models\VehicleImage::where('id', $img['serverId'])->update(['sort_order' => $img['order'], 'is_primary' => false]);
            }
        } else {
            // Delete all existing images if none kept
            $allImages = \App\Models\VehicleImage::where('vehicle_id', $vehicle->id)->get();
            foreach($allImages as $delImg) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($delImg->image_path);
                $delImg->delete();
            }
        }

        // Handle new images
        if ($request->hasFile('images')) {
            $newImagesOrder = json_decode($validated['new_images_order'], true) ?? [];
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('vehicles', 'public');
                $sortOrder = $newImagesOrder[$index]['order'] ?? $index + 999;
                
                \App\Models\VehicleImage::create([
                    'vehicle_id' => $vehicle->id,
                    'image_path' => $path,
                    'is_primary' => false,
                    'sort_order' => $sortOrder
                ]);
            }
        }

        // Set primary image
        $primaryIndex = (int) $request->input('primary_image_index', 0);
        $primaryImage = \App\Models\VehicleImage::where('vehicle_id', $vehicle->id)
            ->where('sort_order', $primaryIndex)
            ->first();
            
        if ($primaryImage) {
            $primaryImage->update(['is_primary' => true]);
        } elseif (\App\Models\VehicleImage::where('vehicle_id', $vehicle->id)->exists()) {
            \App\Models\VehicleImage::where('vehicle_id', $vehicle->id)->orderBy('sort_order')->first()->update(['is_primary' => true]);
        }

        return redirect()->route('bidder.garage.' . ($status === 'approved' ? 'index' : 'drafts'))
            ->with('success', __('تم تعديل بيانات السيارة بنجاح.'));
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

        // Clamp year to 1901 to satisfy MySQL YEAR column constraints
        if (empty($validated['year']) || !is_numeric($validated['year']) || $validated['year'] < 1901) {
            $validated['year'] = 1901;
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
        if (empty($validated['make_en']) || empty($validated['model_en'])) {
            $validated['make_en'] = $validated['make_en'] ?: 'Draft';
            $validated['model_en'] = $validated['model_en'] ?: 'Vehicle';
        }
        if (empty($validated['make_ar']) || empty($validated['model_ar'])) {
            $validated['make_ar'] = $validated['make_ar'] ?: 'مسودة';
            $validated['model_ar'] = $validated['model_ar'] ?: 'مركبة';
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
            ->firstOrFail();

        if (Auction::where('vehicle_id', $vehicle->id)->exists()) {
            return redirect()->route('bidder.garage.index')->with('error', __('تم إنشاء طلب مزاد لهذه السيارة مسبقاً.'));
        }

        // Determine deposit amount based on mode
        $deposit_required = $validated['bidding_mode'] === 'strict';
        $deposit_amount = $deposit_required ? ($validated['start_price'] * 0.10) : 0; // 10% for strict mode

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
            'status' => $status, // Auto-approved
        ]);

        // إرسال إشعار للمزايدين عند إضافة مزاد جديد
        $bidders = \App\Models\User::role('bidder')->where('id', '!=', $user->id)->get();
        if ($bidders->isNotEmpty()) {
            \Illuminate\Support\Facades\Notification::send($bidders, new \App\Notifications\GeneralNotification(
                'مزاد جديد متاح!',
                'تم إضافة مزاد جديد: ' . $auction->title_ar . '. سارع بالمزايدة الآن!',
                ['database'],
                url('/bidder/auctions/' . $auction->id)
            ));
        }

        return redirect()->route('bidder.garage.index')->with('success', __('تم إنشاء المزاد واعتماده بنجاح.'));
    }

    /**
     * Show the form for editing an auction.
     */
    public function editAuction($id)
    {
        $user = auth()->user();

        // Ensure the auction belongs to the seller and is in a state that can be edited (e.g. not ended)
        $auction = Auction::where('id', $id)
            ->where('created_by', $user->id)
            ->whereNotIn('status', ['ended', 'completed', 'cancelled'])
            ->firstOrFail();

        $vehicle = $auction->vehicle;
        $isEdit = true;

        return view('bidder.garage.auction_create', compact('auction', 'vehicle', 'isEdit'));
    }

    /**
     * Update the auction in storage.
     */
    public function updateAuction(Request $request, $id)
    {
        $user = auth()->user();

        $auction = Auction::where('id', $id)
            ->where('created_by', $user->id)
            ->whereNotIn('status', ['ended', 'completed', 'cancelled'])
            ->firstOrFail();

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
        // If it was already live and they edit it, we might just keep it live.
        if ($auction->status === 'live' && !$startTime->isPast()) {
            $status = 'scheduled'; // If they pushed start time to future
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

        return redirect()->route('bidder.garage.index')->with('success', __('تم تعديل المزاد بنجاح.'));
    }
}
