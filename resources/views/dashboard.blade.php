@extends('layouts.app')

@section('title', 'Executive Dashboard')

@section('content')
<div class="executive-dashboard">
    <!-- Executive Header -->
    <div class="executive-header mb-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="executive-welcome">
                    <h1 class="executive-title">
                        Executive Dashboard
                    </h1>
                    <p class="executive-subtitle">
                        {{ auth()->user()->company->name ?? 'Company' }} • {{ now()->format('l, F j, Y') }}
                    </p>
                    <div class="executive-greeting">
                        Good {{ date('H') < 12 ? 'Morning' : (date('H') < 17 ? 'Afternoon' : 'Evening') }}, {{ auth()->user()->name }}
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="executive-actions">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="generateReport()">
                            <i class="bi bi-file-earmark-text me-2"></i>Generate Report
                        </button>
                        <button type="button" class="btn btn-primary" onclick="exportData()">
                            <i class="bi bi-download me-2"></i>Export Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Executive KPI Cards -->
    <div class="row g-4 mb-5">
        <!-- Monthly Revenue -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="kpi-card revenue-card">
                <div class="kpi-header">
                    <div class="kpi-icon">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="kpi-trend positive">
                        <i class="bi bi-arrow-up"></i>
                        <span>+12.5%</span>
                    </div>
                </div>
                <div class="kpi-content">
                    <h2 class="kpi-value">£{{ number_format($stats['monthly_revenue'] ?? 0, 0) }}</h2>
                    <p class="kpi-label">Monthly Revenue</p>
                    <div class="kpi-progress">
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: 78%"></div>
                        </div>
                        <small class="text-muted">78% of target</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Completion Rate -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="kpi-card completion-card">
                <div class="kpi-header">
                    <div class="kpi-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="kpi-trend positive">
                        <i class="bi bi-arrow-up"></i>
                        <span>+5.2%</span>
                    </div>
                </div>
                <div class="kpi-content">
                    <h2 class="kpi-value">{{ $stats['completion_rate'] ?? 0 }}%</h2>
                    <p class="kpi-label">Project Completion Rate</p>
                    <div class="kpi-details">
                        <span class="detail-item">{{ $stats['total_projects'] ?? 0 }} total projects</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Budget Utilization -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="kpi-card budget-card">
                <div class="kpi-header">
                    <div class="kpi-icon">
                        <i class="bi bi-pie-chart"></i>
                    </div>
                    <div class="kpi-trend {{ ($stats['budget_utilization'] ?? 0) > 90 ? 'negative' : 'positive' }}">
                        <i class="bi bi-{{ ($stats['budget_utilization'] ?? 0) > 90 ? 'exclamation-triangle' : 'check-circle' }}"></i>
                        <span>{{ $stats['budget_utilization'] ?? 0 }}%</span>
                    </div>
                </div>
                <div class="kpi-content">
                    <h2 class="kpi-value">£{{ number_format($stats['used_budget'] ?? 0, 0) }}</h2>
                    <p class="kpi-label">Budget Utilized</p>
                    <div class="kpi-progress">
                        <div class="progress">
                            <div class="progress-bar {{ ($stats['budget_utilization'] ?? 0) > 90 ? 'bg-danger' : 'bg-primary' }}" 
                                 style="width: {{ $stats['budget_utilization'] ?? 0 }}%"></div>
                        </div>
                        <small class="text-muted">of £{{ number_format($stats['total_budget'] ?? 0, 0) }} total</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Efficiency -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="kpi-card efficiency-card">
                <div class="kpi-header">
                    <div class="kpi-icon">
                        <i class="bi bi-speedometer2"></i>
                    </div>
                    <div class="kpi-trend positive">
                        <i class="bi bi-arrow-up"></i>
                        <span>+8.1%</span>
                    </div>
                </div>
                <div class="kpi-content">
                    <h2 class="kpi-value">{{ $stats['task_efficiency'] ?? 0 }}%</h2>
                    <p class="kpi-label">Task Efficiency</p>
                    <div class="kpi-details">
                        <span class="detail-item">{{ $stats['completed_tasks'] ?? 0 }} completed</span>
                        <span class="detail-item">{{ $stats['overdue_tasks'] ?? 0 }} overdue</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Metrics -->
    <div class="row g-4 mb-5">
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="metric-card">
                <div class="metric-icon bg-primary">
                    <i class="bi bi-building"></i>
                </div>
                <div class="metric-content">
                    <h4>{{ $stats['total_sites'] ?? 0 }}</h4>
                    <p>Active Sites</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="metric-card">
                <div class="metric-icon bg-success">
                    <i class="bi bi-folder"></i>
                </div>
                <div class="metric-content">
                    <h4>{{ $stats['active_projects'] ?? 0 }}</h4>
                    <p>Active Projects</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="metric-card">
                <div class="metric-icon bg-warning">
                    <i class="bi bi-list-task"></i>
                </div>
                <div class="metric-content">
                    <h4>{{ $stats['pending_tasks'] ?? 0 }}</h4>
                    <p>Pending Tasks</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="metric-card">
                <div class="metric-icon bg-info">
                    <i class="bi bi-people"></i>
                </div>
                <div class="metric-content">
                    <h4>{{ $stats['team_members'] ?? 0 }}</h4>
                    <p>Team Members</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="metric-card">
                <div class="metric-icon bg-secondary">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div class="metric-content">
                    <h4>{{ $stats['total_clients'] ?? 0 }}</h4>
                    <p>Clients</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="metric-card">
                <div class="metric-icon bg-danger">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="metric-content">
                    <h4>£{{ number_format($stats['pending_invoices'] ?? 0, 0) }}</h4>
                    <p>Pending Invoices</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Dashboard -->
    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-xl-8 col-lg-7">
            <!-- Performance Analytics -->
            <div class="analytics-card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Revenue & Performance Analytics</h5>
                            <p class="card-subtitle">Monthly trends and forecasting</p>
                        </div>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary active" data-period="month">Month</button>
                            <button type="button" class="btn btn-outline-secondary" data-period="quarter">Quarter</button>
                            <button type="button" class="btn btn-outline-secondary" data-period="year">Year</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" height="300"></canvas>
                </div>
            </div>

            <!-- Project Portfolio -->
            <div class="analytics-card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Active Project Portfolio</h5>
                            <p class="card-subtitle">Current project status and progress</p>
                        </div>
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-arrow-right me-1"></i>View All Projects
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentProjects && $recentProjects->count() > 0)
                        <div class="project-portfolio">
                            @foreach($recentProjects as $project)
                                <div class="portfolio-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-7">
                                            <div class="project-details">
                                                <div class="d-flex align-items-center mb-2">
                                                    <h6 class="project-title mb-0">
                                                        <a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a>
                                                    </h6>
                                                    <span class="badge ms-2 bg-{{ $project->status === 'completed' ? 'success' : ($project->status === 'in_progress' ? 'primary' : 'secondary') }}">
                                                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                                    </span>
                                                </div>
                                                <div class="project-meta">
                                                    <span class="meta-item">
                                                        <i class="bi bi-person"></i>{{ $project->client->name ?? 'No client' }}
                                                    </span>
                                                    <span class="meta-item">
                                                        <i class="bi bi-calendar"></i>{{ $project->end_date ? $project->end_date->format('M j, Y') : 'No due date' }}
                                                    </span>
                                                    <span class="meta-item">
                                                        <i class="bi bi-currency-pound"></i>{{ number_format($project->budget ?? 0, 0) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="project-progress">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="progress-label">Progress</span>
                                                    <span class="progress-value">{{ $project->progress ?? 0 }}%</span>
                                                </div>
                                                <div class="progress progress-modern">
                                                    <div class="progress-bar" style="width: {{ $project->progress ?? 0 }}%"></div>
                                                </div>
                                                <div class="progress-stats mt-2">
                                                    <small class="text-muted">
                                                        {{ $project->tasks()->where('status', 'completed')->count() }} of {{ $project->tasks()->count() }} tasks completed
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state-professional">
                            <div class="empty-icon">
                                <i class="bi bi-kanban"></i>
                            </div>
                            <h6>No Active Projects</h6>
                            <p class="text-muted">Start your first project to see portfolio analytics</p>
                            @if(auth()->user()->canManageProjects())
                                <a href="{{ route('projects.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>Create New Project
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Active Sites -->
            <div class="analytics-card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Active Sites</h5>
                            <p class="card-subtitle">Current site status and project progress</p>
                        </div>
                        <a href="{{ route('sites.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-arrow-right me-1"></i>View All Sites
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentSites && $recentSites->count() > 0)
                        <div class="project-portfolio">
                            @foreach($recentSites as $site)
                                <div class="portfolio-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-7">
                                            <div class="project-details">
                                                <div class="d-flex align-items-center mb-2">
                                                    <h6 class="project-title mb-0">
                                                        <a href="{{ route('sites.show', $site) }}">{{ $site->name }}</a>
                                                    </h6>
                                                    <span class="badge ms-2 bg-{{ $site->status === 'completed' ? 'success' : ($site->status === 'active' ? 'primary' : ($site->status === 'on_hold' ? 'warning' : 'secondary')) }}">
                                                        {{ ucfirst(str_replace('_', ' ', $site->status)) }}
                                                    </span>
                                                </div>
                                                <div class="project-meta">
                                                    <span class="meta-item">
                                                        <i class="bi bi-building"></i>{{ $site->client->company_name ?? 'No client' }}
                                                    </span>
                                                    <span class="meta-item">
                                                        <i class="bi bi-geo-alt"></i>{{ $site->city ?? 'No location' }}
                                                    </span>
                                                    <span class="meta-item">
                                                        <i class="bi bi-kanban"></i>{{ $site->projects_count }} project{{ $site->projects_count !== 1 ? 's' : '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="project-progress">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="progress-label">Progress</span>
                                                    <span class="progress-value">{{ $site->progress ?? 0 }}%</span>
                                                </div>
                                                <div class="progress progress-modern">
                                                    <div class="progress-bar" style="width: {{ $site->progress ?? 0 }}%"></div>
                                                </div>
                                                <div class="progress-stats mt-2">
                                                    <small class="text-muted">
                                                        {{ $site->completed_projects_count ?? 0 }} of {{ $site->projects_count ?? 0 }} projects completed
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state-professional">
                            <div class="empty-icon">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <h6>No Active Sites</h6>
                            <p class="text-muted">Create your first site to see location analytics</p>
                            @if(auth()->user()->canManageProjects())
                                <a href="{{ route('sites.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"></i>Create New Site
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-xl-4 col-lg-5">
            <!-- Executive Actions -->
            <div class="executive-panel mb-4">
                <div class="panel-header">
                    <h5 class="panel-title">Executive Actions</h5>
                </div>
                <div class="panel-body">
                    <div class="action-grid">
                        @if(auth()->user()->canManageProjects())
                            <a href="{{ route('projects.create') }}" class="action-card primary">
                                <div class="action-icon">
                                    <i class="bi bi-plus-circle"></i>
                                </div>
                                <div class="action-content">
                                    <h6>New Project</h6>
                                    <p>Start a new construction project</p>
                                </div>
                            </a>
                        @endif
                        
                        <a href="{{ route('financial-reports.index') }}" class="action-card success">
                            <div class="action-icon">
                                <i class="bi bi-graph-up"></i>
                            </div>
                            <div class="action-content">
                                <h6>Financial Reports</h6>
                                <p>View revenue and expense analytics</p>
                            </div>
                        </a>
                        
                        @if(auth()->user()->canManageClients())
                            <a href="{{ route('clients.create') }}" class="action-card info">
                                <div class="action-icon">
                                    <i class="bi bi-person-plus"></i>
                                </div>
                                <div class="action-content">
                                    <h6>Add Client</h6>
                                    <p>Register a new client</p>
                                </div>
                            </a>
                        @endif
                        
                        <a href="{{ route('team.index') }}" class="action-card warning">
                            <div class="action-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="action-content">
                                <h6>Team Management</h6>
                                <p>Manage your workforce</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- My Tasks -->
            <div class="content-card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">My Tasks</h5>
                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentTasks && $recentTasks->count() > 0)
                        <div class="tasks-list">
                            @foreach($recentTasks as $task)
                                <div class="task-item d-flex align-items-start mb-3 p-2 rounded
                                    @if($task->status == 'completed')
                                        bg-success bg-opacity-10
                                    @elseif($task->due_date && $task->due_date->isPast() && $task->status !== 'completed')
                                        bg-danger bg-opacity-10
                                    @elseif($task->due_date && $task->due_date->diffInDays(now()) <= 3 && $task->status !== 'completed')
                                        bg-warning bg-opacity-10
                                    @endif
                                ">
                                    <input type="checkbox" class="form-check-input me-3 mt-1" {{ $task->status === 'completed' ? 'checked' : '' }}>
                                    <div class="flex-grow-1">
                                        <h6 class="task-title mb-1">{{ $task->title }}</h6>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-muted">{{ $task->project->name ?? 'No project' }}</small>
                                            <span class="badge bg-{{ $task->priority === 'high' ? 'danger' : ($task->priority === 'medium' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                        </div>
                                        @if($task->due_date)
                                            <small class="text-muted">
                                                <i class="bi bi-calendar me-1"></i>Due {{ $task->due_date->format('M j') }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state text-center py-4">
                            <i class="bi bi-check-circle display-4 text-muted"></i>
                            <p class="text-muted mt-2">All caught up!</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="analytics-card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Recent Activity</h5>
                            <p class="card-subtitle">Latest updates across your projects</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentActivities->count() > 0)
                        <div class="activity-timeline">
                            @foreach($recentActivities as $activity)
                                <div class="timeline-item">
                                    <div class="timeline-dot bg-{{ $activity['color'] }}">
                                        <i class="bi {{ $activity['icon'] }}"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6>{{ $activity['title'] }}</h6>
                                                <p class="text-muted mb-1">{{ $activity['description'] }}</p>
                                            </div>
                                            <small class="text-muted">{{ $activity['time']->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state-professional">
                            <div class="empty-icon">
                                <i class="bi bi-activity"></i>
                            </div>
                            <h6>No Recent Activity</h6>
                            <p class="text-muted">Activities will appear here as your team works on projects</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Chart -->
            <div class="content-card">
                <div class="card-header">
                    <h5 class="card-title">Team Performance</h5>
                    <p class="card-subtitle">This month</p>
                </div>
                <div class="card-body">
                    <canvas id="teamChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Executive Dashboard Styles */
.executive-dashboard {
    max-width: 100%;
    width: 100%;
    background: #f8fafc;
}

/* Executive Header */
.executive-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    color: white;
    padding: 2.5rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.executive-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
}

.executive-welcome {
    position: relative;
    z-index: 1;
}

.executive-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.executive-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 1rem;
}

.executive-greeting {
    font-size: 1rem;
    opacity: 0.8;
}

.executive-actions {
    position: relative;
    z-index: 1;
    text-align: right;
}

/* KPI Cards */
.kpi-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #4f46e5, #7c3aed);
}

.kpi-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.kpi-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.kpi-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.kpi-trend {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
}

.kpi-trend.positive {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.kpi-trend.negative {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.kpi-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.5rem;
    line-height: 1;
}

.kpi-label {
    font-size: 1rem;
    color: #6b7280;
    font-weight: 600;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.kpi-progress {
    margin-top: 1rem;
}

.kpi-progress .progress {
    height: 8px;
    background: #f3f4f6;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.kpi-details {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.detail-item {
    font-size: 0.875rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Secondary Metrics */
.metric-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
    height: 100%;
}

.metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.metric-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.metric-content h4 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.metric-content p {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0;
    font-weight: 500;
}

/* Analytics Cards */
.analytics-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.analytics-card .card-header {
    padding: 2rem 2rem 1rem;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-bottom: 1px solid #e2e8f0;
}

.analytics-card .card-title {
    font-size: 1.375rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.analytics-card .card-subtitle {
    color: #64748b;
    font-size: 0.95rem;
    margin: 0;
}

.analytics-card .card-body {
    padding: 2rem;
}

/* Project Portfolio */
.project-portfolio {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.portfolio-item {
    padding: 1.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    background: #f8fafc;
    transition: all 0.2s ease;
}

.portfolio-item:hover {
    background: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-1px);
}

.project-title a {
    color: #1f2937;
    text-decoration: none;
    font-weight: 700;
    font-size: 1.125rem;
}

.project-title a:hover {
    color: #4f46e5;
}

.project-meta {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
    margin-top: 0.5rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #64748b;
}

.meta-item i {
    color: #94a3b8;
}

.progress-modern {
    height: 10px;
    background: #e2e8f0;
    border-radius: 5px;
    overflow: hidden;
}

.progress-modern .progress-bar {
    background: linear-gradient(90deg, #4f46e5, #7c3aed);
    height: 100%;
    border-radius: 5px;
    transition: width 0.3s ease;
}

.progress-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #475569;
}

.progress-value {
    font-size: 0.875rem;
    font-weight: 700;
    color: #1f2937;
}

/* Executive Panel */
.executive-panel {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.panel-header {
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    border-bottom: 1px solid #e2e8f0;
}

.panel-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.panel-body {
    padding: 2rem;
}

.action-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

.action-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.2s ease;
    border: 2px solid transparent;
}

.action-card:hover {
    transform: translateY(-2px);
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.action-card.primary {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: white;
}

.action-card.success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.action-card.info {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.action-card.warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.action-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.action-content h6 {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.action-content p {
    font-size: 0.875rem;
    opacity: 0.9;
    margin: 0;
}

/* Empty States */
.empty-state-professional {
    text-align: center;
    padding: 3rem 2rem;
}

.empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: #64748b;
}

.empty-state-professional h6 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.75rem;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .executive-title {
        font-size: 2rem;
    }
    
    .kpi-value {
        font-size: 2rem;
    }
}

@media (max-width: 768px) {
    .executive-header {
        padding: 2rem;
        text-align: center;
    }
    
    .executive-actions {
        text-align: center;
        margin-top: 1.5rem;
    }
    
    .executive-title {
        font-size: 1.75rem;
    }
    
    .kpi-card {
        padding: 1.5rem;
    }
    
    .kpi-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .metric-card {
        padding: 1.25rem;
    }
    
    .analytics-card .card-header,
    .analytics-card .card-body {
        padding: 1.5rem;
    }
    
    .portfolio-item {
        padding: 1.25rem;
    }
    
    .project-meta {
        flex-direction: column;
        gap: 0.75rem;
    }
}

@media (max-width: 576px) {
    .executive-header {
        padding: 1.5rem;
    }
    
    .executive-title {
        font-size: 1.5rem;
    }
    
    .kpi-card {
        padding: 1.25rem;
    }
    
    .kpi-value {
        font-size: 1.75rem;
    }
    
    .action-grid {
        grid-template-columns: 1fr;
    }
    
    .action-card {
        padding: 1rem;
    }
}
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Executive Dashboard Functionality
document.addEventListener('DOMContentLoaded', function() {
    
    // Performance Analytics Chart
    const performanceCtx = document.getElementById('performanceChart');
    if (performanceCtx) {
        const performanceChart = new Chart(performanceCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Revenue (£000s)',
                    data: [85, 92, 78, 105, 120, 115, 135, 142, 158, 165, 172, 180],
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#4f46e5',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }, {
                    label: 'Project Completion Rate (%)',
                    data: [65, 68, 72, 75, 78, 82, 85, 88, 90, 92, 94, 96],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 13,
                                weight: '600'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#4f46e5',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                if (context.datasetIndex === 0) {
                                    return 'Revenue: £' + context.parsed.y + 'k';
                                } else {
                                    return 'Completion Rate: ' + context.parsed.y + '%';
                                }
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#6b7280'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#6b7280'
                        }
                    }
                },
                elements: {
                    point: {
                        hoverBackgroundColor: '#ffffff'
                    }
                }
            }
        });

        // Period toggle functionality
        document.querySelectorAll('[data-period]').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('[data-period]').forEach(b => b.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                
                // Update chart data based on period (you can implement this)
                const period = this.dataset.period;
                console.log('Period changed to:', period);
            });
        });
    }

    // Task Progress Chart (if exists)
    const taskCtx = document.getElementById('taskProgressChart');
    if (taskCtx) {
        new Chart(taskCtx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'In Progress', 'Pending', 'Overdue'],
                datasets: [{
                    data: [{{ $stats['completed_tasks'] ?? 0 }}, {{ $stats['in_progress_tasks'] ?? 0 }}, {{ $stats['pending_tasks'] ?? 0 }}, {{ $stats['overdue_tasks'] ?? 0 }}],
                    backgroundColor: ['#10b981', '#4f46e5', '#f59e0b', '#ef4444'],
                    borderWidth: 0,
                    cutout: '70%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: { 
                                size: 12,
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });
    }

    // Animate KPI values on load
    function animateValue(element, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const currentValue = Math.floor(progress * (end - start) + start);
            element.textContent = currentValue.toLocaleString();
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    // Animate KPI values
    document.querySelectorAll('.kpi-value').forEach(element => {
        const finalValue = parseInt(element.textContent.replace(/[^0-9]/g, ''));
        if (finalValue > 0) {
            element.textContent = '0';
            setTimeout(() => {
                animateValue(element, 0, finalValue, 2000);
            }, 500);
        }
    });

    // Animate progress bars
    document.querySelectorAll('.progress-bar').forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.transition = 'width 1.5s ease-out';
            bar.style.width = width;
        }, 800);
    });

    // Task checkboxes functionality
    document.querySelectorAll('.task-item input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const taskTitle = this.closest('.task-item').querySelector('.task-title');
            if (this.checked) {
                taskTitle.style.textDecoration = 'line-through';
                taskTitle.style.opacity = '0.6';
                this.closest('.task-item').style.background = 'rgba(16, 185, 129, 0.1)';
            } else {
                taskTitle.style.textDecoration = 'none';
                taskTitle.style.opacity = '1';
                this.closest('.task-item').style.background = '';
            }
        });
    });

    // Tooltip initialization for KPI cards
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Executive Action Functions
function generateReport() {
    // Show loading state
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Generating...';
    btn.disabled = true;
    
    // Simulate report generation
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        alert('Executive report generated successfully!');
    }, 2000);
}

function exportData() {
    // Show loading state
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-download me-2"></i>Exporting...';
    btn.disabled = true;
    
    // Simulate data export
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        // Create and download a sample CSV
        const csvContent = "data:text/csv;charset=utf-8,Type,Value,Date\n" +
            "Revenue,{{ $stats['monthly_revenue'] ?? 0 }},{{ now()->format('Y-m-d') }}\n" +
            "Projects,{{ $stats['total_projects'] ?? 0 }},{{ now()->format('Y-m-d') }}\n" +
            "Tasks,{{ $stats['pending_tasks'] ?? 0 }},{{ now()->format('Y-m-d') }}";
        
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "executive-dashboard-" + new Date().toISOString().split('T')[0] + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }, 1500);
}

// Real-time updates (placeholder for WebSocket integration)
function updateDashboardMetrics() {
    // This would integrate with your real-time data source
    console.log('Dashboard metrics updated');
}

// Set up periodic updates (every 5 minutes)
setInterval(updateDashboardMetrics, 300000);
</script>
@endpush