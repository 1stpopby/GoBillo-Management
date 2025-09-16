@extends('layouts.app')

@section('title', 'Company Settings')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Company Settings</h1>
            <p class="text-muted mb-0">Manage your company information, compliance, and business details</p>
        </div>
        <div>
            <span class="badge bg-{{ $company->status === 'active' ? 'success' : 'warning' }}">
                {{ ucfirst($company->status) }}
            </span>
        </div>
    </div>

    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" id="settingsForm">
        @csrf
        @method('PUT')

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">
                    <i class="bi bi-building me-2"></i>Basic Information
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab">
                    <i class="bi bi-person-lines-fill me-2"></i>Contact Details
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="address-tab" data-bs-toggle="tab" data-bs-target="#address" type="button" role="tab">
                    <i class="bi bi-geo-alt me-2"></i>Addresses
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="banking-tab" data-bs-toggle="tab" data-bs-target="#banking" type="button" role="tab">
                    <i class="bi bi-bank me-2"></i>Banking Details
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="insurance-tab" data-bs-toggle="tab" data-bs-target="#insurance" type="button" role="tab">
                    <i class="bi bi-shield-check me-2"></i>Insurance
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="compliance-tab" data-bs-toggle="tab" data-bs-target="#compliance" type="button" role="tab">
                    <i class="bi bi-file-earmark-check me-2"></i>Compliance
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="preferences-tab" data-bs-toggle="tab" data-bs-target="#preferences" type="button" role="tab">
                    <i class="bi bi-gear me-2"></i>Preferences
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab">
                    <i class="bi bi-envelope-gear me-2"></i>Email Settings
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="settingsTabsContent">
            <!-- Basic Information Tab -->
            <div class="tab-pane fade show active" id="basic" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-building me-2"></i>Company Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $company->name) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="trading_name" class="form-label">Trading Name</label>
                                        <input type="text" class="form-control" id="trading_name" name="trading_name" value="{{ old('trading_name', $company->trading_name) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="company_number" class="form-label">Company Number</label>
                                        <input type="text" class="form-control" id="company_number" name="company_number" value="{{ old('company_number', $company->company_number) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="business_type" class="form-label">Business Type</label>
                                        <select class="form-select" id="business_type" name="business_type">
                                            <option value="">Select Business Type</option>
                                            <option value="limited_company" {{ old('business_type', $company->business_type) === 'limited_company' ? 'selected' : '' }}>Limited Company</option>
                                            <option value="llp" {{ old('business_type', $company->business_type) === 'llp' ? 'selected' : '' }}>Limited Liability Partnership</option>
                                            <option value="partnership" {{ old('business_type', $company->business_type) === 'partnership' ? 'selected' : '' }}>Partnership</option>
                                            <option value="sole_trader" {{ old('business_type', $company->business_type) === 'sole_trader' ? 'selected' : '' }}>Sole Trader</option>
                                            <option value="other" {{ old('business_type', $company->business_type) === 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="incorporation_date" class="form-label">Incorporation Date</label>
                                        <input type="date" class="form-control" id="incorporation_date" name="incorporation_date" value="{{ old('incorporation_date', $company->incorporation_date?->format('Y-m-d')) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="industry_sector" class="form-label">Industry Sector</label>
                                        <select class="form-select" id="industry_sector" name="industry_sector">
                                            <option value="">Select Industry</option>
                                            <option value="construction" {{ old('industry_sector', $company->industry_sector) === 'construction' ? 'selected' : '' }}>General Construction</option>
                                            <option value="electrical" {{ old('industry_sector', $company->industry_sector) === 'electrical' ? 'selected' : '' }}>Electrical</option>
                                            <option value="plumbing" {{ old('industry_sector', $company->industry_sector) === 'plumbing' ? 'selected' : '' }}>Plumbing & Heating</option>
                                            <option value="roofing" {{ old('industry_sector', $company->industry_sector) === 'roofing' ? 'selected' : '' }}>Roofing</option>
                                            <option value="carpentry" {{ old('industry_sector', $company->industry_sector) === 'carpentry' ? 'selected' : '' }}>Carpentry & Joinery</option>
                                            <option value="groundwork" {{ old('industry_sector', $company->industry_sector) === 'groundwork' ? 'selected' : '' }}>Groundwork</option>
                                            <option value="demolition" {{ old('industry_sector', $company->industry_sector) === 'demolition' ? 'selected' : '' }}>Demolition</option>
                                            <option value="scaffolding" {{ old('industry_sector', $company->industry_sector) === 'scaffolding' ? 'selected' : '' }}>Scaffolding</option>
                                            <option value="other" {{ old('industry_sector', $company->industry_sector) === 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-3 mt-3">
                                    <div class="col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_vat_registered" name="is_vat_registered" {{ old('is_vat_registered', $company->is_vat_registered) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_vat_registered">
                                                VAT Registered
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_cis_registered" name="is_cis_registered" {{ old('is_cis_registered', $company->is_cis_registered) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_cis_registered">
                                                CIS Registered
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="gdpr_compliant" name="gdpr_compliant" {{ old('gdpr_compliant', $company->gdpr_compliant) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="gdpr_compliant">
                                                GDPR Compliant
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mt-3" id="tax-fields">
                                    <div class="col-md-6">
                                        <label for="vat_number" class="form-label">VAT Number</label>
                                        <input type="text" class="form-control" id="vat_number" name="vat_number" value="{{ old('vat_number', $company->vat_number) }}" placeholder="GB123456789">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="utr_number" class="form-label">UTR Number</label>
                                        <input type="text" class="form-control" id="utr_number" name="utr_number" value="{{ old('utr_number', $company->utr_number) }}" placeholder="1234567890">
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label for="business_description" class="form-label">Business Description</label>
                                    <textarea class="form-control" id="business_description" name="business_description" rows="4" placeholder="Describe your business activities and services">{{ old('business_description', $company->business_description) }}</textarea>
                                </div>

                                <!-- Services Offered -->
                                <div class="mt-4">
                                    <label class="form-label">Services Offered</label>
                                    <div class="row g-2">
                                        @php
                                            $servicesOffered = old('services_offered', $company->services_offered ?? []);
                                            $availableServices = [
                                                'construction' => 'General Construction',
                                                'electrical' => 'Electrical Work',
                                                'plumbing' => 'Plumbing & Heating',
                                                'roofing' => 'Roofing',
                                                'carpentry' => 'Carpentry & Joinery',
                                                'groundwork' => 'Groundwork',
                                                'demolition' => 'Demolition',
                                                'scaffolding' => 'Scaffolding',
                                                'painting' => 'Painting & Decorating',
                                                'flooring' => 'Flooring',
                                                'tiling' => 'Tiling',
                                                'landscaping' => 'Landscaping',
                                                'project_management' => 'Project Management',
                                                'design' => 'Design Services',
                                                'maintenance' => 'Maintenance Services',
                                                'refurbishment' => 'Refurbishment',
                                                'new_builds' => 'New Builds',
                                                'extensions' => 'Extensions',
                                                'conversions' => 'Conversions',
                                                'commercial' => 'Commercial Work',
                                                'residential' => 'Residential Work',
                                                'industrial' => 'Industrial Work'
                                            ];
                                        @endphp
                                        @foreach($availableServices as $key => $service)
                                            <div class="col-md-4 col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="service_{{ $key }}" name="services_offered[]" value="{{ $key }}" {{ in_array($key, $servicesOffered) ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="service_{{ $key }}">
                                                        {{ $service }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Company Logo -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="bi bi-image me-2"></i>Company Logo</h6>
                            </div>
                            <div class="card-body text-center">
                                <div class="logo-preview mb-3">
                                    @if($company->logo)
                                        <img src="{{ Storage::url($company->logo) }}" alt="Company Logo" class="img-fluid rounded" style="max-height: 150px;">
                                    @else
                                        <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif
                                </div>
                                <input type="file" class="form-control mb-2" id="logo" name="logo" accept="image/*">
                                <small class="text-muted">Maximum file size: 2MB. Supported formats: JPEG, PNG, JPG, GIF</small>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="card border-0 shadow-sm mt-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Company Overview</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3 text-center">
                                    <div class="col-6">
                                        <div class="border-end">
                                            <h4 class="text-primary mb-1">{{ $company->users()->count() }}</h4>
                                            <small class="text-muted">Users</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-success mb-1">{{ $company->projects()->count() }}</h4>
                                        <small class="text-muted">Projects</small>
                                    </div>
                                    <div class="col-6">
                                        <div class="border-end border-top pt-3">
                                            <h4 class="text-warning mb-1">{{ $company->clients()->count() }}</h4>
                                            <small class="text-muted">Clients</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border-top pt-3">
                                            <h4 class="text-info mb-1">{{ $company->sites()->count() }}</h4>
                                            <small class="text-muted">Sites</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            @include('settings.tabs')
        </div>

        <!-- Save Button -->
        @if(auth()->user()->isCompanyAdmin())
            <div class="text-center mt-4 mb-5">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-check-circle me-2"></i>Save Company Settings
                </button>
            </div>
        @else
            <div class="text-center mt-4 mb-5">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>View Only Mode:</strong> Contact your Company Administrator to make changes to these settings.
                </div>
            </div>
        @endif
    </form>
</div>

@push('scripts')
<script>
// Toggle VAT/UTR fields based on registration status
document.getElementById('is_vat_registered').addEventListener('change', function() {
    const vatField = document.getElementById('vat_number');
    if (this.checked) {
        vatField.setAttribute('required', 'required');
    } else {
        vatField.removeAttribute('required');
    }
});

// Copy business address to registered address
function copyBusinessAddress() {
    const checkbox = document.getElementById('same_as_business');
    if (checkbox.checked) {
        document.getElementById('registered_address').value = document.getElementById('address').value;
        document.getElementById('registered_city').value = document.getElementById('city').value;
        document.getElementById('registered_state').value = document.getElementById('state').value;
        document.getElementById('registered_zip_code').value = document.getElementById('zip_code').value;
        document.getElementById('registered_country').value = document.getElementById('country').value;
    } else {
        document.getElementById('registered_address').value = '';
        document.getElementById('registered_city').value = '';
        document.getElementById('registered_state').value = '';
        document.getElementById('registered_zip_code').value = '';
        document.getElementById('registered_country').value = '';
    }
}

// Form validation
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    const vatRegistered = document.getElementById('is_vat_registered').checked;
    const vatNumber = document.getElementById('vat_number').value.trim();
    
    if (vatRegistered && !vatNumber) {
        e.preventDefault();
        alert('VAT Number is required when VAT Registered is checked.');
        document.getElementById('vat_number').focus();
        return false;
    }
});

// Logo preview
document.getElementById('logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.querySelector('.logo-preview img, .logo-preview .bg-light');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                preview.outerHTML = `<img src="${e.target.result}" alt="Company Logo" class="img-fluid rounded" style="max-height: 150px;">`;
            }
        };
        reader.readAsDataURL(file);
    }
});

// Auto-save draft
let autoSaveTimeout;
document.querySelectorAll('input, textarea, select').forEach(element => {
    element.addEventListener('change', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            console.log('Auto-saving draft...');
            // Could implement draft saving here
        }, 2000);
    });
});

// Tab persistence
document.addEventListener('DOMContentLoaded', function() {
    const activeTab = localStorage.getItem('activeSettingsTab');
    if (activeTab) {
        const tabTrigger = document.querySelector(`[data-bs-target="${activeTab}"]`);
        if (tabTrigger) {
            const tab = new bootstrap.Tab(tabTrigger);
            tab.show();
        }
    }
});

document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
    tab.addEventListener('shown.bs.tab', function(e) {
        localStorage.setItem('activeSettingsTab', e.target.getAttribute('data-bs-target'));
    });
});
</script>
@endpush

@push('styles')
<style>
.nav-tabs .nav-link {
    color: #6c757d;
    border: 1px solid transparent;
    border-radius: 0.375rem 0.375rem 0 0;
}

.nav-tabs .nav-link:hover {
    color: #0d6efd;
    border-color: #e9ecef #e9ecef #dee2e6;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.card-header {
    font-weight: 600;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.logo-preview {
    min-height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush
@endsection
