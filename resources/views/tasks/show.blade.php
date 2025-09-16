@extends('layouts.app')

@section('title', 'Task Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">Tasks</a></li>
                <li class="breadcrumb-item active">{{ $task->title }}</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">{{ $task->title }}</h1>
    </div>
    <div class="btn-group">
        @if(auth()->user()->isSuperAdmin() || auth()->user()->isCompanyAdmin() || auth()->user()->isProjectManager())
            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil"></i> Edit Task
            </a>
        @endif
        @if($task->project)
            <a href="{{ route('projects.show', $task->project) }}#tasks" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Project Tasks
            </a>
        @else
            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Tasks
            </a>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Task Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Task Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Project:</strong>
                    </div>
                    <div class="col-sm-9">
                        <a href="{{ route('projects.show', $task->project) }}" class="text-decoration-none">
                            {{ $task->project->name }}
                        </a>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Status:</strong>
                    </div>
                    <div class="col-sm-9">
                        <span class="badge bg-{{ $task->status_color }}">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Priority:</strong>
                    </div>
                    <div class="col-sm-9">
                        <span class="badge bg-{{ $task->priority_color }}">
                            {{ ucfirst($task->priority) }}
                        </span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Assigned To:</strong>
                    </div>
                    <div class="col-sm-9">
                        @if($task->assignedUser)
                            {{ $task->assignedUser->name }}
                            <small class="text-muted">({{ $task->assignedUser->email }})</small>
                        @else
                            <span class="text-muted">Unassigned</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Created By:</strong>
                    </div>
                    <div class="col-sm-9">
                        {{ $task->createdBy->name }}
                        <small class="text-muted">on {{ $task->created_at->format('M j, Y') }}</small>
                    </div>
                </div>

                @if($task->due_date)
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Due Date:</strong>
                        </div>
                        <div class="col-sm-9">
                            <span class="{{ $task->is_overdue ? 'text-danger fw-bold' : '' }}">
                                {{ $task->due_date->format('M j, Y') }}
                                @if($task->is_overdue)
                                    <i class="bi bi-exclamation-triangle ms-1"></i>
                                @endif
                            </span>
                        </div>
                    </div>
                @endif

                <div class="row mb-3">
                    <div class="col-sm-3">
                        <strong>Progress:</strong>
                    </div>
                    <div class="col-sm-9">
                        <div class="progress mb-1" style="height: 20px;">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ $task->progress }}%">
                                {{ $task->progress }}%
                            </div>
                        </div>
                    </div>
                </div>

                @if($task->estimated_time || $task->actual_time)
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <strong>Time Tracking:</strong>
                        </div>
                        <div class="col-sm-9">
                            @if($task->estimated_time)
                                <span class="badge bg-info me-2">
                                    <i class="bi bi-clock me-1"></i>Estimated: {{ $task->formatted_estimated_time }}
                                </span>
                            @endif
                            @if($task->actual_time)
                                <span class="badge bg-success me-2">
                                    <i class="bi bi-stopwatch me-1"></i>Actual: {{ $task->formatted_actual_time }}
                                </span>
                            @endif
                            @if($task->estimated_time && $task->actual_time)
                                @php
                                    $estimatedHours = $task->estimated_time_in_hours;
                                    $actualHours = $task->actual_time_in_hours;
                                    $variance = $actualHours - $estimatedHours;
                                    $isOverBudget = $variance > 0;
                                @endphp
                                <br><small class="text-muted mt-1">
                                    Variance: 
                                    <span class="text-{{ $isOverBudget ? 'danger' : 'success' }}">
                                        {{ $isOverBudget ? '+' : '' }}{{ number_format($variance, 1) }} hours
                                        @if($estimatedHours > 0)
                                            ({{ $isOverBudget ? '+' : '' }}{{ number_format(($variance / $estimatedHours) * 100, 0) }}%)
                                        @endif
                                    </span>
                                </small>
                            @endif
                        </div>
                    </div>
                @endif

                @if($task->description)
                    <div class="row">
                        <div class="col-sm-3">
                            <strong>Description:</strong>
                        </div>
                        <div class="col-sm-9">
                            {!! nl2br(e($task->description)) !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                @if(auth()->user()->isSuperAdmin() || auth()->user()->isCompanyAdmin() || auth()->user()->isProjectManager())
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-success btn-sm" onclick="updateStatus('completed')">
                            <i class="bi bi-check-circle"></i> Mark Complete
                        </button>
                        <button class="btn btn-outline-warning btn-sm" onclick="updateStatus('in_progress')">
                            <i class="bi bi-play-circle"></i> Mark In Progress
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="updateStatus('review')">
                            <i class="bi bi-eye-fill"></i> Mark for Review
                        </button>
                        <hr>
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil"></i> Edit Task
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Task Timeline -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Timeline</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Task Created</h6>
                            <p class="timeline-text">
                                Created by {{ $task->createdBy->name }}<br>
                                <small class="text-muted">{{ $task->created_at->format('M j, Y g:i A') }}</small>
                            </p>
                        </div>
                    </div>
                    
                    @if($task->assignedUser)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Task Assigned</h6>
                                <p class="timeline-text">
                                    Assigned to {{ $task->assignedUser->name }}<br>
                                    <small class="text-muted">{{ $task->updated_at->format('M j, Y g:i A') }}</small>
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($task->status === 'completed')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Task Completed</h6>
                                <p class="timeline-text">
                                    <small class="text-muted">{{ $task->updated_at->format('M j, Y g:i A') }}</small>
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
}

.timeline-content {
    padding-left: 15px;
}

.timeline-title {
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.timeline-text {
    font-size: 0.85rem;
    margin-bottom: 0;
}
</style>

<script>
function updateStatus(status) {
    if (confirm('Are you sure you want to update the task status?')) {
        // This would typically send an AJAX request to update the status
        // For now, we'll redirect to the edit page
        window.location.href = '{{ route("tasks.edit", $task) }}';
    }
}
</script>