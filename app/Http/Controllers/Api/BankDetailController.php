<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankDetailController extends Controller
{
    /**
     * Get the current user's bank details.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data'    => [
                'iban'             => $user->iban,
                'bic_code'         => $user->bic_code,
                'beneficiary_name' => $user->beneficiary_name,
                'bank_name'        => $user->bank_name,
                'account_number'   => $user->account_number,
                'address_1'        => $user->address_1,
                'address_2'        => $user->address_2,
                'bank_city'        => $user->bank_city,
                'bank_country'     => $user->bank_country,
                'is_verified'      => (bool) $user->check_bank,
            ],
        ]);
    }

    /**
     * Update the current user's bank details.
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'iban'             => 'nullable|string|max:255',
            'bic_code'         => 'nullable|string|max:255',
            'beneficiary_name' => 'nullable|string|max:255',
            'bank_name'        => 'nullable|string|max:255',
            'account_number'   => 'nullable|string|max:255',
            'address_1'        => 'nullable|string|max:255',
            'address_2'        => 'nullable|string|max:255',
            'bank_city'        => 'nullable|string|max:255',
            'bank_country'     => 'nullable|string|max:255',
        ]);

        // Reset verification when bank details change
        $validated['check_bank'] = false;

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('Bank details updated successfully.'),
        ]);
    }
}
