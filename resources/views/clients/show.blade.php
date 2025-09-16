@extends('layouts.app')

@section('title', $client->display_name)

@section('content')
<div class="container-fluid">
    <!-- Professional Page Header -->
    <div class="page-header mb-4">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('clients.index') }}" class="text-decoration-none">Clients</a></li>
                <li class="breadcrumb-item active">{{ $client->display_name }}</li>
            </ol>
        </nav>
        
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="company-icon-wrapper bg-gradient-primary text-white rounded-circle p-4 me-4">
                        <i class="bi bi-building fs-1"></i>
                    </div>
                    <div>
                        <h1 class="page-title mb-2 fw-bold">{{ $client->display_name }}</h1>
                        @if($client->legal_name && $client->legal_name !== $client->company_name)
                            <p class="text-muted mb-2">
                                <i class="bi bi-briefcase me-1"></i>Legal Name: {{ $client->legal_name }}
                            </p>
                        @endif
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @if($client->is_active)
                                <span class="badge rounded-pill bg-success px-3 py-2">
                                    <i class="bi bi-check-circle me-1"></i>Active Client
                                </span>
                            @else
                                <span class="badge rounded-pill bg-secondary px-3 py-2">
                                    <i class="bi bi-pause-circle me-1"></i>Inactive Client
                                </span>
                            @endif
                            @if($client->business_type)
                                <span class="badge rounded-pill bg-info bg-opacity-10 text-info px-3 py-2">
                                    <i class="bi bi-tag me-1"></i>{{ $client->business_type }}
                                </span>
                            @endif
                            @if($client->industry)
                                <span class="badge rounded-pill bg-light text-dark border px-3 py-2">
                                    <i class="bi bi-briefcase me-1"></i>{{ $client->industry }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-end">
                <div class="btn-toolbar justify-content-end" role="toolbar">
                    <div class="btn-group me-2" role="group">
                        <a href="{{ route('invoices.create') }}?client_id={{ $client->id }}" class="btn btn-success btn-lg shadow-sm">
                            <i class="bi bi-receipt me-2"></i>New Invoice
                        </a>
                    </div>
                    @if(auth()->user()->canManageClients())
                        <div class="btn-group" role="group">
                            <a href="{{ route('clients.edit', $client) }}" class="btn btn-primary btn-lg shadow-sm">
                                <i class="bi bi-pencil me-2"></i>Edit Client
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                            <i class="bi bi-building fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Total Sites</p>
                            <h3 class="mb-0 fw-bold">{{ $client->total_sites_count }}</h3>
                            <small class="text-muted">All locations</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 text-info rounded-circle p-3 me-3">
                            <i class="bi bi-folder fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Total Projects</p>
                            <h3 class="mb-0 fw-bold text-info">{{ $client->total_projects_count }}</h3>
                            <small class="text-success">{{ $client->active_projects_count ?? 0 }} active</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                            <i class="bi bi-receipt fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Total Invoices</p>
                            <h3 class="mb-0 fw-bold text-success">{{ $invoiceStats['total'] }}</h3>
                            <small class="text-warning">{{ $invoiceStats['pending'] }} pending</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-circle p-3 me-3">
                            <i class="bi bi-currency-pound fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Project Value</p>
                            <h3 class="mb-0 fw-bold text-warning">£{{ number_format($client->total_projects_value, 0) }}</h3>
                            <small class="text-muted">Total value</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Professional Tab Navigation -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 p-0">
                    <ul class="nav nav-tabs nav-tabs-custom" id="clientTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                                <i class="bi bi-info-circle me-2"></i>Overview
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link position-relative" id="sites-tab" data-bs-toggle="tab" data-bs-target="#sites" type="button" role="tab">
                                <i class="bi bi-building me-2"></i>Sites
                                @if($client->total_sites_count > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                        {{ $client->total_sites_count }}
                                    </span>
                                @endif
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link position-relative" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects" type="button" role="tab">
                                <i class="bi bi-folder me-2"></i>Projects
                                @if($client->total_projects_count > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-info">
                                        {{ $client->total_projects_count }}
                                    </span>
                                @endif
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link position-relative" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices" type="button" role="tab">
                                <i class="bi bi-receipt me-2"></i>Invoices
                                @if($invoiceStats['total'] > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
                                        {{ $invoiceStats['total'] }}
                                    </span>
                                @endif
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">
                                <i class="bi bi-file-earmark-text me-2"></i>Documents
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="clientTabsContent">
                        <!-- Overview Tab -->
                        <div class="tab-pane fade show active" id="overview" role="tabpanel">
                            @include('clients.tabs.overview')
                        </div>

                        <!-- Sites Tab -->
                        <div class="tab-pane fade" id="sites" role="tabpanel">
                            @include('clients.tabs.sites')
                        </div>

                        <!-- Projects Tab -->
                        <div class="tab-pane fade" id="projects" role="tabpanel">
                            @include('clients.tabs.projects')
                        </div>

                        <!-- Invoices Tab -->
                        <div class="tab-pane fade" id="invoices" role="tabpanel">
                            @include('clients.tabs.invoices')
                        </div>

                        <!-- Documents Tab -->
                        <div class="tab-pane fade" id="documents" role="tabpanel">
                            @include('clients.tabs.documents')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Professional Sidebar -->
        <div class="col-lg-4">
            <!-- Contact Information Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-lines-fill me-2"></i>Contact Information
                    </h5>
                </div>
                <div class="card-body">
                    @if($client->contact_person_name || $client->primary_contact)
                        <div class="contact-item d-flex align-items-start mb-3">
                            <div class="contact-icon bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="contact-info">
                                <div class="fw-semibold text-dark">{{ $client->contact_person_name ?? $client->primary_contact }}</div>
                                @if($client->contact_person_title || $client->contact_title)
                                    <small class="text-muted">{{ $client->contact_person_title ?? $client->contact_title }}</small>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($client->contact_email)
                        <div class="contact-item d-flex align-items-start mb-3">
                            <div class="contact-icon bg-info bg-opacity-10 text-info rounded-circle p-2 me-3">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <div class="contact-info">
                                <a href="mailto:{{ $client->contact_email }}" class="text-decoration-none">
                                    {{ $client->contact_email }}
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($client->contact_phone)
                        <div class="contact-item d-flex align-items-start mb-3">
                            <div class="contact-icon bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                <i class="bi bi-telephone-fill"></i>
                            </div>
                            <div class="contact-info">
                                <a href="tel:{{ $client->contact_phone }}" class="text-decoration-none">
                                    {{ $client->contact_phone }}
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($client->website)
                        <div class="contact-item d-flex align-items-start mb-3">
                            <div class="contact-icon bg-warning bg-opacity-10 text-warning rounded-circle p-2 me-3">
                                <i class="bi bi-globe"></i>
                            </div>
                            <div class="contact-info">
                                <a href="{{ $client->website }}" target="_blank" class="text-decoration-none">
                                    {{ parse_url($client->website, PHP_URL_HOST) ?? $client->website }}
                                    <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($client->full_address || ($client->address || $client->city || $client->state))
                        <div class="contact-item d-flex align-items-start">
                            <div class="contact-icon bg-danger bg-opacity-10 text-danger rounded-circle p-2 me-3">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div class="contact-info">
                                <address class="mb-0">
                                    @if($client->full_address)
                                        {{ $client->full_address }}
                                    @else
                                        @if($client->address)
                                            {{ $client->address }}<br>
                                        @endif
                                        @if($client->city || $client->state || $client->zip_code)
                                            {{ $client->city }}{{ $client->city && $client->state ? ', ' : '' }}
                                            {{ $client->state }} {{ $client->zip_code }}
                                        @endif
                                    @endif
                                </address>
                            </div>
                        </div>
                    @endif

                    @if(!$client->contact_email && !$client->contact_phone && !$client->website && !$client->full_address)
                        <div class="text-center py-3">
                            <i class="bi bi-inbox display-4 text-muted"></i>
                            <p class="text-muted mt-2">No contact information available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Invoice Summary Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up-arrow me-2 text-success"></i>Invoice Summary
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Amount Summary -->
                    <div class="invoice-summary mb-4">
                        <div class="summary-item d-flex justify-content-between align-items-center mb-3">
                            <div class="summary-label">
                                <i class="bi bi-cash-stack text-primary me-2"></i>
                                <span class="text-muted">Total Amount</span>
                            </div>
                            <div class="summary-value fw-bold fs-5 text-primary">
                                £{{ number_format($invoiceStats['total_amount'], 2) }}
                            </div>
                        </div>
                        <div class="summary-item d-flex justify-content-between align-items-center mb-3">
                            <div class="summary-label">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <span class="text-muted">Paid Amount</span>
                            </div>
                            <div class="summary-value fw-bold text-success">
                                £{{ number_format($invoiceStats['paid_amount'], 2) }}
                            </div>
                        </div>
                        <div class="summary-item d-flex justify-content-between align-items-center">
                            <div class="summary-label">
                                <i class="bi bi-clock-history text-warning me-2"></i>
                                <span class="text-muted">Pending Amount</span>
                            </div>
                            <div class="summary-value fw-bold text-warning">
                                £{{ number_format($invoiceStats['pending_amount'], 2) }}
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Status Counts -->
                    <div class="row text-center pt-3 border-top">
                        <div class="col-4">
                            <div class="summary-stat">
                                <div class="stat-icon-small bg-success bg-opacity-10 text-success rounded-circle p-2 mx-auto mb-2" style="width: 40px; height: 40px;">
                                    <i class="bi bi-check"></i>
                                </div>
                                <div class="stat-number fw-bold fs-4 text-success">{{ $invoiceStats['paid'] }}</div>
                                <div class="stat-label text-muted small">Paid</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="summary-stat">
                                <div class="stat-icon-small bg-warning bg-opacity-10 text-warning rounded-circle p-2 mx-auto mb-2" style="width: 40px; height: 40px;">
                                    <i class="bi bi-clock"></i>
                                </div>
                                <div class="stat-number fw-bold fs-4 text-warning">{{ $invoiceStats['pending'] }}</div>
                                <div class="stat-label text-muted small">Pending</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="summary-stat">
                                <div class="stat-icon-small bg-danger bg-opacity-10 text-danger rounded-circle p-2 mx-auto mb-2" style="width: 40px; height: 40px;">
                                    <i class="bi bi-exclamation"></i>
                                </div>
                                <div class="stat-number fw-bold fs-4 text-danger">{{ $invoiceStats['overdue'] }}</div>
                                <div class="stat-label text-muted small">Overdue</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Client Information Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2 text-info"></i>Client Since
                    </h5>
                </div>
                <div class="card-body">
                    <div class="detail-group mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-calendar-plus text-muted me-2"></i>
                            <label class="detail-label text-muted mb-0">Added to System</label>
                        </div>
                        <div class="detail-value fw-semibold">{{ $client->created_at->format('F j, Y') }}</div>
                        <small class="text-muted">{{ $client->created_at->diffForHumans() }}</small>
                    </div>

                    <div class="detail-group mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-clock-history text-muted me-2"></i>
                            <label class="detail-label text-muted mb-0">Last Updated</label>
                        </div>
                        <div class="detail-value fw-semibold">{{ $client->updated_at->format('F j, Y') }}</div>
                        <small class="text-muted">{{ $client->updated_at->diffForHumans() }}</small>
                    </div>

                    <div class="detail-group">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-flag text-muted me-2"></i>
                            <label class="detail-label text-muted mb-0">Status</label>
                        </div>
                        <div class="detail-value">
                            @if($client->is_active)
                                <span class="badge rounded-pill bg-success px-3 py-2">
                                    <i class="bi bi-check-circle me-1"></i>Active Client
                                </span>
                            @else
                                <span class="badge rounded-pill bg-secondary px-3 py-2">
                                    <i class="bi bi-pause-circle me-1"></i>Inactive Client
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Professional Client Page Styling */
.company-icon-wrapper {
    width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

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

/* Custom Tab Styling */
.nav-tabs-custom {
    border-bottom: 2px solid #e9ecef;
}

.nav-tabs-custom .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    background: none;
    color: #6c757d;
    font-weight: 500;
    padding: 1rem 1.5rem;
    margin-bottom: -2px;
    transition: all 0.3s;
    position: relative;
}

.nav-tabs-custom .nav-link:hover {
    border-bottom-color: #e9ecef;
    color: #495057;
    background: #f8f9fa;
}

.nav-tabs-custom .nav-link.active {
    background: white;
    border-bottom-color: #0d6efd;
    color: #0d6efd;
}

.nav-tabs-custom .nav-link .badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
    margin-left: 0.5rem;
}

/* Contact Items */
.contact-item {
    transition: transform 0.2s;
}

.contact-item:hover {
    transform: translateX(5px);
}

.contact-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.contact-info {
    flex: 1;
    min-width: 0;
}

.contact-info a {
    color: #495057;
    transition: color 0.2s;
}

.contact-info a:hover {
    color: #0d6efd;
}

/* Invoice Summary */
.summary-item {
    padding: 0.5rem 0;
}

.summary-label {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
}

.summary-value {
    font-family: 'Monaco', 'Courier New', monospace;
}

.stat-icon-small {
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Detail Groups */
.detail-group {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.detail-group:last-child {
    border-bottom: none;
}

.detail-label {
    font-size: 0.875rem;
    font-weight: 500;
}

.detail-value {
    color: #1f2937;
}

/* Responsive Design */
@media (max-width: 768px) {
    .company-icon-wrapper {
        width: 80px;
        height: 80px;
        margin-bottom: 1rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .stat-card .card-body {
        padding: 1rem;
    }
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.tab-pane {
    animation: fadeIn 0.3s ease-in-out;
}

/* Card Shadow Effects */
.card {
    transition: box-shadow 0.3s;
}

.card:hover {
    box-shadow: 0 8px 20px rgba(0,0,0,0.1) !important;
}
</style>

@push('scripts')
<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
@endsection