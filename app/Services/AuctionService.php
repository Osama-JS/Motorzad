<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\Order;
use App\Models\User;
use App\Models\PlatformCommission;
use App\Events\AuctionStatusChangedEvent;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AuctionService
{
    public function __construct(protected WalletService $walletService) {}

    /**
     * Start a scheduled auction and broadcast its live status.
     */
    public function startAuction(Auction $auction): void
    {
        if ($auction->status === 'live') {
            return;
        }

        $auction->update([
            'status' => 'live'
        ]);

        // Broadcast to WebSocket channel auction.{id}
        try {
            broadcast(new AuctionStatusChangedEvent($auction, 'live', __('The auction is now live and accepting bids!')));
        } catch (\Throwable $e) {
            Log::warning("WebSocket broadcast failed on startAuction for auction {$auction->id}: " . $e->getMessage());
        }

        // Notify watchlist users or interested bidders
        try {
            $watchers = $auction->watchlist()->with('user')->get()->pluck('user')->filter();
            if ($watchers->isNotEmpty()) {
                Notification::send($watchers, new GeneralNotification(
                    __('بدأ المزاد الآن!'),
                    __('المزاد :title متاح الآن للمزايدة الحية. سارع بالمشاركة!', ['title' => $auction->title]),
                    ['database', 'fcm'],
                    url('/bidder/auctions/' . $auction->id)
                ));
            }
        } catch (\Throwable $e) {
            Log::warning("Failed to send start auction notifications: " . $e->getMessage());
        }
    }

    /**
     * End an auction, determine the winner, create an Order, and broadcast status.
     * Called by scheduled command or just-in-time state synchronizer.
     */
    public function endAuction(Auction $auction): void
    {
        if (!in_array($auction->status, ['live'])) {
            return;
        }

        DB::transaction(function () use ($auction) {
            $highestBid = Bid::where('auction_id', $auction->id)
                ->where('status', 'active')
                ->orderByDesc('amount')
                ->first();

            if (!$highestBid) {
                // No bids — mark auction as ended
                $auction->update(['status' => 'ended']);
                $this->releaseAllDeposits($auction);

                try {
                    broadcast(new AuctionStatusChangedEvent($auction, 'ended', __('The auction has ended without bids.')));
                } catch (\Throwable $e) {
                    Log::warning("WebSocket broadcast failed on endAuction (no bids) for auction {$auction->id}: " . $e->getMessage());
                }
                return;
            }

            // Check reserve price
            if ($auction->reserve_price && $highestBid->amount < $auction->reserve_price) {
                // Reserve not met
                $auction->update(['status' => 'ended']);
                $this->releaseAllDeposits($auction);

                try {
                    broadcast(new AuctionStatusChangedEvent($auction, 'ended', __('The auction has ended as the reserve price was not met.')));
                } catch (\Throwable $e) {
                    Log::warning("WebSocket broadcast failed on endAuction (reserve not met) for auction {$auction->id}: " . $e->getMessage());
                }
                return;
            }

            // Mark all other bids as outbid
            Bid::where('auction_id', $auction->id)
                ->where('id', '!=', $highestBid->id)
                ->update(['status' => 'outbid']);

            // Mark winning bid
            $highestBid->update(['status' => 'won']);

            // Calculate commission
            $commissionRate = $auction->commission_rate ?? 0;
            $commissionAmount = ($highestBid->amount * $commissionRate) / 100;

            // Update auction as sold
            $auction->update([
                'status'              => 'sold',
                'winner_id'           => $highestBid->user_id,
                'winning_bid_amount'  => $highestBid->amount,
                'sold_at'             => now(),
                'commission_amount'   => $commissionAmount,
            ]);

            // Create Order record for vehicle checkout & tracking
            Order::firstOrCreate(
                ['auction_id' => $auction->id],
                [
                    'user_id'           => $highestBid->user_id,
                    'vehicle_id'        => $auction->vehicle_id,
                    'bid_amount'        => $highestBid->amount,
                    'deposit_amount'    => (float) $auction->deposit_amount,
                    'commission_amount' => $commissionAmount,
                    'vat_amount'        => 0,
                    'total_amount'      => $highestBid->amount + $commissionAmount,
                    'payment_status'    => 'pending',
                    'status'            => 'pending',
                ]
            );

            // Deduct commission from winner's wallet if available
            if ($commissionAmount > 0 && $highestBid->user && $highestBid->user->wallet) {
                $this->walletService->adjustBalance(
                    wallet: $highestBid->user->wallet,
                    amount: $commissionAmount,
                    type: 'debit',
                    description: __('Auction commission for: ') . $auction->title,
                );
            }

            // Release deposits for non-winners
            $this->releaseNonWinnerDeposits($auction, $highestBid->user_id);

            // Record commission in platform_commissions
            DB::table('platform_commissions')->insert([
                'auction_id'     => $auction->id,
                'user_id'        => $highestBid->user_id,
                'amount'         => $commissionAmount,
                'rate'           => $commissionRate,
                'type'           => 'dynamic',
                'payment_status' => 'paid',
                'completed_at'   => now(),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // Broadcast live event to all listeners
            try {
                broadcast(new AuctionStatusChangedEvent(
                    $auction, 
                    'sold', 
                    __('تم بيع المركبة في المزاد بنجاح للمزايد :name بمبلغ :amount ريال.', [
                        'name' => $highestBid->user ? $highestBid->user->masked_bidder_name : '#' . $highestBid->user_id,
                        'amount' => number_format($highestBid->amount, 2)
                    ])
                ));
            } catch (\Throwable $e) {
                Log::warning("WebSocket broadcast failed on endAuction (sold) for auction {$auction->id}: " . $e->getMessage());
            }

            // Send notification to winner
            try {
                if ($highestBid->user) {
                    $highestBid->user->notify(new GeneralNotification(
                        __('تهانينا! لقد فزت بالمزاد! 🎉'),
                        __('مبروك! لقد فزت بالمزاد :title بمبلغ :amount ريال. يرجى استكمال إجراءات الطلب.', [
                            'title' => $auction->title,
                            'amount' => number_format($highestBid->amount, 2)
                        ]),
                        ['database', 'fcm'],
                        url('/bidder/orders')
                    ));
                }
            } catch (\Throwable $e) {
                Log::warning("Failed to notify winner of auction {$auction->id}: " . $e->getMessage());
            }

            // Send notification to seller
            try {
                if ($auction->creator) {
                    $auction->creator->notify(new GeneralNotification(
                        __('تم بيع مركبتك في المزاد!'),
                        __('تم الانتهاء من مزاد مركبتك :title وبيعه بمبلغ :amount ريال.', [
                            'title' => $auction->title,
                            'amount' => number_format($highestBid->amount, 2)
                        ]),
                        ['database', 'fcm'],
                        url('/bidder/garage/auctions')
                    ));
                }
            } catch (\Throwable $e) {
                Log::warning("Failed to notify seller of auction {$auction->id}: " . $e->getMessage());
            }
        });
    }

    /**
     * Cancel an auction, release all deposits, and broadcast status.
     */
    public function cancelAuction(Auction $auction): void
    {
        DB::transaction(function () use ($auction) {
            $auction->update(['status' => 'cancelled']);
            $this->releaseAllDeposits($auction);

            try {
                broadcast(new AuctionStatusChangedEvent($auction, 'cancelled', __('تم إلغاء هذا المزاد وإرجاع كافة الضمانات.')));
            } catch (\Throwable $e) {
                Log::warning("WebSocket broadcast failed on cancelAuction for auction {$auction->id}: " . $e->getMessage());
            }
        });
    }

    /**
     * Release deposits for all non-winners.
     */
    private function releaseNonWinnerDeposits(Auction $auction, int $winnerId): void
    {
        $deposits = $auction->deposits()
            ->where('user_id', '!=', $winnerId)
            ->where('status', 'held')
            ->with('user.wallet')
            ->get();

        foreach ($deposits as $deposit) {
            if ($deposit->user && $deposit->user->wallet) {
                $this->walletService->adjustBalance(
                    wallet: $deposit->user->wallet,
                    amount: $deposit->amount,
                    type: 'credit',
                    description: __('Deposit refund for auction: ') . $auction->title,
                );
            }

            $deposit->update([
                'status'      => 'released',
                'released_at' => now(),
            ]);

            // Notify outbid user
            try {
                if ($deposit->user) {
                    $deposit->user->notify(new GeneralNotification(
                        __('استرداد ضمان المزاد'),
                        __('تم إلغاء حجز مبلغ الضمان :amount ريال وإعادته لرصيد محفظتك المتاح لمزاد :title.', [
                            'amount' => number_format($deposit->amount, 2),
                            'title' => $auction->title
                        ]),
                        ['database'],
                        url('/bidder/wallet')
                    ));
                }
            } catch (\Throwable $e) {
                // Ignore notification error
            }
        }
    }

    /**
     * Release all deposits (when auction has no winner or is cancelled).
     */
    private function releaseAllDeposits(Auction $auction): void
    {
        $deposits = $auction->deposits()
            ->where('status', 'held')
            ->with('user.wallet')
            ->get();

        foreach ($deposits as $deposit) {
            if ($deposit->user && $deposit->user->wallet) {
                $this->walletService->adjustBalance(
                    wallet: $deposit->user->wallet,
                    amount: $deposit->amount,
                    type: 'credit',
                    description: __('Deposit refund — auction ended: ') . $auction->title,
                );
            }

            $deposit->update([
                'status'      => 'released',
                'released_at' => now(),
            ]);

            try {
                if ($deposit->user) {
                    $deposit->user->notify(new GeneralNotification(
                        __('استرداد ضمان المزاد'),
                        __('تم إلغاء حجز مبلغ الضمان :amount ريال وإعادته لرصيد محفظتك المتاح.', [
                            'amount' => number_format($deposit->amount, 2)
                        ]),
                        ['database'],
                        url('/bidder/wallet')
                    ));
                }
            } catch (\Throwable $e) {
                // Ignore
            }
        }
    }
}
