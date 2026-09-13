<?php

namespace App\Events;

use App\Models\Auction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuctionStatusChangedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $auctionId;
    public $status;
    public $isPaused;
    public $winningBidAmount;
    public $winnerId;
    public $endTime;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Auction $auction, string $status, ?string $message = null)
    {
        $this->auctionId = $auction->id;
        $this->status = $status;
        $this->isPaused = (bool) $auction->is_paused;
        $this->winningBidAmount = (float) $auction->winning_bid_amount;
        $this->winnerId = $auction->winner_id;
        $this->endTime = $auction->end_time ? $auction->end_time->toISOString() : null;
        $this->message = $message;
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
        return 'auction.status.changed';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'auction_id'         => $this->auctionId,
            'status'             => $this->status,
            'is_paused'          => $this->isPaused,
            'winning_bid_amount' => $this->winningBidAmount,
            'winner_id'          => $this->winnerId,
            'end_time'           => $this->endTime,
            'message'            => $this->message,
        ];
    }
}
