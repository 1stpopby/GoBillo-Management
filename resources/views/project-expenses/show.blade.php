@extends('layouts.app')

@section('title', 'Expense Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Expense Details</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a></li>
                            <li class="breadcrumb-item active">Expense Details</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    @if(auth()->user()->canManageProjects())
                        <a href="{{ route('project.expenses.edit', ['project' => $project, 'expense' => $expense]) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>Edit
                        </a>
                        @if($expense->status === 'pending')
                            <button class="btn btn-success ms-2" onclick="approveExpense()">
                                <i class="bi bi-check me-2"></i>Approve
                            </button>
                            <button class="btn btn-danger ms-2" onclick="rejectExpense()">
                                <i class="bi bi-x me-2"></i>Reject
                            </button>
                        @endif
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $expense->title }}</h5>
                        </div>
                        <div class="card-body">
                            @if($expense->description)
                                <div class="mb-4">
                                    <h6>Description</h6>
                                    <p class="text-muted">{{ $expense->description }}</p>
                                </div>
                            @endif

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6>Category</h6>
                                    <p>
                                        <i class="{{ $expense->category_icon }} me-2"></i>
                                        {{ ucfirst($expense->category) }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Amount</h6>
                                    <p class="h4 text-primary">{{ $expense->formatted_amount }}</p>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6>Expense Date</h6>
                                    <p>{{ $expense->expense_date->format('F j, Y') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Status</h6>
                                    <p>
                                        <span class="badge bg-{{ $expense->status_color }} fs-6">
                                            {{ ucfirst($expense->status) }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            @if($expense->vendor_name || $expense->invoice_number)
                                <div class="row mb-4">
                                    @if($expense->vendor_name)
                                        <div class="col-md-6">
                                            <h6>Vendor</h6>
                                            <p>{{ $expense->vendor_name }}</p>
                                        </div>
                                    @endif
                                    @if($expense->invoice_number)
                                        <div class="col-md-6">
                                            <h6>Invoice Number</h6>
                                            <p>{{ $expense->invoice_number }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @if($expense->notes)
                                <div class="mb-4">
                                    <h6>Notes</h6>
                                    <p class="text-muted">{{ $expense->notes }}</p>
                                </div>
                            @endif

                            @if($expense->receipt_path)
                                <div class="mb-4">
                                    <h6>Receipt</h6>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-file-earmark-pdf fs-2 text-danger me-3"></i>
                                        <div>
                                            <p class="mb-1">Receipt attached</p>
                                            <a href="{{ Storage::url($expense->receipt_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-download me-1"></i>Download Receipt
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Expense Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted">Created By</small>
                                <p class="mb-0">{{ $expense->creator->name }}</p>
                                <small class="text-muted">{{ $expense->created_at->format('M j, Y g:i A') }}</small>
                            </div>

                            @if($expense->approved_by)
                                <div class="mb-3">
                                    <small class="text-muted">{{ $expense->status === 'approved' ? 'Approved' : 'Rejected' }} By</small>
                                    <p class="mb-0">{{ $expense->approver->name }}</p>
                                    <small class="text-muted">{{ $expense->approved_at->format('M j, Y g:i A') }}</small>
                                </div>
                            @endif

                            <div class="mb-3">
                                <small class="text-muted">Project</small>
                                <p class="mb-0">
                                    <a href="{{ route('projects.show', $project) }}" class="text-decoration-none">
                                        {{ $project->name }}
                                    </a>
                                </p>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Currency</small>
                                <p class="mb-0">{{ $expense->currency }}</p>
                            </div>

                            @if($expense->updated_at != $expense->created_at)
                                <div class="mb-0">
                                    <small class="text-muted">Last Updated</small>
                                    <p class="mb-0">{{ $expense->updated_at->format('M j, Y g:i A') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function approveExpense() {
    if (confirm('Are you sure you want to approve this expense?')) {
        // Get fresh CSRF token first
        fetch('/csrf-token')
        .then(response => {
            // Handle redirects (likely session expired)
            if (response.redirected || response.status === 302) {
                alert('Session expired. Please refresh the page and try again.');
                location.reload();
                return Promise.reject('Session expired');
            }
            if (!response.ok) {
                throw new Error(`Failed to get CSRF token: HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(tokenData => {
            if (!tokenData || !tokenData.token) {
                throw new Error('Invalid CSRF token response');
            }
            return fetch(`/projects/{{ $project->id }}/expenses/{{ $expense->id }}/approve`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': tokenData.token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
        })
        .then(response => {
            // Handle CSRF token mismatch specifically
            if (response.status === 419) {
                alert('Session expired. Please refresh the page and try again.');
                location.reload();
                return Promise.reject('Session expired');
            }
            
            // Handle other errors
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP ${response.status}: ${text}`);
                });
            }
            
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                alert('Expense approved successfully!');
                // Navigate back to project expenses or reload
                window.location.href = '/projects/{{ $project->id }}';
            } else {
                alert('Error approving expense: ' + (data?.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error approving expense. Please try again.');
        });
    }
}

function rejectExpense() {
    if (confirm('Are you sure you want to reject this expense?')) {
        fetch(`/projects/{{ $project->id }}/expenses/{{ $expense->id }}/reject`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error rejecting expense: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error rejecting expense');
        });
    }
}
</script>
@endsection 