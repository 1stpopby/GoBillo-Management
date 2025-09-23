@extends('layouts.app')

@section('title', 'Payment Statement - ' . $statement_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Payment Statement</h1>
        <p class="text-muted mb-0">{{ $statement_number }} - Generated on {{ $statement_date->format('F d, Y') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('payment-statements.pdf', $statement_id ?? request()->route('id')) }}" class="btn btn-danger">
            <i class="bi bi-file-pdf me-2"></i>Download PDF
        </a>
        <button type="button" class="btn btn-success" onclick="sendStatement()">
            <i class="bi bi-envelope me-2"></i>Send to Client
        </button>
        <a href="{{ route('payment-statements.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

<!-- Client Information -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="bi bi-building me-2"></i>Client Information
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>{{ $client->company_name }}</h6>
                @if($client->contact_name)
                    <p class="mb-1">{{ $client->contact_name }}</p>
                @endif
                @if($client->email)
                    <p class="mb-1">
                        <i class="bi bi-envelope me-1"></i>{{ $client->email }}
                    </p>
                @endif
                @if($client->phone)
                    <p class="mb-0">
                        <i class="bi bi-telephone me-1"></i>{{ $client->phone }}
                    </p>
                @endif
            </div>
            <div class="col-md-6 text-md-end">
                <h6>Statement Period</h6>
                <p class="mb-0">
                    @if($date_from && $date_to)
                        {{ $date_from->format('F d, Y') }} - {{ $date_to->format('F d, Y') }}
                    @else
                        All Time
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Financial Summary -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-calculator me-2"></i>Financial Summary
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="text-center p-3 bg-light rounded">
                    <h6 class="text-muted mb-2">Total Budget</h6>
                    <h3 class="text-primary mb-0">£{{ number_format($total_budget, 2) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center p-3 bg-light rounded">
                    <h6 class="text-muted mb-2">Total Invoiced</h6>
                    <h3 class="text-info mb-0">£{{ number_format($total_invoiced, 2) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center p-3 bg-light rounded">
                    <h6 class="text-muted mb-2">Total Paid</h6>
                    <h3 class="text-success mb-0">£{{ number_format($total_paid, 2) }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center p-3 bg-light rounded">
                    <h6 class="text-muted mb-2">Outstanding</h6>
                    <h3 class="text-danger mb-0">£{{ number_format($outstanding_balance, 2) }}</h3>
                </div>
            </div>
        </div>
        
        <!-- Progress Bars -->
        <div class="mt-4">
            @php
                $invoicedPercentage = $total_budget > 0 ? ($total_invoiced / $total_budget) * 100 : 0;
                $paidPercentage = $total_invoiced > 0 ? ($total_paid / $total_invoiced) * 100 : 0;
            @endphp
            
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span>Budget Utilization</span>
                    <span>{{ number_format($invoicedPercentage, 1) }}%</span>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-info" role="progressbar" 
                         style="width: {{ min($invoicedPercentage, 100) }}%"></div>
                </div>
                <small class="text-muted">£{{ number_format($total_invoiced, 2) }} of £{{ number_format($total_budget, 2) }} invoiced</small>
            </div>
            
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span>Payment Progress</span>
                    <span>{{ number_format($paidPercentage, 1) }}%</span>
                </div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: {{ min($paidPercentage, 100) }}%"></div>
                </div>
                <small class="text-muted">£{{ number_format($total_paid, 2) }} of £{{ number_format($total_invoiced, 2) }} paid</small>
            </div>
        </div>
    </div>
</div>

<!-- Projects & Budgets -->
@if($projects->count() > 0)
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-kanban me-2"></i>Projects & Budgets
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Site</th>
                        <th>Status</th>
                        <th class="text-end">Budget</th>
                        <th class="text-end">Start Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                        <tr>
                            <td><strong>{{ $project->name }}</strong></td>
                            <td>{{ $project->site ? $project->site->name : 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $project->status == 'active' ? 'success' : ($project->status == 'completed' ? 'secondary' : 'warning') }}">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </td>
                            <td class="text-end">£{{ number_format($project->budget, 2) }}</td>
                            <td class="text-end">{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M d, Y') : 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <th colspan="3">Total Budget</th>
                        <th class="text-end">£{{ number_format($total_budget, 2) }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Invoice History -->
@if($invoices->count() > 0)
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-receipt me-2"></i>Invoice History
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                        @php
                            $invoicePaid = $invoice->payments->where('status', 'completed')->sum('amount');
                            $invoiceBalance = $invoice->total_amount - $invoicePaid;
                        @endphp
                        <tr>
                            <td><strong>{{ $invoice->invoice_number }}</strong></td>
                            <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                            <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'overdue' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td class="text-end">£{{ number_format($invoice->total_amount, 2) }}</td>
                            <td class="text-end text-success">£{{ number_format($invoicePaid, 2) }}</td>
                            <td class="text-end {{ $invoiceBalance > 0 ? 'text-danger' : '' }}">
                                £{{ number_format($invoiceBalance, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <th colspan="4">Totals</th>
                        <th class="text-end">£{{ number_format($total_invoiced, 2) }}</th>
                        <th class="text-end text-success">£{{ number_format($total_paid, 2) }}</th>
                        <th class="text-end text-danger">£{{ number_format($outstanding_balance, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Payment Transactions -->
@if($payments->count() > 0)
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-credit-card me-2"></i>Payment Transactions
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Payment #</th>
                        <th>Date</th>
                        <th>Invoice</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                        <tr>
                            <td><strong>{{ $payment->payment_number }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($payment->processed_at)->format('M d, Y') }}</td>
                            <td>{{ $payment->invoice->invoice_number ?? 'N/A' }}</td>
                            <td>{{ ucfirst($payment->payment_gateway) }}</td>
                            <td>
                                <span class="badge bg-success">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="text-end text-success">£{{ number_format($payment->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <th colspan="5">Total Payments Received</th>
                        <th class="text-end text-success">£{{ number_format($total_paid, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Additional Information -->
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-info-circle me-2"></i>Statement Summary
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>Budget Analysis</h6>
                <ul>
                    <li>Total Project Budget: <strong>£{{ number_format($total_budget, 2) }}</strong></li>
                    <li>Amount Invoiced: <strong>£{{ number_format($total_invoiced, 2) }}</strong></li>
                    <li>Remaining Budget: <strong class="text-{{ $remaining_budget >= 0 ? 'success' : 'danger' }}">
                        £{{ number_format($remaining_budget, 2) }}
                    </strong></li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6>Payment Status</h6>
                <ul>
                    <li>Total Invoiced: <strong>£{{ number_format($total_invoiced, 2) }}</strong></li>
                    <li>Total Paid: <strong class="text-success">£{{ number_format($total_paid, 2) }}</strong></li>
                    <li>Outstanding Balance: <strong class="text-danger">£{{ number_format($outstanding_balance, 2) }}</strong></li>
                </ul>
            </div>
        </div>
        
        @if($outstanding_balance > 0)
            <div class="alert alert-warning mt-3">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Payment Due:</strong> There is an outstanding balance of £{{ number_format($outstanding_balance, 2) }}.
                Please arrange payment at your earliest convenience.
            </div>
        @else
            <div class="alert alert-success mt-3">
                <i class="bi bi-check-circle me-2"></i>
                <strong>Account Up to Date:</strong> All invoices have been paid in full. Thank you for your prompt payment.
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function sendStatement() {
    if (confirm('Send this statement to the client via email?')) {
        window.location.href = '{{ route("payment-statements.send", $statement_id ?? request()->route("id")) }}';
    }
}
</script>
@endpush