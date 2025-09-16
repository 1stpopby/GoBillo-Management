@extends('layouts.app')

@section('title', 'Membership')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-star me-2 text-warning"></i>Membership
            </h1>
            <p class="text-muted mb-0">Manage your subscription, billing, and account features</p>
        </div>
        <div>
            <span class="badge bg-{{ $company->subscription_status === 'active' ? 'success' : ($company->subscription_status === 'suspended' ? 'warning' : 'danger') }} fs-6">
                {{ ucfirst($company->subscription_status ?? 'Active') }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Subscription Details -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Subscription Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="bi bi-box text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Current Plan</h6>
                                    <p class="mb-0 text-muted">{{ ucfirst($company->subscription_plan ?? 'Trial') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="bi bi-calendar-check text-success fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Status</h6>
                                    <p class="mb-0 text-muted">{{ ucfirst($company->subscription_status ?? 'Active') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="bi bi-clock text-info fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Member Since</h6>
                                    <p class="mb-0 text-muted">{{ $company->created_at->format('F Y') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="bi bi-arrow-repeat text-warning fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Billing Cycle</h6>
                                    <p class="mb-0 text-muted">{{ ucfirst($company->billing_cycle ?? 'Monthly') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage Statistics -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Usage Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Users Usage -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">Users</h6>
                                    <small class="text-muted">
                                        {{ $userUsage['current'] }} / {{ $userUsage['limit'] >= 999999 ? 'Unlimited' : $userUsage['limit'] }}
                                    </small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $userUsage['percentage'] > 80 ? 'danger' : ($userUsage['percentage'] > 60 ? 'warning' : 'success') }}" 
                                         role="progressbar" 
                                         style="width: {{ $userUsage['percentage'] }}%"
                                         aria-valuenow="{{ $userUsage['percentage'] }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Projects Usage -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">Projects</h6>
                                    <small class="text-muted">
                                        {{ $projectUsage['current'] }} / {{ $projectUsage['limit'] >= 999999 ? 'Unlimited' : $projectUsage['limit'] }}
                                    </small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $projectUsage['percentage'] > 80 ? 'danger' : ($projectUsage['percentage'] > 60 ? 'warning' : 'info') }}" 
                                         role="progressbar" 
                                         style="width: {{ $projectUsage['percentage'] }}%"
                                         aria-valuenow="{{ $projectUsage['percentage'] }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-primary mb-1">{{ $company->users()->count() }}</h4>
                                <small class="text-muted">Active Users</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-success mb-1">{{ $company->projects()->where('status', 'active')->count() }}</h4>
                                <small class="text-muted">Active Projects</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-info mb-1">{{ $company->clients()->count() }}</h4>
                                <small class="text-muted">Total Clients</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-warning mb-1">{{ $company->sites()->count() }}</h4>
                                <small class="text-muted">Total Sites</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            @if(auth()->user()->isCompanyAdmin())
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-credit-card-2-front me-2"></i>Payment Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('membership.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method" disabled>
                                    <option value="credit_card" {{ ($company->payment_method ?? 'credit_card') === 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                    <option value="debit_card" {{ ($company->payment_method ?? '') === 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                                    <option value="bank_transfer" {{ ($company->payment_method ?? '') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="direct_debit" {{ ($company->payment_method ?? '') === 'direct_debit' ? 'selected' : '' }}>Direct Debit</option>
                                </select>
                                <small class="text-muted">Contact support to change payment method</small>
                            </div>
                            <div class="col-md-6">
                                <label for="next_billing_date" class="form-label">Next Billing Date</label>
                                <input type="date" class="form-control" id="next_billing_date" name="next_billing_date" 
                                       value="{{ $company->next_billing_date?->format('Y-m-d') ?? now()->addMonth()->format('Y-m-d') }}" readonly>
                            </div>
                            <div class="col-12">
                                <label for="billing_contact_email" class="form-label">Billing Contact Email</label>
                                <input type="email" class="form-control @error('billing_contact_email') is-invalid @enderror" 
                                       id="billing_contact_email" name="billing_contact_email" 
                                       value="{{ old('billing_contact_email', $company->billing_contact_email ?? $company->email) }}">
                                @error('billing_contact_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i>Update Billing Info
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Plan Overview -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>Plan Overview</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <div class="display-6 text-primary mb-2">
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <h4 class="mb-1">{{ ucfirst($company->subscription_plan ?? 'Trial') }}</h4>
                        <p class="text-muted mb-0">Current Plan</p>
                    </div>
                    
                    <div class="mb-4">
                        @php
                            $currentPlanPrice = $availablePlans[$company->subscription_plan ?? 'trial']['price'] ?? 0;
                        @endphp
                        <h2 class="text-primary mb-1">£{{ number_format($currentPlanPrice, 2) }}</h2>
                        <small class="text-muted">per month</small>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#upgradePlanModal">
                            <i class="bi bi-arrow-up-circle me-2"></i>Upgrade Plan
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#billingHistoryModal">
                            <i class="bi bi-receipt me-2"></i>Billing History
                        </button>
                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#usageReportModal">
                            <i class="bi bi-graph-up me-2"></i>Usage Report
                        </button>
                    </div>
                </div>
            </div>

            <!-- Additional Services -->
            @if(auth()->user()->isCompanyAdmin())
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Additional Services</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('membership.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="premium_support" name="premium_support" 
                                   {{ old('premium_support', $company->premium_support ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="premium_support">
                                <strong>Premium Support</strong>
                                <small class="d-block text-muted">24/7 priority support</small>
                                <small class="d-block text-primary fw-bold">+£29/month</small>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="advanced_reporting" name="advanced_reporting" 
                                   {{ old('advanced_reporting', $company->advanced_reporting ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="advanced_reporting">
                                <strong>Advanced Reporting</strong>
                                <small class="d-block text-muted">Custom reports & analytics</small>
                                <small class="d-block text-primary fw-bold">+£19/month</small>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="api_access" name="api_access" 
                                   {{ old('api_access', $company->api_access ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="api_access">
                                <strong>API Access</strong>
                                <small class="d-block text-muted">Integration capabilities</small>
                                <small class="d-block text-primary fw-bold">+£39/month</small>
                            </label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-check-circle me-2"></i>Update Services
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- Support & Help -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0"><i class="bi bi-question-circle me-2"></i>Support & Help</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="mailto:support@gobillo.com" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-envelope me-2"></i>Contact Support
                        </a>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="alert('Knowledge base coming soon!')">
                            <i class="bi bi-book me-2"></i>Knowledge Base
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="alert('Live chat feature coming soon!')">
                            <i class="bi bi-chat-dots me-2"></i>Live Chat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upgrade Plan Modal -->
<div class="modal fade" id="upgradePlanModal" tabindex="-1" aria-labelledby="upgradePlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="upgradePlanModalLabel">
                    <i class="bi bi-arrow-up-circle me-2"></i>Upgrade Your Plan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">Choose the plan that best fits your business needs. You can upgrade or downgrade at any time.</p>
                
                <div class="row g-3">
                    @foreach($availablePlans as $planKey => $plan)
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 {{ ($company->subscription_plan ?? 'trial') === $planKey ? 'border-primary' : '' }} {{ $planKey === 'professional' ? 'position-relative' : '' }}">
                                @if($planKey === 'professional')
                                    <div class="badge bg-warning text-dark position-absolute top-0 start-50 translate-middle px-3 py-1">Popular</div>
                                @endif
                                <div class="card-header text-center {{ ($company->subscription_plan ?? 'trial') === $planKey ? 'bg-primary text-white' : 'bg-light' }}">
                                    <h6 class="mb-0">{{ $plan['name'] }}</h6>
                                    @if(($company->subscription_plan ?? 'trial') === $planKey)
                                        <small class="badge bg-light text-primary">Current Plan</small>
                                    @endif
                                </div>
                                <div class="card-body text-center">
                                    <h4 class="text-primary mb-2">£{{ $plan['price'] }}</h4>
                                    <small class="text-muted">per month</small>
                                    <p class="small text-muted mt-2">{{ $plan['description'] }}</p>
                                    <hr>
                                    <ul class="list-unstyled small">
                                        @foreach($plan['features'] as $feature)
                                            <li><i class="bi bi-check text-success me-2"></i>{{ $feature }}</li>
                                        @endforeach
                                    </ul>
                                    @if(($company->subscription_plan ?? 'trial') !== $planKey)
                                        <button class="btn btn-primary btn-sm" onclick="selectPlan('{{ $planKey }}', {{ $plan['price'] }})">Select {{ $plan['name'] }}</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="mailto:support@gobillo.com?subject=Plan Upgrade Request" class="btn btn-primary">
                    <i class="bi bi-envelope me-2"></i>Contact Support
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Billing History Modal -->
<div class="modal fade" id="billingHistoryModal" tabindex="-1" aria-labelledby="billingHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="billingHistoryModalLabel">
                    <i class="bi bi-receipt me-2"></i>Billing History
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-receipt text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="text-muted mb-3">No Billing History Available</h5>
                    <p class="text-muted mb-4">
                        Your billing history will appear here once you have subscription charges.<br>
                        Currently on <strong>{{ ucfirst($company->subscription_plan ?? 'Trial') }}</strong> plan.
                    </p>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Note:</strong> For billing inquiries or to set up payment methods, please contact our support team.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="mailto:billing@gobillo.com?subject=Billing Inquiry" class="btn btn-success">
                    <i class="bi bi-envelope me-2"></i>Contact Billing
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Usage Report Modal -->
<div class="modal fade" id="usageReportModal" tabindex="-1" aria-labelledby="usageReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="usageReportModalLabel">
                    <i class="bi bi-graph-up me-2"></i>Detailed Usage Report
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <!-- Current Usage Overview -->
                    <div class="col-12">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="card-title mb-3">
                                    <i class="bi bi-speedometer2 me-2"></i>Current Usage Overview
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold">Users</span>
                                            <span class="text-muted">{{ $userUsage['current'] }} / {{ $userUsage['limit'] >= 999999 ? 'Unlimited' : $userUsage['limit'] }}</span>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-{{ $userUsage['percentage'] > 80 ? 'danger' : ($userUsage['percentage'] > 60 ? 'warning' : 'success') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $userUsage['percentage'] }}%">
                                                {{ $userUsage['percentage'] }}%
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold">Projects</span>
                                            <span class="text-muted">{{ $projectUsage['current'] }} / {{ $projectUsage['limit'] >= 999999 ? 'Unlimited' : $projectUsage['limit'] }}</span>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-{{ $projectUsage['percentage'] > 80 ? 'danger' : ($projectUsage['percentage'] > 60 ? 'warning' : 'info') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $projectUsage['percentage'] }}%">
                                                {{ $projectUsage['percentage'] }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Statistics -->
                    <div class="col-md-6">
                        <div class="card border-0">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="bi bi-people me-2"></i>User Statistics</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-primary mb-1">{{ $company->users()->where('role', 'company_admin')->count() }}</h4>
                                            <small class="text-muted">Admins</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-info mb-1">{{ $company->users()->where('role', 'site_manager')->count() }}</h4>
                                            <small class="text-muted">Managers</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-success mb-1">{{ $company->users()->where('role', 'operative')->count() }}</h4>
                                            <small class="text-muted">Operatives</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-warning mb-1">{{ $company->users()->where('is_active', true)->count() }}</h4>
                                            <small class="text-muted">Active</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="bi bi-briefcase me-2"></i>Project Statistics</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-success mb-1">{{ $company->projects()->where('status', 'active')->count() }}</h4>
                                            <small class="text-muted">Active</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-primary mb-1">{{ $company->projects()->where('status', 'completed')->count() }}</h4>
                                            <small class="text-muted">Completed</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-warning mb-1">{{ $company->projects()->where('status', 'on_hold')->count() }}</h4>
                                            <small class="text-muted">On Hold</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h4 class="text-info mb-1">{{ $company->projects()->where('status', 'planning')->count() }}</h4>
                                            <small class="text-muted">Planning</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Metrics -->
                    <div class="col-12">
                        <div class="card border-0">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Additional Metrics</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-primary mb-1">{{ $company->clients()->count() }}</h4>
                                            <small class="text-muted">Total Clients</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-success mb-1">{{ $company->sites()->count() }}</h4>
                                            <small class="text-muted">Total Sites</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-info mb-1">{{ $company->projects()->sum('budget') ? '£' . number_format($company->projects()->sum('budget')) : 'N/A' }}</h4>
                                            <small class="text-muted">Total Budget</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-warning mb-1">{{ $company->created_at->diffInDays(now()) }}</h4>
                                            <small class="text-muted">Days Active</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info" onclick="exportUsageReport()">
                    <i class="bi bi-download me-2"></i>Export Report
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Plan Selection Function
function selectPlan(planName, price) {
    const message = `You have selected the ${planName.charAt(0).toUpperCase() + planName.slice(1)} plan (£${price}/month).\n\n` +
                   `This will:\n` +
                   `• Change your subscription immediately\n` +
                   `• Update your next billing amount\n` +
                   `• Adjust your account limits\n\n` +
                   `Would you like to continue? You'll be redirected to contact support to complete the change.`;
    
    if (confirm(message)) {
        window.location.href = `mailto:support@gobillo.com?subject=Plan Change Request - ${planName.charAt(0).toUpperCase() + planName.slice(1)}&body=Hello,%0D%0A%0D%0AI would like to change my subscription plan to ${planName.charAt(0).toUpperCase() + planName.slice(1)} (£${price}/month).%0D%0A%0D%0ACompany: {{ $company->name }}%0D%0AAccount Email: {{ auth()->user()->email }}%0D%0ACurrent Plan: {{ ucfirst($company->subscription_plan ?? 'Trial') }}%0D%0A%0D%0APlease process this change and let me know the next steps.%0D%0A%0D%0AThank you!`;
    }
}

// Billing History Functions
// No billing history available - contact support for billing inquiries

// Usage Report Functions
function exportUsageReport() {
    const reportData = {
        company: '{{ $company->name }}',
        plan: '{{ ucfirst($company->subscription_plan ?? "Trial") }}',
        status: '{{ ucfirst($company->subscription_status ?? "Active") }}',
        memberSince: '{{ $company->created_at->format("F Y") }}',
        users: {
            current: {{ $userUsage['current'] }},
            limit: {{ $userUsage['limit'] >= 999999 ? '"Unlimited"' : $userUsage['limit'] }},
            percentage: {{ $userUsage['percentage'] }},
            admins: {{ $company->users()->where('role', 'company_admin')->count() }},
            managers: {{ $company->users()->where('role', 'site_manager')->count() }},
            operatives: {{ $company->users()->where('role', 'operative')->count() }},
            active: {{ $company->users()->where('is_active', true)->count() }}
        },
        projects: {
            current: {{ $projectUsage['current'] }},
            limit: {{ $projectUsage['limit'] >= 999999 ? '"Unlimited"' : $projectUsage['limit'] }},
            percentage: {{ $projectUsage['percentage'] }},
            active: {{ $company->projects()->where('status', 'active')->count() }},
            completed: {{ $company->projects()->where('status', 'completed')->count() }},
            onHold: {{ $company->projects()->where('status', 'on_hold')->count() }},
            planning: {{ $company->projects()->where('status', 'planning')->count() }}
        },
        additional: {
            clients: {{ $company->clients()->count() }},
            sites: {{ $company->sites()->count() }},
            totalBudget: '{{ $company->projects()->sum("budget") ? "£" . number_format($company->projects()->sum("budget")) : "N/A" }}',
            daysActive: {{ $company->created_at->diffInDays(now()) }}
        },
        generated: new Date().toISOString()
    };
    
    // Create CSV content
    let csvContent = "GOBILLO USAGE REPORT\n";
    csvContent += `Generated: ${new Date().toLocaleString()}\n\n`;
    csvContent += `COMPANY INFORMATION\n`;
    csvContent += `Company Name,${reportData.company}\n`;
    csvContent += `Plan,${reportData.plan}\n`;
    csvContent += `Status,${reportData.status}\n`;
    csvContent += `Member Since,${reportData.memberSince}\n\n`;
    csvContent += `USER STATISTICS\n`;
    csvContent += `Current Users,${reportData.users.current}\n`;
    csvContent += `User Limit,${reportData.users.limit}\n`;
    csvContent += `Usage Percentage,${reportData.users.percentage}%\n`;
    csvContent += `Admins,${reportData.users.admins}\n`;
    csvContent += `Managers,${reportData.users.managers}\n`;
    csvContent += `Operatives,${reportData.users.operatives}\n`;
    csvContent += `Active Users,${reportData.users.active}\n\n`;
    csvContent += `PROJECT STATISTICS\n`;
    csvContent += `Current Projects,${reportData.projects.current}\n`;
    csvContent += `Project Limit,${reportData.projects.limit}\n`;
    csvContent += `Usage Percentage,${reportData.projects.percentage}%\n`;
    csvContent += `Active Projects,${reportData.projects.active}\n`;
    csvContent += `Completed Projects,${reportData.projects.completed}\n`;
    csvContent += `On Hold Projects,${reportData.projects.onHold}\n`;
    csvContent += `Planning Projects,${reportData.projects.planning}\n\n`;
    csvContent += `ADDITIONAL METRICS\n`;
    csvContent += `Total Clients,${reportData.additional.clients}\n`;
    csvContent += `Total Sites,${reportData.additional.sites}\n`;
    csvContent += `Total Budget,${reportData.additional.totalBudget}\n`;
    csvContent += `Days Active,${reportData.additional.daysActive}\n`;
    
    // Download CSV file
    const element = document.createElement('a');
    const file = new Blob([csvContent], {type: 'text/csv'});
    element.href = URL.createObjectURL(file);
    element.download = `usage-report-${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(element);
    element.click();
    document.body.removeChild(element);
    
    showNotification('success', 'Usage report exported successfully!');
}

// Notification System
function showNotification(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : (type === 'info' ? 'info-circle' : 'exclamation-triangle')} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
}

// Handle service checkbox changes
document.addEventListener('DOMContentLoaded', function() {
    const serviceCheckboxes = ['premium_support', 'advanced_reporting', 'api_access'];
    serviceCheckboxes.forEach(service => {
        const checkbox = document.getElementById(service);
        if (checkbox) {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    const serviceName = this.labels[0].querySelector('strong').textContent;
                    const price = this.labels[0].querySelector('.fw-bold').textContent;
                    if (!confirm(`Enable ${serviceName}? This will add ${price} to your next bill.`)) {
                        this.checked = false;
                    }
                }
            });
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.progress {
    background-color: #f8f9fa;
}

.card-header {
    font-weight: 600;
}

.display-6 {
    font-size: 3.5rem;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}
</style>
@endpush
@endsection
