<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectUser extends Model
{
    use HasFactory;

    protected $table = 'project_users';

    protected $fillable = [
        'project_id',
        'user_id',
        'role',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    // Project roles
    const ROLE_MANAGER = 'manager';
    const ROLE_SUPERVISOR = 'supervisor';
    const ROLE_WORKER = 'worker';
    const ROLE_OBSERVER = 'observer';

    /**
     * Get the project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
