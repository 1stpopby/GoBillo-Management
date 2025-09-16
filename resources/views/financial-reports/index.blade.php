@extends('layouts.app')

@section('title', 'Financial Reports')

@section('content')
<div class="financial-reports-dashboard">
    <!-- Professional Page Header -->
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="header-icon bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-4">
                        <i class="bi bi-graph-up fs-2"></i>
                    </div>
    <div>
                        <h1 class="page-title mb-2 fw-bold">Financial Reports</h1>
                        <p class="page-subtitle text-muted mb-0">
                            Comprehensive financial analytics and reporting dashboard for your construction business
                        </p>
    </div>
</div>
            </div>
            <div class="col-lg-4 text-end">
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="exportReport()">
                        <i class="bi bi-download me-2"></i>Export
                    </button>
                    <button class="btn btn-primary" onclick="refreshReports()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('financial-reports.index') }}" class="row g-3 align-items-end">
                <div class="col-lg-3">
                    <label for="start_date" class="form-label fw-medium">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="{{ $startDate }}">
                </div>
                <div class="col-lg-3">
                    <label for="end_date" class="form-label fw-medium">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="{{ $endDate }}">
                </div>
                <div class="col-lg-3">
                    <label for="quick_range" class="form-label fw-medium">Quick Range</label>
                    <select class="form-select" id="quick_range" onchange="setQuickRange()">
                        <option value="">Custom Range</option>
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="this_quarter">This Quarter</option>
                        <option value="this_year">This Year</option>
                        <option value="last_year">Last Year</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-2"></i>Generate Reports
                    </button>
            </div>
        </form>
        </div>
    </div>

    <!-- Professional Tabs Navigation -->
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom-0">
            <ul class="nav nav-pills nav-fill professional-tabs" id="reportTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="pill" 
                            data-bs-target="#overview" type="button" role="tab">
                        <i class="bi bi-graph-up-arrow me-2"></i>
                        <span class="tab-text">Overview</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="sites-tab" data-bs-toggle="pill" 
                            data-bs-target="#sites" type="button" role="tab">
                        <i class="bi bi-geo-alt me-2"></i>
                        <span class="tab-text">Sites</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="projects-tab" data-bs-toggle="pill" 
                            data-bs-target="#projects" type="button" role="tab">
                        <i class="bi bi-kanban me-2"></i>
                        <span class="tab-text">Projects</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="vat-tab" data-bs-toggle="pill" 
                            data-bs-target="#vat" type="button" role="tab">
                        <i class="bi bi-calculator me-2"></i>
                        <span class="tab-text">VAT Reports</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="cis-tab" data-bs-toggle="pill" 
                            data-bs-target="#cis" type="button" role="tab">
                        <i class="bi bi-receipt me-2"></i>
                        <span class="tab-text">CIS Reports</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="expenses-tab" data-bs-toggle="pill" 
                            data-bs-target="#expenses" type="button" role="tab">
                        <i class="bi bi-credit-card me-2"></i>
                        <span class="tab-text">Expenses</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profit-tab" data-bs-toggle="pill" 
                            data-bs-target="#profit" type="button" role="tab">
                        <i class="bi bi-trophy me-2"></i>
                        <span class="tab-text">Profitability</span>
                    </button>
                </li>
            </ul>
