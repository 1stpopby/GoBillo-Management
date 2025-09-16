<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'color',
        'is_active',
        'requires_receipt',
        'is_mileage',
        'default_mileage_rate',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_receipt' => 'boolean',
        'is_mileage' => 'boolean',
        'default_mileage_rate' => 'decimal:2',
    ];

    /**
     * Get the company that owns the category
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get expenses in this category
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category', 'name');
    }

    /**
     * Scope for company isolation
     */
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?? auth()->user()->company_id ?? null;
        
        if (auth()->user() && auth()->user()->isSuperAdmin()) {
            return $query; // Superadmin can see all categories
        }
        
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get default categories for a company
     */
    public static function getDefaultCategories(): array
    {
        return [
            [
                'name' => 'Fuel',
                'description' => 'Vehicle fuel and gas expenses',
                'color' => '#dc3545',
                'requires_receipt' => true,
                'is_mileage' => false,
            ],
            [
                'name' => 'Materials',
                'description' => 'Construction materials and supplies',
                'color' => '#28a745',
                'requires_receipt' => true,
                'is_mileage' => false,
            ],
            [
                'name' => 'Equipment',
                'description' => 'Tool rentals and equipment purchases',
                'color' => '#ffc107',
                'requires_receipt' => true,
                'is_mileage' => false,
            ],
            [
                'name' => 'Mileage',
                'description' => 'Vehicle mileage reimbursement',
                'color' => '#17a2b8',
                'requires_receipt' => false,
                'is_mileage' => true,
                'default_mileage_rate' => 0.65, // IRS standard rate
            ],
            [
                'name' => 'Meals',
                'description' => 'Business meals and entertainment',
                'color' => '#fd7e14',
                'requires_receipt' => true,
                'is_mileage' => false,
            ],
            [
                'name' => 'Permits',
                'description' => 'Building permits and licenses',
                'color' => '#6f42c1',
                'requires_receipt' => true,
                'is_mileage' => false,
            ],
            [
                'name' => 'Subcontractor',
                'description' => 'Subcontractor payments',
                'color' => '#20c997',
                'requires_receipt' => true,
                'is_mileage' => false,
            ],
            [
                'name' => 'Office',
                'description' => 'Office supplies and administrative costs',
                'color' => '#6c757d',
                'requires_receipt' => true,
                'is_mileage' => false,
            ],
            [
                'name' => 'Other',
                'description' => 'Miscellaneous business expenses',
                'color' => '#343a40',
                'requires_receipt' => true,
                'is_mileage' => false,
            ],
        ];
    }

    /**
     * Create default categories for a company
     */
    public static function createDefaultCategories($companyId): void
    {
        $defaultCategories = self::getDefaultCategories();
        
        foreach ($defaultCategories as $index => $categoryData) {
            self::create(array_merge($categoryData, [
                'company_id' => $companyId,
                'sort_order' => $index,
            ]));
        }
    }
} 