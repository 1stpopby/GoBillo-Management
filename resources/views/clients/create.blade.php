@extends('layouts.app')

@section('title', 'Create Client')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clients</a></li>
                        <li class="breadcrumb-item active">Create Client</li>
                    </ol>
                </nav>
                <h1 class="page-title">Create New Client Company</h1>
                <p class="page-subtitle">Add a new client company to your system</p>
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

    <form action="{{ route('clients.store') }}" method="POST">
        @csrf
        
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
                                                   {{ old('is_private_client', '0') == '0' ? 'checked' : '' }}>
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
                                                   {{ old('is_private_client') == '1' ? 'checked' : '' }}>
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
                                       id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="legal_name" class="form-label">Legal Name</label>
                                <input type="text" class="form-control @error('legal_name') is-invalid @enderror" 
                                       id="legal_name" name="legal_name" value="{{ old('legal_name') }}" 
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
                                    <option value="LLC" {{ old('business_type') == 'LLC' ? 'selected' : '' }}>LLC</option>
                                    <option value="Corporation" {{ old('business_type') == 'Corporation' ? 'selected' : '' }}>Corporation</option>
                                    <option value="Partnership" {{ old('business_type') == 'Partnership' ? 'selected' : '' }}>Partnership</option>
                                    <option value="Sole Proprietorship" {{ old('business_type') == 'Sole Proprietorship' ? 'selected' : '' }}>Sole Proprietorship</option>
                                    <option value="Non-Profit" {{ old('business_type') == 'Non-Profit' ? 'selected' : '' }}>Non-Profit</option>
                                    <option value="Government" {{ old('business_type') == 'Government' ? 'selected' : '' }}>Government</option>
                                    <option value="Other" {{ old('business_type') == 'Other' ? 'selected' : '' }}>Other</option>
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
                                    <option value="Real Estate Development" {{ old('industry') == 'Real Estate Development' ? 'selected' : '' }}>Real Estate Development</option>
                                    <option value="Commercial Construction" {{ old('industry') == 'Commercial Construction' ? 'selected' : '' }}>Commercial Construction</option>
                                    <option value="Residential Construction" {{ old('industry') == 'Residential Construction' ? 'selected' : '' }}>Residential Construction</option>
                                    <option value="Infrastructure" {{ old('industry') == 'Infrastructure' ? 'selected' : '' }}>Infrastructure</option>
                                    <option value="Healthcare" {{ old('industry') == 'Healthcare' ? 'selected' : '' }}>Healthcare</option>
                                    <option value="Education" {{ old('industry') == 'Education' ? 'selected' : '' }}>Education</option>
                                    <option value="Hospitality" {{ old('industry') == 'Hospitality' ? 'selected' : '' }}>Hospitality</option>
                                    <option value="Retail" {{ old('industry') == 'Retail' ? 'selected' : '' }}>Retail</option>
                                    <option value="Manufacturing" {{ old('industry') == 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                    <option value="Technology" {{ old('industry') == 'Technology' ? 'selected' : '' }}>Technology</option>
                                    <option value="Government" {{ old('industry') == 'Government' ? 'selected' : '' }}>Government</option>
                                    <option value="Other" {{ old('industry') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('industry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="tax_id" class="form-label">Tax ID / EIN</label>
                                <input type="text" class="form-control @error('tax_id') is-invalid @enderror" 
                                       id="tax_id" name="tax_id" value="{{ old('tax_id') }}" 
                                       placeholder="XX-XXXXXXX">
                                @error('tax_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="business_description" class="form-label">Business Description</label>
                                <textarea class="form-control @error('business_description') is-invalid @enderror" 
                                          id="business_description" name="business_description" rows="3">{{ old('business_description') }}</textarea>
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
                                       id="contact_person_name" name="contact_person_name" value="{{ old('contact_person_name') }}">
                                @error('contact_person_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="contact_person_title" class="form-label">Contact Title/Position</label>
                                <input type="text" class="form-control @error('contact_person_title') is-invalid @enderror" 
                                       id="contact_person_title" name="contact_person_title" value="{{ old('contact_person_title') }}" 
                                       placeholder="e.g., Project Manager, CEO, etc.">
                                @error('contact_person_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="contact_person_email" class="form-label">Contact Email</label>
                                <input type="email" class="form-control @error('contact_person_email') is-invalid @enderror" 
                                       id="contact_person_email" name="contact_person_email" value="{{ old('contact_person_email') }}">
                                @error('contact_person_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="contact_person_phone" class="form-label">Contact Phone</label>
                                <input type="tel" class="form-control @error('contact_person_phone') is-invalid @enderror" 
                                       id="contact_person_phone" name="contact_person_phone" value="{{ old('contact_person_phone') }}">
                                @error('contact_person_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 company-fields">
                                <label for="email" class="form-label">Company General Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 company-fields">
                                <label for="phone" class="form-label">Company Main Phone</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                       id="website" name="website" value="{{ old('website') }}" 
                                       placeholder="https://www.example.com">
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
                        <h5 class="card-title mb-0">Company Address</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="address" class="form-label">Street Address</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                       id="address" name="address" value="{{ old('address') }}">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                       id="city" name="city" value="{{ old('city') }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="state" class="form-label">State/Province</label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                       id="state" name="state" value="{{ old('state') }}">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="zip_code" class="form-label">ZIP/Postal Code</label>
                                <input type="text" class="form-control @error('zip_code') is-invalid @enderror" 
                                       id="zip_code" name="zip_code" value="{{ old('zip_code') }}">
                                @error('zip_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Notes -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Additional Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="4">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active Client
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Client Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="bi bi-building display-4 text-muted"></i>
                        </div>
                        <p class="text-muted text-center">
                            Create a new client company record with complete business information and contact details.
                        </p>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-2"></i>Create Client Company
                            </button>
                            <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
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
                        <ul class="list-unstyled small text-muted mb-0">
                            <li class="mb-2">• Only Company Name is required</li>
                            <li class="mb-2">• Legal Name is used for contracts if different</li>
                            <li class="mb-2">• Contact person details help with project communication</li>
                            <li>• Business type and industry help with reporting</li>
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
        
        if (isPrivateClient) {
            // Hide company information section
            companyInfoSection.style.display = 'none';
            
            // Hide company-specific fields in contact section
            companyFields.forEach(field => {
                field.style.display = 'none';
            });
            
            // Update labels for private client
            contactInfoTitle.textContent = 'Client Information';
            contactNameLabel.innerHTML = 'Client Name <span class="text-danger">*</span>';
            contactNameRequired.style.display = 'inline';
            
            // Remove required attribute from company name
            if (companyNameField) {
                companyNameField.removeAttribute('required');
            }
        } else {
            // Show company information section
            companyInfoSection.style.display = 'block';
            
            // Show company-specific fields in contact section
            companyFields.forEach(field => {
                field.style.display = 'block';
            });
            
            // Update labels for business client
            contactInfoTitle.textContent = 'Contact Information';
            contactNameLabel.innerHTML = 'Primary Contact Person';
            contactNameRequired.style.display = 'none';
            
            // Add required attribute to company name
            if (companyNameField) {
                companyNameField.setAttribute('required', 'required');
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