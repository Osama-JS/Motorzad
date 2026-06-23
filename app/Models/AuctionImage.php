<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuctionImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'auction_id',
        'image_path',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }
}
