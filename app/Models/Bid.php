<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    use HasFactory;

    protected $fillable = [
        'auction_id', 'user_id', 'amount',
        'is_auto_bid', 'max_auto_bid', 'status',
        'ip_address', 'user_agent',
    ];

    protected $casts = [
        'amount'       => 'float',
        'max_auto_bid' => 'float',
        'is_auto_bid'  => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
