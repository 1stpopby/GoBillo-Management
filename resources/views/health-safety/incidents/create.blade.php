@extends('layouts.app')

@section('title', 'Report Incident')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="page-header-section mb-4">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="page-icon bg-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('health-safety.index') }}">Health & Safety</a></li>
                        <li class="breadcrumb-item active">Report Incident</li>
                    </ol>
                </nav>
                <h1 class="page-title mb-0">Report Incident</h1>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('health-safety.incidents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <!-- Main Form Section -->
                    <div class="col-lg-8">
                        <h5 class="mb-3">Incident Details</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="incident_number" class="form-label">Incident Number</label>
                                <input type="text" class="form-control" 
                                       id="incident_number" value="INC-{{ date('Ymd') }}-{{ str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) }}" readonly>
                                <small class="text-muted">Auto-generated incident reference</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="incident_type" class="form-label">Incident Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('incident_type') is-invalid @enderror" 
                                        id="incident_type" name="incident_type" required>
                                    <option value="">Select Incident Type</option>
                                    <option value="accident" {{ old('incident_type') == 'accident' ? 'selected' : '' }}>Accident</option>
                                    <option value="near_miss" {{ old('incident_type') == 'near_miss' ? 'selected' : '' }}>Near Miss</option>
                                    <option value="dangerous_occurrence" {{ old('incident_type') == 'dangerous_occurrence' ? 'selected' : '' }}>Dangerous Occurrence</option>
                                    <option value="illness" {{ old('incident_type') == 'illness' ? 'selected' : '' }}>Work-related Illness</option>
                                    <option value="property_damage" {{ old('incident_type') == 'property_damage' ? 'selected' : '' }}>Property Damage</option>
                                    <option value="environmental" {{ old('incident_type') == 'environmental' ? 'selected' : '' }}>Environmental Incident</option>
                                </select>
                                @error('incident_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="site_id" class="form-label">Site <span class="text-danger">*</span></label>
                                <select class="form-select @error('site_id') is-invalid @enderror" 
                                        id="site_id" name="site_id" required>
                                    <option value="">Select Site</option>
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
                                <select class="form-select @error('project_id') is-invalid @enderror" 
                                        id="project_id" name="project_id">
                                    <option value="">Select Project (Optional)</option>
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

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="occurred_at" class="form-label">Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('occurred_at') is-invalid @enderror" 
                                       id="occurred_at" name="occurred_at" 
                                       value="{{ old('occurred_at', now()->format('Y-m-d\TH:i')) }}" required>
                                @error('occurred_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="reported_at" class="form-label">Reported At</label>
                                <input type="datetime-local" class="form-control @error('reported_at') is-invalid @enderror" 
                                       id="reported_at" name="reported_at" 
                                       value="{{ old('reported_at', now()->format('Y-m-d\TH:i')) }}">
                                @error('reported_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Incident Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Provide a detailed description of what happened" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Severity Assessment -->
                        <h5 class="mb-3 mt-4">Severity Assessment</h5>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="severity_level" class="form-label">Severity Level <span class="text-danger">*</span></label>
                                <select class="form-select @error('severity_level') is-invalid @enderror" 
                                        id="severity_level" name="severity_level" required>
                                    <option value="">Select Severity</option>
                                    <option value="minor" {{ old('severity_level') == 'minor' ? 'selected' : '' }}>Minor</option>
                                    <option value="moderate" {{ old('severity_level') == 'moderate' ? 'selected' : '' }}>Moderate</option>
                                    <option value="major" {{ old('severity_level') == 'major' ? 'selected' : '' }}>Major</option>
                                    <option value="critical" {{ old('severity_level') == 'critical' ? 'selected' : '' }}>Critical</option>
                                    <option value="fatal" {{ old('severity_level') == 'fatal' ? 'selected' : '' }}>Fatal</option>
                                </select>
                                @error('severity_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="injury_type" class="form-label">Injury Type</label>
                                <select class="form-select @error('injury_type') is-invalid @enderror" 
                                        id="injury_type" name="injury_type">
                                    <option value="">Select Injury Type</option>
                                    <option value="none" {{ old('injury_type') == 'none' ? 'selected' : '' }}>No Injury</option>
                                    <option value="minor_cut" {{ old('injury_type') == 'minor_cut' ? 'selected' : '' }}>Minor Cut/Bruise</option>
                                    <option value="fracture" {{ old('injury_type') == 'fracture' ? 'selected' : '' }}>Fracture</option>
                                    <option value="head_injury" {{ old('injury_type') == 'head_injury' ? 'selected' : '' }}>Head Injury</option>
                                    <option value="back_injury" {{ old('injury_type') == 'back_injury' ? 'selected' : '' }}>Back Injury</option>
                                    <option value="burns" {{ old('injury_type') == 'burns' ? 'selected' : '' }}>Burns</option>
                                    <option value="other" {{ old('injury_type') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('injury_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="medical_attention" class="form-label">Medical Attention</label>
                                <select class="form-select @error('medical_attention') is-invalid @enderror" 
                                        id="medical_attention" name="medical_attention">
                                    <option value="">Select Level</option>
                                    <option value="none" {{ old('medical_attention') == 'none' ? 'selected' : '' }}>None Required</option>
                                    <option value="first_aid" {{ old('medical_attention') == 'first_aid' ? 'selected' : '' }}>First Aid Only</option>
                                    <option value="doctor" {{ old('medical_attention') == 'doctor' ? 'selected' : '' }}>Doctor Visit</option>
                                    <option value="hospital" {{ old('medical_attention') == 'hospital' ? 'selected' : '' }}>Hospital Treatment</option>
                                    <option value="emergency" {{ old('medical_attention') == 'emergency' ? 'selected' : '' }}>Emergency Treatment</option>
                                </select>
                                @error('medical_attention')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- People Involved -->
                        <h5 class="mb-3 mt-4">People Involved</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="injured_person_name" class="form-label">Injured Person Name</label>
                                <input type="text" class="form-control @error('injured_person_name') is-invalid @enderror" 
                                       id="injured_person_name" name="injured_person_name" 
                                       value="{{ old('injured_person_name') }}" 
                                       placeholder="Full name of injured person">
                                @error('injured_person_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="injured_person_company" class="form-label">Company</label>
                                <input type="text" class="form-control @error('injured_person_company') is-invalid @enderror" 
                                       id="injured_person_company" name="injured_person_company" 
                                       value="{{ old('injured_person_company') }}" 
                                       placeholder="Company name">
                                @error('injured_person_company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="witnesses" class="form-label">Witnesses</label>
                            <textarea class="form-control @error('witnesses') is-invalid @enderror" 
                                      id="witnesses" name="witnesses" rows="3" 
                                      placeholder="Names and contact details of any witnesses">{{ old('witnesses') }}</textarea>
                            @error('witnesses')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Root Cause Analysis -->
                        <h5 class="mb-3 mt-4">Root Cause Analysis</h5>
                        
                        <div class="mb-3">
                            <label for="immediate_cause" class="form-label">Immediate Cause</label>
                            <textarea class="form-control @error('immediate_cause') is-invalid @enderror" 
                                      id="immediate_cause" name="immediate_cause" rows="3" 
                                      placeholder="What directly caused the incident?">{{ old('immediate_cause') }}</textarea>
                            @error('immediate_cause')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="root_causes" class="form-label">Root Causes</label>
                            <textarea class="form-control @error('root_causes') is-invalid @enderror" 
                                      id="root_causes" name="root_causes" rows="3" 
                                      placeholder="Underlying factors that contributed to the incident">{{ old('root_causes') }}</textarea>
                            @error('root_causes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="contributing_factors" class="form-label">Contributing Factors</label>
                            <textarea class="form-control @error('contributing_factors') is-invalid @enderror" 
                                      id="contributing_factors" name="contributing_factors" rows="3" 
                                      placeholder="Other factors that may have contributed">{{ old('contributing_factors') }}</textarea>
                            @error('contributing_factors')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Actions Taken -->
                        <h5 class="mb-3 mt-4">Actions Taken</h5>
                        
                        <div class="mb-3">
                            <label for="immediate_actions" class="form-label">Immediate Actions Taken <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('immediate_actions') is-invalid @enderror" 
                                      id="immediate_actions" name="immediate_actions" rows="3" 
                                      placeholder="What actions were taken immediately after the incident?" required>{{ old('immediate_actions') }}</textarea>
                            @error('immediate_actions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="preventive_measures" class="form-label">Preventive Measures</label>
                            <textarea class="form-control @error('preventive_measures') is-invalid @enderror" 
                                      id="preventive_measures" name="preventive_measures" rows="3" 
                                      placeholder="What measures will prevent this from happening again?">{{ old('preventive_measures') }}</textarea>
                            @error('preventive_measures')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Side Panel -->
                    <div class="col-lg-4">
                        <!-- Reporting Information -->
                        <h5 class="mb-3">Reporting Information</h5>
                        
                        <div class="mb-3">
                            <label for="reported_by" class="form-label">Reported By <span class="text-danger">*</span></label>
                            <select class="form-select @error('reported_by') is-invalid @enderror" 
                                    id="reported_by" name="reported_by" required>
                                <option value="">Select Person</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('reported_by', auth()->id()) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('reported_by')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status">
                                <option value="reported" {{ old('status') == 'reported' ? 'selected' : '' }}>Reported</option>
                                <option value="under_investigation" {{ old('status') == 'under_investigation' ? 'selected' : '' }}>Under Investigation</option>
                                <option value="investigation_complete" {{ old('status') == 'investigation_complete' ? 'selected' : '' }}>Investigation Complete</option>
                                <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select @error('priority') is-invalid @enderror" 
                                    id="priority" name="priority">
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Investigation Details -->
                        <h5 class="mb-3 mt-4">Investigation</h5>
                        
                        <div class="mb-3">
                            <label for="investigated_by" class="form-label">Investigated By</label>
                            <select class="form-select @error('investigated_by') is-invalid @enderror" 
                                    id="investigated_by" name="investigated_by">
                                <option value="">Select Investigator</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('investigated_by') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('investigated_by')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="investigation_date" class="form-label">Investigation Date</label>
                            <input type="date" class="form-control @error('investigation_date') is-invalid @enderror" 
                                   id="investigation_date" name="investigation_date" 
                                   value="{{ old('investigation_date') }}">
                            @error('investigation_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Attachments -->
                        <h5 class="mb-3 mt-4">Attachments</h5>
                        
                        <div class="mb-3">
                            <label for="photos" class="form-label">Photos</label>
                            <input type="file" class="form-control @error('photos') is-invalid @enderror" 
                                   id="photos" name="photos[]" multiple accept=".jpg,.jpeg,.png">
                            <small class="text-muted">Upload incident photos (JPG, PNG)</small>
                            @error('photos')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="documents" class="form-label">Documents</label>
                            <input type="file" class="form-control @error('documents') is-invalid @enderror" 
                                   id="documents" name="documents[]" multiple accept=".pdf,.doc,.docx">
                            <small class="text-muted">Upload related documents (PDF, DOC, DOCX)</small>
                            @error('documents')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Cost Impact -->
                        <h5 class="mb-3 mt-4">Cost Impact</h5>
                        
                        <div class="mb-3">
                            <label for="estimated_cost" class="form-label">Estimated Cost</label>
                            <div class="input-group">
                                <span class="input-group-text">£</span>
                                <input type="number" class="form-control @error('estimated_cost') is-invalid @enderror" 
                                       id="estimated_cost" name="estimated_cost" 
                                       value="{{ old('estimated_cost') }}" 
                                       placeholder="0.00" step="0.01" min="0">
                            </div>
                            @error('estimated_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="cost_category" class="form-label">Cost Category</label>
                            <select class="form-select @error('cost_category') is-invalid @enderror" 
                                    id="cost_category" name="cost_category">
                                <option value="">Select Category</option>
                                <option value="medical" {{ old('cost_category') == 'medical' ? 'selected' : '' }}>Medical</option>
                                <option value="equipment" {{ old('cost_category') == 'equipment' ? 'selected' : '' }}>Equipment</option>
                                <option value="property" {{ old('cost_category') == 'property' ? 'selected' : '' }}>Property</option>
                                <option value="legal" {{ old('cost_category') == 'legal' ? 'selected' : '' }}>Legal</option>
                                <option value="other" {{ old('cost_category') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('cost_category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('health-safety.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancel
                            </a>
                            <div>
                                <button type="submit" name="action" value="save_draft" class="btn btn-outline-warning me-2">
                                    <i class="bi bi-save me-2"></i>Save as Draft
                                </button>
                                <button type="submit" name="action" value="save" class="btn btn-warning">
                                    <i class="bi bi-exclamation-triangle me-2"></i>Report Incident
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
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }

    .page-icon.bg-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
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
        color: #ffc107;
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
        border-color: #ffc107;
        box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.1);
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

    .btn-warning {
        background: #ffc107;
        border-color: #ffc107;
        color: #000;
    }

    .btn-warning:hover {
        background: #e0a800;
        border-color: #e0a800;
        color: #000;
        transform: translateY(-1px);
    }

    .btn-outline-warning {
        color: #ffc107;
        border-color: #ffc107;
    }

    .btn-outline-warning:hover {
        background: #ffc107;
        border-color: #ffc107;
        color: #000;
    }

    .btn-secondary {
        background: #64748b;
        border-color: #64748b;
    }

    .btn-secondary:hover {
        background: #475569;
        border-color: #475569;
    }

    .input-group-text {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: #64748b;
    }
</style>
@endpush
@endsection


