@extends('layouts.app')

@section('title', 'Operative Invoices')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Operative Invoices</h1>
        <p class="text-muted mb-0">Review and approve operative invoices</p>
    </div>
</div>

<!-- Invoice Type Tabs (only show for users who can manage both types) -->
@if(auth()->user()->canManageProjects())
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white border-bottom-0">
        <ul class="nav nav-pills nav-fill" id="invoiceTypeTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link" href="{{ route('invoices.index', ['type' => 'client']) }}" role="tab">
                    <i class="bi bi-building me-2"></i>
                    <span class="tab-text">Client Invoices</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link active" href="{{ route('invoices.index', ['type' => 'operative']) }}" role="tab">
                    <i class="bi bi-people me-2"></i>
                    <span class="tab-text">Operative Invoices</span>
                </a>
            </li>
        </ul>
    </div>
</div>
@endif

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="metric-icon bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-receipt text-primary fs-4"></i>
                </div>
                <h3 class="h4 mb-2">{{ $operativeInvoices->total() }}</h3>
                <p class="text-muted mb-0">Total Invoices</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="metric-icon bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-clock text-warning fs-4"></i>
                </div>
                <h3 class="h4 mb-2">{{ $operativeInvoices->where('status', 'submitted')->count() }}</h3>
                <p class="text-muted mb-0">Awaiting Approval</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="metric-icon bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-check-circle text-success fs-4"></i>
                </div>
                <h3 class="h4 mb-2">{{ $operativeInvoices->where('status', 'approved')->count() }}</h3>
                <p class="text-muted mb-0">Approved</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="metric-icon bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-currency-pound text-info fs-4"></i>
                </div>
                <h3 class="h4 mb-2">£{{ number_format($operativeInvoices->sum('net_amount'), 2) }}</h3>
                <p class="text-muted mb-0">Total Value</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('invoices.index') }}" class="row g-3">
            <input type="hidden" name="type" value="operative">
            
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Invoice number or operative name">
            </div>
            
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            
            <div class="col-md-3">
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
                <label>&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Invoices Table -->
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">
            <i class="bi bi-receipt me-2"></i>Operative Invoices
        </h5>
    </div>
    <div class="card-body">
        @if($operativeInvoices->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice #</th>
                            <th>Operative</th>
                            <th>Site</th>
                            <th>Week Period</th>
                            <th>Hours</th>
                            <th>Gross Amount</th>
                            <th>CIS Deduction</th>
                            <th>Net Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($operativeInvoices as $invoice)
                            <tr>
                                <td>
                                    <strong>{{ $invoice->invoice_number }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                            <i class="bi bi-person text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $invoice->operative->name }}</div>
                                            <small class="text-muted">{{ $invoice->operative->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $invoice->site->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <div class="text-nowrap">
                                        {{ $invoice->week_starting->format('M d') }} - 
                                        {{ $invoice->week_ending->format('M d, Y') }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $invoice->total_hours }}h</span>
                                </td>
                                <td class="text-end">
                                    <strong>£{{ number_format($invoice->gross_amount, 2) }}</strong>
                                </td>
                                <td class="text-end text-warning">
                                    @if($invoice->cis_applicable)
                                        £{{ number_format($invoice->cis_deduction, 2) }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">£{{ number_format($invoice->net_amount, 2) }}</strong>
                                </td>
                                <td>
                                    @switch($invoice->status)
                                        @case('submitted')
                                            <span class="badge bg-warning">Awaiting Approval</span>
                                            @break
                                        @case('approved')
                                            <span class="badge bg-success">Approved</span>
                                            @break
                                        @case('rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                            @break
                                        @case('paid')
                                            <span class="badge bg-info">Paid</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($invoice->status) }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('invoices.operative.show', $invoice->id) }}" 
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        @if($invoice->status === 'submitted')
                                            @if(auth()->user()->canManageOperativeInvoices() || $invoice->manager_id === auth()->id())
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        data-bs-toggle="modal" data-bs-target="#approveModal{{ $invoice->id }}"
                                                        title="Approve Invoice">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        data-bs-toggle="modal" data-bs-target="#rejectModal{{ $invoice->id }}"
                                                        title="Reject Invoice">
                                                    <i class="bi bi-x-lg"></i>
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
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $operativeInvoices->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-receipt text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-muted">No Operative Invoices Found</h4>
                <p class="text-muted">No operative invoices match your current filters.</p>
            </div>
        @endif
    </div>
</div>

<!-- Approval/Rejection Modals -->
@foreach($operativeInvoices as $invoice)
    @if($invoice->status === 'submitted')
        <!-- Approve Modal -->
        <div class="modal fade" id="approveModal{{ $invoice->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Approve Invoice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('invoices.operative.approve', $invoice->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <p>Are you sure you want to approve invoice <strong>{{ $invoice->invoice_number }}</strong> from <strong>{{ $invoice->operative->name }}</strong>?</p>
                            
                            <div class="mb-3">
                                <label for="approval_notes{{ $invoice->id }}" class="form-label">Approval Notes (Optional)</label>
                                <textarea class="form-control" id="approval_notes{{ $invoice->id }}" name="approval_notes" rows="3" 
                                          placeholder="Add any notes about this approval..."></textarea>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Invoice Details:</strong><br>
                                Week: {{ $invoice->week_starting->format('M d') }} - {{ $invoice->week_ending->format('M d, Y') }}<br>
                                Hours: {{ $invoice->total_hours }}h<br>
                                Net Amount: £{{ number_format($invoice->net_amount, 2) }}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg me-2"></i>Approve Invoice
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Reject Modal -->
        <div class="modal fade" id="rejectModal{{ $invoice->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Invoice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('invoices.operative.reject', $invoice->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <p>Please provide a reason for rejecting invoice <strong>{{ $invoice->invoice_number }}</strong> from <strong>{{ $invoice->operative->name }}</strong>.</p>
                            
                            <div class="mb-3">
                                <label for="rejection_reason{{ $invoice->id }}" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="rejection_reason{{ $invoice->id }}" name="rejection_reason" rows="3" 
                                          placeholder="Explain why this invoice is being rejected..." required></textarea>
                            </div>
                            
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Note:</strong> The operative will be notified of the rejection and can resubmit with corrections.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-x-lg me-2"></i>Reject Invoice
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection

@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 0.875rem;
}

.metric-icon {
    width: 60px;
    height: 60px;
}

.nav-pills .nav-link {
    border-radius: 0.5rem;
    margin: 0 0.25rem;
    transition: all 0.2s ease-in-out;
}

.nav-pills .nav-link:hover {
    background-color: rgba(13, 110, 253, 0.1);
}

.nav-pills .nav-link.active {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
}

.btn-group .btn {
    border-radius: 0.25rem;
    margin-right: 0.25rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
@endpush
