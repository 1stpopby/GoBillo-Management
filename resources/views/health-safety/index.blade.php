@extends('layouts.app')

@section('title', 'Health & Safety')

@section('content')
<div class="sites-container">
    <!-- Professional Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center">
                    <div class="header-icon bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                        <i class="bi bi-shield-check fs-3"></i>
                    </div>
                    <div>
                        <h1 class="page-title mb-1 fw-bold">Health & Safety Management</h1>
                        <p class="page-subtitle text-muted mb-0">Manage safety documentation, incidents, and compliance across all sites</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-end">
                <div class="dropdown">
                    <button class="btn btn-primary btn-lg shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-plus-circle me-2"></i>Quick Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('health-safety.incidents.create') }}">
                            <i class="bi bi-exclamation-triangle text-warning me-2"></i>Report Incident
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('health-safety.toolbox-talks.create') }}">
                            <i class="bi bi-megaphone text-info me-2"></i>Record Toolbox Talk
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('health-safety.inductions.create') }}">
                            <i class="bi bi-person-check text-success me-2"></i>New Site Induction
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('health-safety.rams.create') }}">
                            <i class="bi bi-file-earmark-shield text-primary me-2"></i>Create RAMS
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('health-safety.forms') }}">
                            <i class="bi bi-clipboard-check text-purple me-2"></i>Submit Form
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                            <i class="bi bi-shield-check fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Active RAMS</p>
                            <h3 class="mb-0 fw-bold">{{ $stats['active_rams'] }}</h3>
                            <small class="text-success">
                                <i class="bi bi-check-circle"></i>All locations
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
                        <div class="stat-icon bg-info bg-opacity-10 text-info rounded-circle p-3 me-3">
                            <i class="bi bi-megaphone fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Toolbox Talks</p>
                            <h3 class="mb-0 fw-bold text-info">{{ $stats['toolbox_talks_month'] }}</h3>
                            <small class="text-muted">This month</small>
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
                            <i class="bi bi-shield-check fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Open Incidents</p>
                            <h3 class="mb-0 fw-bold text-success">{{ $stats['open_incidents'] }}</h3>
                            <small class="text-muted">All clear</small>
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
                            <i class="bi bi-person-check fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Active Inductions</p>
                            <h3 class="mb-0 fw-bold text-warning">{{ $stats['active_inductions'] }}</h3>
                            <small class="text-muted">Valid certificates</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card with Tabs -->
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom-0 pt-3">
            <ul class="nav nav-tabs card-header-tabs" id="healthSafetyTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="rams-tab" data-bs-toggle="tab" data-bs-target="#rams" type="button" role="tab">
                        <i class="bi bi-file-earmark-shield me-2"></i>RAMS
                        <span class="badge bg-primary-subtle text-primary ms-2">{{ $stats['active_rams'] }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="toolbox-tab" data-bs-toggle="tab" data-bs-target="#toolbox" type="button" role="tab">
                        <i class="bi bi-megaphone me-2"></i>Toolbox Talks
                        <span class="badge bg-info-subtle text-info ms-2">{{ $stats['toolbox_talks_month'] }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="incidents-tab" data-bs-toggle="tab" data-bs-target="#incidents" type="button" role="tab">
                        <i class="bi bi-exclamation-triangle me-2"></i>Incidents
                        <span class="badge bg-warning-subtle text-warning ms-2">{{ $stats['open_incidents'] }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="inductions-tab" data-bs-toggle="tab" data-bs-target="#inductions" type="button" role="tab">
                        <i class="bi bi-person-check me-2"></i>Inductions
                        <span class="badge bg-success-subtle text-success ms-2">{{ $stats['active_inductions'] }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="forms-tab" data-bs-toggle="tab" data-bs-target="#forms" type="button" role="tab">
                        <i class="bi bi-clipboard-check me-2"></i>Forms
                        <span class="badge bg-purple-subtle text-purple ms-2">{{ $stats['pending_forms'] }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="observations-tab" data-bs-toggle="tab" data-bs-target="#observations" type="button" role="tab">
                        <i class="bi bi-eye me-2"></i>Observations
                        <span class="badge bg-secondary-subtle text-secondary ms-2">{{ $stats['safety_observations'] }}</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="healthSafetyTabContent">
                <!-- RAMS Tab -->
                <div class="tab-pane fade show active" id="rams" role="tabpanel">
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <h5 class="mb-1">Risk Assessments & Method Statements</h5>
                            <p class="text-muted">Manage safety documentation and risk assessments for all sites</p>
                        </div>
                        <div class="col-lg-6 text-end">
                            <a href="{{ route('health-safety.rams.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Create RAMS
                            </a>
                        </div>
                    </div>
                    
                    <!-- RAMS Content will go here -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Title</th>
                                    <th>Site</th>
                                    <th>Risk Level</th>
                                    <th>Valid Until</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-file-earmark-shield fs-1"></i>
                                        <p class="mt-2">No RAMS documents found</p>
                                        <a href="{{ route('health-safety.rams.create') }}" class="btn btn-sm btn-primary">
                                            Create First RAMS
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Toolbox Talks Tab -->
                <div class="tab-pane fade" id="toolbox" role="tabpanel">
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <h5 class="mb-1">Safety Toolbox Talks</h5>
                            <p class="text-muted">Record and track safety briefings and team communications</p>
                        </div>
                        <div class="col-lg-6 text-end">
                            <a href="{{ route('health-safety.toolbox-talks.create') }}" class="btn btn-info">
                                <i class="bi bi-plus-circle me-2"></i>Record Talk
                            </a>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Title</th>
                                    <th>Site</th>
                                    <th>Conducted By</th>
                                    <th>Attendees</th>
                                    <th>Duration</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-megaphone fs-1"></i>
                                        <p class="mt-2">No toolbox talks recorded</p>
                                        <a href="{{ route('health-safety.toolbox-talks.create') }}" class="btn btn-sm btn-info">
                                            Record First Talk
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Incidents Tab -->
                <div class="tab-pane fade" id="incidents" role="tabpanel">
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <h5 class="mb-1">Incident Reporting</h5>
                            <p class="text-muted">Track accidents, near misses, and safety investigations</p>
                        </div>
                        <div class="col-lg-6 text-end">
                            <a href="{{ route('health-safety.incidents.create') }}" class="btn btn-warning">
                                <i class="bi bi-plus-circle me-2"></i>Report Incident
                            </a>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Incident #</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Site</th>
                                    <th>Severity</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-shield-check fs-1 text-success"></i>
                                        <p class="mt-2 text-success">No incidents reported - Great job!</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Inductions Tab -->
                <div class="tab-pane fade" id="inductions" role="tabpanel">
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <h5 class="mb-1">Site Inductions</h5>
                            <p class="text-muted">Manage worker inductions and safety certifications</p>
                        </div>
                        <div class="col-lg-6 text-end">
                            <a href="{{ route('health-safety.inductions.create') }}" class="btn btn-success">
                                <i class="bi bi-plus-circle me-2"></i>New Induction
                            </a>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Certificate #</th>
                                    <th>Worker Name</th>
                                    <th>Company</th>
                                    <th>Site</th>
                                    <th>Inducted Date</th>
                                    <th>Valid Until</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-person-check fs-1"></i>
                                        <p class="mt-2">No inductions recorded</p>
                                        <a href="{{ route('health-safety.inductions.create') }}" class="btn btn-sm btn-success">
                                            Create First Induction
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Forms Tab -->
                <div class="tab-pane fade" id="forms" role="tabpanel">
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <h5 class="mb-1">Custom Safety Forms</h5>
                            <p class="text-muted">Inspections, permits, and safety checklists</p>
                        </div>
                        <div class="col-lg-6 text-end">
                            <a href="{{ route('health-safety.forms.template.create') }}" class="btn btn-purple">
                                <i class="bi bi-plus-circle me-2"></i>Create Template
                            </a>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Form #</th>
                                    <th>Template</th>
                                    <th>Site</th>
                                    <th>Submitted By</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-clipboard-check fs-1"></i>
                                        <p class="mt-2">No forms submitted</p>
                                        <a href="{{ route('health-safety.forms.template.create') }}" class="btn btn-sm btn-purple">
                                            Create Form Template
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Observations Tab -->
                <div class="tab-pane fade" id="observations" role="tabpanel">
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <h5 class="mb-1">Safety Observations</h5>
                            <p class="text-muted">Track positive and negative safety observations</p>
                        </div>
                        <div class="col-lg-6 text-end">
                            <button class="btn btn-secondary">
                                <i class="bi bi-plus-circle me-2"></i>New Observation
                            </button>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Site</th>
                                    <th>Category</th>
                                    <th>Observed By</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-eye fs-1"></i>
                                        <p class="mt-2">No observations recorded</p>
                                        <button class="btn btn-sm btn-secondary">
                                            Record First Observation
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Force reload - {{ now() }} */


    /* Statistics Cards - Matching Sites Page */
    .stat-card {
        transition: transform 0.3s, box-shadow 0.3s;
        border-left: 4px solid transparent;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }

    .stat-card:nth-child(1) { border-left-color: #0d6efd; }
    .stat-card:nth-child(2) { border-left-color: #0dcaf0; }
    .stat-card:nth-child(3) { border-left-color: #198754; }
    .stat-card:nth-child(4) { border-left-color: #ffc107; }

    .stat-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Module Cards */
    .module-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .module-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border-color: #dee2e6;
    }

    .module-header {
        padding: 2rem;
        text-align: center;
    }

    .module-icon {
        font-size: 3rem;
        margin-bottom: 0;
    }

    .module-body {
        padding: 0 1.5rem 1.5rem;
        text-align: center;
    }

    .module-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 0.5rem;
    }

    .module-description {
        font-size: 0.875rem;
        color: #718096;
        margin-bottom: 1rem;
        line-height: 1.5;
    }

    .module-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1rem;
        border-top: 1px solid #f1f3f5;
    }

    /* Activity Timeline */
    .activity-timeline {
        padding: 0;
    }

    .activity-item {
        display: flex;
        padding: 1.25rem;
        border-bottom: 1px solid #f1f3f5;
        transition: background 0.2s;
    }

    .activity-item:hover {
        background: #f8f9fa;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .activity-content {
        flex-grow: 1;
    }

    .activity-title {
        font-size: 0.9375rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
        color: #1a202c;
    }

    .activity-description {
        font-size: 0.875rem;
        color: #718096;
        margin-bottom: 0.5rem;
    }

    .activity-meta {
        font-size: 0.8125rem;
        color: #a0aec0;
    }

    /* Upcoming Items */
    .upcoming-item {
        padding: 1rem 0;
        border-bottom: 1px solid #f1f3f5;
    }

    .upcoming-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .upcoming-item:first-child {
        padding-top: 0;
    }

    .upcoming-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Performance Metrics */
    .performance-metric {
        margin-bottom: 1.5rem;
    }

    .performance-metric:last-child {
        margin-bottom: 0;
    }

    .progress {
        height: 8px;
        background-color: #f1f3f5;
        border-radius: 4px;
    }

    .progress-bar {
        border-radius: 4px;
        transition: width 0.6s ease;
    }

    /* Color Utilities */
    .bg-purple {
        background: #9333ea !important;
    }

    .bg-purple-subtle {
        background: rgba(147, 51, 234, 0.1) !important;
    }

    .text-purple {
        color: #9333ea !important;
    }

    .bg-purple.bg-opacity-10 {
        background: rgba(147, 51, 234, 0.1) !important;
    }

    .btn-purple {
        background: #9333ea;
        border-color: #9333ea;
        color: white;
    }

    .btn-purple:hover {
        background: #7c2bc7;
        border-color: #7c2bc7;
        color: white;
    }

    /* Card Enhancements */
    .card {
        border-radius: 12px;
        border: 1px solid #e9ecef;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .card-header {
        border-radius: 12px 12px 0 0 !important;
        border-bottom: 1px solid #e9ecef;
        font-weight: 600;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Button Enhancements */
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
        border-radius: 10px;
        font-weight: 600;
    }

    .dropdown-menu {
        border-radius: 12px;
        border: 1px solid #e9ecef;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        padding: 0.5rem;
    }

    .dropdown-item {
        border-radius: 8px;
        padding: 0.625rem 1rem;
        font-size: 0.9375rem;
        transition: all 0.2s;
    }

    .dropdown-item:hover {
        background-color: #f1f3f5;
    }

    .dropdown-divider {
        margin: 0.5rem 0;
    }

    /* Badge Styles */
    .badge {
        padding: 0.375rem 0.75rem;
        font-weight: 600;
        border-radius: 6px;
        font-size: 0.8125rem;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .page-header-section {
            padding: 1.5rem;
        }

        .page-title {
            font-size: 1.5rem;
        }

        .stat-number {
            font-size: 1.5rem;
        }

        .module-card {
            margin-bottom: 1rem;
        }
    }
</style>
@endpush

@endsection
