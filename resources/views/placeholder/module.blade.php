@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="placeholder-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="page-title">
                    <i class="{{ $icon }} me-3"></i>{{ $title }}
                    <span class="badge bg-{{ $badge_color }} ms-2">{{ $badge }}</span>
                </h1>
                <p class="page-subtitle">{{ $description }}</p>
            </div>
            <div class="col-lg-4 text-end">
                <div class="btn-group">
                    <button class="btn btn-primary" disabled>
                        <i class="bi bi-play-circle me-2"></i>Coming Soon
                    </button>
                    <button class="btn btn-outline-secondary" disabled>
                        <i class="bi bi-gear me-2"></i>Configure
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column - Features -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-star me-2"></i>Key Features
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        @foreach(array_chunk($features, ceil(count($features) / 2)) as $chunk)
                            <div class="col-md-6">
                                @foreach($chunk as $feature)
                                    <div class="feature-item">
                                        <div class="feature-icon">
                                            <i class="bi bi-check-circle text-success"></i>
                                        </div>
                                        <div class="feature-content">
                                            <h6>{{ $feature }}</h6>
                                            <small class="text-muted">Advanced functionality for professional project management</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="bi bi-eye me-2"></i>Preview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="preview-mockup">
                        <div class="mockup-header">
                            <div class="mockup-controls">
                                <span class="control"></span>
                                <span class="control"></span>
                                <span class="control"></span>
                            </div>
                            <div class="mockup-title">{{ $title }} Interface</div>
                        </div>
                        <div class="mockup-content">
                            <div class="mockup-sidebar">
                                <div class="mockup-nav-item active"></div>
                                <div class="mockup-nav-item"></div>
                                <div class="mockup-nav-item"></div>
                                <div class="mockup-nav-item"></div>
                            </div>
                            <div class="mockup-main">
                                <div class="mockup-toolbar">
                                    <div class="mockup-button"></div>
                                    <div class="mockup-button"></div>
                                    <div class="mockup-search"></div>
                                </div>
                                <div class="mockup-chart">
                                    <div class="chart-bar" style="height: 60%"></div>
                                    <div class="chart-bar" style="height: 80%"></div>
                                    <div class="chart-bar" style="height: 45%"></div>
                                    <div class="chart-bar" style="height: 90%"></div>
                                    <div class="chart-bar" style="height: 70%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Info -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="bi bi-info-circle me-2"></i>Development Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="status-item">
                        <div class="status-indicator bg-warning"></div>
                        <div class="status-content">
                            <h6>In Development</h6>
                            <small class="text-muted">This module is currently being developed and will be available soon.</small>
                        </div>
                    </div>
                    
                    <div class="progress mt-3 mb-2" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: 75%"></div>
                    </div>
                    <small class="text-muted">75% Complete</small>
                </div>
            </div>

            <!-- Benefits Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="bi bi-award me-2"></i>Benefits
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-arrow-up-circle text-success me-2"></i>
                            Increased Productivity
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-clock text-primary me-2"></i>
                            Time Savings
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-graph-up text-info me-2"></i>
                            Better Insights
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-people text-warning me-2"></i>
                            Team Collaboration
                        </li>
                        <li>
                            <i class="bi bi-shield-check text-success me-2"></i>
                            Enhanced Security
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Contact Card -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="bi bi-envelope me-2"></i>Get Notified
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Want to be the first to know when this module is available?</p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" disabled>
                            <i class="bi bi-bell me-2"></i>Notify Me
                        </button>
                        <button class="btn btn-outline-secondary" disabled>
                            <i class="bi bi-chat-dots me-2"></i>Request Demo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.placeholder-container {
    max-width: 100%;
}

.feature-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.feature-icon {
    flex-shrink: 0;
    margin-top: 0.25rem;
}

.feature-icon i {
    font-size: 1.25rem;
}

.feature-content h6 {
    margin: 0 0 0.25rem 0;
    font-weight: 600;
    color: #1f2937;
}

.preview-mockup {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1rem;
    border: 1px solid #e5e7eb;
}

.mockup-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    background: #ffffff;
    border-radius: 8px 8px 0 0;
    border-bottom: 1px solid #e5e7eb;
}

.mockup-controls {
    display: flex;
    gap: 0.5rem;
}

.control {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #dc3545;
}

.control:nth-child(2) {
    background: #ffc107;
}

.control:nth-child(3) {
    background: #198754;
}

.mockup-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
}

.mockup-content {
    display: flex;
    min-height: 200px;
    background: #ffffff;
    border-radius: 0 0 8px 8px;
}

.mockup-sidebar {
    width: 60px;
    padding: 1rem 0.5rem;
    border-right: 1px solid #e5e7eb;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.mockup-nav-item {
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
}

.mockup-nav-item.active {
    background: #4f46e5;
}

.mockup-main {
    flex: 1;
    padding: 1rem;
}

.mockup-toolbar {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.mockup-button {
    width: 40px;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
}

.mockup-search {
    width: 100px;
    height: 8px;
    background: #f3f4f6;
    border-radius: 4px;
    margin-left: auto;
}

.mockup-chart {
    display: flex;
    align-items: end;
    gap: 8px;
    height: 100px;
}

.chart-bar {
    flex: 1;
    background: linear-gradient(to top, #4f46e5, #7c3aed);
    border-radius: 2px 2px 0 0;
    opacity: 0.8;
}

.status-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-top: 0.25rem;
    flex-shrink: 0;
}

.status-content h6 {
    margin: 0 0 0.25rem 0;
    font-weight: 600;
    color: #1f2937;
}
</style>
@endsection 