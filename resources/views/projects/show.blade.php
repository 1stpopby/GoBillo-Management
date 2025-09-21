@extends('layouts.app')

@section('title', $project->name)

@section('content')
<div class="project-show-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('sites.index') }}">Sites</a></li>
                        @if($project->site)
                            <li class="breadcrumb-item"><a href="{{ route('sites.show', $project->site) }}">{{ $project->site->name }}</a></li>
                        @endif
                        <li class="breadcrumb-item active">{{ $project->name }}</li>
                    </ol>
                </nav>
                <h1 class="page-title">{{ $project->name }}</h1>
                <p class="page-subtitle">{{ $project->description ?? 'No description available' }}</p>
            </div>
            <div class="col-lg-4 text-end">
                <div class="btn-group">
                    @if(auth()->user()->canManageProjects())
                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Project
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Report Cards - Now at top for all tabs -->
                            @if(!in_array(auth()->user()->role, ['site_manager', 'project_manager']))
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <div class="card border-0 bg-gradient-primary text-white">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="bi bi-currency-dollar fs-2 opacity-75"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <div class="fs-6 fw-bold">{{ auth()->user()->company->formatCurrency($financial_stats['project_budget']) }}</div>
                                                    <div class="small opacity-75">Project Budget</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="card border-0 bg-gradient-{{ $financial_stats['is_over_budget'] ? 'danger' : 'warning' }} text-white">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="bi bi-receipt fs-2 opacity-75"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <div class="fs-6 fw-bold">{{ auth()->user()->company->formatCurrency($financial_stats['actual_costs']) }}</div>
                                                    <div class="small opacity-75">Actual Costs</div>
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
                                                    <i class="bi bi-list-task fs-2 opacity-75"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <div class="fs-6 fw-bold">{{ $financial_stats['overall_progress'] }}%</div>
                                                    <div class="small opacity-75">Overall Progress</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="card border-0 bg-gradient-{{ $financial_stats['has_overdue_tasks'] ? 'danger' : 'success' }} text-white">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="bi bi-{{ $financial_stats['has_overdue_tasks'] ? 'exclamation-triangle' : 'check-circle' }} fs-2 opacity-75"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <div class="fs-6 fw-bold">{{ ucfirst(str_replace('_', ' ', $financial_stats['project_status'])) }}</div>
                                                    <div class="small opacity-75">
                                                        @if($financial_stats['has_overdue_tasks'])
                                                            Overdue Tasks!
                                                        @else
                                                            On Track
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif



    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Project Tabs -->
            <div class="card">
                <div class="card-header p-0">
                    <ul class="nav nav-tabs card-header-tabs" id="projectTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active d-flex align-items-center" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                                <i class="bi bi-speedometer2 me-2"></i>Overview
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center" id="tasks-tab" data-bs-toggle="tab" data-bs-target="#tasks" type="button" role="tab">
                                <i class="bi bi-list-task me-2"></i>Tasks
                                <span class="badge bg-primary ms-1 badge-sm">{{ $project->tasks->count() }}</span>
                            </button>
                        </li>
                        @if(!in_array(auth()->user()->role, ['site_manager', 'project_manager']))
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center" id="expenses-tab" data-bs-toggle="tab" data-bs-target="#expenses" type="button" role="tab">
                                <i class="bi bi-receipt me-2"></i>Expenses
                                <span class="badge bg-warning ms-1 badge-sm">{{ $project->projectExpenses->count() ?? 0 }}</span>
                            </button>
                        </li>
                        @endif
                        @if(!in_array(auth()->user()->role, ['site_manager', 'project_manager']))
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button" role="tab">
                                <i class="bi bi-graph-up-arrow me-2"></i>Financial
                                <span class="badge bg-success ms-1 badge-sm">{{ auth()->user()->company->formatCurrency($financial_stats['actual_costs'] ?? 0) }}</span>
                            </button>
                        </li>
                        @endif
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">
                                <i class="bi bi-file-earmark me-2"></i>Documents
                                <span class="badge bg-info ms-1 badge-sm">{{ $project->projectDocuments->count() ?? 0 }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center" id="variations-tab" data-bs-toggle="tab" data-bs-target="#variations" type="button" role="tab">
                                <i class="bi bi-arrow-left-right me-2"></i>Variations
                                <span class="badge bg-secondary ms-1 badge-sm">{{ $project->projectVariations->count() ?? 0 }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center" id="snagging-tab" data-bs-toggle="tab" data-bs-target="#snagging" type="button" role="tab">
                                <i class="bi bi-flag me-2"></i>Snagging
                                <span class="badge bg-danger ms-1 badge-sm">{{ $project->projectSnaggings->where('status', '!=', 'closed')->count() ?? 0 }}</span>
                            </button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body">
                    <div class="tab-content" id="projectTabsContent">
                        <!-- Overview Tab -->
                        <div class="tab-pane fade show active" id="overview" role="tabpanel">
                            <!-- Professional Project Details Card -->
                            <div class="project-details-card">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-gradient-primary text-white py-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-folder-fill fs-4 me-3"></i>
                                            <div>
                                                <h5 class="mb-0 fw-bold">Project Information</h5>
                                                <small class="opacity-75">Complete project details and specifications</small>
                                </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="row g-4">
                                            <!-- Left Column -->
                                            <div class="col-md-6">
                                                <!-- Client Information -->
                                                <div class="detail-group mb-4">
                                                    <div class="detail-item d-flex align-items-start">
                                                        <div class="detail-icon bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                                            <i class="bi bi-building"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <label class="text-muted small mb-1">Client Company</label>
                                            @php $client = $project->getEffectiveClient() @endphp
                                            @if($client)
                                                                <div class="fw-semibold">
                                                                    <a href="{{ route('clients.show', $client) }}" class="text-decoration-none text-dark hover-primary">
                                                    {{ $client->display_name }}
                                                                        <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                                                </a>
                                                                </div>
                                            @else
                                                                <div class="text-muted">Not assigned</div>
                                            @endif
                                                        </div>
                                        </div>
                                    </div>
                                    
                                                <!-- Site Location -->
                                                <div class="detail-group mb-4">
                                                    <div class="detail-item d-flex align-items-start">
                                                        <div class="detail-icon bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                                            <i class="bi bi-geo-alt-fill"></i>
                                        </div>
                                                        <div class="flex-grow-1">
                                                            <label class="text-muted small mb-1">Site Location</label>
                                            @if($project->site)
                                                                <div class="fw-semibold">
                                                                    <a href="{{ route('sites.show', $project->site) }}" class="text-decoration-none text-dark hover-primary">
                                                    {{ $project->site->name }}
                                                                        <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                                                </a>
                                                                </div>
                                            @else
                                                                <div class="text-muted">Not specified</div>
                                            @endif
                                                        </div>
                                        </div>
                                    </div>

                                                <!-- Project Manager -->
                                                <div class="detail-group mb-4">
                                                    <div class="detail-item d-flex align-items-start">
                                                        <div class="detail-icon bg-warning bg-opacity-10 text-warning rounded-circle p-2 me-3">
                                                            <i class="bi bi-person-badge-fill"></i>
                                        </div>
                                                        <div class="flex-grow-1">
                                                            <label class="text-muted small mb-1">Project Manager</label>
                                            @if($project->manager)
                                                                <div class="fw-semibold d-flex align-items-center">
                                                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 28px; height: 28px;">
                                                                        {{ substr($project->manager->name, 0, 1) }}
                                                                    </div>
                                                {{ $project->manager->name }}
                                                                </div>
                                            @else
                                                                <div class="text-muted">Not assigned</div>
                                            @endif
                                                        </div>
                                        </div>
                                    </div>

                                                <!-- Timeline -->
                                                <div class="detail-group">
                                                    <div class="detail-item d-flex align-items-start">
                                                        <div class="detail-icon bg-info bg-opacity-10 text-info rounded-circle p-2 me-3">
                                                            <i class="bi bi-calendar-range"></i>
                                        </div>
                                                        <div class="flex-grow-1">
                                                            <label class="text-muted small mb-1">Project Timeline</label>
                                                            <div class="timeline-info">
                                                                @if($project->start_date && $project->end_date)
                                                                    <div class="d-flex align-items-center mb-2">
                                                                        <span class="fw-semibold">
                                                                            <i class="bi bi-calendar-check me-2 text-success"></i>
                                                                            {{ $project->start_date->format('M j, Y') }}
                                                                        </span>
                                                                        <span class="mx-2 text-muted">→</span>
                                                                        <span class="fw-semibold">
                                                                            <i class="bi bi-calendar-x me-2 text-danger"></i>
                                                                            {{ $project->end_date->format('M j, Y') }}
                                            </span>
                                                                    </div>
                                                                    <div class="text-muted small">
                                                                        <i class="bi bi-clock me-1"></i>
                                                                        Duration: {{ $project->start_date->diffInDays($project->end_date) }} days
                                                                        @if($project->end_date->isPast())
                                                                            <span class="badge bg-danger ms-2">Overdue</span>
                                                                        @elseif($project->end_date->diffInDays(now()) <= 7)
                                                                            <span class="badge bg-warning ms-2">Due Soon</span>
                                                                        @endif
                                                                    </div>
                                                                @else
                                                                    <div class="text-muted">Timeline not set</div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                        </div>
                                    </div>

                                            <!-- Right Column -->
                                            <div class="col-md-6">
                                                <!-- Status & Priority -->
                                                <div class="detail-group mb-4">
                                                    <div class="row g-3">
                                                        <div class="col-6">
                                                            <div class="status-card p-3 rounded-3 bg-light">
                                                                <label class="text-muted small mb-2 d-block">Status</label>
                                                                <span class="badge rounded-pill bg-{{ $project->status_color }} px-3 py-2">
                                                                    @php
                                                                        $statusIcons = [
                                                                            'planning' => 'bi-pencil-square',
                                                                            'in_progress' => 'bi-play-circle',
                                                                            'on_hold' => 'bi-pause-circle',
                                                                            'completed' => 'bi-check-circle',
                                                                            'cancelled' => 'bi-x-circle'
                                                                        ];
                                                                        $icon = $statusIcons[$project->status] ?? 'bi-circle';
                                                                    @endphp
                                                                    <i class="bi {{ $icon }} me-1"></i>
                                                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                                                </span>
                                        </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="priority-card p-3 rounded-3 bg-light">
                                                                <label class="text-muted small mb-2 d-block">Priority</label>
                                                                <span class="badge rounded-pill bg-{{ $project->priority_color }} px-3 py-2">
                                                                    @php
                                                                        $priorityIcons = [
                                                                            'low' => 'bi-arrow-down',
                                                                            'medium' => 'bi-arrow-right',
                                                                            'high' => 'bi-arrow-up',
                                                                            'urgent' => 'bi-exclamation-triangle'
                                                                        ];
                                                                        $pIcon = $priorityIcons[$project->priority] ?? 'bi-dash';
                                                                    @endphp
                                                                    <i class="bi {{ $pIcon }} me-1"></i>
                                                {{ ucfirst($project->priority) }}
                                            </span>
                                        </div>
                                    </div>
                                            </div>
                                        </div>

                                                <!-- Progress -->
                                                <div class="detail-group mb-4">
                                                    <div class="progress-section p-3 rounded-3 bg-light">
                                                        <label class="text-muted small mb-2 d-block">Overall Progress</label>
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                            <span class="fw-bold fs-3 text-primary">{{ $project->progress }}%</span>
                                                            <span class="text-muted small">
                                                                @if($project->progress == 100)
                                                                    <i class="bi bi-check-circle-fill text-success me-1"></i>Complete
                                                                @elseif($project->progress >= 75)
                                                                    <i class="bi bi-hourglass-split text-info me-1"></i>Almost Done
                                                                @elseif($project->progress >= 50)
                                                                    <i class="bi bi-hourglass-bottom text-warning me-1"></i>Halfway
                                                                @else
                                                                    <i class="bi bi-hourglass-top text-secondary me-1"></i>In Progress
                                    @endif
                                                            </span>
                                                        </div>
                                                        <div class="progress" style="height: 12px;">
                                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-{{ $project->progress >= 75 ? 'success' : ($project->progress >= 50 ? 'info' : 'warning') }}" 
                                                                 style="width: {{ $project->progress }}%"
                                                                 role="progressbar"
                                                                 aria-valuenow="{{ $project->progress }}"
                                                                 aria-valuemin="0"
                                                                 aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Description -->
                                                @if($project->description)
                                                    <div class="detail-group">
                                                        <div class="description-section p-3 rounded-3 bg-light">
                                                            <label class="text-muted small mb-2 d-block">
                                                                <i class="bi bi-text-paragraph me-1"></i>Description
                                                            </label>
                                                            <p class="mb-0 text-dark">{{ $project->description }}</p>
                                            </div>
                                        </div>
                                    @endif
                                            </div>
                                        </div>

                                        <!-- Additional Information -->
                                        @if($project->address || $project->city || $project->state || $project->zip_code)
                                            <hr class="my-4">
                                            <div class="location-section">
                                                <h6 class="text-muted mb-3">
                                                    <i class="bi bi-map me-2"></i>Location Details
                                                </h6>
                                                <div class="bg-light rounded-3 p-3">
                                                    <address class="mb-0">
                                                        @if($project->address)
                                                            <div>{{ $project->address }}</div>
                                                        @endif
                                                        @if($project->city || $project->state || $project->zip_code)
                                                            <div>
                                                                {{ $project->city }}{{ $project->city && $project->state ? ', ' : '' }}
                                                                {{ $project->state }} {{ $project->zip_code }}
                                        </div>
                                                        @endif
                                                    </address>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tasks Tab -->
                        <div class="tab-pane fade" id="tasks" role="tabpanel">
                            <!-- Tasks Header -->
                            <div class="tasks-header mb-4">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h4 class="mb-0 fw-bold text-dark">
                                            <i class="bi bi-list-task me-2 text-primary"></i>Project Tasks
                                        </h4>
                                        <p class="text-muted mb-0 mt-1">Manage and track all project tasks</p>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                @if(auth()->user()->canManageTasks() || in_array(auth()->user()->role, ['site_manager', 'project_manager']))
                                            <a href="{{ route('tasks.create', ['project' => $project->id]) }}" class="btn btn-primary shadow-sm">
                                                <i class="bi bi-plus-circle me-2"></i>Add New Task
                                    </a>
                                @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Task Statistics Cards -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <div class="task-stat-card bg-light border-0 rounded-3 p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="task-stat-icon bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                                <i class="bi bi-list-ul fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-muted small">Total Tasks</h6>
                                                <h4 class="mb-0 fw-bold">{{ $project->tasks->count() }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="task-stat-card bg-light border-0 rounded-3 p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="task-stat-icon bg-warning bg-opacity-10 text-warning rounded-circle p-2 me-3">
                                                <i class="bi bi-clock-history fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-muted small">In Progress</h6>
                                                <h4 class="mb-0 fw-bold">{{ $project->tasks->where('status', 'in_progress')->count() }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="task-stat-card bg-light border-0 rounded-3 p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="task-stat-icon bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                                <i class="bi bi-check-circle fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-muted small">Completed</h6>
                                                <h4 class="mb-0 fw-bold">{{ $project->tasks->where('status', 'completed')->count() }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="task-stat-card bg-light border-0 rounded-3 p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="task-stat-icon bg-danger bg-opacity-10 text-danger rounded-circle p-2 me-3">
                                                <i class="bi bi-exclamation-triangle fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-muted small">Overdue</h6>
                                                <h4 class="mb-0 fw-bold">{{ $project->tasks->where('due_date', '<', now())->where('status', '!=', 'completed')->count() }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Advanced Task Filters -->
                            <div class="task-filters-section bg-white border rounded-3 p-3 mb-4 shadow-sm">
                                <div class="row g-2 align-items-center">
                                <div class="col-md-2">
                                        <label class="form-label small text-muted mb-1">Status</label>
                                        <select class="form-select form-select-sm border-secondary" id="projectTaskStatusFilter">
                                        <option value="">All Statuses</option>
                                            <option value="pending">🔵 Pending</option>
                                            <option value="in_progress">🟡 In Progress</option>
                                            <option value="review">🟣 Review</option>
                                            <option value="completed">🟢 Completed</option>
                                            <option value="cancelled">🔴 Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                        <label class="form-label small text-muted mb-1">Priority</label>
                                        <select class="form-select form-select-sm border-secondary" id="projectTaskPriorityFilter">
                                        <option value="">All Priorities</option>
                                            <option value="low">🟢 Low</option>
                                            <option value="medium">🟡 Medium</option>
                                            <option value="high">🟠 High</option>
                                            <option value="urgent">🔴 Urgent</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                        <label class="form-label small text-muted mb-1">Category</label>
                                        <select class="form-select form-select-sm border-secondary" id="projectTaskCategoryFilter">
                                        <option value="">All Categories</option>
                                        @foreach($project->tasks->pluck('taskCategory')->filter()->unique('id') as $category)
                                            <option value="{{ $category->name }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted mb-1">Search</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white border-secondary">
                                                <i class="bi bi-search"></i>
                                            </span>
                                            <input type="text" class="form-control border-secondary" id="projectTaskSearchFilter" placeholder="Search by title or description...">
                                </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small text-muted mb-1">&nbsp;</label>
                                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" id="clearProjectTaskFilters">
                                            <i class="bi bi-x-circle me-1"></i>Clear All
                                    </button>
                                    </div>
                                </div>
                            </div>

                            @if($project->tasks->count() > 0)
                                <!-- Professional Tasks Table -->
                                <div class="tasks-table-container bg-white rounded-3 shadow-sm">
                                <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0 tasks-table">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="border-0 ps-4" style="width: 35%;">
                                                        <i class="bi bi-card-text me-2 text-muted"></i>Task Details
                                                    </th>
                                                    <th class="border-0" style="width: 12%;">
                                                        <i class="bi bi-tag me-2 text-muted"></i>Category
                                                    </th>
                                                    <th class="border-0" style="width: 15%;">
                                                        <i class="bi bi-person me-2 text-muted"></i>Assigned
                                                    </th>
                                                    <th class="border-0" style="width: 12%;">
                                                        <i class="bi bi-flag me-2 text-muted"></i>Status
                                                    </th>
                                                    <th class="border-0" style="width: 10%;">
                                                        <i class="bi bi-exclamation-circle me-2 text-muted"></i>Priority
                                                    </th>
                                                    <th class="border-0" style="width: 12%;">
                                                        <i class="bi bi-calendar3 me-2 text-muted"></i>Due Date
                                                    </th>
                                                    <th class="border-0 text-center" style="width: 10%;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($project->tasks as $task)
                                                    <tr class="task-row {{ $task->status == 'completed' ? 'task-completed' : '' }} {{ $task->is_delayed_or_on_hold ? 'task-delayed-or-on-hold' : '' }}" data-task-id="{{ $task->id }}">
                                                        <td class="ps-4">
                                                            <div class="task-info">
                                                                <a href="javascript:void(0)" onclick="openTaskModal({{ $task->id }})" class="task-title text-decoration-none fw-semibold text-dark">
                                                                    {{ $task->title }}
                                                                    @if($task->is_currently_on_hold)
                                                                        <span class="badge bg-danger ms-2">On Hold</span>
                                                                    @elseif($task->is_currently_delayed)
                                                                        <span class="badge bg-warning ms-2">Delayed</span>
                                                                    @elseif($task->due_date && $task->due_date->isPast() && $task->status !== 'completed')
                                                                        <span class="badge bg-danger ms-2">Overdue</span>
                                                                    @endif
                                                        </a>
                                                        @if($task->description)
                                                                    <p class="text-muted small mb-0 mt-1">{{ Str::limit($task->description, 60) }}</p>
                                                        @endif
                                                                <div class="task-meta mt-1">
                                                                    <span class="text-muted small">
                                                                        <i class="bi bi-clock me-1"></i>Created {{ $task->created_at->diffForHumans() }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                    </td>
                                                    <td>
                                                        @if($task->taskCategory)
                                                                <span class="badge rounded-pill bg-secondary">
                                                                    {{ $task->taskCategory->name }}
                                                                </span>
                                                        @else
                                                                <span class="text-muted small">—</span>
                                                        @endif
                                                    </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                @if($task->assignedUser)
                                                                    <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                                        {{ substr($task->assignedUser->name, 0, 1) }}
                                                                    </div>
                                                                    <span class="small">{{ $task->assignedUser->name }}</span>
                                                                @else
                                                                    <span class="text-muted small">Unassigned</span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    <td>
                                                        @if(auth()->user()->canManageTasks())
                                                                <select class="form-select form-select-sm status-select rounded-pill" 
                                                                    data-task-id="{{ $task->id }}" 
                                                                        data-current-status="{{ $task->status }}">
                                                                    <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>🔵 Pending</option>
                                                                    <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>🟡 Progress</option>
                                                                    <option value="review" {{ $task->status == 'review' ? 'selected' : '' }}>🟣 Review</option>
                                                                    <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>🟢 Done</option>
                                                                    <option value="cancelled" {{ $task->status == 'cancelled' ? 'selected' : '' }}>🔴 Cancelled</option>
                                                            </select>
                                                        @else
                                                                @php
                                                                    $statusColors = [
                                                                        'pending' => 'primary',
                                                                        'in_progress' => 'warning',
                                                                        'review' => 'info',
                                                                        'completed' => 'success',
                                                                        'cancelled' => 'danger'
                                                                    ];
                                                                @endphp
                                                                <span class="badge rounded-pill bg-{{ $statusColors[$task->status] ?? 'secondary' }}">
                                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                        </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                            @php
                                                                $priorityConfig = [
                                                                    'low' => ['color' => 'success', 'icon' => '🟢'],
                                                                    'medium' => ['color' => 'warning', 'icon' => '🟡'],
                                                                    'high' => ['color' => 'orange', 'icon' => '🟠'],
                                                                    'urgent' => ['color' => 'danger', 'icon' => '🔴']
                                                                ];
                                                                $priority = $priorityConfig[$task->priority] ?? ['color' => 'secondary', 'icon' => '⚪'];
                                                            @endphp
                                                            <span class="badge rounded-pill bg-{{ $priority['color'] }} bg-opacity-10 text-{{ $priority['color'] }} border border-{{ $priority['color'] }}">
                                                                {{ $priority['icon'] }} {{ ucfirst($task->priority) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($task->due_date)
                                                                <div class="due-date-info">
                                                                    <span class="{{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-danger fw-semibold' : 'text-dark' }}">
                                                                        <i class="bi bi-calendar-event me-1"></i>
                                                                {{ $task->due_date->format('M j, Y') }}
                                                            </span>
                                                                    @if($task->due_date->isToday())
                                                                        <span class="badge bg-warning ms-1">Today</span>
                                                                    @elseif($task->due_date->isTomorrow())
                                                                        <span class="badge bg-info ms-1">Tomorrow</span>
                                                                    @endif
                                                                </div>
                                                        @else
                                                                <span class="text-muted small">—</span>
                                                        @endif
                                                    </td>
                                                        <td class="text-center">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    <i class="bi bi-three-dots-vertical"></i>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                                                    <li>
                                                                        <a class="dropdown-item" href="javascript:void(0)" onclick="openTaskModal({{ $task->id }})">
                                                                            <i class="bi bi-eye me-2 text-primary"></i>View Details
                                                                        </a>
                                                                    </li>
                                                            @if(auth()->user()->canManageTasks())
                                                                        <li>
                                                                            <a class="dropdown-item" href="{{ route('tasks.edit', ['task' => $task, 'from_project' => 1]) }}">
                                                                                <i class="bi bi-pencil me-2 text-warning"></i>Edit Task
                                                                            </a>
                                                                        </li>
                                                                        <li><hr class="dropdown-divider"></li>
                                                                        <li>
                                                                            <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="confirmDeleteTask({{ $task->id }}, '{{ addslashes($task->title) }}')">
                                                                                <i class="bi bi-trash me-2"></i>Delete Task
                                                                            </a>
                                                                        </li>
                                                            @endif
                                                                </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            @else
                                <div class="empty-state text-center py-4">
                                    <i class="bi bi-list-task display-4 text-muted"></i>
                                    <h6 class="mt-3">No tasks yet</h6>
                                    <p class="text-muted">Create tasks to track project progress</p>
                                    @if(auth()->user()->canManageTasks())
                                        <a href="{{ route('tasks.create', ['project' => $project->id]) }}" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-2"></i>Create First Task
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Expenses Tab -->
                        @if(!in_array(auth()->user()->role, ['site_manager', 'project_manager']))
                        <div class="tab-pane fade" id="expenses" role="tabpanel">
                            <!-- Professional Expenses Header -->
                            <div class="expenses-header mb-4">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="header-icon bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                                                <i class="bi bi-cash-stack fs-4"></i>
                                            </div>
                                            <div>
                                                <h4 class="mb-1 fw-bold">Expense Management</h4>
                                                <p class="text-muted mb-0">Track and manage all project-related expenses</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                @if(auth()->user()->canManageProjects())
                                            <button class="btn btn-success btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                                                <i class="bi bi-plus-circle me-2"></i>Record New Expense
                                    </button>
                                @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Expense Statistics Cards -->
                            @php
                                $totalExpenses = $project->projectExpenses->sum('amount');
                                $approvedExpenses = $project->projectExpenses->where('status', 'approved')->sum('amount');
                                $pendingExpenses = $project->projectExpenses->where('status', 'pending')->sum('amount');
                                $rejectedExpenses = $project->projectExpenses->where('status', 'rejected')->sum('amount');
                                $expenseCount = $project->projectExpenses->count();
                            @endphp
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <div class="expense-stat-card card border-0 shadow-sm h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                                    <i class="bi bi-wallet2"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted small mb-1">Total Expenses</p>
                                                    <h4 class="mb-0 fw-bold">{{ auth()->user()->company->formatCurrency($totalExpenses, 2) }}</h4>
                                                    <small class="text-muted">{{ $expenseCount }} {{ Str::plural('expense', $expenseCount) }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="expense-stat-card card border-0 shadow-sm h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="stat-icon bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                                    <i class="bi bi-check-circle"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted small mb-1">Approved</p>
                                                    <h4 class="mb-0 fw-bold text-success">{{ auth()->user()->company->formatCurrency($approvedExpenses, 2) }}</h4>
                                                    <small class="text-muted">{{ $totalExpenses > 0 ? round(($approvedExpenses / $totalExpenses) * 100) : 0 }}% of total</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="expense-stat-card card border-0 shadow-sm h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-circle p-2 me-3">
                                                    <i class="bi bi-clock-history"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted small mb-1">Pending</p>
                                                    <h4 class="mb-0 fw-bold text-warning">{{ auth()->user()->company->formatCurrency($pendingExpenses, 2) }}</h4>
                                                    <small class="text-muted">Awaiting approval</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="expense-stat-card card border-0 shadow-sm h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="stat-icon bg-info bg-opacity-10 text-info rounded-circle p-2 me-3">
                                                    <i class="bi bi-calculator"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="text-muted small mb-1">Average Expense</p>
                                                    <h4 class="mb-0 fw-bold text-info">{{ $expenseCount > 0 ? auth()->user()->company->formatCurrency($totalExpenses / $expenseCount, 2) : auth()->user()->company->formatCurrency(0, 2) }}</h4>
                                                    <small class="text-muted">Per transaction</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Expense Filters -->
                            <div class="expense-filters card border-0 shadow-sm mb-4">
                                <div class="card-body">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label small text-muted">
                                                <i class="bi bi-funnel me-1"></i>Filter by Status
                                            </label>
                                            <select class="form-select expense-filter-select" id="expenseStatusFilter">
                                                <option value="">All Statuses</option>
                                                <option value="pending">⏳ Pending</option>
                                                <option value="approved">✅ Approved</option>
                                                <option value="rejected">❌ Rejected</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small text-muted">
                                                <i class="bi bi-tag me-1"></i>Category
                                            </label>
                                            <select class="form-select expense-filter-select" id="expenseCategoryFilter">
                                                <option value="">All Categories</option>
                                                <option value="materials">🔨 Materials</option>
                                                <option value="labor">👷 Labor</option>
                                                <option value="equipment">⚙️ Equipment</option>
                                                <option value="travel">✈️ Travel</option>
                                                <option value="other">📦 Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small text-muted">
                                                <i class="bi bi-calendar-range me-1"></i>Date Range
                                            </label>
                                            <select class="form-select expense-filter-select" id="expenseDateFilter">
                                                <option value="">All Time</option>
                                                <option value="today">Today</option>
                                                <option value="week">This Week</option>
                                                <option value="month">This Month</option>
                                                <option value="quarter">This Quarter</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="d-flex gap-2">
                                                <input type="text" class="form-control" id="expenseSearch" placeholder="🔍 Search expenses...">
                                                <button class="btn btn-outline-secondary" onclick="clearExpenseFilters()">
                                                    <i class="bi bi-x-lg"></i> Clear
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Expenses Table -->
                            <div id="expensesContainer">
                                @if($project->projectExpenses->count() > 0)
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body p-0">
                                    <div class="table-responsive">
                                                <table class="table table-hover expense-table mb-0">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th class="border-0 ps-4">
                                                                <i class="bi bi-receipt me-2"></i>Expense Details
                                                            </th>
                                                            <th class="border-0">
                                                                <i class="bi bi-tag me-2"></i>Category
                                                            </th>
                                                            <th class="border-0">
                                                                <i class="bi bi-cash me-2"></i>Amount
                                                            </th>
                                                            <th class="border-0">
                                                                <i class="bi bi-calendar3 me-2"></i>Date
                                                            </th>
                                                            <th class="border-0">
                                                                <i class="bi bi-flag me-2"></i>Status
                                                            </th>
                                                            <th class="border-0">
                                                                <i class="bi bi-shop me-2"></i>Vendor
                                                            </th>
                                                            <th class="border-0 text-center">
                                                                <i class="bi bi-gear me-2"></i>Actions
                                                            </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($project->projectExpenses as $expense)
                                                            <tr class="expense-row" data-status="{{ $expense->status }}" data-category="{{ $expense->category }}">
                                                                <td class="ps-4">
                                                                    <div class="expense-info">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="expense-icon-wrapper bg-{{ $expense->status === 'approved' ? 'success' : ($expense->status === 'pending' ? 'warning' : 'danger') }} bg-opacity-10 rounded-circle p-2 me-3">
                                                                                <i class="{{ $expense->category_icon }} text-{{ $expense->status === 'approved' ? 'success' : ($expense->status === 'pending' ? 'warning' : 'danger') }}"></i>
                                                                            </div>
                                                                            <div>
                                                                                <h6 class="mb-0 fw-semibold">{{ ucfirst(str_replace('_', ' ', $expense->category)) }} Expense</h6>
                                                                                <small class="text-muted">
                                                                                    Net: £{{ number_format($expense->net_amount, 2) }} + VAT: £{{ number_format($expense->vat_amount, 2) }} ({{ number_format($expense->vat_rate, 1) }}%)
                                                                                </small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                        </td>
                                                        <td>
                                                                    <span class="category-badge badge bg-light text-dark">
                                                            <i class="{{ $expense->category_icon }} me-1"></i>
                                                            {{ ucfirst($expense->category) }}
                                                                    </span>
                                                        </td>
                                                                <td>
                                                                    <div class="amount-display">
                                                                        <span class="fw-bold fs-5 text-{{ $expense->amount > 1000 ? 'danger' : 'dark' }}">
                                                                            {{ $expense->formatted_amount }}
                                                                        </span>
                                                                        @if($expense->amount > 1000)
                                                                            <br><small class="text-danger"><i class="bi bi-exclamation-triangle"></i> High value</small>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="date-info">
                                                                        <div class="fw-semibold">{{ $expense->expense_date->format('M j, Y') }}</div>
                                                                        <small class="text-muted">{{ $expense->expense_date->diffForHumans() }}</small>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span class="badge rounded-pill bg-{{ $expense->status_color }} px-3 py-2">
                                                                        @if($expense->status === 'approved')
                                                                            <i class="bi bi-check-circle me-1"></i>
                                                                        @elseif($expense->status === 'pending')
                                                                            <i class="bi bi-clock me-1"></i>
                                                                        @else
                                                                            <i class="bi bi-x-circle me-1"></i>
                                                                        @endif
                                                                {{ ucfirst($expense->status) }}
                                                            </span>
                                                        </td>
                                                                <td>
                                                                    @if($expense->vendor_name)
                                                                        <div class="vendor-info">
                                                                            <i class="bi bi-building me-1 text-muted"></i>
                                                                            {{ $expense->vendor_name }}
                                                                        </div>
                                                                    @else
                                                                        <span class="text-muted">—</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div class="dropdown">
                                                                        <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown">
                                                                            <i class="bi bi-three-dots-vertical"></i>
                                                                </button>
                                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                                            <li>
                                                                                <button class="dropdown-item" onclick="viewExpense({{ $expense->id }})">
                                                                                    <i class="bi bi-eye me-2"></i>View Details
                                                                                </button>
                                                                            </li>
                                                                @if(auth()->user()->canManageProjects())
                                                                                <li>
                                                                                    <button class="dropdown-item" onclick="editExpense({{ $expense->id }})">
                                                                                        <i class="bi bi-pencil me-2"></i>Edit Expense
                                                                    </button>
                                                                                </li>
                                                                    @if($expense->status === 'pending')
                                                                                    <li><hr class="dropdown-divider"></li>
                                                                                    <li>
                                                                                        <button class="dropdown-item text-success" onclick="approveExpense({{ $expense->id }})">
                                                                                            <i class="bi bi-check-circle me-2"></i>Approve
                                                                        </button>
                                                                                    </li>
                                                                                    <li>
                                                                                        <button class="dropdown-item text-danger" onclick="rejectExpense({{ $expense->id }})">
                                                                                            <i class="bi bi-x-circle me-2"></i>Reject
                                                                                        </button>
                                                                                    </li>
                                                                    @endif
                                                                @endif
                                                                        </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body py-5">
                                            <div class="empty-state text-center">
                                                <div class="empty-icon-wrapper mx-auto mb-4" style="width: 120px; height: 120px;">
                                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center h-100">
                                                        <i class="bi bi-receipt-cutoff display-1 text-muted"></i>
                                                    </div>
                                                </div>
                                                <h5 class="fw-bold mb-2">No Expenses Recorded</h5>
                                                <p class="text-muted mb-4">Start tracking project expenses to monitor your budget effectively</p>
                                        @if(auth()->user()->canManageProjects())
                                                    <button class="btn btn-success btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                                                        <i class="bi bi-plus-circle me-2"></i>Record First Expense
                                            </button>
                                        @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Financial Tab -->
                        @if(!in_array(auth()->user()->role, ['site_manager', 'project_manager']))
                        <div class="tab-pane fade" id="financial" role="tabpanel">
                            <!-- Professional Financial Header -->
                            <div class="financial-header mb-4">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-center">
                                            <div class="header-icon bg-gradient-success text-white rounded-circle p-3 me-3">
                                                <i class="bi bi-graph-up-arrow fs-4"></i>
                                            </div>
                                            <div>
                                                <h4 class="mb-1 fw-bold">Financial Overview</h4>
                                                <p class="text-muted mb-0">Complete financial breakdown including materials, tools, and labor costs</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <div class="financial-summary-badge">
                                            <div class="bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                                                <i class="bi bi-currency-pound me-1"></i>
                                                <strong>{{ number_format($financial_stats['actual_costs'] ?? 0, 2) }}</strong>
                                                <small class="ms-1">Total Cost</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Summary Cards -->
                            @php
                                // Calculate financial data
                                $projectExpenses = $project->projectExpenses ?? collect();
                                $operativeInvoices = \App\Models\OperativeInvoice::where('project_id', $project->id)->get();
                                $toolHires = \App\Models\ToolHireRequest::where('project_id', $project->id)->get();
                                
                                $materialsCost = $projectExpenses->where('category', 'materials')->sum('amount');
                                $toolsCost = $toolHires->sum('actual_total_cost') ?: $toolHires->sum('estimated_total_cost');
                                $laborCost = $operativeInvoices->sum('net_amount');
                                $otherCosts = $projectExpenses->whereNotIn('category', ['materials'])->sum('amount');
                                $totalProjectCost = $materialsCost + $toolsCost + $laborCost + $otherCosts;
                                
                                $projectBudget = $project->budget ?? 0;
                                $remainingBudget = $projectBudget - $totalProjectCost;
                                $budgetUsedPercent = $projectBudget > 0 ? ($totalProjectCost / $projectBudget) * 100 : 0;
                            @endphp

                            <div class="row g-4 mb-4">
                                <div class="col-lg-3 col-md-6">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <div class="financial-icon bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                                <i class="bi bi-boxes text-primary fs-4"></i>
                                            </div>
                                            <h3 class="h4 mb-2">{{ auth()->user()->company->formatCurrency($materialsCost, 2) }}</h3>
                                            <p class="text-muted mb-0">Materials & Supplies</p>
                                            <small class="text-muted">{{ $projectExpenses->where('category', 'materials')->count() }} transactions</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-3 col-md-6">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <div class="financial-icon bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                                <i class="bi bi-tools text-warning fs-4"></i>
                                            </div>
                                            <h3 class="h4 mb-2">{{ auth()->user()->company->formatCurrency($toolsCost, 2) }}</h3>
                                            <p class="text-muted mb-0">Tool Hire</p>
                                            <small class="text-muted">{{ $toolHires->count() }} hire requests</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-3 col-md-6">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <div class="financial-icon bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                                <i class="bi bi-people text-success fs-4"></i>
                                            </div>
                                            <h3 class="h4 mb-2">{{ auth()->user()->company->formatCurrency($laborCost, 2) }}</h3>
                                            <p class="text-muted mb-0">Labor Costs</p>
                                            <small class="text-muted">{{ $operativeInvoices->count() }} operative invoices</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-3 col-md-6">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <div class="financial-icon bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                                <i class="bi bi-receipt text-info fs-4"></i>
                                            </div>
                                            <h3 class="h4 mb-2">{{ auth()->user()->company->formatCurrency($otherCosts, 2) }}</h3>
                                            <p class="text-muted mb-0">Other Expenses</p>
                                            <small class="text-muted">{{ $projectExpenses->whereNotIn('category', ['materials'])->count() }} items</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Budget Overview -->
                            @if($projectBudget > 0)
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white border-bottom">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-pie-chart me-2"></i>Budget Analysis
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-muted">Budget Utilization</span>
                                                <span class="fw-bold">{{ number_format($budgetUsedPercent, 1) }}%</span>
                                            </div>
                                            <div class="progress mb-3" style="height: 10px;">
                                                <div class="progress-bar {{ $budgetUsedPercent > 90 ? 'bg-danger' : ($budgetUsedPercent > 75 ? 'bg-warning' : 'bg-success') }}" 
                                                     role="progressbar" style="width: {{ min($budgetUsedPercent, 100) }}%"></div>
                                            </div>
                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <div class="text-muted small">Total Budget</div>
                                                    <div class="fw-bold">{{ auth()->user()->company->formatCurrency($projectBudget, 2) }}</div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="text-muted small">Spent</div>
                                                    <div class="fw-bold text-{{ $budgetUsedPercent > 90 ? 'danger' : 'primary' }}">{{ auth()->user()->company->formatCurrency($totalProjectCost, 2) }}</div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="text-muted small">Remaining</div>
                                                    <div class="fw-bold text-{{ $remainingBudget < 0 ? 'danger' : 'success' }}">{{ auth()->user()->company->formatCurrency($remainingBudget, 2) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if($budgetUsedPercent > 90)
                                                <div class="alert alert-danger mb-0">
                                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                                    <strong>Budget Alert!</strong><br>
                                                    <small>Over 90% of budget used</small>
                                                </div>
                                            @elseif($budgetUsedPercent > 75)
                                                <div class="alert alert-warning mb-0">
                                                    <i class="bi bi-info-circle me-2"></i>
                                                    <strong>Budget Warning</strong><br>
                                                    <small>Over 75% of budget used</small>
                                                </div>
                                            @else
                                                <div class="alert alert-success mb-0">
                                                    <i class="bi bi-check-circle me-2"></i>
                                                    <strong>On Track</strong><br>
                                                    <small>Budget well managed</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Detailed Financial Breakdown -->
                            <div class="row g-4">
                                <!-- Materials & Supplies -->
                                <div class="col-lg-6">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-header bg-white border-bottom">
                                            <h5 class="card-title mb-0">
                                                <i class="bi bi-boxes me-2"></i>Materials & Supplies
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            @if($projectExpenses->where('category', 'materials')->isNotEmpty())
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Date</th>
                                                                <th>Description</th>
                                                                <th class="text-end">Amount</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($projectExpenses->where('category', 'materials')->take(5) as $expense)
                                                                <tr>
                                                                    <td>{{ $expense->expense_date ? $expense->expense_date->format('M d') : 'N/A' }}</td>
                                                                    <td>{{ Str::limit($expense->description ?? 'Material purchase', 30) }}</td>
                                                                    <td class="text-end fw-bold">£{{ number_format($expense->amount ?? 0, 2) }}</td>
                                                                    <td>
                                                                        <span class="badge bg-{{ $expense->status === 'approved' ? 'success' : ($expense->status === 'pending' ? 'warning' : 'danger') }}">
                                                                            {{ ucfirst($expense->status) }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                @if($projectExpenses->where('category', 'materials')->count() > 5)
                                                    <div class="text-center mt-3">
                                                        <button class="btn btn-sm btn-outline-primary" onclick="document.getElementById('expenses-tab').click()">
                                                            View All Materials ({{ $projectExpenses->where('category', 'materials')->count() }})
                                                        </button>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="text-center py-4">
                                                    <i class="bi bi-boxes text-muted" style="font-size: 3rem;"></i>
                                                    <p class="text-muted mt-2">No material expenses recorded</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Tool Hire -->
                                <div class="col-lg-6">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-header bg-white border-bottom">
                                            <h5 class="card-title mb-0">
                                                <i class="bi bi-tools me-2"></i>Tool Hire
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            @if($toolHires->isNotEmpty())
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Tool/Equipment</th>
                                                                <th>Duration</th>
                                                                <th class="text-end">Cost</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($toolHires->take(5) as $hire)
                                                                <tr>
                                                                    <td>{{ Str::limit($hire->tool_name ?? $hire->description ?? 'Tool hire', 25) }}</td>
                                                                    <td>
                                                                        <small class="text-muted">
                                                                            @if($hire->hire_start_date && $hire->hire_end_date)
                                                                                {{ $hire->hire_start_date->format('M d') }} - {{ $hire->hire_end_date->format('M d') }}
                                                                            @else
                                                                                Date TBD
                                                                            @endif
                                                                        </small>
                                                                    </td>
                                                                    <td class="text-end fw-bold">£{{ number_format($hire->actual_total_cost ?? $hire->estimated_total_cost ?? 0, 2) }}</td>
                                                                    <td>
                                                                        <span class="badge bg-{{ $hire->status === 'approved' ? 'success' : ($hire->status === 'pending' ? 'warning' : 'danger') }}">
                                                                            {{ ucfirst($hire->status) }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                @if($toolHires->count() > 5)
                                                    <div class="text-center mt-3">
                                                        <a href="{{ route('tool-hire.index') }}" class="btn btn-sm btn-outline-primary">
                                                            View All Tool Hires ({{ $toolHires->count() }})
                                                        </a>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="text-center py-4">
                                                    <i class="bi bi-tools text-muted" style="font-size: 3rem;"></i>
                                                    <p class="text-muted mt-2">No tool hires recorded</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Operative Labor Costs -->
                                <div class="col-lg-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-white border-bottom">
                                            <h5 class="card-title mb-0">
                                                <i class="bi bi-people me-2"></i>Labor Costs & Operative Invoices
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            @if($operativeInvoices->isNotEmpty())
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Invoice #</th>
                                                                <th>Operative</th>
                                                                <th>Week Period</th>
                                                                <th class="text-center">Hours</th>
                                                                <th class="text-end">Gross Amount</th>
                                                                <th class="text-end">CIS Deduction</th>
                                                                <th class="text-end">Net Amount</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($operativeInvoices as $invoice)
                                                                <tr>
                                                                    <td>
                                                                        <a href="{{ route('invoices.operative.show', $invoice->id) }}" class="text-decoration-none">
                                                                            <strong>{{ $invoice->invoice_number }}</strong>
                                                                        </a>
                                                                    </td>
                                                                    <td>
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                                                <i class="bi bi-person text-primary"></i>
                                                                            </div>
                                                                            <div>
                                                                                <div class="fw-medium">{{ $invoice->operative->name }}</div>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <small class="text-muted">
                                                                            @if($invoice->week_starting && $invoice->week_ending)
                                                                                {{ $invoice->week_starting->format('M d') }} - {{ $invoice->week_ending->format('M d, Y') }}
                                                                            @else
                                                                                Date TBD
                                                                            @endif
                                                                        </small>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span class="badge bg-info">{{ $invoice->total_hours }}h</span>
                                                                    </td>
                                                                    <td class="text-end">£{{ number_format($invoice->gross_amount ?? 0, 2) }}</td>
                                                                    <td class="text-end text-warning">
                                                                        @if($invoice->cis_applicable)
                                                                            £{{ number_format($invoice->cis_deduction ?? 0, 2) }}
                                                                        @else
                                                                            <span class="text-muted">N/A</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-end fw-bold text-success">£{{ number_format($invoice->net_amount ?? 0, 2) }}</td>
                                                                    <td>
                                                                        <span class="badge bg-{{ $invoice->status === 'approved' ? 'success' : ($invoice->status === 'submitted' ? 'warning' : ($invoice->status === 'paid' ? 'info' : 'secondary')) }}">
                                                                            {{ ucfirst($invoice->status) }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="text-center py-4">
                                                    <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                                                    <p class="text-muted mt-2">No operative invoices for this project</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Documents Tab -->
                        <div class="tab-pane fade" id="documents" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Project Documents</h5>
                                @if(auth()->user()->canManageProjects())
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                                        <i class="bi bi-cloud-upload me-1"></i>Upload Document
                                    </button>
                                @endif
                            </div>

                            <div id="documentsContainer">
                                @if($project->projectDocuments->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead>
                                                <tr>
                                                    <th style="width: 40px;"></th>
                                                    <th>Document Name</th>
                                                    <th>Category</th>
                                                    <th>Size</th>
                                                    <th>Uploaded</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                        @foreach($project->projectDocuments as $document)
                                                    <tr>
                                                        <td>
                                                            <i class="{{ $document->file_type_icon }} fs-4 text-primary"></i>
                                                        </td>
                                                        <td>
                                                            <div>
                                                                <strong>{{ $document->title }}</strong>
                                                                @if($document->description)
                                                                    <br><small class="text-muted">{{ Str::limit($document->description, 80) }}</small>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-secondary">{{ ucfirst($document->category) }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="text-muted">{{ $document->formatted_file_size }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="text-muted">{{ $document->created_at->format('M j, Y') }}</span>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <button class="btn btn-outline-info btn-sm" onclick="previewDocument({{ $document->id }}, '{{ $document->title }}', '{{ route('project.documents.view', ['project' => $project->id, 'document' => $document->id]) }}')" title="Preview">
                                                                <i class="bi bi-eye"></i>
                                                            </button>
                                                                <a href="{{ route('project.documents.download', ['project' => $project->id, 'document' => $document->id]) }}" class="btn btn-outline-primary btn-sm" title="Download">
                                                                    <i class="bi bi-download"></i>
                                                                </a>
                                                            @if(auth()->user()->canManageProjects())
                                                                    <button class="btn btn-outline-secondary btn-sm" onclick="editDocument({{ $document->id }})" title="Edit">
                                                                    <i class="bi bi-pencil"></i>
                                                                </button>
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
                                        <i class="bi bi-file-earmark display-4 text-muted"></i>
                                        <h6 class="mt-3">No documents yet</h6>
                                        <p class="text-muted">Upload project files, pictures, contracts, and more</p>
                                        @if(auth()->user()->canManageProjects())
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                                                <i class="bi bi-cloud-upload me-2"></i>Upload First Document
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Variations Tab -->
                        <div class="tab-pane fade" id="variations" role="tabpanel">
                            <!-- Professional Variations Header -->
                            <div class="variations-header mb-4">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <div class="header-icon bg-warning bg-opacity-10 text-warning rounded-circle p-3 me-3">
                                                <i class="bi bi-arrow-left-right fs-4"></i>
                                            </div>
                                            <div>
                                                <h4 class="mb-1 fw-bold">Project Variations</h4>
                                                <p class="text-muted mb-0">Manage changes, additions, and modifications to the project scope</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        @if(auth()->user()->canManageProjects() || in_array(auth()->user()->role, ['site_manager', 'project_manager']))
                                            <button class="btn btn-warning btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#addVariationModal">
                                                <i class="bi bi-plus-circle me-2"></i>Create New Variation
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Variations Statistics Cards -->
                            @php
                                $totalVariations = $project->projectVariations->count();
                                $pendingVariations = $project->projectVariations->where('status', 'draft')->count() + 
                                                    $project->projectVariations->where('status', 'submitted')->count();
                                $approvedVariations = $project->projectVariations->where('status', 'approved')->count();
                                $implementedVariations = $project->projectVariations->where('status', 'implemented')->count();
                                $totalCostImpact = $project->projectVariations->where('status', '!=', 'rejected')->sum('cost_impact');
                                $totalTimeImpact = $project->projectVariations->where('status', '!=', 'rejected')->sum('time_impact');
                            @endphp
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-2">
                                    <div class="variation-stat-card card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-circle p-2 mx-auto mb-2" style="width: 50px; height: 50px;">
                                                <i class="bi bi-list-ol fs-5"></i>
                                            </div>
                                            <h3 class="mb-0 fw-bold">{{ $totalVariations }}</h3>
                                            <small class="text-muted">Total Variations</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="variation-stat-card card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-circle p-2 mx-auto mb-2" style="width: 50px; height: 50px;">
                                                <i class="bi bi-clock-history fs-5"></i>
                                            </div>
                                            <h3 class="mb-0 fw-bold text-warning">{{ $pendingVariations }}</h3>
                                            <small class="text-muted">Pending Review</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="variation-stat-card card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <div class="stat-icon bg-success bg-opacity-10 text-success rounded-circle p-2 mx-auto mb-2" style="width: 50px; height: 50px;">
                                                <i class="bi bi-check-circle fs-5"></i>
                                            </div>
                                            <h3 class="mb-0 fw-bold text-success">{{ $approvedVariations }}</h3>
                                            <small class="text-muted">Approved</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="variation-stat-card card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <div class="stat-icon bg-info bg-opacity-10 text-info rounded-circle p-2 mx-auto mb-2" style="width: 50px; height: 50px;">
                                                <i class="bi bi-gear-fill fs-5"></i>
                                            </div>
                                            <h3 class="mb-0 fw-bold text-info">{{ $implementedVariations }}</h3>
                                            <small class="text-muted">Implemented</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="variation-stat-card card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <div class="stat-icon bg-{{ $totalCostImpact >= 0 ? 'success' : 'danger' }} bg-opacity-10 text-{{ $totalCostImpact >= 0 ? 'success' : 'danger' }} rounded-circle p-2 mx-auto mb-2" style="width: 50px; height: 50px;">
                                                <i class="bi bi-cash-stack fs-5"></i>
                                            </div>
                                            <h5 class="mb-0 fw-bold text-{{ $totalCostImpact >= 0 ? 'success' : 'danger' }}">
                                                {{ $totalCostImpact >= 0 ? '+' : '' }}£{{ number_format(abs($totalCostImpact), 0) }}
                                            </h5>
                                            <small class="text-muted">Cost Impact</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="variation-stat-card card border-0 shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <div class="stat-icon bg-purple bg-opacity-10 text-purple rounded-circle p-2 mx-auto mb-2" style="width: 50px; height: 50px;">
                                                <i class="bi bi-calendar-plus fs-5"></i>
                                            </div>
                                            <h5 class="mb-0 fw-bold {{ $totalTimeImpact >= 0 ? 'text-danger' : 'text-success' }}">
                                                {{ $totalTimeImpact >= 0 ? '+' : '' }}{{ abs($totalTimeImpact) }} days
                                            </h5>
                                            <small class="text-muted">Time Impact</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Variation Filters -->
                            <div class="variation-filters card border-0 shadow-sm mb-4">
                                <div class="card-body">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label small text-muted">
                                                <i class="bi bi-funnel me-1"></i>Filter by Status
                                            </label>
                                            <select class="form-select variation-filter-select" id="variationStatusFilter">
                                                <option value="">All Statuses</option>
                                                <option value="draft">📝 Draft</option>
                                                <option value="submitted">📤 Submitted</option>
                                                <option value="approved">✅ Approved</option>
                                                <option value="rejected">❌ Rejected</option>
                                                <option value="implemented">⚙️ Implemented</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small text-muted">
                                                <i class="bi bi-tag me-1"></i>Type
                                            </label>
                                            <select class="form-select variation-filter-select" id="variationTypeFilter">
                                                <option value="">All Types</option>
                                                <option value="addition">➕ Addition</option>
                                                <option value="omission">➖ Omission</option>
                                                <option value="substitution">🔄 Substitution</option>
                                                <option value="change_order">📋 Change Order</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small text-muted">
                                                <i class="bi bi-sort-down me-1"></i>Sort By
                                            </label>
                                            <select class="form-select variation-filter-select" id="variationSortBy">
                                                <option value="number">Variation Number</option>
                                                <option value="date">Date Created</option>
                                                <option value="cost">Cost Impact</option>
                                                <option value="time">Time Impact</option>
                                                <option value="status">Status</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="d-flex gap-2">
                                                <input type="text" class="form-control" id="variationSearch" placeholder="🔍 Search variations...">
                                                <button class="btn btn-outline-secondary" onclick="clearVariationFilters()">
                                                    <i class="bi bi-x-lg"></i> Clear
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Variations Table -->
                            <div id="variationsContainer">
                                @if($project->projectVariations->count() > 0)
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover variation-table mb-0">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th class="border-0 ps-4" style="width: 100px;">
                                                                <i class="bi bi-hash me-2"></i>Number
                                                            </th>
                                                            <th class="border-0">
                                                                <i class="bi bi-text-left me-2"></i>Variation Details
                                                            </th>
                                                            <th class="border-0">
                                                                <i class="bi bi-diagram-3 me-2"></i>Type
                                                            </th>
                                                            <th class="border-0">
                                                                <i class="bi bi-cash me-2"></i>Cost Impact
                                                            </th>
                                                            <th class="border-0">
                                                                <i class="bi bi-clock me-2"></i>Time Impact
                                                            </th>
                                                            <th class="border-0">
                                                                <i class="bi bi-flag me-2"></i>Status
                                                            </th>
                                                            <th class="border-0">
                                                                <i class="bi bi-person me-2"></i>Requested By
                                                            </th>
                                                            <th class="border-0 text-center">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($project->projectVariations as $variation)
                                                            <tr class="variation-row" data-status="{{ $variation->status }}" data-type="{{ $variation->type }}">
                                                                <td class="ps-4">
                                                                    <div class="variation-number">
                                                                        <span class="badge bg-secondary rounded-pill px-3 py-2">
                                                                            #{{ $variation->variation_number }}
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="variation-info">
                                                                        <h6 class="mb-1 fw-semibold">{{ $variation->title }}</h6>
                                                                        @if($variation->description)
                                                                            <small class="text-muted d-block mb-1">{{ Str::limit($variation->description, 80) }}</small>
                                                                        @endif
                                                                        <div class="mt-1">
                                                                            <small class="text-muted">
                                                                                <i class="bi bi-calendar me-1"></i>
                                                                                Created {{ $variation->created_at->format('M j, Y') }}
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="variation-type">
                                                                        <span class="badge bg-light text-dark border">
                                                                            @php
                                                                                $typeIcons = [
                                                                                    'addition' => 'bi-plus-circle',
                                                                                    'omission' => 'bi-dash-circle',
                                                                                    'substitution' => 'bi-arrow-left-right',
                                                                                    'change_order' => 'bi-clipboard-check'
                                                                                ];
                                                                                $typeIcon = $typeIcons[$variation->type] ?? 'bi-circle';
                                                                            @endphp
                                                                            <i class="bi {{ $typeIcon }} me-1"></i>
                                                                            {{ ucfirst(str_replace('_', ' ', $variation->type)) }}
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="cost-impact">
                                                                        <span class="fw-bold fs-5 {{ $variation->cost_impact >= 0 ? 'text-success' : 'text-danger' }}">
                                                                            {{ $variation->cost_impact >= 0 ? '+' : '' }}{{ $variation->formatted_cost_impact }}
                                                                        </span>
                                                                        @if(abs($variation->cost_impact) > 10000)
                                                                            <br><small class="text-warning">
                                                                                <i class="bi bi-exclamation-triangle"></i> High value
                                                                            </small>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="time-impact">
                                                                        @if($variation->time_impact != 0)
                                                                            <span class="badge {{ $variation->time_impact > 0 ? 'bg-danger' : 'bg-success' }} bg-opacity-75">
                                                                                {{ $variation->time_impact > 0 ? '+' : '' }}{{ $variation->formatted_time_impact }}
                                                                            </span>
                                                                        @else
                                                                            <span class="text-muted">No impact</span>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="status-info">
                                                                        <span class="badge rounded-pill bg-{{ $variation->status_color }} px-3 py-2">
                                                                            @php
                                                                                $statusIcons = [
                                                                                    'draft' => 'bi-pencil',
                                                                                    'submitted' => 'bi-send',
                                                                                    'approved' => 'bi-check-circle',
                                                                                    'rejected' => 'bi-x-circle',
                                                                                    'implemented' => 'bi-gear-fill'
                                                                                ];
                                                                                $statusIcon = $statusIcons[$variation->status] ?? 'bi-circle';
                                                                            @endphp
                                                                            <i class="bi {{ $statusIcon }} me-1"></i>
                                                                            {{ ucfirst(str_replace('_', ' ', $variation->status)) }}
                                                                        </span>
                                                                        @if($variation->is_overdue)
                                                                            <div class="mt-1">
                                                                                <small class="text-danger">
                                                                                    <i class="bi bi-exclamation-triangle me-1"></i>Overdue
                                                                                </small>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="requested-by">
                                                                        @if($variation->requested_by)
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="avatar-sm bg-secondary text-white rounded-circle me-2" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                                                                    {{ substr($variation->requested_by, 0, 1) }}
                                                                                </div>
                                                                                <small>{{ $variation->requested_by }}</small>
                                                                            </div>
                                                                        @else
                                                                            <span class="text-muted">—</span>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="dropdown">
                                                                        <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown">
                                                                            <i class="bi bi-three-dots-vertical"></i>
                                                                        </button>
                                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                                            <li>
                                                                                <button class="dropdown-item" onclick="viewVariation({{ $variation->id }})">
                                                                                    <i class="bi bi-eye me-2"></i>View Details
                                                                                </button>
                                                                            </li>
                                                                            @if(auth()->user()->canManageProjects())
                                                                                @if(!in_array($variation->status, ['approved', 'implemented']))
                                                                                    <li>
                                                                                        <button class="dropdown-item" onclick="editVariation({{ $variation->id }})">
                                                                                            <i class="bi bi-pencil me-2"></i>Edit Variation
                                                                                        </button>
                                                                                    </li>
                                                                                @endif
                                                                                @if($variation->status === 'submitted')
                                                                                    <li><hr class="dropdown-divider"></li>
                                                                                    <li>
                                                                                        <button class="dropdown-item text-success" onclick="approveVariation({{ $variation->id }})">
                                                                                            <i class="bi bi-check-circle me-2"></i>Approve
                                                                                        </button>
                                                                                    </li>
                                                                                    <li>
                                                                                        <button class="dropdown-item text-danger" onclick="rejectVariation({{ $variation->id }})">
                                                                                            <i class="bi bi-x-circle me-2"></i>Reject
                                                                                        </button>
                                                                                    </li>
                                                                                @endif
                                                                                @if($variation->status === 'approved')
                                                                                    <li><hr class="dropdown-divider"></li>
                                                                                    <li>
                                                                                        <button class="dropdown-item text-info" onclick="implementVariation({{ $variation->id }})">
                                                                                            <i class="bi bi-gear me-2"></i>Mark as Implemented
                                                                                        </button>
                                                                                    </li>
                                                                                @endif
                                                                            @endif
                                                                        </ul>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Variations Summary Card -->
                                    @if($totalCostImpact != 0 || $totalTimeImpact != 0)
                                        <div class="card border-0 shadow-sm mt-3 bg-light">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-md-4">
                                                        <h6 class="text-muted mb-0">Total Project Impact</h6>
                                                    </div>
                                                    <div class="col-md-4 text-center">
                                                        <div class="d-inline-block">
                                                            <small class="text-muted">Cost Impact</small>
                                                            <h4 class="mb-0 {{ $totalCostImpact >= 0 ? 'text-success' : 'text-danger' }}">
                                                                {{ $totalCostImpact >= 0 ? '+' : '' }}£{{ number_format(abs($totalCostImpact), 2) }}
                                                            </h4>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 text-end">
                                                        <div class="d-inline-block">
                                                            <small class="text-muted">Schedule Impact</small>
                                                            <h4 class="mb-0 {{ $totalTimeImpact > 0 ? 'text-danger' : 'text-success' }}">
                                                                {{ $totalTimeImpact > 0 ? '+' : '' }}{{ abs($totalTimeImpact) }} days
                                                            </h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body py-5">
                                            <div class="empty-state text-center">
                                                <div class="empty-icon-wrapper mx-auto mb-4" style="width: 120px; height: 120px;">
                                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center h-100">
                                                        <i class="bi bi-arrow-left-right display-1 text-muted"></i>
                                                    </div>
                                                </div>
                                                <h5 class="fw-bold mb-2">No Variations Recorded</h5>
                                                <p class="text-muted mb-4">Track project changes, additions, and modifications as they arise</p>
                                                @if(auth()->user()->canManageProjects())
                                                    <button class="btn btn-warning btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#addVariationModal">
                                                        <i class="bi bi-plus-circle me-2"></i>Create First Variation
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Snagging Tab -->
                        <div class="tab-pane fade" id="snagging" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Snagging Items</h5>
                                @if(auth()->user()->canManageProjects() || in_array(auth()->user()->role, ['site_manager', 'project_manager']))
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSnaggingModal">
                                        <i class="bi bi-plus me-1"></i>Add Snagging Item
                                    </button>
                                @endif
                            </div>

                            <div id="snaggingContainer">
                                @if($project->projectSnaggings->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Item #</th>
                                                    <th>Title</th>
                                                    <th>Location</th>
                                                    <th>Category</th>
                                                    <th>Severity</th>
                                                    <th>Status</th>
                                                    <th>Assigned To</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($project->projectSnaggings as $snagging)
                                                    <tr>
                                                        <td class="fw-bold">{{ $snagging->item_number }}</td>
                                                        <td>
                                                            <strong>{{ $snagging->title }}</strong>
                                                            @if($snagging->description)
                                                                <br><small class="text-muted">{{ Str::limit($snagging->description, 50) }}</small>
                                                            @endif
                                                        </td>
                                                        <td>{{ $snagging->location }}</td>
                                                        <td>
                                                            <i class="{{ $snagging->category_icon }} me-1"></i>
                                                            {{ ucfirst($snagging->category) }}
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $snagging->severity_color }}">
                                                                {{ ucfirst($snagging->severity) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $snagging->status_color }}">
                                                                {{ ucfirst(str_replace('_', ' ', $snagging->status)) }}
                                                            </span>
                                                            @if($snagging->is_overdue)
                                                                <br><small class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Overdue</small>
                                                            @endif
                                                        </td>
                                                        <td>{{ $snagging->assignee->name ?? 'Unassigned' }}</td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <button class="btn btn-outline-primary btn-sm" onclick="viewSnagging({{ $snagging->id }})">
                                                                    <i class="bi bi-eye"></i>
                                                                </button>
                                                                @if(auth()->user()->canManageProjects())
                                                                    <button class="btn btn-outline-secondary btn-sm" onclick="editSnagging({{ $snagging->id }})">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </button>
                                                                    @if($snagging->status === 'open')
                                                                        <button class="btn btn-outline-success btn-sm" onclick="resolveSnagging({{ $snagging->id }})">
                                                                            <i class="bi bi-check"></i>
                                                                        </button>
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
                                        <i class="bi bi-flag display-4 text-muted"></i>
                                        <h6 class="mt-3">No snagging items yet</h6>
                                        <p class="text-muted">Track defects, incomplete work, and quality issues</p>
                                        @if(auth()->user()->canManageProjects())
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSnaggingModal">
                                                <i class="bi bi-flag me-2"></i>Add First Item
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <!-- Cost Breakdown -->
            <div class="card mb-3 border-0 shadow-sm sidebar-financial-card">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-cash-stack me-2 text-success"></i>Cost Breakdown
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Expenses:</span>
                        <span class="fw-semibold small">{{ auth()->user()->company->formatCurrency($financial_stats['total_expenses']) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Invoices Paid:</span>
                        <span class="fw-semibold small">{{ auth()->user()->company->formatCurrency($financial_stats['total_invoices_paid']) }}</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold small">Total Actual Costs:</span>
                        <span class="fw-bold small text-primary">{{ auth()->user()->company->formatCurrency($financial_stats['actual_costs']) }}</span>
                    </div>
                        </div>
                    </div>

            <!-- Budget Analysis -->
            <div class="card mb-3 border-0 shadow-sm sidebar-financial-card">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-graph-up-arrow me-2 text-info"></i>Budget Analysis
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Project Budget:</span>
                        <span class="fw-semibold small">{{ auth()->user()->company->formatCurrency($financial_stats['project_budget']) }}</span>
                                </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Remaining:</span>
                        <span class="fw-semibold small text-{{ $financial_stats['remaining_budget'] >= 0 ? 'success' : 'danger' }}">
                            {{ auth()->user()->company->formatCurrency(abs($financial_stats['remaining_budget'])) }}
                            @if($financial_stats['remaining_budget'] < 0)
                                <i class="bi bi-exclamation-triangle ms-1"></i>
                            @endif
                        </span>
                            </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Utilization:</span>
                        <span class="fw-semibold small">{{ number_format($financial_stats['budget_utilization'], 1) }}%</span>
                        </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar {{ $financial_stats['budget_utilization'] <= 70 ? 'bg-success' : ($financial_stats['budget_utilization'] <= 90 ? 'bg-warning' : 'bg-danger') }}" 
                             style="width: {{ min($financial_stats['budget_utilization'], 100) }}%"
                             data-bs-toggle="tooltip"
                             title="{{ number_format($financial_stats['budget_utilization'], 1) }}% of budget used">
                    </div>
                                </div>
                    @if($financial_stats['is_over_budget'])
                        <div class="alert alert-danger p-2 mb-0 budget-alert">
                            <small class="d-flex align-items-center">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                Over budget by {{ auth()->user()->company->formatCurrency($financial_stats['actual_costs'] - $financial_stats['project_budget']) }}
                            </small>
                            </div>
                    @else
                        <small class="text-success">
                            <i class="bi bi-check-circle me-1"></i>Within budget
                        </small>
                    @endif
                        </div>
                    </div>

            <!-- Quick Actions -->
            <div class="card mb-3">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0">Quick Actions</h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-grid gap-2">
                        @if(auth()->user()->canManageTasks())
                            <a href="{{ route('tasks.create', ['project' => $project->id]) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus me-2"></i>Add Task
                            </a>
                        @endif
                        @if(auth()->user()->canManageProjects())
                            <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-pencil me-2"></i>Edit Project
                            </a>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                                <i class="bi bi-receipt me-2"></i>Add Expense
                            </button>
                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                                <i class="bi bi-cloud-upload me-2"></i>Upload Document
                            </button>
                        @endif
                        <button class="btn btn-sm btn-outline-info" onclick="window.print()">
                            <i class="bi bi-printer me-2"></i>Print Report
                        </button>
                    </div>
                </div>
            </div>

            <!-- Project Statistics -->
            <div class="card">
                <div class="card-header py-2">
                    <h6 class="card-title mb-0">Project Statistics</h6>
                </div>
                <div class="card-body p-3">
                    <div class="stat-item-compact mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon-small bg-primary me-2">
                                    <i class="bi bi-list-task"></i>
                                </div>
                                <span class="small">Total Tasks</span>
                            </div>
                            <span class="fw-bold">{{ $project_stats['total_tasks'] ?? $project->tasks->count() }}</span>
                        </div>
                    </div>
                    
                    <div class="stat-item-compact mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon-small bg-success me-2">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <span class="small">Completed</span>
                            </div>
                            <span class="fw-bold text-success">{{ $project_stats['completed_tasks'] ?? $project->tasks->where('status', 'completed')->count() }}</span>
                        </div>
                    </div>
                    
                    <div class="stat-item-compact mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon-small bg-warning me-2">
                                    <i class="bi bi-clock"></i>
                                </div>
                                <span class="small">In Progress</span>
                            </div>
                            <span class="fw-bold text-warning">{{ $project_stats['in_progress_tasks'] ?? $project->tasks->where('status', 'in_progress')->count() }}</span>
                        </div>
                    </div>
                    
                    <div class="stat-item-compact mb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon-small bg-danger me-2">
                                        <i class="bi bi-exclamation-triangle"></i>
                                    </div>
                                    <span class="small">Overdue</span>
                                </div>
                            <span class="fw-bold text-danger">{{ $project_stats['overdue_tasks'] ?? $project->tasks->where('due_date', '<', now())->where('status', '!=', 'completed')->count() }}</span>
                            </div>
                        </div>
                                </div>
            </div>
        </div>
    </div>
</div>




<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addExpenseModalLabel">Add Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addExpenseForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expense-category" class="form-label">Category *</label>
                                <select class="form-select" id="expense-category" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="materials">Materials</option>
                                    <option value="travel">Travel</option>
                                    <option value="equipment">Equipment</option>
                                    <option value="subcontractor">Subcontractor</option>
                                    <option value="labor">Labor</option>
                                    <option value="permits">Permits</option>
                                    <option value="utilities">Utilities</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expense-net-amount" class="form-label">Net Amount (before VAT) *</label>
                                <div class="input-group">
                                    <span class="input-group-text">£</span>
                                    <input type="number" class="form-control" id="expense-net-amount" name="net_amount" step="0.01" min="0" required oninput="calculateVAT()" onchange="calculateVAT()" onkeyup="calculateVAT()">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expense-vat-rate" class="form-label">VAT Rate (%) *</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="expense-vat-rate" name="vat_rate" step="0.01" min="0" max="100" value="20" required oninput="calculateVAT()" onchange="calculateVAT()" onkeyup="calculateVAT()">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expense-vat-amount" class="form-label">VAT Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">£</span>
                                    <input type="number" class="form-control bg-light" id="expense-vat-amount" step="0.01" readonly>
                                </div>
                                <small class="text-muted">Automatically calculated</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expense-total-amount" class="form-label">Total Amount (inc. VAT)</label>
                                <div class="input-group">
                                    <span class="input-group-text">£</span>
                                    <input type="number" class="form-control bg-light fw-bold" id="expense-total-amount" step="0.01" readonly>
                                </div>
                                <small class="text-muted">Net + VAT</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expense-date" class="form-label">Expense Date *</label>
                                <input type="date" class="form-control" id="expense-date" name="expense_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expense-vendor" class="form-label">Vendor Name</label>
                                <input type="text" class="form-control" id="expense-vendor" name="vendor_name" placeholder="e.g., Home Depot">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expense-invoice" class="form-label">Invoice Number</label>
                                <input type="text" class="form-control" id="expense-invoice" name="invoice_number" placeholder="e.g., INV-12345">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expense-receipt" class="form-label">Receipt</label>
                                <input type="file" class="form-control" id="expense-receipt" name="receipt" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="form-text text-muted">PDF, JPG, PNG files up to 10MB</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Empty column for balance -->
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="expense-notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="expense-notes" name="notes" rows="2" placeholder="Additional notes or details"></textarea>
                    </div>
                    
                    <script>
                        // Inline VAT calculation to ensure it works immediately
                        function calculateVAT() {
                            const netAmount = parseFloat(document.getElementById('expense-net-amount')?.value) || 0;
                            const vatRate = parseFloat(document.getElementById('expense-vat-rate')?.value) || 20;
                            
                            const vatAmount = (netAmount * vatRate) / 100;
                            const totalAmount = netAmount + vatAmount;
                            
                            const vatAmountField = document.getElementById('expense-vat-amount');
                            const totalAmountField = document.getElementById('expense-total-amount');
                            
                            if (vatAmountField) vatAmountField.value = vatAmount.toFixed(2);
                            if (totalAmountField) totalAmountField.value = totalAmount.toFixed(2);
                        }
                        
                        // Auto-calculate when modal opens
                        setTimeout(calculateVAT, 500);
                    </script>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Add Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Document Preview Modal -->
<div class="modal fade" id="documentPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalTitle">Document Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent" class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a id="downloadFromPreview" href="#" class="btn btn-primary" target="_blank">
                    <i class="bi bi-download me-1"></i>Download
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Upload Document Modal -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="uploadDocumentForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category *</label>
                        <select class="form-select" name="category" required>
                            <option value="">Select Category</option>
                            <option value="plans">Plans & Drawings</option>
                            <option value="photos">Photos</option>
                            <option value="contracts">Contracts</option>
                            <option value="permits">Permits</option>
                            <option value="reports">Reports</option>
                            <option value="specifications">Specifications</option>
                            <option value="invoices">Invoices</option>
                            <option value="certificates">Certificates</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File *</label>
                        <input type="file" class="form-control" name="file" required>
                        <small class="text-muted">Maximum file size: 50MB</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tags</label>
                        <input type="text" class="form-control" name="tags" placeholder="Enter tags separated by commas">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_public" id="isPublic">
                            <label class="form-check-label" for="isPublic">
                                Visible to client
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload Document</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Variation Modal -->
<div class="modal fade" id="addVariationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Variation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addVariationForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description *</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason for Variation *</label>
                        <textarea class="form-control" name="reason" rows="2" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Type *</label>
                                <select class="form-select" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="addition">Addition</option>
                                    <option value="omission">Omission</option>
                                    <option value="substitution">Substitution</option>
                                    <option value="change_order">Change Order</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cost Impact *</label>
                                <div class="input-group">
                                    <span class="input-group-text">£</span>
                                    <input type="number" class="form-control" name="cost_impact" step="0.01" required>
                                </div>
                                <small class="text-muted">Use negative values for cost reductions</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Time Impact (Days) *</label>
                                <input type="number" class="form-control" name="time_impact_days" required>
                                <small class="text-muted">Use negative values for time savings</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Client Reference</label>
                                <input type="text" class="form-control" name="client_reference">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Requested Date *</label>
                                <input type="date" class="form-control" name="requested_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Required By Date</label>
                                <input type="date" class="form-control" name="required_by_date">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Variation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Snagging Modal -->
<div class="modal fade" id="addSnaggingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Snagging Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSnaggingForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description *</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Location *</label>
                                <input type="text" class="form-control" name="location" placeholder="e.g., Kitchen, Bathroom, Floor 2" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Category *</label>
                                <select class="form-select" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="defect">Defect</option>
                                    <option value="incomplete">Incomplete Work</option>
                                    <option value="damage">Damage</option>
                                    <option value="quality">Quality Issue</option>
                                    <option value="safety">Safety Concern</option>
                                    <option value="compliance">Compliance Issue</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Severity *</label>
                                <select class="form-select" name="severity" required>
                                    <option value="">Select Severity</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Identified Date *</label>
                                <input type="date" class="form-control" name="identified_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Target Completion Date</label>
                                <input type="date" class="form-control" name="target_completion_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Trade Responsible</label>
                                <input type="text" class="form-control" name="trade_responsible" placeholder="e.g., Plumbing, Electrical">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cost to Fix</label>
                        <div class="input-group">
                            <span class="input-group-text">£</span>
                            <input type="number" class="form-control" name="cost_to_fix" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Photos (Before)</label>
                        <input type="file" class="form-control" name="photos_before[]" multiple accept="image/*">
                        <small class="text-muted">Upload up to 5 photos, 10MB each</small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="hidden" name="client_reported" value="0">
                            <input class="form-check-input" type="checkbox" name="client_reported" id="clientReported" value="1">
                            <label class="form-check-label" for="clientReported">
                                Reported by client
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Snagging Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Function to confirm and delete a task
function confirmDeleteTask(taskId, taskTitle) {
    if (confirm(`Are you sure you want to delete the task "${taskTitle}"?\n\nThis action cannot be undone and will permanently remove the task and all its data.`)) {
        // Create a form to submit DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/tasks/${taskId}`;
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Add method spoofing for DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        // Add a hidden field to indicate we want to stay on tasks tab
        const tabField = document.createElement('input');
        tabField.type = 'hidden';
        tabField.name = 'return_to_tab';
        tabField.value = 'tasks';
        form.appendChild(tabField);
        
        document.body.appendChild(form);
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const projectId = {{ $project->id }};
    
    // Check if we should activate the tasks tab based on URL fragment
    if (window.location.hash === '#tasks') {
        // Activate tasks tab
        const tasksTab = document.querySelector('#tasks-tab');
        const tasksPane = document.querySelector('#tasks');
        const overviewTab = document.querySelector('#overview-tab');
        const overviewPane = document.querySelector('#overview');
        
        if (tasksTab && tasksPane && overviewTab && overviewPane) {
            // Remove active classes from overview
            overviewTab.classList.remove('active');
            overviewPane.classList.remove('show', 'active');
            
            // Add active classes to tasks
            tasksTab.classList.add('active');
            tasksPane.classList.add('show', 'active');
        }
    }

    // VAT Calculation Function
    window.calculateVAT = function() {
        const netAmountInput = document.getElementById('expense-net-amount');
        const vatRateInput = document.getElementById('expense-vat-rate');
        const vatAmountInput = document.getElementById('expense-vat-amount');
        const totalAmountInput = document.getElementById('expense-total-amount');
        
        if (!netAmountInput || !vatRateInput || !vatAmountInput || !totalAmountInput) {
            console.log('VAT calculation inputs not found');
            return;
        }
        
        const netAmount = parseFloat(netAmountInput.value) || 0;
        const vatRate = parseFloat(vatRateInput.value) || 0;
        
        const vatAmount = (netAmount * vatRate) / 100;
        const totalAmount = netAmount + vatAmount;
        
        vatAmountInput.value = vatAmount.toFixed(2);
        totalAmountInput.value = totalAmount.toFixed(2);
        
        console.log(`VAT Calculation: Net £${netAmount.toFixed(2)} + VAT £${vatAmount.toFixed(2)} (${vatRate}%) = Total £${totalAmount.toFixed(2)}`);
    };

    // Set up expense modal when it's shown
    const expenseModal = document.getElementById('addExpenseModal');
    if (expenseModal) {
        expenseModal.addEventListener('shown.bs.modal', function () {
            // Set default date to today
            const dateInput = document.getElementById('expense-date');
            if (dateInput && !dateInput.value) {
                dateInput.value = new Date().toISOString().split('T')[0];
            }
            
            // Add event listeners for VAT calculation
            const netAmountInput = document.getElementById('expense-net-amount');
            const vatRateInput = document.getElementById('expense-vat-rate');
            
            if (netAmountInput && vatRateInput) {
                netAmountInput.addEventListener('input', calculateVAT);
                netAmountInput.addEventListener('change', calculateVAT);
                vatRateInput.addEventListener('input', calculateVAT);
                vatRateInput.addEventListener('change', calculateVAT);
                
                // Initial calculation
                setTimeout(calculateVAT, 100);
            }
        });
    }

    // Add Expense Form
    document.getElementById('addExpenseForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch(`/projects/${projectId}/expenses`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('addExpenseModal')).hide();
                location.reload(); // Refresh to show new expense
            } else {
                alert('Error adding expense: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding expense');
        });
    });

    // Upload Document Form
    document.getElementById('uploadDocumentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch(`/projects/${projectId}/documents`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('uploadDocumentModal')).hide();
                location.reload(); // Refresh to show new document
            } else {
                alert('Error uploading document: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error uploading document');
        });
    });

    // Add Variation Form
    document.getElementById('addVariationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch(`/projects/${projectId}/variations`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('addVariationModal')).hide();
                location.reload(); // Refresh to show new variation
            } else {
                alert('Error adding variation: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding variation');
        });
    });

    // Add Snagging Form
    document.getElementById('addSnaggingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch(`/projects/${projectId}/snagging`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('addSnaggingModal')).hide();
                location.reload(); // Refresh to show new snagging item
            } else {
                let errorMessage = data.message || 'Unknown error';
                if (data.errors) {
                    // Show validation errors
                    const errorList = Object.values(data.errors).flat();
                    errorMessage += '\n\nValidation errors:\n' + errorList.join('\n');
                }
                alert('Error adding snagging item: ' + errorMessage);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding snagging item: ' + error.message);
        });
    });

    // Set default dates
    const today = new Date().toISOString().split('T')[0];
    document.querySelector('input[name="expense_date"]').value = today;
    document.querySelector('input[name="requested_date"]').value = today;
    document.querySelector('input[name="identified_date"]').value = today;
});

// Action functions for buttons
// Expense Management Functions
function viewExpense(id) {
    // Implementation for viewing expense details
    window.open(`/projects/{{ $project->id }}/expenses/${id}`, '_blank');
}

function editExpense(id) {
    // Implementation for editing expense
    alert('Edit expense functionality will be implemented');
}

function approveExpense(id) {
    if (confirm('Are you sure you want to approve this expense?')) {
        fetch(`/projects/{{ $project->id }}/expenses/${id}/approve`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error approving expense');
            }
        });
    }
}

function rejectExpense(id) {
    const reason = prompt('Please provide a reason for rejecting this expense:');
    if (reason) {
        fetch(`/projects/{{ $project->id }}/expenses/${id}/reject`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error rejecting expense');
            }
        });
    }
}

// Expense Filtering Functions
document.addEventListener('DOMContentLoaded', function() {
    // Initialize expense filters if on expenses tab
    const expenseStatusFilter = document.getElementById('expenseStatusFilter');
    const expenseCategoryFilter = document.getElementById('expenseCategoryFilter');
    const expenseDateFilter = document.getElementById('expenseDateFilter');
    const expenseSearch = document.getElementById('expenseSearch');
    
    if (expenseStatusFilter) {
        expenseStatusFilter.addEventListener('change', filterExpenses);
    }
    if (expenseCategoryFilter) {
        expenseCategoryFilter.addEventListener('change', filterExpenses);
    }
    if (expenseDateFilter) {
        expenseDateFilter.addEventListener('change', filterExpenses);
    }
    if (expenseSearch) {
        expenseSearch.addEventListener('input', filterExpenses);
    }
});

function filterExpenses() {
    const statusFilter = document.getElementById('expenseStatusFilter')?.value.toLowerCase();
    const categoryFilter = document.getElementById('expenseCategoryFilter')?.value.toLowerCase();
    const dateFilter = document.getElementById('expenseDateFilter')?.value;
    const searchTerm = document.getElementById('expenseSearch')?.value.toLowerCase();
    
    const rows = document.querySelectorAll('.expense-row');
    
    rows.forEach(row => {
        let show = true;
        
        // Status filter
        if (statusFilter && row.dataset.status !== statusFilter) {
            show = false;
        }
        
        // Category filter
        if (categoryFilter && row.dataset.category !== categoryFilter) {
            show = false;
        }
        
        // Search filter
        if (searchTerm) {
            const text = row.textContent.toLowerCase();
            if (!text.includes(searchTerm)) {
                show = false;
            }
        }
        
        // Date filter (simplified for now)
        if (dateFilter) {
            // This would need actual date comparison logic
            // For now, just showing the structure
        }
        
        row.style.display = show ? '' : 'none';
    });
    
    // Update count
    const visibleRows = document.querySelectorAll('.expense-row:not([style*="display: none"])');
    updateExpenseCount(visibleRows.length);
}

function clearExpenseFilters() {
    document.getElementById('expenseStatusFilter').value = '';
    document.getElementById('expenseCategoryFilter').value = '';
    document.getElementById('expenseDateFilter').value = '';
    document.getElementById('expenseSearch').value = '';
    filterExpenses();
}

function updateExpenseCount(count) {
    // Update a counter display if exists
    const counterElement = document.getElementById('expenseCount');
    if (counterElement) {
        counterElement.textContent = `Showing ${count} expense${count !== 1 ? 's' : ''}`;
    }
}

function viewDocument(id) {
    window.open(`/projects/{{ $project->id }}/documents/${id}`, '_blank');
}

function editDocument(id) {
    alert('Edit document functionality will be implemented');
}

// Variation Management Functions
function viewVariation(id) {
    window.open(`/projects/{{ $project->id }}/variations/${id}`, '_blank');
}

function editVariation(id) {
    alert('Edit variation functionality will be implemented');
}

function approveVariation(id) {
    if (confirm('Are you sure you want to approve this variation? This will allow it to proceed to implementation.')) {
        fetch(`/projects/{{ $project->id }}/variations/${id}/approve`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error approving variation');
            }
        });
    }
}

