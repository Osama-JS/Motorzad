<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HyperpayTransaction;
use App\Services\HyperPayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class HyperPayApiController extends Controller
{
    public function __construct(
        protected HyperPayService $hyperPayService
    ) {}

    /**
     * Initialize HyperPay Checkout session for Mobile Application.
     */
    #[OA\Post(
        path: '/api/wallet/hyperpay/checkout',
        summary: 'Initialize HyperPay Checkout Session',
        description: 'Creates a new HyperPay checkout session for mobile or web clients to pay using Mada, Visa/Mastercard, or Apple Pay.',
        security: [['bearerAuth' => []]],
        tags: ['Wallet'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['amount', 'brand'],
                properties: [
                    new OA\Property(property: 'amount', type: 'number', format: 'float', example: 500.00, description: 'Amount in SAR to deposit'),
                    new OA\Property(property: 'brand', type: 'string', enum: ['mada', 'visa_master', 'apple_pay'], example: 'mada', description: 'Payment brand/method'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Session initialized successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'transaction_id', type: 'integer', example: 12),
                                new OA\Property(property: 'checkout_id', type: 'string', example: '8A8294174B7E6CA0014B829...', description: 'HyperPay checkout ID to pass to mobile SDK'),
                                new OA\Property(property: 'merchant_transaction_id', type: 'string', example: 'MZ-HP-20260905143000-A1B2C'),
                                new OA\Property(property: 'brand', type: 'string', example: 'mada'),
                                new OA\Property(property: 'widget_brands', type: 'string', example: 'MADA'),
                                new OA\Property(property: 'amount', type: 'number', format: 'float', example: 500.00),
                                new OA\Property(property: 'currency', type: 'string', example: 'SAR'),
                                new OA\Property(property: 'base_url', type: 'string', example: 'https://eu-test.oppwa.com'),
                                new OA\Property(property: 'script_url', type: 'string', example: 'https://eu-test.oppwa.com/v1/paymentWidgets.js?checkoutId=8A82...'),
                            ]
                        ),
                        new OA\Property(property: 'message', type: 'string', example: 'Session initialized successfully.')
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation or Gateway Error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'The minimum deposit amount is 10.00 SAR')
                    ]
                )
            )
        ]
    )]
    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'brand'  => 'required|in:mada,visa_master,apple_pay',
        ]);

        try {
            $user = $request->user();
            $data = $this->hyperPayService->prepareCheckout(
                user: $user,
                amount: (float) $validated['amount'],
                brand: $validated['brand'],
                channel: 'api'
            );

            return response()->json([
                'success' => true,
                'data'    => $data,
                'message' => __('Checkout session initialized successfully.'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Verify payment status and credit wallet.
     */
    #[OA\Post(
        path: '/api/wallet/hyperpay/verify',
        summary: 'Verify HyperPay Payment and Credit Wallet',
        description: 'Verifies the outcome of a HyperPay transaction using checkout_id, credits user wallet upon success, and returns transaction details.',
        security: [['bearerAuth' => []]],
        tags: ['Wallet'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['checkout_id'],
                properties: [
                    new OA\Property(property: 'checkout_id', type: 'string', example: '8A8294174B7E6CA0014B829...'),
                    new OA\Property(property: 'brand', type: 'string', enum: ['mada', 'visa_master', 'apple_pay'], example: 'mada', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Payment verified successfully and credited',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Payment successful! 500.00 SAR has been added to your wallet.'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'transaction_id', type: 'integer', example: 12),
                                new OA\Property(property: 'merchant_transaction_id', type: 'string', example: 'MZ-HP-20260905143000-A1B2C'),
                                new OA\Property(property: 'amount', type: 'number', example: 500.00),
                                new OA\Property(property: 'status', type: 'string', example: 'paid'),
                                new OA\Property(property: 'card_brand', type: 'string', example: 'mada'),
                                new OA\Property(property: 'card_last4', type: 'string', example: '1234'),
                                new OA\Property(property: 'new_wallet_balance', type: 'number', example: 2500.00),
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Payment Failed or Rejected',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Payment verification failed.'),
                    ]
                )
            )
        ]
    )]
    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'checkout_id' => 'required|string',
            'brand'       => 'nullable|string|in:mada,visa_master,apple_pay',
        ]);

        $checkoutId = $validated['checkout_id'];
        $user = $request->user();

        $transaction = HyperpayTransaction::where('checkout_id', $checkoutId)
            ->where('user_id', $user->id)
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => __('Transaction not found.'),
            ], 404);
        }

        // If already paid, return success immediately (idempotent)
        if ($transaction->status === 'paid') {
            return response()->json([
                'success' => true,
                'message' => __('Payment already verified and credited.'),
                'data'    => [
                    'transaction_id'          => $transaction->id,
                    'merchant_transaction_id' => $transaction->merchant_transaction_id,
                    'amount'                  => (float) $transaction->amount,
                    'status'                  => $transaction->status,
                    'card_brand'              => $transaction->brand,
                    'card_last4'              => $transaction->card_last4,
                    'new_wallet_balance'      => (float) $user->wallet?->fresh()->balance,
                ],
            ]);
        }

        $brand = $validated['brand'] ?? $transaction->brand;

        try {
            $paymentData = $this->hyperPayService->verifyPayment($checkoutId, $brand);
            $resultCode = $paymentData['result']['code'] ?? '';

            if ($this->hyperPayService->isSuccessCode($resultCode)) {
                $this->hyperPayService->processSuccessfulPayment($transaction, $paymentData);

                return response()->json([
                    'success' => true,
                    'message' => __('Payment successful! An amount of :amount SAR has been credited to your wallet.', [
                        'amount' => number_format($transaction->amount, 2),
                    ]),
                    'data'    => [
                        'transaction_id'          => $transaction->id,
                        'merchant_transaction_id' => $transaction->merchant_transaction_id,
                        'amount'                  => (float) $transaction->amount,
                        'status'                  => 'paid',
                        'card_brand'              => $transaction->brand,
                        'card_last4'              => $transaction->fresh()->card_last4,
                        'new_wallet_balance'      => (float) $user->wallet?->fresh()->balance,
                    ],
                ]);
            } else {
                $this->hyperPayService->markAsFailed($transaction, $paymentData);
                $errorDesc = $paymentData['result']['description'] ?? __('The payment could not be processed.');

                return response()->json([
                    'success' => false,
                    'message' => __('Payment Failed: :desc', ['desc' => $errorDesc]),
                    'data'    => [
                        'transaction_id' => $transaction->id,
                        'result_code'    => $resultCode,
                        'status'         => 'failed',
                    ],
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Webhook receiver for HyperPay asynchronous server-to-server notifications.
     */
    #[OA\Post(
        path: '/api/hyperpay/webhook',
        summary: 'HyperPay Webhook Receiver',
        description: 'Receives asynchronous server-to-server notifications from HyperPay to finalize payments in the background.',
        tags: ['Wallet'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Webhook processed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'result', type: 'string', example: 'success')
                    ]
                )
            )
        ]
    )]
    public function webhook(Request $request): JsonResponse
    {
        $result = $this->hyperPayService->handleWebhook($request);

        return response()->json([
            'success' => true,
            'result'  => $result['status'] ?? 'ok',
        ]);
    }
}
