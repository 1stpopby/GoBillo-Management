@extends('layouts.app')

@section('title', 'Company Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">{{ $company->name }}</h1>
        <p class="text-muted mb-0">Company Details & Management</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('superadmin.companies.edit', $company) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Edit Company
        </a>
        <a href="{{ route('superadmin.companies.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Companies
        </a>
    </div>
</div>

<!-- Company Status Alert -->
@if($company->status !== 'active')
    <div class="alert alert-warning" role="alert">
        <i class="bi bi-exclamation-triangle"></i>
        This company is currently <strong>{{ ucfirst($company->status) }}</strong>.
        @if($company->status === 'suspended')
            <form method="POST" action="{{ route('superadmin.companies.activate', $company) }}" class="d-inline ms-2">
                @csrf
                <button type="submit" class="btn btn-sm btn-success">Activate Company</button>
            </form>
        @endif
    </div>
@endif

<!-- Company Overview -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Company Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Company Name</label>
                            <p class="mb-0">{{ $company->name }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <p class="mb-0">
                                <a href="mailto:{{ $company->email }}">{{ $company->email }}</a>
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone</label>
                            <p class="mb-0">{{ $company->phone ?: 'Not provided' }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Website</label>
                            <p class="mb-0">
                                @if($company->website)
                                    <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a>
                                @else
                                    Not provided
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Address</label>
                            <p class="mb-0">
                                @if($company->address)
                                    {{ $company->address }}<br>
                                    {{ $company->city }}, {{ $company->state }} {{ $company->zip_code }}<br>
                                    {{ $company->country }}
                                @else
                                    Not provided
                                @endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p class="mb-0">
                                <span class="badge bg-{{ $company->status == 'active' ? 'success' : ($company->status == 'suspended' ? 'danger' : 'secondary') }}">
                                    {{ ucfirst($company->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Created</label>
                            <p class="mb-0">{{ $company->created_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Slug</label>
                            <p class="mb-0"><code>{{ $company->slug }}</code></p>
                        </div>
                    </div>
                </div>
                
                @if($company->description)
                    <div class="mt-3">
                        <label class="form-label fw-bold">Description</label>
                        <p class="mb-0">{{ $company->description }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Subscription Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Subscription Details</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Plan</label>
                    <p class="mb-0">
                        <span class="badge bg-{{ $company->subscription_plan == 'trial' ? 'warning' : ($company->subscription_plan == 'enterprise' ? 'success' : 'primary') }}">
                            {{ ucfirst($company->subscription_plan) }}
                        </span>
                    </p>
                </div>
                
                @if($company->trial_ends_at)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Trial Ends</label>
                        <p class="mb-0 {{ $company->trial_ends_at->isPast() ? 'text-danger' : 'text-success' }}">
                            {{ $company->trial_ends_at->format('M d, Y') }}
                            <br><small>({{ $company->trial_ends_at->diffForHumans() }})</small>
                        </p>
                    </div>
                @endif
                
                @if($company->subscription_ends_at)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Subscription Ends</label>
                        <p class="mb-0 {{ $company->subscription_ends_at->isPast() ? 'text-danger' : 'text-success' }}">
                            {{ $company->subscription_ends_at->format('M d, Y') }}
                            <br><small>({{ $company->subscription_ends_at->diffForHumans() }})</small>
                        </p>
                    </div>
                @endif
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Limits</label>
                    <p class="mb-0">
                        <strong>Users:</strong> {{ $company->users_count }}/{{ $company->max_users }}<br>
                        <strong>Projects:</strong> {{ $company->projects_count }}/{{ $company->max_projects }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('superadmin.companies.edit', $company) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Edit Company
                    </a>
                    
                    @if($company->status === 'active')
                        <form method="POST" action="{{ route('superadmin.companies.suspend', $company) }}">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100" 
                                    onclick="return confirm('Are you sure you want to suspend this company?')">
                                <i class="bi bi-pause-circle"></i> Suspend Company
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('superadmin.companies.activate', $company) }}">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-play-circle"></i> Activate Company
                            </button>
                        </form>
                    @endif
                    
                    <form method="POST" action="{{ route('superadmin.companies.destroy', $company) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100" 
                                onclick="return confirm('Are you sure? This will delete the company and ALL associated data!')">
                            <i class="bi bi-trash"></i> Delete Company
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Company Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-left-primary h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $company->users_count }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people text-gray-300" style="font-size: 2rem;"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Projects</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $company->projects_count }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-folder text-gray-300" style="font-size: 2rem;"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Clients</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $company->clients_count }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-person-badge text-gray-300" style="font-size: 2rem;"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Tasks</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $company->tasks_count }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-list-task text-gray-300" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Users List -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Company Users ({{ $company->users_count }})</h5>
    </div>
    <div class="card-body">
        @if($company->users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($company->users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                             style="width: 35px; height: 35px;">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <strong>{{ $user->name }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $user->role_display }}</span>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?: 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3">No Users Yet</h5>
                <p class="text-muted">This company doesn't have any users assigned.</p>
            </div>
        @endif
    </div>
</div>

<!-- Recent Projects -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Recent Projects ({{ $company->projects_count }})</h5>
    </div>
    <div class="card-body">
        @if($company->projects->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Budget</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($company->projects->take(10) as $project)
                            <tr>
                                <td>
                                    <strong>{{ $project->name }}</strong>
                                    @if($project->description)
                                        <br><small class="text-muted">{{ Str::limit($project->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>{{ $project->client->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $project->status_color }}">
                                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $project->status_color }}" 
                                             style="width: {{ $project->progress }}%">
                                            {{ $project->progress }}%
                                        </div>
                                    </div>
                                </td>
                                <td>${{ number_format($project->budget, 0) }}</td>
                                <td>{{ $project->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-folder text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3">No Projects Yet</h5>
                <p class="text-muted">This company hasn't created any projects.</p>
            </div>
        @endif
    </div>
</div>

<style>
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }
.border-left-success { border-left: 0.25rem solid #1cc88a !important; }
.border-left-info { border-left: 0.25rem solid #36b9cc !important; }
.border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
</style>
@endsection 