@extends('layouts.superadmin')

@section('title', 'Platform Analytics')
@section('page-title', 'Platform Analytics')

@section('content')
<div class="mb-4">
    <p class="text-muted mb-0">Comprehensive platform analytics and insights</p>
</div>

<!-- Coming Soon Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="bi bi-graph-up text-primary mb-4" style="font-size: 4rem;"></i>
        <h3 class="mb-3">Analytics Dashboard</h3>
        <p class="text-muted mb-4">
            Advanced analytics and reporting features are coming soon. This will include:
        </p>
        
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 bg-light">
                    <div class="card-body text-center py-3">
                        <i class="bi bi-bar-chart text-primary mb-2"></i>
                        <h6 class="mb-0">Revenue Analytics</h6>
                        <small class="text-muted">Track subscription revenue and growth</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-light">
                    <div class="card-body text-center py-3">
                        <i class="bi bi-people text-success mb-2"></i>
                        <h6 class="mb-0">User Metrics</h6>
                        <small class="text-muted">Monitor user engagement and activity</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-light">
                    <div class="card-body text-center py-3">
                        <i class="bi bi-buildings text-info mb-2"></i>
                        <h6 class="mb-0">Company Insights</h6>
                        <small class="text-muted">Analyze company usage patterns</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 bg-light">
                    <div class="card-body text-center py-3">
                        <i class="bi bi-graph-down text-warning mb-2"></i>
                        <h6 class="mb-0">Churn Analysis</h6>
                        <small class="text-muted">Identify and prevent customer churn</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Development in Progress:</strong> This feature is currently being developed and will be available in the next update.
        </div>
        
        <a href="{{ route('dashboard') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>
</div>
@endsection
