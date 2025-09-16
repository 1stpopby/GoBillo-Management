@extends('layouts.app')

@section('title', 'Edit Site: ' . $site->name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('manager.sites.index') }}">My Sites</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('manager.sites.show', $site) }}">{{ $site->name }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">
                <i class="bi bi-pencil text-primary me-2"></i>
                Edit Site: {{ $site->name }}
            </h1>
            <p class="text-muted mb-0">Update site information and settings</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('manager.sites.show', $site) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Site
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Site Information
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('manager.sites.update', $site) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3 text-primary">Basic Information</h6>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Site Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $site->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3">{{ old('description', $site->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                                <option value="">Select Status</option>
                                                <option value="planning" {{ old('status', $site->status) === 'planning' ? 'selected' : '' }}>Planning</option>
                                                <option value="active" {{ old('status', $site->status) === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="on_hold" {{ old('status', $site->status) === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                                <option value="completed" {{ old('status', $site->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="cancelled" {{ old('status', $site->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                            <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                                <option value="">Select Priority</option>
                                                <option value="low" {{ old('priority', $site->priority) === 'low' ? 'selected' : '' }}>Low</option>
                                                <option value="medium" {{ old('priority', $site->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                                                <option value="high" {{ old('priority', $site->priority) === 'high' ? 'selected' : '' }}>High</option>
                                                <option value="urgent" {{ old('priority', $site->priority) === 'urgent' ? 'selected' : '' }}>Urgent</option>
                                            </select>
                                            @error('priority')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Location Information -->
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3 text-primary">Location Information</h6>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                           id="address" name="address" value="{{ old('address', $site->address) }}" required>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                                   id="city" name="city" value="{{ old('city', $site->city) }}" required>
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="state" class="form-label">State/County <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                                   id="state" name="state" value="{{ old('state', $site->state) }}" required>
                                            @error('state')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="zip_code" class="form-label">Postal Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('zip_code') is-invalid @enderror" 
                                                   id="zip_code" name="zip_code" value="{{ old('zip_code', $site->zip_code) }}" required>
                                            @error('zip_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                                   id="country" name="country" value="{{ old('country', $site->country) }}" required>
                                            @error('country')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <!-- Timeline Information -->
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3 text-primary">Timeline</h6>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="start_date" class="form-label">Start Date</label>
                                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                                   id="start_date" name="start_date" 
                                                   value="{{ old('start_date', $site->start_date?->format('Y-m-d')) }}">
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="expected_completion_date" class="form-label">Expected Completion</label>
                                            <input type="date" class="form-control @error('expected_completion_date') is-invalid @enderror" 
                                                   id="expected_completion_date" name="expected_completion_date" 
                                                   value="{{ old('expected_completion_date', $site->expected_completion_date?->format('Y-m-d')) }}">
                                            @error('expected_completion_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Site Contact Information -->
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3 text-primary">Site Contact</h6>
                                
                                <div class="mb-3">
                                    <label for="site_manager_contact" class="form-label">Contact Person</label>
                                    <input type="text" class="form-control @error('site_manager_contact') is-invalid @enderror" 
                                           id="site_manager_contact" name="site_manager_contact" 
                                           value="{{ old('site_manager_contact', $site->site_manager_contact) }}">
                                    @error('site_manager_contact')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="site_manager_phone" class="form-label">Contact Phone</label>
                                    <input type="tel" class="form-control @error('site_manager_phone') is-invalid @enderror" 
                                           id="site_manager_phone" name="site_manager_phone" 
                                           value="{{ old('site_manager_phone', $site->site_manager_phone) }}">
                                    @error('site_manager_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Notes Section -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-bold mb-3 text-primary">Additional Notes</h6>
                                <div class="mb-4">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="4" 
                                              placeholder="Add any additional notes about this site...">{{ old('notes', $site->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('manager.sites.show', $site) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Site
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    border: 1px solid #e5e7eb;
}

.card-header {
    background-color: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
}

.form-label {
    font-weight: 500;
    color: #374151;
}

.text-primary {
    color: #3b82f6 !important;
}

.form-control:focus,
.form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
}

.btn-primary {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

.btn-primary:hover {
    background-color: #2563eb;
    border-color: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-outline-secondary:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.invalid-feedback {
    display: block;
}

hr {
    border-color: #e5e7eb;
    margin: 2rem 0;
}

@media (max-width: 768px) {
    .col-md-6 {
        margin-bottom: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Date validation - ensure end date is after start date
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('expected_completion_date');

    function validateDates() {
        if (startDateInput.value && endDateInput.value) {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            
            if (endDate < startDate) {
                endDateInput.setCustomValidity('Expected completion date must be after start date');
            } else {
                endDateInput.setCustomValidity('');
            }
        }
    }

    startDateInput.addEventListener('change', validateDates);
    endDateInput.addEventListener('change', validateDates);

    // Form submission confirmation for significant changes
    const form = document.querySelector('form');
    const statusSelect = document.getElementById('status');
    const originalStatus = '{{ $site->status }}';

    form.addEventListener('submit', function(e) {
        if (statusSelect.value === 'cancelled' && originalStatus !== 'cancelled') {
            if (!confirm('Are you sure you want to cancel this site? This action may affect related projects.')) {
                e.preventDefault();
            }
        }
    });
});
</script>
@endpush
