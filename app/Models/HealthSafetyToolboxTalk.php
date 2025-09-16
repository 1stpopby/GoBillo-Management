<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthSafetyToolboxTalk extends Model
{
    use HasFactory;

    protected $table = 'health_safety_toolbox_talks';

    protected $fillable = [
        'company_id',
        'site_id',
        'project_id',
        'title',
        'reference_number',
        'topics_covered',
        'key_points',
        'conducted_by',
        'conducted_at',
        'duration_minutes',
        'location',
        'attendees',
        'attendee_count',
        'document_path',
        'notes',
        'weather_conditions',
    ];

    protected $casts = [
        'attendees' => 'array',
        'conducted_at' => 'datetime',
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

    public function conductedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conducted_by');
    }
}


