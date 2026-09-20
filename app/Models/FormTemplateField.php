<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormTemplateField extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_template_id',
        'name',
        'label',
        'description',
        'type',
        'is_required',
        'options',
        'validation_rules',
        'order',
        'depends_on_field_name',
        'depends_on_value',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'options' => 'array',
        'order' => 'integer',
    ];

    /**
     * Get the template that owns the field.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(FormTemplate::class, 'form_template_id');
    }
}
