@extends('layouts.app')

@section('title', 'Payment Statements')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Payment Statements</h1>
        <p class="text-muted mb-0">Generate and manage client payment statements</p>
    </div>
    <a href="{{ route('payment-statements.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Generate Statement
    </a>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-file-text text-primary fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1">Total Statements</p>
                        <h4 class="mb-0">{{ $statements->total() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-calendar-check text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1">This Month</p>
                        <h4 class="mb-0">{{ $statements->where('created_at', '>=', now()->startOfMonth())->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-send text-warning fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1">Sent Today</p>
                        <h4 class="mb-0">0</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-people text-info fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="text-muted mb-1">Active Clients</p>
                        <h4 class="mb-0">{{ \App\Models\Client::forCompany()->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statements List -->
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">Generated Statements</h5>
    </div>
    <div class="card-body">
        @if($statements->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Statement #</th>
                            <th>Client</th>
                            <th>Period</th>
                            <th>Total Invoiced</th>
                            <th>Total Paid</th>
                            <th>Outstanding</th>
                            <th>Generated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($statements as $statement)
                            @php
                                $client = \App\Models\Client::find($statement->client_id);
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('payment-statements.show', $statement->id) }}" class="text-decoration-none">
                                        <strong>{{ $statement->statement_number }}</strong>
                                    </a>
                                </td>
                                <td>{{ $client ? $client->company_name : 'Unknown' }}</td>
                                <td>
                                    @if($statement->date_from && $statement->date_to)
                                        {{ \Carbon\Carbon::parse($statement->date_from)->format('M d, Y') }} - 
                                        {{ \Carbon\Carbon::parse($statement->date_to)->format('M d, Y') }}
                                    @else
                                        All Time
                                    @endif
                                </td>
                                <td class="text-primary fw-bold">£{{ number_format($statement->total_invoiced, 2) }}</td>
                                <td class="text-success fw-bold">£{{ number_format($statement->total_paid, 2) }}</td>
                                <td class="text-danger fw-bold">£{{ number_format($statement->outstanding_balance, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($statement->created_at)->format('M d, Y') }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('payment-statements.show', $statement->id) }}" 
                                           class="btn btn-sm btn-info text-white"
                                           data-bs-toggle="tooltip" 
                                           title="View Statement">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('payment-statements.pdf', $statement->id) }}" 
                                           class="btn btn-sm btn-danger"
                                           data-bs-toggle="tooltip" 
                                           title="Download PDF">
                                            <i class="bi bi-file-pdf"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-success"
                                                data-bs-toggle="tooltip" 
                                                title="Send to Client"
                                                onclick="sendStatement({{ $statement->id }})">
                                            <i class="bi bi-envelope-fill"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-3">
                {{ $statements->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-file-text display-1 text-muted opacity-50 mb-3"></i>
                <h5 class="text-muted">No Payment Statements Yet</h5>
                <p class="text-muted">Generate your first statement to track client payments and balances.</p>
                <a href="{{ route('payment-statements.create') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-plus-circle me-2"></i>Generate First Statement
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function sendStatement(statementId) {
    if (confirm('Are you sure you want to send this statement to the client?')) {
        window.location.href = `/payment-statements/${statementId}/send`;
    }
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush