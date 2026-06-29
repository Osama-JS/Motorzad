<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleModel extends Model
{
    use HasFactory;

    protected $fillable = ['make_id', 'name_ar', 'name_en', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function make()
    {
        return $this->belongsTo(VehicleMake::class, 'make_id');
    }
}
