<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthSafetyObservation extends Model
{
    use HasFactory;

    protected $table = 'health_safety_observations';

    protected $fillable = [
        'company_id',
        'site_id',
        'project_id',
        'type',
        'category',
        'observation',
        'action_taken',
        'recommendation',
        'observed_by',
        'observed_at',
        'location',
        'photos',
        'priority',
        'status',
        'assigned_to',
        'target_close_date',
        'actual_close_date',
    ];

    protected $casts = [
        'photos' => 'array',
        'observed_at' => 'datetime',
        'target_close_date' => 'date',
        'actual_close_date' => 'date',
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

    public function observedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'observed_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}


