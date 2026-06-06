<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformCommission extends Model
{
    protected $fillable = [
        'auction_id', 'user_id', 'amount', 'rate',
        'type', 'payment_status', 'notes', 'completed_at',
    ];

    protected $casts = [
        'amount'       => 'float',
        'rate'         => 'float',
        'completed_at' => 'datetime',
    ];

    public function auction() { return $this->belongsTo(Auction::class); }
    public function user()    { return $this->belongsTo(User::class); }
}
