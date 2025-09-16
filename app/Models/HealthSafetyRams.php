<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthSafetyRams extends Model
{
    use HasFactory;

    protected $table = 'health_safety_rams';

    protected $fillable = [
        'company_id',
        'site_id',
        'project_id',
        'title',
        'reference_number',
        'task_description',
        'hazards',
        'risk_levels',
        'likelihoods',
        'severities',
        'risk_control_measures',
        'control_measures',
        'sequence_of_work',
        'ppe_required',
        'training_required',
        'emergency_procedures',
        'risk_level',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'valid_from',
        'valid_until',
        'file_path',
        'notes',
    ];

    protected $casts = [
        'hazards' => 'array',
        'risk_levels' => 'array',
        'likelihoods' => 'array',
        'severities' => 'array',
        'risk_control_measures' => 'array',
        'approved_at' => 'datetime',
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
