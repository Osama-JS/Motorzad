<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Auction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_id', 'image', 'created_by', 'title_ar', 'title_en',
        'description_ar', 'description_en', 'location_ar', 'location_en',
        'start_price', 'reserve_price', 'min_bid_increment', 'buy_now_price',
        'deposit_amount', 'deposit_required',
        'start_time', 'end_time', 'auto_extend_minutes',
        'status', 'winner_id', 'winning_bid_amount', 'sold_at',
        'commission_rate', 'commission_amount',
        'is_featured', 'views_count', 'bids_count',
        'is_paused',
    ];

    protected $casts = [
        'start_time'       => 'datetime',
        'end_time'         => 'datetime',
        'sold_at'          => 'datetime',
        'deposit_required' => 'boolean',
        'is_featured'      => 'boolean',
        'is_paused'        => 'boolean',
        'start_price'      => 'float',
        'reserve_price'    => 'float',
        'min_bid_increment'=> 'float',
        'buy_now_price'    => 'float',
        'deposit_amount'   => 'float',
        'winning_bid_amount'=> 'float',
        'commission_rate'  => 'float',
        'commission_amount'=> 'float',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function bids()
    {
        return $this->hasMany(Bid::class)->latest();
    }

    public function images()
    {
        return $this->hasMany(AuctionImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(AuctionImage::class)->where('is_primary', true);
    }

    public function highestBid()
    {
        return $this->hasOne(Bid::class)->ofMany('amount', 'max')->where('status', 'active');
    }

    public function deposits()
    {
        return $this->hasMany(AuctionDeposit::class);
    }

    public function watchlist()
    {
        return $this->hasMany(AuctionWatchlist::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    // ── Accessors & Helpers ────────────────────────────────────────────────

    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : $this->title_en;
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        // Check new AuctionImage relationship first
        $image = $this->primaryImage ?? $this->images->first();
        if ($image) {
            return asset('storage/' . $image->image_path);
        }
        
        // Fallback to legacy single image column
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        
        return null;
    }

    public function getLocationAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->location_ar ?? $this->location_en) : ($this->location_en ?? $this->location_ar);
    }

    public function getIsLiveAttribute(): bool
    {
        return $this->status === 'live'
            && !$this->is_paused
            && now()->between($this->start_time, $this->end_time);
    }

    public function getTimeRemainingAttribute(): int
    {
        if (!$this->is_live) return 0;
        return max(0, now()->diffInSeconds($this->end_time, false));
    }

    public function getCurrentPriceAttribute(): float
    {
        return $this->highestBid?->amount ?? $this->start_price;
    }

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
