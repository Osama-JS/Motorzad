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
                    'live' => '<span class="badge badge-success">Live</span>',
                    'scheduled' => '<span class="badge badge-warning">Scheduled</span>',
                    'completed' => '<span class="badge badge-info">Completed</span>',
                    'cancelled' => '<span class="badge badge-danger">Cancelled</span>',
                    default => '<span class="badge badge-secondary">'.$auction->status.'</span>',
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
                        <div class="actions-cell" style="display:flex; gap:5px; justify-content:center;">
                            <button onclick="editAuction(' . $auction->id . ')" class="btn-icon-only edit" title="' . __('Edit') . '" style="background:var(--primary); color:white; border:none; padding:5px 8px; border-radius:4px; cursor:pointer;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                            <button onclick="deleteAuction(' . $auction->id . ')" class="btn-icon-only delete" title="' . __('Delete') . '" style="background:#ef4444; color:white; border:none; padding:5px 8px; border-radius:4px; cursor:pointer;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                        </div>'
                ];
            })
        ]);
    }

    public function show(Auction $auction)
    {
        return response()->json([
            'success' => true,
            'auction' => $auction
        ]);
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

        return response()->json([
            'success' => true,
            'message' => __('Auction added successfully')
        ]);
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

        return response()->json([
            'success' => true,
            'message' => __('Auction updated successfully')
        ]);
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
