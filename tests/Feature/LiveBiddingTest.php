<?php

namespace Tests\Feature;

use App\Events\BidPlacedEvent;
use App\Events\UserOutbidEvent;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Wallet;
use App\Services\BiddingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class LiveBiddingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \Spatie\Permission\Models\Role::findOrCreate('bidder');
        \Spatie\Permission\Models\Role::findOrCreate('admin');
    }

    protected function createTestAuction(float $startPrice = 50000, float $minIncrement = 1000): Auction
    {
        $vehicle = Vehicle::create([
            'vin' => 'TESTVIN' . rand(10000, 99999),
            'make_ar' => 'مرسيدس',
            'make_en' => 'Mercedes',
            'model_ar' => 'إي كلاس',
            'model_en' => 'E-Class',
            'year' => 2023,
            'mileage' => 15000,
            'color_ar' => 'أسود',
            'color_en' => 'Black',
            'condition' => 'good',
        ]);

        $admin = User::firstOrCreate(['email' => 'admin_test@motorzad.com'], [
            'name' => 'Admin Test',
            'status' => 'approved',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');

        return Auction::create([
            'vehicle_id' => $vehicle->id,
            'created_by' => $admin->id,
            'title_ar' => 'مرسيدس إي كلاس 2023 تجريبي',
            'title_en' => 'Mercedes E-Class 2023 Demo',
            'start_price' => $startPrice,
            'current_price' => $startPrice,
            'min_bid_increment' => $minIncrement,
            'deposit_amount' => 5000,
            'status' => 'live',
            'start_time' => now()->subHour(),
            'end_time' => now()->addHours(2),
            'bids_count' => 0,
        ]);
    }

    protected function createBidder(string $name, float $walletBalance = 200000): User
    {
        $user = User::factory()->create([
            'name' => $name,
            'status' => 'approved',
        ]);
        $user->assignRole('bidder');

        Wallet::updateOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => $walletBalance,
                'total_deposits' => $walletBalance,
            ]
        );

        return $user;
    }

    /**
     * Test placing a bid broadcasts BidPlacedEvent with masked bidder name.
     */
    public function test_bid_placement_broadcasts_event_with_masked_bidder_name()
    {
        Event::fake([BidPlacedEvent::class, UserOutbidEvent::class]);

        $auction = $this->createTestAuction(50000, 1000);
        $user = $this->createBidder('سعد محمد الغامدي', 100000);

        $biddingService = app(BiddingService::class);
        $result = $biddingService->placeBid($auction, $user, 51000);

        $this->assertTrue($result['success']);
        $this->assertEquals(51000, $result['new_price']);
        $this->assertEquals(1, $result['bids_count']);

        // Assert event was broadcast with masked display name
        Event::assertDispatched(BidPlacedEvent::class, function ($event) use ($auction, $user) {
            return $event->auctionId === $auction->id
                && $event->currentPrice == 51000
                && $event->bidderId === $user->id
                && str_contains($event->bidderDisplayName, 'س***');
        });

        // Verify database state
        $auction->refresh();
        $this->assertEquals(51000, $auction->current_price);
        $this->assertEquals(1, $auction->bids_count);
    }

    /**
     * Test that low or stale bids are rejected.
     */
    public function test_stale_or_low_bids_are_rejected()
    {
        $auction = $this->createTestAuction(50000, 1000);
        $user = $this->createBidder('خالد العتيبي', 100000);

        $biddingService = app(BiddingService::class);

        // Attempt bid equal to current price (must be at least start_price + increment)
        $result = $biddingService->placeBid($auction, $user, 50000);
        $this->assertFalse($result['success']);
        $this->assertEquals(422, $result['status_code']);

        // Attempt bid below current price
        $result2 = $biddingService->placeBid($auction, $user, 45000);
        $this->assertFalse($result2['success']);
    }

    /**
     * Test that outbid user receives UserOutbidEvent.
     */
    public function test_outbid_user_receives_outbid_event()
    {
        Event::fake([BidPlacedEvent::class, UserOutbidEvent::class]);

        $auction = $this->createTestAuction(50000, 1000);
        $userA = $this->createBidder('أحمد الشمري', 100000);
        $userB = $this->createBidder('فهد الدوسري', 100000);

        $biddingService = app(BiddingService::class);

        // User A bids 51,000
        $biddingService->placeBid($auction, $userA, 51000);

        // User B bids 55,000
        $resultB = $biddingService->placeBid($auction, $userB, 55000);
        $this->assertTrue($resultB['success']);

        // Assert User A received UserOutbidEvent
        Event::assertDispatched(UserOutbidEvent::class, function ($event) use ($userA, $auction) {
            return $event->userId === $userA->id
                && $event->auctionId === $auction->id
                && $event->newPrice == 55000;
        });
    }

    /**
     * Test automated proxy bidding resolves wars and notifies counter-bidders.
     */
    public function test_auto_bidding_proxy_auto_responds()
    {
        Event::fake([BidPlacedEvent::class, UserOutbidEvent::class]);

        $auction = $this->createTestAuction(50000, 1000);
        $userA = $this->createBidder('سلطان المطيري', 100000);
        $userB = $this->createBidder('ياسر القحطاني', 100000);

        $biddingService = app(BiddingService::class);

        // User A places an auto bid with max limit of 60,000
        $resultA = $biddingService->placeBid($auction, $userA, 51000, true, 60000);
        $this->assertTrue($resultA['success']);

        // User B manually bids 53,000
        $resultB = $biddingService->placeBid($auction, $userB, 53000);
        $this->assertTrue($resultB['success']);

        // Since User A has auto bid up to 60,000, system auto-places 54,000 for User A
        $auction->refresh();
        $this->assertEquals(54000, $auction->current_price);
        $this->assertEquals($userA->id, $auction->winner_id);

        // User B should receive UserOutbidEvent
        Event::assertDispatched(UserOutbidEvent::class, function ($event) use ($userB, $auction) {
            return $event->userId === $userB->id
                && $event->auctionId === $auction->id
                && $event->newPrice == 54000;
        });
    }
}
