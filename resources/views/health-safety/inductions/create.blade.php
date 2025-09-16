@extends('layouts.app')

@section('title', 'New Site Induction')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="header-icon bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                        <i class="bi bi-person-check fs-3"></i>
                    </div>
                    <div>
                        <nav aria-label="breadcrumb" class="mb-2">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('health-safety.index') }}">Health & Safety</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('health-safety.inductions') }}">Site Inductions</a></li>
                                <li class="breadcrumb-item active">New Induction</li>
                            </ol>
                        </nav>
                        <h1 class="page-title mb-1 fw-bold">New Site Induction</h1>
                        <p class="page-subtitle text-muted mb-0">Complete worker induction and safety briefing</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-end">
                <a href="{{ route('health-safety.inductions') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Inductions
                </a>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-light border-0">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-person-check text-success me-2"></i>
                Site Induction Form
            </h5>
            <p class="text-muted mb-0 mt-1">Complete all required fields to process the site induction</p>
        </div>

        <div class="card-body">
            <form action="{{ route('health-safety.inductions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <!-- Main Form Section -->
                    <div class="col-lg-8">
                        <!-- Site and Project Information -->
                        <h5 class="mb-3">Site Information</h5>
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="site_id" class="form-label">Site <span class="text-danger">*</span></label>
                                <select class="form-select @error('site_id') is-invalid @enderror" id="site_id" name="site_id" required>
                                    <option value="">Select a site</option>
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
                                <label for="inducted_at" class="form-label">Induction Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" 
                                       class="form-control @error('inducted_at') is-invalid @enderror" 
                                       id="inducted_at" 
                                       name="inducted_at" 
                                       value="{{ old('inducted_at', now()->format('Y-m-d\TH:i')) }}" 
                                       required>
                                @error('inducted_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Inductee Information -->
                        <h5 class="mb-3">Inductee Information</h5>
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="employee_id" class="form-label">Select Employee (if applicable)</label>
                                <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id">
                                    <option value="">Select existing employee or enter details below</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                            @if($employee->job_title)
                                                - {{ $employee->job_title }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="inductee_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('inductee_name') is-invalid @enderror" 
                                       id="inductee_name" 
                                       name="inductee_name" 
                                       value="{{ old('inductee_name') }}" 
                                       placeholder="Enter full name"
                                       required>
                                @error('inductee_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="inductee_company" class="form-label">Company/Organization</label>
                                <input type="text" 
                                       class="form-control @error('inductee_company') is-invalid @enderror" 
                                       id="inductee_company" 
                                       name="inductee_company" 
                                       value="{{ old('inductee_company') }}" 
                                       placeholder="Company or organization name">
                                @error('inductee_company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="inductee_role" class="form-label">Role/Position</label>
                                <input type="text" 
                                       class="form-control @error('inductee_role') is-invalid @enderror" 
                                       id="inductee_role" 
                                       name="inductee_role" 
                                       value="{{ old('inductee_role') }}" 
                                       placeholder="Job title or role">
                                @error('inductee_role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="inductee_phone" class="form-label">Phone Number</label>
                                <input type="tel" 
                                       class="form-control @error('inductee_phone') is-invalid @enderror" 
                                       id="inductee_phone" 
                                       name="inductee_phone" 
                                       value="{{ old('inductee_phone') }}" 
                                       placeholder="Phone number">
                                @error('inductee_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="inductee_email" class="form-label">Email Address</label>
                                <input type="email" 
                                       class="form-control @error('inductee_email') is-invalid @enderror" 
                                       id="inductee_email" 
                                       name="inductee_email" 
                                       value="{{ old('inductee_email') }}" 
                                       placeholder="Email address">
                                @error('inductee_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <h5 class="mb-3">Emergency Contact</h5>
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                                <input type="text" 
                                       class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                       id="emergency_contact_name" 
                                       name="emergency_contact_name" 
                                       value="{{ old('emergency_contact_name') }}" 
                                       placeholder="Emergency contact full name">
                                @error('emergency_contact_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone</label>
                                <input type="tel" 
                                       class="form-control @error('emergency_contact_phone') is-invalid @enderror" 
                                       id="emergency_contact_phone" 
                                       name="emergency_contact_phone" 
                                       value="{{ old('emergency_contact_phone') }}" 
                                       placeholder="Emergency contact phone">
                                @error('emergency_contact_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Induction Topics -->
                        <h5 class="mb-3">Topics Covered</h5>
                        
                        <div class="mb-4">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <p class="text-muted mb-3">Select all topics covered during the induction:</p>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="topics_covered[]" value="site_overview" id="topic_site_overview" {{ in_array('site_overview', old('topics_covered', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="topic_site_overview">
                                                    Site Overview & Layout
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="topics_covered[]" value="emergency_procedures" id="topic_emergency_procedures" {{ in_array('emergency_procedures', old('topics_covered', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="topic_emergency_procedures">
                                                    Emergency Procedures
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="topics_covered[]" value="ppe_requirements" id="topic_ppe_requirements" {{ in_array('ppe_requirements', old('topics_covered', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="topic_ppe_requirements">
                                                    PPE Requirements
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="topics_covered[]" value="hazard_identification" id="topic_hazard_identification" {{ in_array('hazard_identification', old('topics_covered', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="topic_hazard_identification">
                                                    Hazard Identification
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="topics_covered[]" value="site_rules" id="topic_site_rules" {{ in_array('site_rules', old('topics_covered', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="topic_site_rules">
                                                    Site Rules & Regulations
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="topics_covered[]" value="environmental_awareness" id="topic_environmental_awareness" {{ in_array('environmental_awareness', old('topics_covered', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="topic_environmental_awareness">
                                                    Environmental Awareness
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="topics_covered[]" value="first_aid" id="topic_first_aid" {{ in_array('first_aid', old('topics_covered', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="topic_first_aid">
                                                    First Aid Procedures
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="topics_covered[]" value="reporting_procedures" id="topic_reporting_procedures" {{ in_array('reporting_procedures', old('topics_covered', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="topic_reporting_procedures">
                                                    Incident Reporting
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="topics_covered[]" value="welfare_facilities" id="topic_welfare_facilities" {{ in_array('welfare_facilities', old('topics_covered', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="topic_welfare_facilities">
                                                    Welfare Facilities
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="topics_covered[]" value="vehicle_movement" id="topic_vehicle_movement" {{ in_array('vehicle_movement', old('topics_covered', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="topic_vehicle_movement">
                                                    Vehicle Movement
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Documents Provided -->
                        <h5 class="mb-3">Documents Provided</h5>
                        
                        <div class="mb-4">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <p class="text-muted mb-3">Select all documents provided to the inductee:</p>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="documents_provided[]" value="site_map" id="doc_site_map" {{ in_array('site_map', old('documents_provided', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="doc_site_map">
                                                    Site Map & Layout
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="documents_provided[]" value="emergency_contacts" id="doc_emergency_contacts" {{ in_array('emergency_contacts', old('documents_provided', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="doc_emergency_contacts">
                                                    Emergency Contact List
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="documents_provided[]" value="site_rules" id="doc_site_rules" {{ in_array('site_rules', old('documents_provided', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="doc_site_rules">
                                                    Site Rules Handbook
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="documents_provided[]" value="ppe_guide" id="doc_ppe_guide" {{ in_array('ppe_guide', old('documents_provided', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="doc_ppe_guide">
                                                    PPE Guidelines
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="documents_provided[]" value="visitor_pass" id="doc_visitor_pass" {{ in_array('visitor_pass', old('documents_provided', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="doc_visitor_pass">
                                                    Visitor/Worker Pass
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="documents_provided[]" value="msds_sheets" id="doc_msds_sheets" {{ in_array('msds_sheets', old('documents_provided', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="doc_msds_sheets">
                                                    Material Safety Data Sheets
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Confirmation Checkboxes -->
                        <h5 class="mb-3">Confirmations</h5>
                        
                        <div class="mb-4">
                            <div class="card border-2 border-success bg-success bg-opacity-5">
                                <div class="card-body">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input @error('site_rules_acknowledged') is-invalid @enderror" 
                                               type="checkbox" 
                                               name="site_rules_acknowledged" 
                                               value="1" 
                                               id="site_rules_acknowledged"
                                               {{ old('site_rules_acknowledged') ? 'checked' : '' }}
                                               required>
                                        <label class="form-check-label fw-semibold" for="site_rules_acknowledged">
                                            <i class="bi bi-check-circle text-success me-2"></i>
                                            Site rules and regulations have been explained and acknowledged
                                        </label>
                                        @error('site_rules_acknowledged')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input @error('emergency_procedures_understood') is-invalid @enderror" 
                                               type="checkbox" 
                                               name="emergency_procedures_understood" 
                                               value="1" 
                                               id="emergency_procedures_understood"
                                               {{ old('emergency_procedures_understood') ? 'checked' : '' }}
                                               required>
                                        <label class="form-check-label fw-semibold" for="emergency_procedures_understood">
                                            <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                                            Emergency procedures have been explained and understood
                                        </label>
                                        @error('emergency_procedures_understood')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input @error('ppe_requirements_understood') is-invalid @enderror" 
                                               type="checkbox" 
                                               name="ppe_requirements_understood" 
                                               value="1" 
                                               id="ppe_requirements_understood"
                                               {{ old('ppe_requirements_understood') ? 'checked' : '' }}
                                               required>
                                        <label class="form-check-label fw-semibold" for="ppe_requirements_understood">
                                            <i class="bi bi-shield-check text-primary me-2"></i>
                                            PPE requirements have been explained and understood
                                        </label>
                                        @error('ppe_requirements_understood')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input @error('hazards_communicated') is-invalid @enderror" 
                                               type="checkbox" 
                                               name="hazards_communicated" 
                                               value="1" 
                                               id="hazards_communicated"
                                               {{ old('hazards_communicated') ? 'checked' : '' }}
                                               required>
                                        <label class="form-check-label fw-semibold" for="hazards_communicated">
                                            <i class="bi bi-exclamation-diamond text-danger me-2"></i>
                                            Site hazards and risks have been communicated
                                        </label>
                                        @error('hazards_communicated')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Notes -->
                        <h5 class="mb-3">Additional Notes</h5>
                        
                        <div class="mb-4">
                            <label for="notes" class="form-label">Notes & Comments</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="4" 
                                      placeholder="Any additional notes, observations, or special requirements...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Side Panel -->
                    <div class="col-lg-4">
                        <!-- Validity Information -->
                        <h5 class="mb-3">Certificate Validity</h5>
                        
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="valid_until" class="form-label">Valid Until <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control @error('valid_until') is-invalid @enderror" 
                                           id="valid_until" 
                                           name="valid_until" 
                                           value="{{ old('valid_until', now()->addYear()->format('Y-m-d')) }}" 
                                           required>
                                    @error('valid_until')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="alert alert-info border-0 mb-0">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <small>Standard validity is 12 months from induction date</small>
                                </div>
                            </div>
                        </div>

                        <!-- Induction Checklist -->
                        <h5 class="mb-3">Induction Checklist</h5>
                        
                        <div class="card border-0 bg-light mb-4">
                            <div class="card-body">
                                <div class="checklist-item d-flex align-items-center mb-2">
                                    <i class="bi bi-square text-muted me-2" id="check-site"></i>
                                    <span class="text-muted" id="text-site">Site selected</span>
                                </div>
                                <div class="checklist-item d-flex align-items-center mb-2">
                                    <i class="bi bi-square text-muted me-2" id="check-inductee"></i>
                                    <span class="text-muted" id="text-inductee">Inductee details entered</span>
                                </div>
                                <div class="checklist-item d-flex align-items-center mb-2">
                                    <i class="bi bi-square text-muted me-2" id="check-topics"></i>
                                    <span class="text-muted" id="text-topics">Topics covered selected</span>
                                </div>
                                <div class="checklist-item d-flex align-items-center mb-2">
                                    <i class="bi bi-square text-muted me-2" id="check-confirmations"></i>
                                    <span class="text-muted" id="text-confirmations">All confirmations checked</span>
                                </div>
                                <div class="checklist-item d-flex align-items-center">
                                    <i class="bi bi-square text-muted me-2" id="check-validity"></i>
                                    <span class="text-muted" id="text-validity">Validity date set</span>
                                </div>
                            </div>
                        </div>

                        <!-- Progress -->
                        <h5 class="mb-3">Completion Progress</h5>
                        
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 0%" id="progress-bar"></div>
                        </div>
                        <small class="text-muted">
                            <span id="progress-text">0% Complete</span>
                        </small>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('health-safety.inductions') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancel
                            </a>
                            <div>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>Complete Induction
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
        color: #198754;
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
        border-color: #198754;
        box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.1);
    }

    /* Checkbox styling */
    .form-check-input {
        border-radius: 4px;
    }

    .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }

    .form-check-input:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
    }

    /* Progress bar */
    .progress-bar.bg-success {
        background-color: #198754 !important;
    }

    /* Checklist styling */
    .checklist-item {
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .checklist-item.completed i {
        color: #198754 !important;
    }

    .checklist-item.completed span {
        color: #198754 !important;
        font-weight: 500;
    }

    .checklist-item.completed i::before {
        content: "\f26a"; /* bi-check-square-fill */
    }

    /* Card enhancements */
    .card {
        border-radius: 12px;
    }

    .card.border-2 {
        border-width: 2px !important;
    }

    /* Button styling */
    .btn {
        border-radius: 8px;
        padding: 0.625rem 1.25rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-success:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .page-title {
            font-size: 1.5rem;
        }
        
        .form-check {
            margin-bottom: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    
    // Form validation and progress tracking
    function updateProgress() {
        const requiredFields = form.querySelectorAll('[required]');
        const checklistItems = {
            site: document.getElementById('site_id').value !== '',
            inductee: document.getElementById('inductee_name').value !== '',
            topics: form.querySelectorAll('input[name="topics_covered[]"]:checked').length > 0,
            confirmations: form.querySelectorAll('input[name$="_acknowledged"]:checked, input[name$="_understood"]:checked, input[name$="_communicated"]:checked').length >= 4,
            validity: document.getElementById('valid_until').value !== ''
        };
        
        // Update checklist visual indicators
        Object.keys(checklistItems).forEach(key => {
            const checkIcon = document.getElementById(`check-${key}`);
            const textElement = document.getElementById(`text-${key}`);
            const checklistItem = checkIcon.parentElement;
            
            if (checklistItems[key]) {
                checklistItem.classList.add('completed');
            } else {
                checklistItem.classList.remove('completed');
            }
        });
        
        // Calculate overall progress
        const completedItems = Object.values(checklistItems).filter(Boolean).length;
        const progress = (completedItems / Object.keys(checklistItems).length) * 100;
        
        progressBar.style.width = progress + '%';
        progressText.textContent = Math.round(progress) + '% Complete';
    }
    
    // Employee selection auto-fill
    const employeeSelect = document.getElementById('employee_id');
    const inducteeNameInput = document.getElementById('inductee_name');
    
    employeeSelect.addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            const employeeName = selectedOption.textContent.split(' - ')[0];
            inducteeNameInput.value = employeeName;
        }
        updateProgress();
    });
    
    // Listen for form changes
    form.addEventListener('input', updateProgress);
    form.addEventListener('change', updateProgress);
    
    // Form submission validation
    form.addEventListener('submit', function(e) {
        const requiredCheckboxes = form.querySelectorAll('input[name$="_acknowledged"][required], input[name$="_understood"][required], input[name$="_communicated"][required]');
        let allConfirmed = true;
        
        requiredCheckboxes.forEach(checkbox => {
            if (!checkbox.checked) {
                allConfirmed = false;
                checkbox.classList.add('is-invalid');
            } else {
                checkbox.classList.remove('is-invalid');
            }
        });
        
        if (!allConfirmed) {
            e.preventDefault();
            alert('Please confirm all required safety acknowledgments before completing the induction.');
            return;
        }
        
        // Show confirmation
        if (!confirm('Are you sure you want to complete this site induction? This will generate a certificate for the inductee.')) {
            e.preventDefault();
        }
    });
    
    // Initial progress calculation
    updateProgress();
    
    // Auto-populate datetime if empty
    const inductedAtInput = document.getElementById('inducted_at');
    if (!inductedAtInput.value) {
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        inductedAtInput.value = now.toISOString().slice(0, 16);
    }
});
</script>
@endpush
@endsection


