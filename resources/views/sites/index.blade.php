@extends('layouts.app')

@section('title', 'Sites')

@section('content')
<div class="sites-container">
    <!-- Professional Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center">
                    <div class="header-icon bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                        <i class="bi bi-building fs-3"></i>
                    </div>
                    <div>
                        <h1 class="page-title mb-1 fw-bold">
                            Construction Sites
                            @if(request('archived') == '1')
                                <span class="badge bg-secondary ms-2">
                                    <i class="bi bi-archive me-1"></i>Archived
                                </span>
                            @endif
                        </h1>
                        <p class="page-subtitle text-muted mb-0">
                            @if(request('archived') == '1')
                                View and manage your archived construction sites
                            @else
                                Manage and monitor all your construction sites and their projects
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-end">
                @if(auth()->user()->canManageProjects())
                    @if(request('archived') != '1')
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-archive me-1"></i>Archive Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <button class="dropdown-item" onclick="archiveCompletedSites()">
                                        <i class="bi bi-archive me-2"></i>Archive All Completed Sites
                                    </button>
                                </li>
                            </ul>
                        </div>
                    @endif
                    <a href="{{ route('sites.create') }}" class="btn btn-primary btn-lg shadow-sm">
                        <i class="bi bi-plus-circle me-2"></i>Create New Site
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    @php
        $totalSites = $sites->total();
        $activeSites = \App\Models\Site::forCompany(auth()->user()->company_id)->where('status', 'active')->where('is_active', true)->count();
        $completedSites = \App\Models\Site::forCompany(auth()->user()->company_id)->where('status', 'completed')->where('is_active', true)->count();
        $archivedSites = \App\Models\Site::forCompany(auth()->user()->company_id)->where('is_active', false)->count();
        $totalBudget = \App\Models\Site::forCompany(auth()->user()->company_id)->where('is_active', true)->sum('total_budget');
    @endphp
    
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                            <i class="bi bi-geo-alt-fill fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Total Sites</p>
                            <h3 class="mb-0 fw-bold">{{ $totalSites }}</h3>
                            <small class="text-success">
                                <i class="bi bi-arrow-up-short"></i>All locations
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                            <i class="bi bi-activity fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Active Sites</p>
                            <h3 class="mb-0 fw-bold text-success">{{ $activeSites }}</h3>
                            <small class="text-muted">Currently operating</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-secondary bg-opacity-10 text-secondary rounded-circle p-3 me-3">
                            <i class="bi bi-archive fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Archived</p>
                            <h3 class="mb-0 fw-bold text-secondary">{{ $archivedSites }}</h3>
                            <small class="text-muted">
                                @if($archivedSites > 0)
                                    <a href="{{ route('sites.index', ['archived' => '1']) }}" class="text-decoration-none">
                                        View archived
                                    </a>
                                @else
                                    No archived sites
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-circle p-3 me-3">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Total Budget</p>
                            <h3 class="mb-0 fw-bold text-warning">{{ auth()->user()->company->formatCurrency($totalBudget) }}</h3>
                            <small class="text-muted">Combined value</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light border-0 py-3">
            <div class="d-flex align-items-center">
                <i class="bi bi-funnel text-primary me-2"></i>
                <h5 class="mb-0 fw-semibold">Filter Sites</h5>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('sites.index') }}" class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label for="search" class="form-label small text-muted">
                        <i class="bi bi-search me-1"></i>Search Sites
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Site name or address...">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="status" class="form-label small text-muted">
                        <i class="bi bi-flag me-1"></i>Status
                    </label>
                    <select class="form-select filter-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="planning" {{ request('status') == 'planning' ? 'selected' : '' }}>📋 Planning</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>🚧 Active</option>
                        <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>⏸️ On Hold</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>✅ Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="client_id" class="form-label small text-muted">
                        <i class="bi bi-person-badge me-1"></i>Client
                    </label>
                    <select class="form-select filter-select" id="client_id" name="client_id">
                        <option value="">All Clients</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="priority" class="form-label small text-muted">
                        <i class="bi bi-exclamation-triangle me-1"></i>Priority
                    </label>
                    <select class="form-select filter-select" id="priority" name="priority">
                        <option value="">All Priorities</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>🟢 Low</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>🟠 High</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>🔴 Urgent</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="archived" class="form-label small text-muted">
                        <i class="bi bi-archive me-1"></i>View
                    </label>
                    <select class="form-select filter-select" id="archived" name="archived">
                        <option value="">Active Sites</option>
                        <option value="1" {{ request('archived') == '1' ? 'selected' : '' }}>🗃️ Archived Sites</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="bi bi-funnel-fill me-1"></i>Apply
                        </button>
                        <a href="{{ route('sites.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sites Grid/Table Toggle -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-semibold">{{ $sites->total() }} {{ Str::plural('Site', $sites->total()) }} Found</h5>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-secondary active" id="tableViewBtn" onclick="switchView('table')">
                <i class="bi bi-list-ul"></i> Table
            </button>
            <button type="button" class="btn btn-outline-secondary" id="gridViewBtn" onclick="switchView('grid')">
                <i class="bi bi-grid-3x3-gap"></i> Grid
            </button>
        </div>
    </div>

    <!-- Sites Table View -->
    <div class="card border-0 shadow-sm" id="tableView">
        <div class="card-body p-0">
            @if($sites->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover sites-table mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 ps-4">
                                    <i class="bi bi-building me-2"></i>Site Details
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-person me-2"></i>Client
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-geo-alt me-2"></i>Location
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-flag me-2"></i>Status
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-cash me-2"></i>Budget
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-bar-chart me-2"></i>Progress
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-folder me-2"></i>Projects
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-calendar3 me-2"></i>Completion
                                </th>
                                <th class="border-0 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sites as $site)
                                <tr class="site-row">
                                    <td class="ps-4">
                                        <div class="site-info d-flex align-items-center">
                                            <div class="site-icon-wrapper bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                                <i class="bi bi-building text-primary fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-semibold">
                                                    <a href="{{ route('sites.show', $site) }}" class="text-decoration-none text-dark hover-primary">
                                                        {{ $site->name }}
                                                    </a>
                                                    @if(!$site->is_active)
                                                        <span class="badge bg-secondary ms-2">
                                                            <i class="bi bi-archive me-1"></i>Archived
                                                        </span>
                                                    @endif
                                                </h6>
                                                @if($site->description)
                                                    <small class="text-muted">{{ Str::limit($site->description, 60) }}</small>
                                                @endif
                                                <div class="mt-1">
                                                    <span class="badge bg-{{ $site->priority_color }} bg-opacity-75">
                                                        <i class="bi bi-flag-fill me-1"></i>{{ ucfirst($site->priority) }} Priority
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="client-info">
                                            <a href="{{ route('clients.show', $site->client) }}" class="text-decoration-none fw-semibold">
                                                {{ $site->client->display_name }}
                                            </a>
                                            @if($site->client->primary_contact)
                                                <div class="small text-muted mt-1">
                                                    <i class="bi bi-person me-1"></i>{{ $site->client->primary_contact }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="location-info">
                                            @if($site->full_address)
                                                <span class="d-inline-block" data-bs-toggle="tooltip" title="{{ $site->full_address }}">
                                                    <i class="bi bi-geo-alt text-muted me-1"></i>
                                                    {{ Str::limit($site->full_address, 35) }}
                                                </span>
                                            @else
                                                <span class="text-muted fst-italic">No address specified</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-{{ $site->status_color }} px-3 py-2">
                                            @php
                                                $statusIcons = [
                                                    'planning' => 'bi-clipboard',
                                                    'active' => 'bi-play-circle',
                                                    'on_hold' => 'bi-pause-circle',
                                                    'completed' => 'bi-check-circle',
                                                    'cancelled' => 'bi-x-circle'
                                                ];
                                                $icon = $statusIcons[$site->status] ?? 'bi-circle';
                                            @endphp
                                            <i class="bi {{ $icon }} me-1"></i>
                                            {{ ucfirst(str_replace('_', ' ', $site->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="budget-info">
                                            @if($site->total_budget)
                                                <div class="fw-bold fs-5">£{{ number_format($site->total_budget, 0) }}</div>
                                                <small class="text-muted">Total budget</small>
                                            @else
                                                <span class="text-muted fst-italic">Not set</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="progress-info">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <span class="fw-bold">{{ $site->progress }}%</span>
                                                @if($site->progress == 100)
                                                    <i class="bi bi-check-circle-fill text-success"></i>
                                                @endif
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-{{ $site->progress >= 75 ? 'success' : ($site->progress >= 50 ? 'info' : 'warning') }}" 
                                                     style="width: {{ $site->progress }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="projects-info text-center">
                                            <span class="badge bg-info rounded-pill px-3 py-2">
                                                <i class="bi bi-folder me-1"></i>{{ $site->getTotalProjectsCount() }}
                                            </span>
                                            @if($site->getActiveProjectsCount() > 0)
                                                <div class="small text-success mt-1">
                                                    {{ $site->getActiveProjectsCount() }} active
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="completion-info">
                                            @if($site->expected_completion_date)
                                                <div class="{{ $site->is_overdue ? 'text-danger fw-bold' : '' }}">
                                                    {{ $site->expected_completion_date->format('M j, Y') }}
                                                </div>
                                                @if($site->is_overdue)
                                                    <small class="text-danger">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>Overdue
                                                    </small>
                                                @else
                                                    <small class="text-muted">
                                                        {{ $site->expected_completion_date->diffForHumans() }}
                                                    </small>
                                                @endif
                                            @else
                                                <span class="text-muted fst-italic">Not set</span>
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
                                                    <a class="dropdown-item" href="{{ route('sites.show', $site) }}">
                                                        <i class="bi bi-eye me-2"></i>View Details
                                                    </a>
                                                </li>
                                                @if(auth()->user()->canManageProjects())
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('sites.edit', $site) }}">
                                                            <i class="bi bi-pencil me-2"></i>Edit Site
                                                        </a>
                                                    </li>
                                                    @if($site->is_active)
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('projects.create', ['site_id' => $site->id]) }}">
                                                                <i class="bi bi-plus-circle me-2"></i>Add Project
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li><hr class="dropdown-divider"></li>
                                                    @if($site->is_active)
                                                        <li>
                                                            <form method="POST" action="{{ route('sites.archive', $site) }}" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-warning" 
                                                                        onclick="return confirm('Are you sure you want to archive this site? It will be hidden from the main list.')">
                                                                    <i class="bi bi-archive me-2"></i>Archive Site
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <form method="POST" action="{{ route('sites.unarchive', $site) }}" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-success" 
                                                                        onclick="return confirm('Are you sure you want to unarchive this site? It will be visible in the main list again.')">
                                                                    <i class="bi bi-arrow-up-circle me-2"></i>Unarchive Site
                                                                </button>
                                                            </form>
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

                <!-- Pagination -->
                <div class="card-footer bg-white border-0 py-3">
                    <div class="d-flex justify-content-center">
                        {{ $sites->links() }}
                    </div>
                </div>
            @else
                <div class="empty-state text-center py-5">
                    <div class="empty-icon-wrapper mx-auto mb-4" style="width: 120px; height: 120px;">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center h-100">
                            <i class="bi bi-building display-1 text-muted"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-2">No Sites Found</h4>
                    <p class="text-muted mb-4">
                        {{ request()->hasAny(['search', 'status', 'client_id', 'priority']) 
                            ? 'Try adjusting your filters to find what you\'re looking for.' 
                            : 'Get started by creating your first construction site.' }}
                    </p>
                    @if(auth()->user()->canManageProjects() && !request()->hasAny(['search', 'status', 'client_id', 'priority']))
                        <a href="{{ route('sites.create') }}" class="btn btn-primary btn-lg shadow-sm">
                            <i class="bi bi-plus-circle me-2"></i>Create Your First Site
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Sites Grid View (Hidden by default) -->
    <div class="row g-3" id="gridView" style="display: none;">
        @foreach($sites as $site)
            <div class="col-lg-4 col-md-6">
                <div class="card site-card border-0 shadow-sm h-100">
                    <div class="card-header bg-{{ $site->status_color }} bg-opacity-10 border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-{{ $site->status_color }}">
                                {{ ucfirst(str_replace('_', ' ', $site->status)) }}
                            </span>
                            <span class="badge bg-{{ $site->priority_color }}">
                                {{ ucfirst($site->priority) }} Priority
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3">
                            <i class="bi bi-building text-primary me-2"></i>
                            <a href="{{ route('sites.show', $site) }}" class="text-decoration-none text-dark">
                                {{ $site->name }}
                            </a>
                        </h5>
                        
                        <div class="site-details">
                            <div class="detail-item mb-2">
                                <i class="bi bi-person text-muted me-2"></i>
                                <span class="text-muted">Client:</span>
                                <strong>{{ $site->client->display_name }}</strong>
                            </div>
                            
                            @if($site->full_address)
                                <div class="detail-item mb-2">
                                    <i class="bi bi-geo-alt text-muted me-2"></i>
                                    <small>{{ Str::limit($site->full_address, 50) }}</small>
                                </div>
                            @endif
                            
                            <div class="detail-item mb-3">
                                <i class="bi bi-folder text-muted me-2"></i>
                                <span class="text-muted">Projects:</span>
                                <strong>{{ $site->getTotalProjectsCount() }}</strong>
                                @if($site->getActiveProjectsCount() > 0)
                                    <span class="text-success">({{ $site->getActiveProjectsCount() }} active)</span>
                                @endif
                            </div>
                        </div>

                        <div class="progress-section mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Progress</small>
                                <small class="fw-bold">{{ $site->progress }}%</small>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-{{ $site->progress >= 75 ? 'success' : ($site->progress >= 50 ? 'info' : 'warning') }}" 
                                     style="width: {{ $site->progress }}%"></div>
                            </div>
                        </div>

                        @if($site->total_budget)
                            <div class="budget-section p-2 bg-light rounded mb-3">
                                <small class="text-muted">Budget:</small>
                                <strong class="text-success ms-1">£{{ number_format($site->total_budget, 0) }}</strong>
                            </div>
                        @endif

                        @if($site->expected_completion_date)
                            <div class="completion-section">
                                <small class="text-muted">Expected Completion:</small>
                                <div class="{{ $site->is_overdue ? 'text-danger fw-bold' : 'text-dark' }}">
                                    {{ $site->expected_completion_date->format('M j, Y') }}
                                    @if($site->is_overdue)
                                        <i class="bi bi-exclamation-triangle ms-1"></i>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-white border-0">
                        <div class="d-grid gap-2">
                            <a href="{{ route('sites.show', $site) }}" class="btn btn-primary">
                                <i class="bi bi-eye me-1"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
/* Professional Sites Page Styling */
.stat-card {
    transition: transform 0.3s, box-shadow 0.3s;
    border-left: 4px solid transparent;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.stat-card:nth-child(1) { border-left-color: #0d6efd; }
.stat-card:nth-child(2) { border-left-color: #198754; }
.stat-card:nth-child(3) { border-left-color: #0dcaf0; }
.stat-card:nth-child(4) { border-left-color: #ffc107; }

.stat-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.filter-select {
    border: 1px solid #dee2e6;
    transition: border-color 0.3s, box-shadow 0.3s;
}

.filter-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}

.sites-table thead {
    background: linear-gradient(180deg, #f8f9fa 0%, #e9ecef 100%);
}

.sites-table thead th {
    font-weight: 600;
    color: #495057;
    padding: 1rem;
    white-space: nowrap;
}

.site-row {
    transition: background-color 0.2s, transform 0.2s;
}

.site-row:hover {
    background-color: rgba(0, 123, 255, 0.05);
    transform: scale(1.005);
}

.site-row td {
    padding: 1rem;
    vertical-align: middle;
}

.site-icon-wrapper {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.hover-primary:hover {
    color: #0d6efd !important;
}

.progress {
    background-color: #e9ecef;
}

.progress-bar-striped {
    background-image: linear-gradient(45deg, rgba(255,255,255,.15) 25%, transparent 25%, transparent 50%, rgba(255,255,255,.15) 50%, rgba(255,255,255,.15) 75%, transparent 75%, transparent);
    background-size: 1rem 1rem;
}

/* Grid View Styling */
.site-card {
    transition: transform 0.3s, box-shadow 0.3s;
    overflow: hidden;
}

.site-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.site-card .card-title a {
    transition: color 0.3s;
}

.site-card .card-title a:hover {
    color: #0d6efd !important;
}

.detail-item {
    font-size: 0.9rem;
}

.empty-icon-wrapper {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* View Toggle Buttons */
.btn-group .btn {
    padding: 0.5rem 1rem;
}

.btn-group .btn.active {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}

/* Responsive Design */
@media (max-width: 768px) {
    .stat-card .card-body {
        padding: 1rem;
    }
    
    .site-info h6 {
        font-size: 0.95rem;
    }
    
    .table-responsive {
        font-size: 0.9rem;
    }
}

/* Tooltips */
[data-bs-toggle="tooltip"] {
    cursor: help;
}

/* Dropdown improvements */
.dropdown-item {
    padding: 0.5rem 1rem;
    transition: background-color 0.2s;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.dropdown-item i {
    width: 20px;
}
</style>

<script>
// View Toggle Function
function switchView(view) {
    const tableView = document.getElementById('tableView');
    const gridView = document.getElementById('gridView');
    const tableBtn = document.getElementById('tableViewBtn');
    const gridBtn = document.getElementById('gridViewBtn');
    
    if (view === 'grid') {
        tableView.style.display = 'none';
        gridView.style.display = 'flex';
        tableBtn.classList.remove('active');
        gridBtn.classList.add('active');
    } else {
        tableView.style.display = 'block';
        gridView.style.display = 'none';
        tableBtn.classList.add('active');
        gridBtn.classList.remove('active');
    }
    
    // Save preference
    localStorage.setItem('sitesViewPreference', view);
}

// Load view preference on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('sitesViewPreference');
    if (savedView === 'grid') {
        switchView('grid');
    }
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Archive completed sites function
function archiveCompletedSites() {
    if (confirm('Are you sure you want to archive all completed sites? This will hide them from the main list but they can be accessed via the archived sites view.')) {
        // Create a form to submit the bulk archive request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("sites.bulk-archive-completed") }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection