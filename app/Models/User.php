<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    // Simple role constants
    const ROLE_SUPERADMIN = 'superadmin';
    const ROLE_COMPANY_ADMIN = 'company_admin';
    const ROLE_PROJECT_MANAGER = 'project_manager';
    const ROLE_SITE_MANAGER = 'site_manager';
    const ROLE_CONTRACTOR = 'contractor';
    const ROLE_SUBCONTRACTOR = 'subcontractor';
    const ROLE_OPERATIVE = 'operative';
    const ROLE_CLIENT = 'client';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'is_active',
        'company_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_users');
    }

    /**
     * Projects where the user is the designated manager
     */
    public function managedProjects()
    {
        return $this->hasMany(Project::class, 'manager_id');
    }

    /**
     * Tasks assigned to the user
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    /**
     * Alias for tasks() - used in employees dashboard
     */
    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    /**
     * Tasks created by the user
     */
    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    /**
     * Operative invoices created by this user (if operative)
     */
    public function operativeInvoices()
    {
        return $this->hasMany(\App\Models\OperativeInvoice::class, 'operative_id');
    }

    /**
     * Sites where the user is assigned as a manager
     */
    public function managedSites()
    {
        return $this->belongsToMany(Site::class, 'site_managers', 'manager_id', 'site_id')
                    ->withPivot(['role', 'is_active'])
                    ->withTimestamps();
    }

    /**
     * Active sites where the user is assigned as a manager
     */
    public function activeManagedSites()
    {
        return $this->managedSites()->wherePivot('is_active', true);
    }
    
    /**
     * CIS payments for this user (managers/employees)
     */
    public function cisPayments()
    {
        return $this->hasMany(\App\Models\CisPayment::class, 'user_id');
    }

    /**
     * Document attachments for this user
     */
    public function documentAttachments()
    {
        return $this->morphMany(\App\Models\DocumentAttachment::class, 'attachable');
    }

    /**
     * Employee record for this user (for operatives)
     */
    public function employee()
    {
        return $this->hasOne(\App\Models\Employee::class, 'user_id');
    }

    /**
     * Projects where the user is assigned as a manager (many-to-many)
     */
    public function managedProjectsMany()
    {
        return $this->belongsToMany(Project::class, 'project_managers', 'manager_id', 'project_id')
                    ->withPivot(['role', 'is_active'])
                    ->withTimestamps();
    }

    /**
     * Active projects where the user is assigned as a manager (many-to-many)
     */
    public function activeManagedProjects()
    {
        return $this->managedProjectsMany()->wherePivot('is_active', true);
    }

    // Scopes
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?? auth()->user()?->company_id ?? null;
        
        if ($companyId === null) {
            // For superadmin or console contexts, don't filter
            return $query;
        }

        return $query->where('company_id', $companyId);
    }

    // Simple role checking methods
    public function isSuperAdmin()
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function isCompanyAdmin()
    {
        return $this->role === self::ROLE_COMPANY_ADMIN;
    }

    public function isProjectManager()
    {
        return $this->role === self::ROLE_PROJECT_MANAGER;
    }

    public function isSiteManager()
    {
        return $this->role === self::ROLE_SITE_MANAGER;
    }

    public function isContractor()
    {
        return $this->role === self::ROLE_CONTRACTOR;
    }

    public function isClient()
    {
        return $this->role === self::ROLE_CLIENT;
    }

    public function isSubcontractor()
    {
        return $this->role === self::ROLE_SUBCONTRACTOR;
    }

    public function isOperative()
    {
        return $this->role === self::ROLE_OPERATIVE;
    }

    // Simple permission methods
    public function canManageCompanies()
    {
        return $this->isSuperAdmin();
    }

    public function canManageProjects()
    {
        return in_array($this->role, [self::ROLE_SUPERADMIN, self::ROLE_COMPANY_ADMIN, self::ROLE_PROJECT_MANAGER]);
    }

    /**
     * Whether the user can manage operative invoices.
     * Includes project managers, site managers, and admins.
     */
    public function canManageOperativeInvoices()
    {
        return in_array($this->role, [
            self::ROLE_SUPERADMIN, 
            self::ROLE_COMPANY_ADMIN, 
            self::ROLE_PROJECT_MANAGER,
            self::ROLE_SITE_MANAGER
        ]);
    }

    /**
     * Whether the user can manage tasks (create, edit, delete)
     */
    public function canManageTasks()
    {
        return in_array($this->role, [
            self::ROLE_SUPERADMIN, 
            self::ROLE_COMPANY_ADMIN, 
            self::ROLE_PROJECT_MANAGER,
            self::ROLE_SITE_MANAGER
        ]);
    }

    /**
     * Whether the user can view all tasks in the company
     */
    public function canViewAllTasks()
    {
        return in_array($this->role, [
            self::ROLE_SUPERADMIN, 
            self::ROLE_COMPANY_ADMIN, 
            self::ROLE_PROJECT_MANAGER,
            self::ROLE_SITE_MANAGER
        ]);
    }

    /**
     * Whether the user can view projects across the company.
     * Includes internal roles except client.
     */
    public function canViewProjects(): bool
    {
        return in_array($this->role, [
            self::ROLE_SUPERADMIN,
            self::ROLE_COMPANY_ADMIN,
            self::ROLE_PROJECT_MANAGER,
            self::ROLE_CONTRACTOR,
            self::ROLE_SUBCONTRACTOR,
            self::ROLE_OPERATIVE,
        ]);
    }

    public function canManageClients()
    {
        return in_array($this->role, [self::ROLE_SUPERADMIN, self::ROLE_COMPANY_ADMIN]);
    }

    public function canManageUsers()
    {
        return in_array($this->role, [self::ROLE_SUPERADMIN, self::ROLE_COMPANY_ADMIN]);
    }

    // Alias used by some views
    public function canManageCompanyUsers(): bool
    {
        return $this->canManageUsers();
    }

    public function canManageDocuments()
    {
        return in_array($this->role, [self::ROLE_SUPERADMIN, self::ROLE_COMPANY_ADMIN, self::ROLE_PROJECT_MANAGER]);
    }

    public function canViewFinancials()
    {
        return in_array($this->role, [self::ROLE_SUPERADMIN, self::ROLE_COMPANY_ADMIN]);
    }

}