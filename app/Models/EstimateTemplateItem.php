<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimateTemplateItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'estimate_template_id',
        'category',
        'description',
        'default_quantity',
        'unit',
        'default_unit_price',
        'markup_percentage',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'default_quantity' => 'decimal:2',
        'default_unit_price' => 'decimal:2',
        'markup_percentage' => 'decimal:2',
    ];

    /**
     * Get the template that owns this item
     */
    public function template()
    {
        return $this->belongsTo(EstimateTemplate::class, 'estimate_template_id');
    }

    /**
     * Get the estimated total price
     */
    public function getEstimatedTotalAttribute(): float
    {
        $basePrice = $this->default_quantity * $this->default_unit_price;
        $markup = $basePrice * ($this->markup_percentage / 100);
        return $basePrice + $markup;
    }
} 