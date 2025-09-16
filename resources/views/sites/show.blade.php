@extends('layouts.app')

@section('title', $site->name)

@section('content')
<div class="site-show-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('sites.index') }}">Sites</a></li>
                        <li class="breadcrumb-item active">{{ $site->name }}</li>
                    </ol>
                </nav>
                <h1 class="page-title">{{ $site->name }}</h1>
                <p class="page-subtitle">
                    <i class="bi bi-building me-2"></i>{{ $site->client->display_name }}
                    @if($site->client->primary_contact)
                        <span class="text-muted ms-2">• Contact: {{ $site->client->primary_contact }}</span>
                    @endif
                    @if($site->full_address)
                        <i class="bi bi-geo-alt ms-3 me-2"></i>{{ $site->full_address }}
                    @endif
                </p>
            </div>
            <div class="col-lg-4 text-end">
                @if(auth()->user()->canManageProjects())
                    <div class="btn-group">
                        <a href="{{ route('sites.edit', $site) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Site
                        </a>
                        <a href="{{ route('projects.create', ['site_id' => $site->id]) }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Add Project
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content - Made Larger -->
        <div class="col-lg-9">
            <!-- Financial Report Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 bg-gradient-primary text-white">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-currency-dollar fs-2 opacity-75"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fs-6 fw-bold">${{ number_format($financial_stats['site_budget'], 0) }}</div>
                                    <div class="small opacity-75">Total Site Budget</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-0 bg-gradient-info text-white">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-cash-stack fs-2 opacity-75"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fs-6 fw-bold">${{ number_format($financial_stats['total_projects_revenue'], 0) }}</div>
                                    <div class="small opacity-75">Total Projects Revenue</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-0 bg-gradient-warning text-white">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-receipt fs-2 opacity-75"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fs-6 fw-bold">${{ number_format($financial_stats['total_direct_costs'], 0) }}</div>
                                    <div class="small opacity-75">Direct Costs</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-0 bg-gradient-{{ $financial_stats['remaining_budget'] >= 0 ? 'success' : 'danger' }} text-white">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-{{ $financial_stats['remaining_budget'] >= 0 ? 'piggy-bank' : 'exclamation-triangle' }} fs-2 opacity-75"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fs-6 fw-bold">${{ number_format($financial_stats['remaining_budget'], 0) }}</div>
                                    <div class="small opacity-75">Remaining Budget</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Details Row -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <h6 class="card-title mb-2">Cost Breakdown</h6>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Expenses:</span>
                                <span class="fw-semibold small">${{ number_format($financial_stats['total_expenses'], 0) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Invoices Paid:</span>
                                <span class="fw-semibold small">${{ number_format($financial_stats['total_invoices_paid'], 0) }}</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold small">Total Direct Costs:</span>
                                <span class="fw-bold small">${{ number_format($financial_stats['total_direct_costs'], 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <h6 class="card-title mb-2">Financial Analysis</h6>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Projects Revenue:</span>
                                <span class="fw-semibold small">${{ number_format($financial_stats['total_projects_revenue'], 0) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Profit Margin:</span>
                                <span class="fw-semibold small text-{{ $financial_stats['profit_margin'] >= 0 ? 'success' : 'danger' }}">
                                    {{ number_format($financial_stats['profit_margin'], 1) }}%
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Cost Utilization:</span>
                                <span class="fw-semibold small">{{ number_format($financial_stats['budget_utilization'], 1) }}%</span>
                            </div>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-{{ $financial_stats['budget_utilization'] <= 100 ? 'success' : 'danger' }}" 
                                     style="width: {{ min($financial_stats['budget_utilization'], 100) }}%"></div>
                            </div>
                            @if($financial_stats['site_budget'] > 0)
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Site Budget (Guidance):</span>
                                    <span class="fw-semibold small text-muted">${{ number_format($financial_stats['site_budget'], 0) }}</span>
                                </div>
                                @if($financial_stats['budget_variance'] != 0)
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted small">Budget Variance:</span>
                                        <span class="fw-semibold small text-{{ $financial_stats['budget_variance'] >= 0 ? 'success' : 'warning' }}">
                                            {{ $financial_stats['budget_variance'] >= 0 ? '+' : '' }}${{ number_format($financial_stats['budget_variance'], 0) }}
                                            ({{ $financial_stats['budget_variance'] >= 0 ? '+' : '' }}{{ number_format($financial_stats['budget_variance_percentage'], 1) }}%)
                                        </span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Projects - Now with more space -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 me-3">
                                Projects
                                <span id="projectsViewIndicator" class="badge bg-info ms-2" style="display: none;">
                                    <i class="bi bi-archive me-1"></i>Viewing Archived
                                </span>
                            </h5>
                            @php
                                $archivedCount = $site->projects->where('is_active', false)->count();
                            @endphp
                            @if($archivedCount > 0)
                                <span class="badge bg-secondary me-2" style="cursor: pointer;" onclick="showArchivedProjects()" title="Click to view archived projects">
                                    <i class="bi bi-archive me-1"></i>{{ $archivedCount }} Archived
                                </span>
                            @endif
                        </div>
                        @if(auth()->user()->canManageProjects())
                            <a href="{{ route('projects.create', ['site_id' => $site->id]) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus me-1"></i>Add Project
                            </a>
                        @endif
                    </div>
                    <!-- Project Filter -->
                    <div class="row g-2">
                        <div class="col-md-3">
                            <select class="form-select form-select-sm" id="siteProjectStatusFilter">
                                <option value="">All Statuses</option>
                                <option value="planning">Planning</option>
                                <option value="in_progress">In Progress</option>
                                <option value="on_hold">On Hold</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select form-select-sm" id="siteProjectManagerFilter">
                                <option value="">All Managers</option>
                                @foreach($site->projects->pluck('manager')->filter()->unique('id') as $manager)
                                    <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control form-control-sm" id="siteProjectSearchFilter" placeholder="Search projects...">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select form-select-sm" id="siteProjectArchivedFilter">
                                <option value="">Active Projects</option>
                                <option value="archived">🗃️ Archived Projects</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="showActiveProjectsBtn" onclick="showActiveProjects()" title="Show Active Projects">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="clearSiteProjectFilters">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($site->projects->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Project Name</th>
                                        <th>Status</th>
                                        <th>Manager</th>
                                        <th>Progress</th>
                                        <th>Tasks</th>
                                        <th>Budget</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($site->projects as $project)
                                        <tr>
                                            <td>
                                                <div class="project-info">
                                                    <h6 class="mb-1">
                                                        <a href="{{ route('projects.show', $project) }}" class="text-decoration-none">
                                                            {{ $project->name }}
                                                        </a>
                                                        @if(!$project->is_active)
                                                            <span class="badge bg-secondary ms-2">
                                                                <i class="bi bi-archive me-1"></i>Archived
                                                            </span>
                                                        @endif
                                                    </h6>
                                                    @if($project->description)
                                                        <small class="text-muted">{{ Str::limit($project->description, 60) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if(auth()->user()->canManageProjects())
                                                    <select class="form-select form-select-sm site-project-status-dropdown" 
                                                            data-project-id="{{ $project->id }}" 
                                                            data-current-status="{{ $project->status }}"
                                                            style="width: auto; min-width: 120px;">
                                                        <option value="planning" {{ $project->status == 'planning' ? 'selected' : '' }}>Planning</option>
                                                        <option value="in_progress" {{ $project->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                        <option value="on_hold" {{ $project->status == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                                        <option value="completed" {{ $project->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                        <option value="cancelled" {{ $project->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                    </select>
                                                @else
                                                    <span class="badge bg-{{ $project->status_color }}">
                                                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                                    </span>
                                                @endif
                                                @if($project->is_overdue)
                                                    <br><small class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Overdue</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($project->manager)
                                                    <span class="fw-semibold">{{ $project->manager->name }}</span>
                                                @else
                                                    <span class="text-muted">Not assigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="progress-container">
                                                    <div class="progress mb-1" style="height: 8px; width: 80px;">
                                                        <div class="progress-bar" style="width: {{ $project->progress }}%"></div>
                                                    </div>
                                                    <small class="text-muted">{{ $project->progress }}%</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $project->tasks->count() }}</span>
                                                @if($project->tasks->where('status', 'completed')->count() > 0)
                                                    <br><small class="text-success">{{ $project->tasks->where('status', 'completed')->count() }} done</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($project->budget)
                                                    <span class="fw-semibold">${{ number_format($project->budget, 0) }}</span>
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($project->end_date)
                                                    <div class="date-info">
                                                        {{ $project->end_date->format('M j, Y') }}
                                                        <br><small class="text-muted">{{ $project->end_date->diffForHumans() }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">No due date</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if(auth()->user()->canManageProjects())
                                                        <a href="{{ route('projects.edit', ['project' => $project, 'from_site' => 1]) }}" class="btn btn-outline-secondary btn-sm">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        @if($project->is_active)
                                                            <form method="POST" action="{{ route('projects.archive', $project) }}" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-outline-warning btn-sm" 
                                                                        onclick="return confirm('Archive this project?')"
                                                                        title="Archive Project">
                                                                    <i class="bi bi-archive"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form method="POST" action="{{ route('projects.unarchive', $project) }}" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-outline-success btn-sm" 
                                                                        onclick="return confirm('Unarchive this project?')"
                                                                        title="Unarchive Project">
                                                                    <i class="bi bi-arrow-up-circle"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state text-center py-4">
                            <i class="bi bi-folder display-4 text-muted"></i>
                            <h6 class="mt-3">No projects yet</h6>
                            <p class="text-muted">Start by creating your first project for this site.</p>
                            @if(auth()->user()->canManageProjects())
                                <a href="{{ route('projects.create', ['site_id' => $site->id]) }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>Create First Project
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar - Made Smaller -->
        <div class="col-lg-3">
            <!-- Site Details -->
            <div class="card mb-3">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0">Site Details</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="detail-group mb-2">
                                <label class="detail-label small">Status</label>
                                <div class="detail-value">
                                    <span class="badge bg-{{ $site->status_color }} small">
                                        {{ ucfirst(str_replace('_', ' ', $site->status)) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="detail-group mb-2">
                                <label class="detail-label small">Priority</label>
                                <div class="detail-value">
                                    <span class="badge bg-{{ $site->priority_color }} small">
                                        {{ ucfirst($site->priority) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @if($site->description)
                            <div class="col-12">
                                <div class="detail-group mb-2">
                                    <label class="detail-label small">Description</label>
                                    <div class="detail-value small">{{ Str::limit($site->description, 80) }}</div>
                                </div>
                            </div>
                        @endif
                        @if($site->total_budget)
                            <div class="col-12">
                                <div class="detail-group mb-2">
                                    <label class="detail-label small">Total Budget</label>
                                    <div class="detail-value small fw-bold text-success">${{ number_format($site->total_budget, 0) }}</div>
                                </div>
                            </div>
                        @endif
                        @if($site->start_date)
                            <div class="col-12">
                                <div class="detail-group mb-2">
                                    <label class="detail-label small">Start Date</label>
                                    <div class="detail-value small">{{ $site->start_date->format('M j, Y') }}</div>
                                </div>
                            </div>
                        @endif
                        @if($site->expected_completion)
                            <div class="col-12">
                                <div class="detail-group mb-0">
                                    <label class="detail-label small">Expected Completion</label>
                                    <div class="detail-value small">{{ $site->expected_completion->format('M j, Y') }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Site Statistics -->
            <div class="card mb-3">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0">Site Statistics</h6>
                </div>
                <div class="card-body p-3">
                    <div class="stat-item-compact mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon-small bg-primary me-2">
                                    <i class="bi bi-folder"></i>
                                </div>
                                <span class="small">Total Projects</span>
                            </div>
                            <span class="fw-bold">{{ $stats['total_projects'] }}</span>
                        </div>
                    </div>
                    
                    <div class="stat-item-compact mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon-small bg-success me-2">
                                    <i class="bi bi-folder-check"></i>
                                </div>
                                <span class="small">Completed</span>
                            </div>
                            <span class="fw-bold">{{ $stats['completed_projects'] }}</span>
                        </div>
                    </div>
                    
                    <div class="stat-item-compact mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon-small bg-warning me-2">
                                    <i class="bi bi-folder-symlink"></i>
                                </div>
                                <span class="small">Active</span>
                            </div>
                            <span class="fw-bold">{{ $stats['active_projects'] }}</span>
                        </div>
                    </div>
                    
                    <div class="stat-item-compact mb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon-small bg-info me-2">
                                    <i class="bi bi-list-task"></i>
                                </div>
                                <span class="small">Total Tasks</span>
                            </div>
                            <span class="fw-bold">{{ $stats['total_tasks'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Overview -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Overall Progress</h6>
                </div>
                <div class="card-body text-center">
                    <div class="progress-circle mb-3">
                        <div class="progress-text">
                            <span class="progress-percentage">{{ $site->progress }}%</span>
                            <small class="text-muted d-block">Complete</small>
                        </div>
                    </div>
                    
                    @if($site->expected_completion_date)
                        <div class="completion-info">
                            <small class="text-muted d-block">Expected Completion</small>
                            <strong class="{{ $site->is_overdue ? 'text-danger' : 'text-success' }}">
                                {{ $site->expected_completion_date->format('M j, Y') }}
                            </strong>
                            @if($site->is_overdue)
                                <div class="mt-2">
                                    <span class="badge bg-danger">Overdue</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.site-show-container {
    max-width: 100%;
}

/* Project Cards */
.project-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem;
    height: 100%;
    transition: all 0.2s ease;
}

.project-card:hover {
    background: #f1f5f9;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.project-card-header {
    margin-bottom: 0.75rem;
}

.project-title a {
    color: #1e293b;
    text-decoration: none;
    font-weight: 600;
}

.project-title a:hover {
    color: #4f46e5;
}

.project-description {
    color: #64748b;
    font-size: 0.875rem;
    line-height: 1.4;
    margin-bottom: 0.75rem;
}

.project-meta {
    font-size: 0.8rem;
}

.project-progress .progress-bar {
    background: linear-gradient(90deg, #4f46e5, #7c3aed);
}

.project-stats {
    font-size: 0.8rem;
}

/* Detail Groups */
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

/* Stat Items */
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

/* Progress Circle */
.progress-circle {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto;
    border-radius: 50%;
    background: conic-gradient(#4f46e5 0deg, #4f46e5 {{ $site->progress * 3.6 }}deg, #e5e7eb {{ $site->progress * 3.6 }}deg, #e5e7eb 360deg);
    display: flex;
    align-items: center;
    justify-content: center;
}

.progress-circle::before {
    content: '';
    position: absolute;
    width: 80px;
    height: 80px;
    background: white;
    border-radius: 50%;
}

.progress-text {
    position: relative;
    z-index: 1;
    text-align: center;
}

.progress-percentage {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
}

.completion-info {
    margin-top: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
    .project-card {
        margin-bottom: 1rem;
    }
    
    .page-header .btn-group {
        width: 100%;
        margin-top: 1rem;
    }
    
    .page-header .btn-group .btn {
        flex: 1;
    }
}

/* Financial Report Cards */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.bg-gradient-danger {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.financial-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.financial-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

/* Financial metrics progress bar */
.financial-metrics .progress {
    background-color: rgba(255, 255, 255, 0.2);
}

.financial-metrics .progress-bar {
    background-color: rgba(255, 255, 255, 0.8);
}

/* Compact sidebar styles */
.stat-item-compact {
    padding: 0.25rem 0;
}

.stat-icon-small {
    width: 24px;
    height: 24px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.75rem;
}

.detail-group {
    margin-bottom: 0.5rem;
}

.detail-label {
    font-weight: 600;
    color: #6b7280;
    font-size: 0.75rem;
    margin-bottom: 0.125rem;
    display: block;
}

.detail-value {
    color: #1f2937;
    font-size: 0.875rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle project status changes with simple GET requests
    document.querySelectorAll('.site-project-status-dropdown').forEach(function(dropdown) {
        dropdown.addEventListener('change', function() {
            const projectId = this.dataset.projectId;
            const newStatus = this.value;
            const currentStatus = this.dataset.currentStatus;
            
            if (newStatus !== currentStatus) {
                // Show loading state
                this.disabled = true;
                
                // Use simple GET request to update status
                window.location.href = `/projects/${projectId}/status/${newStatus}`;
            }
        });
    });

    // Project filtering functionality
    const statusFilter = document.getElementById('siteProjectStatusFilter');
    const managerFilter = document.getElementById('siteProjectManagerFilter');
    const searchFilter = document.getElementById('siteProjectSearchFilter');
    const archivedFilter = document.getElementById('siteProjectArchivedFilter');
    const clearFilters = document.getElementById('clearSiteProjectFilters');
    const projectRows = document.querySelectorAll('tbody tr');

    function filterProjects() {
        const statusValue = statusFilter.value.toLowerCase();
        const managerValue = managerFilter.value;
        const searchValue = searchFilter.value.toLowerCase();
        const archivedValue = archivedFilter.value;

        // Update the viewing indicator
        const indicator = document.getElementById('projectsViewIndicator');
        if (indicator) {
            if (archivedValue === 'archived') {
                indicator.style.display = 'inline';
            } else {
                indicator.style.display = 'none';
            }
        }

        projectRows.forEach(function(row) {
            let show = true;

            // Get project data from row
            const projectName = row.querySelector('.project-info h6 a').textContent.toLowerCase();
            const projectDescription = row.querySelector('.project-info small')?.textContent.toLowerCase() || '';
            const projectStatus = row.querySelector('.site-project-status-dropdown')?.value.toLowerCase() || 
                                 row.querySelector('.badge')?.textContent.toLowerCase().replace(' ', '_') || '';
            const projectManager = row.querySelector('.site-project-status-dropdown')?.closest('tr').querySelector('td:nth-child(3)')?.textContent.trim() || '';
            const isArchived = row.querySelector('.badge.bg-secondary') !== null;

            // Apply filters
            if (statusValue && !projectStatus.includes(statusValue)) {
                show = false;
            }

            if (managerValue && !projectManager.includes(managerValue)) {
                show = false;
            }

            if (searchValue && !projectName.includes(searchValue) && !projectDescription.includes(searchValue)) {
                show = false;
            }

            if (archivedValue === 'archived' && !isArchived) {
                show = false;
            } else if (archivedValue === '' && isArchived) {
                show = false;
            }

            // Show/hide row
            row.style.display = show ? '' : 'none';
        });
    }

    // Add event listeners
    if (statusFilter) statusFilter.addEventListener('change', filterProjects);
    if (managerFilter) managerFilter.addEventListener('change', filterProjects);
    if (searchFilter) searchFilter.addEventListener('input', filterProjects);
    if (archivedFilter) archivedFilter.addEventListener('change', filterProjects);

    // Clear filters
    if (clearFilters) {
        clearFilters.addEventListener('click', function() {
            statusFilter.value = '';
            managerFilter.value = '';
            searchFilter.value = '';
            archivedFilter.value = '';
            filterProjects();
        });
    }

    // Initialize filter to show only active projects by default
    filterProjects();
});

// Function to show archived projects when clicking the archived badge
function showArchivedProjects() {
    const archivedFilter = document.getElementById('siteProjectArchivedFilter');
    if (archivedFilter) {
        archivedFilter.value = 'archived';
        // Trigger the change event to apply the filter
        archivedFilter.dispatchEvent(new Event('change'));
    }
}

// Function to show active projects
function showActiveProjects() {
    const archivedFilter = document.getElementById('siteProjectArchivedFilter');
    if (archivedFilter) {
        archivedFilter.value = '';
        // Trigger the change event to apply the filter
        archivedFilter.dispatchEvent(new Event('change'));
    }
}
</script>
@endsection