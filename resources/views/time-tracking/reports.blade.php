@extends('layouts.app')

@section('title', 'Time Tracking Reports')

@section('content')
<div class="time-reports-container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('time-tracking.index') }}">Time Tracking</a></li>
                        <li class="breadcrumb-item active">Reports</li>
                    </ol>
                </nav>
                <h1 class="page-title">
                    <i class="bi bi-graph-up me-3 text-info"></i>Time Tracking Reports
                </h1>
                <p class="page-subtitle">Generate comprehensive time and productivity reports</p>
            </div>
            <div class="col-lg-6 text-end">
                <div class="btn-group">
                    <button class="btn btn-primary">
                        <i class="bi bi-download me-2"></i>Export Report
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="bi bi-printer me-2"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="report_type" class="form-label">Report Type</label>
                    <select name="report_type" id="report_type" class="form-select">
                        <option value="summary" {{ $reportType == 'summary' ? 'selected' : '' }}>Summary Report</option>
                        <option value="detailed" {{ $reportType == 'detailed' ? 'selected' : '' }}>Detailed Report</option>
                        <option value="project" {{ $reportType == 'project' ? 'selected' : '' }}>Project Report</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-arrow-clockwise me-2"></i>Generate Report
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Content -->
    @if($reportType === 'summary')
        <!-- Summary Report -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-bar-chart me-2"></i>Summary Report
                    <span class="text-muted">{{ \Carbon\Carbon::parse($startDate)->format('M j') }} - {{ \Carbon\Carbon::parse($endDate)->format('M j, Y') }}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4 mb-4">
                    <div class="col-md-2">
                        <div class="metric-card">
                            <div class="metric-icon bg-primary">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div class="metric-content">
                                <h3>{{ number_format($reportData['total_hours']) }}</h3>
                                <p>Total Hours</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="metric-card">
                            <div class="metric-icon bg-success">
                                <i class="bi bi-currency-pound"></i>
                            </div>
                            <div class="metric-content">
                                <h3>{{ number_format($reportData['billable_hours']) }}</h3>
                                <p>Billable Hours</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="metric-card">
                            <div class="metric-icon bg-info">
                                <i class="bi bi-cash"></i>
                            </div>
                            <div class="metric-content">
                                <h3>£{{ number_format($reportData['total_cost']) }}</h3>
                                <p>Total Cost</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="metric-card">
                            <div class="metric-icon bg-warning">
                                <i class="bi bi-graph-up"></i>
                            </div>
                            <div class="metric-content">
                                <h3>£{{ number_format($reportData['average_hourly_rate']) }}</h3>
                                <p>Avg Hourly Rate</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="metric-card">
                            <div class="metric-icon bg-secondary">
                                <i class="bi bi-building"></i>
                            </div>
                            <div class="metric-content">
                                <h3>{{ $reportData['projects_count'] }}</h3>
                                <p>Projects</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="metric-card">
                            <div class="metric-icon bg-danger">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="metric-content">
                                <h3>{{ $reportData['users_count'] }}</h3>
                                <p>Team Members</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Charts Placeholder -->
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="chart-placeholder">
                            <h6>Hours by Week</h6>
                            <div class="chart-mock">
                                <div class="chart-bars">
                                    <div class="chart-bar" style="height: 60%"></div>
                                    <div class="chart-bar" style="height: 80%"></div>
                                    <div class="chart-bar" style="height: 45%"></div>
                                    <div class="chart-bar" style="height: 90%"></div>
                                    <div class="chart-bar" style="height: 70%"></div>
                                </div>
                                <p class="text-muted small mt-2">Weekly time tracking trends</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-placeholder">
                            <h6>Billable vs Non-Billable</h6>
                            <div class="chart-mock">
                                <div class="pie-chart">
                                    <div class="pie-slice pie-slice-1"></div>
                                    <div class="pie-slice pie-slice-2"></div>
                                </div>
                                <div class="pie-legend">
                                    <div class="legend-item">
                                        <span class="legend-color bg-success"></span>
                                        <span>Billable (75%)</span>
                                    </div>
                                    <div class="legend-item">
                                        <span class="legend-color bg-secondary"></span>
                                        <span>Non-Billable (25%)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @elseif($reportType === 'detailed')
        <!-- Detailed Report -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-list-ul me-2"></i>Detailed Time Entries
                    <span class="text-muted">{{ \Carbon\Carbon::parse($startDate)->format('M j') }} - {{ \Carbon\Carbon::parse($endDate)->format('M j, Y') }}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Project</th>
                                <th>Task</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Duration</th>
                                <th>Billable</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportData->take(20) as $entry)
                                <tr>
                                    <td>{{ $entry->clock_in->format('M j, Y') }}</td>
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
                                    <td>{{ $entry->clock_in->format('g:i A') }}</td>
                                    <td>{{ $entry->clock_out ? $entry->clock_out->format('g:i A') : '-' }}</td>
                                    <td><strong>{{ gmdate('H:i', $entry->duration) }}</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $entry->is_billable ? 'success' : 'secondary' }}">
                                            {{ $entry->is_billable ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $entry->status === 'approved' ? 'success' : ($entry->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($entry->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @elseif($reportType === 'project')
        <!-- Project Report -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-building me-2"></i>Project Time Report
                    <span class="text-muted">{{ \Carbon\Carbon::parse($startDate)->format('M j') }} - {{ \Carbon\Carbon::parse($endDate)->format('M j, Y') }}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    @foreach($reportData as $project)
                        <div class="col-lg-6">
                            <div class="project-report-card">
                                <div class="project-header">
                                    <h6 class="project-name">{{ $project->project->name }}</h6>
                                    <span class="completion-badge">{{ $project->completion_rate }}% Complete</span>
                                </div>
                                
                                <div class="project-metrics">
                                    <div class="row g-3 text-center">
                                        <div class="col-3">
                                            <div class="metric-item">
                                                <div class="metric-value text-primary">{{ $project->total_hours }}h</div>
                                                <div class="metric-label">Total</div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="metric-item">
                                                <div class="metric-value text-success">{{ $project->billable_hours }}h</div>
                                                <div class="metric-label">Billable</div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="metric-item">
                                                <div class="metric-value text-info">{{ $project->team_members }}</div>
                                                <div class="metric-label">Team</div>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="metric-item">
                                                <div class="metric-value text-warning">£{{ number_format($project->total_hours * 35) }}</div>
                                                <div class="metric-label">Cost</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="project-progress">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small text-muted">Progress</span>
                                        <span class="small fw-bold">{{ $project->completion_rate }}%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-primary" style="width: {{ $project->completion_rate }}%"></div>
                                    </div>
                                </div>

                                <div class="project-efficiency">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="small text-muted">Efficiency Rate</span>
                                        <span class="small fw-bold text-{{ $project->billable_hours / $project->total_hours * 100 > 80 ? 'success' : 'warning' }}">
                                            {{ round($project->billable_hours / $project->total_hours * 100) }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.time-reports-container {
    max-width: 100%;
}

.metric-card {
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    height: 100%;
    text-align: center;
    transition: transform 0.2s ease;
}

.metric-card:hover {
    transform: translateY(-2px);
}

.metric-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.25rem;
    color: white;
}

.metric-content h3 {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0 0 0.25rem 0;
    color: #1f2937;
}

.metric-content p {
    margin: 0;
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

.chart-placeholder {
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    height: 250px;
}

.chart-placeholder h6 {
    margin-bottom: 1rem;
    color: #1f2937;
    font-weight: 600;
}

.chart-mock {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 180px;
}

.chart-bars {
    display: flex;
    align-items: end;
    gap: 8px;
    height: 100px;
    margin-bottom: 1rem;
}

.chart-bar {
    width: 30px;
    background: linear-gradient(to top, #3b82f6, #60a5fa);
    border-radius: 2px 2px 0 0;
    opacity: 0.8;
}

.pie-chart {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: conic-gradient(#10b981 0deg 270deg, #6b7280 270deg 360deg);
    margin-bottom: 1rem;
}

.pie-legend {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 2px;
}

.avatar-sm {
    width: 24px;
    height: 24px;
    font-size: 11px;
    font-weight: 600;
}

.project-report-card {
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    height: 100%;
}

.project-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.project-name {
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

.completion-badge {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.25rem 0.75rem;
    background: #e0f2fe;
    color: #0277bd;
    border-radius: 12px;
}

.project-metrics {
    margin-bottom: 1.5rem;
}

.metric-item {
    text-align: center;
}

.metric-value {
    font-weight: 600;
    font-size: 1rem;
    display: block;
}

.metric-label {
    font-size: 0.75rem;
    color: #6b7280;
    text-transform: uppercase;
    font-weight: 600;
    margin-top: 0.25rem;
}

.project-progress {
    margin-bottom: 1rem;
}

.project-efficiency {
    padding-top: 1rem;
    border-top: 1px solid #f3f4f6;
}

@media (max-width: 768px) {
    .metric-card {
        margin-bottom: 1rem;
    }
    
    .chart-placeholder {
        height: 200px;
    }
    
    .chart-mock {
        height: 130px;
    }
}
</style>
@endsection
