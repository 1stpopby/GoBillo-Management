@extends('layouts.app')

@section('title', 'Time Tracking')

@section('content')
<div class="time-tracking-container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="page-title">
                    <i class="bi bi-stopwatch me-3 text-success"></i>Time Tracking
                </h1>
                <p class="page-subtitle">Track work hours, manage timesheets, and monitor productivity</p>
            </div>
            <div class="col-lg-6 text-end">
                <div class="btn-group">
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#clockInOutModal">
                        <i class="bi bi-play-circle me-2"></i>Clock In/Out
                    </button>
                    <a href="{{ route('time-tracking.timesheets') }}" class="btn btn-outline-primary">
                        <i class="bi bi-calendar-week me-2"></i>Timesheets
                    </a>
                    <a href="{{ route('time-tracking.reports') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-graph-up me-2"></i>Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <label for="project_id" class="form-label">Filter by Project</label>
                    <select name="project_id" id="project_id" class="form-select">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ $projectFilter == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_range" class="form-label">Date Range</label>
                    <select name="date_range" id="date_range" class="form-select">
                        <option value="today" {{ $dateFilter == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ $dateFilter == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $dateFilter == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="quarter" {{ $dateFilter == 'quarter' ? 'selected' : '' }}>This Quarter</option>
                    </select>
                </div>
                <div class="col-md-3">
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

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stats-card bg-primary">
                <div class="stats-icon">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ number_format($stats['total_hours']) }}</h3>
                    <p>Total Hours</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stats-card bg-success">
                <div class="stats-icon">
                    <i class="bi bi-currency-pound"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ number_format($stats['billable_hours']) }}</h3>
                    <p>Billable Hours</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stats-card bg-info">
                <div class="stats-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['active_users'] }}</h3>
                    <p>Active Users</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stats-card bg-warning">
                <div class="stats-icon">
                    <i class="bi bi-building"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['projects_tracked'] }}</h3>
                    <p>Projects</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stats-card bg-danger">
                <div class="stats-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['overtime_hours'] }}</h3>
                    <p>Overtime Hours</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stats-card bg-secondary">
                <div class="stats-icon">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $stats['average_daily_hours'] }}</h3>
                    <p>Avg Daily Hours</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Recent Time Entries -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-ul me-2"></i>Recent Time Entries
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentTimeEntries->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Project</th>
                                        <th>Task</th>
                                        <th>Clock In</th>
                                        <th>Clock Out</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTimeEntries->take(10) as $entry)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        {{ substr($entry->user->name, 0, 1) }}
                                                    </div>
                                                    <span class="small">{{ $entry->user->name }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $entry->project->name }}</td>
                                            <td>{{ $entry->task }}</td>
                                            <td>{{ $entry->clock_in->format('M j, g:i A') }}</td>
                                            <td>
                                                @if($entry->clock_out)
                                                    {{ $entry->clock_out->format('M j, g:i A') }}
                                                @else
                                                    <span class="text-success"><i class="bi bi-circle-fill me-1"></i>Active</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ gmdate('H:i', $entry->duration) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $entry->status === 'approved' ? 'success' : ($entry->status === 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($entry->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i>View</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-check me-2"></i>Approve</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-clock text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No time entries found for the selected filters</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Project Time Breakdown -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pie-chart me-2"></i>Project Time Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    @if($projectTimeBreakdown->count() > 0)
                        @foreach($projectTimeBreakdown as $project)
                            <div class="project-time-item mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">{{ $project->project->name }}</h6>
                                    <span class="fw-bold">{{ $project->total_hours }}h</span>
                                </div>
                                <div class="row g-3 text-center">
                                    <div class="col-3">
                                        <div class="metric-item">
                                            <div class="metric-value text-success">{{ $project->billable_hours }}h</div>
                                            <div class="metric-label">Billable</div>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="metric-item">
                                            <div class="metric-value text-info">{{ $project->team_members }}</div>
                                            <div class="metric-label">Team Members</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="metric-item">
                                            <div class="metric-value text-primary">{{ $project->completion_rate }}%</div>
                                            <div class="metric-label">Completion</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $project->completion_rate }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-pie-chart text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No project time data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Active Timers -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-play-circle me-2"></i>Active Timers
                    </h6>
                </div>
                <div class="card-body">
                    @if($activeTimers->count() > 0)
                        @foreach($activeTimers as $timer)
                            <div class="timer-item mb-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                            {{ substr($timer->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $timer->user->name }}</h6>
                                            <small class="text-muted">{{ $timer->project->name }}</small>
                                            <br>
                                            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $timer->location }}</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="timer-duration fw-bold text-success">{{ gmdate('H:i', $timer->duration) }}</div>
                                        <small class="text-muted">{{ $timer->clock_in->format('g:i A') }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-pause-circle text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2 mb-0">No active timers</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Top Performers -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-trophy me-2"></i>Top Performers
                    </h6>
                </div>
                <div class="card-body">
                    @if($topPerformers->count() > 0)
                        @foreach($topPerformers->take(5) as $index => $performer)
                            <div class="performer-item mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="rank-indicator me-3">
                                        <span class="badge bg-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'primary') }} rounded-pill">
                                            {{ $index + 1 }}
                                        </span>
                                    </div>
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                        {{ substr($performer->user->name, 0, 1) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $performer->user->name }}</h6>
                                        <div class="row g-2 text-center">
                                            <div class="col-6">
                                                <div class="small-metric">
                                                    <div class="small-metric-value">{{ $performer->total_hours }}h</div>
                                                    <div class="small-metric-label">Total</div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="small-metric">
                                                    <div class="small-metric-value text-success">{{ $performer->efficiency_rate }}%</div>
                                                    <div class="small-metric-label">Efficiency</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-trophy text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2 mb-0">No performance data</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Clock In/Out Modal -->
<div class="modal fade" id="clockInOutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Clock In/Out</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="clockInOutForm">
                    <div class="mb-3">
                        <label class="form-label">Action</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="action" id="clock_in" value="clock_in" checked>
                            <label class="btn btn-outline-success" for="clock_in">
                                <i class="bi bi-play-circle me-2"></i>Clock In
                            </label>
                            
                            <input type="radio" class="btn-check" name="action" id="clock_out" value="clock_out">
                            <label class="btn btn-outline-danger" for="clock_out">
                                <i class="bi bi-stop-circle me-2"></i>Clock Out
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="project_id" class="form-label">Project</label>
                        <select class="form-select" name="project_id" id="project_id" required>
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" name="notes" id="notes" rows="3" placeholder="Add any notes about your work..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitClockInOut()">Submit</button>
            </div>
        </div>
    </div>
</div>

<style>
.time-tracking-container {
    max-width: 100%;
}

.stats-card {
    padding: 1.5rem;
    border-radius: 12px;
    color: white;
    text-decoration: none;
    display: block;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transform: translate(30px, -30px);
}

.stats-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    opacity: 0.8;
}

