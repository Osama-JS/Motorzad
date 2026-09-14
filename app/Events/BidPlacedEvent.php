<?php

namespace App\Events;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BidPlacedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $auctionId;
    public $bidId;
    public $currentPrice;
    public $minNextBid;
    public $bidsCount;
    public $bidderId;
    public $bidderDisplayName;
    public $bidIncrement;
    public $timeRemainingSeconds;
    public $endTime;
    public $isExtended;
    public $placedAt;

    /**
     * Create a new event instance.
     */
    public function __construct(Auction $auction, Bid $bid, User $bidder, bool $isExtended = false)
    {
        $this->auctionId = $auction->id;
        $this->bidId = $bid->id;
        $this->currentPrice = (float) $bid->amount;

        // Calculate increment over previous bid or start price
        $prevBid = $auction->bids()
            ->where('id', '<', $bid->id)
            ->whereIn('status', ['active', 'outbid'])
            ->orderBy('id', 'desc')
            ->first();
        $prevAmount = $prevBid ? (float) $prevBid->amount : (float) $auction->start_price;
        $diff = (float) $bid->amount - $prevAmount;
        $this->bidIncrement = $diff > 0 ? $diff : (float) $auction->min_bid_increment;

        $this->minNextBid = (float) ($bid->amount + $auction->min_bid_increment);
        $this->bidsCount = (int) $auction->bids_count;
        $this->bidderId = $bidder->id;
        $this->bidderDisplayName = $bidder->masked_bidder_name;
        $this->timeRemainingSeconds = max(0, (int) now()->diffInSeconds($auction->end_time, false));
        $this->endTime = $auction->end_time ? $auction->end_time->toISOString() : null;
        $this->isExtended = $isExtended;
        $this->placedAt = $bid->created_at ? $bid->created_at->toISOString() : now()->toISOString();
    }

    /**
     * The channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel("auction.{$this->auctionId}")
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'bid.placed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'auction_id'             => $this->auctionId,
            'bid_id'                 => $this->bidId,
            'current_price'          => $this->currentPrice,
            'bid_increment'          => $this->bidIncrement,
            'min_next_bid'           => $this->minNextBid,
            'bids_count'             => $this->bidsCount,
            'bidder_id'              => $this->bidderId,
            'bidder_display_name'    => $this->bidderDisplayName,
            'time_remaining_seconds' => $this->timeRemainingSeconds,
            'end_time'               => $this->endTime,
            'is_extended'            => $this->isExtended,
            'placed_at'              => $this->placedAt,
        ];
    }
}
