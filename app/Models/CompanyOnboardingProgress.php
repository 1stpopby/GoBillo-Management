<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyOnboardingProgress extends Model
{
    protected $table = 'company_onboarding_progress';
    
    protected $fillable = [
        'company_id',
        'steps',
        'current_step',
        'completed_steps',
        'total_steps',
        'started_at',
        'completed_at'
    ];
    
    protected $casts = [
        'steps' => 'array',
        'completed_steps' => 'integer',
        'total_steps' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];
    
    /**
     * Get the company
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    
    /**
     * Check if a step is completed
     */
    public function isStepCompleted($stepKey): bool
    {
        $steps = $this->steps ?? [];
        return isset($steps[$stepKey]) && $steps[$stepKey]['completed'] === true;
    }
    
    /**
     * Mark a step as completed
     */
    public function markStepCompleted($stepKey)
    {
        $steps = $this->steps ?? [];
        if (!isset($steps[$stepKey])) {
            $steps[$stepKey] = [];
        }
        
        $steps[$stepKey]['completed'] = true;
        $steps[$stepKey]['completed_at'] = now()->toDateTimeString();
        
        $this->steps = $steps;
        $this->completed_steps = count(array_filter($steps, function($step) {
            return isset($step['completed']) && $step['completed'] === true;
        }));
        
        // Check if all steps are completed
        if ($this->completed_steps >= $this->total_steps) {
            $this->completed_at = now();
        }
        
        $this->save();
    }
    
    /**
     * Get the completion percentage
     */
    public function getCompletionPercentageAttribute(): int
    {
        if ($this->total_steps === 0) {
            return 0;
        }
        return (int) (($this->completed_steps / $this->total_steps) * 100);
    }
    
    /**
     * Check if onboarding is completed
     */
    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}
