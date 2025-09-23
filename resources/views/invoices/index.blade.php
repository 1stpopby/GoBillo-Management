@extends('layouts.app')

@section('title', 'Invoices')

@push('styles')
<style>
    /* Table Styles */
    .table th {
        background-color: #f8f9fa;
        border-top: none;
        font-weight: 600;
        color: #495057;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 1rem 0.75rem;
    }
    
    .table td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    /* Enhanced Action Button Styles */
    .btn-sm {
        padding: 0.35rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 4px;
        transition: all 0.2s ease-in-out;
    }
    
    .btn-sm:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    }
    
    .btn-info {
        background-color: #0dcaf0;
        border-color: #0dcaf0;
    }
    
    .btn-info:hover {
        background-color: #0bacce;
        border-color: #0bacce;
    }
    
    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
    }
    
    .btn-warning:hover {
        background-color: #e0a800;
        border-color: #e0a800;
    }
    
    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }
    
    .btn-secondary:hover {
        background-color: #5c636a;
        border-color: #5c636a;
    }
    
    .dropdown-menu {
        min-width: 180px;
        border-radius: 8px;
        border: 1px solid rgba(0,0,0,0.1);
    }
    
    .dropdown-item {
        padding: 0.5rem 1rem;
        transition: background-color 0.2s ease;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    
    .dropdown-item i {
        width: 20px;
    }
    
    /* Ensure icons display properly */
    .bi::before {
        display: inline-block;
        vertical-align: middle;
    }
    
    /* Badge Styles */
    .badge {
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0.5rem 0.75rem;
    }
    
    /* Card Styles */
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.075);
        border-radius: 0.75rem;
    }
    
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #e9ecef;
        border-radius: 0.75rem 0.75rem 0 0 !important;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Invoices</h1>
        <p class="text-muted mb-0">Manage your company invoices and billing</p>
    </div>
    @if(auth()->user()->canManageProjects())
        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Invoice
        </a>
    @endif
</div>

<!-- Invoice Type Tabs -->
@if(auth()->user()->canManageProjects())
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white border-bottom-0">
        <ul class="nav nav-pills nav-fill" id="invoiceTypeTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ (!isset($invoiceType) || $invoiceType === 'client') ? 'active' : '' }}" 
                   href="{{ route('invoices.index', ['type' => 'client']) }}" role="tab">
                    <i class="bi bi-building me-2"></i>
                    <span class="tab-text">Client Invoices</span>
                </a>
            </li>
            @if(auth()->user()->canManageOperativeInvoices())
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ (isset($invoiceType) && $invoiceType === 'operative') ? 'active' : '' }}" 
                   href="{{ route('invoices.index', ['type' => 'operative']) }}" role="tab">
                    <i class="bi bi-people me-2"></i>
                    <span class="tab-text">Operative Invoices</span>
                </a>
            </li>
            @endif
        </ul>
    </div>
