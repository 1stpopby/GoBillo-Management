<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectSnagging extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'company_id',
        'task_id',
        'reported_by',
        'item_number',
        'title',
        'description',
        'location',
        'category',
        'severity',
        'status',
        'identified_date',
        'target_completion_date',
        'actual_completion_date',
        'assigned_to',
        'resolution_notes',
        'photos_before',
        'photos_after',
        'trade_responsible',
        'cost_to_fix',
        'client_reported',
        'resolved_by',
        'resolved_at'
    ];

    protected $casts = [
        'identified_date' => 'date',
        'target_completion_date' => 'date',
        'actual_completion_date' => 'date',
        'cost_to_fix' => 'decimal:2',
        'client_reported' => 'boolean',
        'resolved_at' => 'datetime',
        'photos_before' => 'array',
        'photos_after' => 'array'
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // Scopes
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?? auth()->user()->company_id ?? null;
        
        if (auth()->user() && auth()->user()->isSuperAdmin()) {
            return $query;
        }
        
        return $query->where('company_id', $companyId);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress']);
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeOverdue($query)
    {
        return $query->where('target_completion_date', '<', now())
                    ->whereNotIn('status', ['resolved', 'closed']);
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'resolved' => 'success',
            'closed' => 'primary',
            'in_progress' => 'warning',
            'deferred' => 'secondary',
            'open' => 'danger',
            default => 'secondary'
        };
    }

    public function getSeverityColorAttribute()
    {
        return match($this->severity) {
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'success',
            default => 'secondary'
        };
    }

    public function getCategoryIconAttribute()
    {
        return match($this->category) {
            'defect' => 'bi-exclamation-triangle',
            'incomplete' => 'bi-hourglass-split',
            'damage' => 'bi-hammer',
            'quality' => 'bi-star',
            'safety' => 'bi-shield-exclamation',
            'compliance' => 'bi-check-square',
            default => 'bi-flag'
        };
    }

    public function getIsOverdueAttribute()
    {
        return $this->target_completion_date && 
               $this->target_completion_date->isPast() && 
               !in_array($this->status, ['resolved', 'closed']);
    }

    public function getFormattedCostToFixAttribute()
    {
        return $this->cost_to_fix ? '$' . number_format($this->cost_to_fix, 2) : 'Not estimated';
    }

    public function getDaysOpenAttribute()
    {
        $endDate = $this->resolved_at ?? now();
        return $this->created_at->diffInDays($endDate);
    }

    // Generate unique item number
    public static function generateItemNumber($projectId)
    {
        $project = Project::find($projectId);
        $count = self::where('project_id', $projectId)->count() + 1;
        return 'SNG-' . str_pad($project->id, 3, '0', STR_PAD_LEFT) . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}
