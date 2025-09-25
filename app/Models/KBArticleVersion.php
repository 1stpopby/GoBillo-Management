<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KBArticleVersion extends Model
{
    protected $table = 'kb_article_versions';
    
    protected $fillable = [
        'article_id',
        'version',
        'title',
        'content_html',
        'content_plain',
        'change_summary',
        'edited_by'
    ];
    
    protected $casts = [
        'version' => 'integer'
    ];
    
    /**
     * Get the article this version belongs to
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(KBArticle::class, 'article_id');
    }
    
    /**
     * Get the user who edited this version
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
    
    /**
     * Check if this is the current version
     */
    public function isCurrent(): bool
    {
        return $this->article && $this->article->current_version_id === $this->id;
    }
    
    /**
     * Get the previous version
     */
    public function previousVersion()
    {
        return $this->article->versions()
            ->where('version', '<', $this->version)
            ->orderBy('version', 'desc')
            ->first();
    }
    
    /**
     * Get the next version
     */
    public function nextVersion()
    {
        return $this->article->versions()
            ->where('version', '>', $this->version)
            ->orderBy('version', 'asc')
            ->first();
    }
}