function rejectVariation(id) {
    const reason = prompt('Please provide a reason for rejecting this variation:');
    if (reason) {
        fetch(`/projects/{{ $project->id }}/variations/${id}/reject`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error rejecting variation');
            }
        });
    }
}

// Variation Filtering Functions
document.addEventListener('DOMContentLoaded', function() {
    // Initialize variation filters if on variations tab
    const variationStatusFilter = document.getElementById('variationStatusFilter');
    const variationTypeFilter = document.getElementById('variationTypeFilter');
    const variationSortBy = document.getElementById('variationSortBy');
    const variationSearch = document.getElementById('variationSearch');
    
    if (variationStatusFilter) {
        variationStatusFilter.addEventListener('change', filterVariations);
    }
    if (variationTypeFilter) {
        variationTypeFilter.addEventListener('change', filterVariations);
    }
    if (variationSortBy) {
        variationSortBy.addEventListener('change', sortVariations);
    }
    if (variationSearch) {
        variationSearch.addEventListener('input', filterVariations);
    }
});

function filterVariations() {
    const statusFilter = document.getElementById('variationStatusFilter')?.value.toLowerCase();
    const typeFilter = document.getElementById('variationTypeFilter')?.value.toLowerCase();
    const searchTerm = document.getElementById('variationSearch')?.value.toLowerCase();
    
    const rows = document.querySelectorAll('.variation-row');
    
    rows.forEach(row => {
        let show = true;
        
        // Status filter
        if (statusFilter && row.dataset.status !== statusFilter) {
            show = false;
        }
        
        // Type filter
        if (typeFilter && row.dataset.type !== typeFilter) {
            show = false;
        }
        
        // Search filter
        if (searchTerm) {
            const text = row.textContent.toLowerCase();
            if (!text.includes(searchTerm)) {
                show = false;
            }
        }
        
        row.style.display = show ? '' : 'none';
    });
    
    // Update count
    const visibleRows = document.querySelectorAll('.variation-row:not([style*="display: none"])');
    updateVariationCount(visibleRows.length);
}

