<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuctionDeposit extends Model
{
    protected $fillable = [
        'auction_id', 'user_id', 'wallet_transaction_id',
        'amount', 'status', 'released_at',
    ];

    protected $casts = [
        'amount'      => 'float',
        'released_at' => 'datetime',
    ];

    public function auction()   { return $this->belongsTo(Auction::class); }
    public function user()      { return $this->belongsTo(User::class); }
    public function transaction(){ return $this->belongsTo(WalletTransaction::class, 'wallet_transaction_id'); }
}
