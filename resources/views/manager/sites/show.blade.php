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

    <!-- Site Overview Cards -->
    <div class="row mb-4">
        <!-- Status & Priority -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-flag-fill display-4 text-{{ 
                            $site->status === 'active' ? 'success' : 
                            ($site->status === 'completed' ? 'primary' : 
                            ($site->status === 'on_hold' ? 'warning' : 
                            ($site->status === 'cancelled' ? 'danger' : 'secondary'))) 
                        }}"></i>
                    </div>
                    <h6 class="card-title">Status & Priority</h6>
                    <div class="mb-2">
                        <span class="badge bg-{{ 
                            $site->status === 'active' ? 'success' : 
                            ($site->status === 'completed' ? 'primary' : 
                            ($site->status === 'on_hold' ? 'warning' : 
                            ($site->status === 'cancelled' ? 'danger' : 'secondary'))) 
                        }} fs-6">
                            {{ str_replace('_', ' ', ucfirst($site->status)) }}
                        </span>
                    </div>
                    <div>
                        <span class="badge bg-{{ 
                            $site->priority === 'urgent' ? 'danger' : 
                            ($site->priority === 'high' ? 'warning' : 
                            ($site->priority === 'medium' ? 'info' : 'secondary')) 
                        }}">
                            {{ ucfirst($site->priority) }} Priority
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Projects Count -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-folder-fill display-4 text-info"></i>
                    </div>
                    <h6 class="card-title">Active Projects</h6>
                    <div class="display-6 fw-bold text-info">{{ $site->projects->count() }}</div>
                    <small class="text-muted">{{ Str::plural('project', $site->projects->count()) }}</small>
                </div>
            </div>
        </div>

        <!-- Budget -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-currency-pound display-4 text-success"></i>
                    </div>
                    <h6 class="card-title">Total Budget</h6>
                    <div class="display-6 fw-bold text-success">
                        @if($site->total_budget)
                            £{{ number_format($site->total_budget, 0) }}
                        @else
                            N/A
                        @endif
                    </div>
                    <small class="text-muted">allocated budget</small>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-calendar-range display-4 text-warning"></i>
                    </div>
                    <h6 class="card-title">Duration</h6>
                    <div class="display-6 fw-bold text-warning">
                        @if($site->start_date && $site->expected_completion_date)
                            {{ $site->start_date->diffInDays($site->expected_completion_date) }}
                        @else
                            TBD
                        @endif
                    </div>
                    <small class="text-muted">days planned</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Site Details -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Site Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Basic Information</h6>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Site Name</label>
                                <div class="fw-semibold">{{ $site->name }}</div>
                            </div>
                            @if($site->description)
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Description</label>
                                    <div>{{ $site->description }}</div>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label text-muted small">Client</label>
                                <div class="fw-semibold">
                                    @if($site->client)
                                        <i class="bi bi-building me-1"></i>
                                        {{ $site->client->company_name }}
                                    @else
                                        <span class="text-muted">No client assigned</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Site Manager</label>
                                <div class="fw-semibold">
                                    @if($site->manager)
                                        <i class="bi bi-person me-1"></i>
                                        {{ $site->manager->name }}
                                    @else
                                        <span class="text-muted">No manager assigned</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Location & Contact -->
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">Location & Contact</h6>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Address</label>
                                <div>
                                    <i class="bi bi-geo-alt me-1"></i>
                                    {{ $site->address }}<br>
                                    {{ $site->city }}, {{ $site->state }} {{ $site->zip_code }}<br>
                                    {{ $site->country }}
                                </div>
                            </div>
                            @if($site->site_manager_contact)
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Site Contact</label>
                                    <div>
                                        <i class="bi bi-person me-1"></i>
                                        {{ $site->site_manager_contact }}
                                    </div>
                                </div>
                            @endif
                            @if($site->site_manager_phone)
                                <div class="mb-3">
                                    <label class="form-label text-muted small">Contact Phone</label>
                                    <div>
                                        <i class="bi bi-telephone me-1"></i>
                                        <a href="tel:{{ $site->site_manager_phone }}" class="text-decoration-none">
                                            {{ $site->site_manager_phone }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Timeline Section -->
                    @if($site->start_date || $site->expected_completion_date || $site->actual_completion_date)
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3">Timeline</h6>
                                <div class="row">
                                    @if($site->start_date)
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label text-muted small">Start Date</label>
                                            <div>
                                                <i class="bi bi-calendar-check text-success me-1"></i>
                                                {{ $site->start_date->format('M j, Y') }}
                                            </div>
                                        </div>
                                    @endif
                                    @if($site->expected_completion_date)
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label text-muted small">Expected Completion</label>
                                            <div>
                                                <i class="bi bi-calendar-event text-warning me-1"></i>
                                                {{ $site->expected_completion_date->format('M j, Y') }}
                                            </div>
                                        </div>
                                    @endif
                                    @if($site->actual_completion_date)
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label text-muted small">Actual Completion</label>
                                            <div>
                                                <i class="bi bi-calendar-check text-success me-1"></i>
                                                {{ $site->actual_completion_date->format('M j, Y') }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Notes Section -->
                    @if($site->notes)
                        <hr>
                        <div>
                            <h6 class="fw-bold mb-3">Notes</h6>
                            <div class="bg-light p-3 rounded">
                                {{ $site->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Projects Section -->
            @if($site->projects->count() > 0)
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-folder me-2"></i>Projects ({{ $site->projects->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($site->projects as $project)
                                <div class="col-md-6 mb-3">
                                    <div class="card border">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0">
                                                    <a href="{{ route('projects.show', $project) }}" class="text-decoration-none text-dark project-name-link">
                                                        {{ $project->name }}
                                                    </a>
                                                </h6>
                                                <span class="badge bg-{{ 
                                                    $project->status === 'in_progress' ? 'success' : 
                                                    ($project->status === 'completed' ? 'primary' : 
                                                    ($project->status === 'on_hold' ? 'warning' : 'secondary')) 
                                                }}">
                                                    {{ str_replace('_', ' ', ucfirst($project->status)) }}
                                                </span>
                                            </div>
                                            @if($project->description)
                                                <p class="card-text small text-muted">
                                                    {{ Str::limit($project->description, 100) }}
                                                </p>
                                            @endif
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    @if($project->budget)
                                                        Budget: £{{ number_format($project->budget, 0) }}
                                                    @endif
                                                </small>
                                                <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('manager.sites.edit', $site) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Site Details
                        </a>
                        @if($site->projects->count() > 0)
                            <a href="{{ route('projects.index', ['site_id' => $site->id]) }}" class="btn btn-outline-info">
                                <i class="bi bi-folder me-2"></i>View All Projects
                            </a>
                        @endif
                        @if($site->site_manager_phone)
                            <a href="tel:{{ $site->site_manager_phone }}" class="btn btn-outline-success">
                                <i class="bi bi-telephone me-2"></i>Call Site Contact
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Site Statistics -->
            @if(isset($stats))
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-graph-up me-2"></i>Financial Overview
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Total Budget</span>
                                <span class="fw-bold">£{{ number_format($stats['total_budget'] ?? 0, 0) }}</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Total Spent</span>
                                <span class="fw-bold">£{{ number_format($stats['total_spent'] ?? 0, 0) }}</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Remaining</span>
                                <span class="fw-bold text-{{ ($stats['remaining_budget'] ?? 0) >= 0 ? 'success' : 'danger' }}">
                                    £{{ number_format($stats['remaining_budget'] ?? 0, 0) }}
                                </span>
                            </div>
                        </div>
                        @if(isset($stats['budget_utilization']))
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted small">Budget Utilization</span>
                                    <span class="small">{{ number_format($stats['budget_utilization'], 1) }}%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-{{ $stats['budget_utilization'] > 90 ? 'danger' : ($stats['budget_utilization'] > 75 ? 'warning' : 'success') }}" 
                                         style="width: {{ min($stats['budget_utilization'], 100) }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    border: 1px solid #e5e7eb;
}

.card-header {
    background-color: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
}

.badge {
    font-weight: 500;
}

.progress {
    background-color: #f3f4f6;
}

.btn-outline-primary:hover,
.btn-outline-info:hover,
.btn-outline-success:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.project-name-link {
    transition: color 0.2s ease-in-out;
}

.project-name-link:hover {
    color: #3b82f6 !important;
    text-decoration: none !important;
}

.card:hover .project-name-link {
    color: #3b82f6 !important;
}

@media (max-width: 768px) {
    .display-4 {
        font-size: 2rem;
    }
    
    .display-6 {
        font-size: 1.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
@endpush