function sortVariations() {
    const sortBy = document.getElementById('variationSortBy')?.value;
    const tbody = document.querySelector('.variation-table tbody');
    const rows = Array.from(tbody.querySelectorAll('.variation-row'));
    
    rows.sort((a, b) => {
        switch(sortBy) {
            case 'number':
                return parseInt(a.querySelector('.variation-number .badge').textContent.replace('#', '')) - 
                       parseInt(b.querySelector('.variation-number .badge').textContent.replace('#', ''));
            case 'date':
                // Would need actual date values from data attributes
                return 0;
            case 'cost':
                // Would need to parse cost values
                return 0;
            case 'time':
                // Would need to parse time values
                return 0;
            case 'status':
                return a.dataset.status.localeCompare(b.dataset.status);
            default:
                return 0;
        }
    });
    
    // Re-append sorted rows
    rows.forEach(row => tbody.appendChild(row));
}

function clearVariationFilters() {
    document.getElementById('variationStatusFilter').value = '';
    document.getElementById('variationTypeFilter').value = '';
    document.getElementById('variationSortBy').value = 'number';
    document.getElementById('variationSearch').value = '';
    filterVariations();
}

function updateVariationCount(count) {
    // Update a counter display if exists
    const counterElement = document.getElementById('variationCount');
    if (counterElement) {
        counterElement.textContent = `Showing ${count} variation${count !== 1 ? 's' : ''}`;
    }
}