.stats-content h3 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 0.25rem 0;
}

.stats-content p {
    margin: 0;
    font-size: 0.875rem;
    opacity: 0.9;
}

.avatar-sm {
    width: 24px;
    height: 24px;
    font-size: 11px;
    font-weight: 600;
}

.project-time-item {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.metric-item {
    text-align: center;
}

.metric-value {
    font-weight: 600;
    font-size: 1rem;
}

.metric-label {
    font-size: 0.75rem;
    color: #6b7280;
    text-transform: uppercase;
    font-weight: 600;
}

.timer-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.timer-item:last-child {
    border-bottom: none;
}

.timer-duration {
    font-size: 1.125rem;
}

.performer-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.performer-item:last-child {
    border-bottom: none;
}

.small-metric {
    text-align: center;
}

.small-metric-value {
    font-weight: 600;
    font-size: 0.875rem;
}

.small-metric-label {
    font-size: 0.625rem;
    color: #6b7280;
    text-transform: uppercase;
    font-weight: 600;
}

.rank-indicator .badge {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
}
</style>

<script>
function submitClockInOut() {
    const form = document.getElementById('clockInOutForm');
    const formData = new FormData(form);
    
    // Mock AJAX call - in real app, this would call the actual endpoint
    fetch('{{ route("time-tracking.clock-in-out") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert(data.message);
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('clockInOutModal'));
            modal.hide();
            
            // Refresh page to show updated data
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

// Auto-update timer durations every minute
setInterval(function() {
    document.querySelectorAll('.timer-duration').forEach(function(element) {
        // In a real app, this would update the actual timer durations
        // For now, we'll just refresh the page periodically
    });
}, 60000); // Update every minute
</script>
@endsection
