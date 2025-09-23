@extends('layouts.app')

@section('title', 'Edit Client')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header d-print-none">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clients</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('clients.show', $client) }}">{{ $client->company_name }}</a></li>
                        <li class="breadcrumb-item active">Edit Client</li>
                    </ol>
                </nav>
                <h1 class="page-title" id="page-title">Edit Client</h1>
                <p class="page-subtitle" id="page-subtitle">Update client information</p>
            </div>
        </div>
    </div>

    <!-- Display Errors -->
    @if($errors->any())
        <div class="alert alert-danger">
            <h6>Please fix the following errors:</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('clients.update', $client) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Client Type Selection -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Client Type</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Select Client Type <span class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="is_private_client" 
                                                   id="business_client" value="0" 
                                                   {{ old('is_private_client', $client->is_private_client ? '1' : '0') == '0' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="business_client">
                                                <strong>Business Client</strong>
                                                <small class="d-block text-muted">Company or organization with business details</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="is_private_client" 
                                                   id="private_client" value="1"
                                                   {{ old('is_private_client', $client->is_private_client ? '1' : '0') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="private_client">
                                                <strong>Private Client</strong>
                                                <small class="d-block text-muted">Individual person without company details</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Company Information -->
                <div class="card mb-4" id="company-info-section">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Company Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                       id="company_name" name="company_name" value="{{ old('company_name', $client->company_name) }}">
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="legal_name" class="form-label">Legal Name</label>
                                <input type="text" class="form-control @error('legal_name') is-invalid @enderror" 
                                       id="legal_name" name="legal_name" value="{{ old('legal_name', $client->legal_name) }}" 
                                       placeholder="If different from company name">
                                @error('legal_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="business_type" class="form-label">Business Type</label>
                                <select class="form-select @error('business_type') is-invalid @enderror" 
                                        id="business_type" name="business_type">
                                    <option value="">Select type...</option>
                                    <option value="LLC" {{ old('business_type', $client->business_type) == 'LLC' ? 'selected' : '' }}>LLC</option>
                                    <option value="Corporation" {{ old('business_type', $client->business_type) == 'Corporation' ? 'selected' : '' }}>Corporation</option>
                                    <option value="Partnership" {{ old('business_type', $client->business_type) == 'Partnership' ? 'selected' : '' }}>Partnership</option>
                                    <option value="Limited Company" {{ old('business_type', $client->business_type) == 'Limited Company' ? 'selected' : '' }}>Limited Company</option>
                                    <option value="Non-Profit" {{ old('business_type', $client->business_type) == 'Non-Profit' ? 'selected' : '' }}>Non-Profit</option>
                                    <option value="Government" {{ old('business_type', $client->business_type) == 'Government' ? 'selected' : '' }}>Government</option>
                                    <option value="Other" {{ old('business_type', $client->business_type) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('business_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="industry" class="form-label">Industry</label>
                                <select class="form-select @error('industry') is-invalid @enderror" 
                                        id="industry" name="industry">
                                    <option value="">Select industry...</option>
                                    <option value="Real Estate Development" {{ old('industry', $client->industry) == 'Real Estate Development' ? 'selected' : '' }}>Real Estate Development</option>
                                    <option value="Commercial Construction" {{ old('industry', $client->industry) == 'Commercial Construction' ? 'selected' : '' }}>Commercial Construction</option>
                                    <option value="Residential Construction" {{ old('industry', $client->industry) == 'Residential Construction' ? 'selected' : '' }}>Residential Construction</option>
                                    <option value="Infrastructure" {{ old('industry', $client->industry) == 'Infrastructure' ? 'selected' : '' }}>Infrastructure</option>
                                    <option value="Healthcare" {{ old('industry', $client->industry) == 'Healthcare' ? 'selected' : '' }}>Healthcare</option>
                                    <option value="Education" {{ old('industry', $client->industry) == 'Education' ? 'selected' : '' }}>Education</option>
                                    <option value="Hospitality" {{ old('industry', $client->industry) == 'Hospitality' ? 'selected' : '' }}>Hospitality</option>
                                    <option value="Retail" {{ old('industry', $client->industry) == 'Retail' ? 'selected' : '' }}>Retail</option>
                                    <option value="Manufacturing" {{ old('industry', $client->industry) == 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                    <option value="Technology" {{ old('industry', $client->industry) == 'Technology' ? 'selected' : '' }}>Technology</option>
                                    <option value="Government" {{ old('industry', $client->industry) == 'Government' ? 'selected' : '' }}>Government</option>
                                    <option value="Other" {{ old('industry', $client->industry) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('industry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="tax_id" class="form-label">Tax ID / EIN</label>
                                <input type="text" class="form-control @error('tax_id') is-invalid @enderror" 
                                       id="tax_id" name="tax_id" value="{{ old('tax_id', $client->tax_id) }}" 
                                       placeholder="XX-XXXXXXX">
                                @error('tax_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="business_description" class="form-label">Business Description</label>
                                <textarea class="form-control @error('business_description') is-invalid @enderror" 
                                          id="business_description" name="business_description" rows="3">{{ old('business_description', $client->business_description) }}</textarea>
                                @error('business_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0" id="contact-info-title">Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="contact_person_name" class="form-label" id="contact-name-label">
                                    Primary Contact Person <span class="text-danger" id="contact-name-required" style="display: none;">*</span>
                                </label>
                                <input type="text" class="form-control @error('contact_person_name') is-invalid @enderror" 
                                       id="contact_person_name" name="contact_person_name" value="{{ old('contact_person_name', $client->contact_person_name) }}">
                                @error('contact_person_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="contact_person_title" class="form-label">Contact Title/Position</label>
                                <input type="text" class="form-control @error('contact_person_title') is-invalid @enderror" 
                                       id="contact_person_title" name="contact_person_title" value="{{ old('contact_person_title', $client->contact_person_title) }}" 
                                       placeholder="e.g., Project Manager, CEO, etc.">
                                @error('contact_person_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="contact_person_email" class="form-label">Contact Email</label>
                                <input type="email" class="form-control @error('contact_person_email') is-invalid @enderror" 
                                       id="contact_person_email" name="contact_person_email" value="{{ old('contact_person_email', $client->contact_person_email) }}">
                                @error('contact_person_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="contact_person_phone" class="form-label">Contact Phone</label>
                                <input type="tel" class="form-control @error('contact_person_phone') is-invalid @enderror" 
                                       id="contact_person_phone" name="contact_person_phone" value="{{ old('contact_person_phone', $client->contact_person_phone) }}">
                                @error('contact_person_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 company-fields">
                                <label for="email" class="form-label">Company General Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $client->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 company-fields">
                                <label for="phone" class="form-label">Company Main Phone</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $client->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 company-fields">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                       id="website" name="website" value="{{ old('website', $client->website) }}" 
                                       placeholder="https://example.com">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0" id="address-info-title">Address Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="address" class="form-label">Street Address</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                       id="address" name="address" value="{{ old('address', $client->address) }}">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                       id="city" name="city" value="{{ old('city', $client->city) }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="state" class="form-label">State/Region</label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                       id="state" name="state" value="{{ old('state', $client->state) }}">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="zip_code" class="form-label">Postal Code</label>
                                <input type="text" class="form-control @error('zip_code') is-invalid @enderror" 
                                       id="zip_code" name="zip_code" value="{{ old('zip_code', $client->zip_code) }}">
                                @error('zip_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Additional Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3" 
                                          placeholder="Any additional notes or special requirements...">{{ old('notes', $client->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" value="1"
                                           id="is_active" name="is_active" {{ old('is_active', $client->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Client is active
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Action buttons -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Actions</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted text-center">
                            Update client information and save changes.
                        </p>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="submit-button">
                                <i class="bi bi-check-circle me-2"></i>Update Client
                            </button>
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Tips</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled small text-muted mb-0" id="tips-list">
                            <li class="mb-2">• You can change between Business Client and Private Client</li>
                            <li class="mb-2">• For Business: Company Name is required</li>
                            <li class="mb-2">• For Private: Client Name is required</li>
                            <li class="mb-2">• Contact details help with project communication</li>
                            <li>• Business type and industry help with reporting (business clients only)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const businessClientRadio = document.getElementById('business_client');
    const privateClientRadio = document.getElementById('private_client');
    const companyInfoSection = document.getElementById('company-info-section');
    const companyFields = document.querySelectorAll('.company-fields');
    const contactInfoTitle = document.getElementById('contact-info-title');
    const contactNameLabel = document.getElementById('contact-name-label');
    const contactNameRequired = document.getElementById('contact-name-required');
    const companyNameField = document.getElementById('company_name');

    function toggleClientType() {
        const isPrivateClient = privateClientRadio.checked;
        const pageTitle = document.getElementById('page-title');
        const pageSubtitle = document.getElementById('page-subtitle');
        const contactNameField = document.getElementById('contact_person_name');
        const submitButton = document.getElementById('submit-button');
        const addressInfoTitle = document.getElementById('address-info-title');
        
        if (isPrivateClient) {
            // Hide company information section
            companyInfoSection.style.display = 'none';
            
            // Hide company-specific fields in contact section
            companyFields.forEach(field => {
                field.style.display = 'none';
                // Disable company-specific inputs to prevent validation
                const inputs = field.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.disabled = true;
                    input.removeAttribute('required');
                });
            });
            
            // Update page titles
            pageTitle.textContent = 'Edit Private Client';
            pageSubtitle.textContent = 'Update individual client information';
            
            // Update labels for private client
            contactInfoTitle.textContent = 'Client Information';
            contactNameLabel.innerHTML = 'Client Name <span class="text-danger">*</span>';
            contactNameRequired.style.display = 'inline';
            
            // Update address section title
            if (addressInfoTitle) {
                addressInfoTitle.textContent = 'Client Address';
            }
            
            // Remove required and disable company name field
            if (companyNameField) {
                companyNameField.removeAttribute('required');
                companyNameField.disabled = true;
            }
            
            // Make contact name required for private clients
            if (contactNameField) {
                contactNameField.setAttribute('required', 'required');
            }
            
            // Update submit button
            if (submitButton) {
                submitButton.innerHTML = '<i class="bi bi-check-circle me-2"></i>Update Private Client';
            }
        } else {
            // Show company information section
            companyInfoSection.style.display = 'block';
            
            // Show company-specific fields in contact section
            companyFields.forEach(field => {
                field.style.display = 'block';
                // Re-enable company-specific inputs
                const inputs = field.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.disabled = false;
                });
            });
            
            // Update page titles
            pageTitle.textContent = 'Edit Business Client';
            pageSubtitle.textContent = 'Update client company information';
            
            // Update labels for business client
            contactInfoTitle.textContent = 'Contact Information';
            contactNameLabel.innerHTML = 'Primary Contact Person';
            contactNameRequired.style.display = 'none';
            
            // Update address section title
            if (addressInfoTitle) {
                addressInfoTitle.textContent = 'Company Address';
            }
            
            // Add required attribute to company name and enable it
            if (companyNameField) {
                companyNameField.setAttribute('required', 'required');
                companyNameField.disabled = false;
            }
            
            // Make contact name optional for business clients
            if (contactNameField) {
                contactNameField.removeAttribute('required');
            }
            
            // Update submit button
            if (submitButton) {
                submitButton.innerHTML = '<i class="bi bi-check-circle me-2"></i>Update Business Client';
            }
        }
    }

    // Initialize on page load
    toggleClientType();

    // Add event listeners
    businessClientRadio.addEventListener('change', toggleClientType);
    privateClientRadio.addEventListener('change', toggleClientType);
});
</script>