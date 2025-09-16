@extends('layouts.app')

@section('title', 'Hire Request - ' . $hireRequest->title)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">{{ $hireRequest->title }}</h1>
            <div class="page-meta">
                <span class="badge bg-{{ $hireRequest->status_color }} me-2">{{ $hireRequest->status_display }}</span>
                <span class="badge bg-{{ $hireRequest->urgency_color }} me-2">{{ ucfirst($hireRequest->urgency) }}</span>
                @if($hireRequest->isOverdue())
                    <span class="badge bg-danger me-2">OVERDUE</span>
                @endif
                <small class="text-muted">
                    Created {{ $hireRequest->created_at->format('M j, Y \a\t g:i A') }} by {{ $hireRequest->requestedBy->name }}
                </small>
            </div>
        </div>
        <div class="action-buttons">
            <a href="{{ route('hire.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>
            
            @if($hireRequest->canBeEdited())
                <a href="{{ route('hire.edit', $hireRequest) }}" class="btn btn-outline-primary me-2">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
            @endif
            
            @if(auth()->user()->isCompanyAdmin() || auth()->user()->canManageProjects())
                @if($hireRequest->canBeApproved())
                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#approveModal">
                        <i class="bi bi-check-circle me-2"></i>Approve
                    </button>
                    <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-circle me-2"></i>Reject
                    </button>
                @endif
                
                @if($hireRequest->status === 'approved' || $hireRequest->status === 'in_progress')
                    <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#markFilledModal">
                        <i class="bi bi-check-circle-fill me-2"></i>Mark Filled
                    </button>
                @endif
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Basic Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Description</label>
                            <p class="text-muted">{{ $hireRequest->description }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Position Type</label>
                            <p class="mb-0">
                                <span class="badge bg-secondary">{{ $hireRequest->position_type_display }}</span>
                            </p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Employment Type</label>
                            <p class="mb-0">{{ ucfirst(str_replace('_', ' ', $hireRequest->employment_type)) }}</p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Quantity Needed</label>
                            <p class="mb-0">
                                <i class="bi bi-people me-2"></i>{{ $hireRequest->quantity }} 
                                {{ $hireRequest->quantity > 1 ? 'people' : 'person' }}
                            </p>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Urgency Level</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $hireRequest->urgency_color }}">
                                    {{ ucfirst($hireRequest->urgency) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location & Project -->
            @if($hireRequest->site || $hireRequest->project)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-geo-alt me-2"></i>Location & Project
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            @if($hireRequest->site)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Site</label>
                                    <p class="mb-0">
                                        <i class="bi bi-building me-2"></i>{{ $hireRequest->site->name }}
                                    </p>
                                </div>
                            @endif
                            
                            @if($hireRequest->project)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Project</label>
                                    <p class="mb-0">
                                        <i class="bi bi-folder me-2"></i>{{ $hireRequest->project->name }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Requirements -->
            @if($hireRequest->required_skills || $hireRequest->required_qualifications || $hireRequest->required_certifications || $hireRequest->min_experience_years)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-list-check me-2"></i>Requirements
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            @if($hireRequest->required_skills)
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Required Skills</label>
                                    <p class="text-muted">{{ $hireRequest->required_skills }}</p>
                                </div>
                            @endif
                            
                            @if($hireRequest->required_qualifications)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Required Qualifications</label>
                                    <p class="text-muted">{{ $hireRequest->required_qualifications }}</p>
                                </div>
                            @endif
                            
                            @if($hireRequest->required_certifications)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Required Certifications</label>
                                    <p class="text-muted">{{ $hireRequest->required_certifications }}</p>
                                </div>
                            @endif
                            
                            @if($hireRequest->min_experience_years)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Minimum Experience</label>
                                    <p class="mb-0">
                                        <i class="bi bi-clock me-2"></i>{{ $hireRequest->min_experience_years }} 
                                        {{ $hireRequest->min_experience_years > 1 ? 'years' : 'year' }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Compensation -->
            @if($hireRequest->offered_rate || $hireRequest->benefits)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-currency-pound me-2"></i>Compensation
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            @if($hireRequest->offered_rate)
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Offered Rate</label>
                                    <p class="mb-0">
                                        <span class="h5 text-success">£{{ number_format($hireRequest->offered_rate, 2) }}</span>
                                        <small class="text-muted">/ {{ $hireRequest->rate_type }}</small>
                                    </p>
                                </div>
                            @endif
                            
                            @if($hireRequest->benefits)
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Additional Benefits</label>
                                    <p class="text-muted">{{ $hireRequest->benefits }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Timeline -->
            @if($hireRequest->start_date || $hireRequest->end_date || $hireRequest->deadline)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar me-2"></i>Timeline
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            @if($hireRequest->start_date)
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Start Date</label>
                                    <p class="mb-0">
                                        <i class="bi bi-calendar-check me-2"></i>{{ $hireRequest->start_date->format('M j, Y') }}
                                    </p>
                                </div>
                            @endif
                            
                            @if($hireRequest->end_date)
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">End Date</label>
                                    <p class="mb-0">
                                        <i class="bi bi-calendar-x me-2"></i>{{ $hireRequest->end_date->format('M j, Y') }}
                                    </p>
                                </div>
                            @endif
                            
                            @if($hireRequest->deadline)
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Hiring Deadline</label>
                                    <p class="mb-0 {{ $hireRequest->deadline->isPast() ? 'text-danger' : ($hireRequest->deadline->diffInDays() <= 7 ? 'text-warning' : '') }}">
                                        <i class="bi bi-alarm me-2"></i>{{ $hireRequest->deadline->format('M j, Y') }}
                                        @if($hireRequest->deadline->isPast())
                                            <br><small class="text-danger">Past due</small>
                                        @elseif($hireRequest->deadline->diffInDays() <= 7)
                                            <br><small class="text-warning">Due in {{ $hireRequest->deadline->diffInDays() }} days</small>
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Notes -->
            @if($hireRequest->notes)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-sticky me-2"></i>Additional Notes
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-0">{{ $hireRequest->notes }}</p>
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
                            <span class="badge bg-{{ $hireRequest->status_color }} fs-6">
                                {{ $hireRequest->status_display }}
                            </span>
                        </p>
                    </div>
                    
                    <div class="status-item mb-3">
                        <label class="form-label fw-bold">Requested By</label>
                        <p class="mb-0">
                            <i class="bi bi-person me-2"></i>{{ $hireRequest->requestedBy->name }}
                            <br>
                            <small class="text-muted">{{ $hireRequest->created_at->format('M j, Y \a\t g:i A') }}</small>
                        </p>
                    </div>
                    
                    @if($hireRequest->approvedBy)
                        <div class="status-item mb-3">
                            <label class="form-label fw-bold">Approved By</label>
                            <p class="mb-0">
                                <i class="bi bi-person-check me-2"></i>{{ $hireRequest->approvedBy->name }}
                                <br>
                                <small class="text-muted">{{ $hireRequest->approved_at->format('M j, Y \a\t g:i A') }}</small>
                            </p>
                        </div>
                    @endif
                    
                    @if($hireRequest->assignedTo)
                        <div class="status-item mb-3">
                            <label class="form-label fw-bold">Assigned To</label>
                            <p class="mb-0">
                                <i class="bi bi-person-gear me-2"></i>{{ $hireRequest->assignedTo->name }}
                            </p>
                        </div>
                    @endif
                    
                    @if($hireRequest->rejection_reason)
                        <div class="status-item">
                            <label class="form-label fw-bold text-danger">Rejection Reason</label>
                            <p class="text-muted mb-0">{{ $hireRequest->rejection_reason }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Progress Tracking -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bar-chart me-2"></i>Progress Tracking
                    </h5>
                </div>
                <div class="card-body">
                    <div class="progress-item mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">Applications Received</span>
                            <span class="badge bg-primary">{{ $hireRequest->applications_count }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: {{ min(100, ($hireRequest->applications_count / max(1, $hireRequest->quantity)) * 100) }}%"></div>
                        </div>
                    </div>
                    
                    <div class="progress-item mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">Interviews Conducted</span>
                            <span class="badge bg-info">{{ $hireRequest->interviews_count }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: {{ min(100, ($hireRequest->interviews_count / max(1, $hireRequest->applications_count)) * 100) }}%"></div>
                        </div>
                    </div>
                    
                    <div class="progress-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">Positions Filled</span>
                            <span class="badge bg-success">{{ $hireRequest->hired_count }} / {{ $hireRequest->quantity }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ ($hireRequest->hired_count / max(1, $hireRequest->quantity)) * 100 }}%"></div>
                        </div>
                    </div>
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
                        @if($hireRequest->canBeEdited())
                            <a href="{{ route('hire.edit', $hireRequest) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-pencil me-2"></i>Edit Request
                            </a>
                        @endif
                        
                        <a href="{{ route('hire.create') }}" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-plus-circle me-2"></i>Create Similar Request
                        </a>
                        
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="window.print()">
                            <i class="bi bi-printer me-2"></i>Print Request
                        </button>
                        
                        @if($hireRequest->status === 'draft' && auth()->user()->id === $hireRequest->requested_by)
                            <form method="POST" action="{{ route('hire.destroy', $hireRequest) }}" class="d-inline">
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
                <h5 class="modal-title">Approve Hire Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve this hire request?</p>
                <div class="alert alert-info">
                    <strong>{{ $hireRequest->title }}</strong><br>
                    <small>{{ $hireRequest->quantity }} {{ $hireRequest->position_type_display }}(s) needed</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('hire.approve', $hireRequest) }}" class="d-inline">
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
            <form method="POST" action="{{ route('hire.reject', $hireRequest) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Hire Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>{{ $hireRequest->title }}</strong><br>
                        <small>You are about to reject this hire request</small>
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

<!-- Mark Filled Modal -->
<div class="modal fade" id="markFilledModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Request as Filled</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Mark this hire request as filled?</p>
                <div class="alert alert-success">
                    <strong>{{ $hireRequest->title }}</strong><br>
                    <small>This will mark all {{ $hireRequest->quantity }} position(s) as filled</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('hire.mark-filled', $hireRequest) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-info">
                        <i class="bi bi-check-circle-fill me-2"></i>Mark as Filled
                    </button>
                </form>
            </div>
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

.progress-item {
    margin-bottom: 1rem;
}

.progress-item:last-child {
    margin-bottom: 0;
}

.progress {
    background-color: #e9ecef;
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
