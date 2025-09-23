@extends('layouts.app')

@section('title', 'Payments')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Payments</h1>
            <p class="text-muted mb-0">Manage payment processing and transaction history</p>
        </div>
        @if(auth()->user()->canManageProjects())
            <a href="{{ route('payments.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Record Payment
            </a>
        @endif
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Payments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_payments'] }}</div>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Amount</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">£{{ number_format($stats['total_amount'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-pound text-gray-300" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-left-info h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Completed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">£{{ number_format($stats['completed_amount'], 2) }}</div>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">£{{ number_format($stats['pending_amount'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock text-gray-300" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Payment number, client...">
                </div>
                
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="client_id" class="form-label">Client</label>
                    <select class="form-select" id="client_id" name="client_id">
                        <option value="">All Clients</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->display_name }}
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
                
                <div class="col-md-2">
                    <label for="payment_method_id" class="form-label">Payment Method</label>
                    <select class="form-select" id="payment_method_id" name="payment_method_id">
                        <option value="">All Methods</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method->id }}" {{ request('payment_method_id') == $method->id ? 'selected' : '' }}>
                                {{ $method->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Payment History</h5>
        </div>
        <div class="card-body p-0">
            @if($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Payment #</th>
                                <th>Client</th>
                                <th>Project</th>
                                <th>Invoice</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>
                                        <strong>{{ $payment->payment_number }}</strong>
                                        @if($payment->payment_type == 'deposit')
                                            <span class="badge bg-info ms-1">Deposit</span>
                                        @elseif($payment->payment_type == 'partial_payment')
                                            <span class="badge bg-warning ms-1">Partial</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->invoice->client)
                                            <div class="fw-bold">{{ $payment->invoice->client->display_name }}</div>
                                            @if($payment->invoice->client->is_private_client)
                                                <small class="text-muted">Private Client</small>
                                            @endif
                                        @else
                                            <span class="text-muted">No client</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->invoice->project)
                                            <div class="fw-bold">{{ $payment->invoice->project->name }}</div>
                                            <small class="text-muted">{{ $payment->invoice->project->status }}</small>
                                        @else
                                            <span class="text-muted">No project</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('invoices.show', $payment->invoice) }}" class="text-decoration-none">
                                            {{ $payment->invoice->invoice_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-bold">£{{ number_format($payment->amount, 2) }}</div>
                                        @if($payment->processing_fee > 0)
                                            <small class="text-muted">Fee: £{{ number_format($payment->processing_fee, 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->paymentMethod)
                                            <span class="badge bg-primary">{{ $payment->paymentMethod->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($payment->payment_gateway) }}</span>
                                        @endif
                                    </td>
                                    <td>
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
                                        <span class="badge bg-{{ $statusColors[$payment->status] ?? 'secondary' }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($payment->processed_at)
                                            <div>{{ $payment->processed_at->format('M d, Y') }}</div>
                                            <small class="text-muted">{{ $payment->processed_at->format('H:i') }}</small>
                                        @else
                                            <span class="text-muted">Not processed</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('payments.show', $payment) }}">
                                                        <i class="bi bi-eye me-2"></i>View Details
                                                    </a>
                                                </li>
                                                @if(in_array($payment->status, ['pending', 'failed']) && auth()->user()->canManageProjects())
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('payments.edit', $payment) }}">
                                                            <i class="bi bi-pencil me-2"></i>Edit Payment
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger" 
                                                                    onclick="return confirm('Are you sure you want to delete this payment?')">
                                                                <i class="bi bi-trash me-2"></i>Delete Payment
                                                            </button>
                                                        </form>
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
                <div class="card-footer">
                    {{ $payments->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">No payments found</h5>
                    <p class="text-muted">No payments match your current filters.</p>
                    @if(auth()->user()->canManageProjects())
                        <a href="{{ route('payments.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Record Your First Payment
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection