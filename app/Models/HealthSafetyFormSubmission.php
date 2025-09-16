<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthSafetyFormSubmission extends Model
{
    use HasFactory;

    protected $table = 'health_safety_form_submissions';

    protected $fillable = [
        'company_id',
        'template_id',
        'site_id',
        'project_id',
        'submission_number',
        'form_data',
        'submitted_by',
        'submitted_at',
        'signature_path',
        'photos',
        'attachments',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_comments',
    ];

    protected $casts = [
        'form_data' => 'array',
        'photos' => 'array',
        'attachments' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(HealthSafetyFormTemplate::class, 'template_id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}


