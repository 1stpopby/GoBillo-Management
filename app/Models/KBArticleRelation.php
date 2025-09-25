<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KBArticleRelation extends Model
{
    protected $table = 'kb_article_relations';
    
    protected $fillable = [
        'article_id',
        'related_article_id',
        'relation_type'
    ];
    
    /**
     * Get the primary article
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(KBArticle::class, 'article_id');
    }
    
    /**
     * Get the related article
     */
    public function relatedArticle(): BelongsTo
    {
        return $this->belongsTo(KBArticle::class, 'related_article_id');
    }
}
