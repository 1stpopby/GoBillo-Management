@extends('layouts.app')

@section('title', $taskCategory->name . ' Category')

@section('content')
<div class="task-category-show-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('task-categories.index') }}">Task Categories</a></li>
                        <li class="breadcrumb-item active">{{ $taskCategory->name }}</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center">
                    <div class="category-icon me-3" style="background-color: {{ $taskCategory->color }}15; border: 2px solid {{ $taskCategory->color }};">
                        @if($taskCategory->icon)
                            <i class="{{ $taskCategory->icon }}" style="color: {{ $taskCategory->color }};"></i>
                        @else
                            <i class="bi bi-tag" style="color: {{ $taskCategory->color }};"></i>
                        @endif
                    </div>
                    <div>
                        <h1 class="page-title mb-0">{{ $taskCategory->name }}</h1>
                        <p class="page-subtitle">{{ $taskCategory->description ?? 'Task category details and associated tasks' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-end">
                <div class="btn-group">
                    <a href="{{ route('task-categories.edit', $taskCategory) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-2"></i>Edit Category
                    </a>
                    <form action="{{ route('task-categories.toggle', $taskCategory) }}" method="POST" class="d-inline">
                        @csrf
                        @if($taskCategory->is_active)
                            <button type="submit" class="btn btn-outline-warning">
                                <i class="bi bi-pause me-2"></i>Deactivate
                            </button>
                        @else
                            <button type="submit" class="btn btn-outline-success">
                                <i class="bi bi-play me-2"></i>Activate
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Tasks in this Category -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Tasks in this Category</h5>
                        <span class="badge bg-primary">{{ $taskCategory->tasks->count() }} tasks</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($taskCategory->tasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th>Project</th>
                                        <th>Site</th>
                                        <th>Assigned To</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Due Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($taskCategory->tasks as $task)
                                        <tr>
                                            <td>
                                                <a href="{{ route('tasks.show', $task) }}" class="fw-medium">
                                                    {{ $task->title }}
                                                </a>
                                                @if($task->description)
                                                    <br><small class="text-muted">{{ Str::limit($task->description, 60) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('projects.show', $task->project) }}">
                                                    {{ $task->project->name }}
                                                </a>
                                            </td>
                                            <td>
                                                @if($task->project->site)
                                                    <a href="{{ route('sites.show', $task->project->site) }}">
                                                        {{ $task->project->site->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">No site</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($task->assignedUser)
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            {{ substr($task->assignedUser->name, 0, 1) }}
                                                        </div>
                                                        {{ $task->assignedUser->name }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">Unassigned</span>
                                                @endif
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
                                                        @if($task->is_overdue)
                                                            <i class="bi bi-exclamation-triangle ms-1"></i>
                                                        @endif
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
                    @else
                        <div class="empty-state text-center py-4">
                            <i class="bi bi-list-task display-4 text-muted"></i>
                            <h6 class="mt-3">No tasks in this category yet</h6>
                            <p class="text-muted">Tasks assigned to this category will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Category Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Category Details</h6>
                </div>
                <div class="card-body">
                    <div class="detail-group">
                        <label class="detail-label">Status</label>
                        <div class="detail-value">
                            @if($taskCategory->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="detail-group">
                        <label class="detail-label">Color</label>
                        <div class="detail-value d-flex align-items-center gap-2">
                            <span class="color-swatch" style="background-color: {{ $taskCategory->color }};"></span>
                            <code>{{ $taskCategory->color }}</code>
                        </div>
                    </div>
                    
                    @if($taskCategory->icon)
                        <div class="detail-group">
                            <label class="detail-label">Icon</label>
                            <div class="detail-value d-flex align-items-center gap-2">
                                <i class="{{ $taskCategory->icon }}"></i>
                                <code>{{ $taskCategory->icon }}</code>
                            </div>
                        </div>
                    @endif
                    
                    <div class="detail-group">
                        <label class="detail-label">Sort Order</label>
                        <div class="detail-value">{{ $taskCategory->sort_order }}</div>
                    </div>
                    
                    <div class="detail-group mb-0">
                        <label class="detail-label">Created</label>
                        <div class="detail-value">{{ $taskCategory->created_at->format('M j, Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Category Statistics -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="stat-item">
                        <div class="stat-icon bg-primary">
                            <i class="bi bi-list-task"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $taskCategory->tasks->count() }}</div>
                            <div class="stat-label">Total Tasks</div>
                        </div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-icon bg-warning">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $taskCategory->tasks->whereIn('status', ['pending', 'in_progress'])->count() }}</div>
                            <div class="stat-label">Active Tasks</div>
                        </div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-icon bg-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $taskCategory->tasks->where('status', 'completed')->count() }}</div>
                            <div class="stat-label">Completed Tasks</div>
                        </div>
                    </div>
                    
                    <div class="stat-item mb-0">
                        <div class="stat-icon bg-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $taskCategory->tasks->filter(fn($task) => $task->is_overdue)->count() }}</div>
                            <div class="stat-label">Overdue Tasks</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.task-category-show-container {
    max-width: 100%;
}

.category-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.category-icon i {
    font-size: 1.5rem;
}

.detail-group {
    margin-bottom: 1rem;
}

.detail-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #6b7280;
    font-weight: 600;
    margin-bottom: 0.25rem;
    display: block;
}

.detail-value {
    color: #1f2937;
    font-weight: 500;
}

.color-swatch {
    width: 20px;
    height: 20px;
    border-radius: 4px;
    border: 1px solid #e5e7eb;
}

.stat-item {
    display: flex;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.stat-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.stat-icon i {
    font-size: 1.25rem;
    color: white;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

.avatar-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #4f46e5;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
}

.empty-state {
    padding: 3rem 1rem;
}

.empty-state h6 {
    color: #1f2937;
    font-weight: 600;
    margin-bottom: 0.5rem;
}
</style>
@endsection 