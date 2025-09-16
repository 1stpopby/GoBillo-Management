@extends('layouts.app')

@section('title', 'CIS Returns')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">CIS Returns</h1>
                    <p class="text-muted">Manage monthly CIS returns and HMRC submissions</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('cis.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                    </a>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createReturnModal">
                        <i class="bi bi-file-plus me-2"></i>Create Return
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="tax_year" class="form-label">Tax Year</label>
                    <select class="form-select" id="tax_year" name="tax_year">
                        <option value="">All Years</option>
                        @foreach($taxYears as $year)
                            <option value="{{ $year }}" {{ request('tax_year') == $year ? 'selected' : '' }}>
                                {{ $year }}/{{ $year + 1 }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-funnel me-2"></i>Filter
                    </button>
                    <a href="{{ route('cis.returns') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Returns List -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">CIS Returns</h6>
        </div>
        <div class="card-body">
            @if($returns->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Due Date</th>
                                <th>Subcontractors</th>
                                <th>Total Payments</th>
                                <th>Total Deductions</th>
                                <th>Status</th>
                                <th>Prepared By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($returns as $return)
                                <tr>
                                    <td>
                                        <strong>{{ $return->period_description }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ $return->period_start->format('d/m/Y') }} - {{ $return->period_end->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        {{ $return->formatted_due_date }}
                                        @if($return->isOverdue())
                                            <br><span class="badge bg-danger">Overdue</span>
                                        @elseif($return->due_date->diffInDays(now()) <= 7 && $return->status === 'draft')
                                            <br><span class="badge bg-warning">Due Soon</span>
                                        @endif
                                        @if($return->is_late)
                                            <br><span class="badge bg-danger">Late Submission</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $return->total_subcontractors }}</span>
                                        @if($return->total_subcontractors > 0)
                                            <br><small class="text-muted">contractors</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold">£{{ number_format($return->total_payments, 2) }}</span>
                                        @if($return->total_materials > 0)
                                            <br><small class="text-muted">Materials: £{{ number_format($return->total_materials, 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold text-warning">£{{ number_format($return->total_deductions, 2) }}</span>
                                        @if($return->total_payments > 0)
                                            <br><small class="text-muted">
                                                {{ number_format(($return->total_deductions / $return->total_payments) * 100, 1) }}% rate
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $return->status_color }}">
                                            {{ $return->status_label }}
                                        </span>
                                        @if($return->hmrc_reference)
                                            <br><small class="text-muted">{{ $return->hmrc_reference }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $return->preparedBy->name }}
                                        @if($return->submitted_by)
                                            <br><small class="text-muted">
                                                Submitted by: {{ $return->submittedBy->name }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('cis.returns.show', $return) }}" class="btn btn-outline-primary" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($return->canBeSubmitted())
                                                <form action="{{ route('cis.returns.submit', $return) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="Submit to HMRC"
                                                            onclick="return confirm('Are you sure you want to submit this return to HMRC?')">
                                                        <i class="bi bi-send"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('cis.returns.report', $return) }}" class="btn btn-outline-info" title="Generate Report">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $returns->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-file-text text-muted display-1"></i>
                    <h4 class="mt-3">No CIS Returns Found</h4>
                    <p class="text-muted">No CIS returns match your current filters.</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createReturnModal">
                        <i class="bi bi-file-plus me-2"></i>Create First Return
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Create Return Modal -->
<div class="modal fade" id="createReturnModal" tabindex="-1" aria-labelledby="createReturnModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createReturnModalLabel">Create CIS Return</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('cis.returns.create') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="tax_year" class="form-label">Tax Year <span class="text-danger">*</span></label>
                            <select class="form-select" id="tax_year" name="tax_year" required>
                                @for($year = now()->year - 1; $year <= now()->year + 1; $year++)
                                    <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>
                                        {{ $year }}/{{ $year + 1 }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="tax_month" class="form-label">Tax Month <span class="text-danger">*</span></label>
                            <select class="form-select" id="tax_month" name="tax_month" required>
                                @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $index => $month)
                                    <option value="{{ $index + 1 }}" {{ ($index + 1) == now()->month ? 'selected' : '' }}>
                                        {{ $month }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3">
                        <small>
                            <i class="bi bi-info-circle me-2"></i>
                            This will create a new CIS return and automatically include all verified payments from the selected period.
                            The return will be due on the 19th of the following month.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Return</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


