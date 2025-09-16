@extends('layouts.app')

@section('title', 'CIS Return - ' . $cisReturn->period_description)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">CIS Return - {{ $cisReturn->period_description }}</h1>
                    <p class="text-muted">
                        Period: {{ $cisReturn->period_start->format('d/m/Y') }} - {{ $cisReturn->period_end->format('d/m/Y') }}
                        • Due: {{ $cisReturn->formatted_due_date }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('cis.returns') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Returns
                    </a>
                    @if($cisReturn->canBeSubmitted())
                        <form action="{{ route('cis.returns.submit', $cisReturn) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success"
                                    onclick="return confirm('Are you sure you want to submit this return to HMRC?')">
                                <i class="bi bi-send me-2"></i>Submit to HMRC
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('cis.returns.report', $cisReturn) }}" class="btn btn-primary">
                        <i class="bi bi-file-earmark-text me-2"></i>Generate Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Return Status and Summary -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Return Summary</h6>
                    <span class="badge bg-{{ $cisReturn->status_color }} fs-6">
                        {{ $cisReturn->status_label }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="fs-3 fw-bold text-primary">{{ $cisReturn->total_subcontractors }}</div>
                                <div class="text-muted">Subcontractors</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="fs-3 fw-bold text-success">£{{ number_format($cisReturn->total_payments, 2) }}</div>
                                <div class="text-muted">Total Payments</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="fs-3 fw-bold text-warning">£{{ number_format($cisReturn->total_deductions, 2) }}</div>
                                <div class="text-muted">CIS Deductions</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="fs-3 fw-bold text-info">£{{ number_format($cisReturn->total_materials, 2) }}</div>
                                <div class="text-muted">Materials</div>
                            </div>
                        </div>
                    </div>
                    
                    @if($cisReturn->total_payments > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="progress" style="height: 25px;">
                                    @php
                                        $labourAmount = $cisReturn->total_payments - $cisReturn->total_materials;
                                        $materialsPercentage = ($cisReturn->total_materials / $cisReturn->total_payments) * 100;
                                        $deductionPercentage = ($cisReturn->total_deductions / $cisReturn->total_payments) * 100;
                                        $netPercentage = 100 - $materialsPercentage - $deductionPercentage;
                                    @endphp
                                    <div class="progress-bar bg-info" style="width: {{ $materialsPercentage }}%">
                                        Materials ({{ number_format($materialsPercentage, 1) }}%)
                                    </div>
                                    <div class="progress-bar bg-warning" style="width: {{ $deductionPercentage }}%">
                                        CIS ({{ number_format($deductionPercentage, 1) }}%)
                                    </div>
                                    <div class="progress-bar bg-success" style="width: {{ $netPercentage }}%">
                                        Net ({{ number_format($netPercentage, 1) }}%)
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Return Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label text-muted">Tax Year</label>
                            <div class="fw-bold">{{ $cisReturn->tax_year }}/{{ $cisReturn->tax_year + 1 }}</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Tax Month</label>
                            <div class="fw-bold">{{ \Carbon\Carbon::create()->month($cisReturn->tax_month)->format('F') }}</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Due Date</label>
                            <div class="fw-bold {{ $cisReturn->isOverdue() ? 'text-danger' : '' }}">
                                {{ $cisReturn->formatted_due_date }}
                                @if($cisReturn->isOverdue())
                                    <span class="badge bg-danger ms-2">Overdue</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Prepared By</label>
                            <div class="fw-bold">{{ $cisReturn->preparedBy->name }}</div>
                        </div>
                        @if($cisReturn->submitted_by)
                            <div class="col-12">
                                <label class="form-label text-muted">Submitted By</label>
                                <div class="fw-bold">{{ $cisReturn->submittedBy->name }}</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted">Submission Date</label>
                                <div class="fw-bold">{{ $cisReturn->submitted_at->format('d/m/Y H:i') }}</div>
                            </div>
                        @endif
                        @if($cisReturn->hmrc_reference)
                            <div class="col-12">
                                <label class="form-label text-muted">HMRC Reference</label>
                                <div class="fw-bold">{{ $cisReturn->hmrc_reference }}</div>
                            </div>
                        @endif
                        @if($cisReturn->is_late)
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    This return was submitted late and may incur penalties.
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Included Payments -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Included Payments</h6>
        </div>
        <div class="card-body">
            @if($cisReturn->cisPayments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Payment Date</th>
                                <th>Employee</th>
                                <th>CIS Number</th>
                                <th>Project</th>
                                <th>Gross Amount</th>
                                <th>Materials</th>
                                <th>Labour Amount</th>
                                <th>CIS Rate</th>
                                <th>CIS Deduction</th>
                                <th>Net Payment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cisReturn->cisPayments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('employees.show', $payment->employee) }}" class="text-decoration-none">
                                            {{ $payment->employee->full_name }}
                                        </a>
                                    </td>
                                    <td>
                                        <code>{{ $payment->employee->cis_number ?? 'N/A' }}</code>
                                    </td>
                                    <td>
                                        @if($payment->project)
                                            <a href="{{ route('projects.show', $payment->project) }}" class="text-decoration-none">
                                                {{ Str::limit($payment->project->name, 20) }}
                                            </a>
                                        @else
                                            <span class="text-muted">General</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold">£{{ number_format($payment->gross_amount, 2) }}</td>
                                    <td>£{{ number_format($payment->materials_cost, 2) }}</td>
                                    <td>£{{ number_format($payment->labour_amount, 2) }}</td>
                                    <td>{{ number_format($payment->cis_rate, 1) }}%</td>
                                    <td class="text-warning fw-bold">£{{ number_format($payment->cis_deduction, 2) }}</td>
                                    <td class="text-success fw-bold">£{{ number_format($payment->net_payment, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4">Totals</th>
                                <th>£{{ number_format($cisReturn->cisPayments->sum('gross_amount'), 2) }}</th>
                                <th>£{{ number_format($cisReturn->cisPayments->sum('materials_cost'), 2) }}</th>
                                <th>£{{ number_format($cisReturn->cisPayments->sum('labour_amount'), 2) }}</th>
                                <th>{{ $cisReturn->cisPayments->count() > 0 ? number_format($cisReturn->cisPayments->avg('cis_rate'), 1) : 0 }}%</th>
                                <th class="text-warning">£{{ number_format($cisReturn->cisPayments->sum('cis_deduction'), 2) }}</th>
                                <th class="text-success">£{{ number_format($cisReturn->cisPayments->sum('net_payment'), 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Subcontractor Summary -->
                <div class="mt-4">
                    <h6>Subcontractor Summary</h6>
                    <div class="row">
                        @php
                            $subcontractorSummary = $cisReturn->cisPayments->groupBy('employee_id')->map(function($payments) {
                                $employee = $payments->first()->employee;
                                return [
                                    'employee' => $employee,
                                    'payment_count' => $payments->count(),
                                    'total_gross' => $payments->sum('gross_amount'),
                                    'total_deduction' => $payments->sum('cis_deduction'),
                                    'total_net' => $payments->sum('net_payment'),
                                ];
                            });
                        @endphp
                        
                        @foreach($subcontractorSummary as $summary)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $summary['employee']->full_name }}</h6>
                                        <p class="card-text">
                                            <small class="text-muted">CIS: {{ $summary['employee']->cis_number ?? 'N/A' }}</small>
                                        </p>
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="fw-bold">{{ $summary['payment_count'] }}</div>
                                                <small class="text-muted">Payments</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="fw-bold">£{{ number_format($summary['total_gross'], 0) }}</div>
                                                <small class="text-muted">Gross</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="fw-bold text-warning">£{{ number_format($summary['total_deduction'], 0) }}</div>
                                                <small class="text-muted">CIS</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-receipt text-muted display-4"></i>
                    <h6 class="mt-3">No Payments Included</h6>
                    <p class="text-muted">This return does not include any CIS payments yet.</p>
                    <a href="{{ route('cis.payments') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>View Payments
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection


