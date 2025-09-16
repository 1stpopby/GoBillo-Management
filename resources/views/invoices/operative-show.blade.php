@extends('layouts.app')

@section('title', 'Operative Invoice - ' . $operativeInvoice->invoice_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Invoice {{ $operativeInvoice->invoice_number }}</h1>
        <p class="text-muted mb-0">{{ $operativeInvoice->operative->name }} - Week {{ $operativeInvoice->week_starting->format('M d') }} to {{ $operativeInvoice->week_ending->format('M d, Y') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('invoices.index', ['type' => 'operative']) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
        <a href="{{ route('invoices.operative.pdf', $operativeInvoice->id) }}" class="btn btn-success">
            <i class="bi bi-file-pdf"></i> Download PDF
        </a>
    </div>
</div>

<!-- Status Alert -->
@switch($operativeInvoice->status)
    @case('submitted')
        <div class="alert alert-warning">
            <i class="bi bi-clock me-2"></i>
            <strong>Awaiting Approval:</strong> This invoice is waiting for manager approval.
        </div>
        @break
    @case('approved')
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>
            <strong>Approved:</strong> This invoice has been approved{{ $operativeInvoice->approved_at ? ' on ' . $operativeInvoice->approved_at->format('M d, Y \a\t g:i A') : '' }}.
        </div>
        @break
    @case('rejected')
        <div class="alert alert-danger">
            <i class="bi bi-x-circle me-2"></i>
            <strong>Rejected:</strong> This invoice has been rejected and needs to be corrected.
        </div>
        @break
    @case('paid')
        <div class="alert alert-info">
            <i class="bi bi-cash-coin me-2"></i>
            <strong>Paid:</strong> This invoice has been paid{{ $operativeInvoice->paid_at ? ' on ' . $operativeInvoice->paid_at->format('M d, Y') : '' }}.
        </div>
        @break
@endswitch

{{-- Manager Action Section --}}
@if($operativeInvoice->status === 'submitted' && (auth()->user()->canManageOperativeInvoices() || $operativeInvoice->manager_id === auth()->id()))
    <div class="card border-warning mb-4">
        <div class="card-header bg-warning bg-opacity-10 border-warning">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="card-title mb-0 text-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Manager Action Required
                    </h5>
                </div>
                <div class="col-auto">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                            <i class="bi bi-check-lg me-1"></i> Approve
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-lg me-1"></i> Reject
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <p class="mb-2">
                <strong>{{ $operativeInvoice->operative->name }}</strong> has submitted this invoice for your approval.
            </p>
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="border rounded p-2">
                        <div class="small text-muted">Period</div>
                        <div class="fw-semibold">{{ $operativeInvoice->week_starting->format('M d') }} - {{ $operativeInvoice->week_ending->format('M d') }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-2">
                        <div class="small text-muted">Total Hours</div>
                        <div class="fw-semibold">{{ $operativeInvoice->total_hours }}h</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-2">
                        <div class="small text-muted">Gross Amount</div>
                        <div class="fw-semibold">£{{ number_format($operativeInvoice->gross_amount, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-2">
                        <div class="small text-muted">Net Amount</div>
                        <div class="fw-semibold text-success">£{{ number_format($operativeInvoice->net_amount, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Company Admin Action Section - Final Approval & Payment --}}
@if($operativeInvoice->status === 'approved' && auth()->user()->role === 'company_admin')
    <div class="card border-info mb-4">
        <div class="card-header bg-info bg-opacity-10 border-info">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="card-title mb-0 text-info">
                        <i class="bi bi-shield-check me-2"></i>
                        Company Admin - Final Approval Required
                    </h5>
                </div>
                <div class="col-auto">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#adminApproveModal">
                            <i class="bi bi-check-lg me-1"></i> Approve & Process Payment
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#adminRejectModal">
                            <i class="bi bi-x-lg me-1"></i> Reject
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <p class="mb-2">
                <strong>{{ $operativeInvoice->operative->name }}'s</strong> invoice has been approved by the site manager and requires your final approval.
            </p>
            <div class="alert alert-info mb-3">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Manager Approved:</strong> {{ $operativeInvoice->approved_at?->format('M j, Y \a\t g:i A') }}
            </div>
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="border rounded p-2">
                        <div class="small text-muted">Period</div>
                        <div class="fw-semibold">{{ $operativeInvoice->week_starting->format('M d') }} - {{ $operativeInvoice->week_ending->format('M d') }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-2">
                        <div class="small text-muted">Total Hours</div>
                        <div class="fw-semibold">{{ $operativeInvoice->total_hours }}h</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-2">
                        <div class="small text-muted">CIS Deduction</div>
                        <div class="fw-semibold text-warning">
                            @if($operativeInvoice->cis_applicable)
                                £{{ number_format($operativeInvoice->cis_deduction, 2) }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-2 bg-success bg-opacity-10">
                        <div class="small text-muted">Net Payment</div>
                        <div class="fw-bold text-success fs-5">£{{ number_format($operativeInvoice->net_amount, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row">
    <!-- Invoice Details -->
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-receipt me-2"></i>Invoice Details
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-2">Operative Information</h6>
                        <div class="mb-2">
                            <strong>Name:</strong> {{ $operativeInvoice->operative->name }}
                        </div>
                        <div class="mb-2">
                            <strong>Email:</strong> {{ $operativeInvoice->operative->email }}
                        </div>
                        <div class="mb-2">
                            <strong>Day Rate:</strong> £{{ number_format($operativeInvoice->day_rate, 2) }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-2">Work Location</h6>
                        <div class="mb-2">
                            <strong>Site:</strong> {{ $operativeInvoice->site->name ?? 'N/A' }}
                        </div>
                        @if($operativeInvoice->project)
                            <div class="mb-2">
                                <strong>Project:</strong> {{ $operativeInvoice->project->name }}
                            </div>
                        @endif
                        <div class="mb-2">
                            <strong>Manager:</strong> {{ $operativeInvoice->manager->name ?? 'N/A' }}
                        </div>
                    </div>
                </div>

                <!-- Time Summary -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="fw-bold mb-3">Time Summary</h6>
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="bg-primary bg-opacity-10 rounded p-3">
                                    <div class="h4 text-primary mb-1">{{ $operativeInvoice->total_hours }}</div>
                                    <small class="text-muted">Total Hours</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-info bg-opacity-10 rounded p-3">
                                    <div class="h4 text-info mb-1">{{ number_format($operativeInvoice->total_hours / 8, 1) }}</div>
                                    <small class="text-muted">Days Worked</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-success bg-opacity-10 rounded p-3">
                                    <div class="h4 text-success mb-1">£{{ number_format($operativeInvoice->gross_amount, 2) }}</div>
                                    <small class="text-muted">Gross Amount</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-warning bg-opacity-10 rounded p-3">
                                    <div class="h4 text-warning mb-1">£{{ number_format($operativeInvoice->net_amount, 2) }}</div>
                                    <small class="text-muted">Net Amount</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daily Breakdown -->
                @if($operativeInvoice->items->count() > 0)
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Daily Breakdown</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Day</th>
                                        <th>Hours</th>
                                        <th>Description</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($operativeInvoice->items as $item)
                                        <tr class="{{ $item->worked ? 'table-success' : 'table-light' }}">
                                            <td class="{{ $item->worked ? 'fw-bold' : 'text-muted' }}">
                                                {{ $item->work_date->format('M d, Y') }}
                                                @if($item->worked)
                                                    <i class="bi bi-check-circle-fill text-success ms-1"></i>
                                                @endif
                                            </td>
                                            <td class="{{ $item->worked ? 'fw-bold' : 'text-muted' }}">
                                                {{ $item->work_date->format('l') }}
                                            </td>
                                            <td>
                                                @if($item->worked)
                                                    <span class="badge bg-success">{{ $item->hours_worked }}h</span>
                                                @else
                                                    <span class="badge bg-secondary">0h</span>
                                                @endif
                                            </td>
                                            <td class="{{ $item->worked ? '' : 'text-muted' }}">
                                                {{ $item->worked ? ($item->description ?: 'Regular work') : 'Not worked' }}
                                            </td>
                                            <td class="text-end {{ $item->worked ? 'fw-bold text-success' : 'text-muted' }}">
                                                £{{ number_format($item->total_amount, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Notes -->
                @if($operativeInvoice->notes)
                    <div class="mb-3">
                        <h6 class="fw-bold mb-2">Notes</h6>
                        <div class="bg-light rounded p-3">
                            {!! nl2br(e($operativeInvoice->notes)) !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Invoice Summary -->
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-calculator me-2"></i>Payment Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Gross Amount:</span>
                    <strong>£{{ number_format($operativeInvoice->gross_amount, 2) }}</strong>
                </div>
                
                @if($operativeInvoice->cis_applicable)
                    <div class="d-flex justify-content-between mb-2 text-warning">
                        <span>CIS Deduction ({{ $operativeInvoice->cis_rate }}%):</span>
                        <strong>-£{{ number_format($operativeInvoice->cis_deduction, 2) }}</strong>
                    </div>
                @endif
                
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold">Net Amount:</span>
                    <strong class="text-success fs-5">£{{ number_format($operativeInvoice->net_amount, 2) }}</strong>
                </div>
                
                <!-- Status Badge -->
                <div class="text-center">
                    @switch($operativeInvoice->status)
                        @case('submitted')
                            <span class="badge bg-warning fs-6 px-3 py-2">Awaiting Approval</span>
                            @break
                        @case('approved')
                            <span class="badge bg-success fs-6 px-3 py-2">Approved</span>
                            @break
                        @case('rejected')
                            <span class="badge bg-danger fs-6 px-3 py-2">Rejected</span>
                            @break
                        @case('paid')
                            <span class="badge bg-info fs-6 px-3 py-2">Paid</span>
                            @break
                        @default
                            <span class="badge bg-secondary fs-6 px-3 py-2">{{ ucfirst($operativeInvoice->status) }}</span>
                    @endswitch
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>Timeline
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Invoice Created</h6>
                            <small class="text-muted">{{ $operativeInvoice->created_at->format('M d, Y \a\t g:i A') }}</small>
                        </div>
                    </div>
                    
                    @if($operativeInvoice->submitted_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Submitted for Approval</h6>
                                <small class="text-muted">{{ $operativeInvoice->submitted_at->format('M d, Y \a\t g:i A') }}</small>
                            </div>
                        </div>
                    @endif
                    
                    @if($operativeInvoice->approved_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Approved</h6>
                                <small class="text-muted">{{ $operativeInvoice->approved_at->format('M d, Y \a\t g:i A') }}</small>
                            </div>
                        </div>
                    @endif
                    
                    @if($operativeInvoice->paid_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Payment Processed</h6>
                                <small class="text-muted">{{ $operativeInvoice->paid_at->format('M d, Y \a\t g:i A') }}</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
@if($operativeInvoice->status === 'submitted')
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
                        <p>Are you sure you want to approve this invoice?</p>
                        
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
                        <p>Please provide a reason for rejecting this invoice.</p>
                        
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

{{-- Company Admin Modals --}}
@if($operativeInvoice->status === 'approved' && auth()->user()->role === 'company_admin')
    <!-- Admin Approve & Mark as Paid Modal -->
    <div class="modal fade" id="adminApproveModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success bg-opacity-10">
                    <h5 class="modal-title text-success">
                        <i class="bi bi-shield-check me-2"></i>Final Approval & Payment Processing
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.operative-invoices.mark-paid', $operativeInvoice->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>Manager Approved:</strong> This invoice has been approved by {{ $operativeInvoice->manager->name }} on {{ $operativeInvoice->approved_at?->format('M j, Y \a\t g:i A') }}.
                        </div>
                        
                        <p><strong>By clicking "Approve & Mark as Paid", you will:</strong></p>
                        <ul class="mb-3">
                            <li>Give final company approval to this invoice</li>
                            <li>Mark the invoice as paid</li>
                            <li>Complete the payment process</li>
                        </ul>
                        
                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Payment Notes (Optional)</label>
                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" 
                                      placeholder="Add any notes about this payment..."></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Payment Summary</h6>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="small text-muted">Gross Amount</div>
                                                <div class="fw-bold">£{{ number_format($operativeInvoice->gross_amount, 2) }}</div>
                                            </div>
                                            <div class="col-6">
                                                <div class="small text-muted">CIS Deduction</div>
                                                <div class="fw-bold text-warning">
                                                    @if($operativeInvoice->cis_applicable)
                                                        -£{{ number_format($operativeInvoice->cis_deduction, 2) }}
                                                    @else
                                                        £0.00
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="fs-4 fw-bold text-success">
                                            Net Payment: £{{ number_format($operativeInvoice->net_amount, 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Work Summary</h6>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="small text-muted">Total Hours</div>
                                                <div class="fw-bold">{{ $operativeInvoice->total_hours }}h</div>
                                            </div>
                                            <div class="col-6">
                                                <div class="small text-muted">Days Worked</div>
                                                <div class="fw-bold">{{ $operativeInvoice->items->where('worked', true)->count() }}</div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="small text-muted">Period</div>
                                        <div class="fw-bold">{{ $operativeInvoice->week_starting->format('M d') }} - {{ $operativeInvoice->week_ending->format('M d, Y') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle me-2"></i>Approve & Mark as Paid
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Admin Reject Modal -->
    <div class="modal fade" id="adminRejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger bg-opacity-10">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-x-circle me-2"></i>Company Admin Rejection
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('invoices.operative.reject', $operativeInvoice->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="admin_rejection" value="1">
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Note:</strong> This invoice was already approved by the site manager. Your rejection will override their approval.
                        </div>
                        
                        <p>Please provide a detailed reason for rejecting this invoice:</p>
                        
                        <div class="mb-3">
                            <label for="admin_rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="admin_rejection_reason" name="rejection_reason" rows="4" 
                                      placeholder="Explain why this invoice is being rejected..." required></textarea>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Invoice Summary:</strong><br>
                            Net Amount: £{{ number_format($operativeInvoice->net_amount, 2) }}<br>
                            Total Hours: {{ $operativeInvoice->total_hours }}h<br>
                            Period: {{ $operativeInvoice->week_starting->format('M d') }} - {{ $operativeInvoice->week_ending->format('M d, Y') }}
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

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
}

.timeline-marker {
    position: absolute;
    left: -12px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 1px #dee2e6;
}

.timeline-content {
    margin-left: 20px;
}

.timeline-content h6 {
    margin-bottom: 0.25rem;
    font-weight: 600;
}

.bg-primary.bg-opacity-10,
.bg-info.bg-opacity-10,
.bg-success.bg-opacity-10,
.bg-warning.bg-opacity-10 {
    border: 1px solid rgba(var(--bs-primary-rgb), 0.2);
}
</style>
@endpush
