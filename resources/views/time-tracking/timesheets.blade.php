@extends('layouts.app')

@section('title', 'Timesheets')

@section('content')
<div class="timesheets-container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('time-tracking.index') }}">Time Tracking</a></li>
                        <li class="breadcrumb-item active">Timesheets</li>
                    </ol>
                </nav>
                <h1 class="page-title">
                    <i class="bi bi-calendar-week me-3 text-primary"></i>Timesheets
                </h1>
                <p class="page-subtitle">Review and manage weekly timesheets</p>
            </div>
            <div class="col-lg-6 text-end">
                <div class="btn-group">
                    <button class="btn btn-success">
                        <i class="bi bi-check-all me-2"></i>Approve Selected
                    </button>
                    <button class="btn btn-outline-primary">
                        <i class="bi bi-download me-2"></i>Export
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="user_id" class="form-label">Filter by User</label>
                    <select name="user_id" id="user_id" class="form-select">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $userFilter == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="week_start" class="form-label">Week Starting</label>
                    <input type="date" name="week_start" id="week_start" class="form-control" value="{{ $weekStart }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-funnel me-2"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Week Navigation -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="week-navigation">
                    <a href="{{ route('time-tracking.timesheets', ['week_start' => $weekStartDate->copy()->subWeek()->toDateString(), 'user_id' => $userFilter]) }}" 
                       class="btn btn-outline-secondary">
                        <i class="bi bi-chevron-left me-2"></i>Previous Week
                    </a>
                </div>
                <div class="week-display text-center">
                    <h4 class="mb-1">{{ $weekStartDate->format('M j') }} - {{ $weekEndDate->format('M j, Y') }}</h4>
                    <p class="text-muted mb-0">Week {{ $weekStartDate->weekOfYear }}</p>
                </div>
                <div class="week-navigation">
                    <a href="{{ route('time-tracking.timesheets', ['week_start' => $weekStartDate->copy()->addWeek()->toDateString(), 'user_id' => $userFilter]) }}" 
                       class="btn btn-outline-secondary">
                        Next Week<i class="bi bi-chevron-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Timesheets -->
    <div class="row g-4">
        @foreach($timesheets as $timesheet)
            <div class="col-12">
                <div class="timesheet-card">
                    <div class="timesheet-header">
                        <div class="user-info">
                            <div class="d-flex align-items-center">
                                <div class="avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                    {{ substr($timesheet->user->name, 0, 2) }}
                                </div>
                                <div>
                                    <h5 class="mb-1">{{ $timesheet->user->name }}</h5>
                                    <p class="text-muted mb-0">{{ $timesheet->user->role }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="timesheet-summary">
                            <div class="row g-3 text-center">
                                <div class="col-3">
                                    <div class="summary-item">
                                        <div class="summary-value text-primary">{{ $timesheet->total_hours }}h</div>
                                        <div class="summary-label">Total Hours</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="summary-item">
                                        <div class="summary-value text-success">{{ $timesheet->regular_hours }}h</div>
                                        <div class="summary-label">Regular</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="summary-item">
                                        <div class="summary-value text-warning">{{ $timesheet->overtime_hours }}h</div>
                                        <div class="summary-label">Overtime</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="summary-item">
                                        <span class="badge bg-{{ $timesheet->status === 'approved' ? 'success' : ($timesheet->status === 'submitted' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($timesheet->status) }}
                                        </span>
                                        <div class="summary-label">Status</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="timesheet-actions">
                            <div class="btn-group btn-group-sm">
                                @if($timesheet->status === 'submitted')
                                    <button class="btn btn-success" onclick="approveTimesheet({{ $timesheet->user->id }})">
                                        <i class="bi bi-check me-1"></i>Approve
                                    </button>
                                    <button class="btn btn-danger" onclick="rejectTimesheet({{ $timesheet->user->id }})">
                                        <i class="bi bi-x me-1"></i>Reject
                                    </button>
                                @endif
                                <button class="btn btn-outline-secondary" onclick="viewTimesheetDetails({{ $timesheet->user->id }})">
                                    <i class="bi bi-eye me-1"></i>View
                                </button>
                                <button class="btn btn-outline-primary">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="timesheet-body">
                        <div class="table-responsive">
                            <table class="table table-sm timesheet-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Day</th>
                                        @foreach($timesheet->days as $day)
                                            <th class="text-center {{ $day->date->isWeekend() ? 'weekend-column' : '' }}">
                                                <div class="day-header">
                                                    <div class="day-name">{{ $day->date->format('D') }}</div>
                                                    <div class="day-date">{{ $day->date->format('j') }}</div>
                                                </div>
                                            </th>
                                        @endforeach
                                        <th class="text-center total-column">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="row-label">Hours</td>
                                        @foreach($timesheet->days as $day)
                                            <td class="text-center {{ $day->date->isWeekend() ? 'weekend-column' : '' }}">
                                                <div class="hours-cell">
                                                    @if($day->hours > 0)
                                                        <span class="hours-value {{ $day->hours > 8 ? 'overtime' : '' }}">
                                                            {{ $day->hours }}h
                                                        </span>
                                                    @else
                                                        <span class="hours-value text-muted">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                        @endforeach
                                        <td class="text-center total-column">
                                            <span class="total-hours">{{ $timesheet->total_hours }}h</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="row-label">Project</td>
                                        @foreach($timesheet->days as $day)
                                            <td class="text-center {{ $day->date->isWeekend() ? 'weekend-column' : '' }}">
                                                @if($day->project)
                                                    <div class="project-cell">
                                                        <small class="text-muted">{{ Str::limit($day->project->name, 15) }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="text-center total-column">-</td>
                                    </tr>
                                    <tr>
                                        <td class="row-label">Notes</td>
                                        @foreach($timesheet->days as $day)
                                            <td class="text-center {{ $day->date->isWeekend() ? 'weekend-column' : '' }}">
                                                @if($day->notes)
                                                    <div class="notes-cell" title="{{ $day->notes }}">
                                                        <i class="bi bi-chat-text text-info"></i>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="text-center total-column">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($timesheets->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-calendar-week text-muted" style="font-size: 4rem;"></i>
                <h4 class="text-muted mt-3">No Timesheets Found</h4>
                <p class="text-muted">No timesheets available for the selected week and filters</p>
            </div>
        </div>
    @endif
</div>

<style>
.timesheets-container {
    max-width: 100%;
}

.timesheet-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: white;
    overflow: hidden;
    margin-bottom: 1.5rem;
}

.timesheet-header {
    display: flex;
    justify-content: between;
    align-items: center;
    padding: 1.5rem;
    background: #f8f9fa;
    border-bottom: 1px solid #e5e7eb;
    gap: 1rem;
}

.avatar-md {
    width: 48px;
    height: 48px;
    font-size: 1rem;
    font-weight: 600;
}

.timesheet-summary {
    flex: 1;
}

.summary-item {
    text-align: center;
}

.summary-value {
    font-weight: 600;
    font-size: 1.125rem;
    display: block;
}

.summary-label {
    font-size: 0.75rem;
    color: #6b7280;
    text-transform: uppercase;
    font-weight: 600;
    margin-top: 0.25rem;
}

.timesheet-actions {
    margin-left: auto;
}

.timesheet-body {
    padding: 0;
}

.timesheet-table {
    margin: 0;
}

.timesheet-table th,
.timesheet-table td {
    border: 1px solid #e5e7eb;
    vertical-align: middle;
    padding: 0.75rem 0.5rem;
}

.day-header {
    text-align: center;
}

.day-name {
    font-weight: 600;
    font-size: 0.875rem;
}

.day-date {
    font-size: 0.75rem;
    color: #6b7280;
}

.weekend-column {
    background: #f3f4f6;
}

.total-column {
    background: #e5e7eb;
    font-weight: 600;
}

.row-label {
    background: #f8f9fa;
    font-weight: 600;
    min-width: 80px;
}

.hours-cell {
    min-height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hours-value {
    font-weight: 600;
}

.hours-value.overtime {
    color: #f59e0b;
}

.project-cell {
    font-size: 0.75rem;
}

.notes-cell {
    cursor: pointer;
}

.total-hours {
    font-weight: 700;
    font-size: 1.125rem;
}

.week-display h4 {
    color: #1f2937;
}

@media (max-width: 768px) {
    .timesheet-header {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    
    .timesheet-actions {
        margin-left: 0;
        text-align: center;
    }
    
    .timesheet-table {
        font-size: 0.75rem;
    }
    
    .timesheet-table th,
    .timesheet-table td {
        padding: 0.5rem 0.25rem;
    }
}

@media (max-width: 576px) {
    .week-navigation {
        text-align: center;
        margin-bottom: 1rem;
    }
    
    .week-display {
        order: -1;
        margin-bottom: 1rem;
    }
}
</style>

<script>
function approveTimesheet(userId) {
    if (confirm('Are you sure you want to approve this timesheet?')) {
        // Mock approval - in real app, this would make an AJAX call
        alert('Timesheet approved successfully');
        window.location.reload();
    }
}

function rejectTimesheet(userId) {
    const reason = prompt('Please provide a reason for rejection:');
    if (reason) {
        // Mock rejection - in real app, this would make an AJAX call
        alert('Timesheet rejected: ' + reason);
        window.location.reload();
    }
}

function viewTimesheetDetails(userId) {
    // Mock view details - in real app, this would open a detailed modal or page
    alert('Opening timesheet details for user ID: ' + userId);
}
</script>
@endsection
