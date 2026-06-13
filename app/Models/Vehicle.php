<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'submitted_by', 'make', 'model', 'year', 'color', 'vin_number',
        'mileage', 'plate_number', 'country_of_origin',
        'fuel_type', 'transmission', 'engine_capacity', 'cylinders',
        'condition', 'description_ar', 'description_en',
        'features', 'issues', 'status', 'rejection_reason',
        'reviewed_by', 'reviewed_at', 'damage_points',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'features'    => 'array',
        'damage_points' => 'array',
        'year'        => 'integer',
        'mileage'     => 'integer',
        'cylinders'   => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function images()
    {
        return $this->hasMany(VehicleImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(VehicleImage::class)->where('is_primary', true);
    }

    public function auctions()
    {
        return $this->hasMany(Auction::class);
    }

    // ── Accessors ──────────────────────────────────────────────────────────

    public function getTitleAttribute(): string
    {
        return "{$this->year} {$this->make} {$this->model}";
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $image = $this->primaryImage ?? $this->images->first();
        return $image ? asset('storage/' . $image->image_path) : null;
    }
}
