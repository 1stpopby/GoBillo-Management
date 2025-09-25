<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class KBCategory extends Model
{
    protected $table = 'kb_categories';
    
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'icon',
        'order',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];
    
    /**
     * Generate slug from name
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
        
        static::updating(function ($category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
    
    /**
     * Get the parent category
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(KBCategory::class, 'parent_id');
    }
    
    /**
     * Get the child categories
     */
    public function children(): HasMany
    {
        return $this->hasMany(KBCategory::class, 'parent_id')->orderBy('order');
    }
    
    /**
     * Get all articles in this category
     */
    public function articles(): HasMany
    {
        return $this->hasMany(KBArticle::class, 'category_id')->orderBy('order');
    }
    
    /**
     * Get published articles in this category
     */
    public function publishedArticles(): HasMany
    {
        return $this->hasMany(KBArticle::class, 'category_id')
            ->where('status', 'published')
            ->orderBy('priority', 'desc')
            ->orderBy('order');
    }
    
    /**
     * Get all descendants of this category
     */
    public function descendants()
    {
        $descendants = collect();
        
        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->descendants());
        }
        
        return $descendants;
    }
    
    /**
     * Check if category has articles (including in child categories)
     */
    public function hasArticles(): bool
    {
        if ($this->articles()->exists()) {
            return true;
        }
        
        foreach ($this->children as $child) {
            if ($child->hasArticles()) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope for root categories
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }
}