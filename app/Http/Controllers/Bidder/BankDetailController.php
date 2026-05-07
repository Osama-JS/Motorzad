<?php

namespace App\Http\Controllers\Bidder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BankDetailController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('bidder.bank-details.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'iban' => 'nullable|string|max:255',
            'bic_code' => 'nullable|string|max:255',
            'beneficiary_name' => 'nullable|string|max:255',
            'address_1' => 'nullable|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'bank_city' => 'nullable|string|max:255',
            'bank_country' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
        ]);

        // When user updates bank details, reset check_bank to false (unverified)
        $validated['check_bank'] = false;

        $user->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Bank details updated successfully.'),
                'user' => $user
            ]);
        }

        return redirect()->back()->with('success', __('Bank details updated successfully.'));
    }
}
