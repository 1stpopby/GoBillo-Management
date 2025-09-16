<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthSafetyIncident extends Model
{
    use HasFactory;

    protected $table = 'health_safety_incidents';

    protected $fillable = [
        'company_id',
        'site_id',
        'project_id',
        'incident_number',
        'type',
        'severity',
        'occurred_at',
        'location',
        'description',
        'involved_persons',
        'witnesses',
        'immediate_actions',
        'root_cause',
        'corrective_actions',
        'first_aid_given',
        'medical_treatment_required',
        'reported_to_hse',
        'reportable_riddor',
        'days_lost',
        'reported_by',
        'investigated_by',
        'investigation_date',
        'status',
        'attachments',
    ];

    protected $casts = [
        'involved_persons' => 'array',
        'witnesses' => 'array',
        'attachments' => 'array',
        'occurred_at' => 'datetime',
        'investigation_date' => 'date',
        'first_aid_given' => 'boolean',
        'medical_treatment_required' => 'boolean',
        'reported_to_hse' => 'boolean',
        'reportable_riddor' => 'boolean',
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

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function investigatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'investigated_by');
    }
}


