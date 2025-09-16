@extends('layouts.app')

@section('title', 'Safety Management')

@section('content')
<div class="safety-container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('field-operations.index') }}">Field Operations</a></li>
                        <li class="breadcrumb-item active">Safety</li>
                    </ol>
                </nav>
                <h1 class="page-title">
                    <i class="bi bi-shield-exclamation me-3 text-danger"></i>Safety Management
                </h1>
                <p class="page-subtitle">Track incidents, safety reports, and compliance</p>
            </div>
            <div class="col-lg-6 text-end">
                <div class="btn-group">
                    <button class="btn btn-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>Report Incident
                    </button>
                    <button class="btn btn-outline-primary">
                        <i class="bi bi-clipboard-check me-2"></i>Safety Inspection
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Safety Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stats-card bg-success">
                <div class="stats-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $safetyReports->where('status', 'resolved')->count() }}</h3>
                    <p>Resolved</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-warning">
                <div class="stats-icon">
                    <i class="bi bi-search"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $safetyReports->where('status', 'investigating')->count() }}</h3>
                    <p>Investigating</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-danger">
                <div class="stats-icon">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $safetyReports->where('severity', 'high')->count() }}</h3>
                    <p>High Priority</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-primary">
                <div class="stats-icon">
                    <i class="bi bi-shield"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $safetyReports->count() }}</h3>
                    <p>Total Reports</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Safety Reports List -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-list me-2"></i>Safety Reports
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                @foreach($safetyReports as $report)
                    <div class="col-lg-6">
                        <div class="safety-report-card">
                            <div class="report-header">
                                <div class="report-type">
                                    <span class="badge bg-{{ $report->type === 'Near Miss' ? 'warning' : ($report->type === 'Safety Violation' ? 'danger' : 'info') }}">
                                        {{ $report->type }}
                                    </span>
                                    <span class="severity-indicator severity-{{ $report->severity }}">
                                        {{ ucfirst($report->severity) }} Priority
                                    </span>
                                </div>
                                <div class="report-status">
                                    <span class="status-badge status-{{ $report->status }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="report-body">
                                <h6 class="report-description">{{ $report->description }}</h6>
                                
                                <div class="report-details">
                                    <div class="detail-item">
                                        <i class="bi bi-geo-alt text-muted me-2"></i>
                                        <span class="text-muted">{{ $report->location }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="bi bi-person text-muted me-2"></i>
                                        <span class="text-muted">Reported by {{ $report->reported_by }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="bi bi-clock text-muted me-2"></i>
                                        <span class="text-muted">{{ $report->date->diffForHumans() }}</span>
                                    </div>
                                </div>

                                @if($report->actions_taken)
                                    <div class="actions-taken">
                                        <h6 class="actions-title">Actions Taken:</h6>
                                        <p class="actions-text">{{ $report->actions_taken }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="report-footer">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>View
                                    </button>
                                    <button class="btn btn-outline-secondary">
                                        <i class="bi bi-pencil me-1"></i>Edit
                                    </button>
                                    @if($report->status !== 'resolved')
                                        <button class="btn btn-outline-success">
                                            <i class="bi bi-check me-1"></i>Resolve
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($safetyReports->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-shield-check text-muted" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mt-3">No Safety Reports</h4>
                    <p class="text-muted">Great! No safety incidents have been reported</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.safety-container {
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

.safety-report-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: white;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.report-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 1.5rem 1.5rem 1rem;
    border-bottom: 1px solid #f3f4f6;
}

.report-type {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.severity-indicator {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.severity-low {
    background: #d1fae5;
    color: #065f46;
}

.severity-medium {
    background: #fef3c7;
    color: #92400e;
}

.severity-high {
    background: #fee2e2;
    color: #991b1b;
}

.status-badge {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
}

.status-investigating {
    background: #fbbf24;
    color: white;
}

.status-resolved {
    background: #10b981;
    color: white;
}

.report-body {
    padding: 1rem 1.5rem;
    flex: 1;
}

.report-description {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 1rem;
    line-height: 1.5;
}

.report-details {
    margin-bottom: 1rem;
}

.detail-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.actions-taken {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin-top: 1rem;
}

.actions-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #4b5563;
    margin-bottom: 0.5rem;
}

.actions-text {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0;
    line-height: 1.5;
}

.report-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #f3f4f6;
    background: #f8f9fa;
}
</style>
@endsection
