<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HyperpayTransaction extends Model
{
    use HasFactory;

    protected $table = 'hyperpay_transactions';

    protected $fillable = [
        'user_id',
        'wallet_id',
        'merchant_transaction_id',
        'checkout_id',
        'hyperpay_payment_id',
        'brand',
        'entity_id',
        'amount',
        'currency',
        'status',
        'result_code',
        'result_description',
        'card_bin',
        'card_last4',
        'card_holder',
        'card_expiry_month',
        'card_expiry_year',
        'channel',
        'ip_address',
        'raw_response',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'raw_response' => 'array',
        'paid_at' => 'datetime',
    ];

    /**
     * The user who made the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The wallet that was credited.
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Scope for successful/paid transactions.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for pending/initiated transactions.
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['initiated', 'pending']);
    }

    /**
     * Get human readable brand name.
     */
    public function getBrandNameAttribute(): string
    {
        return match ($this->brand) {
            'mada' => 'مدى (Mada)',
            'visa_master' => 'فيزا / ماستركارد',
            'apple_pay' => 'Apple Pay',
            default => strtoupper($this->brand ?? 'CARD'),
        };
    }

    /**
     * Get masked card representation.
     */
    public function getMaskedCardAttribute(): ?string
    {
        if ($this->card_last4) {
            return ($this->card_bin ? $this->card_bin . '******' : '**** **** **** ') . $this->card_last4;
        }
        return null;
    }

    /**
     * Check if transaction is completed.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
