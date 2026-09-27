<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\Models\Auction;
use App\Models\Bid;

class AccountDeletionController extends Controller
{
    /**
     * Display the account deletion form (used for Webview and Google Play link).
     */
    public function show()
    {
        return view('profile.account-deletion');
    }

    /**
     * Handle the account deletion request.
     */
    public function destroy(Request $request)
    {
        // Require password confirmation to delete the account
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Business Logic Validations

        // 1. Check wallet balance
        if ($user->wallet && $user->wallet->balance > 0) {
            return redirect()->back()->withErrors(['error' => __('لا يمكن حذف الحساب لوجود رصيد متاح في المحفظة. يرجى سحب الرصيد أولاً.')]);
        }

        // 2. Check active or scheduled auctions
        $hasActiveAuctions = Auction::where('created_by', $user->id)
            ->whereIn('status', ['live', 'scheduled'])
            ->exists();
            
        if ($hasActiveAuctions) {
            return redirect()->back()->withErrors(['error' => __('لا يمكن حذف الحساب لوجود مزادات نشطة أو مجدولة.')]);
        }

        // 3. Check active bids
        $hasActiveBids = Bid::where('user_id', $user->id)
            ->active()
            ->exists();
            
        if ($hasActiveBids) {
            return redirect()->back()->withErrors(['error' => __('لا يمكن حذف الحساب لوجود مزايدات نشطة لك في مزادات حالية.')]);
        }

        // Proceed with soft deletion
        $user->is_deleted = true;
        $user->save();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('success', __('تم حذف حسابك بنجاح.'));
    }
}
