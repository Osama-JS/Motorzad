<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'submitted_by',
        'make_ar', 'make_en',
        'model_ar', 'model_en',
        'year',
        'color_ar', 'color_en',
        'vin_number',
        'mileage', 'plate_number', 'country_of_origin',
        'fuel_type', 'transmission', 'engine_capacity', 'cylinders',
        'condition', 'description_ar', 'description_en',
        'features',
        'issues_ar', 'issues_en',
        'status', 'rejection_reason',
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

    public function getMakeAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->make_ar ?? $this->make_en) : ($this->make_en ?? $this->make_ar);
    }

    public function getModelAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->model_ar ?? $this->model_en) : ($this->model_en ?? $this->model_ar);
    }

    public function getColorAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->color_ar ?? $this->color_en) : ($this->color_en ?? $this->color_ar);
    }

    public function getIssuesAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->issues_ar ?? $this->issues_en) : ($this->issues_en ?? $this->issues_ar);
    }

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
