@extends('layouts.app')

@section('title', 'Hire Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Hire Management</h1>
            <p class="page-subtitle">Manage hiring requests and recruitment process</p>
        </div>
        <div>
            <a href="{{ route('hire.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>New Hire Request
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-card-body">
                    <div class="stat-icon bg-primary">
                        <i class="bi bi-clipboard-check"></i>
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
                        <div class="stat-number">{{ $stats['filled_requests'] }}</div>
                        <div class="stat-label">Filled</div>
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
            <form method="GET" action="{{ route('hire.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            @foreach(\App\Models\HireRequest::getStatusOptions() as $key => $label)
                                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Urgency</label>
                        <select name="urgency" class="form-select">
                            <option value="">All Urgency</option>
                            @foreach(\App\Models\HireRequest::getUrgencyOptions() as $key => $label)
                                <option value="{{ $key }}" {{ request('urgency') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Position Type</label>
                        <select name="position_type" class="form-select">
                            <option value="">All Positions</option>
                            @foreach(\App\Models\HireRequest::getPositionTypeOptions() as $key => $label)
                                <option value="{{ $key }}" {{ request('position_type') === $key ? 'selected' : '' }}>
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
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search title, description, skills..." 
                                   value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                            @if(request()->hasAny(['status', 'urgency', 'position_type', 'site_id', 'search']))
                                <a href="{{ route('hire.index') }}" class="btn btn-outline-danger">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Hire Requests Table -->
    <div class="card">
        <div class="card-body">
            @if($hireRequests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Request Details</th>
                                <th>Position</th>
                                <th>Site/Project</th>
                                <th>Requested By</th>
                                <th>Status</th>
                                <th>Urgency</th>
                                <th>Deadline</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hireRequests as $request)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $request->title }}</strong>
                                        @if($request->isOverdue())
                                            <span class="badge bg-danger ms-2">OVERDUE</span>
                                        @endif
                                        <br>
                                        <small class="text-muted">{{ Str::limit($request->description, 100) }}</small>
                                        <br>
                                        <small class="text-info">
                                            Quantity: {{ $request->quantity }}
                                            @if($request->offered_rate)
                                                | £{{ number_format($request->offered_rate, 2) }}/{{ $request->rate_type }}
                                            @endif
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $request->position_type_display }}</span>
                                    <br>
                                    <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $request->employment_type)) }}</small>
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
                                </td>
                                <td>
                                    <span class="badge bg-{{ $request->urgency_color }}">
                                        {{ ucfirst($request->urgency) }}
                                    </span>
                                </td>
                                <td>
                                    @if($request->deadline)
                                        {{ $request->deadline->format('M j, Y') }}
                                        @if($request->deadline->isPast())
                                            <br><small class="text-danger">Past due</small>
                                        @elseif($request->deadline->diffInDays() <= 7)
                                            <br><small class="text-warning">Due soon</small>
                                        @endif
                                    @else
                                        <span class="text-muted">No deadline</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('hire.show', $request) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        @if($request->canBeEdited())
                                            <a href="{{ route('hire.edit', $request) }}" class="btn btn-outline-secondary btn-sm">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif
                                        
                                        @if(auth()->user()->isCompanyAdmin() || auth()->user()->canManageProjects())
                                            @if($request->canBeApproved())
                                                <form method="POST" action="{{ route('hire.approve', $request) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success btn-sm" 
                                                            onclick="return confirm('Approve this hire request?')">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if($request->status === 'approved' || $request->status === 'in_progress')
                                                <form method="POST" action="{{ route('hire.mark-filled', $request) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-info btn-sm" 
                                                            onclick="return confirm('Mark this request as filled?')">
                                                        <i class="bi bi-check-circle"></i>
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
                        Showing {{ $hireRequests->firstItem() }} to {{ $hireRequests->lastItem() }} of {{ $hireRequests->total() }} results
                    </div>
                    {{ $hireRequests->links() }}
                </div>
            @else
                <div class="empty-state text-center py-5">
                    <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                    <h4 class="mt-3">No Hire Requests Found</h4>
                    <p class="text-muted">Get started by creating your first hire request.</p>
                    <a href="{{ route('hire.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Hire Request
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
</style>
@endsection
