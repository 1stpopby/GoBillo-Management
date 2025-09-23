<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Site extends Model
{
    use HasFactory;

    const STATUS_PLANNING = 'planning';
    const STATUS_ACTIVE = 'active';
    const STATUS_ON_HOLD = 'on_hold';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    protected $fillable = [
        'company_id',
        'client_id',
        'manager_id',
        'name',
        'description',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'total_budget',
        'start_date',
        'expected_completion_date',
        'actual_completion_date',
        'status',
        'priority',
        'site_details',
        'site_manager_contact',
        'site_manager_phone',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'expected_completion_date' => 'date',
        'actual_completion_date' => 'date',
        'site_details' => 'array',
        'is_active' => 'boolean',
        'total_budget' => 'decimal:2'
    ];

    // Relationships
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'site_managers', 'site_id', 'manager_id')
                    ->withPivot(['role', 'is_active'])
                    ->withTimestamps();
    }

    public function activeManagers(): BelongsToMany
    {
        return $this->managers()->wherePivot('is_active', true);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_site_allocations')
                    ->withPivot(['allocated_from', 'allocated_until', 'allocation_type', 'responsibilities', 'allocation_percentage', 'status', 'notes'])
                    ->withTimestamps();
    }

    public function activeEmployees(): BelongsToMany
    {
        return $this->employees()->wherePivot('status', 'active');
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

    // Accessors
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zip_code
        ]);

        return implode(', ', $parts);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'planning' => 'info',
            'active' => 'success',
            'on_hold' => 'warning',
            'completed' => 'primary',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'success',
            'medium' => 'info',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'secondary'
        };
    }

    public function getProgressAttribute(): int
    {
        // Use efficient query instead of loading full relationship
        if (!$this->relationLoaded('projects')) {
            $projectStats = $this->projects()->selectRaw('COUNT(*) as total, SUM(progress) as total_progress')->first();
            $totalProjects = $projectStats->total ?? 0;
            $totalProgress = $projectStats->total_progress ?? 0;
        } else {
            $totalProjects = $this->projects->count();
            $totalProgress = $this->projects->sum('progress');
        }
        
        if ($totalProjects === 0) {
            return 0;
        }

        return round($totalProgress / $totalProjects);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->expected_completion_date && 
               $this->expected_completion_date->isPast() && 
               !in_array($this->status, ['completed', 'cancelled']);
    }

    // Helper methods
    public function getTotalProjectsCount(): int
    {
        return $this->projects()->count();
    }

    public function getActiveProjectsCount(): int
    {
        return $this->projects()->where('status', 'in_progress')->count();
    }

    public function getCompletedProjectsCount(): int
    {
        return $this->projects->where('status', Project::STATUS_COMPLETED)->count();
    }

    public function getTotalTasksCount(): int
    {
        return $this->projects->sum(function ($project) {
            return $project->tasks->count();
        });
    }
}