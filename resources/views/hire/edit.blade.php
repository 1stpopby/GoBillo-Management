@extends('layouts.app')

@section('title', 'Edit Hire Request - ' . $hireRequest->title)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Edit Hire Request</h1>
            <p class="page-subtitle">Update hiring request details</p>
            <div class="page-meta">
                <span class="badge bg-{{ $hireRequest->status_color }} me-2">{{ $hireRequest->status_display }}</span>
                <small class="text-muted">
                    Created {{ $hireRequest->created_at->format('M j, Y') }} by {{ $hireRequest->requestedBy->name }}
                </small>
            </div>
        </div>
        <div>
            <a href="{{ route('hire.show', $hireRequest) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Request
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('hire.update', $hireRequest) }}">
                @csrf
                @method('PUT')
                
                <!-- Basic Information Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>Basic Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="title" class="form-label">Request Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $hireRequest->title) }}" 
                                       placeholder="e.g., Need 2 Skilled Bricklayers for London Site">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4" 
                                          placeholder="Describe the role, responsibilities, and any specific requirements...">{{ old('description', $hireRequest->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="position_type" class="form-label">Position Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('position_type') is-invalid @enderror" 
                                        id="position_type" name="position_type">
                                    <option value="">Select Position Type</option>
                                    @foreach(\App\Models\HireRequest::getPositionTypeOptions() as $key => $label)
                                        <option value="{{ $key }}" {{ old('position_type', $hireRequest->position_type) === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('position_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="employment_type" class="form-label">Employment Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('employment_type') is-invalid @enderror" 
                                        id="employment_type" name="employment_type">
                                    <option value="">Select Employment Type</option>
                                    @foreach(\App\Models\HireRequest::getEmploymentTypeOptions() as $key => $label)
                                        <option value="{{ $key }}" {{ old('employment_type', $hireRequest->employment_type) === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="quantity" class="form-label">Number of People Needed <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                       id="quantity" name="quantity" value="{{ old('quantity', $hireRequest->quantity) }}" 
                                       min="1" max="100">
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="urgency" class="form-label">Urgency Level <span class="text-danger">*</span></label>
                                <select class="form-select @error('urgency') is-invalid @enderror" 
                                        id="urgency" name="urgency">
                                    <option value="">Select Urgency</option>
                                    @foreach(\App\Models\HireRequest::getUrgencyOptions() as $key => $label)
                                        <option value="{{ $key }}" {{ old('urgency', $hireRequest->urgency) === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('urgency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location & Project Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-geo-alt me-2"></i>Location & Project
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="site_id" class="form-label">Site</label>
                                <select class="form-select @error('site_id') is-invalid @enderror" 
                                        id="site_id" name="site_id">
                                    <option value="">Select Site (Optional)</option>
                                    @foreach($sites as $site)
                                        <option value="{{ $site->id }}" {{ old('site_id', $hireRequest->site_id) == $site->id ? 'selected' : '' }}>
                                            {{ $site->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('site_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="project_id" class="form-label">Project</label>
                                <select class="form-select @error('project_id') is-invalid @enderror" 
                                        id="project_id" name="project_id" {{ !$hireRequest->site_id ? 'disabled' : '' }}>
                                    <option value="">Select Project (Optional)</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" 
                                                data-site-id="{{ $project->site_id }}"
                                                {{ old('project_id', $hireRequest->project_id) == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Requirements Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-list-check me-2"></i>Requirements
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="required_skills" class="form-label">Required Skills</label>
                                <textarea class="form-control @error('required_skills') is-invalid @enderror" 
                                          id="required_skills" name="required_skills" rows="3" 
                                          placeholder="e.g., Bricklaying, Plastering, Knowledge of safety protocols...">{{ old('required_skills', $hireRequest->required_skills) }}</textarea>
                                @error('required_skills')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="required_qualifications" class="form-label">Required Qualifications</label>
                                <textarea class="form-control @error('required_qualifications') is-invalid @enderror" 
                                          id="required_qualifications" name="required_qualifications" rows="3" 
                                          placeholder="e.g., NVQ Level 2 in Bricklaying, City & Guilds...">{{ old('required_qualifications', $hireRequest->required_qualifications) }}</textarea>
                                @error('required_qualifications')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="required_certifications" class="form-label">Required Certifications</label>
                                <textarea class="form-control @error('required_certifications') is-invalid @enderror" 
                                          id="required_certifications" name="required_certifications" rows="3" 
                                          placeholder="e.g., CSCS Card, CPCS License, First Aid Certificate...">{{ old('required_certifications', $hireRequest->required_certifications) }}</textarea>
                                @error('required_certifications')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="min_experience_years" class="form-label">Minimum Experience (Years)</label>
                                <input type="number" class="form-control @error('min_experience_years') is-invalid @enderror" 
                                       id="min_experience_years" name="min_experience_years" 
                                       value="{{ old('min_experience_years', $hireRequest->min_experience_years) }}" 
                                       min="0" max="50" placeholder="e.g., 3">
                                @error('min_experience_years')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Compensation Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-currency-pound me-2"></i>Compensation
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="offered_rate" class="form-label">Offered Rate (£)</label>
                                <input type="number" class="form-control @error('offered_rate') is-invalid @enderror" 
                                       id="offered_rate" name="offered_rate" 
                                       value="{{ old('offered_rate', $hireRequest->offered_rate) }}" 
                                       step="0.01" min="0" max="9999.99" placeholder="e.g., 180.00">
                                @error('offered_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="rate_type" class="form-label">Rate Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('rate_type') is-invalid @enderror" 
                                        id="rate_type" name="rate_type">
                                    @foreach(\App\Models\HireRequest::getRateTypeOptions() as $key => $label)
                                        <option value="{{ $key }}" {{ old('rate_type', $hireRequest->rate_type) === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('rate_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12">
                                <label for="benefits" class="form-label">Additional Benefits</label>
                                <textarea class="form-control @error('benefits') is-invalid @enderror" 
                                          id="benefits" name="benefits" rows="3" 
                                          placeholder="e.g., Company van, Tools provided, Pension scheme, Overtime opportunities...">{{ old('benefits', $hireRequest->benefits) }}</textarea>
                                @error('benefits')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar me-2"></i>Timeline
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" 
                                       value="{{ old('start_date', $hireRequest->start_date?->format('Y-m-d')) }}" 
                                       min="{{ date('Y-m-d') }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">End Date (if contract)</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" 
                                       value="{{ old('end_date', $hireRequest->end_date?->format('Y-m-d')) }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="deadline" class="form-label">Hiring Deadline</label>
                                <input type="date" class="form-control @error('deadline') is-invalid @enderror" 
                                       id="deadline" name="deadline" 
                                       value="{{ old('deadline', $hireRequest->deadline?->format('Y-m-d')) }}" 
                                       min="{{ date('Y-m-d') }}">
                                @error('deadline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-sticky me-2"></i>Additional Notes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="4" 
                                          placeholder="Any additional information, special requirements, or notes for HR/management...">{{ old('notes', $hireRequest->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('hire.show', $hireRequest) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Hire Request
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Help Sidebar -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-square me-2"></i>Current Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge bg-{{ $hireRequest->status_color }} fs-6">
                            {{ $hireRequest->status_display }}
                        </span>
                    </div>
                    
                    @if($hireRequest->status === 'draft')
                        <div class="alert alert-info">
                            <strong>Draft Status</strong><br>
                            This request is still in draft. You can make changes and submit for approval.
                        </div>
                    @elseif($hireRequest->status === 'pending_approval')
                        <div class="alert alert-warning">
                            <strong>Pending Approval</strong><br>
                            This request is awaiting management approval. Limited changes allowed.
                        </div>
                    @else
                        <div class="alert alert-secondary">
                            <strong>{{ $hireRequest->status_display }}</strong><br>
                            This request has been processed. Contact management for major changes.
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-question-circle me-2"></i>Editing Guidelines
                    </h5>
                </div>
                <div class="card-body">
                    <div class="help-section mb-4">
                        <h6 class="text-primary">📝 What You Can Edit</h6>
                        <ul class="small">
                            <li>Request details and description</li>
                            <li>Requirements and qualifications</li>
                            <li>Compensation and benefits</li>
                            <li>Timeline and deadlines</li>
                            <li>Additional notes</li>
                        </ul>
                    </div>
                    
                    <div class="help-section mb-4">
                        <h6 class="text-primary">⚠️ Important Notes</h6>
                        <ul class="small">
                            <li><strong>Status Restrictions:</strong> Some fields may be locked based on current status</li>
                            <li><strong>Approval Required:</strong> Major changes may require re-approval</li>
                            <li><strong>Active Recruitment:</strong> Changes to approved requests may affect ongoing recruitment</li>
                        </ul>
                    </div>
                    
                    <div class="help-section">
                        <h6 class="text-primary">💡 Best Practices</h6>
                        <ul class="small">
                            <li>Update requirements based on feedback</li>
                            <li>Adjust deadlines if needed</li>
                            <li>Add clarifying notes for HR</li>
                            <li>Review compensation competitiveness</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Site-Project dependency
    const siteSelect = document.getElementById('site_id');
    const projectSelect = document.getElementById('project_id');
    
    function updateProjectOptions() {
        const siteId = siteSelect.value;
        const projectOptions = projectSelect.querySelectorAll('option');
        
        if (siteId) {
            projectSelect.disabled = false;
            
            // Show/hide projects based on selected site
            projectOptions.forEach(option => {
                if (option.value === '') {
                    option.style.display = 'block';
                } else if (option.dataset.siteId === siteId) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                    // Clear selection if project doesn't match site
                    if (option.selected) {
                        projectSelect.value = '';
                    }
                }
            });
        } else {
            projectSelect.disabled = true;
            projectSelect.value = '';
            projectOptions.forEach(option => {
                option.style.display = 'block';
            });
        }
    }
    
    // Initialize on page load
    updateProjectOptions();
    
    siteSelect.addEventListener('change', updateProjectOptions);
    
    // Date validation
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    startDate.addEventListener('change', function() {
        if (this.value) {
            endDate.min = this.value;
        }
    });
    
    // Initialize end date minimum
    if (startDate.value) {
        endDate.min = startDate.value;
    }
});
</script>

<style>
.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.page-meta {
    margin-bottom: 0;
}

.help-section h6 {
    font-weight: 600;
    margin-bottom: 0.75rem;
}

.help-section ul {
    margin-bottom: 0;
    padding-left: 1.25rem;
}

.help-section li {
    margin-bottom: 0.5rem;
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.card-title {
    color: #495057;
    font-weight: 600;
}

.form-label {
    font-weight: 500;
    color: #495057;
}

.text-danger {
    color: #dc3545 !important;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    font-weight: 500;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #667eea 100%);
    transform: translateY(-1px);
}

.btn-outline-secondary {
    border-color: #6c757d;
    color: #6c757d;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
}
</style>
@endsection
