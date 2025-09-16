@extends('layouts.app')

@section('title', 'Field Operations')

@section('content')
<div class="field-operations-container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="page-title">
                    <i class="bi bi-tools me-3 text-primary"></i>Field Operations
                </h1>
                <p class="page-subtitle">Manage field activities, equipment, and safety across all sites</p>
            </div>
            <div class="col-lg-6 text-end">
                <div class="btn-group">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newWorkOrderModal">
                        <i class="bi bi-plus-circle me-2"></i>New Work Order
                    </button>
                    <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#safetyReportModal">
                        <i class="bi bi-shield-exclamation me-2"></i>Safety Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="site_id" class="form-label">Filter by Site</label>
                    <select name="site_id" id="site_id" class="form-select">
                        <option value="">All Sites</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ $siteFilter == $site->id ? 'selected' : '' }}>
                                {{ $site->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="project_id" class="form-label">Filter by Project</label>
                    <select name="project_id" id="project_id" class="form-select">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ $projectFilter == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_range" class="form-label">Date Range</label>
                    <select name="date_range" id="date_range" class="form-select">
                        <option value="today" {{ $dateFilter == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ $dateFilter == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $dateFilter == 'month' ? 'selected' : '' }}>This Month</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-funnel me-2"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stats-card bg-primary">
                <div class="stats-icon">
                    <i class="bi bi-building"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['active_projects'] }}</h3>
                    <p>Active Projects</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stats-card bg-success">
                <div class="stats-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['tasks_today'] }}</h3>
                    <p>Tasks Today</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stats-card bg-warning">
                <div class="stats-icon">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['overdue_tasks'] }}</h3>
                    <p>Overdue Tasks</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stats-card bg-info">
                <div class="stats-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['field_team_members'] }}</h3>
                    <p>Field Team</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stats-card bg-secondary">
                <div class="stats-icon">
                    <i class="bi bi-gear"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['equipment_active'] }}</h3>
                    <p>Active Equipment</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stats-card bg-danger">
                <div class="stats-icon">
                    <i class="bi bi-shield-exclamation"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['safety_incidents'] }}</h3>
                    <p>Safety Reports</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Tabs -->
    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Field Operations Modules -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-grid-3x3-gap me-2"></i>Field Operations Modules
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('field-operations.equipment') }}" class="module-card">
                                <div class="module-icon bg-primary">
                                    <i class="bi bi-gear-fill"></i>
                                </div>
                                <div class="module-content">
                                    <h6>Equipment</h6>
                                    <p class="text-muted">Manage machinery and tools</p>
                                    <span class="badge bg-primary">{{ $equipmentStatus['active'] }} Active</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('field-operations.materials') }}" class="module-card">
                                <div class="module-icon bg-warning">
                                    <i class="bi bi-boxes"></i>
                                </div>
                                <div class="module-content">
                                    <h6>Materials</h6>
                                    <p class="text-muted">Track inventory and supplies</p>
                                    <span class="badge bg-warning">12 Items</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('field-operations.safety') }}" class="module-card">
                                <div class="module-icon bg-danger">
                                    <i class="bi bi-shield-exclamation"></i>
                                </div>
                                <div class="module-content">
                                    <h6>Safety</h6>
                                    <p class="text-muted">Incident reports and safety</p>
                                    <span class="badge bg-danger">{{ $safetyIncidents['investigating'] }} Open</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('field-operations.work-orders') }}" class="module-card">
                                <div class="module-icon bg-success">
                                    <i class="bi bi-clipboard-check"></i>
                                </div>
                                <div class="module-content">
                                    <h6>Work Orders</h6>
                                    <p class="text-muted">Manage work assignments</p>
                                    <span class="badge bg-success">5 Active</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="#" class="module-card">
                                <div class="module-icon bg-info">
                                    <i class="bi bi-camera"></i>
                                </div>
                                <div class="module-content">
                                    <h6>Photo Reports</h6>
                                    <p class="text-muted">Progress documentation</p>
                                    <span class="badge bg-info">Coming Soon</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="#" class="module-card">
                                <div class="module-icon bg-secondary">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div class="module-content">
                                    <h6>GPS Tracking</h6>
                                    <p class="text-muted">Location monitoring</p>
                                    <span class="badge bg-secondary">Coming Soon</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Tasks -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-task me-2"></i>Recent Field Tasks
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentTasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Task</th>
                                        <th>Project</th>
                                        <th>Site</th>
                                        <th>Assigned To</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTasks as $task)
                                        <tr>
                                            <td>
                                                <div class="task-info">
                                                    <h6 class="mb-1">{{ $task->title }}</h6>
                                                    @if($task->description)
                                                        <small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $task->project->name ?? 'N/A' }}</td>
                                            <td>{{ $task->project->site->name ?? 'N/A' }}</td>
                                            <td>
                                                @if($task->assignedUser)
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                            {{ substr($task->assignedUser->name, 0, 1) }}
                                                        </div>
                                                        <span class="small">{{ $task->assignedUser->name }}</span>
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
                                                @if($task->due_date)
                                                    <span class="{{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-danger' : '' }}">
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
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-list-task text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No recent field tasks found for the selected filters</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Active Projects -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-building me-2"></i>Active Projects
                    </h6>
                </div>
                <div class="card-body">
                    @if($activeProjects->count() > 0)
                        @foreach($activeProjects as $project)
                            <div class="project-item mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="project-icon bg-{{ $project->status === 'in_progress' ? 'success' : 'warning' }} text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                        <i class="bi bi-building"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $project->name }}</h6>
                                        <div class="text-muted small">
                                            <i class="bi bi-geo-alt me-1"></i>{{ $project->site->name ?? 'No site' }}
                                        </div>
                                        <div class="text-muted small">
                                            <i class="bi bi-person me-1"></i>{{ $project->client->name ?? 'No client' }}
                                        </div>
                                        <span class="badge bg-{{ $project->status === 'in_progress' ? 'success' : 'warning' }} mt-1">
                                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-building text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2 mb-0">No active projects</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Equipment Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-gear me-2"></i>Equipment Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="equipment-status-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-success"><i class="bi bi-check-circle me-2"></i>Active</span>
                            <span class="fw-bold">{{ $equipmentStatus['active'] }}</span>
                        </div>
                    </div>
                    <div class="equipment-status-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-warning"><i class="bi bi-wrench me-2"></i>Maintenance</span>
                            <span class="fw-bold">{{ $equipmentStatus['maintenance'] }}</span>
                        </div>
                    </div>
                    <div class="equipment-status-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-danger"><i class="bi bi-x-circle me-2"></i>Offline</span>
                            <span class="fw-bold">{{ $equipmentStatus['offline'] }}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Total Equipment</span>
                        <span class="fw-bold">{{ $equipmentStatus['total'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Safety Summary -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-shield-exclamation me-2"></i>Safety Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="safety-status-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-success"><i class="bi bi-check-circle me-2"></i>Resolved</span>
                            <span class="fw-bold">{{ $safetyIncidents['resolved'] }}</span>
                        </div>
                    </div>
                    <div class="safety-status-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-warning"><i class="bi bi-search me-2"></i>Investigating</span>
                            <span class="fw-bold">{{ $safetyIncidents['investigating'] }}</span>
                        </div>
                    </div>
                    <div class="safety-status-item mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-danger"><i class="bi bi-exclamation-triangle me-2"></i>High Priority</span>
                            <span class="fw-bold">{{ $safetyIncidents['high_priority'] }}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Total Reports</span>
                        <span class="fw-bold">{{ $safetyIncidents['total'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.field-operations-container {
    max-width: 100%;
}

.stats-card {
    padding: 1.5rem;
    border-radius: 12px;
    color: white;
    text-decoration: none;
    display: block;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transform: translate(30px, -30px);
}

.stats-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    opacity: 0.8;
}

.stats-content h3 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 0.25rem 0;
}

.stats-content p {
    margin: 0;
    font-size: 0.875rem;
    opacity: 0.9;
}

.module-card {
    display: block;
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
    height: 100%;
}

.module-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    color: inherit;
    text-decoration: none;
}

.module-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    font-size: 1.25rem;
    color: white;
}

.module-content h6 {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #1f2937;
}

.module-content p {
    font-size: 0.875rem;
    margin-bottom: 0.75rem;
}

.project-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.project-item:last-child {
    border-bottom: none;
}

.project-icon {
    width: 40px;
    height: 40px;
    font-size: 1rem;
}

.equipment-status-item,
.safety-status-item {
    padding: 0.5rem 0;
}

.task-info h6 {
    font-size: 0.875rem;
    font-weight: 600;
}

.avatar-sm {
    width: 24px;
    height: 24px;
    font-size: 11px;
    font-weight: 600;
}

@media (max-width: 768px) {
    .stats-card {
        margin-bottom: 1rem;
    }
    
    .module-card {
        margin-bottom: 1rem;
    }
}
</style>
@endsection
