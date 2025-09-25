<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class KBTag extends Model
{
    protected $table = 'kb_tags';
    
    protected $fillable = [
        'name',
        'slug',
        'color'
    ];
    
    /**
     * Generate slug from name
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
        
        static::updating(function ($tag) {
            if ($tag->isDirty('name') && !$tag->isDirty('slug')) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }
    
    /**
     * Get the articles with this tag
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(KBArticle::class, 'kb_article_tag', 'tag_id', 'article_id');
    }
    
    /**
     * Get published articles with this tag
     */
    public function publishedArticles(): BelongsToMany
    {
        return $this->belongsToMany(KBArticle::class, 'kb_article_tag', 'tag_id', 'article_id')
            ->where('status', 'published');
    }
}