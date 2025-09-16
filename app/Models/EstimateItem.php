<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimateItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'estimate_id',
        'category',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'total_price',
        'markup_percentage',
        'markup_amount',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'markup_percentage' => 'decimal:2',
        'markup_amount' => 'decimal:2',
    ];

    // Category constants
    const CATEGORY_LABOR = 'Labor';
    const CATEGORY_MATERIALS = 'Materials';
    const CATEGORY_EQUIPMENT = 'Equipment';
    const CATEGORY_PERMITS = 'Permits';
    const CATEGORY_SUBCONTRACTOR = 'Subcontractor';
    const CATEGORY_OTHER = 'Other';

    /**
     * Get the estimate that owns this item
     */
    public function estimate()
    {
        return $this->belongsTo(Estimate::class);
    }

    /**
     * Calculate total price and markup
     */
    public function calculateTotals(): void
    {
        $this->total_price = $this->quantity * $this->unit_price;
        $this->markup_amount = $this->total_price * ($this->markup_percentage / 100);
    }

    /**
     * Get the final price including markup
     */
    public function getFinalPriceAttribute(): float
    {
        return $this->total_price + $this->markup_amount;
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->calculateTotals();
        });

        static::saved(function ($item) {
            $item->estimate->calculateTotals();
        });

        static::deleted(function ($item) {
            $item->estimate->calculateTotals();
        });
    }
} 