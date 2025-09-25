<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class KBArticle extends Model
{
    protected $table = 'kb_articles';
    
    protected $fillable = [
        'category_id',
        'slug',
        'title',
        'summary',
        'status',
        'priority',
        'order',
        'current_version_id',
        'created_by',
        'updated_by',
        'published_at',
        'view_count',
        'meta_data'
    ];
    
    protected $casts = [
        'published_at' => 'datetime',
        'meta_data' => 'array',
        'priority' => 'integer',
        'order' => 'integer',
        'view_count' => 'integer'
    ];
    
    /**
     * Generate slug from title
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
            
            if (auth()->check() && empty($article->created_by)) {
                $article->created_by = auth()->id();
            }
        });
        
        static::updating(function ($article) {
            if ($article->isDirty('title') && !$article->isDirty('slug')) {
                $article->slug = Str::slug($article->title);
            }
            
            if (auth()->check()) {
                $article->updated_by = auth()->id();
            }
        });
        
        // Auto-set published_at when status changes to published
        static::updating(function ($article) {
            if ($article->isDirty('status') && $article->status === 'published' && !$article->published_at) {
                $article->published_at = now();
            }
        });
    }
    
    /**
     * Get the category of the article
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(KBCategory::class, 'category_id');
    }
    
    /**
     * Get the current version of the article
     */
    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(KBArticleVersion::class, 'current_version_id');
    }
    
    /**
     * Get all versions of the article
     */
    public function versions(): HasMany
    {
        return $this->hasMany(KBArticleVersion::class, 'article_id')->orderBy('version', 'desc');
    }
    
    /**
     * Get the author of the article
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Get the last editor of the article
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    /**
     * Get the tags of the article
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(KBTag::class, 'kb_article_tag', 'article_id', 'tag_id');
    }
    
    /**
     * Get related articles
     */
    public function relatedArticles(): BelongsToMany
    {
        return $this->belongsToMany(
            KBArticle::class,
            'kb_article_relations',
            'article_id',
            'related_article_id'
        )->withPivot('relation_type');
    }
    
    /**
     * Get articles that reference this article
     */
    public function referencedBy(): BelongsToMany
    {
        return $this->belongsToMany(
            KBArticle::class,
            'kb_article_relations',
            'related_article_id',
            'article_id'
        )->withPivot('relation_type');
    }
    
    /**
     * Get the bindings of the article (page/feature associations)
     */
    public function bindings(): HasMany
    {
        return $this->hasMany(KBArticleBinding::class, 'article_id');
    }
    
    /**
     * Get the view logs of the article
     */
    public function views(): HasMany
    {
        return $this->hasMany(KBArticleView::class, 'article_id');
    }
    
    /**
     * Scope for published articles
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
    
    /**
     * Scope for draft articles
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
    
    /**
     * Scope for archived articles
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }
    
    /**
     * Scope for high priority articles
     */
    public function scopeHighPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }
    
    /**
     * Get the content from the current version
     */
    public function getContentAttribute()
    {
        return $this->currentVersion ? $this->currentVersion->content_html : '';
    }
    
    /**
     * Get the plain text content from the current version
     */
    public function getPlainContentAttribute()
    {
        return $this->currentVersion ? $this->currentVersion->content_plain : '';
    }
    
    /**
     * Increment view count
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }
    
    /**
     * Log a view
     */
    public function logView($userId = null, $companyId = null, $routeName = null)
    {
        KBArticleView::create([
            'article_id' => $this->id,
            'user_id' => $userId,
            'company_id' => $companyId,
            'route_name' => $routeName,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'viewed_at' => now()
        ]);
        
        $this->incrementViewCount();
    }
    
    /**
     * Create a new version
     */
    public function createVersion($title, $contentHtml, $contentPlain = null, $changeSummary = null)
    {
        $lastVersion = $this->versions()->orderBy('version', 'desc')->first();
        $newVersionNumber = $lastVersion ? $lastVersion->version + 1 : 1;
        
        $version = KBArticleVersion::create([
            'article_id' => $this->id,
            'version' => $newVersionNumber,
            'title' => $title,
            'content_html' => $contentHtml,
            'content_plain' => $contentPlain ?: strip_tags($contentHtml),
            'change_summary' => $changeSummary,
            'edited_by' => auth()->id()
        ]);
        
        // Update the current version
        $this->update(['current_version_id' => $version->id]);
        
        return $version;
    }
}