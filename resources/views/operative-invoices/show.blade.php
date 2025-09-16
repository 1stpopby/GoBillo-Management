@extends('layouts.app')

@section('title', 'Invoice #' . $operativeInvoice->invoice_number)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('operative-dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('operative-invoices.index') }}">Invoices</a></li>
                        <li class="breadcrumb-item active">#{{ $operativeInvoice->invoice_number }}</li>
                    </ol>
                </nav>
                <h1 class="page-title">Invoice #{{ $operativeInvoice->invoice_number }}</h1>
                <p class="page-subtitle">
                    <span class="badge bg-{{ $operativeInvoice->status_color }} me-2">{{ ucfirst($operativeInvoice->status) }}</span>
                    Created {{ $operativeInvoice->created_at->format('M j, Y') }}
                </p>
            </div>
            <div class="col-lg-4 text-end">
                @if($operativeInvoice->status === 'submitted')
                    <div class="alert alert-info mb-2">
                        <i class="bi bi-clock me-2"></i>
                        <strong>Awaiting Approval</strong><br>
                        <small>Submitted to {{ $operativeInvoice->manager->name }} for review</small>
                    </div>
                @endif
                    
                {{-- Manager Approval Buttons - Show for submitted invoices --}}
                @if($operativeInvoice->status === 'submitted' && (auth()->user()->canManageOperativeInvoices() || $operativeInvoice->manager_id === auth()->id()))
                    <div class="btn-group mb-3" role="group">
                        <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#approveModal">
                            <i class="bi bi-check-lg me-2"></i>Approve Invoice
                        </button>
                        <button type="button" class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-lg me-2"></i>Reject Invoice
                        </button>
                    </div>
                @endif
                
                {{-- Status Display for Other States --}}
                @if($operativeInvoice->status === 'approved')
                    <div class="alert alert-success mb-2">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>Approved</strong><br>
                        <small>Approved on {{ $operativeInvoice->approved_at?->format('M j, Y') }}</small>
                    </div>
                @elseif($operativeInvoice->status === 'paid')
                    <div class="alert alert-primary mb-2">
                        <i class="bi bi-cash-coin me-2"></i>
                        <strong>Paid</strong><br>
                        <small>Paid on {{ $operativeInvoice->paid_at?->format('M j, Y') }}</small>
                    </div>
                @elseif($operativeInvoice->status === 'rejected')
                    <div class="alert alert-danger mb-2">
                        <i class="bi bi-x-circle me-2"></i>
                        <strong>Rejected</strong><br>
                        <small>Requires correction and resubmission</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Manager Action Section - Prominent approval area --}}
    @if($operativeInvoice->status === 'submitted' && (auth()->user()->canManageOperativeInvoices() || $operativeInvoice->manager_id === auth()->id()))
        <div class="alert alert-warning border-start border-warning border-4 mb-4" role="alert">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-exclamation-triangle-fill text-warning fs-2"></i>
                        </div>
                        <div>
                            <h5 class="alert-heading mb-1">Manager Action Required</h5>
                            <p class="mb-0">This invoice is awaiting your approval. Please review the details and approve or reject this invoice.</p>
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>
                                Submitted {{ $operativeInvoice->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#approveModal">
                            <i class="bi bi-check-lg me-2"></i>Approve
                        </button>
                        <button type="button" class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-lg me-2"></i>Reject
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Invoice Details -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Invoice Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Manager</label>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2">
                                        <div class="avatar-title bg-primary bg-opacity-10 text-primary rounded-circle">
                                            {{ substr($operativeInvoice->manager->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <strong>{{ $operativeInvoice->manager->name }}</strong><br>
                                        <small class="text-muted">{{ $operativeInvoice->manager->email }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Site & Project</label>
                                <div>
                                    <strong>{{ $operativeInvoice->site->name }}</strong><br>
                                    @if($operativeInvoice->project)
                                        <small class="text-muted">{{ $operativeInvoice->project->name }}</small>
                                    @else
                                        <small class="text-muted">No specific project</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Week Period</label>
                                <div>
                                    <strong>{{ $operativeInvoice->week_period }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Invoice Number</label>
                                <div>
                                    <strong>#{{ $operativeInvoice->invoice_number }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timesheet -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-week me-2"></i>Weekly Timesheet
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Day</th>
                                    <th>Worked</th>
                                    <th>Hours</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($operativeInvoice->items as $item)
                                    <tr>
                                        <td><strong>{{ $item->day_of_week }}</strong></td>
                                        <td>
                                            @if($item->worked)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check"></i> Yes
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-x"></i> No
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->worked)
                                                {{ $item->hours_worked }} hours
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->worked && $item->description)
                                                {{ $item->description }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->worked)
                                                <strong>£{{ number_format($item->amount, 2) }}</strong>
                                            @else
                                                <span class="text-muted">£0.00</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calculator me-2"></i>Financial Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Total Hours:</span>
                        <strong>{{ $operativeInvoice->items->where('worked', true)->sum('hours_worked') }} hours</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span>Gross Amount:</span>
                        <strong>£{{ number_format($operativeInvoice->gross_amount, 2) }}</strong>
                    </div>

                    @if($operativeInvoice->cis_applicable)
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>CIS Rate:</span>
                            <span>{{ $operativeInvoice->cis_rate }}%</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>CIS Deduction:</span>
                            <span class="text-danger">-£{{ number_format($operativeInvoice->cis_deduction, 2) }}</span>
                        </div>
                    @endif

                    <hr>
                    <div class="d-flex justify-content-between mb-0">
                        <strong>Net Amount:</strong>
                        <strong class="text-success">£{{ number_format($operativeInvoice->net_amount, 2) }}</strong>
                    </div>
                </div>
            </div>

            <!-- Status History -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>Status History
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Created</h6>
                                <small class="text-muted">{{ $operativeInvoice->created_at->format('M j, Y \a\t g:i A') }}</small>
                            </div>
                        </div>
                        
                        @if($operativeInvoice->submitted_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Submitted</h6>
                                    <small class="text-muted">{{ $operativeInvoice->submitted_at->format('M j, Y \a\t g:i A') }}</small>
                                </div>
                            </div>
                        @endif

                        @if($operativeInvoice->approved_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Approved</h6>
                                    <small class="text-muted">{{ $operativeInvoice->approved_at->format('M j, Y \a\t g:i A') }}</small>
                                </div>
                            </div>
                        @endif

                        @if($operativeInvoice->paid_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Paid</h6>
                                    <small class="text-muted">{{ $operativeInvoice->paid_at->format('M j, Y \a\t g:i A') }}</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    
    .timeline-marker {
        position: absolute;
        left: -23px;
        top: 5px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid white;
    }
    
    .timeline-content h6 {
        margin-bottom: 5px;
        font-weight: 600;
    }
</style>
@endpush

{{-- Approval/Rejection Modals (only for managers) --}}
@if($operativeInvoice->status === 'submitted' && (auth()->user()->canManageOperativeInvoices() || $operativeInvoice->manager_id === auth()->id()))
    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('invoices.operative.approve', $operativeInvoice->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Are you sure you want to approve invoice <strong>{{ $operativeInvoice->invoice_number }}</strong>?</p>
                        
                        <div class="mb-3">
                            <label for="approval_notes" class="form-label">Approval Notes (Optional)</label>
                            <textarea class="form-control" id="approval_notes" name="approval_notes" rows="3" 
                                      placeholder="Add any notes about this approval..."></textarea>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Invoice Summary:</strong><br>
                            Net Amount: £{{ number_format($operativeInvoice->net_amount, 2) }}<br>
                            Total Hours: {{ $operativeInvoice->total_hours }}h
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-2"></i>Approve Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('invoices.operative.reject', $operativeInvoice->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Please provide a reason for rejecting invoice <strong>{{ $operativeInvoice->invoice_number }}</strong>.</p>
                        
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" 
                                      placeholder="Explain why this invoice is being rejected..." required></textarea>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Note:</strong> The operative will be notified of the rejection and can resubmit with corrections.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-lg me-2"></i>Reject Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@endsection

