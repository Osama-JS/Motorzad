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
        $auctions = Auction::with(['vehicle', 'creator', 'winner'])->latest()->get();

        return response()->json([
            'data' => $auctions->map(function($auction) {
                $statusBadge = match($auction->status) {
                    'live' => '<span class="status-indicator status-live" style="background:#dcfce7; color:#15803d; padding:6px 12px; border-radius:50px; font-weight:600; font-size:0.8rem; display:inline-flex; align-items:center; gap:6px;"><i class="fa-solid fa-circle" style="font-size:0.5rem;"></i> '.__('Live').'</span>',
                    'scheduled' => '<span class="status-indicator status-scheduled" style="background:#fef3c7; color:#b45309; padding:6px 12px; border-radius:50px; font-weight:600; font-size:0.8rem; display:inline-flex; align-items:center; gap:6px;"><i class="fa-solid fa-circle" style="font-size:0.5rem;"></i> '.__('Scheduled').'</span>',
                    'completed', 'sold', 'ended' => '<span class="status-indicator status-completed" style="background:#e0f2fe; color:#0369a1; padding:6px 12px; border-radius:50px; font-weight:600; font-size:0.8rem; display:inline-flex; align-items:center; gap:6px;"><i class="fa-solid fa-circle" style="font-size:0.5rem;"></i> '.__('Completed').'</span>',
                    'cancelled' => '<span class="status-indicator status-cancelled" style="background:#fee2e2; color:#b91c1c; padding:6px 12px; border-radius:50px; font-weight:600; font-size:0.8rem; display:inline-flex; align-items:center; gap:6px;"><i class="fa-solid fa-circle" style="font-size:0.5rem;"></i> '.__('Cancelled').'</span>',
                    default => '<span class="status-indicator status-draft" style="background:#f1f5f9; color:#475569; padding:6px 12px; border-radius:50px; font-weight:600; font-size:0.8rem; display:inline-flex; align-items:center; gap:6px;"><i class="fa-solid fa-circle" style="font-size:0.5rem;"></i> '.__($auction->status).'</span>',
                };

                $imageUrl = $auction->image ? asset('storage/' . $auction->image) : ($auction->vehicle && $auction->vehicle->primary_image_url ? $auction->vehicle->primary_image_url : null);
                $imageHtml = $imageUrl 
                                ? '<img src="' . $imageUrl . '" width="50" style="border-radius:8px; object-fit:cover; height:50px;" alt="">' 
                                : '<div style="width:50px;height:50px;background:#eee;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#999;font-size:10px;">' . __('No Image') . '</div>';

                return [
                    'id' => $auction->id,
                    'image' => $imageHtml,
                    'title' => '<strong>' . $auction->title . '</strong>',
                    'vehicle' => $auction->vehicle ? $auction->vehicle->title : 'N/A',
                    'start_price' => number_format($auction->start_price, 2),
                    'status' => $statusBadge,
                    'start_time' => $auction->start_time ? $auction->start_time->format('Y-m-d H:i') : '-',
                    'end_time' => $auction->end_time ? $auction->end_time->format('Y-m-d H:i') : '-',
                    'actions' => '
                        <div class="actions-cell" style="display:flex; gap:6px; justify-content:center; align-items:center;">
                            <a href="' . route('admin.auctions.show', $auction->id) . '" class="btn btn-sm text-white d-inline-flex align-items-center gap-1 px-3 py-1.5 rounded-pill" style="background:#0ea5e9; border:none; font-size:0.8rem; font-weight:700; transition:all 0.2s;" title="' . __('View') . '"><i class="fa-solid fa-eye" style="font-size:0.75rem;"></i> ' . __('View') . '</a>
                            <a href="' . route('admin.auctions.edit', $auction->id) . '" class="btn btn-sm text-white d-inline-flex align-items-center gap-1 px-3 py-1.5 rounded-pill" style="background:var(--primary); border:none; font-size:0.8rem; font-weight:700; transition:all 0.2s;" title="' . __('Edit') . '"><i class="fa-solid fa-pen-to-square" style="font-size:0.75rem;"></i> ' . __('Edit') . '</a>
                            <button onclick="deleteAuction(' . $auction->id . ')" class="btn btn-sm text-white d-inline-flex align-items-center gap-1 px-3 py-1.5 rounded-pill" style="background:#ef4444; border:none; font-size:0.8rem; font-weight:700; transition:all 0.2s;" title="' . __('Delete') . '"><i class="fa-solid fa-trash" style="font-size:0.75rem;"></i> ' . __('Delete') . '</button>
                        </div>'
                ];
            })
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'auto_extend_minutes' => 'nullable|integer|min:0'
        ]);

        $validated['deposit_required'] = $request->has('deposit_required');
        $validated['is_featured'] = $request->has('is_featured');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('auctions', 'public');
        }

        $validated['created_by'] = auth()->id();

        Auction::create($validated);

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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'auto_extend_minutes' => 'nullable|integer|min:0'
        ]);

        $validated['deposit_required'] = $request->has('deposit_required');
        $validated['is_featured'] = $request->has('is_featured');

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($auction->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($auction->image);
            }
            $validated['image'] = $request->file('image')->store('auctions', 'public');
        }

        $auction->update($validated);

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
