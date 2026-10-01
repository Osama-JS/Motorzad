<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepositRequestResource;
use App\Http\Resources\WalletResource;
use App\Http\Resources\WalletTransactionResource;
use App\Http\Resources\WithdrawalRequestResource;
use App\Models\BankAccount;
use App\Models\DepositRequest;
use App\Models\HyperpayTransaction;
use App\Models\Setting;
use App\Models\WithdrawalRequest;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class WalletController extends Controller
{
    public function __construct(protected WalletService $walletService) {}

    /**
     * Get wallet summary.
     */
    #[OA\Get(
        path: '/api/wallet',
        summary: 'Get Wallet Summary',
        description: 'Returns the authenticated user\'s wallet balance, total deposits, total withdrawals, and debt ceiling information.',
        security: [['bearerAuth' => []]],
        tags: ['Wallet'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            'id' => 1,
                            'balance' => 12500.50,
                            'total_deposits' => 15000.00,
                            'total_withdrawals' => 2500.00,
                            'debt_ceiling' => 5000.00,
                            'debt_usage' => 0.00,
                            'currency' => 'SAR',
                            'user' => [
                                'id' => 5,
                                'first_name' => 'محمد',
                                'last_name' => 'أحمد'
                            ]
                        ],
                        'message' => null
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
        $wallet = $request->user()->wallet;

        return $this->successResponse(new WalletResource($wallet));
    }

    /**
     * Get paginated wallet transactions with filters.
     */
    #[OA\Get(
        path: '/api/wallet/transactions',
        summary: 'Get Wallet Transactions',
        description: 'Returns a paginated list of wallet transactions for the authenticated user.',
        security: [['bearerAuth' => []]],
        tags: ['Wallet'],
        parameters: [
            new OA\Parameter(name: 'type', in: 'query', required: false, description: 'Filter by transaction type (credit, debit)', schema: new OA\Schema(type: 'string', enum: ['credit', 'debit'])),
            new OA\Parameter(name: 'date_from', in: 'query', required: false, description: 'Filter from date (YYYY-MM-DD)', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'date_to', in: 'query', required: false, description: 'Filter to date (YYYY-MM-DD)', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Page number', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, description: 'Items per page', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            'current_page' => 1,
                            'data' => [
                                [
                                    'id' => 1,
                                    'wallet_id' => 1,
                                    'type' => 'credit',
                                    'amount' => 500.00,
                                    'balance_after' => 1500.00,
                                    'description' => 'Deposit via Bank Transfer',
                                    'reference_id' => 'TRX-12345',
                                    'created_at' => '2023-11-10T12:00:00.000000Z'
                                ]
                            ],
                            'last_page' => 1,
                            'per_page' => 15,
                            'total' => 1
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
    public function transactions(Request $request): JsonResponse
    {
        $wallet = $request->user()->wallet;
        $query  = $wallet->transactions()->with('creator')->latest();

        if ($request->filled('type') && in_array($request->type, ['credit', 'debit'])) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate($request->input('per_page', 15));

        return $this->successResponse(
            WalletTransactionResource::collection($transactions->items()),
            null,
            200,
            [
                'current_page' => $transactions->currentPage(),
                'last_page'    => $transactions->lastPage(),
                'total'        => $transactions->total(),
                'per_page'     => $transactions->perPage(),
            ]
        );
    }

    /**
     * Submit a withdrawal request.
     */
    #[OA\Post(
        path: '/api/wallet/withdraw',
        summary: 'Request Withdrawal',
        description: 'Submits a request to withdraw funds from the wallet.',
        security: [['bearerAuth' => []]],
        tags: ['Wallet'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['amount', 'payment_method'],
                properties: [
                    new OA\Property(property: 'amount', type: 'number', example: 500),
                    new OA\Property(property: 'payment_method', type: 'string', enum: ['bank_transfer', 'wallet'], example: 'bank_transfer')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Withdrawal request submitted',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => 'Withdrawal request submitted successfully. It will be reviewed soon.',
                        'data' => [
                            'id' => 1,
                            'wallet_id' => 1,
                            'requested_amount' => 500.00,
                            'status' => 'pending',
                            'payment_method' => 'bank_transfer',
                            'created_at' => '2023-11-10T12:00:00.000000Z'
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error or insufficient balance',
                content: new OA\JsonContent(
                    example: [
                        'success' => false,
                        'message' => 'Insufficient available balance for this withdrawal. Some of your funds may be frozen in active bids.'
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
    public function requestWithdrawal(Request $request): JsonResponse
    {
        $user   = $request->user();
        $wallet = $user->wallet;

        $validated = $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:bank_transfer,wallet',
        ]);

        if ($validated['amount'] > $wallet->available_balance) {
            return $this->errorResponse(__('Insufficient available balance for this withdrawal. Some of your funds may be frozen in active bids.'), 422);
        }

        if (WithdrawalRequest::where('user_id', $user->id)->where('status', 'pending')->exists()) {
            return $this->errorResponse(__('You already have a pending withdrawal request.'), 422);
        }

        $withdrawal = WithdrawalRequest::create([
            'user_id'         => $user->id,
            'wallet_id'       => $wallet->id,
            'requested_amount'=> $validated['amount'],
            'status'          => 'pending',
            'payment_method'  => $validated['payment_method'],
        ]);

        return $this->successResponse(
            new WithdrawalRequestResource($withdrawal),
            __('Withdrawal request submitted successfully. It will be reviewed soon.'),
            201
        );
    }

    /**
     * Submit a deposit proof (receipt image).
     */
    #[OA\Post(
        path: '/api/wallet/deposit',
        summary: 'Request Deposit',
        description: 'Submits a deposit request by uploading a bank transfer receipt.',
        security: [['bearerAuth' => []]],
        tags: ['Wallet'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['amount', 'bank_account_id', 'receipt'],
                    properties: [
                        new OA\Property(property: 'amount', type: 'number', example: 1500),
                        new OA\Property(property: 'bank_account_id', type: 'integer', example: 1),
                        new OA\Property(property: 'receipt', type: 'string', format: 'binary')
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Deposit request submitted',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'message' => 'Deposit request submitted successfully. It will be reviewed soon.',
                        'data' => [
                            'id' => 1,
                            'wallet_id' => 1,
                            'amount' => 1500.00,
                            'status' => 'pending',
                            'receipt_url' => 'https://example.com/storage/receipts/123.jpg',
                            'bank_account' => [
                                'id' => 1,
                                'bank_name_ar' => 'بنك الراجحي',
                                'bank_name_en' => 'Al Rajhi Bank',
                                'account_name' => 'Motorzad Corp',
                                'account_number' => '1234567890',
                                'iban' => 'SA12345678901234567890'
                            ],
                            'created_at' => '2023-11-10T12:00:00.000000Z'
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(example: ['message' => 'The receipt field is required.', 'errors' => ['receipt' => ['The receipt field is required.']]])
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(example: ['message' => 'Unauthenticated.'])
            )
        ]
    )]
    public function requestDeposit(Request $request): JsonResponse
    {
        $user   = $request->user();
        $wallet = $user->wallet;

        $validated = $request->validate([
            'amount'          => 'required|numeric|min:1',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'receipt'         => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $receiptPath = $request->file('receipt')->store('deposits/receipts', 'public');

        $deposit = DepositRequest::create([
            'user_id'         => $user->id,
            'wallet_id'       => $wallet->id,
            'bank_account_id' => $validated['bank_account_id'],
            'amount'          => $validated['amount'],
            'receipt_path'    => $receiptPath,
            'status'          => 'pending',
        ]);

        return $this->successResponse(
            new DepositRequestResource($deposit->load('bankAccount')),
            __('Deposit request submitted successfully. It will be reviewed soon.'),
            201
        );
    }

    /**
     * Get deposit history.
     */
    #[OA\Get(
        path: '/api/wallet/deposits',
        summary: 'Get Deposit History',
        description: 'Returns a paginated list of deposit requests for the authenticated user.',
        security: [['bearerAuth' => []]],
        tags: ['Wallet'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Page number', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, description: 'Items per page', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            [
                                'id' => 1,
                                'wallet_id' => 1,
                                'amount' => 1500.00,
                                'status' => 'pending',
                                'receipt_url' => 'https://example.com/storage/receipts/123.jpg',
                                'bank_account' => [
                                    'id' => 1,
                                    'bank_name_ar' => 'بنك الراجحي',
                                    'bank_name_en' => 'Al Rajhi Bank',
                                    'account_name' => 'Motorzad Corp',
                                    'account_number' => '1234567890',
                                    'iban' => 'SA12345678901234567890'
                                ],
                                'created_at' => '2023-11-10T12:00:00.000000Z'
                            ]
                        ],
                        'meta' => [
                            'current_page' => 1,
                            'last_page' => 1,
                            'total' => 1,
                            'per_page' => 15
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
    public function deposits(Request $request): JsonResponse
    {
        $deposits = DepositRequest::where('user_id', $request->user()->id)
            ->with('bankAccount')
            ->latest()
            ->paginate($request->input('per_page', 15));

        return $this->successResponse(
            DepositRequestResource::collection($deposits->items()),
            null,
            200,
            [
                'current_page' => $deposits->currentPage(),
                'last_page'    => $deposits->lastPage(),
                'total'        => $deposits->total(),
                'per_page'     => $deposits->perPage(),
            ]
        );
    }

    /**
     * Get withdrawal history.
     */
    #[OA\Get(
        path: '/api/wallet/withdrawals',
        summary: 'Get Withdrawal History',
        description: 'Returns a paginated list of withdrawal requests for the authenticated user.',
        security: [['bearerAuth' => []]],
        tags: ['Wallet'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Page number', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, description: 'Items per page', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            [
                                'id' => 1,
                                'wallet_id' => 1,
                                'requested_amount' => 500.00,
                                'status' => 'pending',
                                'payment_method' => 'bank_transfer',
                                'created_at' => '2023-11-10T12:00:00.000000Z'
                            ]
                        ],
                        'meta' => [
                            'current_page' => 1,
                            'last_page' => 1,
                            'total' => 1,
                            'per_page' => 15
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
    public function withdrawals(Request $request): JsonResponse
    {
        $withdrawals = WithdrawalRequest::where('user_id', $request->user()->id)
            ->latest()
            ->paginate($request->input('per_page', 15));

        return $this->successResponse(
            WithdrawalRequestResource::collection($withdrawals->items()),
            null,
            200,
            [
                'current_page' => $withdrawals->currentPage(),
                'last_page'    => $withdrawals->lastPage(),
                'total'        => $withdrawals->total(),
                'per_page'     => $withdrawals->perPage(),
            ]
        );
    }

    /**
     * Get available platform bank accounts for deposit.
     */
    #[OA\Get(
        path: '/api/bank-accounts',
        summary: 'Get Platform Bank Accounts',
        description: 'Returns a list of active platform bank accounts that users can transfer deposits to.',
        security: [['bearerAuth' => []]],
        tags: ['Wallet'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful Response',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'data' => [
                            [
                                'id' => 1,
                                'bank_name' => 'بنك الراجحي',
                                'iban' => 'SA12345678901234567890',
                                'beneficiary_name' => 'شركة موترزاد',
                                'logo_url' => 'https://example.com/storage/banks/alrajhi.png'
                            ],
                            [
                                'id' => 2,
                                'bank_name' => 'البنك الأهلي',
                                'iban' => 'SA09876543210987654321',
                                'beneficiary_name' => 'شركة موترزاد',
                                'logo_url' => null
                            ]
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
    public function bankAccounts(): JsonResponse
    {
        $accounts = BankAccount::where('is_active', true)->get()->map(fn ($a) => [
            'id'               => $a->id,
            'bank_name'        => $a->bank_name,
            'iban'             => $a->iban,
            'beneficiary_name' => $a->beneficiary_name,
            'logo_url'         => $a->logo_path ? asset('storage/' . $a->logo_path) : null,
        ]);

        return $this->successResponse($accounts);
    }

    /**
     * Get enabled payment methods for wallet deposit (HyperPay + Bank Transfer).
     */
    #[OA\Get(
        path: '/api/wallet/payment-methods',
        summary: 'Get Available Deposit Payment Methods',
        description: 'Returns the list of active payment methods enabled by administration for wallet top-up (Mada, Visa/Master, Apple Pay, Bank Transfer).',
        security: [['bearerAuth' => []]],
        tags: ['Wallet'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of available payment methods',
                content: new OA\JsonContent(
                    example: [
                        'error' => false,
                        'data' => [
                            [
                                'id' => 'mada',
                                'name' => 'بطاقة مدى (Mada)',
                                'name_en' => 'Mada Debit Card',
                                'type' => 'hyperpay',
                                'brand' => 'mada',
                                'logo_url' => 'http://localhost/Motorzad/public/images/payments/mada.svg',
                                'description' => 'شحن فوري ولحظي عبر بطاقات الصراف والبنوك السعودية',
                                'requires_webview' => true,
                                'min_amount' => 10.0,
                                'max_amount' => 500000.0,
                                'currency' => 'SAR'
                            ],
                            [
                                'id' => 'visa_master',
                                'name' => 'فيزا / ماستركارد (Visa / MasterCard)',
                                'name_en' => 'Visa / MasterCard',
                                'type' => 'hyperpay',
                                'brand' => 'visa_master',
                                'logo_url' => 'http://localhost/Motorzad/public/images/payments/visa_master.svg',
                                'description' => 'البطاقات الائتمانية الدولية مع حماية 3D-Secure',
                                'requires_webview' => true,
                                'min_amount' => 10.0,
                                'max_amount' => 500000.0,
                                'currency' => 'SAR'
                            ],
                            [
                                'id' => 'bank_transfer',
                                'name' => 'التحويل البنكي اليدوي',
                                'name_en' => 'Bank Transfer',
                                'type' => 'bank_transfer',
                                'brand' => 'bank_transfer',
                                'logo_url' => 'http://localhost/Motorzad/public/images/payments/bank_transfer.svg',
                                'description' => 'التحويل المباشر لحسابات المنصة ورفع صورة الإيصال',
                                'requires_webview' => false,
                                'min_amount' => 1.0,
                                'max_amount' => null,
                                'currency' => 'SAR'
                            ]
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
    public function paymentMethods(): JsonResponse
    {
        $isHyperPayEnabled = (bool) Setting::get('hyperpay_enabled', '0');
        $minDeposit = (float) Setting::get('hyperpay_min_deposit', 10);
        $maxDeposit = (float) Setting::get('hyperpay_max_deposit', 500000);

        $methods = [];

        // 1. Mada
        if ($isHyperPayEnabled && Setting::get('payment_method_mada_enabled', '1') == '1') {
            $methods[] = [
                'id'               => 'mada',
                'name'             => __('بطاقة مدى (Mada)'),
                'name_en'          => 'Mada Debit Card',
                'type'             => 'hyperpay',
                'brand'            => 'mada',
                'logo_url'         => asset('images/payments/mada.svg'),
                'description'      => __('شحن فوري ولحظي عبر بطاقات الصراف والبنوك السعودية'),
                'requires_webview' => true,
                'min_amount'       => $minDeposit,
                'max_amount'       => $maxDeposit,
                'currency'         => 'SAR',
            ];
        }

        // 2. Visa / MasterCard
        if ($isHyperPayEnabled && Setting::get('payment_method_visa_master_enabled', '1') == '1') {
            $methods[] = [
                'id'               => 'visa_master',
                'name'             => __('فيزا / ماستركارد (Visa / MasterCard)'),
                'name_en'          => 'Visa / MasterCard',
                'type'             => 'hyperpay',
                'brand'            => 'visa_master',
                'logo_url'         => asset('images/payments/visa_master.svg'),
                'description'      => __('البطاقات الائتمانية الدولية مع حماية 3D-Secure'),
                'requires_webview' => true,
                'min_amount'       => $minDeposit,
                'max_amount'       => $maxDeposit,
                'currency'         => 'SAR',
            ];
        }

        // 3. Apple Pay
        if ($isHyperPayEnabled && Setting::get('payment_method_apple_pay_enabled', '1') == '1') {
            $methods[] = [
                'id'               => 'apple_pay',
                'name'             => 'Apple Pay',
                'name_en'          => 'Apple Pay',
                'type'             => 'hyperpay',
                'brand'            => 'apple_pay',
                'logo_url'         => asset('images/payments/apple_pay.svg'),
                'description'      => __('الدفع السريع والآمن عبر أجهزة آبل'),
                'requires_webview' => true,
                'min_amount'       => $minDeposit,
                'max_amount'       => $maxDeposit,
                'currency'         => 'SAR',
            ];
        }

        // 4. Bank Transfer
        if (Setting::get('payment_method_bank_transfer_enabled', '1') == '1') {
            $methods[] = [
                'id'               => 'bank_transfer',
                'name'             => __('التحويل البنكي اليدوي'),
                'name_en'          => 'Bank Transfer',
                'type'             => 'bank_transfer',
                'brand'            => 'bank_transfer',
                'logo_url'         => asset('images/payments/bank_transfer.svg'),
                'description'      => __('التحويل المباشر لحسابات المنصة ورفع صورة الإيصال'),
                'requires_webview' => false,
                'min_amount'       => 1.0,
                'max_amount'       => null,
                'currency'         => 'SAR',
            ];
        }

        return response()->json([
            'error' => false,
            'data'  => $methods,
        ]);
    }

    /**
     * Get user payments and deposit requests history with stats and pagination.
     */
    #[OA\Get(
        path: '/api/wallet/payments-history',
        summary: 'Get Payments & Deposit History',
        description: 'Returns the authenticated user\'s payment history (matching bidder/wallet/payments-history web page), including dashboard statistics (bank transfer totals, electronic totals, wallet balance), and paginated items filterable by payment type (all, bank_transfer, electronic).',
        security: [['bearerAuth' => []]],
        tags: ['Wallet'],
        parameters: [
            new OA\Parameter(
                name: 'type',
                in: 'query',
                required: false,
                description: 'Filter by payment type: all (default), bank_transfer (bank receipts), electronic (HyperPay gateway)',
                schema: new OA\Schema(type: 'string', enum: ['all', 'bank_transfer', 'electronic'], default: 'all')
            ),
            new OA\Parameter(
                name: 'page',
                in: 'query',
                required: false,
                description: 'Page number (default: 1)',
                schema: new OA\Schema(type: 'integer', default: 1)
            ),
            new OA\Parameter(
                name: 'per_page',
                in: 'query',
                required: false,
                description: 'Number of items per page (default: 15, max: 100)',
                schema: new OA\Schema(type: 'integer', default: 15)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Payments history retrieved successfully',
                content: new OA\JsonContent(
                    example: [
                        'success' => true,
                        'error'   => false,
                        'data'    => [
                            'stats' => [
                                'bank_transfers_count'          => 3,
                                'bank_transfers_approved_total' => 15000.0,
                                'online_payments_count'         => 2,
                                'online_payments_paid_total'    => 5000.0,
                                'total_deposited_amount'        => 20000.0,
                                'wallet_available_balance'      => 12500.5,
                                'wallet_held_balance'           => 2000.0,
                                'currency'                      => 'SAR'
                            ],
                            'items' => [
                                [
                                    'id'                   => 12,
                                    'type'                 => 'electronic',
                                    'type_label'           => 'الدفع الإلكتروني (البوابة)',
                                    'amount'               => 5000.0,
                                    'currency'             => 'SAR',
                                    'status'               => 'paid',
                                    'status_label'         => 'مدفوع بنجاح',
                                    'created_at'           => '2026-09-28T14:30:00.000000Z',
                                    'created_at_formatted' => '2026-09-28 14:30',
                                    'details'              => [
                                        'transaction_id'     => 'TX-1727526600-9988',
                                        'checkout_id'        => '8ACDA7A082F8E7F0.uat01-vm-tx02',
                                        'brand'              => 'mada',
                                        'brand_name'         => 'MADA',
                                        'brand_logo'         => 'http://localhost/Motorzad/public/images/payments/mada.svg',
                                        'card_last4'         => '4008',
                                        'result_description' => 'Transaction succeeded',
                                        'paid_at'            => '2026-09-28 14:31'
                                    ]
                                ],
                                [
                                    'id'                   => 45,
                                    'type'                 => 'bank_transfer',
                                    'type_label'           => 'التحويل البنكي',
                                    'amount'               => 15000.0,
                                    'currency'             => 'SAR',
                                    'status'               => 'approved',
                                    'status_label'         => 'تم الاعتماد وإيداع الرصيد',
                                    'created_at'           => '2026-09-25T10:15:00.000000Z',
                                    'created_at_formatted' => '2026-09-25 10:15',
                                    'details'              => [
                                        'bank_name'      => 'مصرف الراجحي',
                                        'iban'           => 'SA0380000000608010167519',
                                        'account_number' => '608010167519',
                                        'account_name'   => 'شركة موتورزاد للمزادات',
                                        'receipt_url'    => 'http://localhost/Motorzad/public/storage/receipts/transfer_123.jpg',
                                        'admin_note'     => null,
                                        'processed_at'   => '2026-09-25 11:00'
                                    ]
                                ]
                            ],
                            'pagination' => [
                                'current_page' => 1,
                                'per_page'     => 15,
                                'last_page'    => 1,
                                'total'        => 2,
                                'has_more'     => false
                            ]
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
    public function paymentsHistory(Request $request): JsonResponse
    {
        $user = $request->user();
        $type = strtolower((string) $request->input('type', 'all'));
        $perPage = max(1, min(100, (int) $request->input('per_page', 15)));
        $page = max(1, (int) $request->input('page', 1));

        // 1. Dashboard statistics matching bidder/wallet/payments-history
        $bankTransfersCount = DepositRequest::where('user_id', $user->id)->count();
        $bankTransfersApprovedTotal = (float) DepositRequest::where('user_id', $user->id)->where('status', 'approved')->sum('amount');
        $onlinePaymentsCount = HyperpayTransaction::where('user_id', $user->id)->count();
        $onlinePaymentsPaidTotal = (float) HyperpayTransaction::where('user_id', $user->id)->where('status', 'paid')->sum('amount');
        $totalDepositedAmount = round($bankTransfersApprovedTotal + $onlinePaymentsPaidTotal, 2);

        $wallet = $user->wallet;
        $availableBalance = (float) ($wallet->available_balance ?? 0);
        $heldBalance = (float) ($wallet->held_balance ?? 0);

        $stats = [
            'bank_transfers_count'          => $bankTransfersCount,
            'bank_transfers_approved_total' => $bankTransfersApprovedTotal,
            'online_payments_count'         => $onlinePaymentsCount,
            'online_payments_paid_total'    => $onlinePaymentsPaidTotal,
            'total_deposited_amount'        => $totalDepositedAmount,
            'wallet_available_balance'      => $availableBalance,
            'wallet_held_balance'           => $heldBalance,
            'currency'                      => 'SAR',
        ];

        // 2. Querying based on filter
        if ($type === 'bank_transfer') {
            $query = DepositRequest::where('user_id', $user->id)
                ->with('bankAccount')
                ->latest();

            $paginated = $query->paginate($perPage, ['*'], 'page', $page);
            $items = collect($paginated->items())->map(fn ($item) => $this->formatBankTransferItem($item))->values();
            $total = $paginated->total();
            $lastPage = $paginated->lastPage();
            $currentPage = $paginated->currentPage();
        } elseif ($type === 'electronic' || $type === 'online') {
            $query = HyperpayTransaction::where('user_id', $user->id)
                ->latest();

            $paginated = $query->paginate($perPage, ['*'], 'page', $page);
            $items = collect($paginated->items())->map(fn ($item) => $this->formatElectronicPaymentItem($item))->values();
            $total = $paginated->total();
            $lastPage = $paginated->lastPage();
            $currentPage = $paginated->currentPage();
        } else {
            // 'all': Combine both tables chronologically
            $bankSub = DB::table('deposit_requests')
                ->where('user_id', $user->id)
                ->select([
                    'id',
                    DB::raw("'bank_transfer' as payment_type"),
                    'created_at',
                ]);

            $onlineSub = DB::table('hyperpay_transactions')
                ->where('user_id', $user->id)
                ->select([
                    'id',
                    DB::raw("'electronic' as payment_type"),
                    'created_at',
                ]);

            $unionQuery = $bankSub->unionAll($onlineSub);

            $total = DB::query()->fromSub($unionQuery, 'combined')->count();
            $offset = ($page - 1) * $perPage;

            $pageRows = DB::query()
                ->fromSub($unionQuery, 'combined')
                ->orderBy('created_at', 'desc')
                ->offset($offset)
                ->limit($perPage)
                ->get();

            $bankIds = $pageRows->where('payment_type', 'bank_transfer')->pluck('id')->all();
            $onlineIds = $pageRows->where('payment_type', 'electronic')->pluck('id')->all();

            $bankModels = !empty($bankIds)
                ? DepositRequest::whereIn('id', $bankIds)->with('bankAccount')->get()->keyBy('id')
                : collect();

            $onlineModels = !empty($onlineIds)
                ? HyperpayTransaction::whereIn('id', $onlineIds)->get()->keyBy('id')
                : collect();

            $items = $pageRows->map(function ($row) use ($bankModels, $onlineModels) {
                if ($row->payment_type === 'bank_transfer') {
                    $model = $bankModels->get($row->id);
                    return $model ? $this->formatBankTransferItem($model) : null;
                } else {
                    $model = $onlineModels->get($row->id);
                    return $model ? $this->formatElectronicPaymentItem($model) : null;
                }
            })->filter()->values();

            $lastPage = max(1, (int) ceil($total / $perPage));
            $currentPage = $page;
        }

        return response()->json([
            'success' => true,
            'error'   => false,
            'data'    => [
                'stats'      => $stats,
                'items'      => $items,
                'pagination' => [
                    'current_page' => (int) $currentPage,
                    'per_page'     => (int) $perPage,
                    'last_page'    => (int) $lastPage,
                    'total'        => (int) $total,
                    'has_more'     => $currentPage < $lastPage,
                ],
            ],
        ]);
    }

    /**
     * Format a DepositRequest model into an API payment history item array.
     */
    protected function formatBankTransferItem(DepositRequest $deposit): array
    {
        return [
            'id'                   => $deposit->id,
            'type'                 => 'bank_transfer',
            'type_label'           => __('التحويل البنكي'),
            'amount'               => (float) $deposit->amount,
            'currency'             => 'SAR',
            'status'               => $deposit->status,
            'status_label'         => match ($deposit->status) {
                'approved' => __('تم الاعتماد وإيداع الرصيد'),
                'pending'  => __('قيد المراجعة والتدقيق'),
                'rejected' => __('مرفوض'),
                default    => __($deposit->status),
            },
            'created_at'           => $deposit->created_at?->toISOString(),
            'created_at_formatted' => $deposit->created_at?->format('Y-m-d H:i'),
            'details'              => [
                'bank_name'      => $deposit->bankAccount->bank_name ?? __('حساب منصة موتورزاد'),
                'iban'           => $deposit->bankAccount->iban ?? null,
                'account_number' => $deposit->bankAccount->account_number ?? null,
                'account_name'   => $deposit->bankAccount->account_name ?? null,
                'receipt_url'    => $deposit->receipt_path ? asset('storage/' . $deposit->receipt_path) : null,
                'admin_note'     => $deposit->admin_note,
                'processed_at'   => $deposit->processed_at?->format('Y-m-d H:i'),
            ],
        ];
    }

    /**
     * Format a HyperpayTransaction model into an API payment history item array.
     */
    protected function formatElectronicPaymentItem(HyperpayTransaction $tx): array
    {
        $brandSvg = 'images/payments/' . ($tx->brand ?? 'card') . '.svg';
        $logoUrl = file_exists(public_path($brandSvg)) ? asset($brandSvg) : null;

        return [
            'id'                   => $tx->id,
            'type'                 => 'electronic',
            'type_label'           => __('الدفع الإلكتروني (البوابة)'),
            'amount'               => (float) $tx->amount,
            'currency'             => $tx->currency ?: 'SAR',
            'status'               => $tx->status,
            'status_label'         => match ($tx->status) {
                'paid'                 => __('مدفوع بنجاح'),
                'pending', 'initiated' => __('قيد الانتظار'),
                'failed'               => __('فشلت العملية'),
                default                => __($tx->status),
            },
            'created_at'           => $tx->created_at?->toISOString(),
            'created_at_formatted' => $tx->created_at?->format('Y-m-d H:i'),
            'details'              => [
                'transaction_id'     => $tx->hyperpay_payment_id ?? $tx->merchant_transaction_id ?? $tx->checkout_id,
                'merchant_transaction_id' => $tx->merchant_transaction_id,
                'checkout_id'        => $tx->checkout_id,
                'brand'              => $tx->brand,
                'brand_name'         => strtoupper($tx->brand ?? 'CARD'),
                'brand_logo'         => $logoUrl,
                'card_last4'         => $tx->card_last4,
                'result_description' => $tx->result_description,
                'paid_at'            => $tx->paid_at?->format('Y-m-d H:i'),
            ],
        ];
    }
}
