@extends('layouts.app')

@section('title', 'Payment #' . $payment->payment_number)

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Payment #{{ $payment->payment_number }}</h1>
            <p class="text-muted mb-0">
                <i class="bi bi-calendar me-1"></i>
                {{ $payment->processed_at ? $payment->processed_at->format('M d, Y \a\t H:i') : 'Not processed yet' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Payments
            </a>
            @if(in_array($payment->status, ['pending', 'failed']) && auth()->user()->canManageProjects())
                <a href="{{ route('payments.edit', $payment) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-1"></i>Edit Payment
                </a>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Payment Header Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-credit-card me-2"></i>Payment Details
                            </h5>
                        </div>
                        <div class="col-auto">
                            @php
                                $statusColors = [
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'completed' => 'success',
                                    'failed' => 'danger',
                                    'refunded' => 'secondary',
                                    'cancelled' => 'dark'
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$payment->status] ?? 'secondary' }} fs-6">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="fw-bold">Payment Number:</td>
                                    <td>{{ $payment->payment_number }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Amount:</td>
                                    <td class="fw-bold text-success">£{{ number_format($payment->amount, 2) }}</td>
                                </tr>
                                @if($payment->processing_fee > 0)
                                <tr>
                                    <td class="fw-bold">Processing Fee:</td>
                                    <td class="text-muted">£{{ number_format($payment->processing_fee, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Net Amount:</td>
                                    <td class="fw-bold">£{{ number_format($payment->net_amount, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="fw-bold">Payment Type:</td>
                                    <td>
                                        @if($payment->payment_type == 'full_payment')
                                            <span class="badge bg-success">Full Payment</span>
                                        @elseif($payment->payment_type == 'partial_payment')
                                            <span class="badge bg-warning">Partial Payment</span>
                                        @elseif($payment->payment_type == 'deposit')
                                            <span class="badge bg-info">Deposit</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="fw-bold">Payment Gateway:</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_gateway)) }}</td>
                                </tr>
                                @if($payment->paymentMethod)
                                <tr>
                                    <td class="fw-bold">Payment Method:</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $payment->paymentMethod->name }}</span>
                                    </td>
                                </tr>
                                @endif
                                @if($payment->provider_transaction_id)
                                <tr>
                                    <td class="fw-bold">Transaction ID:</td>
                                    <td><code>{{ $payment->provider_transaction_id }}</code></td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="fw-bold">Currency:</td>
                                    <td>{{ strtoupper($payment->currency) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-receipt me-2"></i>Related Invoice
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Invoice Number:</strong>
                            <div class="mb-2">
                                <a href="{{ route('invoices.show', $payment->invoice) }}" class="text-decoration-none">
                                    {{ $payment->invoice->invoice_number }}
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <strong>Client:</strong>
                            <div class="mb-2">
                                @if($payment->invoice->client)
                                    {{ $payment->invoice->client->display_name }}
                                    @if($payment->invoice->client->is_private_client)
                                        <small class="text-muted d-block">Private Client</small>
                                    @endif
                                @else
                                    <span class="text-muted">No client</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <strong>Project:</strong>
                            <div class="mb-2">
                                @if($payment->invoice->project)
                                    <a href="{{ route('projects.show', $payment->invoice->project) }}" class="text-decoration-none">
                                        {{ $payment->invoice->project->name }}
                                    </a>
                                    <small class="text-muted d-block">{{ ucfirst($payment->invoice->project->status) }}</small>
                                @else
                                    <span class="text-muted">No project</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Invoice Total:</strong>
                            <div class="mb-2">£{{ number_format($payment->invoice->total, 2) }}</div>
                        </div>
                        <div class="col-md-4">
                            <strong>Total Paid:</strong>
                            @php
                                $totalPaid = $payment->invoice->payments()->completed()->sum('amount');
                            @endphp
                            <div class="mb-2">£{{ number_format($totalPaid, 2) }}</div>
                        </div>
                        <div class="col-md-4">
                            <strong>Remaining Balance:</strong>
                            <div class="mb-2 fw-bold">£{{ number_format($payment->invoice->total - $totalPaid, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($payment->notes)
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-chat-text me-2"></i>Notes
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $payment->notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Timeline -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Payment Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <strong>Payment Created</strong>
                                <br><small class="text-muted">{{ $payment->created_at->format('M d, Y \a\t H:i') }}</small>
                            </div>
                        </div>
                        
                        @if($payment->processed_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <strong>Payment Processed</strong>
                                <br><small class="text-muted">{{ $payment->processed_at->format('M d, Y \a\t H:i') }}</small>
                            </div>
                        </div>
                        @endif
                        
                        @if($payment->failed_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <strong>Payment Failed</strong>
                                <br><small class="text-muted">{{ $payment->failed_at->format('M d, Y \a\t H:i') }}</small>
                                @if($payment->failure_reason)
                                    <br><small class="text-danger">{{ $payment->failure_reason }}</small>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        @if($payment->refunded_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-secondary"></div>
                            <div class="timeline-content">
                                <strong>Payment Refunded</strong>
                                <br><small class="text-muted">{{ $payment->refunded_at->format('M d, Y \a\t H:i') }}</small>
                                @if($payment->refund_amount)
                                    <br><small>Amount: £{{ number_format($payment->refund_amount, 2) }}</small>
                                @endif
                                @if($payment->refund_reason)
                                    <br><small class="text-muted">{{ $payment->refund_reason }}</small>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            @if(auth()->user()->canManageProjects())
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="card-title mb-0">Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(in_array($payment->status, ['pending', 'failed']))
                            <a href="{{ route('payments.edit', $payment) }}" class="btn btn-outline-primary">
                                <i class="bi bi-pencil me-2"></i>Edit Payment
                            </a>
                            <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100" 
                                        onclick="return confirm('Are you sure you want to delete this payment?')">
                                    <i class="bi bi-trash me-2"></i>Delete Payment
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('invoices.show', $payment->invoice) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-receipt me-2"></i>View Invoice
                        </a>
                        @if($payment->invoice->project)
                            <a href="{{ route('projects.show', $payment->invoice->project) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-folder me-2"></i>View Project
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline-item {
    position: relative;
    margin-bottom: 15px;
}

.timeline-marker {
    position: absolute;
    left: -28px;
    top: 2px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: -23px;
    top: 14px;
    width: 2px;
    height: calc(100% + 15px);
    background-color: #dee2e6;
}

.timeline-content {
    font-size: 0.9rem;
}
</style>
@endsection