</div>
@endif

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-left-primary h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Invoices</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $invoices->total() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-receipt text-gray-300" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-left-success h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Paid</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $invoices->where('status', 'paid')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-check-circle text-gray-300" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-left-warning h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $invoices->whereIn('status', ['draft', 'sent'])->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-clock text-gray-300" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-left-danger h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Overdue</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $invoices->where('status', 'overdue')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-exclamation-triangle text-gray-300" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('invoices.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Invoice number or client...">
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="client_id" class="form-label">Client</label>
                <select class="form-select" id="client_id" name="client_id">
                    <option value="">All Clients</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" class="form-control" id="date_from" name="date_from" 
                       value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">To Date</label>
                <input type="date" class="form-control" id="date_to" name="date_to" 
                       value="{{ request('date_to') }}">
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Invoices Table -->
<div class="card">
    <div class="card-body">
        @if($invoices->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Client</th>
                            <th>Project</th>
                            <th>Issue Date</th>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                            <tr>
                                <td>
                                    <a href="{{ route('invoices.show', $invoice) }}" class="text-decoration-none">
                                        <strong>{{ $invoice->invoice_number }}</strong>
                                    </a>
                                </td>
                                <td>{{ $invoice->client->name }}</td>
                                <td>
                                    @if($invoice->project)
                                        <a href="{{ route('projects.show', $invoice->project) }}" class="text-decoration-none">
                                            {{ $invoice->project->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">No project</span>
                                    @endif
                                </td>
                                <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                                <td>
                                    {{ $invoice->due_date->format('M d, Y') }}
                                    @if($invoice->isOverdue())
                                        <small class="text-danger">({{ abs($invoice->days_until_due) }} days overdue)</small>
                                    @elseif($invoice->status !== 'paid' && $invoice->days_until_due <= 7)
                                        <small class="text-warning">(Due in {{ $invoice->days_until_due }} days)</small>
                                    @endif
                                </td>
                                <td>
                                    <strong>£{{ number_format($invoice->total_amount, 2) }}</strong>
                                    <small class="text-muted">{{ $invoice->currency }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $invoice->status_color }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 justify-content-start">
                                        <a href="{{ route('invoices.show', $invoice) }}" 
                                           class="btn btn-sm btn-info text-white" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="View Invoice">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        
                                        @if(auth()->user()->canManageProjects() && $invoice->status !== 'paid')
                                            <a href="{{ route('invoices.edit', $invoice) }}" 
                                               class="btn btn-sm btn-warning text-white"
                                               data-bs-toggle="tooltip" 
                                               data-bs-placement="top" 
                                               title="Edit Invoice">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        @endif
                                        
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" 
                                                    type="button" 
                                                    data-bs-toggle="dropdown" 
                                                    aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                                <li>
                                                    <a href="{{ route('invoices.pdf', $invoice) }}" 
                                                       class="dropdown-item">
                                                        <i class="bi bi-file-pdf text-danger me-2"></i> Download PDF
                                                    </a>
                                                </li>
                                                @if(auth()->user()->canManageProjects())
                                                    @if($invoice->status === 'draft')
                                                        <li>
                                                            <form method="POST" action="{{ route('invoices.send', $invoice) }}" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="bi bi-send text-primary me-2"></i> Send Invoice
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                    @if(in_array($invoice->status, ['sent', 'overdue']))
                                                        <li>
                                                            <form method="POST" action="{{ route('invoices.mark-paid', $invoice) }}" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item"
                                                                        onclick="return confirm('Are you sure you want to mark this invoice as paid?')">
                                                                    <i class="bi bi-check-circle text-success me-2"></i> Mark as Paid
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <form method="POST" action="{{ route('invoices.duplicate', $invoice) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="bi bi-files text-info me-2"></i> Duplicate
                                                            </button>
                                                        </form>
                                                    </li>
                                                    @if($invoice->status !== 'paid')
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" 
                                                                  class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="bi bi-trash"></i> Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <!-- Mark as Paid Modal -->
                            @if(in_array($invoice->status, ['sent', 'overdue']))
                                <div class="modal fade" id="markPaidModal{{ $invoice->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('invoices.mark-paid', $invoice) }}">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Mark Invoice as Paid</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Mark invoice <strong>{{ $invoice->invoice_number }}</strong> as paid?</p>
                                                    <div class="mb-3">
                                                        <label for="payment_method{{ $invoice->id }}" class="form-label">Payment Method</label>
                                                        <select class="form-select" name="payment_method" id="payment_method{{ $invoice->id }}">
                                                            <option value="">Select method...</option>
                                                            <option value="cash">Cash</option>
                                                            <option value="check">Check</option>
                                                            <option value="credit_card">Credit Card</option>
                                                            <option value="bank_transfer">Bank Transfer</option>
                                                            <option value="other">Other</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="payment_reference{{ $invoice->id }}" class="form-label">Reference</label>
                                                        <input type="text" class="form-control" name="payment_reference" 
                                                               id="payment_reference{{ $invoice->id }}" 
                                                               placeholder="Transaction ID, check number, etc.">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success">Mark as Paid</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $invoices->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-receipt text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3">No Invoices Found</h4>
                <p class="text-muted">You haven't created any invoices yet.</p>
                @if(auth()->user()->canManageProjects())
                    <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create Your First Invoice
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<style>
.border-left-primary { border-left: 0.25rem solid var(--gobillo-primary) !important; }
.border-left-success { border-left: 0.25rem solid var(--gobillo-success) !important; }
.border-left-warning { border-left: 0.25rem solid var(--gobillo-warning) !important; }
.border-left-danger { border-left: 0.25rem solid var(--gobillo-danger) !important; }
</style>
@endsection

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

.nav-pills .nav-link {
    border-radius: 0.5rem;
    font-weight: 500;
    padding: 0.75rem 1.5rem;
}

.nav-pills .nav-link.active {
    background-color: #0066cc;
    border-color: #0066cc;
}

.form-control, .form-select {
    border-radius: 0.5rem;
    border: 1px solid #ced4da;
    padding: 0.625rem 0.875rem;
}

.form-control:focus, .form-select:focus {
    border-color: #0066cc;
    box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
}

.btn {
    border-radius: 0.5rem;
    font-weight: 500;
    padding: 0.625rem 1.25rem;
}

.btn-primary {
    background-color: #0066cc;
    border-color: #0066cc;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.text-muted {
    color: #6c757d !important;
}

.table-responsive {
    border-radius: 0.75rem;
}

.modal-content {
    border-radius: 1rem;
    border: none;
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
}

.modal-header {
    border-bottom: 1px solid #e9ecef;
    border-radius: 1rem 1rem 0 0;
}

.modal-footer {
    border-top: 1px solid #e9ecef;
    border-radius: 0 0 1rem 1rem;
}
</style> 