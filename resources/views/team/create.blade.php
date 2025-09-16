@extends('layouts.app')

@section('title', 'Add Team Member')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('team.index') }}">Team</a></li>
                        <li class="breadcrumb-item active">Add Member</li>
                    </ol>
                </nav>
                <h1 class="page-title">Add Team Member</h1>
                <p class="page-subtitle">Add a new member to your team</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Team Member Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('team.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="">Select role...</option>
                                @foreach($roles as $value => $label)
                                    <option value="{{ $value }}" {{ old('role') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <!-- Operative Settings (since this form is now only for operatives) -->
                        <div id="operative-settings" class="operative-settings">
                            <hr class="my-4">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-person-workspace me-2"></i>Operative Settings
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="day_rate" class="form-label">Day Rate (£)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">£</span>
                                        <input type="number" step="0.01" min="0" max="9999.99" 
                                               class="form-control @error('day_rate') is-invalid @enderror" 
                                               id="day_rate" name="day_rate" 
                                               value="{{ old('day_rate') }}"
                                               placeholder="0.00">
                                        @error('day_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">Daily rate for invoice calculations</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input @error('cis_applicable') is-invalid @enderror" 
                                               id="cis_applicable" name="cis_applicable" value="1" 
                                               {{ old('cis_applicable') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cis_applicable">
                                            CIS Applicable
                                        </label>
                                        @error('cis_applicable')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">Check if Construction Industry Scheme applies</small>
                                </div>
                                
                                <div class="col-md-6 mb-3" id="cis_rate_field" style="display: none;">
                                    <label for="cis_rate" class="form-label">CIS Rate (%)</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" max="100" 
                                               class="form-control @error('cis_rate') is-invalid @enderror" 
                                               id="cis_rate" name="cis_rate" 
                                               value="{{ old('cis_rate') }}"
                                               placeholder="20.00">
                                        <span class="input-group-text">%</span>
                                        @error('cis_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">CIS deduction rate (typically 20%)</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('team.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Add Team Member
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cisApplicableCheckbox = document.getElementById('cis_applicable');
    const cisRateField = document.getElementById('cis_rate_field');

    function toggleCisRateField() {
        if (cisApplicableCheckbox.checked) {
            cisRateField.style.display = 'block';
        } else {
            cisRateField.style.display = 'none';
            // Clear the CIS rate value when hiding
            document.getElementById('cis_rate').value = '';
        }
    }

    // Initial state
    toggleCisRateField();

    // Listen for changes
    cisApplicableCheckbox.addEventListener('change', toggleCisRateField);
});
</script>
@endpush 