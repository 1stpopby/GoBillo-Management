@extends('layouts.app')

@section('title', $employee->full_name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
                        <li class="breadcrumb-item active">{{ $employee->full_name }}</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center">
                    <img src="{{ $employee->avatar_url }}" alt="{{ $employee->full_name }}" 
                         class="avatar-lg me-3 rounded-circle">
                    <div>
                        <h1 class="page-title mb-1">{{ $employee->full_name }}</h1>
                        <p class="page-subtitle mb-0">{{ $employee->job_title }} • {{ $employee->role_display }}</p>
                        <div class="d-flex gap-2 mt-2">
                            <span class="badge bg-{{ $employee->status_color }}">
                                {{ ucfirst(str_replace('_', ' ', $employee->employment_status)) }}
                            </span>
                            <span class="badge bg-light text-dark">ID: {{ $employee->employee_id }}</span>
                            @if($employee->department)
                                <span class="badge bg-info">{{ $employee->department }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-end">
                @if(auth()->user()->canManageCompanyUsers())
                    <div class="btn-group" role="group">
                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Employee
                        </a>
                        <button type="button" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#allocateModal">
                                <i class="bi bi-geo-alt me-2"></i>Allocate to Site
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="window.print()">
                                <i class="bi bi-printer me-2"></i>Print Profile
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete()">
                                <i class="bi bi-trash me-2"></i>Delete Employee
                            </a></li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-5 mt-4">
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $assignedAssets->count() }}</h4>
                            <p class="mb-0">Assigned Assets</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-laptop display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $employee->activeSiteAllocations->count() }}</h4>
                            <p class="mb-0">Active Sites</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-geo-alt display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">${{ number_format($financialSummary['total_expenses'], 0) }}</h4>
                            <p class="mb-0">Total Expenses</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-currency-dollar display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $documents->count() }}</h4>
                            <p class="mb-0">Documents</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-file-earmark display-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabbed Content -->
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="employeeTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" 
                            data-bs-target="#overview" type="button" role="tab" aria-controls="overview" 
                            aria-selected="true">
                        <i class="bi bi-person me-2"></i>Overview
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="documents-tab" data-bs-toggle="tab" 
                            data-bs-target="#documents" type="button" role="tab" aria-controls="documents" 
                            aria-selected="false">
                        <i class="bi bi-file-earmark me-2"></i>Documents
                        @if($documents->count() > 0)
                            <span class="badge bg-primary ms-1">{{ $documents->count() }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="sites-tab" data-bs-toggle="tab" 
                            data-bs-target="#sites" type="button" role="tab" aria-controls="sites" 
                            aria-selected="false">
                        <i class="bi bi-geo-alt me-2"></i>Assigned Sites
                        @if($employee->activeSiteAllocations->count() > 0)
                            <span class="badge bg-success ms-1">{{ $employee->activeSiteAllocations->count() }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="invoices-tab" data-bs-toggle="tab" 
                            data-bs-target="#invoices" type="button" role="tab" aria-controls="invoices" 
                            aria-selected="false">
                        <i class="bi bi-receipt me-2"></i>Invoices
                        @if($invoices->count() > 0)
                            <span class="badge bg-info ms-1">{{ $invoices->count() }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="cis-tab" data-bs-toggle="tab" 
                            data-bs-target="#cis" type="button" role="tab" aria-controls="cis" 
                            aria-selected="false">
                        <i class="bi bi-shield-check me-2"></i>CIS
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="assets-tab" data-bs-toggle="tab" 
                            data-bs-target="#assets" type="button" role="tab" aria-controls="assets" 
                            aria-selected="false">
                        <i class="bi bi-laptop me-2"></i>Assets
                        @if($assignedAssets->count() > 0)
                            <span class="badge bg-primary ms-1">{{ $assignedAssets->count() }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="financial-tab" data-bs-toggle="tab" 
                            data-bs-target="#financial" type="button" role="tab" aria-controls="financial" 
                            aria-selected="false">
                        <i class="bi bi-graph-up me-2"></i>Financial Report
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body py-4">
            <div class="tab-content" id="employeeTabsContent">
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                    @include('employees.tabs.overview')
                </div>
                
                <!-- Documents Tab -->
                <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                    @include('employees.tabs.documents')
                </div>
                
                <!-- Sites Tab -->
                <div class="tab-pane fade" id="sites" role="tabpanel" aria-labelledby="sites-tab">
                    @include('employees.tabs.sites')
                </div>
                
                <!-- Invoices Tab -->
                <div class="tab-pane fade" id="invoices" role="tabpanel" aria-labelledby="invoices-tab">
                    @include('employees.tabs.invoices')
                </div>
                
                <!-- CIS Tab -->
                <div class="tab-pane fade" id="cis" role="tabpanel" aria-labelledby="cis-tab">
                    @include('employees.tabs.cis')
                </div>
                
                <!-- Assets Tab -->
                <div class="tab-pane fade" id="assets" role="tabpanel" aria-labelledby="assets-tab">
                    @include('employees.tabs.assets')
                </div>
                
                <!-- Financial Report Tab -->
                <div class="tab-pane fade" id="financial" role="tabpanel" aria-labelledby="financial-tab">
                    @include('employees.tabs.financial')
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Allocate to Site Modal -->
@if(auth()->user()->canManageCompanyUsers())
    <div class="modal fade" id="allocateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Allocate to Site</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('employees.allocate-site', $employee) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="site_id" class="form-label">Site <span class="text-danger">*</span></label>
                            <select class="form-select" id="site_id" name="site_id" required>
                                <option value="">Select site...</option>
                                @foreach(\App\Models\Site::forCompany()->orderBy('name')->get() as $site)
                                    <option value="{{ $site->id }}">{{ $site->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label for="allocated_from" class="form-label">From <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="allocated_from" name="allocated_from" required>
                            </div>
                            <div class="col-md-6">
                                <label for="allocated_until" class="form-label">Until</label>
                                <input type="date" class="form-control" id="allocated_until" name="allocated_until">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="allocation_type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="allocation_type" name="allocation_type" required>
                                    <option value="primary">Primary</option>
                                    <option value="secondary">Secondary</option>
                                    <option value="temporary">Temporary</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="allocation_percentage" class="form-label">Allocation % <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="allocation_percentage" name="allocation_percentage" 
                                       min="1" max="100" value="100" required>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="responsibilities" class="form-label">Responsibilities</label>
                            <textarea class="form-control" id="responsibilities" name="responsibilities" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Allocate to Site</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Update CIS Modal -->
@if(auth()->user()->canManageCompanyUsers())
    <div class="modal fade" id="updateCISModal" tabindex="-1" aria-labelledby="updateCISModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="updateCISModalLabel">Update CIS Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('employees.update-cis', $employee) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body bg-white">
                        <div class="mb-3">
                            <label for="cis_number" class="form-label text-dark fw-bold">CIS Registration Number</label>
                            <input type="text" class="form-control bg-white text-dark border" id="cis_number" name="cis_number" 
                                   value="{{ $cisData['registration_number'] }}" 
                                   placeholder="e.g., 123/AB12345">
                            <div class="form-text text-muted">Enter the CIS registration number if the employee is registered.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cis_status" class="form-label text-dark fw-bold">Verification Status</label>
                            <select class="form-select bg-white text-dark border" id="cis_status" name="cis_status">
                                <option value="pending" {{ $cisData['verification_status'] === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="verified" {{ $cisData['verification_status'] === 'verified' ? 'selected' : '' }}>Verified</option>
                                <option value="rejected" {{ $cisData['verification_status'] === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="not_registered" {{ $cisData['verification_status'] === 'not_registered' ? 'selected' : '' }}>Not Registered</option>
                            </select>
                        </div>
                        
                        <div class="alert alert-warning bg-warning-subtle border-warning text-dark">
                            <small>
                                <strong>Note:</strong> CIS deductions and payment tracking features are coming soon. 
                                This information is currently for record-keeping purposes only.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update CIS Info</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Add CIS Payment Modal -->
@if(auth()->user()->canManageProjects())
    <div class="modal fade" id="addCisPaymentModal" tabindex="-1" aria-labelledby="addCisPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="addCisPaymentModalLabel">Add CIS Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('cis.payments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                    <div class="modal-body bg-white">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="payment_date" class="form-label text-dark fw-bold">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control bg-white text-dark border" id="payment_date" name="payment_date" 
                                       value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="payment_reference" class="form-label text-dark fw-bold">Payment Reference</label>
                                <input type="text" class="form-control bg-white text-dark border" id="payment_reference" name="payment_reference" 
                                       placeholder="e.g., PAY-001">
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="period_start" class="form-label text-dark fw-bold">Period Start <span class="text-danger">*</span></label>
                                <input type="date" class="form-control bg-white text-dark border" id="period_start" name="period_start" required>
                            </div>
                            <div class="col-md-6">
                                <label for="period_end" class="form-label text-dark fw-bold">Period End <span class="text-danger">*</span></label>
                                <input type="date" class="form-control bg-white text-dark border" id="period_end" name="period_end" required>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="gross_amount" class="form-label text-dark fw-bold">Gross Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">£</span>
                                    <input type="number" class="form-control bg-white text-dark border" id="gross_amount" name="gross_amount" 
                                           step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="materials_cost" class="form-label text-dark fw-bold">Materials Cost</label>
                                <div class="input-group">
                                    <span class="input-group-text">£</span>
                                    <input type="number" class="form-control bg-white text-dark border" id="materials_cost" name="materials_cost" 
                                           step="0.01" min="0" value="0">
                                </div>
                                <div class="form-text text-muted">Materials are not subject to CIS deduction</div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="project_id" class="form-label text-dark fw-bold">Project</label>
                                <select class="form-select bg-white text-dark border" id="project_id" name="project_id">
                                    <option value="">Select project...</option>
                                    @foreach(\App\Models\Project::forCompany()->orderBy('name')->get() as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="other_deductions" class="form-label text-dark fw-bold">Other Deductions</label>
                                <div class="input-group">
                                    <span class="input-group-text">£</span>
                                    <input type="number" class="form-control bg-white text-dark border" id="other_deductions" name="other_deductions" 
                                           step="0.01" min="0" value="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <label for="description" class="form-label text-dark fw-bold">Description</label>
                            <textarea class="form-control bg-white text-dark border" id="description" name="description" rows="2" 
                                      placeholder="Brief description of work performed"></textarea>
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <small>
                                <strong>CIS Rate:</strong> {{ $employee->cis_status === 'verified' ? '20%' : '30%' }} will be automatically applied based on employee's verification status.
                                <br><strong>Net Payment:</strong> Will be calculated as Gross Amount - CIS Deduction - Other Deductions.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Record Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@section('scripts')
<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this employee? This action cannot be undone.')) {
        // Create a form to submit DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("employees.destroy", $employee) }}';
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Auto-set allocation start date to today
document.getElementById('allocated_from').valueAsDate = new Date();
</script>
@endsection

<style>
.avatar-lg {
    width: 80px;
    height: 80px;
}

.card-header-tabs {
    border-bottom: 0;
    margin-bottom: -1px;
}

.card-header-tabs .nav-link {
    border: 1px solid transparent;
    border-top-left-radius: 0.375rem;
    border-top-right-radius: 0.375rem;
}

.card-header-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.detail-group {
    margin-bottom: 1rem;
}

.detail-label {
    font-weight: 600;
    color: #6b7280;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
    display: block;
}

.detail-value {
    color: #1f2937;
    font-size: 0.95rem;
}

.detail-value a {
    color: #4f46e5;
    text-decoration: none;
}

.detail-value a:hover {
    text-decoration: underline;
}

/* Page spacing improvements */
.page-header {
    padding-bottom: 1rem;
    border-bottom: 1px solid #e9ecef;
}

.page-header .page-title {
    font-size: 2rem;
    font-weight: 600;
}

.page-header .page-subtitle {
    font-size: 1.1rem;
    color: #6c757d;
}

/* Stats cards spacing */
.row.mb-5.mt-4 {
    padding-top: 1.5rem;
}

/* Tab content spacing */
.tab-content {
    min-height: 400px;
}

.tab-pane {
    padding-top: 1rem;
}

/* Card improvements */
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    padding: 1rem 1.25rem;
}

/* Stats cards hover effect */
.card.bg-primary:hover,
.card.bg-success:hover,
.card.bg-warning:hover,
.card.bg-info:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease-in-out;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Modal fixes - force visibility and proper styling */
.modal {
    z-index: 1060 !important;
}

.modal-backdrop {
    z-index: 1055 !important;
    background-color: rgba(0, 0, 0, 0.5) !important;
}

.modal-content {
    background-color: #ffffff !important;
    border: 1px solid #dee2e6 !important;
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1) !important;
    opacity: 1 !important;
}

.modal-header {
    background-color: #f8f9fa !important;
    border-bottom: 1px solid #dee2e6 !important;
    color: #212529 !important;
}

.modal-body {
    background-color: #ffffff !important;
    color: #212529 !important;
}

.modal-footer {
    background-color: #f8f9fa !important;
    border-top: 1px solid #dee2e6 !important;
}

/* Force form elements to be visible */
.modal .form-control,
.modal .form-select {
    background-color: #ffffff !important;
    border: 1px solid #ced4da !important;
    color: #495057 !important;
    opacity: 1 !important;
}

.modal .form-control:focus,
.modal .form-select:focus {
    background-color: #ffffff !important;
    border-color: #86b7fe !important;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
    color: #495057 !important;
}

.modal .form-label {
    color: #212529 !important;
    opacity: 1 !important;
}

.modal .form-text {
    color: #6c757d !important;
    opacity: 1 !important;
}

.modal .alert {
    opacity: 1 !important;
}

.modal .btn {
    opacity: 1 !important;
}

@media print {
    .btn-group, .modal, .page-header .col-lg-4 {
        display: none !important;
    }
    
    .card-header-tabs {
        display: none !important;
    }
    
    .tab-pane {
        display: block !important;
        opacity: 1 !important;
    }
}
</style>
@endsection