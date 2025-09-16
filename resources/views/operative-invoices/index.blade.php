@extends('layouts.app')

@section('title', 'My Invoices')

@push('styles')
<style>
    /* Mobile-first invoice styles */
    .mobile-invoice-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        margin-bottom: 1rem;
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .mobile-invoice-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.12);
    }
    
    .mobile-invoice-header {
        padding: 1rem;
        border-bottom: 1px solid #f1f3f4;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .mobile-invoice-body {
        padding: 1rem;
    }
    
    .mobile-invoice-footer {
        padding: 0.75rem 1rem;
        background: #f8f9fa;
        border-top: 1px solid #f1f3f4;
        border-radius: 0 0 12px 12px;
    }
    
    .invoice-number {
        font-weight: 600;
        color: #495057;
        font-size: 1rem;
    }
    
    .invoice-amount {
        font-weight: 700;
        font-size: 1.1rem;
        color: #28a745;
    }
    
    .invoice-detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    
    .invoice-detail-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 500;
    }
    
    .invoice-detail-value {
        font-size: 0.875rem;
        color: #495057;
        text-align: right;
        flex: 1;
        margin-left: 1rem;
    }
    
    .mobile-filters {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }
    
    .filter-toggle {
        display: none;
    }
    
    @media (max-width: 768px) {
        .page-header {
            padding: 1rem 0;
        }
        
        .page-title {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }
        
        .page-subtitle {
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
        
        .filter-toggle {
            display: block;
        }
        
        .filter-content {
            display: none;
            margin-top: 1rem;
        }
        
        .filter-content.show {
            display: block;
        }
        
        .btn-create-mobile {
            position: fixed;
            bottom: 80px;
            right: 1rem;
            z-index: 1000;
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .container-fluid {
            padding: 0.5rem;
        }
    }
    
    .avatar-xs {
        width: 32px;
        height: 32px;
        font-size: 0.75rem;
    }
    
    .status-badge-lg {
        padding: 0.5rem 0.75rem;
        font-size: 0.8rem;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-8">
                <h1 class="page-title">My Invoices</h1>
                <p class="page-subtitle d-none d-md-block">Manage your invoices and track payments</p>
            </div>
            <div class="col-4 text-end d-none d-md-block">
                <a href="{{ route('operative-invoices.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Create Invoice
                </a>
            </div>
        </div>
    </div>
    
    <!-- Mobile Create Button -->
    <a href="{{ route('operative-invoices.create') }}" class="btn btn-primary btn-create-mobile d-md-none">
        <i class="bi bi-plus-circle me-2"></i>Create
    </a>

    <!-- Filters -->
    <div class="mobile-filters d-md-none">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="bi bi-funnel me-2"></i>Filters
            </h6>
            <button type="button" class="btn btn-sm btn-outline-secondary filter-toggle" onclick="toggleFilters()">
                <i class="bi bi-chevron-down" id="filterIcon"></i>
            </button>
        </div>
        <div class="filter-content" id="filterContent">
            <form method="GET" class="row g-3">
                <div class="col-12">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-lg">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-lg" value="{{ request('date_from') }}">
                </div>
                <div class="col-6">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-lg" value="{{ request('date_to') }}">
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill btn-lg">
                        <i class="bi bi-search me-2"></i>Filter
                    </button>
                    <a href="{{ route('operative-invoices.index') }}" class="btn btn-outline-secondary flex-fill btn-lg">
                        <i class="bi bi-x-circle me-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Desktop Filters -->
    <div class="card mb-4 d-none d-md-block">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                    <a href="{{ route('operative-invoices.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Mobile Invoice Cards -->
    <div class="d-md-none">
        @if($invoices->count() > 0)
            @foreach($invoices as $invoice)
                <div class="mobile-invoice-card">
                    <div class="mobile-invoice-header">
                        <div>
                            <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
                            <small class="text-muted">{{ $invoice->created_at->format('M j, Y') }}</small>
                        </div>
                        @php
                            $statusColors = [
                                'draft' => 'secondary',
                                'submitted' => 'warning',
                                'approved' => 'success',
                                'paid' => 'primary',
                                'rejected' => 'danger'
                            ];
                            $statusColor = $statusColors[$invoice->status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $statusColor }} status-badge-lg">{{ ucfirst($invoice->status) }}</span>
                    </div>
                    
                    <div class="mobile-invoice-body">
                        <div class="invoice-detail-row">
                            <span class="invoice-detail-label">
                                <i class="bi bi-person me-1"></i>Manager
                            </span>
                            <div class="invoice-detail-value">
                                <div class="d-flex align-items-center justify-content-end">
                                    <div class="avatar-xs me-2">
                                        <div class="avatar-title bg-primary bg-opacity-10 text-primary rounded-circle">
                                            {{ substr($invoice->manager->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <span>{{ $invoice->manager->name }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="invoice-detail-row">
                            <span class="invoice-detail-label">
                                <i class="bi bi-geo-alt me-1"></i>Site
                            </span>
                            <div class="invoice-detail-value">
                                <strong>{{ $invoice->site->name }}</strong>
                                @if($invoice->project)
                                    <br><small class="text-muted">{{ $invoice->project->name }}</small>
                                @else
                                    <br><small class="text-muted">No specific project</small>
                                @endif
                            </div>
                        </div>
                        
                        <div class="invoice-detail-row">
                            <span class="invoice-detail-label">
                                <i class="bi bi-calendar-week me-1"></i>Week Period
                            </span>
                            <span class="invoice-detail-value">{{ $invoice->week_period }}</span>
                        </div>
                        
                        <div class="invoice-detail-row">
                            <span class="invoice-detail-label">
                                <i class="bi bi-currency-pound me-1"></i>Amount
                            </span>
                            <div class="invoice-detail-value">
                                <div class="invoice-amount">£{{ number_format($invoice->net_amount, 2) }}</div>
                                @if($invoice->cis_applicable)
                                    <small class="text-muted">CIS: -£{{ number_format($invoice->cis_deduction, 2) }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="mobile-invoice-footer">
                        <a href="{{ route('operative-invoices.show', $invoice) }}" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-eye me-2"></i>View Invoice Details
                        </a>
                    </div>
                </div>
            @endforeach
            
            <!-- Mobile Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $invoices->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-receipt text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 text-muted">No invoices found</h5>
                <p class="text-muted mb-4">Create your first invoice to get started</p>
                <a href="{{ route('operative-invoices.create') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle me-2"></i>Create Invoice
                </a>
            </div>
        @endif
    </div>

    <!-- Desktop Invoice Table -->
    <div class="card d-none d-md-block">
        <div class="card-header">
            <h5 class="card-title mb-0">Invoices</h5>
        </div>
        <div class="card-body">
            @if($invoices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice #</th>
                                <th>Manager</th>
                                <th>Site/Project</th>
                                <th>Week Period</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                                <tr>
                                    <td>
                                        <strong>#{{ $invoice->invoice_number }}</strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <div class="avatar-title bg-primary bg-opacity-10 text-primary rounded-circle">
                                                    {{ substr($invoice->manager->name, 0, 1) }}
                                                </div>
                                            </div>
                                            {{ $invoice->manager->name }}
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $invoice->site->name }}</strong><br>
                                            @if($invoice->project)
                                                <small class="text-muted">{{ $invoice->project->name }}</small>
                                            @else
                                                <small class="text-muted">No specific project</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <small>{{ $invoice->week_period }}</small>
                                    </td>
                                    <td>
                                        <strong>£{{ number_format($invoice->net_amount, 2) }}</strong>
                                        @if($invoice->cis_applicable)
                                            <br><small class="text-muted">CIS: -£{{ number_format($invoice->cis_deduction, 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $invoice->status_color }}">{{ ucfirst($invoice->status) }}</span>
                                    </td>
                                    <td>
                                        <small>{{ $invoice->created_at->format('M j, Y') }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('operative-invoices.show', $invoice) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye me-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Desktop Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $invoices->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">No invoices found</h5>
                    <p class="text-muted">Create your first invoice to get started</p>
                    <a href="{{ route('operative-invoices.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Invoice
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleFilters() {
    const filterContent = document.getElementById('filterContent');
    const filterIcon = document.getElementById('filterIcon');
    
    if (filterContent.classList.contains('show')) {
        filterContent.classList.remove('show');
        filterIcon.classList.remove('bi-chevron-up');
        filterIcon.classList.add('bi-chevron-down');
    } else {
        filterContent.classList.add('show');
        filterIcon.classList.remove('bi-chevron-down');
        filterIcon.classList.add('bi-chevron-up');
    }
}

// Add touch feedback for mobile cards
document.addEventListener('DOMContentLoaded', function() {
    const mobileCards = document.querySelectorAll('.mobile-invoice-card');
    
    mobileCards.forEach(card => {
        card.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.98)';
        });
        
        card.addEventListener('touchend', function() {
            this.style.transform = 'translateY(-1px)';
        });
        
        card.addEventListener('touchcancel', function() {
            this.style.transform = 'translateY(-1px)';
        });
    });
    
    // Prevent double-tap zoom on buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('touchend', function(e) {
            e.preventDefault();
            this.click();
        });
    });
});
</script>
@endpush

