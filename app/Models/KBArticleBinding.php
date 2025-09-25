<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KBArticleBinding extends Model
{
    protected $table = 'kb_article_bindings';
    
    protected $fillable = [
        'article_id',
        'route_name',
        'feature_key',
        'model',
        'context_type'
    ];
    
    /**
     * Get the article this binding belongs to
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(KBArticle::class, 'article_id');
    }
    
    /**
     * Scope for route bindings
     */
    public function scopeForRoute($query, $routeName)
    {
        return $query->where('route_name', $routeName);
    }
    
    /**
     * Scope for feature bindings
     */
    public function scopeForFeature($query, $featureKey)
    {
        return $query->where('feature_key', $featureKey);
    }
}
