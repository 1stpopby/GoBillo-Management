@extends('layouts.app')

@section('title', 'Site Inductions')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center">
                    <div class="header-icon bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                        <i class="bi bi-person-check fs-3"></i>
                    </div>
                    <div>
                        <h1 class="page-title mb-1 fw-bold">Site Inductions</h1>
                        <p class="page-subtitle text-muted mb-0">Manage worker inductions and safety certifications</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-end">
                <a href="{{ route('health-safety.inductions.create') }}" class="btn btn-success btn-lg shadow-sm">
                    <i class="bi bi-plus-circle me-2"></i>New Site Induction
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                            <i class="bi bi-person-check fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Active Inductions</p>
                            <h3 class="mb-0 fw-bold">{{ $inductions->where('status', 'active')->count() }}</h3>
                            <small class="text-success">
                                <i class="bi bi-check-circle"></i>Currently valid
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 text-info rounded-circle p-3 me-3">
                            <i class="bi bi-calendar-week fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">This Month</p>
                            <h3 class="mb-0 fw-bold text-info">{{ $inductions->where('inducted_at', '>=', now()->startOfMonth())->count() }}</h3>
                            <small class="text-muted">New inductions</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-circle p-3 me-3">
                            <i class="bi bi-clock fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Expiring Soon</p>
                            <h3 class="mb-0 fw-bold text-warning">{{ $inductions->where('valid_until', '<=', now()->addDays(30))->where('status', 'active')->count() }}</h3>
                            <small class="text-muted">Within 30 days</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger rounded-circle p-3 me-3">
                            <i class="bi bi-x-circle fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Expired</p>
                            <h3 class="mb-0 fw-bold text-danger">{{ $inductions->where('valid_until', '<', now())->where('status', 'active')->count() }}</h3>
                            <small class="text-muted">Require renewal</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light border-0 py-3">
            <div class="d-flex align-items-center">
                <i class="bi bi-funnel text-success me-2"></i>
                <h5 class="mb-0 fw-semibold">Filter Inductions</h5>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('health-safety.inductions') }}" class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label for="search" class="form-label small text-muted">
                        <i class="bi bi-search me-1"></i>Search Inductees
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Name or company...">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="site_id" class="form-label small text-muted">
                        <i class="bi bi-geo-alt me-1"></i>Site
                    </label>
                    <select class="form-select filter-select" id="site_id" name="site_id">
                        <option value="">All Sites</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>
                                {{ $site->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="status" class="form-label small text-muted">
                        <i class="bi bi-flag me-1"></i>Status
                    </label>
                    <select class="form-select filter-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>✅ Active</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>❌ Expired</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>⏸️ Suspended</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="date_from" class="form-label small text-muted">
                        <i class="bi bi-calendar me-1"></i>From Date
                    </label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-lg-3 col-md-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success shadow-sm">
                            <i class="bi bi-funnel-fill me-1"></i>Apply Filters
                        </button>
                        <a href="{{ route('health-safety.inductions') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i>Clear All
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Inductions List -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-semibold">{{ $inductions->total() }} {{ Str::plural('Induction', $inductions->total()) }} Found</h5>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($inductions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover inductions-table mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 ps-4">
                                    <i class="bi bi-person me-2"></i>Inductee Details
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-building me-2"></i>Company/Role
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-geo-alt me-2"></i>Site
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-calendar me-2"></i>Induction Date
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-calendar-check me-2"></i>Valid Until
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-flag me-2"></i>Status
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-person-badge me-2"></i>Certificate
                                </th>
                                <th class="border-0 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inductions as $induction)
                                <tr class="induction-row">
                                    <td class="ps-4">
                                        <div class="inductee-info d-flex align-items-center">
                                            <div class="inductee-icon-wrapper bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                                <i class="bi bi-person text-success fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-semibold">{{ $induction->inductee_name }}</h6>
                                                @if($induction->inductee_phone)
                                                    <small class="text-muted d-block">
                                                        <i class="bi bi-telephone me-1"></i>{{ $induction->inductee_phone }}
                                                    </small>
                                                @endif
                                                @if($induction->inductee_email)
                                                    <small class="text-muted d-block">
                                                        <i class="bi bi-envelope me-1"></i>{{ $induction->inductee_email }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="company-info">
                                            @if($induction->inductee_company)
                                                <div class="fw-semibold">{{ $induction->inductee_company }}</div>
                                            @endif
                                            @if($induction->inductee_role)
                                                <small class="text-muted">{{ $induction->inductee_role }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="site-info">
                                            @if($induction->site)
                                                <i class="bi bi-geo-alt text-muted me-1"></i>
                                                {{ $induction->site->name }}
                                            @else
                                                <span class="text-muted fst-italic">No site specified</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="date-info">
                                            <div class="fw-bold">{{ $induction->inducted_at->format('M j, Y') }}</div>
                                            <small class="text-muted">{{ $induction->inducted_at->format('g:i A') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="validity-info">
                                            @php
                                                $isExpired = $induction->valid_until < now();
                                                $isExpiringSoon = $induction->valid_until <= now()->addDays(30) && !$isExpired;
                                            @endphp
                                            <div class="fw-bold {{ $isExpired ? 'text-danger' : ($isExpiringSoon ? 'text-warning' : 'text-success') }}">
                                                {{ $induction->valid_until->format('M j, Y') }}
                                            </div>
                                            <small class="{{ $isExpired ? 'text-danger' : ($isExpiringSoon ? 'text-warning' : 'text-muted') }}">
                                                @if($isExpired)
                                                    <i class="bi bi-exclamation-triangle me-1"></i>Expired
                                                @elseif($isExpiringSoon)
                                                    <i class="bi bi-clock me-1"></i>Expires soon
                                                @else
                                                    {{ $induction->valid_until->diffForHumans() }}
                                                @endif
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'active' => 'success',
                                                'expired' => 'danger',
                                                'suspended' => 'warning'
                                            ];
                                            $statusIcons = [
                                                'active' => 'bi-check-circle',
                                                'expired' => 'bi-x-circle',
                                                'suspended' => 'bi-pause-circle'
                                            ];
                                            $color = $statusColors[$induction->status] ?? 'secondary';
                                            $icon = $statusIcons[$induction->status] ?? 'bi-circle';
                                        @endphp
                                        <span class="badge rounded-pill bg-{{ $color }} px-3 py-2">
                                            <i class="bi {{ $icon }} me-1"></i>
                                            {{ ucfirst($induction->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="certificate-info text-center">
                                            <div class="fw-bold text-primary">{{ $induction->certificate_number }}</div>
                                            <small class="text-muted">Certificate #</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('health-safety.inductions.show', $induction) }}">
                                                        <i class="bi bi-eye me-2"></i>View Details
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('health-safety.inductions.certificate', $induction) }}">
                                                        <i class="bi bi-download me-2"></i>Download Certificate
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                @if($induction->status === 'active')
                                                    <li>
                                                        <form action="{{ route('health-safety.inductions.renew', $induction) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to renew this induction for another year?')">
                                                                <i class="bi bi-arrow-clockwise me-2"></i>Renew Induction
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('health-safety.inductions.suspend', $induction) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="dropdown-item text-warning" onclick="return confirm('Are you sure you want to suspend this induction?')">
                                                                <i class="bi bi-pause-circle me-2"></i>Suspend
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                @if($induction->status === 'expired')
                                                    <li>
                                                        <form action="{{ route('health-safety.inductions.renew', $induction) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="dropdown-item text-success" onclick="return confirm('Are you sure you want to renew this expired induction?')">
                                                                <i class="bi bi-arrow-clockwise me-2"></i>Renew
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                @if($induction->status === 'suspended')
                                                    <li>
                                                        <form action="{{ route('health-safety.inductions.reactivate', $induction) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="dropdown-item text-success" onclick="return confirm('Are you sure you want to reactivate this induction?')">
                                                                <i class="bi bi-play-circle me-2"></i>Reactivate
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
                <div class="card-footer bg-white border-0 py-3">
                    <div class="d-flex justify-content-center">
                        {{ $inductions->links() }}
                    </div>
                </div>
            @else
                <div class="empty-state text-center py-5">
                    <div class="empty-icon-wrapper mx-auto mb-4" style="width: 120px; height: 120px;">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center h-100">
                            <i class="bi bi-person-check display-1 text-muted"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-2">No Site Inductions Found</h4>
                    <p class="text-muted mb-4">
                        {{ request()->hasAny(['search', 'site_id', 'status', 'date_from']) 
                            ? 'Try adjusting your filters to find what you\'re looking for.' 
                            : 'Get started by conducting your first site induction.' }}
                    </p>
                    @if(!request()->hasAny(['search', 'site_id', 'status', 'date_from']))
                        <a href="{{ route('health-safety.inductions.create') }}" class="btn btn-success btn-lg shadow-sm">
                            <i class="bi bi-plus-circle me-2"></i>Create Your First Induction
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .page-header {
        margin-bottom: 2rem;
    }

    .header-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
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

    /* Statistics Cards */
    .stat-card {
        transition: transform 0.3s, box-shadow 0.3s;
        border-left: 4px solid transparent;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }

    .stat-card:nth-child(1) { border-left-color: #198754; }
    .stat-card:nth-child(2) { border-left-color: #0dcaf0; }
    .stat-card:nth-child(3) { border-left-color: #ffc107; }
    .stat-card:nth-child(4) { border-left-color: #dc3545; }

    .stat-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Filter styling */
    .filter-select {
        border: 1px solid #dee2e6;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .filter-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.15);
    }

    /* Table styling */
    .inductions-table thead {
        background: linear-gradient(180deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .inductions-table thead th {
        font-weight: 600;
        color: #495057;
        padding: 1rem;
        white-space: nowrap;
    }

    .induction-row {
        transition: background-color 0.2s, transform 0.2s;
    }

    .induction-row:hover {
        background-color: rgba(25, 135, 84, 0.05);
        transform: scale(1.005);
    }

    .induction-row td {
        padding: 1rem;
        vertical-align: middle;
    }

    .inductee-icon-wrapper {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Empty state */
    .empty-icon-wrapper {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    /* Button styling */
    .btn {
        border-radius: 8px;
        padding: 0.625rem 1.25rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-success:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
    }

    /* Dropdown improvements */
    .dropdown-item {
        padding: 0.5rem 1rem;
        transition: background-color 0.2s;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .dropdown-item i {
        width: 20px;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .stat-card .card-body {
            padding: 1rem;
        }
        
        .inductee-info h6 {
            font-size: 0.95rem;
        }
        
        .table-responsive {
            font-size: 0.9rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
@endsection
