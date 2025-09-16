<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HealthSafetyFormTemplate extends Model
{
    use HasFactory;

    protected $table = 'health_safety_form_templates';

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'category',
        'fields',
        'is_active',
        'requires_signature',
        'requires_photo',
        'version',
        'created_by',
    ];

    protected $casts = [
        'fields' => 'array',
        'is_active' => 'boolean',
        'requires_signature' => 'boolean',
        'requires_photo' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(HealthSafetyFormSubmission::class, 'template_id');
    }
}


