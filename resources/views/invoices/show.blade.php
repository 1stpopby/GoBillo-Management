@extends('layouts.app')

@section('title', 'Invoice #' . ($invoice->invoice_number ?? $invoice->id))

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Invoice #{{ $invoice->invoice_number ?? $invoice->id }}</h1>
            <p class="text-muted mb-0">
                <i class="bi bi-calendar me-1"></i>
                Issued {{ $invoice->issue_date->format('M d, Y') }} • 
                Due {{ $invoice->due_date->format('M d, Y') }}
                @if($invoice->isOverdue())
                    <span class="text-danger ms-2">
                        <i class="bi bi-exclamation-triangle"></i>
                        {{ abs($invoice->days_until_due) }} days overdue
                    </span>
                @elseif($invoice->status !== 'paid' && $invoice->days_until_due <= 7)
                    <span class="text-warning ms-2">
                        <i class="bi bi-clock"></i>
                        Due in {{ $invoice->days_until_due }} days
                    </span>
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Invoices
            </a>
            <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-outline-primary">
                <i class="bi bi-file-pdf me-1"></i>Download PDF
            </a>
            @can('update', $invoice)
                @if($invoice->status !== 'paid')
                    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>Edit Invoice
                    </a>
                @endif
            @endcan
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Invoice Header Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-receipt me-2"></i>Invoice Details
                            </h5>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-white text-primary fs-6 px-3 py-2">
                                <i class="bi bi-{{ $invoice->status === 'paid' ? 'check-circle' : ($invoice->status === 'sent' ? 'send' : 'file-text') }} me-1"></i>
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Bill From -->
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">
                                <i class="bi bi-building me-1"></i>From
                            </h6>
                            <div class="border-start border-primary border-3 ps-3">
                                <div class="fw-bold text-primary">{{ auth()->user()->company->name }}</div>
                                @if(auth()->user()->company->address)
                                    <div class="text-muted small">{{ auth()->user()->company->address }}</div>
                                @endif
                                @if(auth()->user()->company->email)
                                    <div class="text-muted small">
                                        <i class="bi bi-envelope me-1"></i>{{ auth()->user()->company->email }}
                                    </div>
                                @endif
                                @if(auth()->user()->company->phone)
                                    <div class="text-muted small">
                                        <i class="bi bi-phone me-1"></i>{{ auth()->user()->company->phone }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Bill To -->
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">
                                <i class="bi bi-person-circle me-1"></i>Bill To
                            </h6>
                            <div class="border-start border-success border-3 ps-3">
                                <div class="fw-bold text-success">{{ $invoice->client->name ?? 'Unknown Client' }}</div>
                                @if($invoice->client && $invoice->client->contact_person_email)
                                    <div class="text-muted small">
                                        <i class="bi bi-envelope me-1"></i>{{ $invoice->client->contact_person_email }}
                                    </div>
                                @endif
                                @if($invoice->client && $invoice->client->contact_person_phone)
                                    <div class="text-muted small">
                                        <i class="bi bi-phone me-1"></i>{{ $invoice->client->contact_person_phone }}
                                    </div>
                                @endif
                                @if($invoice->client && $invoice->client->address)
                                    <div class="text-muted small">{{ $invoice->client->address }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($invoice->project)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info d-flex align-items-center">
                                    <i class="bi bi-folder2-open me-2"></i>
                                    <div>
                                        <strong>Project:</strong> 
                                        <a href="{{ route('projects.show', $invoice->project) }}" class="text-decoration-none">
                                            {{ $invoice->project->name }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-ul me-2"></i>Invoice Items
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="border-0">Description</th>
                                    <th class="border-0 text-center">Quantity</th>
                                    <th class="border-0 text-center">Unit</th>
                                    <th class="border-0 text-end">Unit Price</th>
                                    <th class="border-0 text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoice->items as $item)
                                    <tr>
                                        <td class="fw-medium">{{ $item->description }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">{{ number_format($item->quantity, 2) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-muted">{{ $item->unit }}</span>
                                        </td>
                                        <td class="text-end">£{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end fw-bold">£{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox display-4 d-block mb-2 opacity-50"></i>
                                            No items added to this invoice
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Invoice Totals -->
                <div class="card-footer bg-light">
                    <div class="row justify-content-end">
                        <div class="col-md-6">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <td class="border-0 text-muted">Subtotal:</td>
                                    <td class="border-0 text-end">£{{ number_format($invoice->subtotal_amount ?? 0, 2) }}</td>
                                </tr>
                                @if($invoice->tax_rate > 0)
                                    <tr>
                                        <td class="border-0 text-muted">Tax ({{ number_format($invoice->tax_rate, 2) }}%):</td>
                                        <td class="border-0 text-end">£{{ number_format($invoice->tax_amount ?? 0, 2) }}</td>
                                    </tr>
                                @endif
                                @if($invoice->discount_amount > 0)
                                    <tr>
                                        <td class="border-0 text-muted">Discount:</td>
                                        <td class="border-0 text-end text-success">-£{{ number_format($invoice->discount_amount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr class="border-top">
                                    <td class="fw-bold fs-5 text-primary">Total ({{ $invoice->currency }}):</td>
                                    <td class="fw-bold fs-5 text-end text-primary">£{{ number_format($invoice->total_amount ?? 0, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes & Terms -->
            @if($invoice->notes || $invoice->terms)
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-chat-square-text me-2"></i>Additional Information
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($invoice->notes)
                            <div class="mb-3">
                                <h6 class="text-muted mb-2">Notes:</h6>
                                <div class="p-3 bg-light rounded">{{ $invoice->notes }}</div>
                            </div>
                        @endif
                        @if($invoice->terms)
                            <div>
                                <h6 class="text-muted mb-2">Terms & Conditions:</h6>
                                <div class="p-3 bg-light rounded">{{ $invoice->terms }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Invoice Status Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'overdue' ? 'danger' : 'warning') }} text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-{{ $invoice->status === 'paid' ? 'check-circle' : 'clock' }} me-2"></i>
                        Invoice Status
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <span class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'overdue' ? 'danger' : 'warning') }} fs-6 px-3 py-2">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </div>
                    
                    @if($invoice->status === 'paid')
                        <div class="text-success">
                            <i class="bi bi-check-circle display-4"></i>
                            <p class="mt-2 mb-0">Payment Received</p>
                        </div>
                    @elseif($invoice->isOverdue())
                        <div class="text-danger">
                            <i class="bi bi-exclamation-triangle display-4"></i>
                            <p class="mt-2 mb-0">{{ abs($invoice->days_until_due) }} days overdue</p>
                        </div>
                    @else
                        <div class="text-warning">
                            <i class="bi bi-clock display-4"></i>
                            <p class="mt-2 mb-0">
                                @if($invoice->days_until_due > 0)
                                    Due in {{ $invoice->days_until_due }} days
                                @else
                                    Due today
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-outline-primary">
                        <i class="bi bi-file-pdf me-2"></i>Download PDF
                    </a>
                    
                    @can('update', $invoice)
                        @if($invoice->status === 'draft')
                            <form method="POST" action="{{ route('invoices.send', $invoice) }}">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-send me-2"></i>Send to Client
                                </button>
                            </form>
                        @endif
                        
                        @if(in_array($invoice->status, ['sent','overdue']))
                            <form method="POST" action="{{ route('invoices.mark-paid', $invoice) }}" 
                                  onsubmit="return confirm('Mark this invoice as paid?')">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-check-circle me-2"></i>Mark as Paid
                                </button>
                            </form>
                        @endif

                        @if($invoice->status !== 'paid')
                            <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-pencil me-2"></i>Edit Invoice
                            </a>
                        @endif

                        <form method="POST" action="{{ route('invoices.duplicate', $invoice) }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-info w-100">
                                <i class="bi bi-files me-2"></i>Duplicate Invoice
                            </button>
                        </form>
                    @endcan
                </div>
            </div>

            <!-- Invoice Details -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Invoice Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-muted small">Invoice Number</div>
                            <div class="fw-bold">#{{ $invoice->invoice_number ?? $invoice->id }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Currency</div>
                            <div class="fw-bold">{{ $invoice->currency }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Issue Date</div>
                            <div class="fw-bold">{{ $invoice->issue_date->format('M d, Y') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Due Date</div>
                            <div class="fw-bold">{{ $invoice->due_date->format('M d, Y') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Items Count</div>
                            <div class="fw-bold">{{ $invoice->items->count() }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Created</div>
                            <div class="fw-bold">{{ $invoice->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}

.table th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.badge {
    font-weight: 500;
}

.border-3 {
    border-width: 3px !important;
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.075) !important;
}
</style>
@endsection