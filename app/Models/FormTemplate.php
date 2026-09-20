<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the fields associated with the form template.
     */
    public function fields(): HasMany
    {
        return $this->hasMany(FormTemplateField::class)->orderBy('order');
    }

    /**
     * Get the seller requests submitted using this template.
     */
    public function sellerRequests(): HasMany
    {
        return $this->hasMany(SellerRequest::class);
    }
}
