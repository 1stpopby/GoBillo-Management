<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Construction Industry Scheme (CIS)</h5>
    @if(auth()->user()->canManageCompanyUsers())
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateCISModal">
            <i class="bi bi-pencil me-1"></i>Update CIS Info
        </button>
    @endif
</div>

<div class="row">
    <!-- CIS Registration -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0">CIS Registration</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="detail-group">
                            <label class="detail-label">Registration Number</label>
                            <div class="detail-value">
                                @if($cisData['registration_number'])
                                    <span class="font-monospace">{{ $cisData['registration_number'] }}</span>
                                @else
                                    <span class="text-muted">Not registered</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="detail-group">
                            <label class="detail-label">Verification Status</label>
                            <div class="detail-value">
                                <span class="badge bg-{{ $cisData['verification_status'] === 'verified' ? 'success' : ($cisData['verification_status'] === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($cisData['verification_status']) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="detail-group">
                            <label class="detail-label">Last Verification</label>
                            <div class="detail-value">
                                <span class="text-muted">Not available</span>
                                <small class="d-block text-muted">Feature coming soon</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CIS Deductions Summary -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0">Deductions Summary (YTD)</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="detail-group">
                            <label class="detail-label">Gross Payments (YTD)</label>
                            <div class="detail-value fw-bold">
                                £{{ number_format($cisData['gross_payments_ytd'], 2) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="detail-group">
                            <label class="detail-label">CIS Deductions (YTD)</label>
                            <div class="detail-value fw-bold text-warning">
                                £{{ number_format($cisData['deductions_ytd'], 2) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="detail-group">
                            <label class="detail-label">Net Payments (YTD)</label>
                            <div class="detail-value fw-bold text-success">
                                £{{ number_format($cisData['net_payments_ytd'], 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent CIS Payments -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Recent CIS Payments</h6>
        @if(auth()->user()->canManageProjects())
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCisPaymentModal">
                <i class="bi bi-plus-circle me-1"></i>Add Payment
            </button>
        @endif
    </div>
    <div class="card-body">
        @php
            $recentPayments = $employee->cisPayments()->with('project')->latest('payment_date')->limit(5)->get();
        @endphp
        
        @if($recentPayments->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Project</th>
                            <th>Gross Amount</th>
                            <th>CIS Rate</th>
                            <th>Deduction</th>
                            <th>Net Payment</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentPayments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                <td>
                                    @if($payment->project)
                                        <a href="{{ route('projects.show', $payment->project) }}" class="text-decoration-none">
                                            {{ Str::limit($payment->project->name, 20) }}
                                        </a>
                                    @else
                                        <span class="text-muted">General</span>
                                    @endif
                                </td>
                                <td>£{{ number_format($payment->gross_amount, 2) }}</td>
                                <td>{{ number_format($payment->cis_rate, 1) }}%</td>
                                <td class="text-warning">£{{ number_format($payment->cis_deduction, 2) }}</td>
                                <td class="text-success fw-bold">£{{ number_format($payment->net_payment, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $payment->status_color }}">
                                        {{ $payment->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="text-center mt-3">
                <a href="{{ route('cis.payments') }}?employee_id={{ $employee->id }}" class="btn btn-outline-primary btn-sm">
                    View All Payments
                </a>
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-receipt text-muted display-4"></i>
                <h6 class="mt-3">No CIS Payments</h6>
                <p class="text-muted">No CIS payments have been recorded for this employee.</p>
                @if(auth()->user()->canManageProjects())
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCisPaymentModal">
                        <i class="bi bi-plus-circle me-2"></i>Record First Payment
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- CIS Statistics -->
@if($cisData['payment_count'] > 0)
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">CIS Statistics ({{ now()->year }})</h6>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="fs-4 fw-bold text-primary">{{ $cisData['payment_count'] }}</div>
                        <div class="text-muted small">Total Payments</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="fs-4 fw-bold text-warning">{{ number_format($cisData['average_rate'], 1) }}%</div>
                        <div class="text-muted small">Average Rate</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="fs-4 fw-bold text-info">
                            @if($cisData['last_payment_date'])
                                {{ \Carbon\Carbon::parse($cisData['last_payment_date'])->format('M j') }}
                            @else
                                N/A
                            @endif
                        </div>
                        <div class="text-muted small">Last Payment</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="fs-4 fw-bold text-success">
                            {{ $cisData['deductions_ytd'] > 0 ? number_format(($cisData['deductions_ytd'] / $cisData['gross_payments_ytd']) * 100, 1) : 0 }}%
                        </div>
                        <div class="text-muted small">Effective Rate</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- CIS Status Information -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0">CIS Information</h6>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <h6 class="alert-heading">
                <i class="bi bi-info-circle me-2"></i>Construction Industry Scheme (CIS)
            </h6>
            <p class="mb-2">
                The Construction Industry Scheme (CIS) is a tax deduction scheme that applies to most payments made to subcontractors by contractors in the construction industry.
            </p>
            <hr>
            <div class="mb-0">
                <strong>Key Points:</strong>
                <ul class="mb-0 mt-2">
                    <li>Contractors must register for CIS and verify subcontractors</li>
                    <li>Standard deduction rate is 20% for registered subcontractors</li>
                    <li>Unregistered subcontractors face 30% deductions</li>
                    <li>Monthly returns must be filed by 19th of each month</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Monthly CIS Returns (Placeholder) -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Monthly CIS Returns</h6>
        <span class="badge bg-secondary">Coming Soon</span>
    </div>
    <div class="card-body">
        <div class="text-center py-4">
            <i class="bi bi-calendar-check display-4 text-muted"></i>
            <h6 class="mt-3">CIS Returns Tracking</h6>
            <p class="text-muted">Monthly CIS return tracking and filing status will be available soon.</p>
        </div>
    </div>
</div>

<!-- Compliance Checklist -->
<div class="card">
    <div class="card-header">
        <h6 class="mb-0">CIS Compliance Checklist</h6>
    </div>
    <div class="card-body">
        <div class="list-group list-group-flush">
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-{{ $cisData['registration_number'] ? 'check-circle text-success' : 'x-circle text-danger' }} me-2"></i>
                    CIS Registration
                </div>
                <span class="badge bg-{{ $cisData['registration_number'] ? 'success' : 'danger' }}">
                    {{ $cisData['registration_number'] ? 'Complete' : 'Required' }}
                </span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-{{ $cisData['verification_status'] === 'verified' ? 'check-circle text-success' : 'clock text-warning' }} me-2"></i>
                    Verification Status
                </div>
                <span class="badge bg-{{ $cisData['verification_status'] === 'verified' ? 'success' : 'warning' }}">
                    {{ ucfirst($cisData['verification_status']) }}
                </span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-clock text-secondary me-2"></i>
                    Monthly Returns
                </div>
                <span class="badge bg-secondary">Pending Setup</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-file-earmark-text text-secondary me-2"></i>
                    Payment Records
                </div>
                <span class="badge bg-secondary">Pending Setup</span>
            </div>
        </div>
    </div>
</div>

<!-- CIS Modal will be in main employee page -->

<style>
/* Fix modal display issues */
.modal {
    z-index: 1055 !important;
}

.modal-backdrop {
    z-index: 1050 !important;
    background-color: rgba(0, 0, 0, 0.5) !important;
}

.modal-content {
    background-color: #fff !important;
    border: 1px solid rgba(0, 0, 0, 0.2) !important;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.modal-header {
    background-color: #f8f9fa !important;
    border-bottom: 1px solid #dee2e6 !important;
}

.modal-body {
    padding: 1.5rem !important;
}

.modal-footer {
    background-color: #f8f9fa !important;
    border-top: 1px solid #dee2e6 !important;
}

/* Ensure form elements are visible */
.form-control,
.form-select {
    background-color: #fff !important;
    border: 1px solid #ced4da !important;
    color: #495057 !important;
}

.form-control:focus,
.form-select:focus {
    border-color: #80bdff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
}

.form-label {
    color: #495057 !important;
    font-weight: 600 !important;
}

.form-text {
    color: #6c757d !important;
}

.alert-warning {
    background-color: #fff3cd !important;
    border-color: #ffeaa7 !important;
    color: #856404 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ensure modal works properly
    const updateCISModal = document.getElementById('updateCISModal');
    if (updateCISModal) {
        updateCISModal.addEventListener('shown.bs.modal', function () {
            // Focus on first input when modal is shown
            const firstInput = updateCISModal.querySelector('input, select');
            if (firstInput) {
                firstInput.focus();
            }
        });
        
        // Ensure modal is properly reset when hidden
        updateCISModal.addEventListener('hidden.bs.modal', function () {
            // Reset form if needed
            const form = updateCISModal.querySelector('form');
            if (form) {
                // Don't reset form as user might want to keep their changes
            }
        });
    }
});
</script>
