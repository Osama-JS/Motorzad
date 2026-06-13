<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\Bid;
use App\Models\PlatformCommission;
use Illuminate\Support\Facades\DB;

class AuctionService
{
    public function __construct(protected WalletService $walletService) {}

    /**
     * End an auction and determine the winner.
     * Called by a scheduled job (EndAuctionJob).
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
                // No bids — cancel auction
                $auction->update(['status' => 'ended']);
                $this->releaseAllDeposits($auction);
                return;
            }

            // Check reserve price
            if ($auction->reserve_price && $highestBid->amount < $auction->reserve_price) {
                // Reserve not met
                $auction->update(['status' => 'ended']);
                $this->releaseAllDeposits($auction);
                return;
            }

            // Mark all other bids as outbid
            Bid::where('auction_id', $auction->id)
                ->where('id', '!=', $highestBid->id)
                ->update(['status' => 'outbid']);

            // Mark winning bid
            $highestBid->update(['status' => 'won']);

            // Calculate commission
            $commissionAmount = ($highestBid->amount * $auction->commission_rate) / 100;

            // Update auction as sold
            $auction->update([
                'status'              => 'sold',
                'winner_id'           => $highestBid->user_id,
                'winning_bid_amount'  => $highestBid->amount,
                'sold_at'             => now(),
                'commission_amount'   => $commissionAmount,
            ]);

            // Deduct commission from winner's wallet
            $this->walletService->adjustBalance(
                wallet: $highestBid->user->wallet,
                amount: $commissionAmount,
                type: 'debit',
                description: __('Auction commission for: ') . $auction->title,
            );

            // Release deposits for non-winners
            $this->releaseNonWinnerDeposits($auction, $highestBid->user_id);

            // Record commission
            DB::table('platform_commissions')->insert([
                'auction_id'     => $auction->id,
                'user_id'        => $highestBid->user_id,
                'amount'         => $commissionAmount,
                'rate'           => $auction->commission_rate,
                'type'           => 'dynamic',
                'payment_status' => 'paid',
                'completed_at'   => now(),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        });
    }

    /**
     * Cancel an auction and release all deposits.
     */
    public function cancelAuction(Auction $auction): void
    {
        DB::transaction(function () use ($auction) {
            $auction->update(['status' => 'cancelled']);
            $this->releaseAllDeposits($auction);
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
            $this->walletService->adjustBalance(
                wallet: $deposit->user->wallet,
                amount: $deposit->amount,
                type: 'credit',
                description: __('Deposit refund for auction: ') . $auction->title,
            );

            $deposit->update([
                'status'      => 'released',
                'released_at' => now(),
            ]);
        }
    }

    /**
     * Release all deposits (when auction has no winner).
     */
    private function releaseAllDeposits(Auction $auction): void
    {
        $deposits = $auction->deposits()
            ->where('status', 'held')
            ->with('user.wallet')
            ->get();

        foreach ($deposits as $deposit) {
            $this->walletService->adjustBalance(
                wallet: $deposit->user->wallet,
                amount: $deposit->amount,
                type: 'credit',
                description: __('Deposit refund — auction ended: ') . $auction->title,
            );

            $deposit->update([
                'status'      => 'released',
                'released_at' => now(),
            ]);
        }
    }
}
