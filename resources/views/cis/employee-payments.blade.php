@extends('layouts.app')

@section('title', 'Employee CIS Payments - ' . $user->name)

@section('content')
<div class="operative-payments-dashboard">
    <!-- Professional Header -->
    <div class="operative-header mb-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="operative-welcome">
                    <div class="breadcrumb-nav">
                        <a href="{{ route('cis.index') }}" class="breadcrumb-link">
                            <i class="bi bi-shield-check"></i> CIS Management
                        </a>
                        <i class="bi bi-chevron-right"></i>
                        <span class="breadcrumb-current">Operative Payments</span>
                    </div>
                    <h1 class="operative-title">
                        <div class="operative-avatar">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        {{ $user->name }}
                    </h1>
                    <p class="operative-subtitle">
                        Role: {{ ucfirst(str_replace('_', ' ', $user->role)) }} • 
                        Status: <span class="status-badge verified">{{ $stats['self_employed_count'] > 0 ? 'Self-Employed' : 'Employed' }}</span>
                    </p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="operative-actions">
                    <div class="btn-group" role="group">
                        <a href="{{ route('cis.payments.create') }}?user_id={{ $user->id }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>New Payment
                        </a>
                        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#cisStatementModal">
                            <i class="bi bi-file-earmark-pdf me-2"></i>CIS Statement
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="exportEmployeeData()">
                            <i class="bi bi-download me-2"></i>Export CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Period Filter -->
    <div class="period-filter-card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="filter-title">Payment Period</h6>
                    <div class="btn-group" role="group">
                        <a href="{{ route('cis.employee-payments', ['user' => $user->id, 'period' => 'ytd']) }}" 
                           class="btn btn-outline-primary {{ $period === 'ytd' ? 'active' : '' }}">
                            Year to Date
                        </a>
                        <a href="{{ route('cis.employee-payments', ['user' => $user->id, 'period' => 'quarterly']) }}" 
                           class="btn btn-outline-primary {{ $period === 'quarterly' ? 'active' : '' }}">
                            This Quarter
                        </a>
                        <a href="{{ route('cis.employee-payments', ['user' => $user->id, 'period' => 'monthly']) }}" 
                           class="btn btn-outline-primary {{ $period === 'monthly' ? 'active' : '' }}">
                            This Month
                        </a>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="period-summary">
                        <span class="summary-label">Showing:</span>
                        <span class="summary-value">
                            {{ $stats['payment_count'] }} payments
                        </span>
                        @if($stats['first_payment_date'] && $stats['last_payment_date'])
                            <br>
                            <small class="text-muted">
                                {{ $stats['first_payment_date']->format('M Y') }} - {{ $stats['last_payment_date']->format('M Y') }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Overview KPIs -->
    <div class="row g-4 mb-5">
        <!-- Total Gross Payments -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="financial-kpi-card gross-card">
                <div class="kpi-header">
                    <div class="kpi-icon">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div class="kpi-badge">
                        {{ $stats['payment_count'] }} payments
                    </div>
                </div>
                <div class="kpi-content">
                    <h2 class="kpi-value">£{{ number_format($stats['total_gross'] ?? 0, 2) }}</h2>
                    <p class="kpi-label">Total Gross Payments</p>
                    <div class="kpi-details">
                        <span class="detail-item">
                            <i class="bi bi-arrow-up"></i>
                            Avg: £{{ number_format($stats['average_payment'] ?? 0, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- CIS Deductions -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="financial-kpi-card deductions-card">
                <div class="kpi-header">
                    <div class="kpi-icon">
                        <i class="bi bi-percent"></i>
                    </div>
                    <div class="kpi-badge">
                        {{ number_format($stats['average_rate'] ?? 0, 1) }}% avg rate
                    </div>
                </div>
                <div class="kpi-content">
                    <h2 class="kpi-value">£{{ number_format($stats['total_deductions'] ?? 0, 2) }}</h2>
                    <p class="kpi-label">Total CIS Deductions</p>
                    <div class="kpi-progress">
                        <div class="progress">
                            <div class="progress-bar bg-warning" 
                                 style="width: {{ ($stats['total_gross'] ?? 0) > 0 ? round((($stats['total_deductions'] ?? 0) / ($stats['total_gross'] ?? 1)) * 100, 1) : 0 }}%">
                            </div>
                        </div>
                        <small class="text-muted">
                            {{ ($stats['total_gross'] ?? 0) > 0 ? round((($stats['total_deductions'] ?? 0) / ($stats['total_gross'] ?? 1)) * 100, 1) : 0 }}% of gross
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Net Payments -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="financial-kpi-card net-card">
                <div class="kpi-header">
                    <div class="kpi-icon">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="kpi-badge">
                        Net received
                    </div>
                </div>
                <div class="kpi-content">
                    <h2 class="kpi-value">£{{ number_format($stats['total_net'] ?? 0, 2) }}</h2>
                    <p class="kpi-label">Total Net Payments</p>
                    <div class="kpi-details">
                        <span class="detail-item">
                            <i class="bi bi-calendar-check"></i>
                            Last: {{ $stats['last_payment_date']?->format('d M Y') ?? 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Materials Cost -->
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="financial-kpi-card materials-card">
                <div class="kpi-header">
                    <div class="kpi-icon">
                        <i class="bi bi-tools"></i>
                    </div>
                    <div class="kpi-badge">
                        Materials
                    </div>
                </div>
                <div class="kpi-content">
                    <h2 class="kpi-value">£{{ number_format($stats['total_materials'] ?? 0, 2) }}</h2>
                    <p class="kpi-label">Materials Cost</p>
                    <div class="kpi-details">
                        <span class="detail-item">
                            <i class="bi bi-arrow-up"></i>
                            Highest: £{{ number_format($stats['highest_payment'] ?? 0, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">
        <!-- Left Column - Payment History & Charts -->
        <div class="col-xl-8 col-lg-7">
            <!-- Payment Trends Chart -->
            <div class="chart-card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Payment Trends</h5>
                            <p class="card-subtitle">Monthly breakdown of payments and deductions</p>
                        </div>
                        <div class="chart-controls">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary active" data-chart="combined">Combined</button>
                                <button type="button" class="btn btn-outline-secondary" data-chart="separate">Separate</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="paymentTrendsChart" height="300"></canvas>
                </div>
            </div>

            <!-- Payment History Table -->
            <div class="payments-history-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Payment History</h5>
                            <p class="card-subtitle">Detailed payment records</p>
                        </div>
                        <div class="table-actions">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleTableView()">
                                <i class="bi bi-list-ul me-1"></i>Toggle View
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover payment-history-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Project</th>
                                        <th>Period</th>
                                        <th>Gross Amount</th>
                                        <th>CIS Rate</th>
                                        <th>CIS Deduction</th>
                                        <th>Materials</th>
                                        <th>Net Payment</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr class="payment-row">
                                            <td>
                                                <div class="payment-date">
                                                    <strong>{{ $payment->payment_date->format('d M Y') }}</strong>
                                                    <small class="text-muted d-block">{{ $payment->payment_date->diffForHumans() }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($payment->project)
                                                    <a href="{{ route('projects.show', $payment->project) }}" class="project-link">
                                                        {{ $payment->project->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">No Project</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="period-info">
                                                    @if($payment->period_start && $payment->period_end)
                                                        {{ $payment->period_start->format('d/m') }} - {{ $payment->period_end->format('d/m/Y') }}
                                                    @else
                                                        {{ $payment->payment_date->format('M Y') }}
                                                    @endif
                                                </small>
                                            </td>
                                            <td>
                                                <span class="amount gross">£{{ number_format($payment->gross_amount ?? 0, 2) }}</span>
                                            </td>
                                            <td>
                                                <span class="cis-rate">{{ number_format($payment->cis_rate ?? 0, 1) }}%</span>
                                            </td>
                                            <td>
                                                <span class="amount deduction">£{{ number_format($payment->cis_deduction ?? 0, 2) }}</span>
                                            </td>
                                            <td>
                                                <span class="amount materials">£{{ number_format($payment->materials_cost ?? 0, 2) }}</span>
                                            </td>
                                            <td>
                                                <span class="amount net">£{{ number_format($payment->net_payment ?? 0, 2) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $payment->status_color }}">
                                                    {{ $payment->status_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('cis.payments.show', $payment) }}" 
                                                       class="btn btn-outline-primary" title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if($payment->cisReturn)
                                                        <a href="{{ route('cis.returns.show', $payment->cisReturn) }}" 
                                                           class="btn btn-outline-secondary" title="View Return">
                                                            <i class="bi bi-file-text"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="bi bi-receipt"></i>
                            </div>
                            <h6>No Payments Found</h6>
                            <p class="text-muted">No CIS payments found for the selected period</p>
                            <a href="{{ route('cis.payments.create') }}?user_id={{ $user->id }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Record First Payment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Analytics & Breakdowns -->
        <div class="col-xl-4 col-lg-5">
            <!-- Project Breakdown -->
            <div class="breakdown-card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Project Breakdown</h5>
                    <p class="card-subtitle">Payments by project</p>
                </div>
                <div class="card-body">
                    @if($projectBreakdown->count() > 0)
                        <div class="project-breakdown-list">
                            @foreach($projectBreakdown as $breakdown)
                                <div class="breakdown-item">
                                    <div class="breakdown-header">
                                        <h6 class="project-name">
                                            @if($breakdown['project'])
                                                <a href="{{ route('projects.show', $breakdown['project']) }}">
                                                    {{ $breakdown['project']->name }}
                                                </a>
                                            @else
                                                <span class="text-muted">No Project Assigned</span>
                                            @endif
                                        </h6>
                                        <span class="payment-count">{{ $breakdown['count'] }} payments</span>
                                    </div>
                                    <div class="breakdown-amounts">
                                        <div class="amount-row">
                                            <span class="amount-label">Gross:</span>
                                            <span class="amount-value">£{{ number_format($breakdown['gross'], 2) }}</span>
                                        </div>
                                        <div class="amount-row">
                                            <span class="amount-label">Deductions:</span>
                                            <span class="amount-value deduction">£{{ number_format($breakdown['deductions'], 2) }}</span>
                                        </div>
                                        <div class="amount-row">
                                            <span class="amount-label">Net:</span>
                                            <span class="amount-value net">£{{ number_format($breakdown['net'], 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="breakdown-progress">
                                        <div class="progress progress-thin">
                                            <div class="progress-bar bg-primary" 
                                                 style="width: {{ $stats['total_gross'] > 0 ? round(($breakdown['gross'] / $stats['total_gross']) * 100, 1) : 0 }}%">
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            {{ $stats['total_gross'] > 0 ? round(($breakdown['gross'] / $stats['total_gross']) * 100, 1) : 0 }}% of total
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-breakdown">
                            <i class="bi bi-folder text-muted"></i>
                            <p class="text-muted mb-0">No project data available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- CIS Rate Analysis -->
            <div class="breakdown-card">
                <div class="card-header">
                    <h5 class="card-title">CIS Rate Analysis</h5>
                    <p class="card-subtitle">Deduction rates applied</p>
                </div>
                <div class="card-body">
                    @if($rateAnalysis->count() > 0)
                        <div class="rate-analysis-chart">
                            <canvas id="rateAnalysisChart" height="200"></canvas>
                        </div>
                        <div class="rate-breakdown mt-3">
                            @foreach($rateAnalysis as $analysis)
                                <div class="rate-item">
                                    <div class="rate-header">
                                        <span class="rate-percentage">{{ number_format($analysis['rate'], 1) }}%</span>
                                        <span class="rate-count">{{ $analysis['count'] }} payments</span>
                                    </div>
                                    <div class="rate-amounts">
                                        <small class="text-muted">
                                            £{{ number_format($analysis['total_gross'], 2) }} gross • 
                                            £{{ number_format($analysis['total_deductions'], 2) }} deducted
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-breakdown">
                            <i class="bi bi-percent text-muted"></i>
                            <p class="text-muted mb-0">No rate data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Operative Payments Dashboard Styles */
.operative-payments-dashboard {
    max-width: 100%;
    width: 100%;
    background: #f8fafc;
}

/* Operative Header */
.operative-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 20px;
    color: white;
    padding: 2.5rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.operative-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
}

.operative-welcome {
    position: relative;
    z-index: 1;
}

.breadcrumb-nav {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    opacity: 0.8;
}

.breadcrumb-link {
    color: white;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.breadcrumb-link:hover {
    color: #e2e8f0;
}

.breadcrumb-current {
    opacity: 0.6;
}

.operative-title {
    font-size: 2.25rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.operative-avatar {
    font-size: 3rem;
    opacity: 0.8;
}

.operative-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0;
}

.status-badge {
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.operative-actions {
    position: relative;
    z-index: 1;
    text-align: right;
}

/* Period Filter Card */
.period-filter-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.filter-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.75rem;
}

.period-summary {
    text-align: right;
}

.summary-label {
    font-size: 0.875rem;
    color: #64748b;
}

.summary-value {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
}

/* Financial KPI Cards */
.financial-kpi-card {
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

.financial-kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}

.gross-card::before { background: linear-gradient(90deg, #059669, #10b981); }
.deductions-card::before { background: linear-gradient(90deg, #d97706, #f59e0b); }
.net-card::before { background: linear-gradient(90deg, #2563eb, #3b82f6); }
.materials-card::before { background: linear-gradient(90deg, #7c3aed, #8b5cf6); }

.financial-kpi-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.kpi-badge {
    background: rgba(0, 0, 0, 0.05);
    color: #64748b;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Chart Card */
.chart-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.chart-card .card-header {
    padding: 2rem 2rem 1rem;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-bottom: 1px solid #e2e8f0;
}

.chart-controls {
    display: flex;
    gap: 0.5rem;
}

/* Payments History Card */
.payments-history-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.payments-history-card .card-header {
    padding: 2rem 2rem 1rem;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-bottom: 1px solid #e2e8f0;
}

.payment-history-table {
    font-size: 0.9rem;
}

.payment-history-table th {
    background: #f8fafc;
    border: none;
    font-weight: 600;
    color: #475569;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    padding: 1rem 0.75rem;
}

.payment-row {
    transition: all 0.2s ease;
}

.payment-row:hover {
    background: #f8fafc;
    transform: translateY(-1px);
}

.payment-date strong {
    color: #1f2937;
    font-weight: 600;
}

.project-link {
    color: #4f46e5;
    text-decoration: none;
    font-weight: 600;
}

.project-link:hover {
    color: #7c3aed;
    text-decoration: underline;
}

.period-info {
    color: #64748b;
    background: #f1f5f9;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-family: monospace;
}

.amount {
    font-weight: 700;
    font-family: monospace;
}

.amount.gross { color: #059669; }
.amount.deduction { color: #d97706; }
.amount.net { color: #2563eb; }
.amount.materials { color: #7c3aed; }

.cis-rate {
    background: #fef3c7;
    color: #d97706;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.875rem;
}

/* Breakdown Cards */
.breakdown-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.breakdown-card .card-header {
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    border-bottom: 1px solid #e2e8f0;
}

.breakdown-card .card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.breakdown-card .card-subtitle {
    color: #64748b;
    font-size: 0.9rem;
    margin: 0;
}

.breakdown-card .card-body {
    padding: 2rem;
}

.project-breakdown-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.breakdown-item {
    padding: 1.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: #f8fafc;
    transition: all 0.2s ease;
}

.breakdown-item:hover {
    background: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.breakdown-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1rem;
}

.project-name {
    font-size: 1rem;
    font-weight: 700;
    margin: 0;
}

.project-name a {
    color: #1f2937;
    text-decoration: none;
}

.project-name a:hover {
    color: #4f46e5;
}

.payment-count {
    font-size: 0.75rem;
    color: #64748b;
    background: #e2e8f0;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
}

.breakdown-amounts {
    margin-bottom: 1rem;
}

.amount-row {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.amount-label {
    font-size: 0.875rem;
    color: #64748b;
}

.amount-value {
    font-size: 0.875rem;
    font-weight: 700;
    color: #1f2937;
}

.amount-value.deduction { color: #d97706; }
.amount-value.net { color: #2563eb; }

.breakdown-progress {
    margin-top: 1rem;
}

.progress-thin {
    height: 4px;
}

/* Rate Analysis */
.rate-analysis-chart {
    margin-bottom: 1rem;
}

.rate-breakdown {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.rate-item {
    padding: 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #f8fafc;
}

.rate-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.rate-percentage {
    font-size: 1.125rem;
    font-weight: 700;
    color: #d97706;
}

.rate-count {
    font-size: 0.75rem;
    color: #64748b;
}

/* Empty States */
.empty-state,
.empty-breakdown {
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

.empty-state h6 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.75rem;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .operative-title {
        font-size: 2rem;
    }
    
    .financial-kpi-card {
        padding: 1.5rem;
    }
}

@media (max-width: 768px) {
    .operative-header {
        padding: 2rem;
        text-align: center;
    }
    
    .operative-actions {
        text-align: center;
        margin-top: 1.5rem;
    }
    
    .operative-title {
        font-size: 1.75rem;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .operative-avatar {
        font-size: 2rem;
    }
    
    .financial-kpi-card {
        padding: 1.25rem;
    }
    
    .period-summary {
        text-align: center;
        margin-top: 1rem;
    }
    
    .table-responsive {
        font-size: 0.8rem;
    }
}

@media (max-width: 576px) {
    .operative-header {
        padding: 1.5rem;
    }
    
    .operative-title {
        font-size: 1.5rem;
    }
    
    .financial-kpi-card {
        padding: 1rem;
    }
    
    .breakdown-item {
        padding: 1rem;
    }
    
    /* CIS Statement Button Styling */
    .btn-outline-success {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        color: white;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .btn-outline-success:hover {
        background: linear-gradient(135deg, #059669, #047857);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        color: white;
    }

    /* CIS Statement Modal Styling */
    .period-card {
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .period-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: #0d6efd !important;
    }

    .period-option-card:hover .period-card {
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    }

    #customPeriodSection {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 2rem;
        margin-top: 1rem;
    }
}
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize modal reset when closed
    document.getElementById('cisStatementModal').addEventListener('hidden.bs.modal', function () {
        hideCustomPeriodSection();
    });
    
    // Payment Trends Chart
    const trendsCtx = document.getElementById('paymentTrendsChart');
    if (trendsCtx) {
        const monthlyData = @json($monthlyBreakdown);
        const labels = Object.keys(monthlyData).map(month => {
            const [year, monthNum] = month.split('-');
            return new Date(year, monthNum - 1).toLocaleDateString('en-GB', { month: 'short', year: '2-digit' });
        });
        
        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Gross Payments (£)',
                    data: Object.values(monthlyData).map(data => data.gross),
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5, 150, 105, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'CIS Deductions (£)',
                    data: Object.values(monthlyData).map(data => data.deductions),
                    borderColor: '#d97706',
                    backgroundColor: 'rgba(217, 119, 6, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Net Payments (£)',
                    data: Object.values(monthlyData).map(data => data.net),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#4f46e5',
                        borderWidth: 1,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '£' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Rate Analysis Chart
    const rateCtx = document.getElementById('rateAnalysisChart');
    if (rateCtx) {
        const rateData = @json($rateAnalysis->values());
        
        new Chart(rateCtx, {
            type: 'doughnut',
            data: {
                labels: rateData.map(data => data.rate + '% Rate'),
                datasets: [{
                    data: rateData.map(data => data.total_gross),
                    backgroundColor: [
                        '#059669',
                        '#d97706', 
                        '#2563eb',
                        '#7c3aed',
                        '#dc2626'
                    ],
                    borderWidth: 0,
                    cutout: '60%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const rate = rateData[context.dataIndex];
                                return `${rate.rate}% Rate: £${rate.total_gross.toLocaleString()} (${rate.count} payments)`;
                            }
                        }
                    }
                }
            }
        });
    }
});

// Export operative data
function exportEmployeeData() {
    const userId = {{ $user->id }};
    const period = '{{ $period }}';
    const year = {{ $year }};
    
    // Create CSV content
    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "Date,Project,Gross Amount,CIS Rate,CIS Deduction,Materials,Net Payment,Status\n";
    
    // Use pre-processed data to avoid Blade syntax issues
    const paymentsData = {!! json_encode($payments->map(function($payment) {
        return [
            'date' => $payment->payment_date->format('Y-m-d'),
            'project' => $payment->project ? $payment->project->name : 'No Project',
            'gross' => number_format($payment->gross_amount, 2),
            'rate' => number_format($payment->cis_rate, 1),
            'deduction' => number_format($payment->cis_deduction, 2),
            'materials' => number_format($payment->materials_cost, 2),
            'net' => number_format($payment->net_payment, 2),
            'status' => ucfirst($payment->status)
        ];
    })) !!};
    
    paymentsData.forEach(payment => {
        csvContent += payment.date + ',"' + payment.project + '",' + payment.gross + ',' + payment.rate + '%,' + payment.deduction + ',' + payment.materials + ',' + payment.net + ',"' + payment.status + '"\n';
    });

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "{{ str_replace(' ', '-', $user->name) }}-cis-payments-" + period + "-" + year + ".csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Toggle table view
function toggleTableView() {
    const table = document.querySelector('.payment-history-table');
    table.classList.toggle('compact-view');
}

// Show custom period section in modal
function showCustomPeriodSection() {
    document.getElementById('customPeriodSection').style.display = 'block';
    // Scroll to the custom section
    document.getElementById('customPeriodSection').scrollIntoView({ behavior: 'smooth' });
}

// Hide custom period section in modal
function hideCustomPeriodSection() {
    document.getElementById('customPeriodSection').style.display = 'none';
    // Clear the date inputs
    document.getElementById('customStartDate').value = '';
    document.getElementById('customEndDate').value = '';
}

// Generate CIS Statement
function generateCisStatement(period, customStart = null, customEnd = null) {
    const userId = {{ $user->id }};
    let url = `/cis/employee/${userId}/statement?period=${period}`;
    
    if (period === 'custom' && customStart && customEnd) {
        url += `&start_date=${customStart}&end_date=${customEnd}`;
    }
    
    // Close the modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('cisStatementModal'));
    if (modal) {
        modal.hide();
    }
    
    // Show loading notification
    showNotification('Generating CIS statement...', 'info');
    
    // Create form for PDF generation
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = url;
    form.target = '_blank';
    form.style.display = 'none';
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    // Show success notification after delay
    setTimeout(() => {
        showNotification('CIS statement generated successfully!', 'success');
    }, 1000);
}

// Show notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show`;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (document.body.contains(notification)) {
            notification.remove();
        }
    }, 5000);
}

// Handle custom period modal
function generateCustomStatement() {
    const startDate = document.getElementById('customStartDate').value;
    const endDate = document.getElementById('customEndDate').value;
    
    if (!startDate || !endDate) {
        alert('Please select both start and end dates.');
        return;
    }
    
    if (new Date(startDate) > new Date(endDate)) {
        alert('Start date cannot be later than end date.');
        return;
    }
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('customPeriodModal'));
    modal.hide();
    
    // Generate statement
    generateCisStatement('custom', startDate, endDate);
}
</script>
@endpush

<!-- CIS Statement Modal -->
<div class="modal fade" id="cisStatementModal" tabindex="-1" aria-labelledby="cisStatementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="cisStatementModalLabel">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Generate CIS Statement
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="employee-avatar mb-3">
                                <i class="bi bi-person-circle" style="font-size: 4rem; color: #6c757d;"></i>
                            </div>
                            <h6 class="mb-1">{{ $user->name }}</h6>
                            <small class="text-muted">Role: {{ ucfirst(str_replace('_', ' ', $user->role)) }}</small>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>CIS Statement Information:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Professional PDF format suitable for contractor records</li>
                                <li>HMRC compliant for tax return purposes</li>
                                <li>Includes gross payments, CIS deductions, and net payments</li>
                                <li>Company branded with official details</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <h6 class="mb-3">Select Statement Period:</h6>
                
                <!-- Quick Period Options -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="period-option-card" onclick="generateCisStatement('current_month')" style="cursor: pointer;">
                            <div class="card h-100 border-2 period-card">
                                <div class="card-body text-center">
                                    <i class="bi bi-calendar-month text-primary mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="card-title">Current Month</h6>
                                    <p class="card-text text-muted small">{{ now()->format('F Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="period-option-card" onclick="generateCisStatement('last_month')" style="cursor: pointer;">
                            <div class="card h-100 border-2 period-card">
                                <div class="card-body text-center">
                                    <i class="bi bi-calendar-minus text-warning mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="card-title">Last Month</h6>
                                    <p class="card-text text-muted small">{{ now()->subMonth()->format('F Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="period-option-card" onclick="generateCisStatement('ytd')" style="cursor: pointer;">
                            <div class="card h-100 border-2 period-card">
                                <div class="card-body text-center">
                                    <i class="bi bi-calendar-check text-success mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="card-title">Year to Date</h6>
                                    <p class="card-text text-muted small">{{ now()->month >= 4 ? now()->year : now()->year - 1 }}/{{ (now()->month >= 4 ? now()->year : now()->year - 1) + 1 }} Tax Year</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="period-option-card" onclick="showCustomPeriodSection()" style="cursor: pointer;">
                            <div class="card h-100 border-2 period-card">
                                <div class="card-body text-center">
                                    <i class="bi bi-calendar-range text-info mb-2" style="font-size: 2rem;"></i>
                                    <h6 class="card-title">Custom Period</h6>
                                    <p class="card-text text-muted small">Select specific dates</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Period Section (Initially Hidden) -->
                <div id="customPeriodSection" style="display: none;">
                    <hr class="my-4">
                    <h6 class="mb-3">Custom Date Range:</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="customStartDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="customStartDate" required>
                        </div>
                        <div class="col-md-6">
                            <label for="customEndDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="customEndDate" required>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-success" onclick="generateCustomStatement()">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Generate Custom Statement
                        </button>
                        <button type="button" class="btn btn-outline-secondary ms-2" onclick="hideCustomPeriodSection()">
                            <i class="bi bi-arrow-left me-2"></i>Back to Options
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
