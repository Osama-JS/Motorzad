<?php

namespace App\Services;

use App\Events\AuctionStatusChangedEvent;
use App\Events\BidPlacedEvent;
use App\Events\UserOutbidEvent;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BiddingService
{
    /**
     * Place a live bid on an auction with strict pessimistic locking and real-time broadcasting.
     *
     * @param Auction $auction
     * @param User $user
     * @param float $amount
     * @param bool $isAutoBid
     * @param float|null $maxAutoBid
     * @param string|null $ip
     * @param string|null $userAgent
     * @return array
     * @throws Exception
     */
    public function placeBid(
        Auction $auction,
        User $user,
        float $amount,
        bool $isAutoBid = false,
        ?float $maxAutoBid = null,
        ?string $ip = null,
        ?string $userAgent = null
    ): array {
        // 1. Basic user validation
        if ($user->status !== 'approved') {
            return [
                'success' => false,
                'status_code' => 403,
                'message' => __('Please complete identity verification to place bids.')
            ];
        }

        // 2. Validate auto bid limits
        if ($isAutoBid) {
            if (!$maxAutoBid) {
                return [
                    'success' => false,
                    'status_code' => 422,
                    'message' => __('Please specify a maximum auto bid limit.')
                ];
            }
            if ($maxAutoBid < $amount) {
                return [
                    'success' => false,
                    'status_code' => 422,
                    'message' => __('Maximum auto bid limit must be greater than or equal to the bid amount.')
                ];
            }
        }

        // 3. Execute with Pessimistic Row Lock inside a database transaction
        return DB::transaction(function () use ($auction, $user, $amount, $isAutoBid, $maxAutoBid, $ip, $userAgent) {
            // Lock the auction row for update to prevent concurrent race conditions
            $lockedAuction = Auction::where('id', $auction->id)->lockForUpdate()->first();

            if (!$lockedAuction) {
                return [
                    'success' => false,
                    'status_code' => 404,
                    'message' => __('Auction not found.')
                ];
            }

            // Check if auction is paused
            if ($lockedAuction->is_paused) {
                return [
                    'success' => false,
                    'status_code' => 422,
                    'message' => __('This auction is currently paused by admin.')
                ];
            }

            // Check if user is blocked from this auction
            $isBlocked = DB::table('auction_blocklists')
                ->where('auction_id', $lockedAuction->id)
                ->where('user_id', $user->id)
                ->exists();

            if ($isBlocked) {
                return [
                    'success' => false,
                    'status_code' => 403,
                    'message' => __('You have been blocked from participating in this auction.')
                ];
            }

            // Check if auction is live and has not ended
            if ($lockedAuction->status !== 'live' || now()->gt($lockedAuction->end_time)) {
                return [
                    'success' => false,
                    'status_code' => 422,
                    'message' => __('This auction is not accepting bids.')
                ];
            }

            // Current price evaluation from the locked row
            $currentPrice = (float) $lockedAuction->current_price;
            $minBid = $currentPrice + (float) $lockedAuction->min_bid_increment;

            // Smart increment detection:
            // If the incoming $amount is less than currentPrice, the client sent an INCREMENT amount (e.g. 500)
            // rather than the cumulative total price (e.g. 10500). Calculate cumulative total automatically.
            if ($amount > 0 && $amount < $currentPrice) {
                $amount = $currentPrice + $amount;
            }

            if ($amount < $minBid) {
                return [
                    'success' => false,
                    'status_code' => 422,
                    'message' => __('Bid must be at least :amount SAR.', ['amount' => number_format($minBid, 2)])
                ];
            }

            // Wallet balance check
            $wallet = $user->wallet;
            $newTotalRequired = $isAutoBid ? $maxAutoBid : $amount;

            $currentActiveBid = $lockedAuction->bids()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            $currentActiveBidAmount = 0;
            if ($currentActiveBid) {
                $currentActiveBidAmount = $currentActiveBid->is_auto_bid
                    ? max($currentActiveBid->amount, $currentActiveBid->max_auto_bid)
                    : $currentActiveBid->amount;
            }

            $additionalRequired = max(0, $newTotalRequired - $currentActiveBidAmount);

            if (!$wallet || $wallet->available_balance < $additionalRequired) {
                return [
                    'success' => false,
                    'status_code' => 422,
                    'message' => __('Insufficient available balance. You need at least :amount SAR available in your wallet to place this bid.', [
                        'amount' => number_format($additionalRequired, 2)
                    ])
                ];
            }

            // Get current highest active bid
            $currentHighestBid = $lockedAuction->bids()->where('status', 'active')->first();
            $previousHighestBidderId = $currentHighestBid ? $currentHighestBid->user_id : null;

            // Setup proxy war variables
            $rivalBid = null;
            if ($currentHighestBid && $currentHighestBid->user_id !== $user->id && $currentHighestBid->is_auto_bid) {
                $rivalBid = $currentHighestBid;
            }

            $newBidAmount = $amount;
            $isExtended = false;

            if ($rivalBid) {
                $userMax = $isAutoBid ? $maxAutoBid : $newBidAmount;
                $rivalMax = (float) $rivalBid->max_auto_bid;

                if ($userMax > $rivalMax) {
                    // User wins the proxy war against rival
                    $newBidAmount = min($rivalMax + $lockedAuction->min_bid_increment, $userMax);

                    // Mark rival as outbid
                    $rivalBid->update(['status' => 'outbid']);
                    $this->notifyOutbidUser($rivalBid->user_id, $lockedAuction, $newBidAmount);
                } else {
                    // Rival wins the proxy war automatically
                    $rivalNewAmount = min($userMax + $lockedAuction->min_bid_increment, $rivalMax);

                    // User's bid is recorded but immediately marked outbid
                    $lockedAuction->bids()->where('status', 'active')->where('user_id', $user->id)->update(['status' => 'outbid']);
                    $userOutbidBid = Bid::create([
                        'auction_id'   => $lockedAuction->id,
                        'user_id'      => $user->id,
                        'amount'       => $newBidAmount,
                        'is_auto_bid'  => $isAutoBid,
                        'max_auto_bid' => $maxAutoBid,
                        'status'       => 'outbid',
                        'ip_address'   => $ip,
                        'user_agent'   => $userAgent,
                    ]);
                    $lockedAuction->increment('bids_count');

                    // Rival's automated response bid
                    $lockedAuction->bids()->where('status', 'active')->where('user_id', $rivalBid->user_id)->update(['status' => 'outbid']);
                    $winningBid = Bid::create([
                        'auction_id'   => $lockedAuction->id,
                        'user_id'      => $rivalBid->user_id,
                        'amount'       => $rivalNewAmount,
                        'is_auto_bid'  => true,
                        'max_auto_bid' => $rivalMax,
                        'status'       => 'active',
                        'ip_address'   => 'system',
                        'user_agent'   => 'proxy-bid',
                    ]);
                    $lockedAuction->increment('bids_count');

                    // Update auction winning values
                    $lockedAuction->update([
                        'winning_bid_amount' => $rivalNewAmount,
                        'winner_id'          => $rivalBid->user_id
                    ]);

                    // Check auto-extend
                    $isExtended = $this->handleAutoExtend($lockedAuction);

                    // Notify the user they were outbid
                    $this->notifyOutbidUser($user->id, $lockedAuction, $rivalNewAmount);

                    // Broadcast the winning rival bid to all clients
                    $rivalUser = User::find($rivalBid->user_id);
                    if ($rivalUser) {
                        broadcast(new BidPlacedEvent($lockedAuction, $winningBid, $rivalUser, $isExtended));
                    }

                    return [
                        'success'            => true,
                        'status_code'        => 200,
                        'new_price'          => $rivalNewAmount,
                        'bids_count'         => $lockedAuction->bids_count,
                        'time_left_seconds'  => max(0, (int) now()->diffInSeconds($lockedAuction->end_time, false)),
                        'end_time'           => $lockedAuction->end_time ? $lockedAuction->end_time->toISOString() : null,
                        'is_extended'        => $isExtended,
                        'message'            => __('Your bid was placed, but you have been immediately outbid by an automatic proxy bid!')
                    ];
                }
            }

            // Normal flow (or User won the proxy war)
            // Mark previous active bids of other users as outbid and notify them
            if ($previousHighestBidderId && $previousHighestBidderId !== $user->id) {
                $lockedAuction->bids()
                    ->where('status', 'active')
                    ->where('user_id', $previousHighestBidderId)
                    ->update(['status' => 'outbid']);

                $this->notifyOutbidUser($previousHighestBidderId, $lockedAuction, $newBidAmount);
            }

            // Mark user's own previous active bids as outbid
            $lockedAuction->bids()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->update(['status' => 'outbid']);

            // Create new active bid
            $bid = Bid::create([
                'auction_id'   => $lockedAuction->id,
                'user_id'      => $user->id,
                'amount'       => $newBidAmount,
                'is_auto_bid'  => $isAutoBid,
                'max_auto_bid' => $maxAutoBid,
                'status'       => 'active',
                'ip_address'   => $ip,
                'user_agent'   => $userAgent,
            ]);

            // Update bids count and winning bid
            $lockedAuction->increment('bids_count');
            $lockedAuction->update([
                'winning_bid_amount' => $newBidAmount,
                'winner_id'          => $user->id
            ]);

            // Check auto-extend
            $isExtended = $this->handleAutoExtend($lockedAuction);

            // Broadcast live event to all listeners on auction.{id}
            broadcast(new BidPlacedEvent($lockedAuction, $bid, $user, $isExtended));

            return [
                'success'             => true,
                'status_code'         => 200,
                'bid_id'              => $bid->id,
                'bidder_display_name' => $user->masked_bidder_name,
                'new_price'           => $newBidAmount,
                'bid_increment'       => max(0, (float) ($newBidAmount - $currentPrice)),
                'bids_count'          => $lockedAuction->bids_count,
                'time_left_seconds'   => max(0, (int) now()->diffInSeconds($lockedAuction->end_time, false)),
                'end_time'            => $lockedAuction->end_time ? $lockedAuction->end_time->toISOString() : null,
                'is_extended'         => $isExtended,
                'message'             => $isAutoBid
                    ? __('Automatic proxy bid setup successfully at :amount (Max Limit: :max)!', [
                        'amount' => number_format($newBidAmount, 2),
                        'max'    => number_format($maxAutoBid, 2)
                    ])
                    : __('Your bid has been placed successfully!')
            ];
        });
    }

    /**
     * Notify an outbid user both via WebSocket and Push Notification (FCM / DB).
     */
    protected function notifyOutbidUser(int $outbidUserId, Auction $auction, float $newPrice): void
    {
        try {
            // 1. Broadcast immediate private channel event
            broadcast(new UserOutbidEvent($outbidUserId, $auction, $newPrice));

            // 2. Send in-app database + push notification
            $outbidUser = User::find($outbidUserId);
            if ($outbidUser) {
                $title = "⚡ تم تجاوز عرضك في المزاد!";
                $body = "قام مزايد آخر بتقديم عرض أعلى على {$auction->title_ar} بقيمة " . number_format($newPrice) . " ريال. زايد الآن قبل انتهاء الوقت!";
                $actionUrl = url("/bidder/auctions/{$auction->id}");

                $outbidUser->notify(new GeneralNotification(
                    $title,
                    $body,
                    ['database', 'fcm'],
                    $actionUrl,
                    [
                        'auction_id' => $auction->id,
                        'new_price'  => $newPrice,
                        'type'       => 'outbid'
                    ]
                ));
            }
        } catch (Exception $e) {
            Log::error("Failed to notify outbid user #{$outbidUserId}: " . $e->getMessage());
        }
    }

    /**
     * Auto-extend the auction if bid arrives in the final minutes (anti-sniping protection).
     */
    protected function handleAutoExtend(Auction $auction): bool
    {
        if (!$auction->auto_extension_enabled || !$auction->end_time) {
            return false;
        }

        $now = now();
        $diffMinutes = $now->diffInMinutes($auction->end_time, false);
        $thresholdMinutes = (int) ($auction->auto_extension_trigger_minutes ?: 2);
        $extensionMinutes = (int) ($auction->auto_extension_minutes ?: 3);

        // If time left is less than or equal to the trigger threshold, extend
        if ($diffMinutes <= $thresholdMinutes && $diffMinutes >= 0) {
            $newEndTime = $auction->end_time->copy()->addMinutes($extensionMinutes);
            $auction->update([
                'end_time' => $newEndTime,
                'extension_count' => ($auction->extension_count ?? 0) + 1
            ]);

            // Broadcast status change regarding time extension
            broadcast(new AuctionStatusChangedEvent($auction, 'live', "تم تمديد وقت المزاد بمقدار {$extensionMinutes} دقائق لتنافس المزايدين في اللحظات الأخيرة."));

            return true;
        }

        return false;
    }
}
