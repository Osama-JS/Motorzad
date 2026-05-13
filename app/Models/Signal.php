<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Signal extends Model
{
    protected $fillable = [
        'coin_id',
        'type',
        'entry_price',
        'take_profit',
        'stop_loss',
        'timeframe',
        'status'
    ];

    public function coin()
    {
        return $this->belongsTo(Coin::class);
    }

}
