@extends('layouts.app')

@section('title', 'Edit Employee - ' . $user->name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
                        <li class="breadcrumb-item active">Edit {{ $user->name }}</li>
                    </ol>
                </nav>
                <h1 class="page-title">Edit Employee</h1>
                <p class="page-subtitle">Update employee information and permissions</p>
            </div>
            <div class="col-lg-4 text-end">
                <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Employees
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Employee Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('employees.update-user', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    @foreach($roles as $value => $label)
                                        <option value="{{ $value }}" {{ old('role', $user->role) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <strong>Active Employee</strong>
                                        <small class="text-muted d-block">Inactive employees cannot access the system</small>
                                    </label>
                                </div>
                            </div>

                            <!-- Password Section -->
                            <div class="col-12">
                                <hr class="my-4">
                                <h6 class="text-muted mb-3">Password Settings</h6>
                                <p class="text-muted small">Leave password fields empty to keep the current password unchanged.</p>
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Minimum 8 characters</small>
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Employee
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Employee Information Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Employee Details</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <strong>Employee ID:</strong>
                            <div class="text-muted">{{ $user->id }}</div>
                        </div>
                        <div class="col-md-4">
                            <strong>Joined:</strong>
                            <div class="text-muted">{{ $user->created_at->format('M j, Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <strong>Last Updated:</strong>
                            <div class="text-muted">{{ $user->updated_at->format('M j, Y g:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password confirmation validation
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    
    function validatePasswordMatch() {
        if (password.value && passwordConfirmation.value) {
            if (password.value !== passwordConfirmation.value) {
                passwordConfirmation.setCustomValidity('Passwords do not match');
                passwordConfirmation.classList.add('is-invalid');
            } else {
                passwordConfirmation.setCustomValidity('');
                passwordConfirmation.classList.remove('is-invalid');
            }
        } else {
            passwordConfirmation.setCustomValidity('');
            passwordConfirmation.classList.remove('is-invalid');
        }
    }
    
    password.addEventListener('input', validatePasswordMatch);
    passwordConfirmation.addEventListener('input', validatePasswordMatch);
});
</script>
@endpush
