<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KBArticleView extends Model
{
    protected $table = 'kb_article_views';
    
    protected $fillable = [
        'article_id',
        'user_id',
        'company_id',
        'route_name',
        'ip_address',
        'user_agent',
        'viewed_at'
    ];
    
    protected $casts = [
        'viewed_at' => 'datetime'
    ];
    
    public $timestamps = false;
    
    /**
     * Get the article that was viewed
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(KBArticle::class, 'article_id');
    }
    
    /**
     * Get the user who viewed the article
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get the company of the viewer
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
