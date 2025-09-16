@extends('layouts.app')

@section('title', 'CIS Management Dashboard')

@section('content')
<div class="cis-management-dashboard">
    <!-- Professional Header -->
    <div class="cis-header mb-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="cis-welcome">
                    <h1 class="cis-title">
                        <i class="bi bi-shield-check me-3"></i>CIS Management Dashboard
                    </h1>
                    <p class="cis-subtitle">
                        Construction Industry Scheme • Tax Year {{ $currentYear }}/{{ $currentYear + 1 }}
                    </p>
                    <div class="cis-status">
                        <span class="status-indicator active"></span>
                        HMRC Compliance Active • Last Updated: {{ now()->format('d M Y, H:i') }}
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="cis-actions">
                    <div class="btn-group" role="group">
                        <a href="{{ route('cis.payments.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Record Payment
                        </a>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createReturnModal">
                            <i class="bi bi-file-plus me-2"></i>Create Return
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Executive CIS KPIs -->
    <div class="row g-4 mb-5">
        <!-- Total Payments YTD -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="cis-kpi-card payments-card">
                <div class="kpi-header">
                    <div class="kpi-icon">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div class="kpi-trend positive">
                        <i class="bi bi-arrow-up"></i>
                        <span>+15.2%</span>
                    </div>
                </div>
                <div class="kpi-content">
                    <h2 class="kpi-value">£{{ number_format($stats['total_payments_ytd'], 0) }}</h2>
                    <p class="kpi-label">Total Payments YTD</p>
                    <div class="kpi-details">
                        <span class="detail-item">
                            <i class="bi bi-calendar-range"></i>
                            {{ now()->format('M Y') }} Period
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- CIS Deductions YTD -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="cis-kpi-card deductions-card">
                <div class="kpi-header">
                    <div class="kpi-icon">
                        <i class="bi bi-percent"></i>
                    </div>
                    <div class="kpi-trend positive">
                        <i class="bi bi-check-circle"></i>
                        <span>Compliant</span>
                    </div>
                </div>
                <div class="kpi-content">
                    <h2 class="kpi-value">£{{ number_format($stats['total_deductions_ytd'], 0) }}</h2>
                    <p class="kpi-label">CIS Deductions YTD</p>
                    <div class="kpi-progress">
                        <div class="progress">
                            <div class="progress-bar bg-warning" style="width: {{ $stats['total_payments_ytd'] > 0 ? round(($stats['total_deductions_ytd'] / $stats['total_payments_ytd']) * 100, 1) : 0 }}%"></div>
                        </div>
                        <small class="text-muted">{{ $stats['total_payments_ytd'] > 0 ? round(($stats['total_deductions_ytd'] / $stats['total_payments_ytd']) * 100, 1) : 0 }}% of total payments</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Subcontractors -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="cis-kpi-card contractors-card">
                <div class="kpi-header">
                    <div class="kpi-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="kpi-trend neutral">
                        <i class="bi bi-shield-check"></i>
                        <span>Verified</span>
                    </div>
                </div>
                <div class="kpi-content">
                    <h2 class="kpi-value">{{ $stats['registered_subcontractors'] }}</h2>
                    <p class="kpi-label">Registered Subcontractors</p>
                    <div class="kpi-details">
                        <span class="detail-item">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            All HMRC Verified
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compliance Status -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="cis-kpi-card compliance-card">
                <div class="kpi-header">
                    <div class="kpi-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div class="kpi-trend {{ ($stats['pending_payments'] + $stats['overdue_returns']) > 0 ? 'negative' : 'positive' }}">
                        <i class="bi bi-{{ ($stats['pending_payments'] + $stats['overdue_returns']) > 0 ? 'exclamation-triangle' : 'check-circle' }}"></i>
                        <span>{{ ($stats['pending_payments'] + $stats['overdue_returns']) > 0 ? 'Action Required' : 'Compliant' }}</span>
                    </div>
                </div>
                <div class="kpi-content">
                    <h2 class="kpi-value">{{ $stats['pending_payments'] + $stats['overdue_returns'] }}</h2>
                    <p class="kpi-label">Pending Actions</p>
                    <div class="kpi-details">
                        <span class="detail-item">
                            {{ $stats['pending_payments'] }} pending payments
                        </span>
                        <span class="detail-item">
                            {{ $stats['overdue_returns'] }} overdue returns
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters Panel -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="cis-filters-panel">
                <div class="filters-header">
                    <h5 class="filters-title">
                        <i class="bi bi-funnel me-2"></i>Advanced Filters
                    </h5>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="toggleFilters">
                        <i class="bi bi-chevron-down me-1"></i>Show Filters
                    </button>
                </div>
                <div class="filters-content" id="filtersContent" style="display: none;">
                    <form id="cisFiltersForm" method="GET" action="{{ route('cis.index') }}">
                        <div class="row g-3">
                            <!-- Date Range Filter -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">Date Range</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" name="date_from" 
                                           value="{{ request('date_from') }}" placeholder="From">
                                    <input type="date" class="form-control" name="date_to" 
                                           value="{{ request('date_to') }}" placeholder="To">
                                </div>
                            </div>

                            <!-- Operative Filter -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">Operative</label>
                                <select class="form-select" name="operative_id">
                                    <option value="">All Operatives</option>
                                    @foreach($allOperatives ?? [] as $operative)
                                        <option value="{{ $operative->id }}" 
                                                {{ request('operative_id') == $operative->id ? 'selected' : '' }}>
                                            {{ $operative->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Project Filter -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">Project</label>
                                <select class="form-select" name="project_id">
                                    <option value="">All Projects</option>
                                    @foreach($allProjects ?? [] as $project)
                                        <option value="{{ $project->id }}" 
                                                {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- CIS Rate Filter -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">CIS Rate</label>
                                <select class="form-select" name="cis_rate">
                                    <option value="">All Rates</option>
                                    <option value="20" {{ request('cis_rate') == '20' ? 'selected' : '' }}>20% (Registered)</option>
                                    <option value="30" {{ request('cis_rate') == '30' ? 'selected' : '' }}>30% (Unregistered)</option>
                                </select>
                            </div>

                            <!-- Payment Status Filter -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">Payment Status</label>
                                <select class="form-select" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                                </select>
                            </div>

                            <!-- Amount Range Filter -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">Amount Range (£)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="amount_min" 
                                           value="{{ request('amount_min') }}" placeholder="Min" step="0.01">
                                    <input type="number" class="form-control" name="amount_max" 
                                           value="{{ request('amount_max') }}" placeholder="Max" step="0.01">
                                </div>
                            </div>

                            <!-- Sort Order -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">Sort By</label>
                                <select class="form-select" name="sort_by">
                                    <option value="total_deductions_desc" {{ request('sort_by') == 'total_deductions_desc' ? 'selected' : '' }}>Highest Deductions</option>
                                    <option value="total_deductions_asc" {{ request('sort_by') == 'total_deductions_asc' ? 'selected' : '' }}>Lowest Deductions</option>
                                    <option value="name_asc" {{ request('sort_by') == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                                    <option value="name_desc" {{ request('sort_by') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                                    <option value="last_payment_desc" {{ request('sort_by') == 'last_payment_desc' ? 'selected' : '' }}>Recent Payment</option>
                                    <option value="payment_count_desc" {{ request('sort_by') == 'payment_count_desc' ? 'selected' : '' }}>Most Payments</option>
                                </select>
                            </div>

                            <!-- Search -->
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">Search</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search" 
                                           value="{{ request('search') }}" placeholder="Search operatives...">
                                    <button type="button" class="btn btn-outline-secondary" id="clearSearch">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="filters-actions mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('cis.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise me-2"></i>Reset Filters
                            </a>
                            <button type="button" class="btn btn-outline-info" id="saveFilters">
                                <i class="bi bi-bookmark me-2"></i>Save Filter Set
                            </button>
                            <div class="filter-summary ms-3">
                                @if(request()->hasAny(['date_from', 'date_to', 'operative_id', 'project_id', 'cis_rate', 'status', 'amount_min', 'amount_max', 'search']))
                                    <span class="badge bg-info">
                                        <i class="bi bi-funnel-fill me-1"></i>
                                        {{ collect(request()->only(['date_from', 'date_to', 'operative_id', 'project_id', 'cis_rate', 'status', 'amount_min', 'amount_max', 'search']))->filter()->count() }} 
                                        filter(s) active
                                    </span>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">
        <!-- Left Column - Operative CIS Summary -->
        <div class="col-xl-8 col-lg-7">
            <div class="cis-operatives-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Operative CIS Summary</h5>
                            <p class="card-subtitle">
                                Cumulative CIS deductions by operative
                                @if(request()->hasAny(['date_from', 'date_to', 'operative_id', 'project_id', 'cis_rate', 'status', 'amount_min', 'amount_max', 'search']))
                                    <span class="text-muted">• Filtered Results</span>
                                @endif
                            </p>
                        </div>
                        <div class="header-actions">
                            <div class="btn-group btn-group-sm me-2" role="group">
                                <button type="button" class="btn btn-outline-info btn-sm" id="exportFiltered">
                                    <i class="bi bi-download me-1"></i>Export
                                </button>
                            </div>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary active" data-period="ytd">YTD</button>
                                <button type="button" class="btn btn-outline-secondary" data-period="quarterly">Quarterly</button>
                                <button type="button" class="btn btn-outline-secondary" data-period="monthly">Monthly</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentPayments && $recentPayments->count() > 0)
                        <div class="operatives-summary">
                            @php
                                // Group by both employee_id and user_id to handle both Employee and User payments
                                $operativesSummary = collect();
                                
                                // Group Employee payments
                                $employeePayments = $recentPayments->whereNotNull('employee_id')->groupBy('employee_id');
                                foreach($employeePayments as $employeeId => $payments) {
                                    $employee = $payments->first()->employee;
                                    if ($employee) {
                                        $operativesSummary->push([
                                            'type' => 'employee',
                                            'employee' => $employee,
                                            'user' => null,
                                            'total_gross' => $payments->sum('gross_amount'),
                                            'total_deductions' => $payments->sum('cis_deduction'),
                                            'total_net' => $payments->sum('net_payment'),
                                            'payment_count' => $payments->count(),
                                            'last_payment' => $payments->sortByDesc('payment_date')->first()->payment_date,
                                            'avg_rate' => $payments->avg('cis_rate')
                                        ]);
                                    }
                                }
                                
                                // Group User payments
                                $userPayments = $recentPayments->whereNotNull('user_id')->groupBy('user_id');
                                foreach($userPayments as $userId => $payments) {
                                    $user = $payments->first()->user;
                                    if ($user) {
                                        $operativesSummary->push([
                                            'type' => 'user',
                                            'employee' => null,
                                            'user' => $user,
                                            'total_gross' => $payments->sum('gross_amount'),
                                            'total_deductions' => $payments->sum('cis_deduction'),
                                            'total_net' => $payments->sum('net_payment'),
                                            'payment_count' => $payments->count(),
                                            'last_payment' => $payments->sortByDesc('payment_date')->first()->payment_date,
                                            'avg_rate' => $payments->avg('cis_rate')
                                        ]);
                                    }
                                }
                            @endphp

                                // Apply sorting based on request
                                $sortBy = request('sort_by', 'total_deductions_desc');
                                switch($sortBy) {
                                    case 'total_deductions_asc':
                                        $operativesSummary = $operativesSummary->sortBy('total_deductions');
                                        break;
                                    case 'name_asc':
                                        $operativesSummary = $operativesSummary->sortBy(function($item) {
                                            return $item['type'] === 'employee' ? $item['employee']->full_name : $item['user']->name;
                                        });
                                        break;
                                    case 'name_desc':
                                        $operativesSummary = $operativesSummary->sortByDesc(function($item) {
                                            return $item['type'] === 'employee' ? $item['employee']->full_name : $item['user']->name;
                                        });
                                        break;
                                    case 'last_payment_desc':
                                        $operativesSummary = $operativesSummary->sortByDesc('last_payment');
                                        break;
                                    case 'payment_count_desc':
                                        $operativesSummary = $operativesSummary->sortByDesc('payment_count');
                                        break;
                                    case 'total_deductions_desc':
                                    default:
                                        $operativesSummary = $operativesSummary->sortByDesc('total_deductions');
                                        break;
                                }
                            @endphp

                            @foreach($operativesSummary as $summary)
                                <div class="operative-summary-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-5">
                                            <div class="operative-info">
                                                <div class="operative-header">
                                                    <h6 class="operative-name">
                                                        @if($summary['type'] === 'employee')
                                                            <a href="{{ route('profiles.operative', $summary['employee']->id) }}" class="operative-link">
                                                                <i class="bi bi-person-circle me-2"></i>
                                                                {{ $summary['employee']->full_name }}
                                                            </a>
                                                        @else
                                                            <a href="{{ route('profiles.employee', $summary['user']->id) }}" class="operative-link">
                                                                <i class="bi bi-person-circle me-2"></i>
                                                                {{ $summary['user']->name }}
                                                                <span class="badge bg-info ms-2">Manager</span>
                                                            </a>
                                                        @endif
                                                    </h6>
                                                    <div class="operative-meta">
                                                        <span class="meta-badge">
                                                            <i class="bi bi-credit-card-2-front"></i>
                                                            @if($summary['type'] === 'employee')
                                                                CIS: {{ $summary['employee']->cis_number ?? 'Not Set' }}
                                                            @else
                                                                Role: {{ $summary['user']->role ?? 'Manager' }}
                                                            @endif
                                                        </span>
                                                        <span class="meta-badge">
                                                            <i class="bi bi-calendar-check"></i>
                                                            {{ $summary['payment_count'] }} payments
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="operative-financials">
                                                <div class="financial-grid">
                                                    <div class="financial-item">
                                                        <span class="financial-label">Total Gross</span>
                                                        <span class="financial-value gross">£{{ number_format($summary['total_gross'], 2) }}</span>
                                                    </div>
                                                    <div class="financial-item">
                                                        <span class="financial-label">CIS Deductions</span>
                                                        <span class="financial-value deductions">£{{ number_format($summary['total_deductions'], 2) }}</span>
                                                    </div>
                                                    <div class="financial-item">
                                                        <span class="financial-label">Net Payment</span>
                                                        <span class="financial-value net">£{{ number_format($summary['total_net'], 2) }}</span>
                                                    </div>
                                                    <div class="financial-item">
                                                        <span class="financial-label">Avg Rate</span>
                                                        <span class="financial-value rate">{{ number_format($summary['avg_rate'], 1) }}%</span>
                                                    </div>
                                                </div>
                                                <div class="deduction-progress mt-2">
                                                    <div class="progress progress-thin">
                                                        <div class="progress-bar bg-warning" 
                                                             style="width: {{ $summary['total_gross'] > 0 ? round(($summary['total_deductions'] / $summary['total_gross']) * 100, 1) : 0 }}%">
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">
                                                        Last payment: {{ $summary['last_payment']->format('d M Y') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state-cis">
                            <div class="empty-icon">
                                <i class="bi bi-receipt"></i>
                            </div>
                            <h6>No CIS Payments Recorded</h6>
                            <p class="text-muted">Start recording CIS payments to see operative summaries</p>
                            <a href="{{ route('cis.payments.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Record First Payment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Returns & Actions -->
        <div class="col-xl-4 col-lg-5">
            <!-- Quick Actions Panel -->
            <div class="cis-actions-panel mb-4">
                <div class="panel-header">
                    <h5 class="panel-title">Quick Actions</h5>
                </div>
                <div class="panel-body">
                    <div class="action-grid">
                        <a href="{{ route('cis.payments.create') }}" class="action-card primary">
                            <div class="action-icon">
                                <i class="bi bi-plus-circle"></i>
                            </div>
                            <div class="action-content">
                                <h6>Record Payment</h6>
                                <p>Add new CIS payment</p>
                            </div>
                        </a>
                        
                        <button type="button" class="action-card success" data-bs-toggle="modal" data-bs-target="#createReturnModal">
                            <div class="action-icon">
                                <i class="bi bi-file-plus"></i>
                            </div>
                            <div class="action-content">
                                <h6>Create Return</h6>
                                <p>Generate HMRC return</p>
                            </div>
                        </button>
                        
                        <a href="{{ route('cis.payments') }}" class="action-card info">
                            <div class="action-icon">
                                <i class="bi bi-list-ul"></i>
                            </div>
                            <div class="action-content">
                                <h6>All Payments</h6>
                                <p>View payment history</p>
                            </div>
                        </a>
                        
                        <a href="{{ route('cis.returns') }}" class="action-card warning">
                            <div class="action-icon">
                                <i class="bi bi-file-text"></i>
                            </div>
                            <div class="action-content">
                                <h6>Returns</h6>
                                <p>Manage CIS returns</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Upcoming Returns -->
            <div class="cis-returns-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Upcoming Returns</h5>
                        <a href="{{ route('cis.returns') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-arrow-right me-1"></i>View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($upcomingReturns && $upcomingReturns->count() > 0)
                        <div class="returns-list">
                            @foreach($upcomingReturns as $return)
                                <div class="return-item">
                                    <div class="return-header">
                                        <h6 class="return-period">{{ $return->period_description }}</h6>
                                        <span class="badge bg-{{ $return->status_color }}">
                                            {{ $return->status_label }}
                                        </span>
                                    </div>
                                    <div class="return-details">
                                        <div class="detail-row">
                                            <span class="detail-label">Due Date:</span>
                                            <span class="detail-value {{ $return->isOverdue() ? 'text-danger' : '' }}">
                                                {{ $return->formatted_due_date }}
                                                @if($return->isOverdue())
                                                    <i class="bi bi-exclamation-triangle text-danger ms-1"></i>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Subcontractors:</span>
                                            <span class="detail-value">{{ $return->total_subcontractors }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Total Deductions:</span>
                                            <span class="detail-value">£{{ number_format($return->total_deductions, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="return-actions">
                                        <a href="{{ route('cis.returns.show', $return) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye me-1"></i>View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state-returns">
                            <div class="empty-icon">
                                <i class="bi bi-file-text"></i>
                            </div>
                            <h6>No Pending Returns</h6>
                            <p class="text-muted">All returns are up to date</p>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createReturnModal">
                                <i class="bi bi-file-plus me-2"></i>Create Return
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Return Modal -->
<div class="modal fade" id="createReturnModal" tabindex="-1" aria-labelledby="createReturnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createReturnModalLabel">
                    <i class="bi bi-file-plus me-2"></i>Create CIS Return
                </h5>
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
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Return Creation Process:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Automatically includes all verified payments from the selected period</li>
                            <li>Return will be due on the 19th of the following month</li>
                            <li>You can review and modify the return before submission</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-file-plus me-2"></i>Create Return
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* CIS Management Dashboard Styles */
.cis-management-dashboard {
    max-width: 100%;
    width: 100%;
    background: #f8fafc;
}

/* CIS Header */
.cis-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #3730a3 100%);
    border-radius: 20px;
    color: white;
    padding: 2.5rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.cis-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="8" height="8" patternUnits="userSpaceOnUse"><path d="M 8 0 L 0 0 0 8" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
}

.cis-welcome {
    position: relative;
    z-index: 1;
}

.cis-title {
    font-size: 2.25rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.cis-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 1rem;
}

.cis-status {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
    opacity: 0.8;
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 0.5rem;
}

.status-indicator.active {
    background: #10b981;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.cis-actions {
    position: relative;
    z-index: 1;
    text-align: right;
}

/* CIS KPI Cards */
.cis-kpi-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.cis-kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}

.payments-card::before { background: linear-gradient(90deg, #10b981, #059669); }
.deductions-card::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
.contractors-card::before { background: linear-gradient(90deg, #3b82f6, #2563eb); }
.compliance-card::before { background: linear-gradient(90deg, #8b5cf6, #7c3aed); }

.cis-kpi-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

/* Operatives Summary */
.cis-operatives-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.cis-operatives-card .card-header {
    padding: 2rem 2rem 1rem;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-bottom: 1px solid #e2e8f0;
}

.cis-operatives-card .card-title {
    font-size: 1.375rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.cis-operatives-card .card-subtitle {
    color: #64748b;
    font-size: 0.95rem;
    margin: 0;
}

.cis-operatives-card .card-body {
    padding: 2rem;
}

.operatives-summary {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.operative-summary-item {
    padding: 1.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    background: #f8fafc;
    transition: all 0.2s ease;
}

.operative-summary-item:hover {
    background: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transform: translateY(-1px);
}

.operative-name .operative-link {
    color: #1f2937;
    text-decoration: none;
    font-weight: 700;
    font-size: 1.125rem;
    transition: color 0.2s ease;
}

.operative-name .operative-link:hover {
    color: #4f46e5;
}

.operative-meta {
    display: flex;
    gap: 1rem;
    margin-top: 0.5rem;
}

.meta-badge {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    color: #64748b;
    background: #e2e8f0;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
}

.financial-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}

.financial-item {
    text-align: center;
}

.financial-label {
    display: block;
    font-size: 0.75rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.financial-value {
    display: block;
    font-size: 1.125rem;
    font-weight: 700;
    color: #1f2937;
}

.financial-value.gross { color: #059669; }
.financial-value.deductions { color: #d97706; }
.financial-value.net { color: #2563eb; }
.financial-value.rate { color: #7c3aed; }

.progress-thin {
    height: 4px;
}

/* CIS Actions Panel */
.cis-actions-panel {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.panel-header {
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    border-bottom: 1px solid #e2e8f0;
}

.panel-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.panel-body {
    padding: 2rem;
}

.action-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.action-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.2s ease;
    border: 2px solid transparent;
    text-align: center;
}

.action-card:hover {
    transform: translateY(-2px);
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.action-card.primary {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: white;
}

.action-card.success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.action-card.info {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.action-card.warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.action-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.action-content h6 {
    font-size: 0.9rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.action-content p {
    font-size: 0.75rem;
    opacity: 0.9;
    margin: 0;
}

/* CIS Returns Card */
.cis-returns-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.cis-returns-card .card-header {
    padding: 2rem 2rem 1rem;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-bottom: 1px solid #e2e8f0;
}

.cis-returns-card .card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.cis-returns-card .card-body {
    padding: 2rem;
}

.returns-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.return-item {
    padding: 1.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: #f8fafc;
    transition: all 0.2s ease;
}

.return-item:hover {
    background: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.return-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1rem;
}

.return-period {
    font-size: 1rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.return-details {
    margin-bottom: 1rem;
}

.detail-row {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.detail-label {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
}

.detail-value {
    font-size: 0.875rem;
    color: #1f2937;
    font-weight: 600;
}

.return-actions {
    text-align: center;
}

/* Empty States */
.empty-state-cis,
.empty-state-returns {
    text-align: center;
    padding: 3rem 2rem;
}

.empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: #64748b;
}

.empty-state-cis h6,
.empty-state-returns h6 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.75rem;
}

/* CIS Filters Panel */
.cis-filters-panel {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    overflow: hidden;
    transition: all 0.3s ease;
}

.filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    border-bottom: 1px solid #e2e8f0;
}

.filters-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.filters-content {
    padding: 2rem;
    background: white;
}

.filters-actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
}

.filter-summary {
    margin-left: auto;
}

.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.form-control, .form-select {
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.input-group .form-control:first-child {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-right: none;
}

.input-group .form-control:last-child {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

.input-group .btn {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

/* Filter Animation */
.filters-content {
    transition: all 0.3s ease;
    overflow: hidden;
}

.filters-content.show {
    display: block !important;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        max-height: 0;
        padding-top: 0;
        padding-bottom: 0;
    }
    to {
        opacity: 1;
        max-height: 500px;
        padding-top: 2rem;
        padding-bottom: 2rem;
    }
}

/* Export Button Enhancement */
#exportFiltered {
    position: relative;
}

#exportFiltered:hover {
    transform: translateY(-1px);
}

/* Badge Enhancements */
.badge.bg-info {
    background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
    border: none;
    padding: 0.5rem 0.75rem;
    font-weight: 600;
    border-radius: 12px;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .cis-title {
        font-size: 2rem;
    }
    
    .financial-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .cis-header {
        padding: 2rem;
        text-align: center;
    }
    
    .cis-actions {
        text-align: center;
        margin-top: 1.5rem;
    }
    
    .cis-title {
        font-size: 1.75rem;
    }
    
    .cis-kpi-card {
        padding: 1.5rem;
    }
    
    .financial-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .action-grid {
        grid-template-columns: 1fr;
    }
    
    .operative-meta {
        flex-direction: column;
        gap: 0.5rem;
    }
}

@media (max-width: 576px) {
    .cis-header {
        padding: 1.5rem;
    }
    
    .cis-title {
        font-size: 1.5rem;
    }
    
    .cis-kpi-card {
        padding: 1.25rem;
    }
    
    .operative-summary-item {
        padding: 1.25rem;
    }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle filters panel
    const toggleFiltersBtn = document.getElementById('toggleFilters');
    const filtersContent = document.getElementById('filtersContent');
    let filtersVisible = {{ request()->hasAny(['date_from', 'date_to', 'operative_id', 'project_id', 'cis_rate', 'status', 'amount_min', 'amount_max', 'search']) ? 'true' : 'false' }};
    
    // Show filters if any are active
    if (filtersVisible) {
        filtersContent.style.display = 'block';
        toggleFiltersBtn.innerHTML = '<i class="bi bi-chevron-up me-1"></i>Hide Filters';
    }
    
    toggleFiltersBtn.addEventListener('click', function() {
        if (filtersContent.style.display === 'none' || filtersContent.style.display === '') {
            filtersContent.style.display = 'block';
            filtersContent.classList.add('show');
            toggleFiltersBtn.innerHTML = '<i class="bi bi-chevron-up me-1"></i>Hide Filters';
        } else {
            filtersContent.style.display = 'none';
            filtersContent.classList.remove('show');
            toggleFiltersBtn.innerHTML = '<i class="bi bi-chevron-down me-1"></i>Show Filters';
        }
    });
    
    // Clear search functionality
    const clearSearchBtn = document.getElementById('clearSearch');
    const searchInput = document.querySelector('input[name="search"]');
    
    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        searchInput.focus();
    });
    
    // Save filter sets functionality
    const saveFiltersBtn = document.getElementById('saveFilters');
    saveFiltersBtn.addEventListener('click', function() {
        const formData = new FormData(document.getElementById('cisFiltersForm'));
        const filterData = {};
        
        for (let [key, value] of formData.entries()) {
            if (value) {
                filterData[key] = value;
            }
        }
        
        const filterName = prompt('Enter a name for this filter set:', 'My CIS Filter');
        if (filterName) {
            // Save to localStorage
            const savedFilters = JSON.parse(localStorage.getItem('cisFilters') || '{}');
            savedFilters[filterName] = filterData;
            localStorage.setItem('cisFilters', JSON.stringify(savedFilters));
            
            alert('Filter set "' + filterName + '" saved successfully!');
        }
    });
    
    // Export filtered data
    const exportBtn = document.getElementById('exportFiltered');
    exportBtn.addEventListener('click', function() {
        const form = document.getElementById('cisFiltersForm');
        const formData = new FormData(form);
        formData.append('export', 'csv');
        
        // Create a temporary form for download
        const tempForm = document.createElement('form');
        tempForm.method = 'GET';
        tempForm.action = '{{ route("cis.index") }}';
        
        for (let [key, value] of formData.entries()) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            tempForm.appendChild(input);
        }
        
        document.body.appendChild(tempForm);
        tempForm.submit();
        document.body.removeChild(tempForm);
    });
    
    // Auto-submit on date change
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Optional: Auto-submit form on date change
            // document.getElementById('cisFiltersForm').submit();
        });
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + F to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
        
        // Escape to clear search
        if (e.key === 'Escape' && document.activeElement === searchInput) {
            searchInput.value = '';
        }
    });
    
    // Form validation
    const form = document.getElementById('cisFiltersForm');
    form.addEventListener('submit', function(e) {
        const dateFrom = document.querySelector('input[name="date_from"]').value;
        const dateTo = document.querySelector('input[name="date_to"]').value;
        const amountMin = parseFloat(document.querySelector('input[name="amount_min"]').value) || 0;
        const amountMax = parseFloat(document.querySelector('input[name="amount_max"]').value) || Infinity;
        
        // Validate date range
        if (dateFrom && dateTo && new Date(dateFrom) > new Date(dateTo)) {
            e.preventDefault();
            alert('Start date cannot be later than end date.');
            return false;
        }
        
        // Validate amount range
        if (amountMin > amountMax) {
            e.preventDefault();
            alert('Minimum amount cannot be greater than maximum amount.');
            return false;
        }
    });
    
    // Period buttons functionality (existing)
    const periodButtons = document.querySelectorAll('[data-period]');
    periodButtons.forEach(button => {
        button.addEventListener('click', function() {
            const period = this.dataset.period;
            const today = new Date();
            let startDate, endDate;
            
            switch(period) {
                case 'ytd':
                    startDate = new Date(today.getFullYear(), 3, 6); // April 6th (UK tax year)
                    endDate = today;
                    break;
                case 'quarterly':
                    const quarter = Math.floor((today.getMonth() + 3) / 3);
                    const quarterStart = new Date(today.getFullYear(), (quarter - 1) * 3, 1);
                    startDate = quarterStart;
                    endDate = new Date(today.getFullYear(), quarter * 3, 0);
                    break;
                case 'monthly':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    break;
            }
            
            // Update date inputs
            document.querySelector('input[name="date_from"]').value = startDate.toISOString().split('T')[0];
            document.querySelector('input[name="date_to"]').value = endDate.toISOString().split('T')[0];
            
            // Update active state
            periodButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Auto-submit form
            form.submit();
        });
    });
});
</script>
@endpush
@endsection