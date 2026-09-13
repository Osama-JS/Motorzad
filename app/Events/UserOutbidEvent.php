<?php

namespace App\Events;

use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserOutbidEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $auctionId;
    public $auctionTitle;
    public $newPrice;
    public $minNextBid;
    public $timeRemainingSeconds;
    public $actionUrl;

    /**
     * Create a new event instance.
     */
    public function __construct(int $outbidUserId, Auction $auction, float $newPrice)
    {
        $this->userId = $outbidUserId;
        $this->auctionId = $auction->id;
        $this->auctionTitle = $auction->title_ar ?: ($auction->title_en ?: 'مزاد مركبة');
        $this->newPrice = $newPrice;
        $this->minNextBid = $newPrice + $auction->min_bid_increment;
        $this->timeRemainingSeconds = max(0, (int) now()->diffInSeconds($auction->end_time, false));
        $this->actionUrl = url("/bidder/auctions/{$auction->id}");
    }

    /**
     * The channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("App.Models.User.{$this->userId}")
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'user.outbid';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'auction_id'             => $this->auctionId,
            'auction_title'          => $this->auctionTitle,
            'new_price'              => $this->newPrice,
            'min_next_bid'           => $this->minNextBid,
            'time_remaining_seconds' => $this->timeRemainingSeconds,
            'action_url'             => $this->actionUrl,
            'message'                => "⚠️ تنبيه عاجل: قام مزايد آخر بتجاوز عرضك على {$this->auctionTitle}! السعر الحالي: " . number_format($this->newPrice) . " ريال."
        ];
    }
}
