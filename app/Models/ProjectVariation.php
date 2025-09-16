<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'company_id',
        'created_by',
        'variation_number',
        'title',
        'description',
        'reason',
        'type',
        'cost_impact',
        'time_impact_days',
        'status',
        'requested_date',
        'required_by_date',
        'approved_by',
        'approved_at',
        'approval_notes',
        'affected_tasks',
        'client_reference',
        'client_approved',
        'client_approved_at'
    ];

    protected $casts = [
        'requested_date' => 'date',
        'required_by_date' => 'date',
        'cost_impact' => 'decimal:2',
        'time_impact_days' => 'integer',
        'approved_at' => 'datetime',
        'client_approved' => 'boolean',
        'client_approved_at' => 'datetime',
        'affected_tasks' => 'array'
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
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

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'submitted', 'under_review']);
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'approved' => 'success',
            'rejected' => 'danger',
            'implemented' => 'primary',
            'under_review' => 'warning',
            'submitted' => 'info',
            'draft' => 'secondary',
            default => 'secondary'
        };
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'addition' => 'bi-plus-circle',
            'omission' => 'bi-dash-circle',
            'substitution' => 'bi-arrow-left-right',
            'change_order' => 'bi-pencil-square',
            default => 'bi-file-text'
        };
    }

    public function getFormattedCostImpactAttribute()
    {
        $prefix = $this->cost_impact >= 0 ? '+' : '';
        return $prefix . '£' . number_format($this->cost_impact, 2);
    }

    public function getFormattedTimeImpactAttribute()
    {
        if ($this->time_impact_days == 0) {
            return 'No impact';
        }
        $prefix = $this->time_impact_days > 0 ? '+' : '';
        $days = abs($this->time_impact_days);
        $unit = $days == 1 ? 'day' : 'days';
        return $prefix . $days . ' ' . $unit;
    }

    public function getIsOverdueAttribute()
    {
        return $this->required_by_date && 
               $this->required_by_date->isPast() && 
               !in_array($this->status, ['approved', 'rejected', 'implemented']);
    }

    // Generate unique variation number
    public static function generateVariationNumber($projectId)
    {
        $project = Project::find($projectId);
        $count = self::where('project_id', $projectId)->count() + 1;
        return 'VAR-' . str_pad($project->id, 3, '0', STR_PAD_LEFT) . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}
