<?php

namespace Tests\Feature;

use App\Models\HyperpayTransaction;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Services\HyperPayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HyperPayIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \Spatie\Permission\Models\Role::findOrCreate('admin');
        \Spatie\Permission\Models\Role::findOrCreate('bidder');

        // Seed basic settings for test
        Setting::set('hyperpay_enabled', '1');
        Setting::set('hyperpay_mode', 'test');
        Setting::set('hyperpay_test_base_url', 'https://eu-test.oppwa.com');
        Setting::set('hyperpay_access_token', 'OGE4MmRlNGExMjM0NTY3ODkwMW...');
        Setting::set('hyperpay_entity_id_mada', '8a8294174b7e6ca0014b829e00000001');
        Setting::set('hyperpay_entity_id_visa_master', '8a8294174b7e6ca0014b829e00000002');
        Setting::set('hyperpay_entity_id_apple_pay', '8a8294174b7e6ca0014b829e00000003');
        Setting::set('hyperpay_min_deposit', '10');
        Setting::set('hyperpay_max_deposit', '500000');
    }

    /**
     * Test HyperPayService checkout preparation with mocked HTTP.
     */
    public function test_hyperpay_service_prepare_checkout()
    {
        Http::fake([
            'https://eu-test.oppwa.com/v1/checkouts' => Http::response([
                'result' => [
                    'code' => '000.200.100',
                    'description' => 'successfully created checkout',
                ],
                'id' => '8A8294174B7E6CA0014B829E12345678',
            ], 200),
        ]);

        $user = User::factory()->create();
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        $service = app(HyperPayService::class);
        $result = $service->prepareCheckout($user, 250.00, 'mada', 'web');

        $this->assertEquals('8A8294174B7E6CA0014B829E12345678', $result['checkout_id']);
        $this->assertEquals(250.00, $result['amount']);
        $this->assertEquals('mada', $result['brand']);

        $this->assertDatabaseHas('hyperpay_transactions', [
            'user_id' => $user->id,
            'checkout_id' => '8A8294174B7E6CA0014B829E12345678',
            'amount' => 250.00,
            'status' => 'initiated',
        ]);
    }

    /**
     * Test successful payment verification and wallet crediting with idempotency.
     */
    public function test_hyperpay_successful_payment_credits_wallet_idempotently()
    {
        $user = User::factory()->create();
        $wallet = $user->wallet;
        $wallet->update(['balance' => 0.00]);

        $transaction = HyperpayTransaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'merchant_transaction_id' => 'MZ-HP-TEST-001',
            'checkout_id' => 'TEST_CHECKOUT_SUCCESS_1',
            'brand' => 'mada',
            'entity_id' => '8a8294174b7e6ca0014b829e00000001',
            'amount' => 300.00,
            'currency' => 'SAR',
            'status' => 'initiated',
        ]);

        $mockPaymentData = [
            'id' => '8ac7a4a0869...TEST_PAY_ID',
            'result' => [
                'code' => '000.000.000',
                'description' => 'Transaction succeeded',
            ],
            'card' => [
                'bin' => '588845',
                'last4Digits' => '1234',
                'holder' => 'Fahad Al-Otaibi',
                'expiryMonth' => '12',
                'expiryYear' => '2028',
            ],
        ];

        $service = app(HyperPayService::class);

        // First process: should credit wallet
        $firstResult = $service->processSuccessfulPayment($transaction, $mockPaymentData);
        $this->assertTrue($firstResult);

        $wallet->refresh();
        $this->assertEquals(300.00, (float) $wallet->balance); // 0 + 300 = 300

        $transaction->refresh();
        $this->assertEquals('paid', $transaction->status);
        $this->assertEquals('1234', $transaction->card_last4);
        $this->assertNotNull($transaction->paid_at);

        // Second process (replay): MUST NOT double-credit
        $secondResult = $service->processSuccessfulPayment($transaction, $mockPaymentData);
        $this->assertTrue($secondResult);

        $wallet->refresh();
        $this->assertEquals(300.00, (float) $wallet->balance); // Still 300! No double-crediting!
    }

    /**
     * Test API checkout endpoint.
     */
    public function test_api_checkout_endpoint()
    {
        Http::fake([
            'https://eu-test.oppwa.com/v1/checkouts' => Http::response([
                'result' => ['code' => '000.200.100', 'description' => 'checkout created'],
                'id' => 'API_TEST_CHECKOUT_999',
            ], 200),
        ]);

        $user = User::factory()->create();
        Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/wallet/hyperpay/checkout', [
            'amount' => 500.00,
            'brand'  => 'mada',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'checkout_id' => 'API_TEST_CHECKOUT_999',
                    'amount' => 500.00,
                    'brand' => 'mada',
                ],
            ]);
    }

    /**
     * Test API verification endpoint.
     */
    public function test_api_verify_endpoint()
    {
        Http::fake([
            'https://eu-test.oppwa.com/v1/checkouts/API_VERIFY_CHECKOUT_123/payment*' => Http::response([
                'id' => 'PAYMENT_ID_ABC',
                'result' => ['code' => '000.000.000', 'description' => 'Transaction succeeded'],
                'card' => ['bin' => '411111', 'last4Digits' => '1111'],
            ], 200),
        ]);

        $user = User::factory()->create();
        $wallet = $user->wallet;
        $wallet->update(['balance' => 0]);

        $transaction = HyperpayTransaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'merchant_transaction_id' => 'MZ-HP-API-TEST',
            'checkout_id' => 'API_VERIFY_CHECKOUT_123',
            'brand' => 'visa_master',
            'entity_id' => '8a8294174b7e6ca0014b829e00000002',
            'amount' => 150.00,
            'currency' => 'SAR',
            'status' => 'initiated',
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/wallet/hyperpay/verify', [
            'checkout_id' => 'API_VERIFY_CHECKOUT_123',
            'brand' => 'visa_master',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => 'paid',
                    'amount' => 150.00,
                    'new_wallet_balance' => 150.00,
                ],
            ]);

        $this->assertEquals(150.00, (float) $wallet->fresh()->balance);
    }

    /**
     * Test Webhook endpoint.
     */
    public function test_webhook_endpoint()
    {
        Http::fake([
            'https://eu-test.oppwa.com/v1/checkouts/WEBHOOK_CHECKOUT_XYZ/payment*' => Http::response([
                'id' => 'PAYMENT_ID_WEBHOOK',
                'result' => ['code' => '000.000.000', 'description' => 'Transaction succeeded'],
                'card' => ['bin' => '400000', 'last4Digits' => '0002'],
            ], 200),
        ]);

        $user = User::factory()->create();
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        HyperpayTransaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'merchant_transaction_id' => 'MZ-HP-WH-TEST',
            'checkout_id' => 'WEBHOOK_CHECKOUT_XYZ',
            'brand' => 'mada',
            'entity_id' => '8a8294174b7e6ca0014b829e00000001',
            'amount' => 1000.00,
            'currency' => 'SAR',
            'status' => 'initiated',
        ]);

        $response = $this->postJson('/api/hyperpay/webhook', [
            'id' => 'WEBHOOK_CHECKOUT_XYZ',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'result' => 'success',
            ]);

        $this->assertEquals(1000.00, (float) $wallet->fresh()->balance);
    }
}
