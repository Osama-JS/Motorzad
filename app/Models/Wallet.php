<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'total_deposits',
        'total_withdrawals',
        'debt_ceiling',
        'debt_usage',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function depositRequests()
    {
        return $this->hasMany(DepositRequest::class);
    }

    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    /**
     * Calculate the frozen balance from active bids on live auctions.
     * The frozen amount is either the bid amount or max_auto_bid, whichever is higher.
     */
    public function getFrozenBalanceAttribute()
    {
        return Bid::where('user_id', $this->user_id)
            ->where('status', 'active')
            ->whereHas('auction', function ($query) {
                $query->where('status', 'live')
                      ->where('start_time', '<=', now())
                      ->where('end_time', '>=', now())
                      ->where('is_paused', false);
            })
            ->get()
            ->sum(function ($bid) {
                return $bid->is_auto_bid ? max($bid->amount, $bid->max_auto_bid) : $bid->amount;
            });
    }

    /**
     * Calculate the available balance (Total Balance - Frozen Balance).
     */
    public function getAvailableBalanceAttribute()
    {
        return max(0, $this->balance - $this->frozen_balance);
    }

    /**
     * Get the list of active bids that contribute to the frozen balance.
     */
    public function getFrozenBidsAttribute()
    {
        return Bid::where('user_id', $this->user_id)
            ->where('status', 'active')
            ->whereHas('auction', function ($query) {
                $query->where('status', 'live')
                      ->where('start_time', '<=', now())
                      ->where('end_time', '>=', now())
                      ->where('is_paused', false);
            })
            ->with('auction')
            ->get();
    }
}
