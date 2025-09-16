<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthSafetyInduction extends Model
{
    use HasFactory;

    protected $table = 'health_safety_inductions';

    protected $fillable = [
        'company_id',
        'site_id',
        'employee_id',
        'inductee_name',
        'inductee_company',
        'inductee_role',
        'inductee_phone',
        'inductee_email',
        'emergency_contact_name',
        'emergency_contact_phone',
        'inducted_at',
        'inducted_by',
        'topics_covered',
        'documents_provided',
        'site_rules_acknowledged',
        'emergency_procedures_understood',
        'ppe_requirements_understood',
        'hazards_communicated',
        'valid_until',
        'certificate_number',
        'signature_path',
        'notes',
        'status',
    ];

    protected $casts = [
        'topics_covered' => 'array',
        'documents_provided' => 'array',
        'inducted_at' => 'datetime',
        'valid_until' => 'date',
        'site_rules_acknowledged' => 'boolean',
        'emergency_procedures_understood' => 'boolean',
        'ppe_requirements_understood' => 'boolean',
        'hazards_communicated' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function inductedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inducted_by');
    }
}


