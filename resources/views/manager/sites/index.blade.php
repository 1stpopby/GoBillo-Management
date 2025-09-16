@extends('layouts.app')

@section('title', 'My Sites - Manager Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Operational</li>
                    <li class="breadcrumb-item active">My Sites</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">
                <i class="bi bi-geo-alt text-primary me-2"></i>
                My Sites
            </h1>
            <p class="text-muted mb-0">Sites allocated to your management</p>
        </div>
        <div class="d-flex gap-2">
            <div class="badge bg-info fs-6">
                {{ $sites->total() }} {{ Str::plural('site', $sites->total()) }}
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white border-bottom-0">
            <h6 class="card-title mb-0">
                <i class="bi bi-funnel me-2"></i>Filters
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('manager.sites.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Site name, address, client...">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="planning" {{ request('status') === 'planning' ? 'selected' : '' }}>Planning</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="on_hold" {{ request('status') === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="priority" class="form-label">Priority</label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="">All Priorities</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('manager.sites.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Sites Grid -->
    @if($sites->count() > 0)
        <div class="row">
            @foreach($sites as $site)
                <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <!-- Site Header -->
                        <div class="card-header bg-white border-bottom-0 pb-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title mb-1 fw-bold">
                                        <a href="{{ route('manager.sites.show', $site) }}" class="text-decoration-none text-dark site-name-link">
                                            {{ $site->name }}
                                        </a>
                                    </h6>
                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-building me-1"></i>
                                        {{ $site->client->company_name ?? 'No Client' }}
                                    </p>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <!-- Priority Badge -->
                                    <span class="badge bg-{{ 
                                        $site->priority === 'urgent' ? 'danger' : 
                                        ($site->priority === 'high' ? 'warning' : 
                                        ($site->priority === 'medium' ? 'info' : 'secondary')) 
                                    }} badge-sm">
                                        {{ ucfirst($site->priority) }}
                                    </span>
                                    <!-- Status Badge -->
                                    <span class="badge bg-{{ 
                                        $site->status === 'active' ? 'success' : 
                                        ($site->status === 'completed' ? 'primary' : 
                                        ($site->status === 'on_hold' ? 'warning' : 
                                        ($site->status === 'cancelled' ? 'danger' : 'secondary'))) 
                                    }} badge-sm">
                                        {{ str_replace('_', ' ', ucfirst($site->status)) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Site Body -->
                        <div class="card-body pt-2">
                            <!-- Address -->
                            <div class="mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-geo-alt text-muted me-2 mt-1"></i>
                                    <div class="small text-muted">
                                        {{ $site->address }}<br>
                                        {{ $site->city }}, {{ $site->state }} {{ $site->zip_code }}
                                    </div>
                                </div>
                            </div>

                            <!-- Key Metrics -->
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="border-end">
                                        <div class="fw-bold text-primary">{{ $site->projects->count() }}</div>
                                        <div class="small text-muted">Projects</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border-end">
                                        <div class="fw-bold text-success">
                                            @if($site->total_budget)
                                                £{{ number_format($site->total_budget / 1000, 0) }}k
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                        <div class="small text-muted">Budget</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="fw-bold text-info">
                                        @if($site->start_date && $site->expected_completion_date)
                                            {{ $site->start_date->diffInDays($site->expected_completion_date) }}
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                    <div class="small text-muted">Days</div>
                                </div>
                            </div>

                            <!-- Timeline -->
                            @if($site->start_date || $site->expected_completion_date)
                                <div class="mb-3">
                                    <div class="small text-muted mb-1">Timeline</div>
                                    <div class="d-flex justify-content-between small">
                                        <span class="text-muted">
                                            @if($site->start_date)
                                                <i class="bi bi-calendar-check me-1"></i>
                                                {{ $site->start_date->format('M j, Y') }}
                                            @else
                                                TBD
                                            @endif
                                        </span>
                                        <span class="text-muted">
                                            @if($site->expected_completion_date)
                                                <i class="bi bi-calendar-x me-1"></i>
                                                {{ $site->expected_completion_date->format('M j, Y') }}
                                            @else
                                                TBD
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <!-- Description -->
                            @if($site->description)
                                <div class="mb-3">
                                    <p class="small text-muted mb-0">
                                        {{ Str::limit($site->description, 120) }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Site Footer -->
                        <div class="card-footer bg-white border-top-0 pt-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="small text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    Updated {{ $site->updated_at->diffForHumans() }}
                                </div>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('manager.sites.show', $site) }}" 
                                       class="btn btn-outline-primary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('manager.sites.edit', $site) }}" 
                                       class="btn btn-outline-secondary" title="Edit Site">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $sites->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-5">
            <i class="bi bi-geo-alt display-4 text-muted mb-3"></i>
            <h5 class="text-muted">No Sites Allocated</h5>
            <p class="text-muted">
                @if(request()->hasAny(['search', 'status', 'priority']))
                    No sites match your current filters.
                    <a href="{{ route('manager.sites.index') }}" class="text-decoration-none">Clear filters</a>
                @else
                    You don't have any sites allocated to your management yet.
                @endif
            </p>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.badge-sm {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
}

.card-header {
    background-color: #f8fafc !important;
}

.btn-group-sm .btn {
    border-radius: 0.375rem !important;
    margin-right: 0.25rem;
}

.btn-group-sm .btn:last-child {
    margin-right: 0;
}

.border-end {
    border-right: 1px solid #e5e7eb !important;
}

.site-name-link {
    transition: color 0.2s ease-in-out;
}

.site-name-link:hover {
    color: #3b82f6 !important;
    text-decoration: none !important;
}

.card:hover .site-name-link {
    color: #3b82f6 !important;
}

@media (max-width: 768px) {
    .col-xl-4, .col-lg-6, .col-md-6 {
        margin-bottom: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
@endpush
