<?php

namespace App\Services;

use App\Models\DepositRequest;
use App\Models\HyperpayTransaction;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class HyperPayService
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    /**
     * Check if HyperPay gateway is enabled in system settings.
     */
    public function isEnabled(): bool
    {
        return (bool) Setting::get('hyperpay_enabled', '0');
    }

    /**
     * Get operational mode: 'test' or 'live'.
     */
    public function getMode(): string
    {
        return Setting::get('hyperpay_mode', 'test');
    }

    /**
     * Get Base URL according to test or live mode.
     */
    public function getBaseUrl(): string
    {
        if ($this->getMode() === 'live') {
            return Setting::get('hyperpay_live_base_url', 'https://oppwa.com');
        }
        return Setting::get('hyperpay_test_base_url', 'https://eu-test.oppwa.com');
    }

    /**
     * Get secret Access Token.
     */
    public function getAccessToken(): string
    {
        return Setting::get('hyperpay_access_token', '');
    }

    /**
     * Get Entity ID based on payment brand.
     */
    public function getEntityId(string $brand = 'mada'): ?string
    {
        $normalizedBrand = strtolower($brand);
        return match ($normalizedBrand) {
            'mada' => Setting::get('hyperpay_entity_id_mada', ''),
            'visa', 'master', 'visa_master', 'mastercard' => Setting::get('hyperpay_entity_id_visa_master', ''),
            'apple_pay', 'applepay' => Setting::get('hyperpay_entity_id_apple_pay', ''),
            default => Setting::get('hyperpay_entity_id_mada', ''),
        };
    }

    /**
     * Map brand string to HyperPay widget brands attribute.
     */
    public function getWidgetBrands(string $brand = 'mada'): string
    {
        $normalizedBrand = strtolower($brand);
        return match ($normalizedBrand) {
            'mada' => 'MADA',
            'visa', 'master', 'visa_master', 'mastercard' => 'VISA MASTER',
            'apple_pay', 'applepay' => 'APPLEPAY',
            default => 'MADA VISA MASTER',
        };
    }

    /**
     * Prepare a checkout session with HyperPay.
     *
     * @param User $user
     * @param float $amount
     * @param string $brand ('mada', 'visa_master', 'apple_pay')
     * @param string $channel ('web', 'api')
     * @param string|null $returnUrl
     * @return array
     * @throws \Exception
     */
    public function prepareCheckout(
        User $user,
        float $amount,
        string $brand = 'mada',
        string $channel = 'web',
        ?string $returnUrl = null
    ): array {
        if (!$this->isEnabled()) {
            throw new \Exception(__('HyperPay gateway is currently disabled.'));
        }

        $minDeposit = (float) Setting::get('hyperpay_min_deposit', 10);
        $maxDeposit = (float) Setting::get('hyperpay_max_deposit', 500000);

        if ($amount < $minDeposit) {
            throw new \Exception(__('The minimum deposit amount is :amount SAR', ['amount' => number_format($minDeposit, 2)]));
        }

        if ($amount > $maxDeposit) {
            throw new \Exception(__('The maximum deposit amount is :amount SAR', ['amount' => number_format($maxDeposit, 2)]));
        }

        $entityId = $this->getEntityId($brand);
        $accessToken = $this->getAccessToken();

        if (empty($entityId) || empty($accessToken)) {
            throw new \Exception(__('HyperPay configuration credentials are missing. Please contact administration.'));
        }

        // Generate unique internal transaction ID
        $merchantTransactionId = 'MZ-HP-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -5));

        // Format amount with 2 decimals
        $formattedAmount = number_format($amount, 2, '.', '');
        $currency = 'SAR';

        // Prepare request body
        $params = [
            'entityId'                => $entityId,
            'amount'                  => $formattedAmount,
            'currency'                => $currency,
            'paymentType'             => 'DB', // Debit
            'merchantTransactionId'   => $merchantTransactionId,
            'customer.email'          => $user->email ?? 'customer@motorzad.com',
            'customer.givenName'      => $user->first_name ?: ($user->name ?: 'Customer'),
            'customer.surname'        => $user->last_name ?: 'Motorzad',
            'billing.street1'         => 'King Fahd Road',
            'billing.city'            => 'Riyadh',
            'billing.state'           => 'Riyadh',
            'billing.country'         => 'SA',
            'billing.postcode'        => '12211',
        ];

        // Specific handling for Mada cards
        if (strtolower($brand) === 'mada') {
            $params['customParameters[SHOPPER_brand]'] = 'MADA';
        }

        $endpoint = rtrim($this->getBaseUrl(), '/') . '/v1/checkouts';

        Log::info('HyperPay Checkout Init Request:', [
            'url' => $endpoint,
            'merchantTransactionId' => $merchantTransactionId,
            'amount' => $formattedAmount,
            'brand' => $brand,
            'channel' => $channel,
        ]);

        $response = Http::withToken($accessToken)
            ->asForm()
            ->post($endpoint, $params);

        $responseData = $response->json();

        Log::info('HyperPay Checkout Init Response:', [
            'status' => $response->status(),
            'response' => $responseData,
        ]);

        if (!$response->successful() || empty($responseData['id'])) {
            $errorMsg = $responseData['result']['description'] ?? __('Failed to initialize payment session with gateway.');
            throw new \Exception($errorMsg);
        }

        $checkoutId = $responseData['id'];

        // Save transaction in database
        $transaction = HyperpayTransaction::create([
            'user_id'                 => $user->id,
            'wallet_id'               => $user->wallet?->id,
            'merchant_transaction_id' => $merchantTransactionId,
            'checkout_id'             => $checkoutId,
            'brand'                   => $brand,
            'entity_id'               => $entityId,
            'amount'                  => $amount,
            'currency'                => $currency,
            'status'                  => 'initiated',
            'channel'                 => $channel,
            'ip_address'              => request()->ip(),
            'raw_response'            => $responseData,
        ]);

        $scriptUrl = rtrim($this->getBaseUrl(), '/') . "/v1/paymentWidgets.js?checkoutId={$checkoutId}";

        return [
            'transaction_id'          => $transaction->id,
            'checkout_id'             => $checkoutId,
            'merchant_transaction_id' => $merchantTransactionId,
            'amount'                  => $amount,
            'currency'                => $currency,
            'brand'                   => $brand,
            'widget_brands'           => $this->getWidgetBrands($brand),
            'script_url'              => $scriptUrl,
            'base_url'                => $this->getBaseUrl(),
            'return_url'              => $returnUrl,
        ];
    }

    /**
     * Inquire payment status from HyperPay.
     *
     * @param string $checkoutId
     * @param string $brand
     * @return array
     * @throws \Exception
     */
    public function verifyPayment(string $checkoutId, string $brand = 'mada'): array
    {
        $entityId = $this->getEntityId($brand);
        $accessToken = $this->getAccessToken();

        if (empty($entityId) || empty($accessToken)) {
            throw new \Exception(__('Missing gateway credentials for status verification.'));
        }

        $endpoint = rtrim($this->getBaseUrl(), '/') . "/v1/checkouts/{$checkoutId}/payment";

        $response = Http::withToken($accessToken)
            ->get($endpoint, [
                'entityId' => $entityId,
            ]);

        $data = $response->json();

        Log::info('HyperPay Payment Verification Result:', [
            'checkoutId' => $checkoutId,
            'brand' => $brand,
            'data' => $data,
        ]);

        return $data ?: [];
    }

    /**
     * Check whether HyperPay result code indicates successful transaction.
     * Follows official HyperPay regex specs.
     */
    public function isSuccessCode(?string $code): bool
    {
        if (empty($code)) {
            return false;
        }

        // HyperPay Success Patterns
        // 000.000.000: Transaction succeeded
        // 000.100.110, 000.100.112: Request successfully processed in 'Merchant in Integrator Mode'
        // Pattern: /^(000\.000\.|000\.100\.1|000\.[36]|000\.400\.[12]0)/
        $pattern = '/^(000\.000\.|000\.100\.1|000\.[36]|000\.400\.[12]0)/';
        return (bool) preg_match($pattern, $code);
    }

    /**
     * Atomically process and credit successful payment into user's wallet.
     * Strictly idempotent to avoid double-crediting.
     *
     * @param HyperpayTransaction $transaction
     * @param array $paymentData
     * @return bool
     */
    public function processSuccessfulPayment(HyperpayTransaction $transaction, array $paymentData): bool
    {
        return DB::transaction(function () use ($transaction, $paymentData) {
            // Lock record for atomic update
            $tx = HyperpayTransaction::where('id', $transaction->id)->lockForUpdate()->first();

            if (!$tx) {
                return false;
            }

            // If already processed and paid, exit safely
            if ($tx->status === 'paid') {
                return true;
            }

            $resultCode = $paymentData['result']['code'] ?? null;
            $resultDescription = $paymentData['result']['description'] ?? null;
            $paymentId = $paymentData['id'] ?? null;

            $cardBin = $paymentData['card']['bin'] ?? null;
            $cardLast4 = $paymentData['card']['last4Digits'] ?? null;
            $cardHolder = $paymentData['card']['holder'] ?? null;
            $expiryMonth = $paymentData['card']['expiryMonth'] ?? null;
            $expiryYear = $paymentData['card']['expiryYear'] ?? null;

            // 1. Update Transaction record
            $tx->update([
                'status'              => 'paid',
                'hyperpay_payment_id' => $paymentId,
                'result_code'         => $resultCode,
                'result_description'  => $resultDescription,
                'card_bin'            => $cardBin,
                'card_last4'          => $cardLast4,
                'card_holder'         => $cardHolder,
                'card_expiry_month'   => $expiryMonth,
                'card_expiry_year'    => $expiryYear,
                'raw_response'        => $paymentData,
                'paid_at'             => now(),
            ]);

            // 2. Deposit into User Wallet
            $user = $tx->user;
            if ($user && $user->wallet) {
                $description = __('Online Wallet Deposit via HyperPay (:brand) - Ref: #:ref', [
                    'brand' => $tx->brand_name,
                    'ref' => $tx->merchant_transaction_id,
                ]);

                $this->walletService->adjustBalance(
                    wallet: $user->wallet,
                    amount: $tx->amount,
                    type: 'credit',
                    description: $description,
                );

                // 3. Create an approved DepositRequest for ledger & receipt tracking
                DepositRequest::create([
                    'user_id'         => $user->id,
                    'wallet_id'       => $user->wallet->id,
                    'bank_account_id' => null,
                    'amount'          => $tx->amount,
                    'receipt_path'    => null,
                    'status'          => 'approved',
                    'reviewed_by'     => null,
                    'reviewed_at'     => now(),
                ]);

                // 4. Send Instant Notifications (In-App Database + Push FCM)
                try {
                    $user->notify(new GeneralNotification(
                        __('Deposit Successful'),
                        __('An amount of :amount SAR has been successfully added to your wallet via HyperPay.', ['amount' => number_format($tx->amount, 2)]),
                        ['database', 'fcm'],
                        url('/bidder/wallet')
                    ));

                    // Notify Admins
                    $admins = User::role('admin')->get();
                    if ($admins->isNotEmpty()) {
                        Notification::send($admins, new GeneralNotification(
                            __('Online Deposit Completed'),
                            __('User :name deposited :amount SAR via HyperPay.', [
                                'name' => $user->name,
                                'amount' => number_format($tx->amount, 2),
                            ]),
                            ['database'],
                            url('/admin/wallets/' . $user->wallet->id)
                        ));
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to dispatch deposit notification: ' . $e->getMessage());
                }
            }

            return true;
        });
    }

    /**
     * Mark transaction as failed.
     */
    public function markAsFailed(HyperpayTransaction $transaction, array $paymentData): void
    {
        if ($transaction->status === 'paid') {
            return;
        }

        $transaction->update([
            'status'             => 'failed',
            'result_code'        => $paymentData['result']['code'] ?? 'UNKNOWN_ERROR',
            'result_description' => $paymentData['result']['description'] ?? __('Payment failed or was cancelled by user.'),
            'raw_response'       => $paymentData,
        ]);
    }

    /**
     * Handle incoming asynchronous webhook from HyperPay.
     */
    public function handleWebhook(Request $request): array
    {
        $payload = $request->all();
        Log::info('HyperPay Webhook Received:', $payload);

        // HyperPay webhook sends either encrypted payload or checkout/payment reference
        $checkoutId = $payload['id'] ?? ($payload['checkoutId'] ?? null);
        $merchantTxId = $payload['merchantTransactionId'] ?? null;

        $transaction = null;
        if ($checkoutId) {
            $transaction = HyperpayTransaction::where('checkout_id', $checkoutId)->first();
        } elseif ($merchantTxId) {
            $transaction = HyperpayTransaction::where('merchant_transaction_id', $merchantTxId)->first();
        }

        if (!$transaction) {
            Log::warning('HyperPay Webhook: Transaction not found for payload', $payload);
            return ['status' => 'not_found'];
        }

        // Inquire official status from gateway
        try {
            $paymentData = $this->verifyPayment($transaction->checkout_id, $transaction->brand);
            $code = $paymentData['result']['code'] ?? '';

            if ($this->isSuccessCode($code)) {
                $this->processSuccessfulPayment($transaction, $paymentData);
                return ['status' => 'success'];
            } else {
                $this->markAsFailed($transaction, $paymentData);
                return ['status' => 'failed'];
            }
        } catch (\Exception $e) {
            Log::error('HyperPay Webhook Processing Error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
