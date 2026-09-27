<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo = $path;
        }

        $user->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Profile updated successfully.'),
                'user' => [
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'profile_photo_url' => $user->profile_photo_url,
                ]
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // 1. Check wallet balance
        if ($user->wallet && $user->wallet->balance > 0) {
            return Redirect::back()->withErrors(['userDeletion' => __('لا يمكن حذف الحساب لوجود رصيد متاح في المحفظة. يرجى سحب الرصيد أولاً.')]);
        }

        // 2. Check active or scheduled auctions
        $hasActiveAuctions = \App\Models\Auction::where('created_by', $user->id)
            ->whereIn('status', ['live', 'scheduled'])
            ->exists();
            
        if ($hasActiveAuctions) {
            return Redirect::back()->withErrors(['userDeletion' => __('لا يمكن حذف الحساب لوجود مزادات نشطة أو مجدولة.')]);
        }

        // 3. Check active bids
        $hasActiveBids = \App\Models\Bid::where('user_id', $user->id)
            ->active()
            ->exists();
            
        if ($hasActiveBids) {
            return Redirect::back()->withErrors(['userDeletion' => __('لا يمكن حذف الحساب لوجود مزايدات نشطة لك في مزادات حالية.')]);
        }

        $user->is_deleted = true;
        $user->save();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