function implementVariation(id) {
    if (confirm('Are you sure you want to implement this variation? This will update the project budget and timeline.')) {
        fetch(`/projects/{{ $project->id }}/variations/${id}/implement`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error implementing variation');
            }
        });
    }
}

function viewSnagging(id) {
    window.open(`/projects/{{ $project->id }}/snagging/${id}`, '_blank');
}

function editSnagging(id) {
    alert('Edit snagging functionality will be implemented');
}

function resolveSnagging(id) {
    const notes = prompt('Please enter resolution notes:');
    if (notes) {
        const formData = new FormData();
        formData.append('resolution_notes', notes);
        
        fetch(`/projects/{{ $project->id }}/snagging/${id}/resolve`, {
            method: 'PATCH',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error resolving snagging item');
            }
        });
    }
}
</script>

<style>
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

/* Tab styling */
.nav-tabs .nav-link {
    border-bottom: 2px solid transparent;
    color: #6b7280;
    font-weight: 500;
}

.nav-tabs .nav-link.active {
    border-bottom-color: #4f46e5;
    color: #4f46e5;
    background: transparent;
}

.nav-tabs .nav-link:hover {
    border-bottom-color: #9ca3af;
    color: #374151;
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

/* Empty state styling */
.empty-state {
    background: #f9fafb;
    border-radius: 8px;
    border: 2px dashed #d1d5db;
}

/* Tab styles are now handled in the main layout file */
</style>

@endsection

@section('styles')
<style>
    /* Expense Modal Styling */
    #addExpenseModal .modal-dialog {
        max-width: 650px;
    }
    
    #addExpenseModal .modal-content {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    
    #addExpenseModal .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        border-radius: 0.5rem 0.5rem 0 0;
        padding: 1.25rem;
    }
    
    #addExpenseModal .modal-title {
        font-weight: 600;
        color: #212529;
    }
    
    #addExpenseModal .modal-body {
        padding: 1.5rem;
    }
    
    #addExpenseModal .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    #addExpenseModal .form-control,
    #addExpenseModal .form-select {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    #addExpenseModal .form-control:focus,
    #addExpenseModal .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    #addExpenseModal .input-group-text {
        background-color: #e9ecef;
        border: 1px solid #ced4da;
        color: #495057;
    }
    
    #addExpenseModal .modal-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
        border-radius: 0 0 0.5rem 0.5rem;
        padding: 1rem 1.5rem;
    }
    
    #addExpenseModal .btn {
        padding: 0.5rem 1.25rem;
        font-weight: 500;
    }
    
    #addExpenseModal .form-text {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    #addExpenseModal textarea {
        resize: vertical;
        min-height: 60px;
    }
    
    /* Ensure proper spacing between form groups */
    #addExpenseModal .mb-3 {
        margin-bottom: 1.25rem !important;
    }
    
    #addExpenseModal .row {
        margin-left: -0.75rem;
        margin-right: -0.75rem;
    }
    
    #addExpenseModal .row > [class*="col-"] {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
    
    /* Professional Tasks Tab Styling */
    .tasks-header {
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 1rem;
    }
    
    .task-stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #e9ecef;
    }
    
    .task-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .task-stat-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .task-filters-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }
    
    .tasks-table-container {
        border: 1px solid #dee2e6;
        overflow: hidden;
    }
    
    .tasks-table thead {
        background: linear-gradient(180deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .tasks-table thead th {
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem;
        color: #495057;
    }
    
    .tasks-table tbody tr {
        border-bottom: 1px solid #e9ecef;
        transition: background-color 0.15s;
    }
    
    .tasks-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .tasks-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }
    
    .task-row.task-completed {
        opacity: 0.7;
    }
    
    .task-row.task-completed .task-title {
        text-decoration: line-through;
        color: #6c757d !important;
    }
    
    .task-title {
        font-size: 0.95rem;
        color: #212529;
        transition: color 0.15s;
    }
    
    .task-title:hover {
        color: #0d6efd;
    }
    
    .task-info .task-meta {
        font-size: 0.75rem;
    }
    
    .status-select {
        border: 1px solid #dee2e6;
        font-size: 0.875rem;
        padding: 0.25rem 0.75rem;
        min-width: 120px;
        background-color: #fff;
        transition: border-color 0.15s;
    }
    
    .status-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    .avatar-sm {
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .due-date-info {
        font-size: 0.875rem;
    }
    
    .dropdown-menu {
        border: none;
        border-radius: 0.5rem;
        padding: 0.5rem;
    }
    
    .dropdown-item {
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        transition: background-color 0.15s;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    
    .bg-orange {
        background-color: #fd7e14 !important;
    }
    
    .text-orange {
        color: #fd7e14 !important;
    }
    
    .border-orange {
        border-color: #fd7e14 !important;
    }
    
    /* Empty state styling */
    .empty-state {
        padding: 3rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 0.5rem;
        border: 2px dashed #dee2e6;
    }
    
    .empty-state i {
        color: #dee2e6;
    }
    
    /* Sidebar Financial Cards Styling */
    .sidebar-financial-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .sidebar-financial-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.1) !important;
    }
    
    .sidebar-financial-card .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 2px solid #dee2e6;
    }
    
    .sidebar-financial-card .progress {
        background-color: #e9ecef;
    }
    
    .sidebar-financial-card .progress-bar {
        transition: width 0.6s ease;
    }
    
    .budget-alert {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.8; }
        100% { opacity: 1; }
    }
    
    /* Professional Project Details Styling */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
    
    .project-details-card .card {
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .project-details-card .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
    }
    
    .detail-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .avatar-circle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .hover-primary:hover {
        color: #0d6efd !important;
    }
    
    .status-card, .priority-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #e9ecef;
    }
    
    .status-card:hover, .priority-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .progress-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border: 1px solid #e9ecef;
    }
    
    .progress-bar-striped {
        background-image: linear-gradient(45deg, rgba(255,255,255,.15) 25%, transparent 25%, transparent 50%, rgba(255,255,255,.15) 50%, rgba(255,255,255,.15) 75%, transparent 75%, transparent);
        background-size: 1rem 1rem;
    }
    
    .description-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border: 1px solid #e9ecef;
    }
    
    .timeline-info {
        background: #ffffff;
        padding: 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid #e9ecef;
    }
    
    .location-section address {
        font-style: normal;
        line-height: 1.8;
    }
    
    .detail-group {
        transition: transform 0.2s;
    }
    
    .detail-group:hover {
        transform: translateX(5px);
    }
    
    /* Professional Expenses Tab Styling */
    .expense-stat-card {
        transition: transform 0.3s, box-shadow 0.3s;
        border-left: 4px solid transparent;
    }
    
    .expense-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }
    
    .expense-stat-card:nth-child(1) .card { border-left-color: #0d6efd; }
    .expense-stat-card:nth-child(2) .card { border-left-color: #198754; }
    .expense-stat-card:nth-child(3) .card { border-left-color: #ffc107; }
    .expense-stat-card:nth-child(4) .card { border-left-color: #0dcaf0; }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .expense-filters {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }
    
    .expense-filter-select {
        border: 1px solid #dee2e6;
        transition: border-color 0.3s, box-shadow 0.3s;
    }
    
    .expense-filter-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }
    
    .expense-table thead {
        background: linear-gradient(180deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .expense-table thead th {
        font-weight: 600;
        color: #495057;
        padding: 1rem;
        white-space: nowrap;
    }
    
    .expense-row {
        transition: background-color 0.2s, transform 0.2s;
    }
    
    .expense-row:hover {
        background-color: rgba(0, 123, 255, 0.05);
        transform: scale(1.01);
    }
    
    .expense-row td {
        padding: 1rem;
        vertical-align: middle;
    }
    
    .expense-icon-wrapper {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .category-badge {
        font-weight: 500;
        padding: 0.5rem 0.75rem;
        border: 1px solid #dee2e6;
    }
    
    .amount-display {
        font-family: 'Monaco', 'Courier New', monospace;
    }
    
    .vendor-info {
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    .empty-icon-wrapper {
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    
    .dropdown-item i {
        width: 20px;
    }
    
    /* Professional Variations Tab Styling */
    .variation-stat-card {
        transition: transform 0.3s, box-shadow 0.3s;
        border-top: 3px solid transparent;
    }
    
    .variation-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }
    
    .variation-stat-card:nth-child(1) .card { border-top-color: #0d6efd; }
    .variation-stat-card:nth-child(2) .card { border-top-color: #ffc107; }
    .variation-stat-card:nth-child(3) .card { border-top-color: #198754; }
    .variation-stat-card:nth-child(4) .card { border-top-color: #0dcaf0; }
    .variation-stat-card:nth-child(5) .card { border-top-color: #dc3545; }
    .variation-stat-card:nth-child(6) .card { border-top-color: #6f42c1; }
    
    .variation-filters {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }
    
    .variation-filter-select {
        border: 1px solid #dee2e6;
        transition: border-color 0.3s, box-shadow 0.3s;
    }
    
    .variation-filter-select:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.15);
    }
    
    .variation-table thead {
        background: linear-gradient(180deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .variation-table thead th {
        font-weight: 600;
        color: #495057;
        padding: 1rem;
        white-space: nowrap;
    }
    
    .variation-row {
        transition: background-color 0.2s, transform 0.2s;
    }
    
    .variation-row:hover {
        background-color: rgba(255, 193, 7, 0.05);
        transform: scale(1.005);
    }
    
    .variation-row td {
        padding: 1rem;
        vertical-align: middle;
    }
    
    .variation-number .badge {
        font-size: 0.9rem;
        font-weight: 600;
    }
    
    .cost-impact {
        font-family: 'Monaco', 'Courier New', monospace;
    }
    
    .avatar-sm {
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .bg-purple {
        background-color: #6f42c1 !important;
    }
    
    .text-purple {
        color: #6f42c1 !important;
    }
</style>
@endsection

@section('scripts')
<script>
// Document preview function
function previewDocument(documentId, title, viewUrl) {
    // Create modal for document preview
    const modalHtml = `
        <div class="modal fade" id="documentPreviewModal" tabindex="-1" aria-labelledby="documentPreviewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="documentPreviewModalLabel">
                            <i class="bi bi-file-earmark me-2"></i>${title}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div id="documentPreviewContent">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted">Loading document preview...</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="${viewUrl.replace('/view', '/download')}" class="btn btn-primary" target="_blank">
                            <i class="bi bi-download me-2"></i>Download
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove existing modal if present
    const existingModal = document.getElementById('documentPreviewModal');
    if (existingModal) {
        existingModal.remove();
    }

    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('documentPreviewModal'));
    modal.show();

    // Load preview content
    loadDocumentPreview(viewUrl, title);
}

function loadDocumentPreview(viewUrl, title) {
    const previewContent = document.getElementById('documentPreviewContent');
    
    // Debug: log the title and extension
    console.log('Document title:', title);
    
    // Determine file type from title - handle cases where title might not have extension
    let fileExtension = '';
    if (title && title.includes('.')) {
        fileExtension = title.split('.').pop().toLowerCase();
    }
    
    console.log('File extension:', fileExtension);
    
    const imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    const pdfTypes = ['pdf'];
    const documentTypes = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
    const textTypes = ['txt', 'csv'];
    
    if (imageTypes.includes(fileExtension)) {
        // Show image preview
        console.log('Loading image preview');
        previewContent.innerHTML = `
            <img src="${viewUrl}" class="img-fluid rounded" alt="${title}" style="max-height: 500px;" 
                 onload="console.log('Image loaded successfully')" 
                 onerror="console.log('Image failed to load'); showPreviewError('Unable to load image preview')">
        `;
    } else if (pdfTypes.includes(fileExtension)) {
        // Show PDF preview
        console.log('Loading PDF preview');
        previewContent.innerHTML = `
            <embed src="${viewUrl}" type="application/pdf" width="100%" height="500px" class="rounded">
        `;
    } else if (documentTypes.includes(fileExtension) || textTypes.includes(fileExtension)) {
        // Show iframe for office documents and text files
        console.log('Loading document preview via iframe');
        previewContent.innerHTML = `
            <iframe src="${viewUrl}" width="100%" height="500px" class="rounded border" 
                    onload="console.log('Document loaded in iframe')" 
                    onerror="console.log('Document failed to load in iframe'); showPreviewError('Unable to preview this document type')">
            </iframe>
        `;
    } else {
        // For unknown types, try to load as image first, then fallback
        console.log('Unknown file type, attempting universal preview');
        previewContent.innerHTML = `
            <div class="text-center">
                <div class="mb-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Trying to load preview...</span>
                    </div>
                </div>
                <p class="text-muted">Attempting to preview...</p>
            </div>
        `;
        
        // Try to load as image first
        const testImg = new Image();
        testImg.onload = function() {
            console.log('File loaded as image successfully');
            previewContent.innerHTML = `
                <img src="${viewUrl}" class="img-fluid rounded" alt="${title}" style="max-height: 500px;">
            `;
        };
        testImg.onerror = function() {
            console.log('File is not an image, trying iframe');
            // Try iframe
            previewContent.innerHTML = `
                <iframe src="${viewUrl}" width="100%" height="500px" class="rounded border" 
                        onload="console.log('Content loaded in iframe')" 
                        onerror="showFinalFallback('${title}', '${fileExtension}')">
                </iframe>
            `;
        };
        testImg.src = viewUrl;
    }
}

function showFinalFallback(title, fileExtension) {
    const previewContent = document.getElementById('documentPreviewContent');
    previewContent.innerHTML = `
        <div class="text-center py-4">
            <i class="bi bi-file-earmark display-1 text-muted mb-3"></i>
            <h6>${title}</h6>
            <p class="text-muted">Preview not available for this file type.</p>
            <p class="small text-muted">Detected extension: "${fileExtension}"</p>
            <p class="small text-muted">Supported preview formats: Images (JPG, PNG, GIF, WebP, SVG), PDFs, Office documents</p>
            <div class="mt-3">
                <button class="btn btn-outline-primary btn-sm" onclick="window.open('${document.getElementById('documentPreviewModal').querySelector('.modal-footer a').href}', '_blank')">
                    <i class="bi bi-download me-2"></i>Download to view
                </button>
            </div>
        </div>
    `;
}

function showPreviewError(message) {
    const previewContent = document.getElementById('documentPreviewContent');
    previewContent.innerHTML = `
        <div class="text-center py-4">
            <i class="bi bi-exclamation-triangle display-1 text-warning mb-3"></i>
            <h6>Preview Error</h6>
            <p class="text-muted">${message}</p>
        </div>
    `;
}

// Edit document function (placeholder)
function editDocument(documentId) {
    // This function can be implemented later for editing document details
    alert('Edit document functionality will be implemented soon.');
}
</script>

<!-- Task Slide-in Modal -->
<div class="task-slide-modal" id="taskSlideModal">
    <div class="task-slide-overlay" onclick="closeTaskModal()"></div>
    <div class="task-slide-panel">
        <div class="task-slide-header">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="task-icon me-3">
                        <i class="bi bi-check-square fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="taskModalTitle">Loading Task...</h5>
                        <div class="task-meta mt-1" id="taskModalMeta" style="opacity: 0.8; font-size: 0.85rem;"></div>
                    </div>
                </div>
                <button type="button" class="btn-close" onclick="closeTaskModal()" aria-label="Close"></button>
            </div>
        </div>
        <div class="task-slide-body" id="taskModalContent">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Loading task details...</p>
            </div>
        </div>
    </div>
    
    <!-- Floating Action Button for Quick Edit -->
    <button class="task-fab" onclick="editCurrentTask()" title="Quick Edit">
        <i class="bi bi-pencil"></i>
    </button>
</div>

<style>
/* Task Slide-in Modal Styles */
.task-slide-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1055;
    display: none;
}

.task-slide-modal.show {
    display: block;
}

.task-slide-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.task-slide-modal.show .task-slide-overlay {
    opacity: 1;
}

.task-slide-panel {
    position: absolute;
    top: 0;
    right: -70vw;
    width: 70vw;
    max-width: 70vw;
    height: 100%;
    background: white;
    box-shadow: -2px 0 15px rgba(0, 0, 0, 0.15);
    transition: right 0.3s ease;
    display: flex;
    flex-direction: column;
}

.task-slide-modal.show .task-slide-panel {
    right: 0;
}

.task-slide-header {
    padding: 2rem;
    border-bottom: 1px solid #e5e7eb;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
}

.task-slide-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    z-index: -1;
}

.task-slide-header .modal-title {
    font-weight: 600;
    font-size: 1.25rem;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.task-slide-header .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
    opacity: 0.8;
    transition: opacity 0.2s ease;
}

.task-slide-header .btn-close:hover {
    opacity: 1;
    transform: scale(1.1);
}

.task-slide-body {
    flex: 1;
    padding: 0;
    overflow-y: auto;
    background-color: #f8f9fa;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .task-slide-panel {
        width: 100vw;
        right: -100vw;
    }
}

@media (min-width: 769px) and (max-width: 992px) {
    .task-slide-panel {
        width: 85vw;
        right: -85vw;
    }
}

/* Animation for smooth entrance */
.task-slide-modal {
    animation: slideModalIn 0.3s ease-out;
}

@keyframes slideModalIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Task link styling */
.task-modal-trigger {
    color: #0d6efd !important;
    cursor: pointer;
    transition: color 0.2s ease;
}

.task-modal-trigger:hover {
    color: #0b5ed7 !important;
    text-decoration: underline !important;
}

/* Enhanced Task Modal Content Styling */
.task-slide-body .row {
    margin: 0;
    padding: 2rem;
}

.task-slide-body .card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 1.5rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.task-slide-body .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
}

.task-slide-body .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
    border-radius: 12px 12px 0 0 !important;
    padding: 1.25rem 1.5rem;
}

.task-slide-body .card-title {
    color: #495057;
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 0;
}

.task-slide-body .card-body {
    padding: 1.5rem;
    background: white;
}

/* Enhanced badges and status indicators */
.task-slide-body .badge {
    font-size: 0.75rem;
    font-weight: 500;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Enhanced progress bars */
.task-slide-body .progress {
    height: 8px;
    border-radius: 10px;
    background-color: #e9ecef;
    overflow: hidden;
}

.task-slide-body .progress-bar {
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    transition: width 0.6s ease;
}

/* Enhanced buttons */
.task-slide-body .btn {
    border-radius: 8px;
    font-weight: 500;
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
    border: none;
    position: relative;
    overflow: hidden;
}

.task-slide-body .btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.task-slide-body .btn:hover::before {
    left: 100%;
}

.task-slide-body .btn-outline-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    border: none;
}

.task-slide-body .btn-outline-success:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.task-slide-body .btn-outline-warning {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: white;
    border: none;
}

.task-slide-body .btn-outline-warning:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
}

.task-slide-body .btn-outline-info {
    background: linear-gradient(135deg, #17a2b8, #6f42c1);
    color: white;
    border: none;
}

.task-slide-body .btn-outline-info:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3);
}

.task-slide-body .btn-outline-primary {
    background: linear-gradient(135deg, #007bff, #6610f2);
    color: white;
    border: none;
}

.task-slide-body .btn-outline-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}

/* Enhanced form elements */
.task-slide-body .row.mb-3 {
    padding: 1rem 0;
    border-bottom: 1px solid #f1f3f4;
    margin-bottom: 1rem !important;
}

.task-slide-body .row.mb-3:last-child {
    border-bottom: none;
}

.task-slide-body .row.mb-3 strong {
    color: #495057;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Time tracking badges */
.task-slide-body .badge.bg-info,
.task-slide-body .badge.bg-success {
    background: linear-gradient(135deg, #17a2b8, #28a745) !important;
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.75rem;
}

/* Enhanced text styling */
.task-slide-body .text-muted {
    color: #6c757d !important;
    font-size: 0.9rem;
}

/* Loading spinner enhancement */
.task-slide-body .spinner-border {
    width: 3rem;
    height: 3rem;
    border-width: 0.3em;
    border-color: #667eea;
    border-right-color: transparent;
}

/* Alert enhancements */
.task-slide-body .alert {
    border: none;
    border-radius: 10px;
    font-weight: 500;
    margin-bottom: 1.5rem;
}

.task-slide-body .alert-success {
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    color: #155724;
}

.task-slide-body .alert-danger {
    background: linear-gradient(135deg, #f8d7da, #f5c6cb);
    color: #721c24;
}

.task-slide-body .alert-warning {
    background: linear-gradient(135deg, #fff3cd, #ffeaa7);
    color: #856404;
}

/* Floating Action Button */
.task-fab {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    border: none;
    color: white;
    font-size: 1.5rem;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 1060;
    display: none;
}

.task-slide-modal.show .task-fab {
    display: flex;
    align-items: center;
    justify-content: center;
}

.task-fab:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(102, 126, 234, 0.6);
}

/* Task icon animations */
.task-icon {
    transition: transform 0.3s ease;
}

.task-slide-modal.show .task-icon {
    animation: taskIconBounce 0.6s ease-out 0.3s both;
}

@keyframes taskIconBounce {
    0% { transform: scale(0.3); }
    50% { transform: scale(1.05); }
    70% { transform: scale(0.9); }
    100% { transform: scale(1); }
}

/* Smooth content reveal */
.task-slide-body .card {
    opacity: 0;
    transform: translateY(20px);
    animation: cardReveal 0.5s ease-out forwards;
}

.task-slide-body .card:nth-child(1) { animation-delay: 0.1s; }
.task-slide-body .card:nth-child(2) { animation-delay: 0.2s; }
.task-slide-body .card:nth-child(3) { animation-delay: 0.3s; }
.task-slide-body .card:nth-child(4) { animation-delay: 0.4s; }

@keyframes cardReveal {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Enhanced Task Modal Layout */
.task-modal-content {
    background: #f8f9fa;
    min-height: 100vh;
}

.task-details-card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    background: white;
}

.quick-actions-card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    background: white;
}

.quick-actions-card .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
    border-radius: 16px 16px 0 0 !important;
}

/* Info items styling */
.info-item {
    padding: 1rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.info-value {
    font-size: 0.95rem;
    color: #495057;
    font-weight: 500;
}

.description-content {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid #667eea;
    font-size: 0.9rem;
    line-height: 1.6;
}

/* Enhanced badges in modal */
.task-modal-content .badge {
    font-size: 0.75rem;
    font-weight: 500;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Progress bar in modal */
.task-modal-content .progress {
    height: 12px;
    border-radius: 10px;
    background-color: #e9ecef;
    overflow: hidden;
}

.task-modal-content .progress-bar {
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    transition: width 0.6s ease;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

/* Quick actions buttons */
.quick-actions-card .btn {
    border-radius: 10px;
    font-weight: 500;
    padding: 0.6rem 1rem;
    transition: all 0.2s ease;
    text-align: left;
    border: 1px solid #dee2e6;
}

.quick-actions-card .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Responsive adjustments for modal content */
@media (max-width: 992px) {
    .task-modal-content .col-lg-4 {
        margin-top: 1rem;
    }
    
    .info-item {
        padding: 0.75rem 0;
    }
    
    .task-modal-content .row.g-4 {
        padding: 1.5rem !important;
    }
}
</style>

<script>
// Task Modal Functions
function openTaskModal(taskId) {
    // Store current task ID for floating action button
    currentTaskId = taskId;
    
    const modal = document.getElementById('taskSlideModal');
    const title = document.getElementById('taskModalTitle');
    const content = document.getElementById('taskModalContent');
    
    // Show modal with loading state
    modal.classList.add('show');
    title.textContent = 'Loading Task...';
    content.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Loading task details...</p>
        </div>
    `;
    
    // Prevent body scrolling
    document.body.style.overflow = 'hidden';
    
    // Load task content via AJAX
    fetch(`/tasks/${taskId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html',
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load task');
            }
            return response.text();
        })
        .then(html => {
            // Since we're now returning modal content directly, just set it
            content.innerHTML = html;
            
            // Update the modal title from the content
            const modalTitle = content.querySelector('.task-modal-title');
            if (modalTitle) {
                title.textContent = modalTitle.textContent;
            } else {
                title.textContent = 'Task Details';
            }
            
            // Re-initialize any Bootstrap components in the loaded content
            if (typeof bootstrap !== 'undefined') {
                // Initialize tooltips
                const tooltipTriggerList = content.querySelectorAll('[data-bs-toggle="tooltip"]');
                tooltipTriggerList.forEach(tooltipTriggerEl => {
                    new bootstrap.Tooltip(tooltipTriggerEl);
                });
                
                // Initialize dropdowns
                const dropdownElementList = content.querySelectorAll('.dropdown-toggle');
                dropdownElementList.forEach(dropdownToggleEl => {
                    new bootstrap.Dropdown(dropdownToggleEl);
                });
                
                // Initialize progress bars
                const progressBars = content.querySelectorAll('.progress-bar');
                progressBars.forEach(bar => {
                    const width = bar.style.width;
                    bar.style.width = '0%';
                    setTimeout(() => {
                        bar.style.width = width;
                    }, 100);
                });
            }
            
            // Handle status update buttons within the modal
            const statusButtons = content.querySelectorAll('button[onclick*="updateStatus"]');
            statusButtons.forEach(button => {
                const onclick = button.getAttribute('onclick');
                if (onclick) {
                    const status = onclick.match(/'([^']+)'/)?.[1];
                    if (status) {
                        button.setAttribute('onclick', `updateTaskStatusInModal(${taskId}, '${status}')`);
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error loading task:', error);
            title.textContent = 'Error Loading Task';
            content.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-exclamation-triangle display-4 text-danger"></i>
                    <h6 class="mt-3">Failed to Load Task</h6>
                    <p class="text-muted">There was an error loading the task details.</p>
                    <button class="btn btn-outline-primary" onclick="openTaskModal(${taskId})">
                        <i class="bi bi-arrow-clockwise me-2"></i>Try Again
                    </button>
                </div>
            `;
        });
}

function closeTaskModal() {
    const modal = document.getElementById('taskSlideModal');
    modal.classList.remove('show');
    
    // Restore body scrolling
    document.body.style.overflow = '';
    
    // Clear content after animation completes
    setTimeout(() => {
        if (!modal.classList.contains('show')) {
            document.getElementById('taskModalContent').innerHTML = '';
            document.getElementById('taskModalTitle').textContent = 'Loading Task...';
        }
    }, 300);
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && document.getElementById('taskSlideModal').classList.contains('show')) {
        closeTaskModal();
    }
});

// Function to update task status within the modal
function updateTaskStatusInModal(taskId, status) {
    // Show loading state
    const statusButtons = document.querySelectorAll('button[onclick*="updateTaskStatusInModal"]');
    statusButtons.forEach(btn => {
        btn.disabled = true;
        if (btn.getAttribute('onclick').includes(`'${status}'`)) {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
        }
    });
    
    // Send AJAX request to update status
    fetch(`/tasks/${taskId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the modal content to show updated status
            openTaskModal(taskId);
            
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="bi bi-check-circle me-2"></i>Task status updated successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const modalContent = document.getElementById('taskModalContent');
            modalContent.insertBefore(alertDiv, modalContent.firstChild);
            
            // Auto-dismiss after 3 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 3000);
        } else {
            throw new Error(data.message || 'Failed to update task status');
        }
    })
    .catch(error => {
        console.error('Error updating task status:', error);
        
        // Re-enable buttons
        statusButtons.forEach(btn => {
            btn.disabled = false;
            // Restore original button text
            const status = btn.getAttribute('onclick').match(/'([^']+)'/)?.[1];
            if (status) {
                const statusText = status.replace('_', ' ').split(' ').map(word => 
                    word.charAt(0).toUpperCase() + word.slice(1)
                ).join(' ');
                
                const icons = {
                    'completed': 'check-circle',
                    'in_progress': 'play-circle',
                    'review': 'eye-fill',
                    'pending': 'clock',
                    'cancelled': 'x-circle'
                };
                
                btn.innerHTML = `<i class="bi bi-${icons[status] || 'circle'}"></i> Mark ${statusText}`;
            }
        });
        
        // Show error message
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = `
            <i class="bi bi-exclamation-triangle me-2"></i>Failed to update task status. Please try again.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const modalContent = document.getElementById('taskModalContent');
        modalContent.insertBefore(alertDiv, modalContent.firstChild);
    });
}

// Global variable to store current task ID
let currentTaskId = null;

// Simplified layout creation function
function createSimpleTaskLayout(doc) {
    const container = document.createElement('div');
    container.className = 'task-modal-content';
    
    const row = document.createElement('div');
    row.className = 'row g-4 p-4';
    
    // Main content column
    const mainCol = document.createElement('div');
    mainCol.className = 'col-lg-8';
    
    const mainCard = document.createElement('div');
    mainCard.className = 'card task-details-card';
    
    const cardBody = document.createElement('div');
    cardBody.className = 'card-body p-4';
    
    // Extract and organize task information
    const taskRows = doc.querySelectorAll('.row.mb-3');
    const infoGrid = document.createElement('div');
    infoGrid.className = 'row g-3';
    
    // Add project information first if available
    const projectLink = doc.querySelector('a[href*="projects.show"]');
    let projectName = projectLink ? projectLink.textContent.trim() : '';
    
    // Fallback: try to get project name from current page context
    if (!projectName) {
        const currentProjectName = document.querySelector('.page-title')?.textContent?.trim();
        if (currentProjectName) {
            projectName = currentProjectName;
        }
    }
    
    if (projectName) {
        const col = document.createElement('div');
        col.className = 'col-md-6';
        
        const infoItem = document.createElement('div');
        infoItem.className = 'info-item';
        
        const infoLabel = document.createElement('label');
        infoLabel.className = 'info-label';
        infoLabel.textContent = 'PROJECT';
        
        const infoValue = document.createElement('div');
        infoValue.className = 'info-value';
        infoValue.innerHTML = `<span class="fw-medium text-primary">${projectName}</span>`;
        
        infoItem.appendChild(infoLabel);
        infoItem.appendChild(infoValue);
        col.appendChild(infoItem);
        infoGrid.appendChild(col);
    }
    
    taskRows.forEach((taskRow, index) => {
        if (index > 8) return; // Limit to prevent too many items
        
        const label = taskRow.querySelector('strong');
        const value = taskRow.querySelector('.col-sm-9');
        
        if (label && value) {
            const labelText = label.textContent.trim();
            
            // Skip project row since we handle it separately
            if (labelText.toLowerCase().includes('project')) {
                return;
            }
            
            const col = document.createElement('div');
            col.className = (labelText.toLowerCase().includes('description') || 
                           labelText.toLowerCase().includes('progress') || 
                           labelText.toLowerCase().includes('time')) ? 'col-12' : 'col-md-6';
            
            const infoItem = document.createElement('div');
            infoItem.className = 'info-item';
            
            const infoLabel = document.createElement('label');
            infoLabel.className = 'info-label';
            infoLabel.textContent = labelText;
            
            const infoValue = document.createElement('div');
            infoValue.className = 'info-value';
            
            // Special handling for description
            if (labelText.toLowerCase().includes('description')) {
                infoValue.className = 'info-value description-content';
            }
            
            infoValue.innerHTML = value.innerHTML;
            
            infoItem.appendChild(infoLabel);
            infoItem.appendChild(infoValue);
            col.appendChild(infoItem);
            infoGrid.appendChild(col);
        }
    });
    
    cardBody.appendChild(infoGrid);
    mainCard.appendChild(cardBody);
    mainCol.appendChild(mainCard);
    
    // Quick actions column
    const actionsCol = document.createElement('div');
    actionsCol.className = 'col-lg-4';
    
    const actionsCard = document.createElement('div');
    actionsCard.className = 'card quick-actions-card';
    
    const actionsHeader = document.createElement('div');
    actionsHeader.className = 'card-header';
    actionsHeader.innerHTML = '<h6 class="card-title mb-0"><i class="bi bi-lightning-charge me-2"></i>Quick Actions</h6>';
    
    const actionsBody = document.createElement('div');
    actionsBody.className = 'card-body p-3';
    
    const buttonGrid = document.createElement('div');
    buttonGrid.className = 'd-grid gap-2';
    
    // Extract action buttons
    const actionButtons = doc.querySelectorAll('button[onclick*="updateStatus"]');
    actionButtons.forEach(btn => {
        const newBtn = btn.cloneNode(true);
        newBtn.className = 'btn btn-outline-primary btn-sm';
        
        // Update onclick to use modal function
        const onclick = btn.getAttribute('onclick');
        const status = onclick.match(/'([^']+)'/)?.[1];
        if (status) {
            newBtn.setAttribute('onclick', `updateTaskStatusInModal(${currentTaskId}, '${status}')`);
        }
        
        buttonGrid.appendChild(newBtn);
    });
    
    actionsBody.appendChild(buttonGrid);
    actionsCard.appendChild(actionsHeader);
    actionsCard.appendChild(actionsBody);
    actionsCol.appendChild(actionsCard);
    
    row.appendChild(mainCol);
    row.appendChild(actionsCol);
    container.appendChild(row);
    
    return container.outerHTML;
}



// Function to handle floating action button
function editCurrentTask() {
    if (currentTaskId) {
        // Close modal and navigate to edit page
        closeTaskModal();
        setTimeout(() => {
            window.location.href = `/tasks/${currentTaskId}/edit?from_project=1`;
        }, 300);
    }
}

// Delay and Hold Functions
function showDelayModal(taskId) {
    const html = `
        <div class="modal fade" id="delayModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Apply Delay to Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="delayForm">
                            <div class="mb-3">
                                <label class="form-label">Delay Days <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="delayDays" min="1" max="365" required>
                                <small class="text-muted">Number of days to delay the task</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Delay Reason <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="delayReason" rows="3" required placeholder="Explain why this task needs to be delayed..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-warning" onclick="applyDelay(${taskId})">Apply Delay</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', html);
    const modal = new bootstrap.Modal(document.getElementById('delayModal'));
    modal.show();
    
    document.getElementById('delayModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

function showOnHoldModal(taskId) {
    const html = `
        <div class="modal fade" id="onHoldModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Put Task On Hold</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="onHoldForm">
                            <div class="mb-3">
                                <label class="form-label">Hold Reason <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="onHoldReason" rows="3" required placeholder="Explain why this task needs to be put on hold..."></textarea>
                            </div>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Note:</strong> Putting a task on hold will pause all work until the hold is removed.
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" onclick="applyOnHold(${taskId})">Put On Hold</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', html);
    const modal = new bootstrap.Modal(document.getElementById('onHoldModal'));
    modal.show();
    
    document.getElementById('onHoldModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

function applyDelay(taskId) {
    const delayDays = document.getElementById('delayDays').value;
    const delayReason = document.getElementById('delayReason').value;
    
    if (!delayDays || !delayReason) {
        alert('Please fill in all required fields.');
        return;
    }
    
    fetch(`/tasks/${taskId}/apply-delay`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            delay_days: parseInt(delayDays),
            delay_reason: delayReason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('delayModal')).hide();
            showSuccessMessage(data.message);
            // Refresh the modal content
            openTaskModal(taskId);
            // Refresh the page to show updated task list
            setTimeout(() => location.reload(), 1500);
        } else {
            alert('Error applying delay: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error applying delay. Please try again.');
    });
}

function applyOnHold(taskId) {
    const onHoldReason = document.getElementById('onHoldReason').value;
    
    if (!onHoldReason) {
        alert('Please provide a reason for putting the task on hold.');
        return;
    }
    
    fetch(`/tasks/${taskId}/apply-on-hold`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            on_hold_reason: onHoldReason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('onHoldModal')).hide();
            showSuccessMessage(data.message);
            // Refresh the modal content
            openTaskModal(taskId);
            // Refresh the page to show updated task list
            setTimeout(() => location.reload(), 1500);
        } else {
            alert('Error putting task on hold: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error putting task on hold. Please try again.');
    });
}

function removeDelay(taskId) {
    if (!confirm('Are you sure you want to remove the delay from this task?')) {
        return;
    }
    
    fetch(`/tasks/${taskId}/remove-delay`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessMessage(data.message);
            // Refresh the modal content
            openTaskModal(taskId);
            // Refresh the page to show updated task list
            setTimeout(() => location.reload(), 1500);
        } else {
            alert('Error removing delay: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error removing delay. Please try again.');
    });
}

function removeOnHold(taskId) {
    if (!confirm('Are you sure you want to remove the hold from this task?')) {
        return;
    }
    
    fetch(`/tasks/${taskId}/remove-on-hold`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessMessage(data.message);
            // Refresh the modal content
            openTaskModal(taskId);
            // Refresh the page to show updated task list
            setTimeout(() => location.reload(), 1500);
        } else {
            alert('Error removing hold: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error removing hold. Please try again.');
    });
}

function showSuccessMessage(message) {
    const alertHtml = `
        <div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="bi bi-check-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert-success');
        if (alert) {
            bootstrap.Alert.getOrCreateInstance(alert).close();
        }
    }, 5000);
}
</script>

<style>
/* Task styling for delayed/on hold tasks */
.task-delayed-or-on-hold {
    background-color: rgba(220, 53, 69, 0.05) !important;
    border-left: 4px solid #dc3545 !important;
}

.task-delayed-or-on-hold:hover {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.task-completed {
    opacity: 0.7;
    background-color: rgba(25, 135, 84, 0.05);
}

.task-completed .task-title {
    text-decoration: line-through !important;
}

/* Enhanced badge styling */
.badge {
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.5px;
}

/* Modal enhancements */
.modal-content {
    border-radius: 8px;
    border: none;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.modal-header {
    border-bottom: 1px solid #e9ecef;
    background-color: #f8f9fa;
}

.form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>

@endsection