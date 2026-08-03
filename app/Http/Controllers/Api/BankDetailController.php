<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class BankDetailController extends Controller
{
    /**
     * Get the current user's bank details.
     */
    #[OA\Get(
        path: '/api/bank-details',
        summary: 'Get Bank Details',
        description: 'Returns the authenticated user\'s bank account details.',
        security: [['bearerAuth' => []]],
        tags: ['Bank Details'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            'iban' => 'SA12345678901234567890',
                            'bic_code' => 'NBBKSA11',
                            'beneficiary_name' => 'John Doe',
                            'bank_name' => 'National Bank',
                            'account_number' => '1234567890',
                            'address_1' => '123 Main St',
                            'address_2' => 'Apt 4B',
                            'bank_city' => 'Riyadh',
                            'bank_country' => 'Saudi Arabia',
                            'is_verified' => true
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(example: ['message' => 'Unauthenticated.'])
            )
        ]
    )]
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
    #[OA\Put(
        path: '/api/bank-details',
        summary: 'Update Bank Details',
        description: 'Updates the authenticated user\'s bank account details. Updating details will reset the verification status.',
        security: [['bearerAuth' => []]],
        tags: ['Bank Details'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'iban', type: 'string', example: 'SA12345678901234567890'),
                    new OA\Property(property: 'bic_code', type: 'string', example: 'NBBKSA11'),
                    new OA\Property(property: 'beneficiary_name', type: 'string', example: 'John Doe'),
                    new OA\Property(property: 'bank_name', type: 'string', example: 'National Bank'),
                    new OA\Property(property: 'account_number', type: 'string', example: '1234567890'),
                    new OA\Property(property: 'address_1', type: 'string', example: '123 Main St'),
                    new OA\Property(property: 'address_2', type: 'string', example: 'Apt 4B'),
                    new OA\Property(property: 'bank_city', type: 'string', example: 'Riyadh'),
                    new OA\Property(property: 'bank_country', type: 'string', example: 'Saudi Arabia')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Bank details updated successfully',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => 'Bank details updated successfully.'
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation Error',
                content: new OA\JsonContent(
                    example: [
                        'message' => 'The iban field must not be greater than 255 characters.',
                        'errors' => [
                            'iban' => ['The iban field must not be greater than 255 characters.']
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(example: ['message' => 'Unauthenticated.'])
            )
        ]
    )]
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
