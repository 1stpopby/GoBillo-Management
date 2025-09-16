@extends('layouts.app')

@section('title', 'Operative Invoices - Payment Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Commercial</li>
                    <li class="breadcrumb-item active">Operative Invoices</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">
                <i class="bi bi-person-check text-primary me-2"></i>
                Operative Invoices - Payment Management
            </h1>
            <p class="text-muted mb-0">Review and mark approved operative invoices as paid</p>
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-gradient rounded-circle p-3">
                                <i class="bi bi-receipt text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Awaiting Payment</div>
                            <div class="h4 mb-0">{{ $operativeInvoices->total() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-gradient rounded-circle p-3">
                                <i class="bi bi-currency-pound text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Total Gross Amount</div>
                            <div class="h4 mb-0">£{{ number_format($totalAmount, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-gradient rounded-circle p-3">
                                <i class="bi bi-percent text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Total CIS Deduction</div>
                            <div class="h4 mb-0">£{{ number_format($totalCisDeduction, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-gradient rounded-circle p-3">
                                <i class="bi bi-cash-stack text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Total Net Payment</div>
                            <div class="h4 mb-0">£{{ number_format($totalNetAmount, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white border-bottom-0">
            <h6 class="card-title mb-0">
                <i class="bi bi-funnel me-2"></i>Filters
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.operative-invoices.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Operative, site, project...">
                </div>
                <div class="col-md-2">
                    <label for="operative_id" class="form-label">Operative</label>
                    <select class="form-select" id="operative_id" name="operative_id">
                        <option value="">All Operatives</option>
                        @foreach($operatives as $operative)
                            <option value="{{ $operative->id }}" {{ request('operative_id') == $operative->id ? 'selected' : '' }}>
                                {{ $operative->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
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
                <div class="col-md-2">
                    <label for="project_id" class="form-label">Project</label>
                    <select class="form-select" id="project_id" name="project_id">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label for="date_from" class="form-label">From</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-md-1">
                    <label for="date_to" class="form-label">To</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('admin.operative-invoices.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom-0">
            <h6 class="card-title mb-0">
                <i class="bi bi-table me-2"></i>Approved Invoices Awaiting Payment
            </h6>
        </div>
        <div class="card-body p-0">
            @if($operativeInvoices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice</th>
                                <th>Operative</th>
                                <th>Site & Project</th>
                                <th>Week Period</th>
                                <th>Hours</th>
                                <th class="text-end">Gross Amount</th>
                                <th class="text-end">CIS Deduction</th>
                                <th class="text-end">Net Amount</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($operativeInvoices as $invoice)
                                <tr>
                                    <td>
                                        <div class="fw-medium">{{ $invoice->invoice_number }}</div>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>
                                            {{ $invoice->created_at->format('M j, Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-gradient rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <span class="text-white small fw-bold">
                                                    {{ strtoupper(substr($invoice->operative->name, 0, 2)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="fw-medium">{{ $invoice->operative->name }}</div>
                                                <small class="text-muted">Operative</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $invoice->site->name }}</div>
                                        @if($invoice->project)
                                            <small class="text-muted">{{ $invoice->project->name }}</small>
                                        @endif
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
                                        <span class="badge bg-success">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('invoices.operative.show', $invoice->id) }}" 
                                               class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    onclick="markAsPaid({{ $invoice->id }})" title="Mark as Paid">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-white border-top-0">
                    {{ $operativeInvoices->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                    <h5 class="text-muted">No Approved Invoices Found</h5>
                    <p class="text-muted">There are currently no approved operative invoices awaiting payment.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Mark as Paid Confirmation Modal -->
<div class="modal fade" id="markPaidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle text-success me-2"></i>
                    Mark Invoice as Paid
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to mark this invoice as <strong>paid</strong>?</p>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    This action will update the invoice status and record the payment timestamp.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="markPaidForm" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-2"></i>Mark as Paid
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 0.75rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #374151;
    background-color: #f8fafc !important;
}

.table td {
    vertical-align: middle;
    border-color: #e5e7eb;
}

.btn-group .btn {
    border-radius: 0.375rem !important;
    margin-right: 0.25rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.card {
    border: 1px solid #e5e7eb;
}

.card-header {
    background-color: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
}
</style>
@endpush

@push('scripts')
<script>
function markAsPaid(invoiceId) {
    const modal = new bootstrap.Modal(document.getElementById('markPaidModal'));
    const form = document.getElementById('markPaidForm');
    form.action = `/admin/operative-invoices/${invoiceId}/mark-paid`;
    modal.show();
}

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
