<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'admin_notes',
    ];

    /**
     * Get the user that submitted the request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
