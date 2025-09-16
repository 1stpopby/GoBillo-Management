<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TimeEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'project_id',
        'site_id',
        'task_id',
        'clock_in',
        'clock_out',
        'duration',
        'notes',
        'location',
        'latitude',
        'longitude',
        'operative_latitude',
        'operative_longitude',
        'operative_location_address',
        'project_latitude',
        'project_longitude',
        'distance_from_project',
        'location_validated',
        'location_validation_error',
        'is_billable',
        'status',
    ];

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'is_billable' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'operative_latitude' => 'decimal:8',
        'operative_longitude' => 'decimal:8',
        'project_latitude' => 'decimal:8',
        'project_longitude' => 'decimal:8',
        'distance_from_project' => 'decimal:2',
        'location_validated' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Scopes
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('clock_in', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('clock_in', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Accessors
     */
    public function getIsActiveAttribute()
    {
        return $this->status === 'active' && is_null($this->clock_out);
    }

    public function getDurationFormattedAttribute()
    {
        if ($this->duration) {
            return gmdate('H:i:s', $this->duration);
        }
        
        if ($this->is_active) {
            $duration = now()->diffInSeconds($this->clock_in);
            return gmdate('H:i:s', $duration);
        }
        
        return '00:00:00';
    }

    public function getCurrentDurationAttribute()
    {
        if ($this->is_active) {
            return now()->diffInSeconds($this->clock_in);
        }
        
        return $this->duration ?? 0;
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'success',
            'completed' => 'primary',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Methods
     */
    public function clockOut($notes = null)
    {
        $now = now();
        $duration = $now->diffInSeconds($this->clock_in);
        
        $this->update([
            'clock_out' => $now,
            'duration' => $duration,
            'status' => 'completed',
            'notes' => $notes ?: $this->notes,
        ]);
        
        return $this;
    }

    public function calculateDuration()
    {
        if ($this->clock_out && $this->clock_in) {
            return $this->clock_out->diffInSeconds($this->clock_in);
        }
        
        if ($this->is_active) {
            return now()->diffInSeconds($this->clock_in);
        }
        
        return 0;
    }

    /**
     * Static methods
     */
    public static function getActiveForUser($userId)
    {
        return static::where('user_id', $userId)
            ->where('status', 'active')
            ->whereNull('clock_out')
            ->first();
    }

    public static function getTodayHoursForUser($userId)
    {
        return static::where('user_id', $userId)
            ->whereDate('clock_in', today())
            ->where('status', '!=', 'active')
            ->sum('duration') / 3600; // Convert seconds to hours
    }

    public static function getWeekHoursForUser($userId)
    {
        return static::where('user_id', $userId)
            ->whereBetween('clock_in', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('status', '!=', 'active')
            ->sum('duration') / 3600; // Convert seconds to hours
    }
}