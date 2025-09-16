@extends('layouts.superadmin')

@section('title', 'Edit Membership Plan')
@section('page-title', 'Edit Membership Plan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Edit Membership Plan</h1>
        <p class="text-muted mb-0">Modify the {{ $plan->name }} subscription plan</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('superadmin.plans.show', $plan) }}" class="btn btn-outline-info">
            <i class="bi bi-eye"></i> View Plan
        </a>
        <a href="{{ route('superadmin.plans.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Plans
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Plan Details</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('superadmin.plans.update', $plan) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">Plan Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $plan->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="slug" class="form-label">Plan Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" name="slug" value="{{ old('slug', $plan->slug) }}" required>
                            <small class="text-muted">URL-friendly identifier (e.g., starter, professional)</small>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $plan->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pricing -->
                        <div class="col-md-4">
                            <label for="monthly_price" class="form-label">Monthly Price (£) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('monthly_price') is-invalid @enderror" 
                                   id="monthly_price" name="monthly_price" value="{{ old('monthly_price', $plan->monthly_price) }}" 
                                   step="0.01" min="0" required>
                            @error('monthly_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="yearly_price" class="form-label">Yearly Price (£)</label>
                            <input type="number" class="form-control @error('yearly_price') is-invalid @enderror" 
                                   id="yearly_price" name="yearly_price" value="{{ old('yearly_price', $plan->yearly_price) }}" 
                                   step="0.01" min="0">
                            <small class="text-muted">Leave blank to disable yearly billing</small>
                            @error('yearly_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="setup_fee" class="form-label">Setup Fee (£)</label>
                            <input type="number" class="form-control @error('setup_fee') is-invalid @enderror" 
                                   id="setup_fee" name="setup_fee" value="{{ old('setup_fee', $plan->setup_fee) }}" 
                                   step="0.01" min="0">
                            @error('setup_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Limits -->
                        <div class="col-md-3">
                            <label for="max_users" class="form-label">Max Users</label>
                            <input type="number" class="form-control @error('max_users') is-invalid @enderror" 
                                   id="max_users" name="max_users" value="{{ old('max_users', $plan->max_users) }}" min="0">
                            <small class="text-muted">0 = Unlimited</small>
                            @error('max_users')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="max_sites" class="form-label">Max Sites</label>
                            <input type="number" class="form-control @error('max_sites') is-invalid @enderror" 
                                   id="max_sites" name="max_sites" value="{{ old('max_sites', $plan->max_sites) }}" min="0">
                            <small class="text-muted">0 = Unlimited</small>
                            @error('max_sites')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="max_projects" class="form-label">Max Projects</label>
                            <input type="number" class="form-control @error('max_projects') is-invalid @enderror" 
                                   id="max_projects" name="max_projects" value="{{ old('max_projects', $plan->max_projects) }}" min="0">
                            <small class="text-muted">0 = Unlimited</small>
                            @error('max_projects')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="max_storage_gb" class="form-label">Max Storage (GB)</label>
                            <input type="number" class="form-control @error('max_storage_gb') is-invalid @enderror" 
                                   id="max_storage_gb" name="max_storage_gb" value="{{ old('max_storage_gb', $plan->max_storage_gb) }}" min="0">
                            <small class="text-muted">0 = Unlimited</small>
                            @error('max_storage_gb')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Trial -->
                        <div class="col-md-6">
                            <label for="trial_days" class="form-label">Trial Days</label>
                            <input type="number" class="form-control @error('trial_days') is-invalid @enderror" 
                                   id="trial_days" name="trial_days" value="{{ old('trial_days', $plan->trial_days) }}" min="0">
                            @error('trial_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', $plan->sort_order) }}" min="0">
                            <small class="text-muted">Lower numbers appear first</small>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Features -->
                        <div class="col-12">
                            <label class="form-label">Features</label>
                            <div class="row g-2">
                                @php
                                    $features = [
                                        'project_management' => 'Project Management',
                                        'user_management' => 'User Management',
                                        'document_storage' => 'Document Storage',
                                        'reporting' => 'Advanced Reporting',
                                        'api_access' => 'API Access',
                                        'custom_branding' => 'Custom Branding',
                                        'priority_support' => 'Priority Support',
                                        'integrations' => 'Third-party Integrations',
                                        'advanced_permissions' => 'Advanced Permissions',
                                        'audit_logs' => 'Audit Logs',
                                        'backup_restore' => 'Backup & Restore',
                                        'white_labeling' => 'White Labeling',
                                        'sso' => 'Single Sign-On (SSO)',
                                        'advanced_analytics' => 'Advanced Analytics',
                                        'mobile_app' => 'Mobile App Access'
                                    ];
                                    $planFeatures = old('features', $plan->features ?? []);
                                @endphp
                                @foreach($features as $key => $feature)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="feature_{{ $key }}" name="features[]" value="{{ $key }}"
                                                   {{ in_array($key, $planFeatures) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="feature_{{ $key }}">
                                                {{ $feature }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active (Available for new subscriptions)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('superadmin.plans.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-circle me-1"></i>Update Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Current Plan Info -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Current Plan Info</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h4 class="mb-1">{{ $plan->name }}</h4>
                    <p class="text-muted small mb-0">{{ $plan->description ?: 'No description' }}</p>
                </div>
                
                <div class="text-center mb-3">
                    <div class="display-6 fw-bold text-primary">£{{ number_format($plan->monthly_price, 2) }}</div>
                    <small class="text-muted">per month</small>
                    @if($plan->yearly_price)
                        <div class="mt-1">
                            <small class="text-success">£{{ number_format($plan->yearly_price, 2) }}/year</small>
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <h6 class="small fw-bold text-uppercase text-muted mb-2">Current Limits</h6>
                    <ul class="list-unstyled small">
                        <li><i class="bi bi-people me-2"></i>{{ $plan->max_users ?: 'Unlimited' }} Users</li>
                        <li><i class="bi bi-geo-alt me-2"></i>{{ $plan->max_sites ?: 'Unlimited' }} Sites</li>
                        <li><i class="bi bi-kanban me-2"></i>{{ $plan->max_projects ?: 'Unlimited' }} Projects</li>
                        <li><i class="bi bi-hdd me-2"></i>{{ $plan->max_storage_gb ?: 'Unlimited' }} GB Storage</li>
                    </ul>
                </div>

                <div class="mb-3">
                    <h6 class="small fw-bold text-uppercase text-muted mb-2">Status</h6>
                    <span class="badge bg-{{ $plan->is_active ? 'success' : 'secondary' }}">
                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                @if($plan->features && count($plan->features) > 0)
                    <div>
                        <h6 class="small fw-bold text-uppercase text-muted mb-2">Features</h6>
                        <ul class="list-unstyled small">
                            @foreach($plan->features as $feature)
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>{{ ucwords(str_replace('_', ' ', $feature)) }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        <!-- Usage Stats -->
        @if($plan->subscriptions()->count() > 0)
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Usage Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary mb-1">{{ $plan->subscriptions()->count() }}</h4>
                                <small class="text-muted">Total Subscriptions</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success mb-1">{{ $plan->subscriptions()->whereIn('status', ['active', 'trial'])->count() }}</h4>
                            <small class="text-muted">Active</small>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Auto-generate slug from name (but don't override if manually changed)
let slugManuallyChanged = false;

document.getElementById('slug').addEventListener('input', function() {
    slugManuallyChanged = true;
});

document.getElementById('name').addEventListener('input', function() {
    if (!slugManuallyChanged) {
        const name = this.value;
        const slug = name.toLowerCase()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/[\s_-]+/g, '-')
                        .replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = slug;
    }
});

// Calculate yearly price suggestion
document.getElementById('monthly_price').addEventListener('input', function() {
    const monthlyPrice = parseFloat(this.value) || 0;
    const suggestedYearly = (monthlyPrice * 12 * 0.85).toFixed(2); // 15% discount
    
    const yearlyField = document.getElementById('yearly_price');
    if (!yearlyField.value || yearlyField.value === '0.00') {
        yearlyField.placeholder = 'Suggested: £' + suggestedYearly + ' (15% discount)';
    }
});
</script>
@endpush

@push('styles')
<style>
.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.list-unstyled li {
    padding: 2px 0;
}
</style>
@endpush
@endsection
