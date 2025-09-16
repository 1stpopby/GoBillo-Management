@extends('layouts.app')

@section('title', 'Projects')

@section('content')
<div class="projects-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="page-title">
                    Projects
                    @if(request('archived') == '1')
                        <span class="badge bg-secondary ms-2">
                            <i class="bi bi-archive me-1"></i>Archived
                        </span>
                    @endif
                </h1>
                <p class="page-subtitle">
                    @if(request('archived') == '1')
                        View and manage your archived construction projects
                    @else
                        Manage all your construction projects
                    @endif
                </p>
            </div>
            <div class="col-lg-4 text-end">
                @if(auth()->user()->canManageProjects())
                    @if(request('archived') != '1')
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-archive me-1"></i>Archive Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <button class="dropdown-item" onclick="archiveCompletedProjects()">
                                        <i class="bi bi-archive me-2"></i>Archive All Completed Projects
                                    </button>
                                </li>
                            </ul>
                        </div>
                    @endif
                    <a href="{{ route('projects.create') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle me-2"></i>New Project
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    @php
        $totalProjects = $projects->total();
        $activeProjects = \App\Models\Project::forCompany(auth()->user()->company_id)->where('status', 'in_progress')->where('is_active', true)->count();
        $completedProjects = \App\Models\Project::forCompany(auth()->user()->company_id)->where('status', 'completed')->where('is_active', true)->count();
        $archivedProjects = \App\Models\Project::forCompany(auth()->user()->company_id)->where('is_active', false)->count();
        $totalBudget = \App\Models\Project::forCompany(auth()->user()->company_id)->where('is_active', true)->sum('budget');
    @endphp
    
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                            <i class="bi bi-folder fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Total Projects</p>
                            <h3 class="mb-0 fw-bold">{{ $totalProjects }}</h3>
                            <small class="text-success">
                                <i class="bi bi-arrow-up-short"></i>All projects
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
                            <i class="bi bi-play-circle fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Active Projects</p>
                            <h3 class="mb-0 fw-bold text-success">{{ $activeProjects }}</h3>
                            <small class="text-muted">In progress</small>
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
                            <h3 class="mb-0 fw-bold text-secondary">{{ $archivedProjects }}</h3>
                            <small class="text-muted">
                                @if($archivedProjects > 0)
                                    <a href="{{ route('projects.index', ['archived' => '1']) }}" class="text-decoration-none">
                                        View archived
                                    </a>
                                @else
                                    No archived projects
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
                            <h3 class="mb-0 fw-bold text-warning">£{{ number_format($totalBudget, 0) }}</h3>
                            <small class="text-muted">Combined value</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('projects.index') }}" class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label for="search" class="form-label">Search Projects</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Project name or description...">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="planning" {{ request('status') == 'planning' ? 'selected' : '' }}>Planning</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="priority" class="form-label">Priority</label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="">All Priorities</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="site_id" class="form-label">Site</label>
                    <select class="form-select" id="site_id" name="site_id">
                        <option value="">All Sites</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>
                                {{ $site->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="archived" class="form-label">View</label>
                    <select class="form-select" id="archived" name="archived">
                        <option value="">Active Projects</option>
                        <option value="1" {{ request('archived') == '1' ? 'selected' : '' }}>🗃️ Archived Projects</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>Filter
                        </button>
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="card">
        <div class="card-body">
            @if($projects->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Project</th>
                                <th>Site</th>
                                <th>Client</th>
                                <th>Manager</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Budget</th>
                                <th>Progress</th>
                                <th>Tasks</th>
                                <th>End Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projects as $project)
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
                                                <small class="text-muted">{{ Str::limit($project->description, 50) }}</small>
                                            @endif
                                        </div>
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
                                        @php $client = $project->getEffectiveClient() @endphp
                                        @if($client)
                                            <a href="{{ route('clients.show', $client) }}" class="text-decoration-none">
                                                {{ $client->display_name }}
                                            </a>
                                        @else
                                            <span class="text-muted">No client</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($project->manager)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    {{ substr($project->manager->name, 0, 1) }}
                                                </div>
                                                {{ $project->manager->name }}
                                            </div>
                                        @else
                                            <span class="text-muted">No manager</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(auth()->user()->canManageProjects())
                                            <select class="form-select form-select-sm project-status-dropdown" 
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
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $project->priority_color }}">
                                            {{ ucfirst($project->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($project->budget)
                                            <span class="text-success fw-bold">${{ number_format($project->budget, 0) }}</span>
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar" style="width: {{ $project->progress }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $project->progress }}%</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $project->total_tasks_count }}</span>
                                        @if($project->pending_tasks_count > 0)
                                            <small class="text-muted d-block">{{ $project->pending_tasks_count }} pending</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($project->end_date)
                                            <span class="{{ $project->is_overdue ? 'text-danger fw-bold' : '' }}">
                                                {{ $project->end_date->format('M j, Y') }}
                                                @if($project->is_overdue)
                                                    <i class="bi bi-exclamation-triangle ms-1"></i>
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('projects.show', $project) }}">
                                                        <i class="bi bi-eye me-2"></i>View Details
                                                    </a>
                                                </li>
                                                @if(auth()->user()->canManageProjects())
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('projects.edit', $project) }}">
                                                            <i class="bi bi-pencil me-2"></i>Edit Project
                                                        </a>
                                                    </li>
                                                    @if($project->is_active)
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('tasks.create', ['project_id' => $project->id]) }}">
                                                                <i class="bi bi-plus-circle me-2"></i>Add Task
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li><hr class="dropdown-divider"></li>
                                                    @if($project->is_active)
                                                        <li>
                                                            <form method="POST" action="{{ route('projects.archive', $project) }}" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-warning" 
                                                                        onclick="return confirm('Are you sure you want to archive this project? It will be hidden from the main list.')">
                                                                    <i class="bi bi-archive me-2"></i>Archive Project
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <form method="POST" action="{{ route('projects.unarchive', $project) }}" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-success" 
                                                                        onclick="return confirm('Are you sure you want to unarchive this project? It will be visible in the main list again.')">
                                                                    <i class="bi bi-arrow-up-circle me-2"></i>Unarchive Project
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
                <div class="d-flex justify-content-center mt-4">
                    {{ $projects->links() }}
                </div>
            @else
                <div class="empty-state text-center py-5">
                    <i class="bi bi-folder display-1 text-muted"></i>
                    <h4 class="mt-3">No projects found</h4>
                    <p class="text-muted">{{ request()->hasAny(['search', 'status', 'priority', 'site_id']) ? 'Try adjusting your filters to find what you\'re looking for.' : 'Get started by creating your first project.' }}</p>
                    @if(auth()->user()->canManageProjects() && !request()->hasAny(['search', 'status', 'priority', 'site_id']))
                        <a href="{{ route('projects.create') }}" class="btn btn-primary btn-lg mt-3">
                            <i class="bi bi-plus-circle me-2"></i>Create Your First Project
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle project status changes with simple GET requests
    document.querySelectorAll('.project-status-dropdown').forEach(function(dropdown) {
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
});

// Archive completed projects function
function archiveCompletedProjects() {
    if (confirm('Are you sure you want to archive all completed projects? This will hide them from the main list but they can be accessed via the archived projects view.')) {
        // Create a form to submit the bulk archive request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("projects.bulk-archive-completed") }}';
        
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

<style>
/* Professional Projects Page Styling */
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
.stat-card:nth-child(3) { border-left-color: #6c757d; }
.stat-card:nth-child(4) { border-left-color: #ffc107; }

.stat-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.page-header {
    margin-bottom: 2rem;
}

.page-title {
    font-size: 2.25rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    font-size: 1rem;
    color: #718096;
    margin-bottom: 0;
}
</style>

@endsection 