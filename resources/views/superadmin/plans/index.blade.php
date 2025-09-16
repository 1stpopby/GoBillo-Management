@extends('layouts.superadmin')

@section('title', 'Membership Plans')
@section('page-title', 'Membership Plans')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted mb-0">Create and manage subscription plans for companies</p>
    </div>
    <a href="{{ route('superadmin.plans.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>New Plan
    </a>
</div>

<!-- Plans Overview Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-layers text-primary mb-2" style="font-size: 2rem;"></i>
                <h3 class="mb-1">{{ $plans->count() }}</h3>
                <p class="text-muted mb-0">Total Plans</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                <h3 class="mb-1">{{ $plans->where('is_active', true)->count() }}</h3>
                <p class="text-muted mb-0">Active Plans</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-star text-warning mb-2" style="font-size: 2rem;"></i>
                <h3 class="mb-1">{{ $plans->where('is_featured', true)->count() }}</h3>
                <p class="text-muted mb-0">Featured Plans</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-buildings text-info mb-2" style="font-size: 2rem;"></i>
                <h3 class="mb-1">{{ $plans->sum('companies_count') }}</h3>
                <p class="text-muted mb-0">Total Subscriptions</p>
            </div>
        </div>
    </div>
</div>

<!-- Plans List -->
<div class="row g-4">
    @forelse($plans as $plan)
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100 {{ $plan->is_featured ? 'border-warning' : '' }}">
                @if($plan->is_featured)
                    <div class="card-header bg-warning text-dark text-center py-2">
                        <i class="bi bi-star me-1"></i><strong>FEATURED</strong>
                    </div>
                @endif
                
                <div class="card-body d-flex flex-column">
                    <div class="text-center mb-3">
                        <h4 class="mb-1">{{ $plan->name }}</h4>
                        <p class="text-muted small">{{ $plan->description }}</p>
                    </div>

                    <!-- Pricing -->
                    <div class="text-center mb-3">
                        <div class="d-flex justify-content-center align-items-baseline">
                            <span class="h2 mb-0">£{{ number_format($plan->monthly_price, 0) }}</span>
                            <span class="text-muted">/month</span>
                        </div>
                        @if($plan->yearly_price > 0 && $plan->yearly_savings_percentage > 0)
                            <small class="text-success">
                                Save {{ $plan->yearly_savings_percentage }}% with yearly billing
                            </small>
                        @endif
                    </div>

                    <!-- Limits -->
                    <div class="mb-3">
                        <ul class="list-unstyled small">
                            <li class="mb-1">
                                <i class="bi bi-people text-primary me-2"></i>
                                {{ $plan->max_users > 0 ? $plan->max_users . ' users' : 'Unlimited users' }}
                            </li>
                            <li class="mb-1">
                                <i class="bi bi-geo-alt text-primary me-2"></i>
                                {{ $plan->max_sites > 0 ? $plan->max_sites . ' sites' : 'Unlimited sites' }}
                            </li>
                            <li class="mb-1">
                                <i class="bi bi-folder text-primary me-2"></i>
                                {{ $plan->max_projects > 0 ? $plan->max_projects . ' projects' : 'Unlimited projects' }}
                            </li>
                            <li class="mb-1">
                                <i class="bi bi-hdd text-primary me-2"></i>
                                {{ $plan->max_storage_gb }}GB storage
                            </li>
                        </ul>
                    </div>

                    <!-- Features -->
                    <div class="mb-3 flex-grow-1">
                        <div class="row g-2">
                            @if($plan->has_time_tracking)
                                <div class="col-6"><small><i class="bi bi-check text-success me-1"></i>Time Tracking</small></div>
                            @endif
                            @if($plan->has_invoicing)
                                <div class="col-6"><small><i class="bi bi-check text-success me-1"></i>Invoicing</small></div>
                            @endif
                            @if($plan->has_reporting)
                                <div class="col-6"><small><i class="bi bi-check text-success me-1"></i>Reporting</small></div>
                            @endif
                            @if($plan->has_api_access)
                                <div class="col-6"><small><i class="bi bi-check text-success me-1"></i>API Access</small></div>
                            @endif
                            @if($plan->has_white_label)
                                <div class="col-6"><small><i class="bi bi-check text-success me-1"></i>White Label</small></div>
                            @endif
                            @if($plan->has_priority_support)
                                <div class="col-6"><small><i class="bi bi-check text-success me-1"></i>Priority Support</small></div>
                            @endif
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="border-top pt-3 mb-3">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="small text-muted">Companies</div>
                                <div class="fw-bold">{{ $plan->companies_count }}</div>
                            </div>
                            <div class="col-6">
                                <div class="small text-muted">Subscriptions</div>
                                <div class="fw-bold">{{ $plan->subscriptions_count }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="text-center mb-3">
                        <span class="badge bg-{{ $plan->is_active ? 'success' : 'secondary' }}">
                            {{ $plan->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($plan->is_trial_available)
                            <span class="badge bg-info">{{ $plan->trial_days }}-day trial</span>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="mt-auto">
                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('superadmin.plans.edit', $plan) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="{{ route('superadmin.plans.show', $plan) }}" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-eye"></i>
                            </a>
                            <form method="POST" action="{{ route('superadmin.plans.toggle-status', $plan) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-{{ $plan->is_active ? 'warning' : 'success' }} btn-sm">
                                    <i class="bi bi-{{ $plan->is_active ? 'pause' : 'play' }}"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-layers text-muted mb-3" style="font-size: 3rem;"></i>
                    <h5 class="text-muted">No membership plans found</h5>
                    <p class="text-muted">Create your first membership plan to get started.</p>
                    <a href="{{ route('superadmin.plans.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create First Plan
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>
@endsection

@push('styles')
<style>
.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.border-warning {
    border-left: 4px solid #ffc107 !important;
}
</style>
@endpush
