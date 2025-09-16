<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'filename',
        'original_filename',
        'path',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attachment) {
            if (auth()->check()) {
                $attachment->uploaded_by = auth()->id();
            }
        });

        static::deleting(function ($attachment) {
            // Delete the physical file when the attachment is deleted
            if (Storage::disk('public')->exists($attachment->path)) {
                Storage::disk('public')->delete($attachment->path);
            }
        });
    }

    // Relationships
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Accessors
    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('attachments.download', $this);
    }

    public function getSizeFormattedAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function getIconAttribute(): string
    {
        return match(true) {
            str_starts_with($this->mime_type, 'image/') => 'photo',
            $this->mime_type === 'application/pdf' => 'document-text',
            str_contains($this->mime_type, 'word') => 'document',
            str_contains($this->mime_type, 'excel') || str_contains($this->mime_type, 'spreadsheet') => 'table-cells',
            default => 'document',
        };
    }
}