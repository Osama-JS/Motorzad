<?php

namespace App\Http\Controllers\Bidder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SellerSubscriptionController extends Controller
{
    /**
     * Display the become a seller page.
     */
    public function index()
    {
        $user = auth()->user();
        
        // If they are already a seller
        $isSeller = $user->hasRole('seller');
        
        $pendingRequest = \App\Models\SellerRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();
            
        $rejectedRequest = \App\Models\SellerRequest::where('user_id', $user->id)
            ->where('status', 'rejected')
            ->first();
        
        return view('bidder.seller-subscription.index', compact('user', 'isSeller', 'pendingRequest', 'rejectedRequest'));
    }

    /**
     * Handle the upgrade request.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // 1. Check if they are already a seller
        if ($user->hasRole('seller')) {
            return redirect()->back()->with('success', __('You are already a seller.'));
        }

        // 2. Check if they have completed KYC
        if ($user->status !== 'approved') {
            return redirect()->route('kyc.index')
                ->with('error', __('Please complete identity verification to become a seller.'));
        }
        
        // 3. Check if there is already a pending request
        $pendingRequest = \App\Models\SellerRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();
            
        if ($pendingRequest) {
            return redirect()->back()->with('info', __('Your request to become a seller is already pending approval.'));
        }

        // 4. Create a request
        \App\Models\SellerRequest::create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', __('Your request to become a seller has been submitted successfully and is awaiting admin approval.'));
    }
}
