<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'original_name',
        'file_path',
        'file_size',
        'mime_type',
        'project_id',
        'task_id',
        'uploaded_by',
        'category',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    // Category constants
    const CATEGORY_BLUEPRINT = 'blueprint';
    const CATEGORY_CONTRACT = 'contract';
    const CATEGORY_PERMIT = 'permit';
    const CATEGORY_PHOTO = 'photo';
    const CATEGORY_REPORT = 'report';
    const CATEGORY_OTHER = 'other';

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Accessor methods
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isImage()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function getExtensionAttribute()
    {
        return pathinfo($this->original_name, PATHINFO_EXTENSION);
    }

    // Scope methods for company isolation
    public function scopeForCompany($query, $companyId = null)
    {
        $companyId = $companyId ?? auth()->user()->company_id ?? null;
        
        if (auth()->user() && auth()->user()->isSuperAdmin()) {
            return $query; // Superadmin can see all documents
        }
        
        return $query->where('company_id', $companyId);
    }
}
