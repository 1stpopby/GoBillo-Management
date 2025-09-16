<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Financial Report</h5>
    <div class="btn-group btn-group-sm" role="group">
        <input type="radio" class="btn-check" name="periodFilter" id="ytd-period" autocomplete="off" checked>
        <label class="btn btn-outline-primary" for="ytd-period">YTD</label>

        <input type="radio" class="btn-check" name="periodFilter" id="quarterly-period" autocomplete="off">
        <label class="btn btn-outline-primary" for="quarterly-period">Quarterly</label>

        <input type="radio" class="btn-check" name="periodFilter" id="monthly-period" autocomplete="off">
        <label class="btn btn-outline-primary" for="monthly-period">Monthly</label>
    </div>
</div>

<!-- Financial Summary Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">${{ number_format($financialSummary['total_expenses'], 0) }}</h4>
                        <p class="mb-0">Total Expenses</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-currency-dollar display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">${{ number_format($financialSummary['pending_expenses'], 0) }}</h4>
                        <p class="mb-0">Pending Expenses</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-clock display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">${{ number_format($financialSummary['reimbursed_expenses'], 0) }}</h4>
                        <p class="mb-0">Reimbursed</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-check-circle display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">${{ number_format($financialSummary['billable_expenses'], 0) }}</h4>
                        <p class="mb-0">Billable Expenses</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-receipt display-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Expense Breakdown -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Recent Expenses</h6>
            </div>
            <div class="card-body">
                @if($expenses->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Project</th>
                                    <th class="text-end">Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->expense_date->format('M j') }}</td>
                                        <td>
                                            <a href="{{ route('expenses.show', $expense) }}" class="text-decoration-none">
                                                {{ $expense->description }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $expense->category }}</span>
                                        </td>
                                        <td>
                                            @if($expense->project)
                                                <a href="{{ route('projects.show', $expense->project) }}" class="text-decoration-none small">
                                                    {{ Str::limit($expense->project->name, 20) }}
                                                </a>
                                            @else
                                                <span class="text-muted small">General</span>
                                            @endif
                                        </td>
                                        <td class="text-end">${{ number_format($expense->amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $expense->status === 'approved' ? 'success' : ($expense->status === 'submitted' ? 'warning' : ($expense->status === 'reimbursed' ? 'primary' : 'secondary')) }}">
                                                {{ ucfirst($expense->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('expenses.index') }}?user={{ $employee->user_id }}" class="btn btn-outline-primary btn-sm">
                            View All Expenses
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-receipt text-muted display-4"></i>
                        <p class="text-muted mt-2">No expenses recorded</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Expense Categories -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Expense Categories</h6>
            </div>
            <div class="card-body">
                @php
                    $expenseCategories = $expenses->groupBy('category')->map(function($group) {
                        return [
                            'count' => $group->count(),
                            'total' => $group->sum('amount')
                        ];
                    })->sortByDesc('total');
                @endphp
                
                @if($expenseCategories->count() > 0)
                    @foreach($expenseCategories->take(5) as $category => $data)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="fw-medium">{{ ucfirst($category) }}</div>
                                <small class="text-muted">{{ $data['count'] }} expense(s)</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">${{ number_format($data['total'], 0) }}</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-3">
                        <i class="bi bi-pie-chart text-muted display-6"></i>
                        <p class="text-muted mt-2 mb-0">No categories</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Payroll Information (Placeholder) -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Payroll Information</h6>
        <span class="badge bg-secondary">Coming Soon</span>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <i class="bi bi-cash-stack display-4 text-muted"></i>
                        <h6 class="mt-3">Salary & Wages</h6>
                        <p class="text-muted mb-0">Payroll integration coming soon</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <i class="bi bi-calculator display-4 text-muted"></i>
                        <h6 class="mt-3">Tax & Deductions</h6>
                        <p class="text-muted mb-0">PAYE, NI, and pension calculations</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Financial Timeline -->
<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Financial Timeline</h6>
    </div>
    <div class="card-body">
        @if($expenses->count() > 0)
            <div class="timeline">
                @foreach($expenses->take(5) as $expense)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-{{ $expense->status === 'approved' ? 'success' : 'warning' }}"></div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $expense->description }}</h6>
                                    <p class="text-muted mb-1">{{ $expense->category }} • {{ $expense->expense_date->format('M j, Y') }}</p>
                                    @if($expense->project)
                                        <small class="text-muted">Project: {{ $expense->project->name }}</small>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">${{ number_format($expense->amount, 2) }}</div>
                                    <span class="badge bg-{{ $expense->status === 'approved' ? 'success' : ($expense->status === 'submitted' ? 'warning' : 'secondary') }} badge-sm">
                                        {{ ucfirst($expense->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-clock-history display-4 text-muted"></i>
                <h6 class="mt-3">No Financial Activity</h6>
                <p class="text-muted">No financial transactions to display</p>
            </div>
        @endif
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -2.25rem;
    top: 0.25rem;
    width: 0.75rem;
    height: 0.75rem;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    background: #f8f9fa;
    border-radius: 0.375rem;
    padding: 1rem;
    border-left: 3px solid #dee2e6;
}
</style>


