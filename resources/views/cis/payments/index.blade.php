@extends('layouts.app')

@section('title', 'CIS Payments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">CIS Payments</h1>
                    <p class="text-muted">Manage CIS payments and deductions</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('cis.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                    </a>
                    <a href="{{ route('cis.payments.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Record Payment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <label for="employee_id" class="form-label">Employee</label>
                    <select class="form-select" id="employee_id" name="employee_id">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->full_name }}
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
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-funnel me-2"></i>Filter
                    </button>
                    <a href="{{ route('cis.payments') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Payments List -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">CIS Payments</h6>
        </div>
        <div class="card-body">
            @if($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Payment Date</th>
                                <th>Employee</th>
                                <th>Project</th>
                                <th>Reference</th>
                                <th>Gross Amount</th>
                                <th>Materials</th>
                                <th>CIS Rate</th>
                                <th>CIS Deduction</th>
                                <th>Net Payment</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('employees.show', $payment->employee) }}" class="text-decoration-none">
                                            <strong>{{ $payment->employee->full_name }}</strong>
                                        </a>
                                        @if($payment->employee->cis_number)
                                            <br><small class="text-muted">{{ $payment->employee->cis_number }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->project)
                                            <a href="{{ route('projects.show', $payment->project) }}" class="text-decoration-none">
                                                {{ Str::limit($payment->project->name, 25) }}
                                            </a>
                                        @else
                                            <span class="text-muted">General</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->payment_reference)
                                            <code>{{ $payment->payment_reference }}</code>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold">£{{ number_format($payment->gross_amount, 2) }}</td>
                                    <td>£{{ number_format($payment->materials_cost, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $payment->cis_rate == 20 ? 'success' : 'warning' }}">
                                            {{ number_format($payment->cis_rate, 1) }}%
                                        </span>
                                    </td>
                                    <td class="text-warning fw-bold">£{{ number_format($payment->cis_deduction, 2) }}</td>
                                    <td class="text-success fw-bold">£{{ number_format($payment->net_payment, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $payment->status_color }}">
                                            {{ $payment->status_label }}
                                        </span>
                                        @if($payment->included_in_return)
                                            <br><small class="text-info">In Return</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('cis.payments.show', $payment) }}" class="btn btn-outline-primary" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($payment->status === 'draft' && auth()->user()->canManageProjects())
                                                <form action="{{ route('cis.payments.verify', $payment) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="Verify Payment"
                                                            onclick="return confirm('Are you sure you want to verify this payment?')">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4">Totals ({{ $payments->total() }} payments)</th>
                                <th>£{{ number_format($payments->sum('gross_amount'), 2) }}</th>
                                <th>£{{ number_format($payments->sum('materials_cost'), 2) }}</th>
                                <th>{{ $payments->count() > 0 ? number_format($payments->avg('cis_rate'), 1) : 0 }}%</th>
                                <th class="text-warning">£{{ number_format($payments->sum('cis_deduction'), 2) }}</th>
                                <th class="text-success">£{{ number_format($payments->sum('net_payment'), 2) }}</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $payments->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-receipt text-muted display-1"></i>
                    <h4 class="mt-3">No CIS Payments Found</h4>
                    <p class="text-muted">No CIS payments match your current filters.</p>
                    <a href="{{ route('cis.payments.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Record First Payment
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection


