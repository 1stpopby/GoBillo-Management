@extends('layouts.app')

@section('title', 'Tools Hire Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Tools Hire Management</h1>
            <p class="page-subtitle">Manage tool and equipment hire requests</p>
        </div>
        <div>
            <a href="{{ route('tool-hire.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>New Tool Hire Request
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon bg-primary">
                        <i class="bi bi-tools"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['total_requests'] }}</div>
                        <div class="stat-label">Total Requests</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon bg-warning">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['pending_approval'] }}</div>
                        <div class="stat-label">Pending Approval</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon bg-info">
                        <i class="bi bi-play-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['active_requests'] }}</div>
                        <div class="stat-label">Active</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon bg-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['currently_hired'] }}</div>
                        <div class="stat-label">Currently Hired</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon bg-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['urgent_requests'] }}</div>
                        <div class="stat-label">Urgent</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon bg-dark">
                        <i class="bi bi-calendar-x"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['overdue_requests'] }}</div>
                        <div class="stat-label">Overdue</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('tool-hire.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            @foreach(\App\Models\ToolHireRequest::getStatusOptions() as $key => $label)
                                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Category</label>
                        <select name="tool_category" class="form-select">
                            <option value="">All Categories</option>
                            @foreach(\App\Models\ToolHireRequest::getCategoryOptions() as $key => $label)
                                <option value="{{ $key }}" {{ request('tool_category') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Site</label>
                        <select name="site_id" class="form-select">
                            <option value="">All Sites</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}" {{ request('site_id') == $site->id ? 'selected' : '' }}>
                                    {{ $site->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Search</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search tools, titles..." 
                                   value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                            @if(request()->hasAny(['status', 'tool_category', 'site_id', 'search']))
                                <a href="{{ route('tool-hire.index') }}" class="btn btn-outline-danger">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tool Hire Requests Table -->
    <div class="card">
        <div class="card-body">
            @if($toolHireRequests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Tool Details</th>
                                <th>Category</th>
                                <th>Hire Period</th>
                                <th>Site/Project</th>
                                <th>Requested By</th>
                                <th>Status</th>
                                <th>Cost</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($toolHireRequests as $request)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $request->tool_name }}</strong>
                                        @if($request->isOverdue())
                                            <span class="badge bg-danger ms-2">OVERDUE</span>
                                        @endif
                                        <br>
                                        <small class="text-muted">{{ $request->title }}</small>
                                        <br>
                                        <small class="text-info">
                                            Qty: {{ $request->quantity }}
                                            @if($request->estimated_daily_rate)
                                                | £{{ number_format($request->estimated_daily_rate, 2) }}/day
                                            @endif
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $request->category_display }}</span>
                                    <br>
                                    <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $request->tool_type)) }}</small>
                                </td>
                                <td>
                                    @if($request->hire_start_date)
                                        <strong>{{ $request->hire_start_date->format('M j') }}</strong> - 
                                        <strong>{{ $request->hire_end_date->format('M j, Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $request->hire_duration_days }} days</small>
                                        @if($request->hire_start_date->isPast() && $request->hire_end_date->isFuture())
                                            <br><small class="text-success">Currently active</small>
                                        @elseif($request->hire_start_date->isFuture())
                                            <br><small class="text-info">Upcoming</small>
                                        @endif
                                    @else
                                        <span class="text-muted">Not set</span>
                                    @endif
                                </td>
                                <td>
                                    @if($request->site)
                                        <strong>{{ $request->site->name }}</strong><br>
                                    @endif
                                    @if($request->project)
                                        <small class="text-muted">{{ $request->project->name }}</small>
                                    @else
                                        <small class="text-muted">No specific project</small>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $request->requestedBy->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $request->created_at->format('M j, Y') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $request->status_color }}">
                                        {{ $request->status_display }}
                                    </span>
                                    @if($request->urgency !== 'normal')
                                        <br>
                                        <span class="badge bg-{{ $request->urgency_color }} mt-1">
                                            {{ ucfirst($request->urgency) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($request->estimated_total_cost)
                                        <strong>£{{ number_format($request->estimated_total_cost, 2) }}</strong>
                                        <br>
                                        <small class="text-muted">Estimated</small>
                                    @elseif($request->estimated_daily_rate)
                                        <strong>£{{ number_format($request->estimated_daily_rate, 2) }}/day</strong>
                                    @else
                                        <span class="text-muted">TBD</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('tool-hire.show', $request) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        @if($request->canBeEdited())
                                            <a href="{{ route('tool-hire.edit', $request) }}" class="btn btn-outline-secondary btn-sm">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif
                                        
                                        @if(auth()->user()->isCompanyAdmin() || auth()->user()->canManageProjects())
                                            @if($request->canBeApproved())
                                                <form method="POST" action="{{ route('tool-hire.approve', $request) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success btn-sm" 
                                                            onclick="return confirm('Approve this tool hire request?')">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $toolHireRequests->firstItem() }} to {{ $toolHireRequests->lastItem() }} of {{ $toolHireRequests->total() }} results
                    </div>
                    {{ $toolHireRequests->links() }}
                </div>
            @else
                <div class="empty-state text-center py-5">
                    <i class="bi bi-tools text-muted" style="font-size: 3rem;"></i>
                    <h4 class="mt-3">No Tool Hire Requests Found</h4>
                    <p class="text-muted">Get started by creating your first tool hire request.</p>
                    <a href="{{ route('tool-hire.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Tool Hire Request
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.stat-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: none;
    height: 100%;
}

.stat-card-body {
    padding: 1.5rem;
    display: flex;
    align-items: center;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    color: white;
    font-size: 1.5rem;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    line-height: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #6c757d;
    margin-bottom: 0;
}

.empty-state {
    padding: 4rem 2rem;
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    font-weight: 500;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #667eea 100%);
    transform: translateY(-1px);
}
</style>
@endsection
