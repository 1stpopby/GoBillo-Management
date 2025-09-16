<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'color',
        'icon',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // Scopes
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?? auth()->user()?->company_id ?? null;
        
        if ($companyId === null) {
            // If no company ID is available, don't filter (for superadmin or console contexts)
            return $query;
        }
        
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Helper methods
    public function getTasksCount(): int
    {
        return $this->tasks()->count();
    }

    public function getActiveTasksCount(): int
    {
        return $this->tasks()->whereIn('status', [Task::STATUS_PENDING, Task::STATUS_IN_PROGRESS])->count();
    }

    public function getCompletedTasksCount(): int
    {
        return $this->tasks()->where('status', Task::STATUS_COMPLETED)->count();
    }

    // Default categories for seeding
    public static function getDefaultCategories(): array
    {
        return [
            ['name' => 'Kitchen', 'description' => 'Kitchen renovation and installation tasks', 'color' => '#ef4444', 'icon' => 'bi-cup-hot', 'sort_order' => 1],
            ['name' => 'Bathroom', 'description' => 'Bathroom renovation and plumbing tasks', 'color' => '#3b82f6', 'icon' => 'bi-droplet', 'sort_order' => 2],
            ['name' => 'Electrical', 'description' => 'Electrical installation and maintenance', 'color' => '#f59e0b', 'icon' => 'bi-lightning', 'sort_order' => 3],
            ['name' => 'Plumbing', 'description' => 'Plumbing installation and repairs', 'color' => '#06b6d4', 'icon' => 'bi-wrench', 'sort_order' => 4],
            ['name' => 'Flooring', 'description' => 'Floor installation and renovation', 'color' => '#8b5cf6', 'icon' => 'bi-grid', 'sort_order' => 5],
            ['name' => 'Painting', 'description' => 'Interior and exterior painting', 'color' => '#10b981', 'icon' => 'bi-palette', 'sort_order' => 6],
            ['name' => 'HVAC', 'description' => 'Heating, ventilation, and air conditioning', 'color' => '#f97316', 'icon' => 'bi-thermometer', 'sort_order' => 7],
            ['name' => 'Roofing', 'description' => 'Roof installation and repairs', 'color' => '#6b7280', 'icon' => 'bi-house-up', 'sort_order' => 8],
            ['name' => 'Windows & Doors', 'description' => 'Window and door installation', 'color' => '#ec4899', 'icon' => 'bi-door-open', 'sort_order' => 9],
            ['name' => 'General', 'description' => 'General construction and maintenance tasks', 'color' => '#64748b', 'icon' => 'bi-tools', 'sort_order' => 10]
        ];
    }
}