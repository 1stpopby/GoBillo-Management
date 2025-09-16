@extends('layouts.app')

@section('title', $member->name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('team.index') }}">Team</a></li>
                        <li class="breadcrumb-item active">{{ $member->name }}</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center">
                    <div class="avatar-lg me-3">
                        {{ substr($member->name, 0, 1) }}
                    </div>
                    <div>
                        <h1 class="page-title mb-1">{{ $member->name }}</h1>
                        <p class="page-subtitle mb-0">{{ $member->role_display }}</p>
                        <div class="d-flex gap-2 mt-2">
                            @if($member->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                            <span class="badge bg-light text-dark">Member since {{ optional($member->created_at)->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-end">
                @if(auth()->user()->canManageCompanyUsers() && $member->id !== auth()->id())
                    <a href="{{ route('team.edit', ['team'=>$member->id]) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit Member
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Member Details -->
        <div class="col-lg-8">
            <!-- Contact Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="detail-group">
                                <label class="detail-label">Email</label>
                                <div class="detail-value">
                                    <a href="mailto:{{ $member->email }}">{{ $member->email }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-group">
                                <label class="detail-label">Phone</label>
                                <div class="detail-value">
                                    @if($member->phone)
                                        <a href="tel:{{ $member->phone }}">{{ $member->phone }}</a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-group">
                                <label class="detail-label">Role</label>
                                <div class="detail-value">
                                    <span class="badge bg-primary">{{ $member->role_display }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-group">
                                <label class="detail-label">Status</label>
                                <div class="detail-value">
                                    @if($member->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Projects -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Managed Projects</h5>
                </div>
                <div class="card-body">
                    @if($member->managedProjects && $member->managedProjects->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Project</th>
                                        <th>Site</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($member->managedProjects as $project)
                                        <tr>
                                            <td>
                                                <a href="{{ route('projects.show', $project) }}" class="text-decoration-none">
                                                    {{ $project->name }}
                                                </a>
                                            </td>
                                            <td>
                                                @if($project->site)
                                                    <a href="{{ route('sites.show', $project->site) }}" class="text-decoration-none">
                                                        {{ $project->site->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">No site</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $project->status_color }}">
                                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 6px; width: 80px;">
                                                    <div class="progress-bar" style="width: {{ $project->progress }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ $project->progress }}%</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-folder display-4 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">No managed projects</p>
                            <small class="text-muted">This team member is not managing any projects.</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tasks -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Assigned Tasks</h5>
                </div>
                <div class="card-body">
                    @if($member->tasks && $member->tasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th>Project</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Due Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($member->tasks->take(10) as $task)
                                        <tr>
                                            <td>
                                                <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none">
                                                    {{ $task->title }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('projects.show', $task->project) }}" class="text-decoration-none">
                                                    {{ $task->project->name }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $task->status_color }}">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $task->priority_color }}">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($task->due_date)
                                                    <span class="{{ $task->is_overdue ? 'text-danger fw-bold' : '' }}">
                                                        {{ $task->due_date->format('M j, Y') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">No due date</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                         @if($member->tasks->count() > 10)
                            <div class="text-center mt-3">
                                     <a href="{{ route('tasks.index', ['assigned_to' => $member->id]) }}" class="btn btn-outline-primary btn-sm">
                                     View All {{ $member->tasks->count() }} Tasks
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-list-task display-4 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">No assigned tasks</p>
                            <small class="text-muted">This team member has no tasks assigned.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Activity & Stats -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center">
                                 <div class="h4 mb-1 text-primary">{{ $member->managedProjects ? $member->managedProjects->count() : 0 }}</div>
                                <small class="text-muted">Projects Managed</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                 <div class="h4 mb-1 text-warning">{{ $member->tasks ? $member->tasks->count() : 0 }}</div>
                                <small class="text-muted">Tasks Assigned</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                 <div class="h4 mb-1 text-success">{{ $member->createdTasks ? $member->createdTasks->count() : 0 }}</div>
                                <small class="text-muted">Tasks Created</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                 <div class="h4 mb-1 text-info">{{ $member->tasks ? $member->tasks->where('status', 'completed')->count() : 0 }}</div>
                                <small class="text-muted">Tasks Completed</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Member Since -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Member Information</h5>
                </div>
                <div class="card-body">
                    <div class="detail-group mb-3">
                        <label class="detail-label">Member Since</label>
                        <div class="detail-value">{{ optional($member->created_at)->format('F j, Y') ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-group mb-3">
                        <label class="detail-label">Last Login</label>
                        <div class="detail-value">
                            @if($member->email_verified_at)
                                {{ $member->email_verified_at->diffForHumans() }}
                            @else
                                <span class="text-muted">Never logged in</span>
                            @endif
                        </div>
                    </div>
                    <div class="detail-group">
                        <label class="detail-label">Account Status</label>
                        <div class="detail-value">
                            @if($member->is_active)
                                <span class="badge bg-success">Active Member</span>
                            @else
                                <span class="badge bg-secondary">Inactive Member</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-lg {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #4f46e5;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 2rem;
}

.detail-group {
    margin-bottom: 1rem;
}

.detail-label {
    font-weight: 600;
    color: #6b7280;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
    display: block;
}

.detail-value {
    color: #1f2937;
    font-size: 0.95rem;
}

.detail-value a {
    color: #4f46e5;
    text-decoration: none;
}

.detail-value a:hover {
    text-decoration: underline;
}

.progress {
    margin-bottom: 0.25rem;
}
</style>
@endsection 