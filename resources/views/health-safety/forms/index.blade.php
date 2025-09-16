@extends('layouts.app')

@section('title', 'Safety Forms')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center">
                    <div class="header-icon bg-purple bg-opacity-10 text-purple rounded-circle p-3 me-3">
                        <i class="bi bi-clipboard-check fs-3"></i>
                    </div>
                    <div>
                        <h1 class="page-title mb-1 fw-bold">Safety Forms Management</h1>
                        <p class="page-subtitle text-muted mb-0">Manage custom safety forms, templates, and submissions</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-end">
                <a href="{{ route('health-safety.forms.template.create') }}" class="btn btn-purple btn-lg shadow-sm">
                    <i class="bi bi-plus-circle me-2"></i>Create New Template
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-purple bg-opacity-10 text-purple rounded-circle p-3 me-3">
                            <i class="bi bi-file-earmark-text fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Active Templates</p>
                            <h3 class="mb-0 fw-bold">{{ $templates->count() }}</h3>
                            <small class="text-success">
                                <i class="bi bi-check-circle"></i>Available for use
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
                            <i class="bi bi-file-earmark-check fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Total Submissions</p>
                            <h3 class="mb-0 fw-bold text-info">{{ $submissions->total() }}</h3>
                            <small class="text-muted">All time</small>
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
                            <i class="bi bi-clock fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Pending Review</p>
                            <h3 class="mb-0 fw-bold text-warning">{{ $submissions->where('status', 'submitted')->count() }}</h3>
                            <small class="text-muted">Awaiting approval</small>
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
                            <i class="bi bi-calendar-week fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">This Month</p>
                            <h3 class="mb-0 fw-bold text-success">{{ $submissions->where('submitted_at', '>=', now()->startOfMonth())->count() }}</h3>
                            <small class="text-muted">New submissions</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content with Tabs -->
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom-0 pt-3">
            <ul class="nav nav-tabs card-header-tabs" id="formsManagementTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="templates-tab" data-bs-toggle="tab" data-bs-target="#templates" type="button" role="tab">
                        <i class="bi bi-file-earmark-text me-2"></i>Form Templates
                        <span class="badge bg-purple ms-2">{{ $templates->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="submissions-tab" data-bs-toggle="tab" data-bs-target="#submissions" type="button" role="tab">
                        <i class="bi bi-file-earmark-check me-2"></i>Submissions
                        <span class="badge bg-info ms-2">{{ $submissions->total() }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="formsManagementTabsContent">
                <!-- Form Templates Tab -->
                <div class="tab-pane fade show active" id="templates" role="tabpanel">
                    @if($templates->count() > 0)
                        <div class="row g-3">
                            @foreach($templates as $template)
                                <div class="col-lg-4 col-md-6">
                                    <div class="card template-card border-0 shadow-sm h-100">
                                        <div class="card-header bg-light border-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-{{ $template->category === 'inspection' ? 'primary' : ($template->category === 'permit' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($template->category) }}
                                                </span>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('health-safety.forms.submit', $template->id) }}">
                                                                <i class="bi bi-file-earmark-plus me-2"></i>Use Template
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="bi bi-eye me-2"></i>Preview
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="bi bi-pencil me-2"></i>Edit Template
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#">
                                                                <i class="bi bi-archive me-2"></i>Archive
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title fw-bold mb-3">
                                                <i class="bi bi-clipboard-check text-purple me-2"></i>
                                                {{ $template->name }}
                                            </h5>
                                            
                                            @if($template->description)
                                                <p class="card-text text-muted mb-3">
                                                    {{ Str::limit($template->description, 100) }}
                                                </p>
                                            @endif

                                            <div class="template-details">
                                                <div class="detail-item mb-2">
                                                    <i class="bi bi-person text-muted me-2"></i>
                                                    <span class="text-muted">Created by:</span>
                                                    <strong>{{ $template->createdBy->name }}</strong>
                                                </div>
                                                
                                                <div class="detail-item mb-2">
                                                    <i class="bi bi-calendar text-muted me-2"></i>
                                                    <span class="text-muted">Created:</span>
                                                    <strong>{{ $template->created_at->format('M j, Y') }}</strong>
                                                </div>
                                                
                                                <div class="detail-item mb-3">
                                                    <i class="bi bi-list-ul text-muted me-2"></i>
                                                    <span class="text-muted">Fields:</span>
                                                    <strong>{{ count(json_decode($template->fields, true) ?? []) }}</strong>
                                                </div>
                                            </div>

                                            <div class="template-features mb-3">
                                                @if($template->requires_signature)
                                                    <span class="badge bg-info me-2">
                                                        <i class="bi bi-pen me-1"></i>Signature Required
                                                    </span>
                                                @endif
                                                @if($template->requires_photo)
                                                    <span class="badge bg-success me-2">
                                                        <i class="bi bi-camera me-1"></i>Photo Required
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white border-0">
                                            <div class="d-grid">
                                                <a href="{{ route('health-safety.forms.submit', $template->id) }}" class="btn btn-purple">
                                                    <i class="bi bi-file-earmark-plus me-1"></i>Use This Template
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state text-center py-5">
                            <div class="empty-icon-wrapper mx-auto mb-4" style="width: 120px; height: 120px;">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center h-100">
                                    <i class="bi bi-file-earmark-text display-1 text-muted"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold mb-2">No Form Templates Found</h4>
                            <p class="text-muted mb-4">Create your first custom safety form template to get started.</p>
                            <a href="{{ route('health-safety.forms.template.create') }}" class="btn btn-purple btn-lg shadow-sm">
                                <i class="bi bi-plus-circle me-2"></i>Create Your First Template
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Submissions Tab -->
                <div class="tab-pane fade" id="submissions" role="tabpanel">
                    @if($submissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover submissions-table mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 ps-4">
                                            <i class="bi bi-file-earmark-check me-2"></i>Submission Details
                                        </th>
                                        <th class="border-0">
                                            <i class="bi bi-file-earmark-text me-2"></i>Template
                                        </th>
                                        <th class="border-0">
                                            <i class="bi bi-geo-alt me-2"></i>Location
                                        </th>
                                        <th class="border-0">
                                            <i class="bi bi-person me-2"></i>Submitted By
                                        </th>
                                        <th class="border-0">
                                            <i class="bi bi-calendar me-2"></i>Date
                                        </th>
                                        <th class="border-0">
                                            <i class="bi bi-flag me-2"></i>Status
                                        </th>
                                        <th class="border-0 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submissions as $submission)
                                        <tr class="submission-row">
                                            <td class="ps-4">
                                                <div class="submission-info">
                                                    <h6 class="mb-1 fw-semibold">
                                                        {{ $submission->submission_number }}
                                                    </h6>
                                                    <small class="text-muted">Reference Number</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="template-info">
                                                    <strong>{{ $submission->template->name }}</strong>
                                                    <div class="small text-muted mt-1">
                                                        <span class="badge bg-{{ $submission->template->category === 'inspection' ? 'primary' : 'info' }} badge-sm">
                                                            {{ ucfirst($submission->template->category) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="location-info">
                                                    @if($submission->site)
                                                        <i class="bi bi-geo-alt text-muted me-1"></i>
                                                        {{ $submission->site->name }}
                                                    @else
                                                        <span class="text-muted fst-italic">No site specified</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="submitter-info">
                                                    <strong>{{ $submission->submittedBy->name }}</strong>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="date-info">
                                                    <strong>{{ $submission->submitted_at->format('M j, Y') }}</strong>
                                                    <div class="small text-muted">
                                                        {{ $submission->submitted_at->format('g:i A') }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge rounded-pill bg-{{ $submission->status === 'submitted' ? 'warning' : ($submission->status === 'approved' ? 'success' : 'danger') }} px-3 py-2">
                                                    <i class="bi bi-{{ $submission->status === 'submitted' ? 'clock' : ($submission->status === 'approved' ? 'check-circle' : 'x-circle') }} me-1"></i>
                                                    {{ ucfirst($submission->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="bi bi-eye me-2"></i>View Details
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#">
                                                                <i class="bi bi-download me-2"></i>Download PDF
                                                            </a>
                                                        </li>
                                                        @if($submission->status === 'submitted')
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item text-success" href="#">
                                                                    <i class="bi bi-check-circle me-2"></i>Approve
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item text-danger" href="#">
                                                                    <i class="bi bi-x-circle me-2"></i>Reject
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

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $submissions->links() }}
                        </div>
                    @else
                        <div class="empty-state text-center py-5">
                            <div class="empty-icon-wrapper mx-auto mb-4" style="width: 120px; height: 120px;">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center h-100">
                                    <i class="bi bi-file-earmark-check display-1 text-muted"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold mb-2">No Form Submissions Found</h4>
                            <p class="text-muted mb-4">Form submissions will appear here once templates are used.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .page-header {
        margin-bottom: 2rem;
    }

    .header-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1a202c;
    }

    .page-subtitle {
        font-size: 1rem;
        color: #64748b;
    }

    /* Statistics Cards */
    .stat-card {
        transition: transform 0.3s, box-shadow 0.3s;
        border-left: 4px solid transparent;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }

    .stat-card:nth-child(1) { border-left-color: #9333ea; }
    .stat-card:nth-child(2) { border-left-color: #0dcaf0; }
    .stat-card:nth-child(3) { border-left-color: #ffc107; }
    .stat-card:nth-child(4) { border-left-color: #198754; }

    .stat-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Purple theme */
    .bg-purple {
        background-color: #9333ea !important;
    }

    .text-purple {
        color: #9333ea !important;
    }

    .btn-purple {
        background-color: #9333ea;
        border-color: #9333ea;
        color: white;
    }

    .btn-purple:hover {
        background-color: #7c2bc7;
        border-color: #7c2bc7;
        color: white;
        transform: translateY(-1px);
    }

    .badge.bg-purple {
        background-color: #9333ea !important;
    }

    /* Template Cards */
    .template-card {
        transition: transform 0.3s, box-shadow 0.3s;
        border-left: 4px solid #9333ea;
    }

    .template-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
    }

    .detail-item {
        font-size: 0.9rem;
    }

    /* Submissions Table */
    .submissions-table thead {
        background: linear-gradient(180deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .submissions-table thead th {
        font-weight: 600;
        color: #495057;
        padding: 1rem;
        white-space: nowrap;
    }

    .submission-row {
        transition: background-color 0.2s, transform 0.2s;
    }

    .submission-row:hover {
        background-color: rgba(147, 51, 234, 0.05);
        transform: scale(1.005);
    }

    .submission-row td {
        padding: 1rem;
        vertical-align: middle;
    }

    /* Empty State */
    .empty-icon-wrapper {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    /* Nav tabs */
    .nav-tabs .nav-link {
        border: none;
        color: #64748b;
        font-weight: 500;
        padding: 1rem 1.5rem;
    }

    .nav-tabs .nav-link.active {
        background-color: transparent;
        border-bottom: 2px solid #9333ea;
        color: #9333ea;
        font-weight: 600;
    }

    .nav-tabs .nav-link:hover {
        border-color: transparent;
        color: #9333ea;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stat-card .card-body {
            padding: 1rem;
        }
        
        .template-card .card-title {
            font-size: 1rem;
        }
        
        .table-responsive {
            font-size: 0.9rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
@endsection


