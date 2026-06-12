<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
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
            'location' => 'nullable|string|max:255',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'is_featured' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Default booleans if not present
        if (!isset($validated['deposit_required'])) $validated['deposit_required'] = false;
        if (!isset($validated['is_featured'])) $validated['is_featured'] = false;

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
            'location' => 'nullable|string|max:255',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'is_featured' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
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
}
