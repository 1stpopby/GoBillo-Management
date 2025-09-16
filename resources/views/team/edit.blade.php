@extends('layouts.app')

@section('title', 'Edit Team Member')

@section('content')
<div class="container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('team.index') }}">Team</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('team.show', ['team'=>$member->id]) }}">{{ $member->name }}</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
                <h1 class="page-title">Edit Team Member</h1>
                <p class="page-subtitle">Update {{ $member->name }}'s information</p>
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
                    <form action="{{ route('team.update', ['team'=>$member->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                    id="name" name="name" value="{{ old('name', $member->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                    id="email" name="email" value="{{ old('email', $member->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="">Select role...</option>
                                @foreach($roles as $value => $label)
                                     <option value="{{ $value }}" {{ old('role', $member->role) == $value ? 'selected' : '' }}>
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
                                    id="phone" name="phone" value="{{ old('phone', $member->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       {{ old('is_active', $member->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Member
                                </label>
                            </div>
                        </div>

                        <!-- Operative Settings (only show for operatives) -->
                        <div id="operative-settings" class="operative-settings" style="display: none;">
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
                                               value="{{ old('day_rate', $employee->day_rate ?? '') }}"
                                               placeholder="0.00">
                                        @error('day_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">Daily rate for invoice calculations</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="cis_applicable" name="cis_applicable" 
                                           {{ old('cis_applicable', $employee->cis_applicable ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="cis_applicable">
                                        <strong>CIS Applicable</strong>
                                    </label>
                                </div>
                                <small class="form-text text-muted">Check if Construction Industry Scheme applies to this operative</small>
                            </div>

                            <div id="cis-rate-section" class="mb-3" style="display: none;">
                                <label for="cis_rate" class="form-label">CIS Deduction Rate (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100" 
                                           class="form-control @error('cis_rate') is-invalid @enderror" 
                                           id="cis_rate" name="cis_rate" 
                                           value="{{ old('cis_rate', $employee->cis_rate ?? 20.00) }}"
                                           placeholder="20.00">
                                    <span class="input-group-text">%</span>
                                    @error('cis_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Standard CIS rate is 20% for registered subcontractors</small>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('team.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Team Member
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .operative-settings {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        padding: 1.5rem;
        margin-top: 1rem;
    }
    
    .operative-settings h6 {
        margin-bottom: 1rem;
        font-weight: 600;
    }
    
    .form-text.text-muted {
        font-size: 0.875rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const operativeSettings = document.getElementById('operative-settings');
    const cisApplicableCheckbox = document.getElementById('cis_applicable');
    const cisRateSection = document.getElementById('cis-rate-section');
    
    // Function to toggle operative settings visibility
    function toggleOperativeSettings() {
        if (roleSelect.value === 'operative') {
            operativeSettings.style.display = 'block';
        } else {
            operativeSettings.style.display = 'none';
        }
    }
    
    // Function to toggle CIS rate section
    function toggleCisRateSection() {
        if (cisApplicableCheckbox.checked) {
            cisRateSection.style.display = 'block';
        } else {
            cisRateSection.style.display = 'none';
        }
    }
    
    // Initial setup
    toggleOperativeSettings();
    toggleCisRateSection();
    
    // Event listeners
    roleSelect.addEventListener('change', toggleOperativeSettings);
    cisApplicableCheckbox.addEventListener('change', toggleCisRateSection);
});

</script>
@endpush

@endsection 