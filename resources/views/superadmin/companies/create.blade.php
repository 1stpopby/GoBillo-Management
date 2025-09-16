@extends('layouts.app')

@section('title', 'Create Company')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Create New Company</h1>
        <p class="text-muted mb-0">Set up a new company with admin user</p>
    </div>
    <a href="{{ route('superadmin.companies.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Companies
    </a>
</div>

<form method="POST" action="{{ route('superadmin.companies.store') }}">
    @csrf
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
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Company Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                   id="website" name="website" value="{{ old('website') }}" placeholder="https://example.com">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" name="address" rows="2">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                   id="city" name="city" value="{{ old('city') }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="state" class="form-label">State</label>
                            <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                   id="state" name="state" value="{{ old('state') }}">
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="zip_code" class="form-label">ZIP Code</label>
                            <input type="text" class="form-control @error('zip_code') is-invalid @enderror" 
                                   id="zip_code" name="zip_code" value="{{ old('zip_code') }}">
                            @error('zip_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                            <select class="form-select @error('country') is-invalid @enderror" id="country" name="country" required>
                                <option value="US" {{ old('country', 'US') == 'US' ? 'selected' : '' }}>United States</option>
                                <option value="CA" {{ old('country') == 'CA' ? 'selected' : '' }}>Canada</option>
                                <option value="GB" {{ old('country') == 'GB' ? 'selected' : '' }}>United Kingdom</option>
                                <option value="AU" {{ old('country') == 'AU' ? 'selected' : '' }}>Australia</option>
                            </select>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Company Admin User -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Company Admin User</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="admin_name" class="form-label">Admin Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('admin_name') is-invalid @enderror" 
                                   id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required>
                            @error('admin_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="admin_email" class="form-label">Admin Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('admin_email') is-invalid @enderror" 
                                   id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required>
                            @error('admin_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="admin_password" class="form-label">Admin Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('admin_password') is-invalid @enderror" 
                                   id="admin_password" name="admin_password" required>
                            @error('admin_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="admin_password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" 
                                   id="admin_password_confirmation" name="admin_password_confirmation" required>
                        </div>
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
                            <option value="trial" {{ old('subscription_plan', 'trial') == 'trial' ? 'selected' : '' }}>Trial (30 days)</option>
                            <option value="basic" {{ old('subscription_plan') == 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="professional" {{ old('subscription_plan') == 'professional' ? 'selected' : '' }}>Professional</option>
                            <option value="enterprise" {{ old('subscription_plan') == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                        </select>
                        @error('subscription_plan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="max_users" class="form-label">Max Users <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('max_users') is-invalid @enderror" 
                               id="max_users" name="max_users" value="{{ old('max_users', 10) }}" min="1" max="1000" required>
                        @error('max_users')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="max_projects" class="form-label">Max Projects <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('max_projects') is-invalid @enderror" 
                               id="max_projects" name="max_projects" value="{{ old('max_projects', 15) }}" min="1" max="10000" required>
                        @error('max_projects')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Quick Tips -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-info-circle text-primary"></i>
                            The company admin will be able to manage users and projects
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-shield-check text-success"></i>
                            Trial accounts get 30 days free access
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-gear text-info"></i>
                            Limits can be adjusted later from the company details
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-envelope text-warning"></i>
                            Admin will receive login credentials via email
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('superadmin.companies.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create Company
        </button>
    </div>
</form>
@endsection 