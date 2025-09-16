@extends('layouts.app')

@section('title', 'Employees Management')

@section('content')
<div class="employees-dashboard">
    <!-- Professional Page Header -->
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="header-icon bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-4">
                        <i class="bi bi-briefcase-fill fs-2"></i>
                    </div>
                    <div>
                        <h1 class="page-title mb-2 fw-bold">Employees Management</h1>
                        <p class="page-subtitle text-muted mb-0">
                            Comprehensive dashboard for managing company staff, project assignments, and performance tracking
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-end">
                @if(auth()->user()->canManageCompanyUsers())
                    <a href="{{ route('employees.create') }}" class="btn btn-primary btn-lg shadow-sm">
                        <i class="bi bi-plus-circle me-2"></i>Add New Employee
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Key Performance Metrics -->
    <div class="row g-4 mb-5">
        <!-- Total Employees -->
        <div class="col-xl-3 col-lg-6">
            <div class="metric-card bg-gradient-primary">
                <div class="metric-card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="metric-number text-white mb-1">{{ $metrics['total_employees'] }}</h3>
                            <p class="metric-label text-white-75 mb-0">Total Employees</p>
                        </div>
                        <div class="metric-icon text-white-50">
                            <i class="bi bi-people-fill fs-1"></i>
                        </div>
                    </div>
                    <div class="metric-footer mt-3">
                        <span class="badge bg-white bg-opacity-25 text-white">
                            {{ $metrics['active_employees'] }} Active
                        </span>
                        <span class="trend-indicator ms-2">
                            <i class="bi bi-arrow-{{ $metrics['trend_active_employees'] === 'up' ? 'up' : ($metrics['trend_active_employees'] === 'down' ? 'down' : 'right') }} me-1"></i>
                            {{ $metrics['activity_rate'] }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Projects -->
        <div class="col-xl-3 col-lg-6">
            <div class="metric-card bg-gradient-success">
                <div class="metric-card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="metric-number text-white mb-1">{{ $metrics['active_projects'] }}</h3>
                            <p class="metric-label text-white-75 mb-0">Active Projects</p>
                        </div>
                        <div class="metric-icon text-white-50">
                            <i class="bi bi-kanban fs-1"></i>
                        </div>
                    </div>
                    <div class="metric-footer mt-3">
                        <span class="badge bg-white bg-opacity-25 text-white">
                            {{ $metrics['total_projects'] }} Total
                        </span>
                        <span class="trend-indicator ms-2">
                            <i class="bi bi-arrow-{{ $metrics['trend_project_completion'] === 'up' ? 'up' : ($metrics['trend_project_completion'] === 'down' ? 'down' : 'right') }} me-1"></i>
                            {{ $metrics['project_success_rate'] }}% Success
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manager Utilization -->
        <div class="col-xl-3 col-lg-6">
            <div class="metric-card bg-gradient-warning">
                <div class="metric-card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="metric-number text-white mb-1">{{ $metrics['manager_utilization'] }}%</h3>
                            <p class="metric-label text-white-75 mb-0">Manager Utilization</p>
                        </div>
                        <div class="metric-icon text-white-50">
                            <i class="bi bi-graph-up-arrow fs-1"></i>
                        </div>
                    </div>
                    <div class="metric-footer mt-3">
                        <span class="badge bg-white bg-opacity-25 text-white">
                            {{ $metrics['managers_with_active_projects'] }} Active Managers
                        </span>
                        <span class="trend-indicator ms-2">
                            <i class="bi bi-arrow-{{ $metrics['trend_task_efficiency'] === 'up' ? 'up' : ($metrics['trend_task_efficiency'] === 'down' ? 'down' : 'right') }} me-1"></i>
                            Efficient
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Task Completion -->
        <div class="col-xl-3 col-lg-6">
            <div class="metric-card bg-gradient-info">
                <div class="metric-card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="metric-number text-white mb-1">{{ $metrics['completed_tasks_this_month'] }}</h3>
                            <p class="metric-label text-white-75 mb-0">Tasks This Month</p>
                        </div>
                        <div class="metric-icon text-white-50">
                            <i class="bi bi-check-circle fs-1"></i>
                        </div>
                    </div>
                    <div class="metric-footer mt-3">
                        <span class="badge bg-white bg-opacity-25 text-white">
                            {{ $metrics['overdue_tasks'] }} Overdue
                        </span>
                        <span class="trend-indicator ms-2">
                            <i class="bi bi-arrow-up me-1"></i>
                            {{ $metrics['task_completion_rate'] }}% Rate
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Role Distribution & Performance -->
    <div class="row g-4 mb-5">
        <!-- Role Breakdown -->
        <div class="col-lg-6">
            <div class="metric-card-secondary h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="metric-icon-sm bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bi bi-diagram-3"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Team Structure</h6>
                            <small class="text-muted">Role distribution</small>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-6">
                            <div class="role-stat p-3 bg-light rounded">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="role-indicator bg-primary"></div>
                                    <span class="fw-medium">Admins</span>
                                </div>
                                <h4 class="text-primary mb-2">{{ $metrics['company_admins'] }}</h4>
                                <small class="text-muted">Company Administrators</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="role-stat p-3 bg-light rounded">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="role-indicator bg-success"></div>
                                    <span class="fw-medium">PM</span>
                                </div>
                                <h4 class="text-success mb-2">{{ $metrics['project_managers'] }}</h4>
                                <small class="text-muted">Project Managers</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="role-stat p-3 bg-light rounded">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="role-indicator bg-warning"></div>
                                    <span class="fw-medium">SM</span>
                                </div>
                                <h4 class="text-warning mb-2">{{ $metrics['site_managers'] }}</h4>
                                <small class="text-muted">Site Managers</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="role-stat p-3 bg-light rounded">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="role-indicator bg-info"></div>
                                    <span class="fw-medium">Contractors</span>
                                </div>
                                <h4 class="text-info mb-2">{{ $metrics['contractors'] }}</h4>
                                <small class="text-muted">Contract Workers</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="col-lg-6">
            <div class="metric-card-secondary h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="metric-icon-sm bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-trophy"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Performance Overview</h6>
                            <small class="text-muted">Key performance indicators</small>
                        </div>
                    </div>
                    
                    <div class="performance-metrics">
                        <div class="metric-item mb-4 p-3 bg-light rounded">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-medium">Project Success Rate</span>
                                <span class="text-success fw-bold">{{ $metrics['project_success_rate'] }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $metrics['project_success_rate'] }}%"></div>
                            </div>
                        </div>

                        <div class="metric-item mb-4 p-3 bg-light rounded">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-medium">Task Completion Rate</span>
                                <span class="text-info fw-bold">{{ $metrics['task_completion_rate'] }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-info" role="progressbar" 
                                     style="width: {{ $metrics['task_completion_rate'] }}%"></div>
                            </div>
                        </div>

                        <div class="row text-center g-3">
                            <div class="col-4">
                                <div class="p-3 bg-primary bg-opacity-10 rounded">
                                    <h5 class="text-primary mb-2">{{ $metrics['employees_with_projects'] }}</h5>
                                    <small class="text-muted">With Projects</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-success bg-opacity-10 rounded">
                                    <h5 class="text-success mb-2">{{ $metrics['completed_projects_this_month'] }}</h5>
                                    <small class="text-muted">Completed</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-warning bg-opacity-10 rounded">
                                    <h5 class="text-warning mb-2">{{ $metrics['overdue_tasks'] }}</h5>
                                    <small class="text-muted">Overdue</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CIS Information (if applicable) -->
    @if($metrics['employees_with_cis'] > 0)
    <div class="row g-4 mb-5">
        <div class="col-lg-12">
            <div class="metric-card-secondary">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="metric-icon-sm bg-warning bg-opacity-10 text-warning me-3">
                            <i class="bi bi-calculator"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">CIS Management</h6>
                            <small class="text-muted">Construction Industry Scheme for contractors</small>
                        </div>
                    </div>
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h4 class="text-warning mb-1">{{ $metrics['employees_with_cis'] }}</h4>
                            <p class="text-muted mb-0">CIS Registered</p>
                        </div>
                        <div class="col-md-4">
                            <h4 class="text-success mb-1">£{{ number_format($metrics['cis_deductions_this_year'], 0) }}</h4>
                            <p class="text-muted mb-0">Deductions YTD</p>
                        </div>
                        <div class="col-md-4">
                            <span class="badge bg-warning bg-opacity-10 text-warning fs-6">
                                <i class="bi bi-shield-check me-2"></i>Compliant
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Simple Employee List -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">
                <i class="bi bi-briefcase me-2"></i>Employees List 
                <span class="badge bg-primary ms-2">{{ $employees->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($employees->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 ps-4">Employee</th>
                                <th class="border-0">Role</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Projects</th>
                                <th class="border-0 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center me-3">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">{{ $employee->name }}</h6>
                                                <small class="text-muted">{{ $employee->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                            {{ $roles[$employee->role] ?? $employee->role }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($employee->is_active)
                                            <span class="badge bg-success bg-opacity-10 text-success">Active</span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $employee->managedProjects->count() }} projects</small>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('profiles.employee', $employee) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-light">
                    {{ $employees->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-briefcase display-1 text-muted mb-4"></i>
                    <h4 class="text-muted">No Employees Found</h4>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .employees-dashboard {
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

    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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

    .trend-indicator {
        color: rgba(255,255,255,0.8);
        font-size: 0.85rem;
        font-weight: 500;
    }

    /* Secondary Metric Cards */
    .metric-card-secondary {
        background: white;
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s ease;
    }

    .metric-card-secondary:hover {
        transform: translateY(-2px);
    }

    .metric-icon-sm {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    /* Role Statistics */
    .role-stat h4 {
        font-size: 1.8rem;
        font-weight: 700;
    }

    .role-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
    }

    /* Performance Metrics */
    .performance-metrics .metric-item {
        padding-bottom: 1rem;
        border-bottom: 1px solid #f1f3f4;
    }

    .performance-metrics .metric-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    /* Role Badges */
    .role-badge {
        font-weight: 500;
        border-radius: 6px;
        padding: 0.4em 0.8em;
    }

    .role-company-admin {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }

    .role-project-manager {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
    }

    .role-site-manager {
        background: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }

    .role-contractor {
        background: rgba(23, 162, 184, 0.1);
        color: #17a2b8;
    }

    /* Table Enhancements */
    .table th {
        font-weight: 600;
        color: #495057;
        background: #f8f9fa !important;
        border-top: none;
    }

    .employee-row {
        transition: background-color 0.2s ease;
    }

    .employee-row:hover {
        background: #f8f9fa;
    }

    .employee-name-link {
        color: #495057;
        font-weight: 600;
        transition: color 0.2s ease;
    }

    .employee-name-link:hover {
        color: #667eea;
    }

    .avatar-placeholder {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        font-size: 1.2rem;
    }

    /* Card Enhancements */
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    .card-header {
        border-bottom: 1px solid #e9ecef;
        border-radius: 15px 15px 0 0 !important;
        font-weight: 600;
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

    .input-group-text {
        background: #f8f9fa;
        border: 1px solid #e1e5e9;
        color: #6c757d;
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

    /* Empty State */
    .empty-state i {
        font-size: 4rem;
    }

    /* Badge Enhancements */
    .badge {
        font-weight: 500;
        border-radius: 6px;
        padding: 0.4em 0.8em;
    }

    /* Progress Bar */
    .progress {
        border-radius: 10px;
        background: #f1f3f4;
    }

    .progress-bar {
        border-radius: 10px;
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
    }
</style>
@endpush

@push('scripts')
<script>
    function exportToCSV() {
        const table = document.querySelector('.table');
        const rows = Array.from(table.querySelectorAll('tr'));
        
        const csvContent = rows.map(row => {
            const cells = Array.from(row.querySelectorAll('th, td'));
            return cells.map(cell => {
                const text = cell.textContent.trim().replace(/\s+/g, ' ');
                return `"${text.replace(/"/g, '""')}"`;
            }).join(',');
        }).join('\n');
        
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `employees-${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }

    // Auto-refresh metrics every 5 minutes
    setInterval(function() {
        if (!document.hidden) {
            location.reload();
        }
    }, 300000);
</script>
@endpush
@endsection
