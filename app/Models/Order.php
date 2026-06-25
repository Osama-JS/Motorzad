<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'auction_id', 'user_id', 'vehicle_id',
        'bid_amount', 'deposit_amount', 'commission_amount', 'vat_amount', 'total_amount',
        'payment_status', 'payment_method', 'delivery_type', 'delivery_address',
        'delivery_phone', 'notes', 'status', 'paid_at'
    ];

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
