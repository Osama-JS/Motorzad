<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class SellerSubscriptionController extends Controller
{
    use ApiResponse;

    /**
     * Get Seller Subscription Status
     * 
     * Returns the current status of the user's seller subscription request.
     */
    #[OA\Get(
        path: '/api/seller-subscription/status',
        summary: 'Get Seller Subscription Status',
        description: 'Returns the current status of the user\'s seller subscription request (seller, pending, rejected, or none).',
        security: [['bearerAuth' => []]],
        tags: ['Seller'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Current status retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object', properties: [
                            new OA\Property(property: 'status', type: 'string', example: 'pending', description: 'Can be: seller, pending, rejected, none'),
                            new OA\Property(property: 'admin_notes', type: 'string', nullable: true, example: 'Identity proof is not clear.', description: 'Present if status is rejected')
                        ])
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function status(Request $request): JsonResponse
    {
        $user = auth()->user();

        // If they are already a seller
        if ($user->hasRole('seller')) {
            return $this->successResponse([
                'status' => 'seller',
                'admin_notes' => null
            ]);
        }

        // Check for pending or rejected requests
        $latestRequest = \App\Models\SellerRequest::where('user_id', $user->id)
            ->latest()
            ->first();

        if ($latestRequest) {
            return $this->successResponse([
                'status' => $latestRequest->status, // 'pending' or 'rejected'
                'admin_notes' => $latestRequest->admin_notes
            ]);
        }

        return $this->successResponse([
            'status' => 'none',
            'admin_notes' => null
        ]);
    }

    /**
     * Upgrade user to a seller.
     */
    #[OA\Post(
        path: '/api/seller-subscription/subscribe',
        summary: 'Submit Seller Subscription Request',
        description: 'Submits a request to upgrade the authenticated user to a seller role. Requires KYC verification to be approved. Admin approval is required.',
        security: [['bearerAuth' => []]],
        tags: ['Seller'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successfully submitted the request',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Your request to become a seller has been submitted successfully and is awaiting admin approval.')
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Bad Request - Already a seller or KYC not approved',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Please complete identity verification to become a seller.')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        // 1. Check if they are already a seller
        if ($user->hasRole('seller')) {
            return $this->errorResponse(__('You are already a seller.'), 400);
        }

        // 2. Check if they have completed KYC
        if ($user->status !== 'approved') {
            return $this->errorResponse(__('Please complete identity verification to become a seller.'), 400);
        }
        
        // 3. Check if there is already a pending request
        $pendingRequest = \App\Models\SellerRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();
            
        if ($pendingRequest) {
            return $this->errorResponse(__('Your request to become a seller is already pending approval.'), 400);
        }

        // 4. Create a request
        \App\Models\SellerRequest::create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        return $this->successResponse(null, __('Your request to become a seller has been submitted successfully and is awaiting admin approval.'));
    }
}
