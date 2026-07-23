<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'role_ar',
        'role_en',
        'text_ar',
        'text_en',
        'avatar_init',
        'avatar_init_en',
        'is_active',
    ];
}
