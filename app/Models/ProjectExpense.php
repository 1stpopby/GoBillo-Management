<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'company_id',
        'created_by',
        'category',
        'amount',
        'currency',
        'expense_date',
        'receipt_path',
        'status',
        'notes',
        'vendor_name',
        'invoice_number',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime'
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
        return $query->where('status', 'pending');
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'approved' => 'success',
            'rejected' => 'danger',
            'pending' => 'warning',
            default => 'secondary'
        };
    }

    public function getCategoryIconAttribute()
    {
        return match($this->category) {
            'materials' => 'bi-box-seam',
            'travel' => 'bi-car-front',
            'equipment' => 'bi-tools',
            'subcontractor' => 'bi-people',
            'labor' => 'bi-person-working',
            'permits' => 'bi-file-text',
            'utilities' => 'bi-lightning',
            default => 'bi-receipt'
        };
    }

    public function getFormattedAmountAttribute()
    {
        return $this->currency . ' ' . number_format($this->amount, 2);
    }
}
