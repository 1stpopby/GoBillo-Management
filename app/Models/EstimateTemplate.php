<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimateTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'category',
        'default_tax_rate',
        'default_terms',
        'default_notes',
        'is_active',
        'usage_count',
    ];

    protected $casts = [
        'default_tax_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the company that owns the template
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the template items
     */
    public function items()
    {
        return $this->hasMany(EstimateTemplateItem::class)->orderBy('sort_order');
    }

    /**
     * Scope for company isolation
     */
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?? auth()->user()->company_id ?? null;
        
        if (auth()->user() && auth()->user()->isSuperAdmin()) {
            return $query; // Superadmin can see all templates
        }
        
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Create estimate from template
     */
    public function createEstimate($clientId, $additionalData = []): Estimate
    {
        $estimate = Estimate::create(array_merge([
            'company_id' => $this->company_id,
            'client_id' => $clientId,
            'tax_rate' => $this->default_tax_rate,
            'terms' => $this->default_terms,
            'notes' => $this->default_notes,
            'issue_date' => now()->toDateString(),
            'valid_until' => now()->addDays(30)->toDateString(),
            'currency' => 'GBP',
        ], $additionalData));

        // Copy template items to estimate
        foreach ($this->items as $index => $templateItem) {
            EstimateItem::create([
                'estimate_id' => $estimate->id,
                'category' => $templateItem->category,
                'description' => $templateItem->description,
                'quantity' => $templateItem->default_quantity,
                'unit' => $templateItem->unit,
                'unit_price' => $templateItem->default_unit_price,
                'markup_percentage' => $templateItem->markup_percentage,
                'notes' => $templateItem->notes,
                'sort_order' => $index,
            ]);
        }

        $estimate->calculateTotals();
        $this->incrementUsage();

        return $estimate;
    }
} 