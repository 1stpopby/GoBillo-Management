@extends('layouts.app')

@section('title', 'Estimates & Quotes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Estimates & Quotes</h1>
        <p class="text-muted mb-0">Create professional estimates and track client approvals</p>
    </div>
    @if(auth()->user()->canManageProjects())
        <div class="btn-group">
            <a href="{{ route('estimates.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> New Estimate
            </a>
            <button type="button" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('estimates.create') }}">
                    <i class="bi bi-file-text"></i> Blank Estimate
                </a></li>
                <li><a class="dropdown-item" href="{{ route('estimates.create', ['template' => true]) }}">
                    <i class="bi bi-file-plus"></i> From Template
                </a></li>
            </ul>
        </div>
    @endif
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-left-primary h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Estimates</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $estimates->total() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-calculator text-gray-300" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-left-success h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Approved</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $estimates->where('status', 'approved')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-check-circle text-gray-300" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-left-warning h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $estimates->whereIn('status', ['draft', 'sent'])->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-clock text-gray-300" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-left-info h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Converted</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $estimates->where('status', 'converted')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-arrow-right-circle text-gray-300" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('estimates.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Estimate number or client...">
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="client_id" class="form-label">Client</label>
                <select class="form-select" id="client_id" name="client_id">
                    <option value="">All Clients</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" class="form-control" id="date_from" name="date_from" 
                       value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">To Date</label>
                <input type="date" class="form-control" id="date_to" name="date_to" 
                       value="{{ request('date_to') }}">
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Estimates Table -->
<div class="card">
    <div class="card-body">
        @if($estimates->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Estimate #</th>
                            <th>Client</th>
                            <th>Project</th>
                            <th>Issue Date</th>
                            <th>Valid Until</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($estimates as $estimate)
                            <tr>
                                <td>
                                    <a href="{{ route('estimates.show', $estimate) }}" class="text-decoration-none">
                                        <strong>{{ $estimate->estimate_number }}</strong>
                                    </a>
                                </td>
                                <td>{{ $estimate->client->name }}</td>
                                <td>
                                    @if($estimate->project)
                                        <a href="{{ route('projects.show', $estimate->project) }}" class="text-decoration-none">
                                            {{ $estimate->project->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">No project</span>
                                    @endif
                                </td>
                                <td>{{ $estimate->issue_date->format('M d, Y') }}</td>
                                <td>
                                    {{ $estimate->valid_until->format('M d, Y') }}
                                    @if($estimate->isExpired())
                                        <small class="text-danger">(Expired)</small>
                                    @elseif($estimate->status !== 'approved' && $estimate->days_until_expiration <= 7)
                                        <small class="text-warning">({{ $estimate->days_until_expiration }} days left)</small>
                                    @endif
                                </td>
                                <td>
                                    <strong>${{ number_format($estimate->total_amount, 2) }}</strong>
                                    <small class="text-muted">{{ $estimate->currency }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $estimate->status_color }}">
                                        {{ ucfirst($estimate->status) }}
                                    </span>
                                    @if($estimate->status === 'converted' && $estimate->convertedToProject)
                                        <br><small class="text-muted">→ {{ $estimate->convertedToProject->name }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('estimates.show', $estimate) }}" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if(auth()->user()->canManageProjects() && !in_array($estimate->status, ['approved', 'converted']))
                                            <a href="{{ route('estimates.edit', $estimate) }}" 
                                               class="btn btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('estimates.pdf', $estimate) }}" 
                                           class="btn btn-outline-info" title="Download PDF">
                                            <i class="bi bi-file-pdf"></i>
                                        </a>
                                        @if(auth()->user()->canManageProjects())
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-secondary dropdown-toggle" 
                                                        data-bs-toggle="dropdown" title="More actions">
                                                    <i class="bi bi-three-dots"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @if($estimate->status === 'draft')
                                                        <li>
                                                            <form method="POST" action="{{ route('estimates.send', $estimate) }}" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="bi bi-send"></i> Send Estimate
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                    @if(in_array($estimate->status, ['sent', 'expired']))
                                                        <li>
                                                            <form method="POST" action="{{ route('estimates.approve', $estimate) }}" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="bi bi-check-circle"></i> Mark as Approved
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <button type="button" class="dropdown-item" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#rejectModal{{ $estimate->id }}">
                                                                <i class="bi bi-x-circle"></i> Mark as Rejected
                                                            </button>
                                                        </li>
                                                    @endif
                                                    @if($estimate->status === 'approved')
                                                        <li>
                                                            <button type="button" class="dropdown-item" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#convertModal{{ $estimate->id }}">
                                                                <i class="bi bi-arrow-right-circle"></i> Convert to Project
                                                            </button>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <form method="POST" action="{{ route('estimates.duplicate', $estimate) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="bi bi-files"></i> Duplicate
                                                            </button>
                                                        </form>
                                                    </li>
                                                    @if(!in_array($estimate->status, ['approved', 'converted']))
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form method="POST" action="{{ route('estimates.destroy', $estimate) }}" 
                                                                  class="d-inline" onsubmit="return confirm('Are you sure?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="bi bi-trash"></i> Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <!-- Reject Modal -->
                            @if(in_array($estimate->status, ['sent', 'expired']))
                                <div class="modal fade" id="rejectModal{{ $estimate->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('estimates.reject', $estimate) }}">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Reject Estimate</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Mark estimate <strong>{{ $estimate->estimate_number }}</strong> as rejected?</p>
                                                    <div class="mb-3">
                                                        <label for="rejection_reason{{ $estimate->id }}" class="form-label">Reason (Optional)</label>
                                                        <textarea class="form-control" name="rejection_reason" 
                                                                  id="rejection_reason{{ $estimate->id }}" rows="3"
                                                                  placeholder="Why was this estimate rejected?"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Mark as Rejected</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Convert to Project Modal -->
                            @if($estimate->status === 'approved')
                                <div class="modal fade" id="convertModal{{ $estimate->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('estimates.convert-to-project', $estimate) }}">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Convert to Project</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Convert estimate <strong>{{ $estimate->estimate_number }}</strong> to a project?</p>
                                                    <div class="mb-3">
                                                        <label for="project_name{{ $estimate->id }}" class="form-label">Project Name</label>
                                                        <input type="text" class="form-control" name="project_name" 
                                                               id="project_name{{ $estimate->id }}" required
                                                               value="{{ $estimate->description ? Str::limit($estimate->description, 50) : $estimate->client->name . ' Project' }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="project_description{{ $estimate->id }}" class="form-label">Description</label>
                                                        <textarea class="form-control" name="project_description" 
                                                                  id="project_description{{ $estimate->id }}" rows="3">{{ $estimate->description }}</textarea>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="start_date{{ $estimate->id }}" class="form-label">Start Date</label>
                                                            <input type="date" class="form-control" name="start_date" 
                                                                   id="start_date{{ $estimate->id }}" required value="{{ now()->toDateString() }}">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="end_date{{ $estimate->id }}" class="form-label">End Date</label>
                                                            <input type="date" class="form-control" name="end_date" 
                                                                   id="end_date{{ $estimate->id }}" required value="{{ now()->addMonths(3)->toDateString() }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success">Convert to Project</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $estimates->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-calculator text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3">No Estimates Found</h4>
                <p class="text-muted">You haven't created any estimates yet.</p>
                @if(auth()->user()->canManageProjects())
                    <a href="{{ route('estimates.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create Your First Estimate
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<style>
.border-left-primary { border-left: 0.25rem solid var(--gobillo-primary) !important; }
.border-left-success { border-left: 0.25rem solid var(--gobillo-success) !important; }
.border-left-warning { border-left: 0.25rem solid var(--gobillo-warning) !important; }
.border-left-info { border-left: 0.25rem solid var(--gobillo-info) !important; }
</style>
@endsection 