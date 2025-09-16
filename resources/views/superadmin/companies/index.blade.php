@extends('layouts.superadmin')

@section('title', 'Manage Companies')
@section('page-title', 'Companies Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Companies Management</h1>
        <p class="text-muted mb-0">Manage all companies on the platform</p>
    </div>
    <a href="{{ route('superadmin.companies.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Company
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('superadmin.companies.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Company name, email, or slug">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="plan" class="form-label">Subscription Plan</label>
                <select class="form-select" id="plan" name="plan">
                    <option value="">All Plans</option>
                    <option value="trial" {{ request('plan') == 'trial' ? 'selected' : '' }}>Trial</option>
                    <option value="basic" {{ request('plan') == 'basic' ? 'selected' : '' }}>Basic</option>
                    <option value="professional" {{ request('plan') == 'professional' ? 'selected' : '' }}>Professional</option>
                    <option value="enterprise" {{ request('plan') == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('superadmin.companies.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Companies List -->
<div class="card">
    <div class="card-body">
        @if($companies->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Company</th>
                            <th>Contact</th>
                            <th>Plan</th>
                            <th>Usage</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($companies as $company)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            @if($company->logo)
                                                <img src="{{ asset('storage/' . $company->logo) }}" 
                                                     alt="{{ $company->name }}" class="rounded-circle" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="bg-gradient-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    {{ strtoupper(substr($company->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0">
                                                <a href="{{ route('superadmin.companies.show', $company) }}" 
                                                   class="text-decoration-none">{{ $company->name }}</a>
                                            </h6>
                                            <small class="text-muted">{{ $company->slug }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div>{{ $company->email }}</div>
                                        @if($company->phone)
                                            <small class="text-muted">{{ $company->phone }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($company->activeSubscription && $company->activeSubscription->membershipPlan)
                                        <span class="badge bg-{{ $company->activeSubscription->status === 'trial' ? 'info' : ($company->activeSubscription->membershipPlan->slug === 'enterprise' ? 'success' : ($company->activeSubscription->membershipPlan->slug === 'professional' ? 'warning' : 'primary')) }}">
                                            {{ $company->activeSubscription->membershipPlan->name }}
                                        </span>
                                        <br><small class="text-muted">
                                            £{{ number_format($company->activeSubscription->amount, 0) }}/{{ $company->activeSubscription->billing_cycle === 'yearly' ? 'year' : 'month' }}
                                        </small>
                                        @if($company->activeSubscription->status === 'trial' && $company->activeSubscription->trial_ends_at)
                                            <br><small class="text-warning">Trial expires: {{ $company->activeSubscription->trial_ends_at->format('M d, Y') }}</small>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">No Active Plan</span>
                                        <br><small class="text-muted">No subscription</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="small">
                                        @if($company->activeSubscription && $company->activeSubscription->membershipPlan)
                                            @php
                                                $plan = $company->activeSubscription->membershipPlan;
                                                $subscription = $company->activeSubscription;
                                            @endphp
                                            <div>Users: {{ $subscription->current_users }}{{ $plan->max_users > 0 ? '/' . $plan->max_users : '' }}</div>
                                            <div>Sites: {{ $subscription->current_sites }}{{ $plan->max_sites > 0 ? '/' . $plan->max_sites : '' }}</div>
                                            <div>Projects: {{ $subscription->current_projects }}{{ $plan->max_projects > 0 ? '/' . $plan->max_projects : '' }}</div>
                                        @else
                                            <div>Users: {{ $company->users_count }}</div>
                                            <div>Projects: {{ $company->projects_count }}</div>
                                            <div>Clients: {{ $company->clients_count }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $company->status == 'active' ? 'success' : ($company->status == 'suspended' ? 'danger' : 'secondary') }}">
                                        {{ ucfirst($company->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div>{{ $company->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $company->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('superadmin.companies.show', $company) }}">
                                                    <i class="bi bi-eye"></i> View Details
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('superadmin.companies.edit', $company) }}">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            @if($company->status == 'active')
                                                <li>
                                                    <form method="POST" action="{{ route('superadmin.companies.suspend', $company) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-warning" 
                                                                onclick="return confirm('Are you sure you want to suspend this company?')">
                                                            <i class="bi bi-pause-circle"></i> Suspend
                                                        </button>
                                                    </form>
                                                </li>
                                            @else
                                                <li>
                                                    <form method="POST" action="{{ route('superadmin.companies.activate', $company) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-success">
                                                            <i class="bi bi-play-circle"></i> Activate
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form method="POST" action="{{ route('superadmin.companies.destroy', $company) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger" 
                                                            onclick="return confirm('Are you sure? This will delete the company and all associated data!')">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <p class="text-muted mb-0">
                        Showing {{ $companies->firstItem() }} to {{ $companies->lastItem() }} 
                        of {{ $companies->total() }} companies
                    </p>
                </div>
                <div>
                    {{ $companies->withQueryString()->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-building text-muted" style="font-size: 3rem;"></i>
                <h4 class="mt-3">No Companies Found</h4>
                <p class="text-muted">Get started by creating your first company.</p>
                <a href="{{ route('superadmin.companies.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create Company
                </a>
            </div>
        @endif
    </div>
</div>
@endsection 