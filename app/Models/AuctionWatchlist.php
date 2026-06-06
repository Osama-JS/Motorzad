<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuctionWatchlist extends Model
{
    protected $table = 'auction_watchlist';

    protected $fillable = ['auction_id', 'user_id'];

    public function auction() { return $this->belongsTo(Auction::class); }
    public function user()    { return $this->belongsTo(User::class); }
}
