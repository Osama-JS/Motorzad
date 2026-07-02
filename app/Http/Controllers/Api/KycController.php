<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KycRequestResource;
use App\Models\KycRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KycController extends Controller
{
    /**
     * Get current user's KYC status.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $kycRequest = $user->latestKycRequest;

        return response()->json([
            'success'   => true,
            'kyc_level' => (int) $user->kyc_level,
            'status'    => $user->status,
            'data'      => $kycRequest ? new KycRequestResource($kycRequest) : null,
        ]);
    }

    /**
     * Submit a new KYC request.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        // Prevent duplicate pending submissions
        if ($user->kycRequests()->where('status', 'pending')->exists()) {
            return response()->json([
                'success' => false,
                'message' => __('You already have a pending KYC request under review.'),
            ], 422);
        }

        $request->validate([
            'full_name'    => 'required|string|max:255',
            'country'      => 'required|string|max:100',
            'id_number'    => 'required|string|max:50',
            'id_image'     => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'selfie_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $idPath     = $request->file('id_image')->store('kyc', 'public');
        $selfiePath = $request->file('selfie_image')->store('kyc', 'public');

        $kycRequest = KycRequest::create([
            'user_id'      => $user->id,
            'full_name'    => $request->full_name,
            'country'      => $request->country,
            'id_number'    => $request->id_number,
            'id_image'     => $idPath,
            'selfie_image' => $selfiePath,
            'status'       => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => __('KYC request submitted successfully. It will be reviewed soon.'),
            'data'    => new KycRequestResource($kycRequest),
        ], 201);
    }

    /**
     * Get user's KYC history.
     */
    public function history(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = $request->query('per_page', 10);

        $kycRequests = $user->kycRequests()->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => KycRequestResource::collection($kycRequests->items()),
            'pagination' => [
                'total'        => $kycRequests->total(),
                'per_page'     => $kycRequests->perPage(),
                'current_page' => $kycRequests->currentPage(),
                'last_page'    => $kycRequests->lastPage(),
            ],
        ]);
    }
}
