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
        
        return view('bidder.seller-subscription.index', compact('user', 'isSeller'));
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

        // 3. Upgrade to seller role
        $user->assignRole('seller');

        return redirect()->back()->with('success', __('Congratulations! Your account has been upgraded to a Seller. Seller features will be available soon.'));
    }
}
