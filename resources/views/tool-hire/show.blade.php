@extends('layouts.app')

@section('title', 'Tool Hire Request - ' . $toolHireRequest->tool_name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">{{ $toolHireRequest->tool_name }}</h1>
            <div class="page-meta">
                <span class="badge bg-{{ $toolHireRequest->status_color }} me-2">{{ $toolHireRequest->status_display }}</span>
                <span class="badge bg-{{ $toolHireRequest->category_display }} me-2">{{ $toolHireRequest->category_display }}</span>
                @if($toolHireRequest->isOverdue())
                    <span class="badge bg-danger me-2">OVERDUE</span>
                @endif
                <small class="text-muted">
                    Created {{ $toolHireRequest->created_at->format('M j, Y \a\t g:i A') }} by {{ $toolHireRequest->requestedBy->name }}
                </small>
            </div>
        </div>
        <div class="action-buttons">
            <a href="{{ route('tool-hire.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>
            
            @if($toolHireRequest->canBeEdited())
                <a href="{{ route('tool-hire.edit', $toolHireRequest) }}" class="btn btn-outline-primary me-2">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
            @endif
            
            @if(auth()->user()->isCompanyAdmin() || auth()->user()->canManageProjects())
                @if($toolHireRequest->canBeApproved())
                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#approveModal">
                        <i class="bi bi-check-circle me-2"></i>Approve
                    </button>
                    <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-circle me-2"></i>Reject
                    </button>
                @endif
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Tool Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-tools me-2"></i>Tool Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Request Title</label>
                            <p class="mb-0">{{ $toolHireRequest->title }}</p>
                        </div>
                        
                        @if($toolHireRequest->description)
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Description</label>
                            <p class="text-muted">{{ $toolHireRequest->description }}</p>
                        </div>
                        @endif
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Category</label>
                            <p class="mb-0">
                                <span class="badge bg-secondary">{{ $toolHireRequest->category_display }}</span>
                            </p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Quantity</label>
                            <p class="mb-0">
                                <i class="bi bi-hash me-2"></i>{{ $toolHireRequest->quantity }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hire Period -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar me-2"></i>Hire Period
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Start Date</label>
                            <p class="mb-0">
                                <i class="bi bi-calendar-check me-2"></i>{{ $toolHireRequest->hire_start_date->format('M j, Y') }}
                            </p>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold">End Date</label>
                            <p class="mb-0">
                                <i class="bi bi-calendar-x me-2"></i>{{ $toolHireRequest->hire_end_date->format('M j, Y') }}
                            </p>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Duration</label>
                            <p class="mb-0">
                                <i class="bi bi-clock me-2"></i>{{ $toolHireRequest->hire_duration_days }} days
                            </p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Urgency</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $toolHireRequest->urgency_color }}">
                                    {{ ucfirst($toolHireRequest->urgency) }}
                                </span>
                            </p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Delivery Method</label>
                            <p class="mb-0">{{ ucfirst(str_replace('_', ' ', $toolHireRequest->delivery_method)) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location & Project -->
            @if($toolHireRequest->site || $toolHireRequest->project)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-geo-alt me-2"></i>Location & Project
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            @if($toolHireRequest->site)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Site</label>
                                    <p class="mb-0">
                                        <i class="bi bi-building me-2"></i>{{ $toolHireRequest->site->name }}
                                    </p>
                                </div>
                            @endif
                            
                            @if($toolHireRequest->project)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Project</label>
                                    <p class="mb-0">
                                        <i class="bi bi-folder me-2"></i>{{ $toolHireRequest->project->name }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Cost Information -->
            @if($toolHireRequest->estimated_daily_rate || $toolHireRequest->estimated_total_cost)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-currency-pound me-2"></i>Cost Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            @if($toolHireRequest->estimated_daily_rate)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Daily Rate</label>
                                    <p class="mb-0">
                                        <span class="h5 text-success">£{{ number_format($toolHireRequest->estimated_daily_rate, 2) }}</span>
                                        <small class="text-muted">per day</small>
                                    </p>
                                </div>
                            @endif
                            
                            @if($toolHireRequest->estimated_total_cost)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Total Estimated Cost</label>
                                    <p class="mb-0">
                                        <span class="h5 text-primary">£{{ number_format($toolHireRequest->estimated_total_cost, 2) }}</span>
                                    </p>
                                </div>
                            @endif
                            
                            @if($toolHireRequest->deposit_amount)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Expected Deposit</label>
                                    <p class="mb-0">£{{ number_format($toolHireRequest->deposit_amount, 2) }}</p>
                                </div>
                            @endif
                            
                            @if($toolHireRequest->insurance_required)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Insurance</label>
                                    <p class="mb-0">
                                        <span class="badge bg-info">Required</span>
                                        @if($toolHireRequest->insurance_cost)
                                            - £{{ number_format($toolHireRequest->insurance_cost, 2) }}
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Additional Information -->
            @if($toolHireRequest->special_requirements || $toolHireRequest->preferred_supplier || $toolHireRequest->notes)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-sticky me-2"></i>Additional Information
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($toolHireRequest->special_requirements)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Special Requirements</label>
                                <p class="text-muted">{{ $toolHireRequest->special_requirements }}</p>
                            </div>
                        @endif
                        
                        @if($toolHireRequest->preferred_supplier)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Preferred Supplier</label>
                                <p class="text-muted">{{ $toolHireRequest->preferred_supplier }}</p>
                            </div>
                        @endif
                        
                        @if($toolHireRequest->notes)
                            <div class="mb-0">
                                <label class="form-label fw-bold">Additional Notes</label>
                                <p class="text-muted mb-0">{{ $toolHireRequest->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-square me-2"></i>Status Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="status-item mb-3">
                        <label class="form-label fw-bold">Current Status</label>
                        <p class="mb-0">
                            <span class="badge bg-{{ $toolHireRequest->status_color }} fs-6">
                                {{ $toolHireRequest->status_display }}
                            </span>
                        </p>
                    </div>
                    
                    <div class="status-item mb-3">
                        <label class="form-label fw-bold">Requested By</label>
                        <p class="mb-0">
                            <i class="bi bi-person me-2"></i>{{ $toolHireRequest->requestedBy->name }}
                            <br>
                            <small class="text-muted">{{ $toolHireRequest->created_at->format('M j, Y \a\t g:i A') }}</small>
                        </p>
                    </div>
                    
                    @if($toolHireRequest->approvedBy)
                        <div class="status-item mb-3">
                            <label class="form-label fw-bold">Approved By</label>
                            <p class="mb-0">
                                <i class="bi bi-person-check me-2"></i>{{ $toolHireRequest->approvedBy->name }}
                                <br>
                                <small class="text-muted">{{ $toolHireRequest->approved_at->format('M j, Y \a\t g:i A') }}</small>
                            </p>
                        </div>
                    @endif
                    
                    @if($toolHireRequest->rejection_reason)
                        <div class="status-item">
                            <label class="form-label fw-bold text-danger">Rejection Reason</label>
                            <p class="text-muted mb-0">{{ $toolHireRequest->rejection_reason }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($toolHireRequest->canBeEdited())
                            <a href="{{ route('tool-hire.edit', $toolHireRequest) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-pencil me-2"></i>Edit Request
                            </a>
                        @endif
                        
                        <a href="{{ route('tool-hire.create') }}" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-plus-circle me-2"></i>Create Similar Request
                        </a>
                        
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="window.print()">
                            <i class="bi bi-printer me-2"></i>Print Request
                        </button>
                        
                        @if($toolHireRequest->status === 'draft' && auth()->user()->id === $toolHireRequest->requested_by)
                            <form method="POST" action="{{ route('tool-hire.destroy', $toolHireRequest) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100" 
                                        onclick="return confirm('Are you sure you want to delete this request?')">
                                    <i class="bi bi-trash me-2"></i>Delete Request
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Tool Hire Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve this tool hire request?</p>
                <div class="alert alert-info">
                    <strong>{{ $toolHireRequest->tool_name }}</strong><br>
                    <small>{{ $toolHireRequest->quantity }} unit(s) needed from {{ $toolHireRequest->hire_start_date->format('M j') }} to {{ $toolHireRequest->hire_end_date->format('M j, Y') }}</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('tool-hire.approve', $toolHireRequest) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-2"></i>Approve Request
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('tool-hire.reject', $toolHireRequest) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Tool Hire Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>{{ $toolHireRequest->tool_name }}</strong><br>
                        <small>You are about to reject this tool hire request</small>
                    </div>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" 
                                  placeholder="Please provide a clear reason for rejection..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-2"></i>Reject Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 0.5rem;
}

.page-meta {
    margin-bottom: 0;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.card-title {
    color: #495057;
    font-weight: 600;
}

.form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.25rem;
}

.status-item {
    border-bottom: 1px solid #f1f3f4;
    padding-bottom: 0.75rem;
}

.status-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    font-weight: 500;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #667eea 100%);
    transform: translateY(-1px);
}

@media print {
    .action-buttons,
    .btn,
    .modal {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
}
</style>
@endsection
