<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingStep extends Model
{
    protected $table = 'onboarding_steps';
    
    protected $fillable = [
        'key',
        'name',
        'description',
        'order',
        'route',
        'icon',
        'help_article_id',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];
    
    /**
     * Get the help article for this step
     */
    public function helpArticle(): BelongsTo
    {
        return $this->belongsTo(KBArticle::class, 'help_article_id');
    }
    
    /**
     * Scope for active steps
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
