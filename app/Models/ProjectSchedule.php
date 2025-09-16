<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class ProjectSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'project_id',
        'task_name',
        'description',
        'start_date',
        'end_date',
        'actual_start',
        'actual_end',
        'duration_days',
        'progress',
        'status',
        'priority',
        'assigned_to',
        'parent_task_id',
        'dependencies',
        'is_milestone',
        'color',
        'order_index',
        'resources',
        'estimated_hours',
        'actual_hours',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'actual_start' => 'date',
        'actual_end' => 'date',
        'dependencies' => 'array',
        'resources' => 'array',
        'is_milestone' => 'boolean',
        'progress' => 'float',
        'estimated_hours' => 'float',
        'actual_hours' => 'float',
    ];

    // Status constants
    const STATUS_NOT_STARTED = 'not_started';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DELAYED = 'delayed';
    const STATUS_ON_HOLD = 'on_hold';
    const STATUS_CANCELLED = 'cancelled';

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_CRITICAL = 'critical';

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($schedule) {
            if (auth()->check() && !$schedule->company_id) {
                $schedule->company_id = auth()->user()->company_id;
            }
            
            // Calculate duration if not set
            if (!$schedule->duration_days && $schedule->start_date && $schedule->end_date) {
                $schedule->duration_days = $schedule->start_date->diffInDays($schedule->end_date) + 1;
            }
            
            // Set default color if not provided
            if (!$schedule->color) {
                $colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6'];
                $schedule->color = $colors[array_rand($colors)];
            }
        });

        static::updating(function ($schedule) {
            // Update duration when dates change
            if ($schedule->isDirty(['start_date', 'end_date'])) {
                $schedule->duration_days = $schedule->start_date->diffInDays($schedule->end_date) + 1;
            }
            
            // Auto-update status based on progress
            if ($schedule->isDirty('progress')) {
                if ($schedule->progress >= 100) {
                    $schedule->status = self::STATUS_COMPLETED;
                    if (!$schedule->actual_end) {
                        $schedule->actual_end = now();
                    }
                } elseif ($schedule->progress > 0 && $schedule->status === self::STATUS_NOT_STARTED) {
                    $schedule->status = self::STATUS_IN_PROGRESS;
                    if (!$schedule->actual_start) {
                        $schedule->actual_start = now();
                    }
                }
            }
            
            // Check if task is delayed
            if ($schedule->end_date < now() && $schedule->status !== self::STATUS_COMPLETED) {
                $schedule->status = self::STATUS_DELAYED;
            }
        });
    }

    /**
     * Relationships
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(ProjectSchedule::class, 'parent_task_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(ProjectSchedule::class, 'parent_task_id')->orderBy('order_index');
    }

    public function dependentTasks()
    {
        return ProjectSchedule::where('project_id', $this->project_id)
            ->whereJsonContains('dependencies', $this->id)
            ->get();
    }

    /**
     * Scopes
     */
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?? auth()->user()->company_id ?? null;
        
        if ($companyId) {
            return $query->where('company_id', $companyId);
        }
        
        return $query;
    }

    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeMilestones($query)
    {
        return $query->where('is_milestone', true);
    }

    public function scopeRootTasks($query)
    {
        return $query->whereNull('parent_task_id');
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->where('start_date', '<=', now()->addDays($days))
                    ->where('status', '!=', self::STATUS_COMPLETED);
    }

    public function scopeOverdue($query)
    {
        return $query->where('end_date', '<', now())
                    ->where('status', '!=', self::STATUS_COMPLETED);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Accessors
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->end_date < now() && $this->status !== self::STATUS_COMPLETED;
    }

    public function getDaysRemainingAttribute(): int
    {
        if ($this->status === self::STATUS_COMPLETED) {
            return 0;
        }
        
        return max(0, now()->diffInDays($this->end_date, false));
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) {
            return 0;
        }
        
        return now()->diffInDays($this->end_date);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_NOT_STARTED => '#6B7280',
            self::STATUS_IN_PROGRESS => '#3B82F6',
            self::STATUS_COMPLETED => '#10B981',
            self::STATUS_DELAYED => '#EF4444',
            self::STATUS_ON_HOLD => '#F59E0B',
            self::STATUS_CANCELLED => '#9CA3AF',
            default => '#6B7280',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_NOT_STARTED => 'Not Started',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_DELAYED => 'Delayed',
            self::STATUS_ON_HOLD => 'On Hold',
            self::STATUS_CANCELLED => 'Cancelled',
            default => 'Unknown',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            self::PRIORITY_LOW => '#10B981',
            self::PRIORITY_MEDIUM => '#3B82F6',
            self::PRIORITY_HIGH => '#F59E0B',
            self::PRIORITY_CRITICAL => '#EF4444',
            default => '#6B7280',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return ucfirst($this->priority);
    }

    public function getFormattedProgressAttribute(): string
    {
        return number_format($this->progress, 0) . '%';
    }

    /**
     * Methods
     */
    public function canStart(): bool
    {
        if (!$this->dependencies || count($this->dependencies) === 0) {
            return true;
        }
        
        $dependentTasks = ProjectSchedule::whereIn('id', $this->dependencies)->get();
        
        foreach ($dependentTasks as $task) {
            if ($task->status !== self::STATUS_COMPLETED) {
                return false;
            }
        }
        
        return true;
    }

    public function updateProgress($progress): bool
    {
        $this->progress = min(100, max(0, $progress));
        return $this->save();
    }

    public function markAsCompleted(): bool
    {
        $this->progress = 100;
        $this->status = self::STATUS_COMPLETED;
        $this->actual_end = now();
        return $this->save();
    }

    public function getGanttData(): array
    {
        return [
            'id' => $this->id,
            'text' => $this->task_name,
            'start_date' => $this->start_date->format('Y-m-d'),
            'end_date' => $this->end_date->format('Y-m-d'),
            'duration' => $this->duration_days,
            'progress' => $this->progress / 100,
            'parent' => $this->parent_task_id ?? 0,
            'color' => $this->color,
            'open' => true,
            'type' => $this->is_milestone ? 'milestone' : 'task',
        ];
    }

    public function getCalendarEvent(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->task_name,
            'start' => $this->start_date->format('Y-m-d'),
            'end' => $this->end_date->addDay()->format('Y-m-d'), // FullCalendar expects exclusive end date
            'color' => $this->color,
            'extendedProps' => [
                'progress' => $this->progress,
                'status' => $this->status,
                'priority' => $this->priority,
                'assigned_to' => $this->assignedTo?->name,
                'is_milestone' => $this->is_milestone,
            ],
        ];
    }
}


