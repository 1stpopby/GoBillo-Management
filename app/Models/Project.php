<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;

    const STATUS_PLANNING = 'planning';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_ON_HOLD = 'on_hold';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    protected $fillable = [
        'company_id',
        'site_id',
        'client_id',
        'manager_id',
        'name',
        'description',
        'status',
        'priority',
        'budget',
        'start_date',
        'end_date',
        'progress',
        'address',
        'postcode',
        'latitude',
        'longitude',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'progress' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_users')
                    ->withPivot('role', 'joined_at')
                    ->withTimestamps();
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_managers', 'project_id', 'manager_id')
                    ->withPivot(['role', 'is_active'])
                    ->withTimestamps();
    }

    public function activeManagers(): BelongsToMany
    {
        return $this->managers()->wherePivot('is_active', true);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function projectExpenses(): HasMany
    {
        return $this->hasMany(ProjectExpense::class);
    }

    public function projectDocuments(): HasMany
    {
        return $this->hasMany(ProjectDocument::class);
    }

    public function projectVariations(): HasMany
    {
        return $this->hasMany(ProjectVariation::class);
    }

    public function projectSnaggings(): HasMany
    {
        return $this->hasMany(ProjectSnagging::class);
    }

    public function cisPayments(): HasMany
    {
        return $this->hasMany(CisPayment::class);
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


    /**
     * Scope: restrict projects to those the given user can view.
     * Managers/Admins can see all company projects; others only projects
     * they are on the team for OR have tasks assigned in.
     */
    public function scopeVisibleToUser($query, ?User $user = null)
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if (method_exists($user, 'canManageProjects') && $user->canManageProjects()) {
            return $query; // already company-scoped by caller
        }

        // Site managers and project managers can only see projects in sites they manage
        if (in_array($user->role, ['site_manager', 'project_manager'])) {
            return $query->whereHas('site', function($siteQuery) use ($user) {
                $siteQuery->where('manager_id', $user->id);
            });
        }

        return $query->where(function ($q) use ($user) {
            $q->whereHas('users', function ($uq) use ($user) {
                $uq->where('users.id', $user->id);
            })->orWhereHas('tasks', function ($tq) use ($user) {
                $tq->where('assigned_to', $user->id);
            });
        });
    }

    // Accessors
    public function getPendingTasksCountAttribute(): int
    {
        return $this->tasks()->where('status', Task::STATUS_PENDING)->count();
    }

    public function getInProgressTasksCountAttribute(): int
    {
        return $this->tasks()->where('status', Task::STATUS_IN_PROGRESS)->count();
    }

    public function getCompletedTasksCountAttribute(): int
    {
        return $this->tasks()->where('status', Task::STATUS_COMPLETED)->count();
    }

    public function getTotalTasksCountAttribute(): int
    {
        return $this->tasks()->count();
    }

    public function getTotalEstimatedCostAttribute(): float
    {
        return (float) $this->tasks()->sum('estimated_cost') ?? 0.0;
    }

    public function getTotalActualCostAttribute(): float
    {
        return (float) $this->tasks()->sum('actual_cost') ?? 0.0;
    }

    public function getCostVarianceAttribute(): float
    {
        return $this->total_actual_cost - $this->total_estimated_cost;
    }

    public function getBudgetUtilizationAttribute(): float
    {
        if (!$this->budget || $this->budget <= 0) {
            return 0.0;
        }
        return ($this->total_actual_cost / $this->budget) * 100;
    }

    public function getBudgetRemainingAttribute(): float
    {
        if (!$this->budget) {
            return 0.0;
        }
        return max(0, $this->budget - $this->total_actual_cost);
    }

    public function getIsOverBudgetAttribute(): bool
    {
        return $this->budget && $this->total_actual_cost > $this->budget;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->end_date && 
               $this->end_date->isPast() && 
               !in_array($this->status, ['completed', 'cancelled']);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'planning' => 'info',
            'in_progress' => 'primary',
            'on_hold' => 'warning',
            'completed' => 'success',
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

    public function getCompletionPercentage(): int
    {
        // If project has a progress field set, use it
        if ($this->progress !== null && $this->progress >= 0 && $this->progress <= 100) {
            return (int) $this->progress;
        }

        // Otherwise calculate based on completed tasks
        $totalTasks = $this->total_tasks_count;
        
        if ($totalTasks === 0) {
            return 0;
        }

        $completedTasks = $this->completed_tasks_count;
        return (int) round(($completedTasks / $totalTasks) * 100);
    }

    /**
     * Get all valid status values
     */
    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_PLANNING,
            self::STATUS_IN_PROGRESS,
            self::STATUS_ON_HOLD,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PLANNING => 'Planning',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_ON_HOLD => 'On Hold',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst(str_replace('_', ' ', $this->status))
        };
    }

    // Helper method to get client through site if not directly assigned
    public function getEffectiveClient()
    {
        // If project has a direct client, use that
        if ($this->client_id) {
            return $this->client;
        }
        
        // Otherwise, use the site's client if available
        if ($this->site && $this->site->client) {
            return $this->site->client;
        }
        
        return null;
    }

    /**
     * Update project coordinates from postcode using GeolocationService
     */
    public function updateCoordinatesFromPostcode(): bool
    {
        if (!$this->postcode) {
            return false;
        }

        $geolocationService = app(\App\Services\GeolocationService::class);
        return $geolocationService->updateProjectCoordinates($this);
    }

    /**
     * Check if project has valid coordinates
     */
    public function hasValidCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Get formatted address with postcode
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([$this->address, $this->postcode]);
        return implode(', ', $parts);
    }
}