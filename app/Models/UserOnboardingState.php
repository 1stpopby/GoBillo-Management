<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserOnboardingState extends Model
{
    protected $table = 'user_onboarding_state';
    
    protected $fillable = [
        'user_id',
        'has_seen_welcome',
        'dismissed_at',
        'completed_at',
        'last_seen_at',
        'skip_onboarding'
    ];
    
    protected $casts = [
        'has_seen_welcome' => 'boolean',
        'skip_onboarding' => 'boolean',
        'dismissed_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_seen_at' => 'datetime'
    ];
    
    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Check if onboarding should be shown
     */
    public function shouldShowOnboarding(): bool
    {
        // Don't show if skipped or completed
        if ($this->skip_onboarding || $this->completed_at) {
            return false;
        }
        
        // Show if not dismissed or dismissed more than 24 hours ago
        if (!$this->dismissed_at) {
            return true;
        }
        
        return $this->dismissed_at->diffInHours(now()) > 24;
    }
    
    /**
     * Dismiss the onboarding
     */
    public function dismiss()
    {
        $this->dismissed_at = now();
        $this->last_seen_at = now();
        $this->save();
    }
    
    /**
     * Complete the onboarding
     */
    public function complete()
    {
        $this->completed_at = now();
        $this->save();
    }
    
    /**
     * Skip the onboarding permanently
     */
    public function skip()
    {
        $this->skip_onboarding = true;
        $this->dismissed_at = now();
        $this->save();
    }
}