</div>

            <div class="card-body">
            <div class="tab-content" id="reportTabsContent">
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <div class="row g-4 mb-4">
                        <!-- Revenue Card -->
                        <div class="col-xl-3 col-lg-6">
                            <div class="metric-card bg-gradient-success">
                                <div class="metric-card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h3 class="metric-number text-white mb-1">£{{ number_format($reportData['overview']['total_revenue'], 0) }}</h3>
                                            <p class="metric-label text-white-75 mb-0">Total Revenue</p>
                        </div>
                                        <div class="metric-icon text-white-50">
                                            <i class="bi bi-currency-pound fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
                        <!-- Expenses Card -->
                        <div class="col-xl-3 col-lg-6">
                            <div class="metric-card bg-gradient-danger">
                                <div class="metric-card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h3 class="metric-number text-white mb-1">£{{ number_format($reportData['overview']['total_expenses'], 0) }}</h3>
                                            <p class="metric-label text-white-75 mb-0">Total Expenses</p>
                        </div>
                                        <div class="metric-icon text-white-50">
                                            <i class="bi bi-credit-card fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
                        <!-- Gross Profit Card -->
                        <div class="col-xl-3 col-lg-6">
                            <div class="metric-card bg-gradient-primary">
                                <div class="metric-card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h3 class="metric-number text-white mb-1">£{{ number_format($reportData['overview']['gross_profit'], 0) }}</h3>
                                            <p class="metric-label text-white-75 mb-0">Gross Profit</p>
                                        </div>
                                        <div class="metric-icon text-white-50">
                                            <i class="bi bi-graph-up fs-1"></i>
                                        </div>
                                    </div>
                                    <div class="metric-footer mt-3">
                                        <span class="badge bg-white bg-opacity-25 text-white">
                                            {{ $reportData['overview']['profit_margin'] }}% Margin
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Projects Card -->
                        <div class="col-xl-3 col-lg-6">
                            <div class="metric-card bg-gradient-info">
                                <div class="metric-card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h3 class="metric-number text-white mb-1">{{ $reportData['overview']['active_projects'] }}</h3>
                                            <p class="metric-label text-white-75 mb-0">Active Projects</p>
                                        </div>
                                        <div class="metric-icon text-white-50">
                                            <i class="bi bi-kanban fs-1"></i>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
                    <!-- Charts Section -->
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Revenue vs Expenses Trend</h5>
                                </div>
            <div class="card-body">
                                    <canvas id="revenueExpensesChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Profit Distribution</h5>
                    </div>
                                <div class="card-body">
                                    <canvas id="profitChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

                <!-- Sites Tab -->
                <div class="tab-pane fade" id="sites" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-geo-alt me-2"></i>Sites Financial Summary
                            </h5>
            </div>
                        <div class="card-body p-0">
                    <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0 ps-4">Site Name</th>
                                            <th class="border-0">Location</th>
                                            <th class="border-0">Projects</th>
                                            <th class="border-0">Active</th>
                                            <th class="border-0 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                        @foreach($reportData['sites'] as $siteData)
                                            <tr>
                                                <td class="ps-4">
                                                    <div>
                                                        <h6 class="mb-1">{{ $siteData['site']->name }}</h6>
                                                        <small class="text-muted">{{ $siteData['site']->site_id }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $siteData['site']->city }}, {{ $siteData['site']->postcode }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                                        {{ $siteData['project_count'] }} Projects
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success bg-opacity-10 text-success">
                                                        {{ $siteData['active_projects'] }} Active
                                                    </span>
                                        </td>
                                        <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> View Details
                                                    </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
    </div>

                <!-- Projects Tab -->
                <div class="tab-pane fade" id="projects" role="tabpanel">
                    <div class="card">
            <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-kanban me-2"></i>Projects Financial Summary
                            </h5>
            </div>
                        <div class="card-body p-0">
                    <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0 ps-4">Project Name</th>
                                            <th class="border-0">Site</th>
                                            <th class="border-0">Status</th>
                                            <th class="border-0">Budget</th>
                                            <th class="border-0">Progress</th>
                                            <th class="border-0 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                        @foreach($reportData['projects'] as $projectData)
                                            <tr>
                                                <td class="ps-4">
                                                    <div>
                                                        <h6 class="mb-1">{{ $projectData['project']->name }}</h6>
                                                        <small class="text-muted">{{ $projectData['project']->client->company_name ?? 'N/A' }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $projectData['project']->site->name ?? 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge 
                                                        @if($projectData['status'] === 'completed') bg-success
                                                        @elseif($projectData['status'] === 'in_progress') bg-primary
                                                        @elseif($projectData['status'] === 'planning') bg-warning
                                                        @else bg-secondary
                                                        @endif bg-opacity-10 text-{{ $projectData['status'] === 'completed' ? 'success' : ($projectData['status'] === 'in_progress' ? 'primary' : ($projectData['status'] === 'planning' ? 'warning' : 'secondary')) }}">
                                                        {{ ucfirst(str_replace('_', ' ', $projectData['status'])) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="fw-medium">£{{ number_format($projectData['budget'], 0) }}</span>
                                                </td>
                                                <td>
                                                    <div class="progress" style="width: 80px; height: 6px;">
                                                        <div class="progress-bar bg-primary" style="width: {{ $projectData['progress'] }}%"></div>
                                                    </div>
                                                    <small class="text-muted">{{ $projectData['progress'] }}%</small>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> View Details
                                                    </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                        </div>
                    </div>
                </div>

                <!-- VAT Reports Tab -->
                <div class="tab-pane fade" id="vat" role="tabpanel">
                    <div class="row g-4 mb-4">
                        <!-- VAT Summary Cards -->
                        <div class="col-xl-4">
                            <div class="metric-card bg-gradient-primary">
                                <div class="metric-card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h3 class="metric-number text-white mb-1">£{{ number_format($reportData['vat']['summary']->total_net ?? 0, 0) }}</h3>
                                            <p class="metric-label text-white-75 mb-0">Net Amount</p>
                                        </div>
                                        <div class="metric-icon text-white-50">
                                            <i class="bi bi-receipt fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="metric-card bg-gradient-success">
                                <div class="metric-card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h3 class="metric-number text-white mb-1">£{{ number_format($reportData['vat']['summary']->total_vat ?? 0, 0) }}</h3>
                                            <p class="metric-label text-white-75 mb-0">Total VAT</p>
                                        </div>
                                        <div class="metric-icon text-white-50">
                                            <i class="bi bi-calculator fs-1"></i>
                                        </div>
                                    </div>
                                    <div class="metric-footer mt-3">
                                        <span class="badge bg-white bg-opacity-25 text-white">
                                            {{ number_format($reportData['vat']['summary']->average_vat_rate ?? 0, 1) }}% Avg Rate
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="metric-card bg-gradient-info">
                                <div class="metric-card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h3 class="metric-number text-white mb-1">{{ $reportData['vat']['summary']->expense_count ?? 0 }}</h3>
                                            <p class="metric-label text-white-75 mb-0">VAT Transactions</p>
                                        </div>
                                        <div class="metric-icon text-white-50">
                                            <i class="bi bi-file-text fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <!-- Monthly VAT Breakdown -->
                        <div class="col-lg-8">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-bar-chart me-2"></i>Monthly VAT Breakdown
                                    </h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="border-0 ps-4">Month</th>
                                                    <th class="border-0">Net Amount</th>
                                                    <th class="border-0">VAT Amount</th>
                                                    <th class="border-0">Transactions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($reportData['vat']['monthly_breakdown'] as $month)
                                                    <tr>
                                                        <td class="ps-4">
                                                            <strong>{{ \Carbon\Carbon::createFromFormat('Y-m', $month->month)->format('M Y') }}</strong>
                                                        </td>
                                                        <td>
                                                            <span class="fw-medium">£{{ number_format($month->net_amount, 2) }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-success bg-opacity-10 text-success">
                                                                £{{ number_format($month->vat_amount, 2) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="text-muted">{{ $month->expense_count }}</span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center py-4 text-muted">
                                                            No VAT data available for this period
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
            </div>
        </div>

                        <!-- VAT by Category -->
                        <div class="col-lg-4">
                            <div class="card h-100">
            <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-pie-chart me-2"></i>VAT by Category
                                    </h5>
            </div>
            <div class="card-body">
                                    @forelse($reportData['vat']['by_category'] as $category)
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $category->category)) }}</h6>
                                                <small class="text-muted">{{ $category->expense_count }} transactions</small>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-medium">£{{ number_format($category->vat_amount, 0) }}</div>
                                                <small class="text-muted">VAT</small>
                                            </div>
                                        </div>
                                        <div class="progress mb-3" style="height: 4px;">
                                            <div class="progress-bar bg-primary" 
                                                 style="width: {{ $reportData['vat']['summary']->total_vat > 0 ? ($category->vat_amount / $reportData['vat']['summary']->total_vat) * 100 : 0 }}%"></div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-muted">
                                            <i class="bi bi-pie-chart display-6 d-block mb-2"></i>
                                            No VAT categories available
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CIS Reports Tab -->
                <div class="tab-pane fade" id="cis" role="tabpanel">
                    <!-- CIS Summary Cards -->
                    <div class="row g-4 mb-4">
                        <div class="col-lg-3 col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="metric-icon bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="bi bi-currency-pound text-primary fs-4"></i>
                                    </div>
                                    <h3 class="h4 mb-2">£{{ number_format($reportData['cis']['summary']->total_gross_pay ?? 0, 2) }}</h3>
                                    <p class="text-muted mb-0">Total Gross Pay</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="metric-icon bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="bi bi-receipt text-warning fs-4"></i>
                                    </div>
                                    <h3 class="h4 mb-2">£{{ number_format($reportData['cis']['summary']->total_cis_deductions ?? 0, 2) }}</h3>
                                    <p class="text-muted mb-0">CIS Deductions</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="metric-icon bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="bi bi-cash-stack text-success fs-4"></i>
                                    </div>
                                    <h3 class="h4 mb-2">£{{ number_format($reportData['cis']['summary']->total_net_pay ?? 0, 2) }}</h3>
                                    <p class="text-muted mb-0">Total Net Pay</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="metric-icon bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="bi bi-people text-info fs-4"></i>
                                    </div>
                                    <h3 class="h4 mb-2">{{ $reportData['cis']['summary']->unique_operatives ?? 0 }}</h3>
                                    <p class="text-muted mb-0">Active Operatives</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <!-- Monthly CIS Breakdown -->
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-bottom">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-calendar3 me-2"></i>Monthly CIS Breakdown
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($reportData['cis']['monthly_breakdown']->isNotEmpty())
                    <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Month</th>
                                                        <th class="text-end">Gross Pay</th>
                                                        <th class="text-end">CIS Deduction</th>
                                                        <th class="text-end">Net Pay</th>
                                                        <th class="text-center">Payments</th>
                                </tr>
                            </thead>
                            <tbody>
                                                    @foreach($reportData['cis']['monthly_breakdown'] as $month)
                                                        <tr>
                                                            <td><strong>{{ \Carbon\Carbon::parse($month->month . '-01')->format('M Y') }}</strong></td>
                                                            <td class="text-end">£{{ number_format($month->gross_pay, 2) }}</td>
                                                            <td class="text-end text-warning">£{{ number_format($month->cis_deduction, 2) }}</td>
                                                            <td class="text-end text-success">£{{ number_format($month->net_pay, 2) }}</td>
                                                            <td class="text-center"><span class="badge bg-primary">{{ $month->payment_count }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                            <p class="text-muted mt-2">No CIS payments found for this period</p>
                                        </div>
                @endif
            </div>
        </div>
    </div>
        
                        <!-- Top Operatives -->
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-bottom">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-trophy me-2"></i>Top Operatives
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($reportData['cis']['top_operatives']->isNotEmpty())
                                        @foreach($reportData['cis']['top_operatives'] as $operative)
                                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                                <div>
                                                    <h6 class="mb-1">{{ $operative->payee_name }}</h6>
                                                    <small class="text-muted">{{ $operative->payment_count }} payments</small>
                                                </div>
                                                <div class="text-end">
                                                    <div class="fw-bold text-warning">£{{ number_format($operative->total_cis_deduction, 2) }}</div>
                                                    <small class="text-muted">CIS deducted</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-4">
                                            <i class="bi bi-person-x text-muted" style="font-size: 2rem;"></i>
                                            <p class="text-muted mt-2">No operative data available</p>
                                        </div>
                                    @endif
                                </div>
</div>
            </div>
        </div>
    </div>
    
                <!-- Expenses Tab -->
                <div class="tab-pane fade" id="expenses" role="tabpanel">
                    <!-- Expenses Summary Cards -->
                    <div class="row g-4 mb-4">
                        <div class="col-lg-4 col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="metric-icon bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="bi bi-credit-card text-danger fs-4"></i>
                                    </div>
                                    <h3 class="h4 mb-2">£{{ number_format(($reportData['expenses']['project_expenses']->total_amount ?? 0) + ($reportData['expenses']['regular_expenses']->total_amount ?? 0), 2) }}</h3>
                                    <p class="text-muted mb-0">Total Expenses</p>
            </div>
        </div>
    </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="metric-icon bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="bi bi-receipt text-warning fs-4"></i>
                                    </div>
                                    <h3 class="h4 mb-2">£{{ number_format($reportData['expenses']['project_expenses']->total_vat_amount ?? 0, 2) }}</h3>
                                    <p class="text-muted mb-0">Total VAT</p>
            </div>
        </div>
    </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="metric-icon bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="bi bi-files text-info fs-4"></i>
                                    </div>
                                    <h3 class="h4 mb-2">{{ ($reportData['expenses']['project_expenses']->expense_count ?? 0) + ($reportData['expenses']['regular_expenses']->expense_count ?? 0) }}</h3>
                                    <p class="text-muted mb-0">Total Transactions</p>
            </div>
        </div>
    </div>
</div>

                    <div class="row g-4 mb-4">
                        <!-- Category Breakdown -->
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-bottom">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-pie-chart me-2"></i>Expenses by Category
                                    </h5>
            </div>
            <div class="card-body">
                                    @if($reportData['expenses']['category_breakdown']->isNotEmpty())
                                        @foreach($reportData['expenses']['category_breakdown'] as $category)
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="category-indicator me-3" style="width: 12px; height: 12px; background-color: 
                                                        @switch($category->category)
                                                            @case('materials') #dc3545 @break
                                                            @case('travel') #198754 @break
                                                            @case('equipment') #fd7e14 @break
                                                            @case('subcontractor') #6f42c1 @break
                                                            @case('labor') #0dcaf0 @break
                                                            @case('permits') #ffc107 @break
                                                            @case('utilities') #20c997 @break
                                                            @default #6c757d
                                                        @endswitch; border-radius: 50%;"></div>
                                                    <div>
                                                        <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $category->category)) }}</h6>
                                                        <small class="text-muted">{{ $category->expense_count }} transactions</small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="fw-bold">£{{ number_format($category->total_amount, 2) }}</div>
                                                    <small class="text-muted">+£{{ number_format($category->vat_amount, 2) }} VAT</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-4">
                                            <i class="bi bi-pie-chart text-muted" style="font-size: 3rem;"></i>
                                            <p class="text-muted mt-2">No expense categories found</p>
                                        </div>
                                    @endif
            </div>
        </div>
    </div>
    
                        <!-- Top Projects by Expenses -->
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-bottom">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-building me-2"></i>Top Projects by Expenses
                                    </h5>
            </div>
            <div class="card-body">
                                    @if($reportData['expenses']['top_projects']->isNotEmpty())
                                        @foreach($reportData['expenses']['top_projects'] as $project)
                                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                                <div>
                                                    <h6 class="mb-1">{{ $project->project_name }}</h6>
                                                    <small class="text-muted">{{ $project->expense_count }} expenses</small>
                                                </div>
                                                <div class="text-end">
                                                    <div class="fw-bold text-danger">£{{ number_format($project->total_expenses, 2) }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-4">
                                            <i class="bi bi-building-x text-muted" style="font-size: 2rem;"></i>
                                            <p class="text-muted mt-2">No project expenses found</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Expenses Chart -->
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-bottom">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-graph-up me-2"></i>Monthly Expense Trend
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($reportData['expenses']['monthly_expenses']->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Month</th>
                                                        <th class="text-end">Net Amount</th>
                                                        <th class="text-end">VAT Amount</th>
                                                        <th class="text-end">Total Amount</th>
                                                        <th class="text-center">Transactions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($reportData['expenses']['monthly_expenses'] as $month)
                                                        <tr>
                                                            <td><strong>{{ \Carbon\Carbon::parse($month->month . '-01')->format('M Y') }}</strong></td>
                                                            <td class="text-end">£{{ number_format($month->net_amount, 2) }}</td>
                                                            <td class="text-end text-warning">£{{ number_format($month->vat_amount, 2) }}</td>
                                                            <td class="text-end text-danger">£{{ number_format($month->total_amount, 2) }}</td>
                                                            <td class="text-center"><span class="badge bg-primary">{{ $month->expense_count }}</span></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="bi bi-graph-down text-muted" style="font-size: 3rem;"></i>
                                            <p class="text-muted mt-2">No monthly expense data available</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profitability Tab -->
                <div class="tab-pane fade" id="profit" role="tabpanel">
                    <!-- Profitability Summary Cards -->
                    <div class="row g-4 mb-4">
                        <div class="col-lg-3 col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="metric-icon bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="bi bi-arrow-up-circle text-success fs-4"></i>
                                    </div>
                                    <h3 class="h4 mb-2">£{{ number_format($reportData['profitability']['summary']['total_revenue'] ?? 0, 2) }}</h3>
                                    <p class="text-muted mb-0">Total Revenue</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="metric-icon bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="bi bi-arrow-down-circle text-danger fs-4"></i>
                                    </div>
                                    <h3 class="h4 mb-2">£{{ number_format($reportData['profitability']['summary']['total_expenses'] ?? 0, 2) }}</h3>
                                    <p class="text-muted mb-0">Total Expenses</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="metric-icon bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="bi bi-calculator text-primary fs-4"></i>
                                    </div>
                                    <h3 class="h4 mb-2">£{{ number_format($reportData['profitability']['summary']['gross_profit'] ?? 0, 2) }}</h3>
                                    <p class="text-muted mb-0">Gross Profit</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="metric-icon 
                                        @if(($reportData['profitability']['summary']['profit_margin'] ?? 0) >= 20) bg-success
                                        @elseif(($reportData['profitability']['summary']['profit_margin'] ?? 0) >= 10) bg-warning
                                        @else bg-danger
                                        @endif bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class="bi bi-percent 
                                            @if(($reportData['profitability']['summary']['profit_margin'] ?? 0) >= 20) text-success
                                            @elseif(($reportData['profitability']['summary']['profit_margin'] ?? 0) >= 10) text-warning
                                            @else text-danger
                                            @endif fs-4"></i>
                                    </div>
                                    <h3 class="h4 mb-2">{{ $reportData['profitability']['summary']['profit_margin'] ?? 0 }}%</h3>
                                    <p class="text-muted mb-0">Profit Margin</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <!-- Project Profitability -->
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-bottom">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-building me-2"></i>Project Profitability Analysis
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($reportData['profitability']['project_profitability']->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Project</th>
                                                        <th class="text-end">Revenue</th>
                                                        <th class="text-end">Expenses</th>
                                                        <th class="text-end">Profit</th>
                                                        <th class="text-center">Margin</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($reportData['profitability']['project_profitability'] as $projectData)
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $projectData['project']->name }}</strong>
                                                                <br><small class="text-muted">{{ $projectData['project']->client->name ?? 'No Client' }}</small>
                                                            </td>
                                                            <td class="text-end text-success">£{{ number_format($projectData['revenue'], 2) }}</td>
                                                            <td class="text-end text-danger">£{{ number_format($projectData['expenses'], 2) }}</td>
                                                            <td class="text-end 
                                                                @if($projectData['profit'] >= 0) text-success
                                                                @else text-danger
                                                                @endif">
                                                                £{{ number_format($projectData['profit'], 2) }}
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge 
                                                                    @if($projectData['margin'] >= 20) bg-success
                                                                    @elseif($projectData['margin'] >= 10) bg-warning
                                                                    @elseif($projectData['margin'] >= 0) bg-info
                                                                    @else bg-danger
                                                                    @endif">
                                                                    {{ $projectData['margin'] }}%
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="bi bi-building-x text-muted" style="font-size: 3rem;"></i>
                                            <p class="text-muted mt-2">No project profitability data available</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Profitability Metrics -->
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-white border-bottom">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-speedometer2 me-2"></i>Key Metrics
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="metric-item mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Revenue Growth</span>
                                            <span class="fw-bold text-success">+12.5%</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success" style="width: 75%;"></div>
                                        </div>
                                    </div>

                                    <div class="metric-item mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Cost Control</span>
                                            <span class="fw-bold text-primary">{{ $reportData['profitability']['summary']['profit_margin'] ?? 0 }}%</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-primary" style="width: {{ min(100, max(0, ($reportData['profitability']['summary']['profit_margin'] ?? 0) * 2)) }}%;"></div>
                                        </div>
                                    </div>

                                    <div class="metric-item mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">CIS Impact</span>
                                            <span class="fw-bold text-warning">£{{ number_format($reportData['profitability']['summary']['total_cis_deductions'] ?? 0, 2) }}</span>
                                        </div>
                                        <small class="text-muted">Total CIS deductions this period</small>
                                    </div>

                                    <div class="bg-light rounded p-3">
                                        <h6 class="mb-2">Profitability Status</h6>
                                        @php $margin = $reportData['profitability']['summary']['profit_margin'] ?? 0; @endphp
                                        @if($margin >= 20)
                                            <span class="badge bg-success">Excellent</span>
                                            <small class="text-muted d-block mt-1">Strong profit margins</small>
                                        @elseif($margin >= 10)
                                            <span class="badge bg-warning">Good</span>
                                            <small class="text-muted d-block mt-1">Healthy profit margins</small>
                                        @elseif($margin >= 0)
                                            <span class="badge bg-info">Fair</span>
                                            <small class="text-muted d-block mt-1">Room for improvement</small>
                                        @else
                                            <span class="badge bg-danger">Poor</span>
                                            <small class="text-muted d-block mt-1">Needs attention</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Profitability Trend -->
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-bottom">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-graph-up-arrow me-2"></i>Monthly Profitability Trend
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($reportData['profitability']['monthly_profitability']->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Month</th>
                                                        <th class="text-end">Revenue</th>
                                                        <th class="text-end">Expenses</th>
                                                        <th class="text-end">Profit</th>
                                                        <th class="text-center">Margin</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($reportData['profitability']['monthly_profitability'] as $month)
                                                        <tr>
                                                            <td><strong>{{ \Carbon\Carbon::parse($month['month'] . '-01')->format('M Y') }}</strong></td>
                                                            <td class="text-end text-success">£{{ number_format($month['revenue'], 2) }}</td>
                                                            <td class="text-end text-danger">£{{ number_format($month['expenses'], 2) }}</td>
                                                            <td class="text-end 
                                                                @if($month['profit'] >= 0) text-success
                                                                @else text-danger
                                                                @endif">
                                                                £{{ number_format($month['profit'], 2) }}
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge 
                                                                    @if($month['margin'] >= 20) bg-success
                                                                    @elseif($month['margin'] >= 10) bg-warning
                                                                    @elseif($month['margin'] >= 0) bg-info
                                                                    @else bg-danger
                                                                    @endif">
                                                                    {{ $month['margin'] }}%
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="bi bi-graph-down text-muted" style="font-size: 3rem;"></i>
                                            <p class="text-muted mt-2">No monthly profitability data available</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .financial-reports-dashboard {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 2rem 0;
    }

    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .dashboard-header .header-icon {
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.2) !important;
        backdrop-filter: blur(10px);
    }

    .dashboard-header .page-title {
        font-size: 2.5rem;
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .dashboard-header .page-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    /* Professional Tabs */
    .professional-tabs {
        border: none;
        background: #f8f9fa;
        border-radius: 12px;
        padding: 8px;
        margin: 0;
    }

    .professional-tabs .nav-link {
        border: none;
        border-radius: 8px;
        padding: 12px 20px;
        color: #6c757d;
        font-weight: 500;
        transition: all 0.3s ease;
        background: transparent;
        margin: 0 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 50px;
    }

    .professional-tabs .nav-link:hover {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
        transform: translateY(-1px);
    }

    .professional-tabs .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .professional-tabs .nav-link i {
        font-size: 1.1rem;
    }

    .tab-text {
        font-size: 0.9rem;
    }

    /* Metric Cards */
    .metric-card {
        border-radius: 15px;
        border: none;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
    }

    .bg-gradient-danger {
        background: linear-gradient(135deg, #ff6b6b 0%, #ffa8a8 100%);
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .metric-card-body {
        padding: 2rem;
    }

    .metric-number {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1;
    }

    .metric-label {
        font-size: 0.9rem;
        font-weight: 500;
        opacity: 0.8;
    }

    .metric-icon {
        font-size: 3rem;
        opacity: 0.3;
    }

    .metric-footer {
        border-top: 1px solid rgba(255,255,255,0.2);
        padding-top: 1rem;
    }

    /* Card Enhancements */
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        border-radius: 15px 15px 0 0 !important;
        font-weight: 600;
    }

    /* Table Enhancements */
    .table th {
        font-weight: 600;
        color: #495057;
        background: #f8f9fa !important;
        border-top: none;
    }

    .table tbody tr {
        transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    /* Form Controls */
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #e1e5e9;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    /* Buttons */
    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #667eea 100%);
        transform: translateY(-1px);
    }

    /* Progress bars */
    .progress {
        border-radius: 10px;
        background: #f1f3f4;
    }

    .progress-bar {
        border-radius: 10px;
    }

    /* Badge Enhancements */
    .badge {
        font-weight: 500;
        border-radius: 6px;
        padding: 0.4em 0.8em;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .dashboard-header .page-title {
            font-size: 2rem;
        }
        
        .metric-number {
            font-size: 2rem;
        }
        
        .metric-card-body {
            padding: 1.5rem;
        }

        .professional-tabs .tab-text {
            display: none;
        }

        .professional-tabs .nav-link {
            padding: 12px;
            min-width: 50px;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Quick date range selector
    function setQuickRange() {
        const range = document.getElementById('quick_range').value;
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        
        const today = new Date();
        
        switch(range) {
            case 'this_month':
                startDate.value = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                endDate.value = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
                break;
            case 'last_month':
                startDate.value = new Date(today.getFullYear(), today.getMonth() - 1, 1).toISOString().split('T')[0];
                endDate.value = new Date(today.getFullYear(), today.getMonth(), 0).toISOString().split('T')[0];
                break;
            case 'this_quarter':
                const quarter = Math.floor(today.getMonth() / 3);
                startDate.value = new Date(today.getFullYear(), quarter * 3, 1).toISOString().split('T')[0];
                endDate.value = new Date(today.getFullYear(), (quarter + 1) * 3, 0).toISOString().split('T')[0];
                break;
            case 'this_year':
                startDate.value = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
                endDate.value = new Date(today.getFullYear(), 11, 31).toISOString().split('T')[0];
                break;
            case 'last_year':
                startDate.value = new Date(today.getFullYear() - 1, 0, 1).toISOString().split('T')[0];
                endDate.value = new Date(today.getFullYear() - 1, 11, 31).toISOString().split('T')[0];
                break;
        }
    }

    // Export functionality
    function exportReport() {
        const activeTab = document.querySelector('.nav-link.active').id.replace('-tab', '');
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        const url = `{{ route('financial-reports.export') }}?report_type=${activeTab}&start_date=${startDate}&end_date=${endDate}&format=pdf`;
        window.open(url, '_blank');
    }

    // Refresh reports
    function refreshReports() {
        window.location.reload();
    }

    // Initialize charts when Overview tab is active
    document.addEventListener('DOMContentLoaded', function() {
        // Sample chart initialization
        if (document.getElementById('revenueExpensesChart')) {
            const ctx = document.getElementById('revenueExpensesChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Revenue',
                        data: [12000, 19000, 15000, 25000, 22000, 30000],
                        borderColor: '#56ab2f',
                        backgroundColor: 'rgba(86, 171, 47, 0.1)',
                        tension: 0.4
        }, {
            label: 'Expenses',
                        data: [8000, 12000, 10000, 18000, 15000, 20000],
                        borderColor: '#ff6b6b',
                        backgroundColor: 'rgba(255, 107, 107, 0.1)',
                        tension: 0.4
        }]
    },
    options: {
        responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        }

        if (document.getElementById('profitChart')) {
            const ctx2 = document.getElementById('profitChart').getContext('2d');
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ['Gross Profit', 'Expenses'],
                    datasets: [{
                        data: [{{ $reportData['overview']['gross_profit'] }}, {{ $reportData['overview']['total_expenses'] }}],
                        backgroundColor: ['#667eea', '#ff6b6b'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
    }
});
</script>
@endpush
@endsection 