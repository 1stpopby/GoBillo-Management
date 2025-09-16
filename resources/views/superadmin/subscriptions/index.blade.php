@extends('layouts.superadmin')

@section('title', 'Subscriptions')
@section('page-title', 'Subscription Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted mb-0">Monitor and manage all company subscriptions</p>
    </div>
    <a href="{{ route('superadmin.subscriptions.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>New Subscription
    </a>
</div>

<!-- Subscription Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-credit-card text-primary mb-2" style="font-size: 2rem;"></i>
                <h3 class="mb-1">{{ $subscriptions->total() }}</h3>
                <p class="text-muted mb-0">Total Subscriptions</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                <h3 class="mb-1">{{ $subscriptions->where('status', 'active')->count() }}</h3>
                <p class="text-muted mb-0">Active</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-clock text-info mb-2" style="font-size: 2rem;"></i>
                <h3 class="mb-1">{{ $subscriptions->where('status', 'trial')->count() }}</h3>
                <p class="text-muted mb-0">Trial</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-currency-pound text-success mb-2" style="font-size: 2rem;"></i>
                <h3 class="mb-1">£{{ number_format($subscriptions->sum('amount'), 0) }}</h3>
                <p class="text-muted mb-0">Monthly Revenue</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('superadmin.subscriptions.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Company name or email">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="plan" class="form-label">Plan</label>
                <select class="form-select" id="plan" name="plan">
                    <option value="">All Plans</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->slug }}" {{ request('plan') == $plan->slug ? 'selected' : '' }}>
                            {{ $plan->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <a href="{{ route('superadmin.subscriptions.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Subscriptions List -->
<div class="card">
    <div class="card-body">
        @if($subscriptions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Company</th>
                            <th>Plan</th>
                            <th>Status</th>
                            <th>Billing</th>
                            <th>Usage</th>
                            <th>Next Billing</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscriptions as $subscription)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                            {{ substr($subscription->company->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $subscription->company->name }}</div>
                                            <small class="text-muted">{{ $subscription->company->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-semibold">{{ $subscription->membershipPlan->name }}</div>
                                        <small class="text-muted">
                                            £{{ number_format($subscription->amount, 0) }}/{{ $subscription->billing_cycle === 'yearly' ? 'year' : 'month' }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $subscription->status_color }}">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                    @if($subscription->is_trial && $subscription->trial_days_remaining >= 0)
                                        <br><small class="text-muted">{{ $subscription->trial_days_remaining }} days left</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="small">
                                        <div>{{ ucfirst($subscription->billing_cycle) }}</div>
                                        @if($subscription->last_payment_date)
                                            <small class="text-muted">
                                                Last: {{ $subscription->last_payment_date->format('M j, Y') }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <div>Users: {{ $subscription->current_users }}</div>
                                        <div>Sites: {{ $subscription->current_sites }}</div>
                                        <div>Projects: {{ $subscription->current_projects }}</div>
                                    </div>
                                </td>
                                <td>
                                    @if($subscription->next_billing_date)
                                        <div class="small">
                                            {{ $subscription->next_billing_date->format('M j, Y') }}
                                            @if($subscription->next_billing_date->isPast())
                                                <br><span class="text-danger">Overdue</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('superadmin.subscriptions.show', $subscription) }}">
                                                    <i class="bi bi-eye me-2"></i>View Details
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('superadmin.subscriptions.edit', $subscription) }}">
                                                    <i class="bi bi-pencil me-2"></i>Edit
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            @if($subscription->status === 'active')
                                                <li>
                                                    <form method="POST" action="{{ route('superadmin.subscriptions.suspend', $subscription) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-warning" onclick="return confirm('Are you sure?')">
                                                            <i class="bi bi-pause me-2"></i>Suspend
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form method="POST" action="{{ route('superadmin.subscriptions.cancel', $subscription) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure?')">
                                                            <i class="bi bi-x-circle me-2"></i>Cancel
                                                        </button>
                                                    </form>
                                                </li>
                                            @elseif($subscription->status === 'cancelled' || $subscription->status === 'suspended')
                                                <li>
                                                    <form method="POST" action="{{ route('superadmin.subscriptions.reactivate', $subscription) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-success">
                                                            <i class="bi bi-play me-2"></i>Reactivate
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
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $subscriptions->firstItem() }} to {{ $subscriptions->lastItem() }} of {{ $subscriptions->total() }} subscriptions
                </div>
                {{ $subscriptions->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-credit-card text-muted mb-3" style="font-size: 3rem;"></i>
                <h5 class="text-muted">No subscriptions found</h5>
                <p class="text-muted">No subscriptions match your current filters.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 0.875rem;
    font-weight: 600;
}
</style>
@endpush
