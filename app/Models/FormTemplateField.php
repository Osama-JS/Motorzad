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

    protected $appends = ['ui_hint', 'data_source_url'];

    public function getDataSourceUrlAttribute()
    {
        if (in_array($this->type, ['select', 'multi-select'])) {
            $name = strtolower($this->name);
            
            // Auto-magically link known large lists to remote API endpoints
            if (str_contains($name, 'city') || str_contains($name, 'cities')) {
                return url('/api/locations/cities');
            } elseif (str_contains($name, 'country') || str_contains($name, 'countries')) {
                return url('/api/locations/countries');
            } elseif (str_contains($name, 'brand') || str_contains($name, 'make')) {
                return url('/api/vehicles/brands');
            }
            
            // If there are no static options, assume it's a remote field that needs dynamic loading
            if (empty($this->options)) {
                return url('/api/options/' . $this->name);
            }
        }

        return null;
    }

    public function getUiHintAttribute()
    {
        $hint = ['keyboard' => 'default'];
        $name = strtolower($this->name);
        
        if ($this->type === 'file') {
            $hint['picker'] = 'image_and_pdf';
        } elseif ($this->type === 'checkbox') {
            $hint['widget'] = 'switch';
        } elseif (in_array($this->type, ['select', 'multi-select'])) {
            $hint['widget'] = 'bottom_sheet_picker';
        } else {
            if (str_contains($name, 'phone') || str_contains($name, 'mobile')) {
                $hint['keyboard'] = 'phone_pad';
                $hint['prefix'] = '+966';
            } elseif (str_contains($name, 'email')) {
                $hint['keyboard'] = 'email_address';
            } elseif (str_contains($name, 'amount') || str_contains($name, 'price')) {
                $hint['keyboard'] = 'decimal_pad';
                $hint['suffix'] = 'SAR';
            } elseif (str_contains($name, 'id') || str_contains($name, 'number') || str_contains($name, 'zip')) {
                $hint['keyboard'] = 'number_pad';
            }
        }

        return $hint;
    }

    /**
     * Get the template that owns the field.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(FormTemplate::class, 'form_template_id');
    }
}
