<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProjectDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'company_id',
        'uploaded_by',
        'title',
        'description',
        'original_filename',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'category',
        'is_public',
        'tags',
        'version',
        'parent_document_id'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_public' => 'boolean',
        'file_size' => 'integer',
        'version' => 'integer'
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

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function parentDocument()
    {
        return $this->belongsTo(ProjectDocument::class, 'parent_document_id');
    }

    public function childDocuments()
    {
        return $this->hasMany(ProjectDocument::class, 'parent_document_id');
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

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessors
    public function getCategoryIconAttribute()
    {
        return match($this->category) {
            'plans' => 'bi-blueprint',
            'photos' => 'bi-camera',
            'contracts' => 'bi-file-text',
            'permits' => 'bi-shield-check',
            'reports' => 'bi-graph-up',
            'specifications' => 'bi-list-columns',
            'invoices' => 'bi-receipt',
            'certificates' => 'bi-award',
            default => 'bi-file-earmark'
        };
    }

    public function getFileTypeIconAttribute()
    {
        if (str_contains($this->mime_type, 'image/')) {
            return 'bi-file-image';
        } elseif (str_contains($this->mime_type, 'pdf')) {
            return 'bi-file-pdf';
        } elseif (str_contains($this->mime_type, 'word') || str_contains($this->mime_type, 'document')) {
            return 'bi-file-word';
        } elseif (str_contains($this->mime_type, 'excel') || str_contains($this->mime_type, 'spreadsheet')) {
            return 'bi-file-excel';
        } else {
            return 'bi-file-earmark';
        }
    }

    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function getDownloadUrlAttribute()
    {
        return route('project.documents.download', ['project' => $this->project_id, 'document' => $this->id]);
    }

    public function getIsImageAttribute()
    {
        return str_contains($this->mime_type, 'image/');
    }
}
