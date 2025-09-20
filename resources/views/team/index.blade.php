@extends('layouts.app')

@section('title', 'Operatives Management')

@section('content')
<div class="operatives-dashboard">
    <!-- Professional Page Header -->
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="header-icon bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-4">
                        <i class="bi bi-people-fill fs-2"></i>
                    </div>
                    <div>
                        <h1 class="page-title mb-2 fw-bold">Operatives Management</h1>
                        <p class="page-subtitle text-muted mb-0">
                            Comprehensive dashboard for managing construction operatives, CIS compliance, and performance metrics
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-end">
                @if(auth()->user()->canManageCompanyUsers())
                    <a href="{{ route('team.create') }}" class="btn btn-primary btn-lg shadow-sm">
                        <i class="bi bi-plus-circle me-2"></i>Add New Operative
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Key Performance Metrics -->
    <div class="row g-4 mb-5">
        <!-- Total Operatives -->
        <div class="col-xl-3 col-lg-6">
            <div class="metric-card bg-gradient-primary">
                <div class="metric-card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="metric-number text-white mb-1">{{ $metrics['total_operatives'] }}</h3>
                            <p class="metric-label text-white-75 mb-0">Total Operatives</p>
                        </div>
                        <div class="metric-icon text-white-50">
                            <i class="bi bi-people fs-1"></i>
                        </div>
                    </div>
                    <div class="metric-footer mt-3">
                        <span class="badge bg-white bg-opacity-25 text-white">
                            {{ $metrics['active_operatives'] }} Active
                        </span>
                        <span class="trend-indicator ms-2">
                            <i class="bi bi-arrow-{{ $metrics['trend_active_operatives'] === 'up' ? 'up' : ($metrics['trend_active_operatives'] === 'down' ? 'down' : 'right') }} me-1"></i>
                            {{ $metrics['activity_rate'] }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- CIS Deductions -->
        <div class="col-xl-3 col-lg-6">
            <div class="metric-card bg-gradient-success">
                <div class="metric-card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="metric-number text-white mb-1">£{{ number_format($metrics['cis_deductions_this_month'], 0) }}</h3>
                            <p class="metric-label text-white-75 mb-0">CIS This Month</p>
                        </div>
                        <div class="metric-icon text-white-50">
                            <i class="bi bi-receipt fs-1"></i>
                        </div>
                    </div>
                    <div class="metric-footer mt-3">
                        <span class="badge bg-white bg-opacity-25 text-white">
                            {{ $metrics['cis_applicable_operatives'] }} CIS Registered
                        </span>
                        <span class="trend-indicator ms-2">
                            <i class="bi bi-arrow-{{ $metrics['trend_cis_deductions'] === 'up' ? 'up' : ($metrics['trend_cis_deductions'] === 'down' ? 'down' : 'right') }} me-1"></i>
                            {{ $metrics['cis_compliance_rate'] }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Work Utilization -->
        <div class="col-xl-3 col-lg-6">
            <div class="metric-card bg-gradient-warning">
                <div class="metric-card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="metric-number text-white mb-1">{{ $metrics['utilization_rate'] }}%</h3>
                            <p class="metric-label text-white-75 mb-0">Utilization Rate</p>
                        </div>
                        <div class="metric-icon text-white-50">
                            <i class="bi bi-graph-up fs-1"></i>
                        </div>
                    </div>
                    <div class="metric-footer mt-3">
                        <span class="badge bg-white bg-opacity-25 text-white">
                            {{ $metrics['operatives_with_active_tasks'] }} On Tasks
                        </span>
                        <span class="trend-indicator ms-2">
                            <i class="bi bi-arrow-{{ $metrics['trend_task_completion'] === 'up' ? 'up' : ($metrics['trend_task_completion'] === 'down' ? 'down' : 'right') }} me-1"></i>
                            Active
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Invoices -->
        <div class="col-xl-3 col-lg-6">
            <div class="metric-card bg-gradient-info">
                <div class="metric-card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="metric-number text-white mb-1">{{ $metrics['pending_invoices'] }}</h3>
                            <p class="metric-label text-white-75 mb-0">Pending Invoices</p>
                        </div>
                        <div class="metric-icon text-white-50">
                            <i class="bi bi-file-earmark-text fs-1"></i>
                        </div>
                    </div>
                    <div class="metric-footer mt-3">
                        <span class="badge bg-white bg-opacity-25 text-white">
                            £{{ number_format($metrics['approved_invoices_amount'], 0) }} Approved
                        </span>
                        <span class="trend-indicator ms-2">
                            <i class="bi bi-clock me-1"></i>
                            Review
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Metrics Row -->
    <div class="row g-4 mb-5">
        <!-- Certification Status -->
        <div class="col-lg-4">
            <div class="metric-card-secondary h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="metric-icon-sm bg-danger bg-opacity-10 text-danger me-3">
                            <i class="bi bi-shield-exclamation"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Certification Status</h6>
                            <small class="text-muted">Document compliance</small>
                        </div>
                    </div>
                    <div class="row text-center g-3">
                        <div class="col-6">
                            <div class="metric-stat p-3 bg-warning bg-opacity-10 rounded">
                                <h4 class="text-warning mb-2">{{ $metrics['expiring_certifications'] }}</h4>
                                <small class="text-muted">Expiring Soon</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="metric-stat p-3 bg-danger bg-opacity-10 rounded">
                                <h4 class="text-danger mb-2">{{ $metrics['expired_certifications'] }}</h4>
                                <small class="text-muted">Expired</small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-light rounded">
                        <div class="progress mb-2" style="height: 8px; background-color: #e9ecef;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $metrics['certification_compliance'] }}%; border-radius: 4px;"></div>
                        </div>
                        <div class="text-center">
                            <small class="text-muted fw-medium">{{ $metrics['certification_compliance'] }}% Compliant</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Overview -->
        <div class="col-lg-4">
            <div class="metric-card-secondary h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="metric-icon-sm bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">Performance (30 Days)</h6>
                            <small class="text-muted">Task completion</small>
                        </div>
                    </div>
                    <div class="row text-center g-3">
                        <div class="col-12">
                            <div class="metric-stat p-3 bg-success bg-opacity-10 rounded">
                                <h4 class="text-success mb-2">{{ $metrics['completed_tasks_last_30_days'] }}</h4>
                                <small class="text-muted">Tasks Completed</small>
                            </div>
                        </div>
                    </div>
                        <div class="p-3 bg-light rounded">
                            <div class="progress mb-2" style="height: 8px; background-color: #e9ecef;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: 100%; border-radius: 4px;"></div>
                            </div>
                            <div class="text-center">
                                <small class="text-muted fw-medium">100% Completed</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CIS Overview -->
        <div class="col-lg-4">
            <div class="metric-card-secondary h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="metric-icon-sm bg-info bg-opacity-10 text-info me-3">
                            <i class="bi bi-calculator"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">CIS Overview (YTD)</h6>
                            <small class="text-muted">Year to date</small>
                        </div>
                    </div>
                    <div class="row text-center g-3">
                        <div class="col-12">
                            <div class="metric-stat p-3 bg-info bg-opacity-10 rounded">
                                <h4 class="text-info mb-2">£{{ number_format($metrics['cis_deductions_this_year'], 0) }}</h4>
                                <small class="text-muted">Total Deductions</small>
                            </div>
                        </div>
                    </div>
                        <div class="p-3 bg-light rounded">
                            <div class="progress mb-2" style="height: 8px; background-color: #e9ecef;">
                                <div class="progress-bar bg-info" role="progressbar" 
                                     style="width: {{ $metrics['cis_applicable_operatives'] > 0 ? 100 : 0 }}%; border-radius: 4px;"></div>
                            </div>
                            <div class="text-center">
                                <small class="text-muted fw-medium">{{ $metrics['cis_applicable_operatives'] }} Registered</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">
                <i class="bi bi-funnel me-2"></i>Filter & Search Operatives
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('team.index') }}" class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label for="search" class="form-label fw-medium">Search Operatives</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Name or email...">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="status" class="form-label fw-medium">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label for="certification_status" class="form-label fw-medium">Certification Status</label>
                    <select class="form-select" id="certification_status" name="certification_status">
                        <option value="">All Certifications</option>
                        <option value="expiring" {{ request('certification_status') == 'expiring' ? 'selected' : '' }}>Expiring Soon</option>
                        <option value="expired" {{ request('certification_status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bi bi-search me-1"></i>Search
                        </button>
                        <a href="{{ route('team.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Operatives Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="bi bi-people me-2"></i>Operatives List 
                <span class="badge bg-primary ms-2">{{ $users->total() }}</span>
            </h5>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" onclick="exportToCSV()">
                    <i class="bi bi-download me-1"></i>Export CSV
                </button>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-printer me-2"></i>Print List</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-envelope me-2"></i>Email Report</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 ps-4">Operative</th>
                                <th class="border-0">Contact</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">CIS Info</th>
                                <th class="border-0">Performance</th>
                                <th class="border-0">Last Activity</th>
                                <th class="border-0 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr class="operative-row" data-operative-id="{{ $user->id }}">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-container me-3">
                                                @if($user->employee && $user->employee->avatar)
                                                    <img src="{{ Storage::url($user->employee->avatar) }}" 
                                                         class="rounded-circle" width="45" height="45" 
                                                         alt="{{ $user->name }}">
                                                @else
                                                    <div class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center">
                                                        <i class="bi bi-person-fill"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-1">
                                                    <a href="{{ route('profiles.operative', $user->employee ?? $user) }}" 
                                                       class="text-decoration-none fw-medium operative-name-link">
                                                        {{ $user->name }}
                                                    </a>
                                                </h6>
                                                <small class="text-muted">
                                                    {{ $user->employee ? $user->employee->employee_id : 'ID: ' . $user->id }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="mb-1">
                                                <i class="bi bi-envelope-fill text-muted me-2"></i>
                                                <small>{{ $user->email }}</small>
                                            </div>
                                            @if($user->phone)
                                                <div>
                                                    <i class="bi bi-telephone-fill text-muted me-2"></i>
                                                    <small>{{ $user->phone }}</small>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            @if($user->is_active)
                                                <span class="badge bg-success bg-opacity-10 text-success">
                                                    <i class="bi bi-check-circle-fill me-1"></i>Active
                                                </span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger">
                                                    <i class="bi bi-x-circle-fill me-1"></i>Inactive
                                                </span>
                                            @endif
                                            @if($user->employee && $user->employee->cis_applicable)
                                                <div class="mt-1">
                                                    <span class="badge bg-info bg-opacity-10 text-info">
                                                        <i class="bi bi-shield-check me-1"></i>CIS
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($user->employee && $user->employee->cis_applicable)
                                            <div>
                                                <small class="text-muted d-block">Rate: {{ $user->employee->cis_rate ?? 20 }}%</small>
                                                @if($user->employee->day_rate)
                                                    <small class="text-muted">Day Rate: £{{ number_format($user->employee->day_rate, 0) }}</small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">
                                                <i class="bi bi-dash"></i> Not Applicable
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            @php
                                                $activeTasks = $user->tasks()->whereIn('status', ['pending', 'in_progress'])->count();
                                                $completedTasks = $user->tasks()->where('status', 'completed')->count();
                                            @endphp
                                            <small class="text-muted d-block">Active: {{ $activeTasks }} tasks</small>
                                            <small class="text-success">Completed: {{ $completedTasks }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $user->updated_at ? $user->updated_at->diffForHumans() : 'Never' }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('profiles.operative', $user->employee ?? $user) }}">
                                                        <i class="bi bi-person-lines-fill me-2"></i>View Profile
                                                    </a>
                                                </li>
                                                @if(auth()->user()->canManageCompanyUsers())
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('team.edit', $user) }}">
                                                            <i class="bi bi-pencil me-2"></i>Edit Details
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form method="POST" action="{{ route('team.destroy', $user) }}" 
                                                              onsubmit="return confirm('Are you sure you want to remove this operative?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="bi bi-trash me-2"></i>Remove Operative
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} 
                            of {{ $users->total() }} operatives
                        </div>
                        {{ $users->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state">
                        <i class="bi bi-people display-1 text-muted mb-4"></i>
                        <h4 class="text-muted mb-3">No Operatives Found</h4>
                        <p class="text-muted mb-4">
                            @if(request()->hasAny(['search', 'status', 'certification_status']))
                                No operatives match your current filters. Try adjusting your search criteria.
                            @else
                                You haven't added any operatives yet. Start by adding your first operative.
                            @endif
                        </p>
                        @if(auth()->user()->canManageCompanyUsers())
                            <a href="{{ route('team.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add First Operative
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .operatives-dashboard {
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
        height: 100%;
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

    .metric-stat h4 {
        font-size: 1.8rem;
        font-weight: 700;
    }

    /* Table Enhancements */
    .table th {
        font-weight: 600;
        color: #495057;
        background: #f8f9fa !important;
        border-top: none;
    }

    .operative-row {
        transition: background-color 0.2s ease;
    }

    .operative-row:hover {
        background: #f8f9fa;
    }

    .operative-name-link {
        color: #495057;
        font-weight: 600;
        transition: color 0.2s ease;
    }

    .operative-name-link:hover {
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
        background: rgba(255,255,255,0.2);
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
        a.download = `operatives-${new Date().toISOString().split('T')[0]}.csv`;
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

