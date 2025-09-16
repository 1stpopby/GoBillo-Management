@extends('layouts.app')

@section('title', 'Submit Form - ' . $template->name)

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="header-icon bg-purple bg-opacity-10 text-purple rounded-circle p-3 me-3">
                        <i class="bi bi-file-earmark-plus fs-3"></i>
                    </div>
                    <div>
                        <nav aria-label="breadcrumb" class="mb-2">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('health-safety.index') }}">Health & Safety</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('health-safety.forms') }}">Forms</a></li>
                                <li class="breadcrumb-item active">Submit Form</li>
                            </ol>
                        </nav>
                        <h1 class="page-title mb-1 fw-bold">{{ $template->name }}</h1>
                        <p class="page-subtitle text-muted mb-0">Complete this safety form for your site or project</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-end">
                <a href="{{ route('health-safety.forms') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Forms
                </a>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-light border-0">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-clipboard-check text-purple me-2"></i>
                        {{ $template->name }}
                    </h5>
                    @if($template->description)
                        <p class="text-muted mb-0 mt-1">{{ $template->description }}</p>
                    @endif
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-{{ $template->category === 'inspection' ? 'primary' : ($template->category === 'permit' ? 'warning' : 'info') }} px-3 py-2">
                        <i class="bi bi-tag me-1"></i>{{ ucfirst($template->category) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card-body">
            @if($template->instructions)
                <div class="alert alert-info border-0 mb-4">
                    <div class="d-flex">
                        <div class="flex-shrink-0">
                            <i class="bi bi-info-circle fs-4 text-info"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="alert-heading mb-2">Instructions</h6>
                            <p class="mb-0">{{ $template->instructions }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('health-safety.forms.submission.store', $template->id) }}" method="POST" enctype="multipart/form-data" id="form-submission">
                @csrf
                
                <div class="row">
                    <!-- Main Form Section -->
                    <div class="col-lg-8">
                        <!-- Location Information -->
                        <h5 class="mb-3">Location Information</h5>
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="site_id" class="form-label">Site</label>
                                <select class="form-select @error('site_id') is-invalid @enderror" id="site_id" name="site_id">
                                    <option value="">Select a site (optional)</option>
                                    @foreach($sites as $site)
                                        <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>
                                            {{ $site->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('site_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="project_id" class="form-label">Project</label>
                                <select class="form-select @error('project_id') is-invalid @enderror" id="project_id" name="project_id">
                                    <option value="">Select a project (optional)</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Dynamic Form Fields -->
                        <h5 class="mb-3">Form Details</h5>
                        
                        <div id="dynamic-form-fields">
                            @php
                                $fields = json_decode($template->fields, true) ?? [];
                            @endphp

                            @foreach($fields as $index => $field)
                                <div class="mb-3 dynamic-field" data-field-type="{{ $field['type'] }}" data-field-index="{{ $index }}">
                                    <label for="field_{{ $index }}" class="form-label">
                                        {{ $field['label'] }}
                                        @if($field['required'] ?? false)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>

                                    @switch($field['type'])
                                        @case('text')
                                            <input type="text" 
                                                   class="form-control @error('form_data.'.$index) is-invalid @enderror" 
                                                   id="field_{{ $index }}" 
                                                   name="form_data[{{ $index }}]" 
                                                   value="{{ old('form_data.'.$index) }}"
                                                   placeholder="{{ $field['placeholder'] ?? '' }}"
                                                   {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                            @break

                                        @case('textarea')
                                            <textarea class="form-control @error('form_data.'.$index) is-invalid @enderror" 
                                                      id="field_{{ $index }}" 
                                                      name="form_data[{{ $index }}]" 
                                                      rows="{{ $field['rows'] ?? 3 }}"
                                                      placeholder="{{ $field['placeholder'] ?? '' }}"
                                                      {{ ($field['required'] ?? false) ? 'required' : '' }}>{{ old('form_data.'.$index) }}</textarea>
                                            @break

                                        @case('select')
                                            <select class="form-select @error('form_data.'.$index) is-invalid @enderror" 
                                                    id="field_{{ $index }}" 
                                                    name="form_data[{{ $index }}]"
                                                    {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                                <option value="">Select option...</option>
                                                @foreach(($field['options'] ?? []) as $option)
                                                    <option value="{{ $option }}" {{ old('form_data.'.$index) == $option ? 'selected' : '' }}>
                                                        {{ $option }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @break

                                        @case('checkbox')
                                            <div class="form-check">
                                                <input class="form-check-input @error('form_data.'.$index) is-invalid @enderror" 
                                                       type="checkbox" 
                                                       id="field_{{ $index }}" 
                                                       name="form_data[{{ $index }}]" 
                                                       value="1"
                                                       {{ old('form_data.'.$index) ? 'checked' : '' }}
                                                       {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                                <label class="form-check-label" for="field_{{ $index }}">
                                                    {{ $field['label'] }}
                                                </label>
                                            </div>
                                            @break

                                        @case('date')
                                            <input type="date" 
                                                   class="form-control @error('form_data.'.$index) is-invalid @enderror" 
                                                   id="field_{{ $index }}" 
                                                   name="form_data[{{ $index }}]" 
                                                   value="{{ old('form_data.'.$index) }}"
                                                   {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                            @break
                                    @endswitch

                                    @error('form_data.'.$index)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach

                            @if(empty($fields))
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    This template has no form fields configured. Please contact an administrator.
                                </div>
                            @endif
                        </div>

                        <!-- Photo Upload Section -->
                        @if($template->requires_photo)
                            <h5 class="mb-3 mt-4">Photo Documentation</h5>
                            
                            <div class="mb-3">
                                <label for="photos" class="form-label">Upload Photos <span class="text-danger">*</span></label>
                                <input type="file" 
                                       class="form-control @error('photos') is-invalid @enderror" 
                                       id="photos" 
                                       name="photos[]" 
                                       multiple 
                                       accept="image/*"
                                       required>
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    You can select multiple photos. Accepted formats: JPG, PNG, GIF. Max size: 5MB per photo.
                                </div>
                                @error('photos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Photo Preview -->
                            <div id="photo-preview" class="row g-3 mb-3" style="display: none;">
                                <!-- Photos will be previewed here -->
                            </div>
                        @endif

                        <!-- Digital Signature Section -->
                        @if($template->requires_signature)
                            <h5 class="mb-3 mt-4">Digital Signature</h5>
                            
                            <div class="mb-3">
                                <div class="card border-2 border-dashed">
                                    <div class="card-body text-center py-4">
                                        <i class="bi bi-pen display-4 text-muted mb-3"></i>
                                        <h6 class="text-muted">Digital Signature Required</h6>
                                        <p class="text-muted mb-3">By submitting this form, you are providing your digital signature and confirming the accuracy of the information provided.</p>
                                        
                                        <div class="form-check d-inline-block">
                                            <input class="form-check-input @error('signature_confirmation') is-invalid @enderror" 
                                                   type="checkbox" 
                                                   id="signature_confirmation" 
                                                   name="signature_confirmation" 
                                                   value="1"
                                                   {{ old('signature_confirmation') ? 'checked' : '' }}
                                                   required>
                                            <label class="form-check-label fw-semibold" for="signature_confirmation">
                                                I confirm the accuracy of this form and provide my digital signature
                                            </label>
                                        </div>
                                        @error('signature_confirmation')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Side Panel -->
                    <div class="col-lg-4">
                        <!-- Template Information -->
                        <h5 class="mb-3">Template Information</h5>
                        
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <div class="template-info">
                                    <div class="info-item mb-2">
                                        <i class="bi bi-tag text-muted me-2"></i>
                                        <span class="text-muted">Category:</span>
                                        <strong>{{ ucfirst($template->category) }}</strong>
                                    </div>
                                    
                                    <div class="info-item mb-2">
                                        <i class="bi bi-person text-muted me-2"></i>
                                        <span class="text-muted">Created by:</span>
                                        <strong>{{ $template->createdBy->name }}</strong>
                                    </div>
                                    
                                    <div class="info-item mb-2">
                                        <i class="bi bi-calendar text-muted me-2"></i>
                                        <span class="text-muted">Version:</span>
                                        <strong>{{ $template->version }}</strong>
                                    </div>
                                    
                                    <div class="info-item">
                                        <i class="bi bi-list-ul text-muted me-2"></i>
                                        <span class="text-muted">Fields:</span>
                                        <strong>{{ count($fields) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Requirements -->
                        <h5 class="mb-3">Requirements</h5>
                        
                        <div class="requirements-list mb-4">
                            <div class="requirement-item d-flex align-items-center mb-2">
                                <i class="bi bi-{{ $template->requires_signature ? 'check-circle text-success' : 'x-circle text-muted' }} me-2"></i>
                                <span class="{{ $template->requires_signature ? 'text-dark' : 'text-muted' }}">Digital Signature</span>
                            </div>
                            
                            <div class="requirement-item d-flex align-items-center mb-2">
                                <i class="bi bi-{{ $template->requires_photo ? 'check-circle text-success' : 'x-circle text-muted' }} me-2"></i>
                                <span class="{{ $template->requires_photo ? 'text-dark' : 'text-muted' }}">Photo Upload</span>
                            </div>
                            
                            <div class="requirement-item d-flex align-items-center">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <span class="text-dark">Form Completion</span>
                            </div>
                        </div>

                        <!-- Progress Indicator -->
                        <h5 class="mb-3">Completion Progress</h5>
                        
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar bg-purple" role="progressbar" style="width: 0%" id="completion-progress"></div>
                        </div>
                        <small class="text-muted">
                            <span id="completion-text">0% Complete</span>
                        </small>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('health-safety.forms') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancel
                            </a>
                            <div>
                                <button type="button" class="btn btn-outline-purple me-2" id="save-draft">
                                    <i class="bi bi-save me-2"></i>Save Draft
                                </button>
                                <button type="submit" class="btn btn-purple" id="submit-form">
                                    <i class="bi bi-send me-2"></i>Submit Form
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .page-header {
        margin-bottom: 2rem;
    }

    .header-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1a202c;
    }

    .page-subtitle {
        font-size: 1rem;
        color: #64748b;
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        color: #94a3b8;
    }

    .breadcrumb-item a {
        color: #64748b;
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        color: #9333ea;
    }

    /* Purple theme */
    .bg-purple {
        background-color: #9333ea !important;
    }

    .text-purple {
        color: #9333ea !important;
    }

    .btn-purple {
        background-color: #9333ea;
        border-color: #9333ea;
        color: white;
    }

    .btn-purple:hover {
        background-color: #7c2bc7;
        border-color: #7c2bc7;
        color: white;
        transform: translateY(-1px);
    }

    .btn-outline-purple {
        color: #9333ea;
        border-color: #9333ea;
    }

    .btn-outline-purple:hover {
        background-color: #9333ea;
        border-color: #9333ea;
        color: white;
    }

    .progress-bar.bg-purple {
        background-color: #9333ea !important;
    }

    /* Form styling */
    .form-label {
        font-weight: 500;
        color: #475569;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 0.625rem 0.875rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: #9333ea;
        box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
    }

    .dynamic-field {
        padding: 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background-color: #f9fafb;
        margin-bottom: 1rem;
    }

    .dynamic-field:hover {
        border-color: #d1d5db;
        background-color: #ffffff;
    }

    .template-info .info-item {
        font-size: 0.9rem;
    }

    .requirement-item {
        font-size: 0.9rem;
    }

    /* Photo preview */
    .photo-preview-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
    }

    .photo-preview-item img {
        width: 100%;
        height: 100px;
        object-fit: cover;
    }

    .photo-preview-item .remove-photo {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: #dc3545;
    }

    /* Signature section */
    .card.border-dashed {
        border-style: dashed !important;
        border-color: #d1d5db !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .dynamic-field {
            padding: 0.75rem;
        }
        
        .page-title {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-submission');
    const photosInput = document.getElementById('photos');
    const photoPreview = document.getElementById('photo-preview');
    const completionProgress = document.getElementById('completion-progress');
    const completionText = document.getElementById('completion-text');
    
    // Photo preview functionality
    if (photosInput) {
        photosInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            photoPreview.innerHTML = '';
            
            if (files.length > 0) {
                photoPreview.style.display = 'flex';
                
                files.forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const photoItem = document.createElement('div');
                            photoItem.className = 'col-md-3 col-sm-4 col-6';
                            photoItem.innerHTML = `
                                <div class="photo-preview-item">
                                    <img src="${e.target.result}" alt="Preview ${index + 1}">
                                    <button type="button" class="remove-photo" onclick="removePhoto(${index})">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            `;
                            photoPreview.appendChild(photoItem);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                photoPreview.style.display = 'none';
            }
        });
    }
    
    // Progress calculation
    function updateProgress() {
        const requiredFields = form.querySelectorAll('[required]');
        let completedFields = 0;
        
        requiredFields.forEach(field => {
            if (field.type === 'checkbox') {
                if (field.checked) completedFields++;
            } else if (field.value.trim() !== '') {
                completedFields++;
            }
        });
        
        const progress = requiredFields.length > 0 ? (completedFields / requiredFields.length) * 100 : 100;
        completionProgress.style.width = progress + '%';
        completionText.textContent = Math.round(progress) + '% Complete';
    }
    
    // Listen for form changes
    form.addEventListener('input', updateProgress);
    form.addEventListener('change', updateProgress);
    
    // Initial progress calculation
    updateProgress();
    
    // Form validation
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (field.type === 'checkbox') {
                if (!field.checked) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            } else if (field.value.trim() === '') {
                isValid = false;
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields before submitting.');
        }
    });
    
    // Save draft functionality
    const saveDraftBtn = document.getElementById('save-draft');
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', function() {
            // This would typically save to localStorage or make an AJAX call
            alert('Draft saved! (This is a placeholder - implement actual draft saving as needed)');
        });
    }
});

// Remove photo function
function removePhoto(index) {
    const photosInput = document.getElementById('photos');
    const photoPreview = document.getElementById('photo-preview');
    
    // Create new FileList without the removed file
    const dt = new DataTransfer();
    const files = Array.from(photosInput.files);
    
    files.forEach((file, i) => {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    photosInput.files = dt.files;
    
    // Trigger change event to update preview
    photosInput.dispatchEvent(new Event('change'));
}
</script>
@endpush
@endsection


