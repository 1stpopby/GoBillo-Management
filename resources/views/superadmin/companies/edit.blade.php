@extends('layouts.app')

@section('title', 'Edit Company')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Edit Company</h1>
        <p class="text-muted mb-0">Update {{ $company->name }} details</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('superadmin.companies.show', $company) }}" class="btn btn-outline-secondary">
            <i class="bi bi-eye"></i> View Details
        </a>
        <a href="{{ route('superadmin.companies.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Companies
        </a>
    </div>
</div>

<form method="POST" action="{{ route('superadmin.companies.update', $company) }}">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Company Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Company Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Company Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $company->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Company Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $company->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $company->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                   id="website" name="website" value="{{ old('website', $company->website) }}" 
                                   placeholder="https://example.com">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" name="address" rows="2">{{ old('address', $company->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                   id="city" name="city" value="{{ old('city', $company->city) }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="state" class="form-label">State</label>
                            <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                   id="state" name="state" value="{{ old('state', $company->state) }}">
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="zip_code" class="form-label">ZIP Code</label>
                            <input type="text" class="form-control @error('zip_code') is-invalid @enderror" 
                                   id="zip_code" name="zip_code" value="{{ old('zip_code', $company->zip_code) }}">
                            @error('zip_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                            <select class="form-select @error('country') is-invalid @enderror" id="country" name="country" required>
                                <option value="US" {{ old('country', $company->country) == 'US' ? 'selected' : '' }}>United States</option>
                                <option value="CA" {{ old('country', $company->country) == 'CA' ? 'selected' : '' }}>Canada</option>
                                <option value="GB" {{ old('country', $company->country) == 'GB' ? 'selected' : '' }}>United Kingdom</option>
                                <option value="AU" {{ old('country', $company->country) == 'AU' ? 'selected' : '' }}>Australia</option>
                            </select>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $company->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $company->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status', $company->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $company->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Subscription Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Subscription Settings</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="subscription_plan" class="form-label">Plan <span class="text-danger">*</span></label>
                        <select class="form-select @error('subscription_plan') is-invalid @enderror" 
                                id="subscription_plan" name="subscription_plan" required>
                            <option value="trial" {{ old('subscription_plan', $company->subscription_plan) == 'trial' ? 'selected' : '' }}>Trial</option>
                            <option value="basic" {{ old('subscription_plan', $company->subscription_plan) == 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="professional" {{ old('subscription_plan', $company->subscription_plan) == 'professional' ? 'selected' : '' }}>Professional</option>
                            <option value="enterprise" {{ old('subscription_plan', $company->subscription_plan) == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                        </select>
                        @error('subscription_plan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="trial_ends_at" class="form-label">Trial End Date</label>
                        <input type="date" class="form-control @error('trial_ends_at') is-invalid @enderror" 
                               id="trial_ends_at" name="trial_ends_at" 
                               value="{{ old('trial_ends_at', $company->trial_ends_at?->format('Y-m-d')) }}">
                        @error('trial_ends_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Leave empty if not on trial</div>
                    </div>

                    <div class="mb-3">
                        <label for="subscription_ends_at" class="form-label">Subscription End Date</label>
                        <input type="date" class="form-control @error('subscription_ends_at') is-invalid @enderror" 
                               id="subscription_ends_at" name="subscription_ends_at" 
                               value="{{ old('subscription_ends_at', $company->subscription_ends_at?->format('Y-m-d')) }}">
                        @error('subscription_ends_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Leave empty for unlimited</div>
                    </div>

                    <div class="mb-3">
                        <label for="max_users" class="form-label">Max Users <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('max_users') is-invalid @enderror" 
                               id="max_users" name="max_users" value="{{ old('max_users', $company->max_users) }}" 
                               min="1" max="1000" required>
                        @error('max_users')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Current users: {{ $company->users_count }}</div>
                    </div>

                    <div class="mb-3">
                        <label for="max_projects" class="form-label">Max Projects <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('max_projects') is-invalid @enderror" 
                               id="max_projects" name="max_projects" value="{{ old('max_projects', $company->max_projects) }}" 
                               min="1" max="10000" required>
                        @error('max_projects')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Current projects: {{ $company->projects_count }}</div>
                    </div>
                </div>
            </div>

            <!-- Current Statistics -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Current Usage</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Users</span>
                            <span>{{ $company->users_count }}/{{ $company->max_users }}</span>
                        </div>
                        <div class="progress mt-1">
                            <div class="progress-bar" style="width: {{ $company->max_users > 0 ? ($company->users_count / $company->max_users * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Projects</span>
                            <span>{{ $company->projects_count }}/{{ $company->max_projects }}</span>
                        </div>
                        <div class="progress mt-1">
                            <div class="progress-bar bg-success" style="width: {{ $company->max_projects > 0 ? ($company->projects_count / $company->max_projects * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div class="text-muted small">
                        <div>Clients: {{ $company->clients_count }}</div>
                        <div>Tasks: {{ $company->tasks_count }}</div>
                        <div>Created: {{ $company->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Warning -->
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h6 class="card-title mb-0"><i class="bi bi-exclamation-triangle"></i> Important</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2">• Reducing user/project limits below current usage may cause issues</li>
                        <li class="mb-2">• Status changes affect user access immediately</li>
                        <li class="mb-0">• Subscription changes should be coordinated with billing</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('superadmin.companies.show', $company) }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle"></i> Update Company
        </button>
    </div>
</form>
@endsection 