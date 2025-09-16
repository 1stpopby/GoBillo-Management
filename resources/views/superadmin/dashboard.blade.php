@extends('layouts.superadmin')

@section('title', 'SuperAdmin Dashboard')
@section('page-title', 'Platform Dashboard')

@section('content')
<div class="superadmin-dashboard">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">
                <i class="bi bi-shield-check text-danger me-2"></i>SuperAdmin Dashboard
            </h1>
            <p class="text-muted mb-0">Platform Overview & Company Management</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('superadmin.companies.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>New Company
            </a>
            <a href="{{ route('superadmin.companies.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-buildings me-1"></i>Manage Companies
            </a>
        </div>
    </div>

    <!-- Platform Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card bg-gradient-primary">
                <div class="stat-icon">
                    <i class="bi bi-buildings"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $stats['total_companies'] }}</div>
                    <div class="stat-label">Total Companies</div>
                    <div class="stat-change text-success">
                        <i class="bi bi-arrow-up"></i>{{ $stats['active_companies'] }} Active
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card bg-gradient-success">
                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($stats['total_users']) }}</div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-change text-info">
                        <i class="bi bi-graph-up"></i>Across all companies
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card bg-gradient-info">
                <div class="stat-icon">
                    <i class="bi bi-kanban"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ number_format($stats['total_projects']) }}</div>
                    <div class="stat-label">Total Projects</div>
                    <div class="stat-change text-warning">
                        <i class="bi bi-clock"></i>{{ $stats['active_projects'] }} Active
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card bg-gradient-warning">
                <div class="stat-icon">
                    <i class="bi bi-currency-pound"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">£{{ number_format($stats['total_revenue'] / 1000, 0) }}K</div>
                    <div class="stat-label">Platform Revenue</div>
                    <div class="stat-change text-success">
                        <i class="bi bi-trending-up"></i>Monthly recurring
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Companies Overview -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-building me-2"></i>Companies Overview
                    </h5>
                    <a href="{{ route('superadmin.companies.index') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    <!-- Company Status Charts -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="status-chart">
                                <h6>By Status</h6>
                                <div class="chart-bars">
                                    <div class="chart-bar">
                                        <div class="bar bg-success" style="height: {{ $companiesByStatus['active'] ? ($companiesByStatus['active'] / max(array_values($companiesByStatus)) * 100) : 0 }}%"></div>
                                        <small>Active ({{ $companiesByStatus['active'] }})</small>
                                    </div>
                                    <div class="chart-bar">
                                        <div class="bar bg-warning" style="height: {{ $companiesByStatus['suspended'] ? ($companiesByStatus['suspended'] / max(array_values($companiesByStatus)) * 100) : 0 }}%"></div>
                                        <small>Suspended ({{ $companiesByStatus['suspended'] }})</small>
                                    </div>
                                    <div class="chart-bar">
                                        <div class="bar bg-secondary" style="height: {{ $companiesByStatus['inactive'] ? ($companiesByStatus['inactive'] / max(array_values($companiesByStatus)) * 100) : 0 }}%"></div>
                                        <small>Inactive ({{ $companiesByStatus['inactive'] }})</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="status-chart">
                                <h6>By Subscription Plan</h6>
                                <div class="plan-stats">
                                    <div class="plan-item">
                                        <span class="plan-label">Trial</span>
                                        <div class="plan-bar">
                                            <div class="progress">
                                                <div class="progress-bar bg-info" style="width: {{ $companiesByPlan['trial'] ? ($companiesByPlan['trial'] / array_sum($companiesByPlan) * 100) : 0 }}%"></div>
                                            </div>
                                        </div>
                                        <span class="plan-count">{{ $companiesByPlan['trial'] }}</span>
                                    </div>
                                    <div class="plan-item">
                                        <span class="plan-label">Starter</span>
                                        <div class="plan-bar">
                                            <div class="progress">
                                                <div class="progress-bar bg-primary" style="width: {{ $companiesByPlan['starter'] ? ($companiesByPlan['starter'] / array_sum($companiesByPlan) * 100) : 0 }}%"></div>
                                            </div>
                                        </div>
                                        <span class="plan-count">{{ $companiesByPlan['starter'] }}</span>
                                    </div>
                                    <div class="plan-item">
                                        <span class="plan-label">Professional</span>
                                        <div class="plan-bar">
                                            <div class="progress">
                                                <div class="progress-bar bg-success" style="width: {{ $companiesByPlan['professional'] ? ($companiesByPlan['professional'] / array_sum($companiesByPlan) * 100) : 0 }}%"></div>
                                            </div>
                                        </div>
                                        <span class="plan-count">{{ $companiesByPlan['professional'] }}</span>
                                    </div>
                                    <div class="plan-item">
                                        <span class="plan-label">Enterprise</span>
                                        <div class="plan-bar">
                                            <div class="progress">
                                                <div class="progress-bar bg-warning" style="width: {{ $companiesByPlan['enterprise'] ? ($companiesByPlan['enterprise'] / array_sum($companiesByPlan) * 100) : 0 }}%"></div>
                                            </div>
                                        </div>
                                        <span class="plan-count">{{ $companiesByPlan['enterprise'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Companies -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Company</th>
                                    <th>Users</th>
                                    <th>Projects</th>
                                    <th>Plan</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentCompanies as $company)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="company-avatar me-2">
                                                    {{ substr($company->name, 0, 2) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $company->name }}</div>
                                                    <small class="text-muted">{{ $company->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $company->users_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $company->projects_count }}</span>
                                        </td>
                                        <td>
                                            @if($company->activeSubscription && $company->activeSubscription->membershipPlan)
                                                <span class="badge bg-{{ $company->activeSubscription->status === 'trial' ? 'info' : ($company->activeSubscription->membershipPlan->slug === 'starter' ? 'primary' : ($company->activeSubscription->membershipPlan->slug === 'professional' ? 'success' : 'warning')) }}">
                                                    {{ $company->activeSubscription->membershipPlan->name }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">No Plan</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $company->status === 'active' ? 'success' : ($company->status === 'suspended' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($company->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('superadmin.companies.show', $company->id) }}" class="btn btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('superadmin.companies.edit', $company->id) }}" class="btn btn-outline-secondary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Platform Activity -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-activity me-2"></i>Recent Platform Activity
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentActivity->count() > 0)
                        <div class="activity-timeline">
                            @foreach($recentActivity as $activity)
                                <div class="activity-item">
                                    <div class="activity-icon bg-{{ $activity['color'] }}">
                                        <i class="bi {{ $activity['icon'] }}"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">{{ $activity['title'] }}</div>
                                        <div class="activity-description">{{ $activity['description'] }}</div>
                                        <div class="activity-meta">
                                            <span class="company-tag">{{ $activity['company'] }}</span>
                                            <span class="activity-time">{{ $activity['date']->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-activity text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No recent activity</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Top Companies -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-trophy me-2"></i>Top Companies
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($topCompanies as $index => $company)
                        <div class="top-company-item mb-3">
                            <div class="d-flex align-items-center">
                                <div class="rank-badge me-3">
                                    <span class="badge bg-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'primary') }} rounded-pill">
                                        {{ $index + 1 }}
                                    </span>
                                </div>
                                <div class="company-avatar me-3">
                                    {{ substr($company->name, 0, 2) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ Str::limit($company->name, 20) }}</div>
                                    <div class="small text-muted">
                                        {{ $company->users_count }} users • {{ $company->projects_count }} projects
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('superadmin.companies.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Create New Company
                        </a>
                        <a href="{{ route('superadmin.companies.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-buildings me-2"></i>Manage All Companies
                        </a>
                        <button class="btn btn-outline-success" onclick="refreshStats()">
                            <i class="bi bi-arrow-clockwise me-2"></i>Refresh Statistics
                        </button>
                        <button class="btn btn-outline-info" onclick="exportReport()">
                            <i class="bi bi-download me-2"></i>Export Platform Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.superadmin-dashboard {
    max-width: 100%;
}

.stat-card {
    background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-primary-dark, #0056b3) 100%);
    border-radius: 12px;
    padding: 1.5rem;
    color: white;
    position: relative;
    overflow: hidden;
    height: 100%;
}

.stat-card.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-card.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.stat-card.bg-gradient-info {
    background: linear-gradient(135deg, #2196F3 0%, #21CBF3 100%);
}

.stat-card.bg-gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.stat-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    opacity: 0.8;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 1rem;
    opacity: 0.9;
    margin-bottom: 0.5rem;
}

.stat-change {
    font-size: 0.875rem;
    font-weight: 600;
}

.company-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
}

.status-chart h6 {
    color: #6c757d;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.chart-bars {
    display: flex;
    align-items: end;
    gap: 1rem;
    height: 80px;
}

.chart-bar {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.chart-bar .bar {
    width: 100%;
    min-height: 10px;
    border-radius: 4px 4px 0 0;
}

.chart-bar small {
    font-size: 0.75rem;
    color: #6c757d;
    text-align: center;
}

.plan-stats {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.plan-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.plan-label {
    min-width: 80px;
    font-size: 0.875rem;
    font-weight: 500;
}

.plan-bar {
    flex: 1;
}

.plan-bar .progress {
    height: 8px;
}

.plan-count {
    min-width: 30px;
    text-align: right;
    font-weight: 600;
    color: #495057;
}

.activity-timeline {
    max-height: 400px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.activity-item:last-child {
    margin-bottom: 0;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.activity-description {
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.activity-meta {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.company-tag {
    background: #e9ecef;
    color: #495057;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.activity-time {
    font-size: 0.75rem;
    color: #adb5bd;
}

.top-company-item {
    padding-bottom: 1rem;
    border-bottom: 1px solid #f8f9fa;
}

.top-company-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.rank-badge .badge {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
}

@media (max-width: 768px) {
    .stat-card {
        margin-bottom: 1rem;
    }
    
    .chart-bars {
        height: 60px;
    }
    
    .activity-timeline {
        max-height: 300px;
    }
}
</style>

<script>
function refreshStats() {
    // Show loading state
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Refreshing...';
    
    // Simulate refresh (in real app, this would make an AJAX call)
    setTimeout(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        
        // Show success message
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show position-fixed';
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
        alert.innerHTML = `
            Statistics refreshed successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);
        
        setTimeout(() => alert.remove(), 3000);
    }, 2000);
}

function exportReport() {
    // Show loading state
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Generating...';
    
    // Simulate export (in real app, this would generate and download a report)
    setTimeout(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        
        // Show success message
        const alert = document.createElement('div');
        alert.className = 'alert alert-info alert-dismissible fade show position-fixed';
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
        alert.innerHTML = `
            Platform report generated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alert);
        
        setTimeout(() => alert.remove(), 3000);
    }, 3000);
}
</script>
@endsection