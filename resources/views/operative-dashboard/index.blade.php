@extends('layouts.app')

@section('title', 'Operative Dashboard')

@section('content')
<div class="container-fluid px-2 px-md-4">
    <!-- Mobile-Optimized Page Header -->
    <div class="page-header-section mb-3 mb-md-4">
        <div class="row align-items-center g-2">
            <div class="col-auto d-none d-md-block">
                <div class="page-icon">
                    <i class="bi bi-kanban"></i>
                </div>
            </div>
            <div class="col">
                <h1 class="page-title mb-1">
                    <i class="bi bi-kanban d-md-none me-2"></i>
                    <span class="d-none d-sm-inline">Operative Dashboard</span>
                    <span class="d-sm-none">Dashboard</span>
                </h1>
                <p class="page-subtitle text-muted mb-0 d-none d-sm-block">Your operational workspace and activity center</p>
            </div>
            <div class="col-auto">
                <div class="date-info text-end">
                    <div class="current-date d-none d-sm-block">{{ now()->format('l, F j, Y') }}</div>
                    <div class="current-date d-sm-none">{{ now()->format('M j, Y') }}</div>
                    <div class="current-time">{{ now()->format('g:i A') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile-Optimized Quick Stats -->
    <div class="row g-2 g-md-4 mb-3 mb-md-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $myInvoices->count() }}</div>
                    <div class="stat-label">
                        <span class="d-none d-sm-inline">My Recent Invoices</span>
                        <span class="d-sm-none">Invoices</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-check-square"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $myTasks->where('status', '!=', 'completed')->count() }}</div>
                    <div class="stat-label">
                        <span class="d-none d-sm-inline">Active Tasks</span>
                        <span class="d-sm-none">Active</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $myTasks->where('priority', 'urgent')->where('status', '!=', 'completed')->count() }}</div>
                    <div class="stat-label">
                        <span class="d-none d-sm-inline">Urgent Tasks</span>
                        <span class="d-sm-none">Urgent</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card">
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $myTasks->where('due_date', '<', now())->where('status', '!=', 'completed')->count() }}</div>
                    <div class="stat-label">
                        <span class="d-none d-sm-inline">Overdue Tasks</span>
                        <span class="d-sm-none">Overdue</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Tabs (Hidden on Mobile) -->
    <div class="card shadow-sm d-none d-md-block">
        <div class="card-header bg-white border-bottom p-3">
            <ul class="nav nav-tabs card-header-tabs" id="operativeTabsDesktop" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'invoices' ? 'active' : '' }}" 
                            id="invoices-tab-desktop" 
                            data-bs-toggle="tab" 
                            data-bs-target="#invoices" 
                            type="button" 
                            role="tab">
                        <i class="bi bi-receipt me-2"></i>My Invoices
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'tasks' ? 'active' : '' }}" 
                            id="tasks-tab-desktop" 
                            data-bs-toggle="tab" 
                            data-bs-target="#tasks" 
                            type="button" 
                            role="tab">
                        <i class="bi bi-check-square me-2"></i>My Tasks
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'time' ? 'active' : '' }}" 
                            id="time-tab-desktop" 
                            data-bs-toggle="tab" 
                            data-bs-target="#time" 
                            type="button" 
                            role="tab">
                        <i class="bi bi-stopwatch me-2"></i>Time Tracking
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'reports' ? 'active' : '' }}" 
                            id="reports-tab-desktop" 
                            data-bs-toggle="tab" 
                            data-bs-target="#reports" 
                            type="button" 
                            role="tab">
                        <i class="bi bi-graph-up me-2"></i>Reports
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body d-none d-md-block">
            <div class="tab-content" id="operativeTabsContent">
                <!-- My Invoices Tab -->
                <div class="tab-pane fade {{ $activeTab === 'invoices' ? 'show active' : '' }}" 
                     id="invoices" 
                     role="tabpanel">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 mb-md-4 gap-2">
                        <h5 class="mb-0">
                            <span class="d-none d-sm-inline">My Recent Invoices</span>
                            <span class="d-sm-none">My Invoices</span>
                        </h5>
                        <a href="{{ route('operative-invoices.create') }}" class="btn btn-primary btn-sm w-100 w-sm-auto">
                            <i class="bi bi-plus-circle me-1"></i>
                            <span class="d-none d-sm-inline">Create Invoice</span>
                            <span class="d-sm-none">Create New</span>
                        </a>
                    </div>

                    @if($myInvoices->count() > 0)
                        <!-- Mobile-First Invoice Cards -->
                        <div class="d-block d-lg-none">
                            <div class="row g-3">
                                @foreach($myInvoices as $invoice)
                                    <div class="col-12">
                                        <div class="invoice-card mobile-card">
                                            <div class="card-header-mobile">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1"><strong>#{{ $invoice->invoice_number }}</strong></h6>
                                                        <small class="text-muted">{{ $invoice->week_period }}</small>
                                                    </div>
                                                    <span class="badge bg-{{ $invoice->status_color }}">{{ ucfirst($invoice->status) }}</span>
                                                </div>
                                            </div>
                                            <div class="card-body-mobile">
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <div class="info-item">
                                                            <small class="text-muted d-block">Manager</small>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-xs me-2">
                                                                    <div class="avatar-title bg-primary bg-opacity-10 text-primary rounded-circle">
                                                                        {{ substr($invoice->manager->name, 0, 1) }}
                                                                    </div>
                                                                </div>
                                                                <span class="small">{{ Str::limit($invoice->manager->name, 15) }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="info-item">
                                                            <small class="text-muted d-block">Amount</small>
                                                            <div>
                                                                <strong class="text-success">£{{ number_format($invoice->net_amount, 2) }}</strong>
                                                                @if($invoice->cis_applicable)
                                                                    <br><small class="text-muted">CIS: -£{{ number_format($invoice->cis_deduction, 2) }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <small class="text-muted d-block">Site/Project</small>
                                                    <div>
                                                        <strong class="small">{{ $invoice->site->name }}</strong><br>
                                                        @if($invoice->project)
                                                            <small class="text-muted">{{ $invoice->project->name }}</small>
                                                        @else
                                                            <small class="text-muted">No specific project</small>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-2 align-items-center">
                                                    <a href="{{ route('operative-invoices.show', $invoice) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                                        <i class="bi bi-eye me-1"></i>View
                                                    </a>
                                                    @php
                                                        $statusColors = [
                                                            'draft' => 'secondary',
                                                            'submitted' => 'warning',
                                                            'approved' => 'success',
                                                            'paid' => 'primary',
                                                            'rejected' => 'danger'
                                                        ];
                                                        $statusColor = $statusColors[$invoice->status] ?? 'secondary';
                                                    @endphp
                                                    <span class="badge bg-{{ $statusColor }}">{{ ucfirst($invoice->status) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Desktop Table -->
                        <div class="d-none d-lg-block">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Invoice #</th>
                                            <th>Manager</th>
                                            <th>Site/Project</th>
                                            <th>Week Period</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($myInvoices as $invoice)
                                            <tr>
                                                <td>
                                                    <strong>#{{ $invoice->invoice_number }}</strong>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-2">
                                                            <div class="avatar-title bg-primary bg-opacity-10 text-primary rounded-circle">
                                                                {{ substr($invoice->manager->name, 0, 1) }}
                                                            </div>
                                                        </div>
                                                        {{ $invoice->manager->name }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $invoice->site->name }}</strong><br>
                                                        @if($invoice->project)
                                                            <small class="text-muted">{{ $invoice->project->name }}</small>
                                                        @else
                                                            <small class="text-muted">No specific project</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <small>{{ $invoice->week_period }}</small>
                                                </td>
                                                <td>
                                                    <strong>£{{ number_format($invoice->net_amount, 2) }}</strong>
                                                    @if($invoice->cis_applicable)
                                                        <br><small class="text-muted">CIS: -£{{ number_format($invoice->cis_deduction, 2) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $invoice->status_color }}">{{ ucfirst($invoice->status) }}</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('operative-invoices.show', $invoice) }}" class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-eye me-1"></i>View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">No invoices found</h5>
                            <p class="text-muted">Create your first invoice to get started</p>
                            <a href="{{ route('operative-invoices.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Create Invoice
                            </a>
                        </div>
                    @endif
                </div>

                <!-- My Tasks Tab -->
                <div class="tab-pane fade {{ $activeTab === 'tasks' ? 'show active' : '' }}" 
                     id="tasks" 
                     role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">My Tasks</h5>
                        <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i>Create Task
                        </a>
                    </div>

                    @if($myTasks->count() > 0)
                        <div class="row g-3">
                            @foreach($myTasks as $task)
                                <div class="col-md-6 col-lg-4">
                                    <div class="task-card {{ $task->status === 'completed' ? 'completed' : '' }}">
                                        <div class="task-header">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h6 class="task-title mb-1">{{ $task->title }}</h6>
                                                @php
                                                    $priorityColors = [
                                                        'urgent' => 'danger',
                                                        'high' => 'warning',
                                                        'medium' => 'info',
                                                        'low' => 'secondary'
                                                    ];
                                                    $color = $priorityColors[$task->priority] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $color }} badge-sm">{{ ucfirst($task->priority) }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="task-body">
                                            @if($task->project)
                                                <div class="task-project mb-2">
                                                    <i class="bi bi-folder2 text-muted me-1"></i>
                                                    <small class="text-muted">{{ $task->project->name }}</small>
                                                </div>
                                            @endif
                                            
                                            @if($task->description)
                                                <p class="task-description">{{ Str::limit($task->description, 100) }}</p>
                                            @endif
                                        </div>

                                        <div class="task-footer">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="task-due">
                                                    @if($task->due_date)
                                                        <small class="text-muted">
                                                            <i class="bi bi-calendar me-1"></i>
                                                            {{ $task->due_date->format('M j') }}
                                                        </small>
                                                    @endif
                                                </div>
                                                <div class="task-status">
                                                    @php
                                                        $statusColors = [
                                                            'pending' => 'warning',
                                                            'in_progress' => 'info',
                                                            'completed' => 'success',
                                                            'on_hold' => 'secondary'
                                                        ];
                                                        $statusColor = $statusColors[$task->status] ?? 'secondary';
                                                    @endphp
                                                    <span class="badge bg-{{ $statusColor }} badge-sm">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="task-actions mt-2">
                                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye me-1"></i>View
                                            </a>
                                            @if($task->status !== 'completed')
                                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="bi bi-pencil me-1"></i>Edit
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-check-square text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">No tasks assigned</h5>
                            <p class="text-muted">You don't have any tasks assigned to you yet</p>
                        </div>
                    @endif
                </div>

                <!-- Reports Tab -->
                <div class="tab-pane fade {{ $activeTab === 'reports' ? 'show active' : '' }}" 
                     id="reports" 
                     role="tabpanel">
                    <h5 class="mb-4">Operational Reports</h5>
                    
                    <div class="row g-4">
                        <!-- Invoice Reports -->
                        <div class="col-lg-6">
                            <div class="report-card">
                                <div class="report-header">
                                    <h6 class="report-title">
                                        <i class="bi bi-receipt text-primary me-2"></i>Invoice Overview
                                    </h6>
                                </div>
                                <div class="report-body">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="report-stat">
                                                <div class="stat-number text-primary">{{ $reportsData['invoices']['total_invoices'] }}</div>
                                                <div class="stat-label">Total Invoices</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="report-stat">
                                                <div class="stat-number text-success">{{ $reportsData['invoices']['paid_invoices'] }}</div>
                                                <div class="stat-label">Paid</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="report-stat">
                                                <div class="stat-number text-warning">{{ $reportsData['invoices']['pending_invoices'] }}</div>
                                                <div class="stat-label">Pending</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="report-stat">
                                                <div class="stat-number text-danger">{{ $reportsData['invoices']['overdue_invoices'] }}</div>
                                                <div class="stat-label">Overdue</div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="my-3">
                                    <div class="revenue-stats">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Total Revenue:</span>
                                            <strong class="text-success">${{ number_format($reportsData['invoices']['total_revenue'], 2) }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Pending Revenue:</span>
                                            <strong class="text-warning">${{ number_format($reportsData['invoices']['pending_revenue'], 2) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Task Reports -->
                        <div class="col-lg-6">
                            <div class="report-card">
                                <div class="report-header">
                                    <h6 class="report-title">
                                        <i class="bi bi-check-square text-success me-2"></i>Task Overview
                                    </h6>
                                </div>
                                <div class="report-body">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="report-stat">
                                                <div class="stat-number text-primary">{{ $reportsData['tasks']['total_tasks'] }}</div>
                                                <div class="stat-label">Total Tasks</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="report-stat">
                                                <div class="stat-number text-info">{{ $reportsData['tasks']['my_tasks'] }}</div>
                                                <div class="stat-label">My Tasks</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="report-stat">
                                                <div class="stat-number text-success">{{ $reportsData['tasks']['completed_tasks'] }}</div>
                                                <div class="stat-label">Completed</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="report-stat">
                                                <div class="stat-number text-danger">{{ $reportsData['tasks']['overdue_tasks'] }}</div>
                                                <div class="stat-label">Overdue</div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="my-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Urgent Tasks:</span>
                                        <strong class="text-danger">{{ $reportsData['tasks']['urgent_tasks'] }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Project Reports -->
                        <div class="col-lg-6">
                            <div class="report-card">
                                <div class="report-header">
                                    <h6 class="report-title">
                                        <i class="bi bi-folder text-info me-2"></i>Project Overview
                                    </h6>
                                </div>
                                <div class="report-body">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="report-stat">
                                                <div class="stat-number text-primary">{{ $reportsData['projects']['total_projects'] }}</div>
                                                <div class="stat-label">Total Projects</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="report-stat">
                                                <div class="stat-number text-success">{{ $reportsData['projects']['active_projects'] }}</div>
                                                <div class="stat-label">Active</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="report-stat">
                                                <div class="stat-number text-info">{{ $reportsData['projects']['completed_projects'] }}</div>
                                                <div class="stat-label">Completed</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="report-stat">
                                                <div class="stat-number text-warning">{{ $reportsData['projects']['on_hold_projects'] }}</div>
                                                <div class="stat-label">On Hold</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="col-lg-6">
                            <div class="report-card">
                                <div class="report-header">
                                    <h6 class="report-title">
                                        <i class="bi bi-clock-history text-secondary me-2"></i>Recent Activity
                                    </h6>
                                </div>
                                <div class="report-body">
                                    @if($reportsData['recent_activity']->count() > 0)
                                        <div class="activity-list">
                                            @foreach($reportsData['recent_activity'] as $activity)
                                                <div class="activity-item">
                                                    <div class="activity-icon">
                                                        <i class="bi {{ $activity['icon'] }} text-{{ $activity['color'] }}"></i>
                                                    </div>
                                                    <div class="activity-content">
                                                        <div class="activity-title">{{ $activity['title'] }}</div>
                                                        <div class="activity-description">{{ $activity['description'] }}</div>
                                                        <div class="activity-time">{{ $activity['date']->diffForHumans() }}</div>
                                                    </div>
                                                    @if(isset($activity['amount']))
                                                        <div class="activity-amount">
                                                            <strong>${{ number_format($activity['amount'], 2) }}</strong>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-3">
                                            <i class="bi bi-clock-history text-muted" style="font-size: 2rem;"></i>
                                            <p class="text-muted mt-2 mb-0">No recent activity</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Time Tracking Tab -->
                <div class="tab-pane fade {{ $activeTab === 'time' ? 'show active' : '' }}" 
                     id="time" 
                     role="tabpanel">
                    <div class="time-tracking-content">
                        <!-- Time Status Card -->
                        <div class="row g-4 mb-4">
                            <div class="col-lg-8">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">
                                                <i class="bi bi-stopwatch me-2 text-primary"></i>Time Tracking
                                            </h5>
                                            <div class="current-time">
                                                <small class="text-muted">Current Time</small>
                                                <div class="fw-bold" id="currentTime">{{ now()->format('g:i A') }}</div>
                                            </div>
                                        </div>
                                        
                                        <!-- Clock In/Out Section -->
                                        <div class="clock-section">
                                            @if($activeTimeEntry)
                                                <!-- Currently Clocked In -->
                                                <div class="alert alert-success d-flex align-items-center" role="alert">
                                                    <i class="bi bi-play-circle-fill me-3 fs-4"></i>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">You are currently clocked in</h6>
                                                        <p class="mb-0">
                                                            Started at {{ $activeTimeEntry->clock_in->format('g:i A') }}
                                                            @if($activeTimeEntry->project)
                                                                • Project: {{ $activeTimeEntry->project->name }}
                                                            @endif
                                                            @if($activeTimeEntry->site)
                                                                • Site: {{ $activeTimeEntry->site->name }}
                                                            @endif
                                                        </p>
                                                        <div class="mt-2">
                                                            <span class="badge bg-success" id="currentDuration">{{ $activeTimeEntry->duration_formatted }}</span>
                                                        </div>
                                                    </div>
                                                    <button class="btn btn-danger" onclick="clockOut()">
                                                        <i class="bi bi-stop-circle me-2"></i>Clock Out
                                                    </button>
                                                </div>
                                            @else
                                                <!-- Not Clocked In -->
                                                <div class="clock-in-form">
                                                    <div class="text-center mb-4">
                                                        <div class="clock-icon mb-3">
                                                            <i class="bi bi-clock text-muted" style="font-size: 3rem;"></i>
                                                        </div>
                                                        <h6 class="text-muted">Ready to start your workday?</h6>
                                                    </div>
                                                    
                                                    <form id="clockInForm">
                                                        @csrf
                                                        <div class="row g-3 mb-3">
                                                            <div class="col-md-6">
                                                                <label for="site_id" class="form-label">Site</label>
                                                                <select class="form-select" name="site_id" id="site_id">
                                                                    <option value="">Select Site (Optional)</option>
                                                                    @foreach($availableSites as $site)
                                                                        <option value="{{ $site->id }}">{{ $site->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                                                                <select class="form-select" name="project_id" id="project_id" required>
                                                                    <option value="">Select Project</option>
                                                                    @foreach($availableProjects as $project)
                                                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <small class="form-text text-muted">Required for location-based clock in</small>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="notes" class="form-label">Notes (Optional)</label>
                                                            <textarea class="form-control" name="notes" id="notes" rows="2" placeholder="What are you working on today?"></textarea>
                                                        </div>
                                                        <div class="text-center">
                                                            <button type="button" class="btn btn-success btn-lg" onclick="clockIn()">
                                                                <i class="bi bi-play-circle me-2"></i>Clock In
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Time Summary -->
                            <div class="col-lg-4">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body text-center">
                                                <i class="bi bi-calendar-day text-primary mb-2" style="font-size: 2rem;"></i>
                                                <h3 class="mb-1">{{ number_format($todayHours, 1) }}</h3>
                                                <p class="text-muted mb-0">Hours Today</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body text-center">
                                                <i class="bi bi-calendar-week text-success mb-2" style="font-size: 2rem;"></i>
                                                <h3 class="mb-1">{{ number_format($weekHours, 1) }}</h3>
                                                <p class="text-muted mb-0">Hours This Week</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Time Entries -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">
                                    <i class="bi bi-list-ul me-2"></i>Recent Time Entries
                                </h6>
                            </div>
                            <div class="card-body">
                                @if($recentTimeEntries->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Project/Site</th>
                                                    <th>Clock In</th>
                                                    <th>Clock Out</th>
                                                    <th>Duration</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($recentTimeEntries as $entry)
                                                    <tr>
                                                        <td>{{ $entry->clock_in->format('M j, Y') }}</td>
                                                        <td>
                                                            @if($entry->project)
                                                                <strong>{{ $entry->project->name }}</strong>
                                                                @if($entry->site)
                                                                    <br><small class="text-muted">{{ $entry->site->name }}</small>
                                                                @endif
                                                            @elseif($entry->site)
                                                                <strong>{{ $entry->site->name }}</strong>
                                                            @else
                                                                <span class="text-muted">General Work</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $entry->clock_in->format('g:i A') }}</td>
                                                        <td>
                                                            @if($entry->clock_out)
                                                                {{ $entry->clock_out->format('g:i A') }}
                                                            @else
                                                                <span class="badge bg-success">Active</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <strong>{{ $entry->duration_formatted }}</strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $entry->status_color }}">
                                                                {{ ucfirst($entry->status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="bi bi-clock text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-2">No time entries yet. Clock in to get started!</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Content Area (No Card Wrapper) -->
    <div class="mobile-content d-block d-md-none">
        <div class="tab-content" id="operativeTabsContentMobile">
            <!-- Mobile Invoices Tab -->
            <div class="tab-pane fade {{ $activeTab === 'invoices' ? 'show active' : '' }}" 
                 id="invoices-mobile" 
                 role="tabpanel">
                <div class="mobile-tab-content">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 gap-2">
                        <h5 class="mb-0">My Invoices</h5>
                        <a href="{{ route('operative-invoices.create') }}" class="btn btn-primary btn-sm w-100 w-sm-auto">
                            <i class="bi bi-plus-circle me-1"></i>Create New
                        </a>
                    </div>

                    @if($myInvoices->count() > 0)
                        <div class="row g-3">
                            @foreach($myInvoices as $invoice)
                                <div class="col-12">
                                    <div class="invoice-card mobile-card">
                                        <div class="card-header-mobile">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1"><strong>#{{ $invoice->invoice_number }}</strong></h6>
                                                    <small class="text-muted">{{ $invoice->week_period }}</small>
                                                </div>
                                                <span class="badge bg-{{ $invoice->status_color }}">{{ ucfirst($invoice->status) }}</span>
                                            </div>
                                        </div>
                                        <div class="card-body-mobile">
                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <div class="info-item">
                                                        <small class="text-muted d-block">Manager</small>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-xs me-2">
                                                                <div class="avatar-title bg-primary bg-opacity-10 text-primary rounded-circle">
                                                                    {{ substr($invoice->manager->name, 0, 1) }}
                                                                </div>
                                                            </div>
                                                            <span class="small">{{ Str::limit($invoice->manager->name, 15) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="info-item">
                                                        <small class="text-muted d-block">Amount</small>
                                                        <div>
                                                            <strong class="text-success">£{{ number_format($invoice->net_amount, 2) }}</strong>
                                                            @if($invoice->cis_applicable)
                                                                <br><small class="text-muted">CIS: -£{{ number_format($invoice->cis_deduction, 2) }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <small class="text-muted d-block">Site/Project</small>
                                                <div>
                                                    <strong class="small">{{ $invoice->site->name }}</strong><br>
                                                    @if($invoice->project)
                                                        <small class="text-muted">{{ $invoice->project->name }}</small>
                                                    @else
                                                        <small class="text-muted">No specific project</small>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2 align-items-center">
                                                <a href="{{ route('operative-invoices.show', $invoice) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                                    <i class="bi bi-eye me-1"></i>View
                                                </a>
                                                @php
                                                    $statusColors = [
                                                        'draft' => 'secondary',
                                                        'submitted' => 'warning',
                                                        'approved' => 'success',
                                                        'paid' => 'primary',
                                                        'rejected' => 'danger'
                                                    ];
                                                    $statusColor = $statusColors[$invoice->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $statusColor }}">{{ ucfirst($invoice->status) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">No invoices found</h5>
                            <p class="text-muted">Create your first invoice to get started</p>
                            <a href="{{ route('operative-invoices.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Create Invoice
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Mobile Tasks Tab -->
            <div class="tab-pane fade {{ $activeTab === 'tasks' ? 'show active' : '' }}" 
                 id="tasks-mobile" 
                 role="tabpanel">
                <div class="mobile-tab-content">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">My Tasks</h5>
                        <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i>New Task
                        </a>
                    </div>

                    @if($myTasks->count() > 0)
                        <div class="row g-3">
                            @foreach($myTasks as $task)
                                <div class="col-12">
                                    <div class="task-card mobile-card {{ $task->status === 'completed' ? 'completed' : '' }}">
                                        <div class="card-header-mobile">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h6 class="task-title mb-1">{{ $task->title }}</h6>
                                                @php
                                                    $priorityColors = [
                                                        'urgent' => 'danger',
                                                        'high' => 'warning',
                                                        'medium' => 'info',
                                                        'low' => 'secondary'
                                                    ];
                                                    $color = $priorityColors[$task->priority] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $color }} badge-sm">{{ ucfirst($task->priority) }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="card-body-mobile">
                                            @if($task->project)
                                                <div class="task-project mb-2">
                                                    <i class="bi bi-folder2 text-muted me-1"></i>
                                                    <small class="text-muted">{{ $task->project->name }}</small>
                                                </div>
                                            @endif
                                            
                                            @if($task->description)
                                                <p class="task-description small">{{ Str::limit($task->description, 100) }}</p>
                                            @endif

                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="task-due">
                                                    @if($task->due_date)
                                                        <small class="text-muted">
                                                            <i class="bi bi-calendar me-1"></i>
                                                            {{ $task->due_date->format('M j') }}
                                                        </small>
                                                    @endif
                                                </div>
                                                <div class="task-status">
                                                    @php
                                                        $statusColors = [
                                                            'pending' => 'warning',
                                                            'in_progress' => 'info',
                                                            'completed' => 'success',
                                                            'on_hold' => 'secondary'
                                                        ];
                                                        $statusColor = $statusColors[$task->status] ?? 'secondary';
                                                    @endphp
                                                    <span class="badge bg-{{ $statusColor }} badge-sm">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
                                                </div>
                                            </div>

                                            <div class="d-flex gap-2">
                                                <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                                    <i class="bi bi-eye me-1"></i>View
                                                </a>
                                                @if($task->status !== 'completed')
                                                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-secondary btn-sm flex-fill">
                                                        <i class="bi bi-pencil me-1"></i>Edit
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-check-square text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 text-muted">No tasks assigned</h5>
                            <p class="text-muted">You don't have any tasks assigned to you yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Mobile Time Tracking Tab -->
            <div class="tab-pane fade {{ $activeTab === 'time' ? 'show active' : '' }}" 
                 id="time-mobile" 
                 role="tabpanel">
                <div class="mobile-tab-content">
                    <h5 class="mb-4">
                        <i class="bi bi-stopwatch me-2"></i>Time Tracking
                    </h5>
                    
                    <!-- Mobile Time Status -->
                    <div class="mb-4">
                        @if($activeTimeEntry)
                            <!-- Currently Clocked In - Mobile -->
                            <div class="alert alert-success" role="alert">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-play-circle-fill me-2 fs-5"></i>
                                    <strong>Currently Clocked In</strong>
                                </div>
                                <p class="mb-2">Started: {{ $activeTimeEntry->clock_in->format('g:i A') }}</p>
                                @if($activeTimeEntry->project)
                                    <p class="mb-2">Project: {{ $activeTimeEntry->project->name }}</p>
                                @endif
                                @if($activeTimeEntry->site)
                                    <p class="mb-2">Site: {{ $activeTimeEntry->site->name }}</p>
                                @endif
                                <div class="mb-3">
                                    <span class="badge bg-success fs-6" id="mobileDuration">{{ $activeTimeEntry->duration_formatted }}</span>
                                </div>
                                <button class="btn btn-danger btn-sm w-100" onclick="clockOut()">
                                    <i class="bi bi-stop-circle me-2"></i>Clock Out
                                </button>
                            </div>
                        @else
                            <!-- Clock In Form - Mobile -->
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <i class="bi bi-clock text-muted mb-2" style="font-size: 2.5rem;"></i>
                                        <h6 class="text-muted">Ready to start?</h6>
                                    </div>
                                    
                                    <form id="mobileClockInForm">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="mobile_site_id" class="form-label">Site</label>
                                            <select class="form-select" name="site_id" id="mobile_site_id">
                                                <option value="">Select Site (Optional)</option>
                                                @foreach($availableSites as $site)
                                                    <option value="{{ $site->id }}">{{ $site->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="mobile_project_id" class="form-label">Project <span class="text-danger">*</span></label>
                                            <select class="form-select" name="project_id" id="mobile_project_id" required>
                                                <option value="">Select Project</option>
                                                @foreach($availableProjects as $project)
                                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">Required for location-based clock in</small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="mobile_notes" class="form-label">Notes</label>
                                            <textarea class="form-control" name="notes" id="mobile_notes" rows="2" placeholder="What are you working on?"></textarea>
                                        </div>
                                        <button type="button" class="btn btn-success btn-lg w-100" onclick="clockInMobile()">
                                            <i class="bi bi-play-circle me-2"></i>Clock In
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Mobile Time Summary -->
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center py-3">
                                    <i class="bi bi-calendar-day text-primary mb-1"></i>
                                    <h4 class="mb-0">{{ number_format($todayHours, 1) }}</h4>
                                    <small class="text-muted">Hours Today</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center py-3">
                                    <i class="bi bi-calendar-week text-success mb-1"></i>
                                    <h4 class="mb-0">{{ number_format($weekHours, 1) }}</h4>
                                    <small class="text-muted">This Week</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Recent Time Entries -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">Recent Time Entries</h6>
                        </div>
                        <div class="card-body">
                            @if($recentTimeEntries->count() > 0)
                                @foreach($recentTimeEntries->take(5) as $entry)
                                    <div class="time-entry-mobile mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $entry->clock_in->format('M j, Y') }}</h6>
                                                @if($entry->project)
                                                    <p class="mb-1 small">{{ $entry->project->name }}</p>
                                                @elseif($entry->site)
                                                    <p class="mb-1 small">{{ $entry->site->name }}</p>
                                                @else
                                                    <p class="mb-1 small text-muted">General Work</p>
                                                @endif
                                                <small class="text-muted">
                                                    {{ $entry->clock_in->format('g:i A') }}
                                                    @if($entry->clock_out)
                                                        - {{ $entry->clock_out->format('g:i A') }}
                                                    @endif
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold">{{ $entry->duration_formatted }}</div>
                                                <span class="badge bg-{{ $entry->status_color }} small">
                                                    {{ ucfirst($entry->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-3">
                                    <i class="bi bi-clock text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2 mb-0">No time entries yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Reports Tab -->
            <div class="tab-pane fade {{ $activeTab === 'reports' ? 'show active' : '' }}" 
                 id="reports-mobile" 
                 role="tabpanel">
                <div class="mobile-tab-content">
                    <h5 class="mb-4">Reports</h5>
                    
                    <div class="row g-3">
                        <!-- Mobile Invoice Stats -->
                        <div class="col-12">
                            <div class="report-card mobile-card">
                                <div class="card-header-mobile">
                                    <h6 class="mb-0">
                                        <i class="bi bi-receipt text-primary me-2"></i>Invoice Overview
                                    </h6>
                                </div>
                                <div class="card-body-mobile">
                                    <div class="row g-2 text-center">
                                        <div class="col-6">
                                            <div class="stat-number text-primary small">{{ $reportsData['invoices']['total_invoices'] }}</div>
                                            <div class="stat-label small">Total</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-number text-success small">{{ $reportsData['invoices']['paid_invoices'] }}</div>
                                            <div class="stat-label small">Paid</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Task Stats -->
                        <div class="col-12">
                            <div class="report-card mobile-card">
                                <div class="card-header-mobile">
                                    <h6 class="mb-0">
                                        <i class="bi bi-check-square text-success me-2"></i>Task Overview
                                    </h6>
                                </div>
                                <div class="card-body-mobile">
                                    <div class="row g-2 text-center">
                                        <div class="col-6">
                                            <div class="stat-number text-info small">{{ $reportsData['tasks']['my_tasks'] }}</div>
                                            <div class="stat-label small">My Tasks</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-number text-danger small">{{ $reportsData['tasks']['urgent_tasks'] }}</div>
                                            <div class="stat-label small">Urgent</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Native App Bottom Tab Bar (Mobile Only) -->
    <div class="bottom-tab-bar d-block d-md-none">
        <div class="tab-bar-content">
            <button class="bottom-tab {{ $activeTab === 'invoices' ? 'active' : '' }}" 
                    data-bs-toggle="tab" 
                    data-bs-target="#invoices-mobile" 
                    type="button" 
                    role="tab">
                <i class="bi bi-receipt"></i>
                <span>Invoices</span>
            </button>
            <button class="bottom-tab {{ $activeTab === 'tasks' ? 'active' : '' }}" 
                    data-bs-toggle="tab" 
                    data-bs-target="#tasks-mobile" 
                    type="button" 
                    role="tab">
                <i class="bi bi-check-square"></i>
                <span>Tasks</span>
            </button>
            <button class="bottom-tab {{ $activeTab === 'time' ? 'active' : '' }}" 
                    data-bs-toggle="tab" 
                    data-bs-target="#time-mobile" 
                    type="button" 
                    role="tab">
                <i class="bi bi-stopwatch"></i>
                <span>Time</span>
            </button>
            <button class="bottom-tab {{ $activeTab === 'reports' ? 'active' : '' }}" 
                    data-bs-toggle="tab" 
                    data-bs-target="#reports-mobile" 
                    type="button" 
                    role="tab">
                <i class="bi bi-graph-up"></i>
                <span>Reports</span>
            </button>
        </div>
    </div>

    <!-- Mobile Quick Action Button -->
    <div class="fab-container d-block d-lg-none">
        <div class="fab-main" id="fabMain">
            <i class="bi bi-plus"></i>
        </div>
        <div class="fab-menu" id="fabMenu">
            <a href="{{ route('operative-invoices.create') }}" class="fab-option" title="Create Invoice">
                <i class="bi bi-receipt"></i>
                <span class="fab-label">Invoice</span>
            </a>
            <a href="{{ route('tasks.create') }}" class="fab-option" title="Create Task">
                <i class="bi bi-check-square"></i>
                <span class="fab-label">Task</span>
            </a>
            <a href="#" class="fab-option" onclick="window.scrollTo({top: 0, behavior: 'smooth'})" title="Scroll to Top">
                <i class="bi bi-arrow-up"></i>
                <span class="fab-label">Top</span>
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
    .page-header-section {
        margin-bottom: 2rem;
    }

    .page-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1a202c;
    }

    .page-subtitle {
        font-size: 1rem;
        color: #64748b;
    }

    .date-info {
        text-align: right;
    }

    .current-date {
        font-weight: 600;
        color: #374151;
        font-size: 0.875rem;
    }

    .current-time {
        color: #6b7280;
        font-size: 0.875rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        transition: all 0.2s ease;
    }

    .stat-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.25rem;
    }

    .stat-content {
        flex: 1;
    }

    .stat-number {
        font-size: 1.875rem;
        font-weight: 700;
        color: #1f2937;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    .nav-tabs .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        color: #6b7280;
        font-weight: 500;
        padding: 1rem 1.5rem;
        transition: all 0.2s ease;
    }

    .nav-tabs .nav-link:hover {
        border-color: transparent;
        color: #374151;
        background-color: #f9fafb;
    }

    .nav-tabs .nav-link.active {
        color: #667eea;
        border-bottom-color: #667eea;
        background-color: transparent;
    }

    .task-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.2s ease;
        height: 100%;
    }

    .task-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .task-card.completed {
        opacity: 0.7;
        background-color: #f9fafb;
    }

    .task-title {
        font-weight: 600;
        color: #1f2937;
        font-size: 0.95rem;
    }

    .task-description {
        color: #6b7280;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    .task-project {
        font-size: 0.8rem;
    }

    .badge-sm {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }

    .report-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        height: 100%;
    }

    .report-header {
        background: #f9fafb;
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .report-title {
        margin: 0;
        font-weight: 600;
        color: #1f2937;
    }

    .report-body {
        padding: 1rem;
    }

    .report-stat {
        text-align: center;
    }

    .report-stat .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
    }

    .report-stat .stat-label {
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    .activity-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        font-size: 1rem;
    }

    .activity-content {
        flex: 1;
    }

    .activity-title {
        font-weight: 600;
        color: #1f2937;
        font-size: 0.875rem;
    }

    .activity-description {
        color: #6b7280;
        font-size: 0.8rem;
        margin-top: 0.25rem;
    }

    .activity-time {
        color: #9ca3af;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    .activity-amount {
        font-size: 0.875rem;
        color: #059669;
    }

    .avatar-sm {
        width: 32px;
        height: 32px;
    }

    .avatar-title {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.8rem;
    }

    /* Mobile-specific styles */
    .mobile-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }

    .mobile-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .card-header-mobile {
        background: #f8fafc;
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .card-body-mobile {
        padding: 1rem;
    }

    .info-item {
        margin-bottom: 0.5rem;
    }

    .avatar-xs {
        width: 24px;
        height: 24px;
    }

    .avatar-xs .avatar-title {
        font-size: 0.7rem;
        font-weight: 600;
    }

    /* Mobile tabs */
    .mobile-tabs {
        border-bottom: 1px solid #e5e7eb;
    }

    .mobile-tabs .nav-item {
        flex: 1;
    }

    .mobile-tabs .nav-link {
        border: none;
        border-bottom: 2px solid transparent;
        text-align: center;
        padding: 0.75rem 0.5rem;
        font-size: 0.8rem;
        font-weight: 500;
        color: #6b7280;
        transition: all 0.2s ease;
    }

    .mobile-tabs .nav-link:hover {
        background-color: #f9fafb;
        color: #374151;
    }

    .mobile-tabs .nav-link.active {
        color: #667eea;
        border-bottom-color: #667eea;
        background-color: transparent;
    }

    /* Floating Action Button */
    .fab-container {
        position: fixed;
        bottom: 100px; /* Position above bottom tab bar */
        right: 20px;
        z-index: 999; /* Below tab bar but above content */
    }

    .fab-main {
        width: 56px;
        height: 56px;
        background: #667eea;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        transition: all 0.3s ease;
        position: relative;
        z-index: 1001;
    }

    .fab-main:hover {
        background: #5a67d8;
        transform: scale(1.1);
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.5);
    }

    .fab-main.active {
        transform: rotate(45deg);
    }

    .fab-menu {
        position: absolute;
        bottom: 70px;
        right: 0;
        display: flex;
        flex-direction: column;
        gap: 12px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px);
        transition: all 0.3s ease;
    }

    .fab-menu.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .fab-option {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        text-decoration: none;
        color: white;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .fab-option i {
        width: 44px;
        height: 44px;
        background: #4f46e5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3);
        transition: all 0.2s ease;
    }

    .fab-option:hover i {
        background: #4338ca;
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
    }

    .fab-label {
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        white-space: nowrap;
        opacity: 0;
        transform: translateX(10px);
        transition: all 0.2s ease;
    }

    .fab-option:hover .fab-label {
        opacity: 1;
        transform: translateX(0);
    }

    /* Mobile Content Area */
    .mobile-content {
        padding-bottom: 80px; /* Space for bottom tab bar */
    }

    .mobile-tab-content {
        padding: 1rem;
        min-height: calc(100vh - 200px);
    }

    /* Native App Bottom Tab Bar */
    .bottom-tab-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        z-index: 1000;
        padding: 0;
        box-shadow: 0 -2px 20px rgba(0, 0, 0, 0.1);
    }

    .tab-bar-content {
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 8px 0;
        max-width: 100%;
    }

    .bottom-tab {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: none;
        border: none;
        color: #8e8e93;
        font-size: 10px;
        font-weight: 500;
        padding: 6px 12px;
        min-width: 60px;
        transition: all 0.2s ease;
        cursor: pointer;
        text-decoration: none;
        position: relative;
    }

    .bottom-tab i {
        font-size: 20px;
        margin-bottom: 2px;
        transition: all 0.2s ease;
    }

    .bottom-tab span {
        font-size: 10px;
        line-height: 1;
        transition: all 0.2s ease;
    }

    .bottom-tab:hover {
        color: #007AFF;
        transform: scale(1.05);
    }

    .bottom-tab.active {
        color: #007AFF;
        font-weight: 600;
    }

    .bottom-tab.active i {
        transform: scale(1.1);
    }

    /* iOS-style active indicator */
    .bottom-tab.active::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 24px;
        height: 2px;
        background: #007AFF;
        border-radius: 2px;
    }

    /* Android-style ripple effect */
    .bottom-tab::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(0, 122, 255, 0.2);
        transform: translate(-50%, -50%);
        transition: width 0.3s ease, height 0.3s ease;
    }

    .bottom-tab:active::after {
        width: 40px;
        height: 40px;
    }

    /* Safe area for devices with home indicator */
    @supports (padding-bottom: env(safe-area-inset-bottom)) {
        .bottom-tab-bar {
            padding-bottom: env(safe-area-inset-bottom);
        }
        
        .mobile-content {
            padding-bottom: calc(80px + env(safe-area-inset-bottom));
        }
        
        .fab-container {
            bottom: calc(100px + env(safe-area-inset-bottom));
        }
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 8px !important;
            padding-right: 8px !important;
        }
        
        .page-title {
            font-size: 1.25rem;
        }
        
        .stat-card {
            padding: 0.75rem;
        }
        
        .stat-number {
            font-size: 1.25rem;
        }
        
        .stat-label {
            font-size: 0.75rem;
        }
        
        .mobile-tabs .nav-link {
            padding: 0.5rem 0.25rem;
            font-size: 0.7rem;
        }
        
        .card-header-mobile,
        .card-body-mobile {
            padding: 0.75rem;
        }
    }

    @media (max-width: 768px) {
        .page-title {
            font-size: 1.5rem;
        }
        
        .stat-card {
            padding: 1rem;
        }
        
        .stat-number {
            font-size: 1.5rem;
        }
        
        .nav-tabs .nav-link {
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
        }
        
        body {
            padding-bottom: 160px; /* Space for bottom tab bar + FAB */
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle tab switching with URL updates for desktop
    const desktopTabs = document.querySelectorAll('#operativeTabsDesktop button[data-bs-toggle="tab"]');
    
    desktopTabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            const targetId = e.target.getAttribute('data-bs-target').substring(1);
            const url = new URL(window.location);
            url.searchParams.set('tab', targetId.replace('-mobile', ''));
            window.history.pushState({}, '', url);
        });
    });

    // Handle bottom tab bar navigation for mobile
    const bottomTabs = document.querySelectorAll('.bottom-tab[data-bs-toggle="tab"]');
    
    bottomTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all bottom tabs
            bottomTabs.forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Hide all mobile tab content
            const allMobileTabPanes = document.querySelectorAll('#operativeTabsContentMobile .tab-pane');
            allMobileTabPanes.forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // Show target tab content
            const targetId = this.getAttribute('data-bs-target');
            const targetPane = document.querySelector(targetId);
            if (targetPane) {
                targetPane.classList.add('show', 'active');
            }
            
            // Update URL
            const tabName = targetId.replace('#', '').replace('-mobile', '');
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.pushState({}, '', url);
            
            // Add haptic feedback on supported devices
            if (navigator.vibrate) {
                navigator.vibrate(10);
            }
        });
    });

    // Floating Action Button functionality
    const fabMain = document.getElementById('fabMain');
    const fabMenu = document.getElementById('fabMenu');
    
    if (fabMain && fabMenu) {
        fabMain.addEventListener('click', function() {
            fabMain.classList.toggle('active');
            fabMenu.classList.toggle('active');
        });

        // Close FAB menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!fabMain.contains(e.target) && !fabMenu.contains(e.target)) {
                fabMain.classList.remove('active');
                fabMenu.classList.remove('active');
            }
        });

        // Close FAB menu when scrolling
        let ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(function() {
                    fabMain.classList.remove('active');
                    fabMenu.classList.remove('active');
                    ticking = false;
                });
                ticking = true;
            }
        });
    }

    // Update time every minute
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit',
            hour12: true 
        });
        const timeElement = document.querySelector('.current-time');
        if (timeElement) {
            timeElement.textContent = timeString;
        }
    }

    setInterval(updateTime, 60000); // Update every minute

    // Touch-friendly enhancements
    if ('ontouchstart' in window) {
        // Add touch feedback to buttons
        const buttons = document.querySelectorAll('.btn, .nav-link, .stat-card, .mobile-card');
        buttons.forEach(button => {
            button.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.98)';
            });
            
            button.addEventListener('touchend', function() {
                this.style.transform = '';
            });
        });

        // Prevent double-tap zoom on buttons
        const actionButtons = document.querySelectorAll('.btn');
        actionButtons.forEach(button => {
            button.addEventListener('touchend', function(e) {
                e.preventDefault();
                button.click();
            });
        });
    }

    // Auto-hide FAB when scrolling down, show when scrolling up
    let lastScrollTop = 0;
    const fabContainer = document.querySelector('.fab-container');
    
    if (fabContainer) {
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                // Scrolling down
                fabContainer.style.transform = 'translateY(100px)';
                fabContainer.style.opacity = '0';
            } else {
                // Scrolling up
                fabContainer.style.transform = 'translateY(0)';
                fabContainer.style.opacity = '1';
            }
            
            lastScrollTop = scrollTop;
        }, false);
    }

    // Time tracking functionality
    window.clockIn = function() {
        const form = document.getElementById('clockInForm');
        const button = form.querySelector('button');
        const originalText = button.innerHTML;
        const projectSelect = form.querySelector('#project_id');
        
        // Validate project selection
        if (!projectSelect.value) {
            showNotification('Please select a project before clocking in.', 'error');
            projectSelect.focus();
            return;
        }
        
        // Show location loading state
        button.disabled = true;
        button.innerHTML = '<i class="bi bi-geo-alt me-2"></i>Getting Location...';
        
        // Get user's current location
        getCurrentLocation()
            .then(position => {
                button.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Clocking In...';
                
                const formData = new FormData(form);
                formData.append('operative_latitude', position.coords.latitude);
                formData.append('operative_longitude', position.coords.longitude);
                
                return fetch('{{ route("operative.clock-in") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let message = data.message;
                    if (data.distance) {
                        message += ` (Distance: ${data.distance}m from project)`;
                    }
                    showNotification(message, 'success');
                    
                    // Reload page to show updated state
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    if (data.location_error && data.distance) {
                        showLocationErrorModal(data.message, data.distance, data.max_distance);
                    } else {
                        throw new Error(data.message || 'Failed to clock in');
                    }
                }
            })
            .catch(error => {
                console.error('Clock in error:', error);
                if (error.message.includes('location')) {
                    showNotification('Location access is required to clock in. Please enable location permissions and try again.', 'error');
                } else {
                    showNotification(error.message || 'Failed to clock in. Please try again.', 'error');
                }
                
                // Restore button
                button.disabled = false;
                button.innerHTML = originalText;
            });
    };

    window.clockInMobile = function() {
        const form = document.getElementById('mobileClockInForm');
        const button = form.querySelector('button');
        const originalText = button.innerHTML;
        const projectSelect = form.querySelector('#mobile_project_id');
        
        // Validate project selection
        if (!projectSelect.value) {
            showNotification('Please select a project before clocking in.', 'error');
            projectSelect.focus();
            return;
        }
        
        // Show location loading state
        button.disabled = true;
        button.innerHTML = '<i class="bi bi-geo-alt me-2"></i>Getting Location...';
        
        // Get user's current location
        getCurrentLocation()
            .then(position => {
                button.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Clocking In...';
                
                const formData = new FormData(form);
                formData.append('operative_latitude', position.coords.latitude);
                formData.append('operative_longitude', position.coords.longitude);
                
                return fetch('{{ route("operative.clock-in") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let message = data.message;
                    if (data.distance) {
                        message += ` (Distance: ${data.distance}m from project)`;
                    }
                    showNotification(message, 'success');
                    
                    // Reload page to show updated state
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    if (data.location_error && data.distance) {
                        showLocationErrorModal(data.message, data.distance, data.max_distance);
                    } else {
                        throw new Error(data.message || 'Failed to clock in');
                    }
                }
            })
            .catch(error => {
                console.error('Clock in error:', error);
                if (error.message.includes('location')) {
                    showNotification('Location access is required to clock in. Please enable location permissions and try again.', 'error');
                } else {
                    showNotification(error.message || 'Failed to clock in. Please try again.', 'error');
                }
                
                // Restore button
                button.disabled = false;
                button.innerHTML = originalText;
            });
    };

    window.clockOut = function() {
        if (!confirm('Are you sure you want to clock out?')) {
            return;
        }

        const notes = prompt('Any additional notes for your work session? (Optional)');
        
        // Show loading state
        const buttons = document.querySelectorAll('button[onclick="clockOut()"]');
        buttons.forEach(button => {
            button.disabled = true;
            button.innerHTML = '<i class="bi bi-geo-alt me-2"></i>Getting Location...';
        });
        
        // Get user's current location
        getCurrentLocation()
            .then(position => {
                buttons.forEach(button => {
                    button.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Clocking Out...';
                });
                
                const formData = new FormData();
                if (notes) {
                    formData.append('notes', notes);
                }
                formData.append('operative_latitude', position.coords.latitude);
                formData.append('operative_longitude', position.coords.longitude);
                
                return fetch('{{ route("operative.clock-out") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message with duration
                    showNotification(`${data.message} Duration: ${data.duration}`, 'success');
                    
                    // Reload page to show updated state
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    if (data.location_error && data.distance) {
                        showLocationErrorModal(data.message, data.distance, data.max_distance);
                    } else {
                        throw new Error(data.message || 'Failed to clock out');
                    }
                }
            })
            .catch(error => {
                console.error('Clock out error:', error);
                if (error.message.includes('location')) {
                    showNotification('Location access is required to clock out. Please enable location permissions and try again.', 'error');
                } else {
                    showNotification(error.message || 'Failed to clock out. Please try again.', 'error');
                }
                
                // Restore buttons
                buttons.forEach(button => {
                    button.disabled = false;
                    button.innerHTML = '<i class="bi bi-stop-circle me-2"></i>Clock Out';
                });
            });
    };

    // Update current time every second
    function updateCurrentTime() {
        const timeElement = document.getElementById('currentTime');
        if (timeElement) {
            const now = new Date();
            timeElement.textContent = now.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
        }
    }

    // Update duration for active time entries
    function updateDuration() {
        const durationElements = document.querySelectorAll('#currentDuration, #mobileDuration');
        @if($activeTimeEntry)
            const clockInTime = new Date('{{ $activeTimeEntry->clock_in->toISOString() }}');
            const now = new Date();
            const diffInSeconds = Math.floor((now - clockInTime) / 1000);
            
            const hours = Math.floor(diffInSeconds / 3600);
            const minutes = Math.floor((diffInSeconds % 3600) / 60);
            const seconds = diffInSeconds % 60;
            
            const formattedDuration = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            durationElements.forEach(element => {
                if (element) {
                    element.textContent = formattedDuration;
                }
            });
        @endif
    }

    // Notification function
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    // Location functions
    function getCurrentLocation() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject(new Error('Geolocation is not supported by this browser.'));
                return;
            }

            const options = {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 300000 // 5 minutes
            };

            navigator.geolocation.getCurrentPosition(resolve, (error) => {
                let errorMessage = 'Unable to get your location. ';
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += 'Please enable location permissions for this website.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += 'Location information is unavailable.';
                        break;
                    case error.TIMEOUT:
                        errorMessage += 'Location request timed out. Please try again.';
                        break;
                    default:
                        errorMessage += 'An unknown error occurred.';
                        break;
                }
                reject(new Error(errorMessage));
            }, options);
        });
    }

    // Show location error modal
    function showLocationErrorModal(message, distance, maxDistance) {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-geo-alt-fill me-2"></i>Location Error
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="mb-3">You're too far from the project site</h6>
                        <p class="mb-3">${message}</p>
                        <div class="alert alert-info">
                            <strong>Current Distance:</strong> ${distance}m<br>
                            <strong>Required:</strong> Within ${maxDistance}m
                        </div>
                        <p class="text-muted small">
                            Please move closer to the project location and try again.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Close
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        // Remove modal from DOM when hidden
        modal.addEventListener('hidden.bs.modal', () => {
            modal.remove();
        });
    }

    // Start timers
    setInterval(updateCurrentTime, 1000);
    setInterval(updateDuration, 1000);
    
    // Initial updates
    updateCurrentTime();
    updateDuration();
});
</script>
@endpush
@endsection

