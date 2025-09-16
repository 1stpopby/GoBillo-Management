@extends('layouts.app')

@section('title', 'Create Form Template')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="page-header-section mb-4">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="page-icon bg-purple">
                    <i class="bi bi-clipboard-check"></i>
                </div>
            </div>
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('health-safety.index') }}">Health & Safety</a></li>
                        <li class="breadcrumb-item active">Create Form Template</li>
                    </ol>
                </nav>
                <h1 class="page-title mb-0">Create Custom Safety Form Template</h1>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('health-safety.forms.template.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <!-- Main Form Section -->
                    <div class="col-lg-8">
                        <h5 class="mb-3">Template Information</h5>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Template Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" 
                                   placeholder="e.g., Site Safety Inspection Checklist" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Brief description of what this form is used for">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select @error('category') is-invalid @enderror" 
                                        id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="inspection" {{ old('category') == 'inspection' ? 'selected' : '' }}>Inspection</option>
                                    <option value="permit" {{ old('category') == 'permit' ? 'selected' : '' }}>Permit</option>
                                    <option value="checklist" {{ old('category') == 'checklist' ? 'selected' : '' }}>Checklist</option>
                                    <option value="assessment" {{ old('category') == 'assessment' ? 'selected' : '' }}>Assessment</option>
                                    <option value="report" {{ old('category') == 'report' ? 'selected' : '' }}>Report</option>
                                    <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="frequency" class="form-label">Frequency</label>
                                <select class="form-select @error('frequency') is-invalid @enderror" 
                                        id="frequency" name="frequency">
                                    <option value="">Select Frequency</option>
                                    <option value="daily" {{ old('frequency') == 'daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="weekly" {{ old('frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ old('frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="quarterly" {{ old('frequency') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                    <option value="annual" {{ old('frequency') == 'annual' ? 'selected' : '' }}>Annual</option>
                                    <option value="as_needed" {{ old('frequency') == 'as_needed' ? 'selected' : '' }}>As Needed</option>
                                </select>
                                @error('frequency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Builder Section -->
                        <h5 class="mb-3 mt-4">Form Fields</h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="field-type-selector" class="form-label">Select Field Type to Add</label>
                                <select class="form-select" id="field-type-selector">
                                    <option value="">Select field type...</option>
                                    <option value="text">Text Input</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="select">Dropdown Select</option>
                                    <option value="checkbox">Checkbox</option>
                                    <option value="date">Date Picker</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" class="btn btn-primary w-100" id="add-field">
                                    <i class="bi bi-plus-circle me-2"></i>Add Field
                                </button>
                            </div>
                        </div>

                        <div id="form-fields">
                            <!-- Dynamic form fields will be added here -->
                        </div>

                        <!-- Instructions Section -->
                        <h5 class="mb-3 mt-4">Instructions</h5>
                        
                        <div class="mb-3">
                            <label for="instructions" class="form-label">Form Instructions</label>
                            <textarea class="form-control @error('instructions') is-invalid @enderror" 
                                      id="instructions" name="instructions" rows="4" 
                                      placeholder="Provide instructions for completing this form">{{ old('instructions') }}</textarea>
                            @error('instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Side Panel -->
                    <div class="col-lg-4">
                        <!-- Template Settings -->
                        <h5 class="mb-3">Settings</h5>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status">
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input @error('requires_signature') is-invalid @enderror" 
                                   type="checkbox" id="requires_signature" name="requires_signature" value="1" 
                                   {{ old('requires_signature') ? 'checked' : '' }}>
                            <label class="form-check-label" for="requires_signature">
                                Requires Digital Signature
                            </label>
                            @error('requires_signature')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input @error('requires_photo') is-invalid @enderror" 
                                   type="checkbox" id="requires_photo" name="requires_photo" value="1" 
                                   {{ old('requires_photo') ? 'checked' : '' }}>
                            <label class="form-check-label" for="requires_photo">
                                Requires Photo Upload
                            </label>
                            @error('requires_photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>



                        <!-- Preview Section -->
                        <h5 class="mb-3 mt-4">Preview</h5>
                        
                        <div class="card bg-light">
                            <div class="card-body">
                                <div id="form-preview">
                                    <p class="text-muted text-center">Add fields to see preview</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden field to store form JSON -->
                <input type="hidden" name="fields" id="fields-json">

                <!-- Form Actions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('health-safety.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancel
                            </a>
                            <div>
                                <button type="submit" name="action" value="save_draft" class="btn btn-outline-purple me-2">
                                    <i class="bi bi-save me-2"></i>Save as Draft
                                </button>
                                <button type="submit" name="action" value="save" class="btn btn-purple">
                                    <i class="bi bi-check-circle me-2"></i>Create Template
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
    .page-header-section {
        margin-bottom: 2rem;
    }

    .page-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #9333ea 0%, #7c2bc7 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }

    .page-icon.bg-purple {
        background: linear-gradient(135deg, #9333ea 0%, #7c2bc7 100%);
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1a202c;
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

    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

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

    h5 {
        color: #1e293b;
        font-weight: 600;
        font-size: 1.125rem;
    }

    textarea.form-control {
        resize: vertical;
    }

    .text-danger {
        color: #ef4444 !important;
    }

    .btn {
        border-radius: 8px;
        padding: 0.625rem 1.25rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-purple {
        background: #9333ea;
        border-color: #9333ea;
        color: white;
    }

    .btn-purple:hover {
        background: #7c2bc7;
        border-color: #7c2bc7;
        color: white;
        transform: translateY(-1px);
    }

    .btn-outline-purple {
        color: #9333ea;
        border-color: #9333ea;
    }

    .btn-outline-purple:hover {
        background: #9333ea;
        border-color: #9333ea;
        color: white;
    }

    .btn-secondary {
        background: #64748b;
        border-color: #64748b;
    }

    .btn-secondary:hover {
        background: #475569;
        border-color: #475569;
    }

    .field-template .card {
        border-left: 4px solid #9333ea;
    }

    .field-type-label {
        font-weight: 600;
        color: #9333ea;
    }

    #form-preview {
        min-height: 100px;
    }

    .dynamic-field {
        border-left: 4px solid #9333ea;
        margin-bottom: 1rem;
    }

    .dynamic-field .card-header {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
// Wait for everything to be fully loaded
window.addEventListener('load', function() {
    console.log('Form builder script loaded - window.load event');
    
    // Add a small delay to ensure everything is rendered
    setTimeout(function() {
        initializeFormBuilder();
    }, 100);
});

function initializeFormBuilder() {
    console.log('Initializing form builder...');
    
    let fieldCounter = 0;
    const formFields = document.getElementById('form-fields');
    const addFieldBtn = document.getElementById('add-field');
    const fieldTypeSelector = document.getElementById('field-type-selector');
    const formPreview = document.getElementById('form-preview');
    const fieldsJson = document.getElementById('fields-json');

    // Debug: List all elements with these IDs
    console.log('formFields:', formFields);
    console.log('addFieldBtn:', addFieldBtn);
    console.log('fieldTypeSelector:', fieldTypeSelector);
    console.log('formPreview:', formPreview);
    console.log('fieldsJson:', fieldsJson);

    // Check if elements exist
    if (!formFields) {
        console.error('form-fields element not found');
        return;
    }
    if (!addFieldBtn) {
        console.error('add-field button not found');
        return;
    }
    if (!fieldTypeSelector) {
        console.error('field-type-selector not found');
        return;
    }

    console.log('All elements found, setting up event listeners');

    // Add field functionality with multiple event types for compatibility
    addFieldBtn.onclick = function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Add field button clicked (onclick)');
        
        const selectedType = fieldTypeSelector.value;
        console.log('Selected type:', selectedType);
        
        if (!selectedType) {
            alert('Please select a field type first');
            return false;
        }
        addField(selectedType);
        fieldTypeSelector.value = '';
        return false;
    };

    // Also add addEventListener as backup
    addFieldBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Add field button clicked (addEventListener)');
        
        const selectedType = fieldTypeSelector.value;
        console.log('Selected type:', selectedType);
        
        if (!selectedType) {
            alert('Please select a field type first');
            return;
        }
        addField(selectedType);
        fieldTypeSelector.value = '';
    });

    function addField(type) {
        const fieldId = fieldCounter++;
        let fieldHtml = '';

        switch (type) {
            case 'text':
                fieldHtml = createTextFieldHtml(fieldId);
                break;
            case 'textarea':
                fieldHtml = createTextareaFieldHtml(fieldId);
                break;
            case 'select':
                fieldHtml = createSelectFieldHtml(fieldId);
                break;
            case 'checkbox':
                fieldHtml = createCheckboxFieldHtml(fieldId);
                break;
            case 'date':
                fieldHtml = createDateFieldHtml(fieldId);
                break;
        }

        const fieldDiv = document.createElement('div');
        fieldDiv.className = 'dynamic-field card mb-3';
        fieldDiv.dataset.type = type;
        fieldDiv.dataset.fieldId = fieldId;
        fieldDiv.innerHTML = fieldHtml;

        // Add event listeners
        const removeBtn = fieldDiv.querySelector('.remove-field');
        removeBtn.addEventListener('click', function() {
            fieldDiv.remove();
            updatePreview();
            updateFieldsJson();
        });

        // Add change listeners to all inputs
        const inputs = fieldDiv.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                updatePreview();
                updateFieldsJson();
            });
        });

        formFields.appendChild(fieldDiv);
        updatePreview();
        updateFieldsJson();
    }

    function createTextFieldHtml(fieldId) {
        return `
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="field-type-label">Text Field</span>
                <button type="button" class="btn btn-sm btn-outline-danger remove-field">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Field Label</label>
                        <input type="text" class="form-control field-label" placeholder="e.g., Inspector Name">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Placeholder</label>
                        <input type="text" class="form-control field-placeholder" placeholder="Enter placeholder text">
                    </div>
                </div>
                <div class="form-check">
                    <input class="form-check-input field-required" type="checkbox">
                    <label class="form-check-label">Required Field</label>
                </div>
            </div>
        `;
    }

    function createTextareaFieldHtml(fieldId) {
        return `
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="field-type-label">Textarea Field</span>
                <button type="button" class="btn btn-sm btn-outline-danger remove-field">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Field Label</label>
                        <input type="text" class="form-control field-label" placeholder="e.g., Comments">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Rows</label>
                        <input type="number" class="form-control field-rows" value="3" min="2" max="10">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Placeholder</label>
                    <input type="text" class="form-control field-placeholder" placeholder="Enter placeholder text">
                </div>
                <div class="form-check">
                    <input class="form-check-input field-required" type="checkbox">
                    <label class="form-check-label">Required Field</label>
                </div>
            </div>
        `;
    }

    function createSelectFieldHtml(fieldId) {
        return `
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="field-type-label">Select Field</span>
                <button type="button" class="btn btn-sm btn-outline-danger remove-field">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Field Label</label>
                    <input type="text" class="form-control field-label" placeholder="e.g., Safety Rating">
                </div>
                <div class="mb-3">
                    <label class="form-label">Options (one per line)</label>
                    <textarea class="form-control field-options" rows="4" placeholder="Excellent\nGood\nFair\nPoor"></textarea>
                </div>
                <div class="form-check">
                    <input class="form-check-input field-required" type="checkbox">
                    <label class="form-check-label">Required Field</label>
                </div>
            </div>
        `;
    }

    function createCheckboxFieldHtml(fieldId) {
        return `
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="field-type-label">Checkbox Field</span>
                <button type="button" class="btn btn-sm btn-outline-danger remove-field">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Field Label</label>
                    <input type="text" class="form-control field-label" placeholder="e.g., PPE Worn Correctly">
                </div>
                <div class="form-check">
                    <input class="form-check-input field-required" type="checkbox">
                    <label class="form-check-label">Required Field</label>
                </div>
            </div>
        `;
    }

    function createDateFieldHtml(fieldId) {
        return `
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="field-type-label">Date Field</span>
                <button type="button" class="btn btn-sm btn-outline-danger remove-field">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Field Label</label>
                    <input type="text" class="form-control field-label" placeholder="e.g., Inspection Date">
                </div>
                <div class="form-check">
                    <input class="form-check-input field-required" type="checkbox">
                    <label class="form-check-label">Required Field</label>
                </div>
            </div>
        `;
    }

    function updatePreview() {
        const fields = Array.from(formFields.children);
        let previewHtml = '';

        if (fields.length === 0) {
            previewHtml = '<p class="text-muted text-center">Add fields to see preview</p>';
        } else {
            fields.forEach(field => {
                const type = field.dataset.type;
                const label = field.querySelector('.field-label')?.value || 'Field Label';
                const placeholder = field.querySelector('.field-placeholder')?.value || '';
                const required = field.querySelector('.field-required')?.checked;
                const requiredText = required ? ' <span class="text-danger">*</span>' : '';

                previewHtml += `<div class="mb-3">`;
                previewHtml += `<label class="form-label">${label}${requiredText}</label>`;

                switch (type) {
                    case 'text':
                        previewHtml += `<input type="text" class="form-control" placeholder="${placeholder}" disabled>`;
                        break;
                    case 'textarea':
                        const rows = field.querySelector('.field-rows')?.value || 3;
                        previewHtml += `<textarea class="form-control" rows="${rows}" placeholder="${placeholder}" disabled></textarea>`;
                        break;
                    case 'select':
                        const options = field.querySelector('.field-options')?.value.split('\n').filter(o => o.trim());
                        previewHtml += `<select class="form-select" disabled>`;
                        previewHtml += `<option>Select option...</option>`;
                        if (options) {
                            options.forEach(option => {
                                previewHtml += `<option>${option.trim()}</option>`;
                            });
                        }
                        previewHtml += `</select>`;
                        break;
                    case 'checkbox':
                        previewHtml += `<div class="form-check"><input class="form-check-input" type="checkbox" disabled><label class="form-check-label">${label}</label></div>`;
                        break;
                    case 'date':
                        previewHtml += `<input type="date" class="form-control" disabled>`;
                        break;
                }
                previewHtml += `</div>`;
            });
        }

        formPreview.innerHTML = previewHtml;
    }

    function updateFieldsJson() {
        const fields = Array.from(formFields.children).map(field => {
            const type = field.dataset.type;
            const fieldData = {
                type: type,
                label: field.querySelector('.field-label')?.value || '',
                required: field.querySelector('.field-required')?.checked || false
            };

            // Add type-specific properties
            switch (type) {
                case 'text':
                case 'textarea':
                    fieldData.placeholder = field.querySelector('.field-placeholder')?.value || '';
                    if (type === 'textarea') {
                        fieldData.rows = field.querySelector('.field-rows')?.value || 3;
                    }
                    break;
                case 'select':
                    fieldData.options = field.querySelector('.field-options')?.value.split('\n').filter(o => o.trim()).map(o => o.trim()) || [];
                    break;
            }

            return fieldData;
        });

        fieldsJson.value = JSON.stringify(fields);
    }

    // Initialize
    updatePreview();
    updateFieldsJson();
}
</script>
@endpush
@endsection
