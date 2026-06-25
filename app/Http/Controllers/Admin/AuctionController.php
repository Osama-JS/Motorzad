<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Vehicle;
use App\Models\User;
use App\Services\AuctionService;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    public function __construct(protected AuctionService $auctionService) {}
    public function index()
    {
        $stats = [
            'total' => Auction::count(),
            'live' => Auction::where('status', 'live')->count(),
            'scheduled' => Auction::where('status', 'scheduled')->count(),
            'completed' => Auction::where('status', 'completed')->count(),
        ];
        
        $vehicles = Vehicle::all();
        $users = User::all();

        return view('admin.auctions.index', compact('stats', 'vehicles', 'users'));
    }

    public function getData(Request $request)
    {
        $query = Auction::with(['vehicle', 'creator', 'winner'])->latest();

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title_ar', 'like', "%{$request->search}%")
                  ->orWhere('title_en', 'like', "%{$request->search}%")
                  ->orWhereHas('vehicle', function($vq) use ($request) {
                      $vq->where('make', 'like', "%{$request->search}%")
                         ->orWhere('model', 'like', "%{$request->search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->per_page ?? 10;
        $auctions = $query->paginate($perPage);

        $data = [];
        foreach ($auctions as $auction) {
            $statusBadge = match($auction->status) {
                'live' => '<span class="badge bg-success text-white px-3 py-2 rounded-pill"><i class="fa-solid fa-circle me-1" style="font-size:0.5rem;"></i> '.__('Live').'</span>',
                'scheduled' => '<span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i class="fa-solid fa-clock me-1" style="font-size:0.8rem;"></i> '.__('Scheduled').'</span>',
                'completed', 'sold', 'ended' => '<span class="badge bg-primary text-white px-3 py-2 rounded-pill"><i class="fa-solid fa-check-circle me-1" style="font-size:0.8rem;"></i> '.__('Completed').'</span>',
                'cancelled' => '<span class="badge bg-danger text-white px-3 py-2 rounded-pill"><i class="fa-solid fa-times-circle me-1" style="font-size:0.8rem;"></i> '.__('Cancelled').'</span>',
                default => '<span class="badge bg-secondary text-white px-3 py-2 rounded-pill"><i class="fa-solid fa-circle me-1" style="font-size:0.5rem;"></i> '.__($auction->status).'</span>',
            };

            $imageUrl = $auction->image ? asset('storage/' . $auction->image) : ($auction->vehicle && $auction->vehicle->primary_image_url ? $auction->vehicle->primary_image_url : null);
            $imageHtml = $imageUrl 
                            ? '<img src="' . $imageUrl . '" width="60" style="border-radius:12px; object-fit:cover; height:60px; box-shadow:0 2px 5px rgba(0,0,0,0.1);" alt="">' 
                            : '<div style="width:60px;height:60px;background:#f8f9fa;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#adb5bd;font-size:24px;"><i class="fa-solid fa-car"></i></div>';

            $actions = '<div class="dropdown action-dropdown">
                <button class="btn btn-sm btn-icon border-0 shadow-none dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm py-2">
                    <li><a class="dropdown-item text-info" href="' . route('admin.auctions.show', $auction->id) . '"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>' . __('View') . '</a></li>
                    <li><a class="dropdown-item text-primary" href="' . route('admin.auctions.edit', $auction->id) . '"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>' . __('Edit') . '</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="deleteAuction(' . $auction->id . ')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>' . __('Delete') . '</a></li>
                </ul>
            </div>';

            $data[] = [
                'id' => $auction->id,
                'image' => $imageHtml,
                'image_url' => $imageUrl,
                'title' => '<strong>' . $auction->title . '</strong>',
                'raw_title' => $auction->title,
                'vehicle' => $auction->vehicle ? $auction->vehicle->title : 'N/A',
                'start_price' => '<span class="fw-bold text-success">' . number_format($auction->start_price, 2) . '</span>',
                'status' => $statusBadge,
                'start_time' => $auction->start_time ? '<span dir="ltr" class="text-muted"><i class="fa-regular fa-calendar-alt me-1"></i> ' . $auction->start_time->format('Y-m-d H:i') . '</span>' : '-',
                'end_time' => $auction->end_time ? '<span dir="ltr" class="text-muted"><i class="fa-regular fa-calendar-check me-1"></i> ' . $auction->end_time->format('Y-m-d H:i') . '</span>' : '-',
                'actions' => $actions
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => $auctions->total(),
                'current_page' => $auctions->currentPage(),
                'links' => $auctions->linkCollection()->toArray()
            ]
        ]);
    }

    public function show(Auction $auction)
    {
        $auction->load(['vehicle.images', 'creator', 'winner', 'bids' => function($q) {
            $q->with('user')->orderBy('amount', 'desc');
        }, 'deposits.user']);

        return view('admin.auctions.show', compact('auction'));
    }

    public function create()
    {
        $vehicles = Vehicle::all();
        return view('admin.auctions.create', compact('vehicles'));
    }

    public function edit(Auction $auction)
    {
        $vehicles = Vehicle::all();
        return view('admin.auctions.edit', compact('auction', 'vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'start_price' => 'required|numeric|min:0',
            'reserve_price' => 'nullable|numeric|min:0',
            'min_bid_increment' => 'required|numeric|min:1',
            'deposit_amount' => 'required|numeric|min:0',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'status' => 'required|in:draft,scheduled,live,completed,cancelled',
            'location_ar' => 'nullable|string|max:255',
            'location_en' => 'nullable|string|max:255',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'is_featured' => 'boolean',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'primary_image_index' => 'nullable|integer',
            'auto_extend_minutes' => 'nullable|integer|min:0'
        ]);

        $validated['deposit_required'] = $request->has('deposit_required');
        $validated['is_featured'] = $request->has('is_featured');

        $validated['created_by'] = auth()->id();

        $auction = Auction::create($validated);

        if ($request->hasFile('images')) {
            $primaryIndex = (int) $request->input('primary_image_index', 0);
            foreach ($request->file('images') as $index => $imageFile) {
                $path = $imageFile->store('auctions', 'public');
                $auction->images()->create([
                    'image_path' => $path,
                    'is_primary' => ($index == $primaryIndex),
                    'sort_order' => $index
                ]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Auction added successfully')
            ]);
        }

        return redirect()->route('admin.auctions.index')->with('success', __('Auction added successfully'));
    }

    public function update(Request $request, Auction $auction)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'start_price' => 'required|numeric|min:0',
            'reserve_price' => 'nullable|numeric|min:0',
            'min_bid_increment' => 'required|numeric|min:1',
            'deposit_amount' => 'required|numeric|min:0',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'status' => 'required|in:draft,scheduled,live,completed,cancelled',
            'location_ar' => 'nullable|string|max:255',
            'location_en' => 'nullable|string|max:255',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'is_featured' => 'boolean',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'deleted_images' => 'nullable|array',
            'deleted_images.*' => 'exists:auction_images,id',
            'auto_extend_minutes' => 'nullable|integer|min:0'
        ]);

        $validated['deposit_required'] = $request->has('deposit_required');
        $validated['is_featured'] = $request->has('is_featured');

        $auction->update($validated);

        // Handle deleted images
        if ($request->filled('deleted_images')) {
            $imagesToDelete = $auction->images()->whereIn('id', $request->deleted_images)->get();
            foreach ($imagesToDelete as $img) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($img->image_path);
                $img->delete();
            }
        }

        // Handle new images
        if ($request->hasFile('images')) {
            $existingCount = $auction->images()->count();
            foreach ($request->file('images') as $index => $imageFile) {
                $path = $imageFile->store('auctions', 'public');
                $auction->images()->create([
                    'image_path' => $path,
                    'is_primary' => ($existingCount === 0 && $index === 0),
                    'sort_order' => $existingCount + $index
                ]);
            }
            
            // Ensure there is at least one primary image if any images exist
            if (!$auction->primaryImage && $auction->images()->count() > 0) {
                $firstImage = $auction->images()->first();
                $firstImage->update(['is_primary' => true]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Auction updated successfully')
            ]);
        }

        return redirect()->route('admin.auctions.index')->with('success', __('Auction updated successfully'));
    }

    public function destroy(Auction $auction)
    {
        $auction->delete();

        return response()->json([
            'success' => true,
            'message' => __('Auction deleted successfully')
        ]);
    }

    public function pause(Auction $auction)
    {
        $auction->update(['is_paused' => true]);

        return response()->json([
            'success' => true,
            'message' => __('Auction paused successfully')
        ]);
    }

    public function resume(Auction $auction)
    {
        $auction->update(['is_paused' => false]);

        return response()->json([
            'success' => true,
            'message' => __('Auction resumed successfully')
        ]);
    }

    public function extend(Request $request, Auction $auction)
    {
        $request->validate([
            'minutes' => 'required|integer|min:1|max:1440'
        ]);

        $minutes = (int)$request->minutes;
        $endTime = $auction->end_time;

        // If the auction has already ended, we extend it relative to now()
        if ($endTime->isPast()) {
            $newEndTime = now()->addMinutes($minutes);
        } else {
            $newEndTime = $endTime->addMinutes($minutes);
        }

        $auction->update([
            'end_time' => $newEndTime,
            // If the auction is ended, we can also restore it to live status
            'status' => $auction->status === 'ended' || $auction->status === 'cancelled' ? 'live' : $auction->status
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Auction extended successfully'),
            'new_end_time' => $newEndTime->format('Y-m-d H:i:s')
        ]);
    }

    public function forceEnd(Request $request, Auction $auction)
    {
        $request->validate([
            'action' => 'required|in:complete,cancel'
        ]);

        if ($request->action === 'complete') {
            // End the auction immediately (awarding it to the highest bidder if reserve met)
            // Note: to bypass status check in endAuction, we ensure status is 'live'
            if ($auction->status !== 'live') {
                $auction->update(['status' => 'live']);
            }
            $this->auctionService->endAuction($auction);

            return response()->json([
                'success' => true,
                'message' => __('Auction ended and sold process completed.')
            ]);
        } else {
            // Cancel the auction and refund all deposits
            $this->auctionService->cancelAuction($auction);

            return response()->json([
                'success' => true,
                'message' => __('Auction cancelled and deposits refunded successfully.')
            ]);
        }
    }

    public function blockUser(Request $request, Auction $auction)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:auction,global'
        ]);

        $userId = $request->user_id;

        if ($request->type === 'global') {
            // Globally suspend the user by setting status to rejected
            $user = User::find($userId);
            $user->update(['status' => 'rejected']);

            return response()->json([
                'success' => true,
                'message' => __('User suspended globally successfully.')
            ]);
        } else {
            // Block from this auction specifically by inserting into blocklist
            \Illuminate\Support\Facades\DB::table('auction_blocklists')->insertOrIgnore([
                'auction_id' => $auction->id,
                'user_id' => $userId,
                'blocked_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => __('User blocked from this auction specifically.')
            ]);
        }
    }

    public function analytics()
    {
        // Total commissions
        $totalCommissions = Auction::where('status', 'sold')->sum('commission_amount');

        // Total sold auctions
        $soldCount = Auction::where('status', 'sold')->count();

        // Total ended/cancelled
        $endedCount = Auction::where('status', 'ended')->count();
        $cancelledCount = Auction::where('status', 'cancelled')->count();
        $totalEnded = $endedCount + $cancelledCount;

        // Average commission
        $avgCommission = $soldCount > 0 ? $totalCommissions / $soldCount : 0;

        // Monthly commission growth for the current year
        $currentYear = now()->year;
        $monthlyCommissions = \Illuminate\Support\Facades\DB::table('auctions')
            ->where('status', 'sold')
            ->whereYear('sold_at', $currentYear)
            ->selectRaw('MONTH(sold_at) as month, SUM(commission_amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        // Prepare monthly data for all 12 months (defaulting to 0 if no sales)
        $monthsData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthsData[$m] = $monthlyCommissions[$m] ?? 0;
        }

        // Recent sales
        $recentSales = Auction::where('status', 'sold')
            ->with(['winner', 'vehicle'])
            ->orderByDesc('sold_at')
            ->limit(5)
            ->get();

        return view('admin.auctions.analytics', compact(
            'totalCommissions',
            'soldCount',
            'endedCount',
            'cancelledCount',
            'totalEnded',
            'avgCommission',
            'monthsData',
            'recentSales'
        ));
    }

    public function exportReport(Request $request)
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=auctions_financial_report.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            // Add BOM for Excel Arabic characters
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, [
                __('Auction ID'),
                __('Title'),
                __('Winner'),
                __('Winning Bid (SAR)'),
                __('Commission Rate (%)'),
                __('Commission Amount (SAR)'),
                __('Sold Date')
            ]);

            $auctions = Auction::where('status', 'sold')
                ->with(['winner'])
                ->orderByDesc('sold_at')
                ->get();

            foreach ($auctions as $auction) {
                fputcsv($file, [
                    $auction->id,
                    $auction->title,
                    $auction->winner?->name ?? 'N/A',
                    number_format($auction->winning_bid_amount, 2),
                    $auction->commission_rate . '%',
                    number_format($auction->commission_amount, 2),
                    $auction->sold_at ? $auction->sold_at->format('Y-m-d H:i') : 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
