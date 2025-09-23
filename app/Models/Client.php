<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'company_name',
        'legal_name',
        'contact_person_name',
        'contact_person_title',
        'contact_person_email', 
        'contact_person_phone',
        'email', // Company general email
        'phone', // Company general phone
        'website',
        'tax_id',
        'business_type',
        'business_description',
        'industry',
        'address',
        'city',
        'state',
        'zip_code',
        'notes',
        'is_active',
        'is_private_client',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_private_client' => 'boolean',
        ];
    }

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // Scopes
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?: (auth()->check() ? auth()->user()->company_id : null);
        
        if (auth()->user() && auth()->user()->isSuperAdmin()) {
            return $query; // Superadmin can see all clients
        }
        
        return $query->where('company_id', $companyId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors
    public function getDisplayNameAttribute(): string
    {
        return $this->company_name ?: $this->legal_name ?: 'Unnamed Company';
    }

    public function getNameAttribute(): string
    {
        return $this->getDisplayNameAttribute();
    }

    public function getFullAddressAttribute(): ?string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zip_code
        ]);
        
        return !empty($parts) ? implode(', ', $parts) : null;
    }

    public function getPrimaryContactAttribute(): ?string
    {
        if ($this->contact_person_name) {
            $contact = $this->contact_person_name;
            if ($this->contact_person_title) {
                $contact .= ' (' . $this->contact_person_title . ')';
            }
            return $contact;
        }
        return null;
    }

    public function getContactEmailAttribute(): ?string
    {
        return $this->contact_person_email ?: $this->email;
    }

    public function getContactPhoneAttribute(): ?string
    {
        return $this->contact_person_phone ?: $this->phone;
    }

    // Computed attribute accessors for stats (used by views)
    public function getTotalSitesCountAttribute(): int
    {
        return $this->sites()->count();
    }

    public function getActiveSitesCountAttribute(): int
    {
        return $this->sites()->whereIn('status', ['planning', 'active'])->count();
    }

    public function getTotalProjectsCountAttribute(): int
    {
        return $this->projects()->count();
    }

    public function getActiveProjectsCountAttribute(): int
    {
        return $this->projects()->whereIn('status', ['planning', 'in_progress'])->count();
    }

    public function getTotalProjectsValueAttribute(): float
    {
        return (float) ($this->projects()->sum('budget') ?: 0);
    }
}
