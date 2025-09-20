@extends('layouts.app')

@section('title', 'Site: ' . $site->name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('manager.sites.index') }}">My Sites</a></li>
                    <li class="breadcrumb-item active">{{ $site->name }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">
                <i class="bi bi-geo-alt text-primary me-2"></i>
                {{ $site->name }}
            </h1>
            <p class="text-muted mb-0">
                <i class="bi bi-building me-1"></i>
                {{ $site->client->company_name ?? 'No Client Assigned' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('manager.sites.edit', $site) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-2"></i>Edit Site
            </a>
            <a href="{{ route('manager.sites.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Sites
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-9">
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
                        <a href="{{ route('projects.create', ['site_id' => $site->id]) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus me-1"></i>Add Project
                        </a>
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
                                                <select class="form-select form-select-sm site-project-status-dropdown" 
                                                        data-project-id="{{ $project->id }}" 
                                                        style="min-width: 140px;">
                                                    <option value="planning" {{ $project->status === 'planning' ? 'selected' : '' }}>Planning</option>
                                                    <option value="in_progress" {{ $project->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                    <option value="on_hold" {{ $project->status === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                                    <option value="completed" {{ $project->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="cancelled" {{ $project->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                </select>
                                            </td>
                                            <td>
                                                @if($project->manager)
                                                    <span class="badge bg-primary">{{ $project->manager->name }}</span>
                                                @else
                                                    <span class="text-muted">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 8px; min-width: 80px;">
                                                    <div class="progress-bar" 
                                                         style="width: {{ $project->getCompletionPercentage() }}%"
                                                         role="progressbar">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $project->getCompletionPercentage() }}%</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $project->tasks->count() }} tasks
                                                </span>
                                                @if($project->tasks->where('status', 'completed')->count() > 0)
                                                    <br><small class="text-success">{{ $project->tasks->where('status', 'completed')->count() }} completed</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($project->deadline)
                                                    <small class="text-{{ $project->deadline->isPast() ? 'danger' : 'muted' }}">
                                                        {{ $project->deadline->format('M d, Y') }}
                                                    </small>
                                                    @if($project->deadline->isPast())
                                                        <br><small class="text-danger"><i class="bi bi-exclamation-triangle"></i> Overdue</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No deadline</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('projects.edit', ['project' => $project, 'from_site' => 1]) }}" class="btn btn-outline-secondary btn-sm">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-folder-plus display-4 text-muted mb-3"></i>
                            <h6 class="text-muted mb-3">No Projects Yet</h6>
                            <p class="text-muted mb-4">Get started by creating your first project for this site.</p>
                            <a href="{{ route('projects.create', ['site_id' => $site->id]) }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Create First Project
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar with Site Stats -->
        <div class="col-lg-3">
            <!-- Site Overview -->
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-title">Site Overview</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Status:</span>
                        <span class="badge bg-{{ 
                            $site->status === 'active' ? 'success' : 
                            ($site->status === 'completed' ? 'primary' : 
                            ($site->status === 'on_hold' ? 'warning' : 
                            ($site->status === 'cancelled' ? 'danger' : 'secondary'))) 
                        }}">
                            {{ str_replace('_', ' ', ucfirst($site->status)) }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Priority:</span>
                        <span class="badge bg-{{ 
                            $site->priority === 'urgent' ? 'danger' : 
                            ($site->priority === 'high' ? 'warning' : 
                            ($site->priority === 'medium' ? 'info' : 'secondary')) 
                        }}">
                            {{ ucfirst($site->priority) }}
                        </span>
                    </div>
                    @if($site->start_date)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Start Date:</span>
                            <span>{{ $site->start_date->format('M d, Y') }}</span>
                        </div>
                    @endif
                    @if($site->expected_completion_date)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Expected Completion:</span>
                            <span class="text-{{ $site->expected_completion_date->isPast() ? 'danger' : 'muted' }}">
                                {{ $site->expected_completion_date->format('M d, Y') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Site Statistics -->
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="card-title">Statistics</h6>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-folder me-2 text-primary"></i>
                            <div>
                                <span class="small">Total Projects</span>
                            </div>
                        </div>
                        <span class="fw-bold">{{ $stats['total_projects'] }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-play-circle me-2 text-success"></i>
                            <div>
                                <span class="small">Active Projects</span>
                            </div>
                        </div>
                        <span class="fw-bold">{{ $stats['active_projects'] }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle me-2 text-info"></i>
                            <div>
                                <span class="small">Completed Projects</span>
                            </div>
                        </div>
                        <span class="fw-bold">{{ $stats['completed_projects'] }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-list-task me-2 text-warning"></i>
                            <div>
                                <span class="small">Total Tasks</span>
                            </div>
                        </div>
                        <span class="fw-bold">{{ $stats['total_tasks'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Site Managers -->
            @if($site->activeManagers->count() > 0)
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Site Managers</h6>
                        @foreach($site->activeManagers as $manager)
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-person-badge me-2 text-primary"></i>
                                <div>
                                    <div class="fw-semibold">{{ $manager->name }}</div>
                                    <small class="text-muted">{{ $manager->pivot->role === 'primary' ? 'Primary Manager' : 'Manager' }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize variables
    const statusFilter = document.getElementById('siteProjectStatusFilter');
    const managerFilter = document.getElementById('siteProjectManagerFilter');
    const searchFilter = document.getElementById('siteProjectSearchFilter');
    const archivedFilter = document.getElementById('siteProjectArchivedFilter');
    const clearFilters = document.getElementById('clearSiteProjectFilters');
    const projectsTable = document.querySelector('table tbody');
    const projectsViewIndicator = document.getElementById('projectsViewIndicator');

    // Project status update functionality
    document.querySelectorAll('.site-project-status-dropdown').forEach(function(dropdown) {
        dropdown.addEventListener('change', function() {
            const projectId = this.getAttribute('data-project-id');
            const newStatus = this.value;
            
            // Show loading state
            this.disabled = true;
            
            fetch(`/api/projects/${projectId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showStatusMessage('Project status updated successfully', 'success');
                } else {
                    // Revert the dropdown
                    this.value = data.originalStatus || 'planning';
                    showStatusMessage('Failed to update project status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showStatusMessage('Failed to update project status', 'error');
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    });

    // Filter projects
    function filterProjects() {
        const statusValue = statusFilter.value.toLowerCase();
        const managerValue = managerFilter.value;
        const searchValue = searchFilter.value.toLowerCase();
        const archivedValue = archivedFilter.value;
        
        // Update the indicator
        if (archivedValue === 'archived') {
            projectsViewIndicator.style.display = 'inline';
        } else {
            projectsViewIndicator.style.display = 'none';
        }

        if (projectsTable) {
            const rows = projectsTable.querySelectorAll('tr');
            
            rows.forEach(function(row) {
                const projectName = row.querySelector('.project-info h6 a')?.textContent.toLowerCase() || '';
                const statusSelect = row.querySelector('.site-project-status-dropdown');
                const statusText = statusSelect ? statusSelect.value.toLowerCase() : '';
                const managerBadge = row.querySelector('td:nth-child(3) .badge');
                const managerText = managerBadge ? managerBadge.textContent : '';
                const archivedBadge = row.querySelector('.badge:contains("Archived")');
                const isArchived = archivedBadge !== null;

                let showRow = true;

                // Filter by status
                if (statusValue && statusText !== statusValue) {
                    showRow = false;
                }

                // Filter by manager
                if (managerValue && !managerText.includes(managerValue)) {
                    showRow = false;
                }

                // Filter by search
                if (searchValue && !projectName.includes(searchValue)) {
                    showRow = false;
                }

                // Filter by archived status
                if (archivedValue === 'archived' && !isArchived) {
                    showRow = false;
                } else if (archivedValue === '' && isArchived) {
                    showRow = false;
                }

                row.style.display = showRow ? 'table-row' : 'none';
            });
        }
    }

    // Show status message
    function showStatusMessage(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        const container = document.querySelector('.container-fluid');
        const existingAlert = container.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }
        
        container.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto-hide after 3 seconds
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 3000);
    }

    // Attach event listeners
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
