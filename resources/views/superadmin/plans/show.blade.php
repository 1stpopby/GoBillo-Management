@extends('layouts.superadmin')

@section('title', $plan->name . ' - Membership Plan')
@section('page-title', 'Membership Plan Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">{{ $plan->name }}</h1>
        <p class="text-muted mb-0">{{ $plan->description ?: 'No description available' }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('superadmin.plans.edit', $plan) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit Plan
        </a>
        <form method="POST" action="{{ route('superadmin.plans.toggle-status', $plan) }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-{{ $plan->is_active ? 'outline-secondary' : 'success' }}">
                <i class="bi bi-{{ $plan->is_active ? 'pause' : 'play' }}-circle"></i>
                {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
            </button>
        </form>
        <a href="{{ route('superadmin.plans.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Plans
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Plan Details -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Plan Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <!-- Basic Info -->
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Basic Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <td class="fw-semibold">Plan Name:</td>
                                <td>{{ $plan->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Slug:</td>
                                <td><code>{{ $plan->slug }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Status:</td>
                                <td>
                                    <span class="badge bg-{{ $plan->is_active ? 'success' : 'secondary' }}">
                                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Sort Order:</td>
                                <td>{{ $plan->sort_order }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Pricing -->
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Pricing</h6>
                        <table class="table table-sm">
                            <tr>
                                <td class="fw-semibold">Monthly Price:</td>
                                <td class="text-success fw-bold">£{{ number_format($plan->monthly_price, 2) }}</td>
                            </tr>
                            @if($plan->yearly_price)
                                <tr>
                                    <td class="fw-semibold">Yearly Price:</td>
                                    <td class="text-success fw-bold">£{{ number_format($plan->yearly_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">Yearly Savings:</td>
                                    <td class="text-info">
                                        £{{ number_format(($plan->monthly_price * 12) - $plan->yearly_price, 2) }}
                                        ({{ round((($plan->monthly_price * 12) - $plan->yearly_price) / ($plan->monthly_price * 12) * 100) }}%)
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td class="fw-semibold">Setup Fee:</td>
                                <td>£{{ number_format($plan->setup_fee, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Trial Period:</td>
                                <td>{{ $plan->trial_days }} days</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Limits -->
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Resource Limits</h6>
                        <table class="table table-sm">
                            <tr>
                                <td class="fw-semibold">Max Users:</td>
                                <td>{{ $plan->max_users ?: 'Unlimited' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Max Sites:</td>
                                <td>{{ $plan->max_sites ?: 'Unlimited' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Max Projects:</td>
                                <td>{{ $plan->max_projects ?: 'Unlimited' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Max Storage:</td>
                                <td>{{ $plan->max_storage_gb ? $plan->max_storage_gb . ' GB' : 'Unlimited' }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Features -->
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Features</h6>
                        @if($plan->features && count($plan->features) > 0)
                            <div class="row g-1">
                                @foreach($plan->features as $feature)
                                    <div class="col-12">
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-check-circle-fill text-success me-1"></i>
                                            {{ ucwords(str_replace('_', ' ', $feature)) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted small">No features defined</p>
                        @endif
                    </div>

                    <!-- Metadata -->
                    <div class="col-12">
                        <h6 class="text-muted mb-2">Metadata</h6>
                        <table class="table table-sm">
                            <tr>
                                <td class="fw-semibold">Created:</td>
                                <td>{{ $plan->created_at->format('F j, Y \a\t g:i A') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Last Updated:</td>
                                <td>{{ $plan->updated_at->format('F j, Y \a\t g:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="col-lg-4">
        <!-- Usage Stats -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Usage Statistics</h6>
            </div>
            <div class="card-body">
                @php
                    $totalSubscriptions = $plan->subscriptions()->count();
                    $activeSubscriptions = $plan->subscriptions()->whereIn('status', ['active', 'trial'])->count();
                    $trialSubscriptions = $plan->subscriptions()->where('status', 'trial')->count();
                    $cancelledSubscriptions = $plan->subscriptions()->where('status', 'cancelled')->count();
                @endphp
                <div class="row g-3 text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h3 class="text-primary mb-1">{{ $totalSubscriptions }}</h3>
                            <small class="text-muted">Total Subscriptions</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h3 class="text-success mb-1">{{ $activeSubscriptions }}</h3>
                        <small class="text-muted">Active</small>
                    </div>
                    <div class="col-6">
                        <div class="border-end border-top pt-3">
                            <h3 class="text-info mb-1">{{ $trialSubscriptions }}</h3>
                            <small class="text-muted">On Trial</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border-top pt-3">
                            <h3 class="text-warning mb-1">{{ $cancelledSubscriptions }}</h3>
                            <small class="text-muted">Cancelled</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Stats -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-currency-pound me-2"></i>Revenue Statistics</h6>
            </div>
            <div class="card-body">
                @php
                    $monthlyRevenue = $plan->subscriptions()->whereIn('status', ['active', 'trial'])->sum('amount');
                    $annualRevenue = $monthlyRevenue * 12;
                @endphp
                <div class="text-center mb-3">
                    <h3 class="text-success mb-1">£{{ number_format($monthlyRevenue, 0) }}</h3>
                    <small class="text-muted">Monthly Recurring Revenue</small>
                </div>
                <div class="text-center">
                    <h4 class="text-primary mb-1">£{{ number_format($annualRevenue, 0) }}</h4>
                    <small class="text-muted">Annual Recurring Revenue</small>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-dark text-white">
                <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('superadmin.plans.edit', $plan) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-2"></i>Edit Plan
                    </a>
                    <form method="POST" action="{{ route('superadmin.plans.toggle-status', $plan) }}">
                        @csrf
                        <button type="submit" class="btn btn-{{ $plan->is_active ? 'outline-secondary' : 'success' }} w-100">
                            <i class="bi bi-{{ $plan->is_active ? 'pause' : 'play' }}-circle me-2"></i>
                            {{ $plan->is_active ? 'Deactivate Plan' : 'Activate Plan' }}
                        </button>
                    </form>
                    <a href="{{ route('superadmin.subscriptions.index', ['plan' => $plan->slug]) }}" class="btn btn-outline-primary">
                        <i class="bi bi-list me-2"></i>View Subscriptions
                    </a>
                    @if($totalSubscriptions === 0)
                        <form method="POST" action="{{ route('superadmin.plans.destroy', $plan) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this plan? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-trash me-2"></i>Delete Plan
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Subscriptions -->
@if($plan->subscriptions()->count() > 0)
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="bi bi-people me-2"></i>Recent Subscriptions</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Company</th>
                            <th>Status</th>
                            <th>Started</th>
                            <th>Next Billing</th>
                            <th>Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plan->subscriptions()->with('company')->latest()->limit(10)->get() as $subscription)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            @if($subscription->company->logo)
                                                <img src="{{ Storage::url($subscription->company->logo) }}" 
                                                     alt="{{ $subscription->company->name }}" class="rounded-circle" 
                                                     style="width: 32px; height: 32px; object-fit: cover;">
                                            @else
                                                <div class="bg-gradient-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 32px; height: 32px;">
                                                    {{ strtoupper(substr($subscription->company->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $subscription->company->name }}</div>
                                            <small class="text-muted">{{ $subscription->company->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $subscription->status === 'active' ? 'success' : ($subscription->status === 'trial' ? 'info' : 'secondary') }}">
                                        {{ ucfirst($subscription->status) }}
                                    </span>
                                </td>
                                <td>{{ $subscription->starts_at?->format('M d, Y') }}</td>
                                <td>{{ $subscription->next_billing_at?->format('M d, Y') }}</td>
                                <td class="fw-bold text-success">£{{ number_format($subscription->amount, 0) }}</td>
                                <td>
                                    <a href="{{ route('superadmin.subscriptions.show', $subscription) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($plan->subscriptions()->count() > 10)
                <div class="text-center mt-3">
                    <a href="{{ route('superadmin.subscriptions.index', ['plan' => $plan->slug]) }}" 
                       class="btn btn-outline-primary">
                        View All {{ $plan->subscriptions()->count() }} Subscriptions
                    </a>
                </div>
            @endif
        </div>
    </div>
@endif
@endsection
