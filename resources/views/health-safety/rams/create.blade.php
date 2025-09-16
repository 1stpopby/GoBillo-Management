@extends('layouts.app')

@section('title', 'Create RAMS Document')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="page-header-section mb-4">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="page-icon">
                    <i class="bi bi-file-earmark-shield"></i>
                </div>
            </div>
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('health-safety.index') }}">Health & Safety</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('health-safety.rams') }}">RAMS</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </nav>
                <h1 class="page-title mb-0">Create Risk Assessment & Method Statement</h1>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('health-safety.rams.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-lg-8">
                        <h5 class="mb-3">Document Information</h5>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Document Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="reference_number" class="form-label">Reference Number</label>
                            <input type="text" class="form-control @error('reference_number') is-invalid @enderror" 
                                   id="reference_number" name="reference_number" value="{{ old('reference_number', 'RAMS-' . date('Ymd') . '-' . rand(1000, 9999)) }}">
                            @error('reference_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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

                        <div class="mb-3">
                            <label for="task_description" class="form-label">Task/Activity Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('task_description') is-invalid @enderror" 
                                      id="task_description" name="task_description" rows="3" required>{{ old('task_description') }}</textarea>
                            @error('task_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Risk Assessment Section -->
                        <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                            <h5 class="mb-0">Risk Assessments</h5>
                            <button type="button" id="addRiskAssessment" class="btn btn-outline-success btn-sm">
                                <i class="bi bi-plus-circle me-1"></i>Add Risk Assessment
                            </button>
                        </div>
                        
                        <div id="riskAssessmentsContainer">
                            <!-- Initial Risk Assessment -->
                            <div class="risk-assessment-item border rounded p-3 mb-3" data-index="0">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0 text-primary">Risk Assessment #1</h6>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-risk-assessment" style="display: none;">
                                        <i class="bi bi-trash me-1"></i>Remove
                                    </button>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Identified Hazards <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('risk_assessments.0.hazards') is-invalid @enderror" 
                                              name="risk_assessments[0][hazards]" rows="3" 
                                              placeholder="List all potential hazards associated with this task" required>{{ old('risk_assessments.0.hazards') }}</textarea>
                                    @error('risk_assessments.0.hazards')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Risk Level <span class="text-danger">*</span></label>
                                        <select class="form-select @error('risk_assessments.0.risk_level') is-invalid @enderror" 
                                                name="risk_assessments[0][risk_level]" required>
                                            <option value="">Select Risk Level</option>
                                            <option value="low" {{ old('risk_assessments.0.risk_level') == 'low' ? 'selected' : '' }}>Low</option>
                                            <option value="medium" {{ old('risk_assessments.0.risk_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="high" {{ old('risk_assessments.0.risk_level') == 'high' ? 'selected' : '' }}>High</option>
                                            <option value="very_high" {{ old('risk_assessments.0.risk_level') == 'very_high' ? 'selected' : '' }}>Very High</option>
                                        </select>
                                        @error('risk_assessments.0.risk_level')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Likelihood</label>
                                        <select class="form-select @error('risk_assessments.0.likelihood') is-invalid @enderror" 
                                                name="risk_assessments[0][likelihood]">
                                            <option value="">Select Likelihood</option>
                                            <option value="rare" {{ old('risk_assessments.0.likelihood') == 'rare' ? 'selected' : '' }}>Rare</option>
                                            <option value="unlikely" {{ old('risk_assessments.0.likelihood') == 'unlikely' ? 'selected' : '' }}>Unlikely</option>
                                            <option value="possible" {{ old('risk_assessments.0.likelihood') == 'possible' ? 'selected' : '' }}>Possible</option>
                                            <option value="likely" {{ old('risk_assessments.0.likelihood') == 'likely' ? 'selected' : '' }}>Likely</option>
                                            <option value="almost_certain" {{ old('risk_assessments.0.likelihood') == 'almost_certain' ? 'selected' : '' }}>Almost Certain</option>
                                        </select>
                                        @error('risk_assessments.0.likelihood')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Severity</label>
                                        <select class="form-select @error('risk_assessments.0.severity') is-invalid @enderror" 
                                                name="risk_assessments[0][severity]">
                                            <option value="">Select Severity</option>
                                            <option value="negligible" {{ old('risk_assessments.0.severity') == 'negligible' ? 'selected' : '' }}>Negligible</option>
                                            <option value="minor" {{ old('risk_assessments.0.severity') == 'minor' ? 'selected' : '' }}>Minor</option>
                                            <option value="moderate" {{ old('risk_assessments.0.severity') == 'moderate' ? 'selected' : '' }}>Moderate</option>
                                            <option value="major" {{ old('risk_assessments.0.severity') == 'major' ? 'selected' : '' }}>Major</option>
                                            <option value="catastrophic" {{ old('risk_assessments.0.severity') == 'catastrophic' ? 'selected' : '' }}>Catastrophic</option>
                                        </select>
                                        @error('risk_assessments.0.severity')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Control Measures</label>
                                    <textarea class="form-control @error('risk_assessments.0.control_measures') is-invalid @enderror" 
                                              name="risk_assessments[0][control_measures]" rows="2" 
                                              placeholder="Describe specific control measures for this hazard">{{ old('risk_assessments.0.control_measures') }}</textarea>
                                    @error('risk_assessments.0.control_measures')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Method Statement Section -->
                        <h5 class="mb-3 mt-4">Method Statement</h5>
                        
                        <div class="mb-3">
                            <label for="control_measures" class="form-label">Control Measures <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('control_measures') is-invalid @enderror" 
                                      id="control_measures" name="control_measures" rows="4" 
                                      placeholder="Describe the control measures to mitigate the identified risks" required>{{ old('control_measures') }}</textarea>
                            @error('control_measures')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sequence_of_work" class="form-label">Sequence of Work <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('sequence_of_work') is-invalid @enderror" 
                                      id="sequence_of_work" name="sequence_of_work" rows="4" 
                                      placeholder="Step-by-step sequence of how the work will be carried out" required>{{ old('sequence_of_work') }}</textarea>
                            @error('sequence_of_work')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="ppe_required" class="form-label">PPE Required</label>
                            <textarea class="form-control @error('ppe_required') is-invalid @enderror" 
                                      id="ppe_required" name="ppe_required" rows="3" 
                                      placeholder="List all Personal Protective Equipment required">{{ old('ppe_required') }}</textarea>
                            @error('ppe_required')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="training_required" class="form-label">Training Required</label>
                            <textarea class="form-control @error('training_required') is-invalid @enderror" 
                                      id="training_required" name="training_required" rows="3" 
                                      placeholder="Specify any training requirements for workers">{{ old('training_required') }}</textarea>
                            @error('training_required')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="emergency_procedures" class="form-label">Emergency Procedures</label>
                            <textarea class="form-control @error('emergency_procedures') is-invalid @enderror" 
                                      id="emergency_procedures" name="emergency_procedures" rows="3" 
                                      placeholder="Describe emergency procedures and contacts">{{ old('emergency_procedures') }}</textarea>
                            @error('emergency_procedures')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Side Panel -->
                    <div class="col-lg-4">
                        <h5 class="mb-3">Document Details</h5>
                        
                        <div class="mb-3">
                            <label for="valid_from" class="form-label">Valid From <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('valid_from') is-invalid @enderror" 
                                   id="valid_from" name="valid_from" value="{{ old('valid_from', date('Y-m-d')) }}" required>
                            @error('valid_from')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="valid_until" class="form-label">Valid Until <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('valid_until') is-invalid @enderror" 
                                   id="valid_until" name="valid_until" value="{{ old('valid_until', date('Y-m-d', strtotime('+1 year'))) }}" required>
                            @error('valid_until')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status">
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="pending_approval" {{ old('status') == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                                <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="approved_by" class="form-label">Approved By</label>
                            <select class="form-select @error('approved_by') is-invalid @enderror" 
                                    id="approved_by" name="approved_by">
                                <option value="">Select Approver</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('approved_by') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('approved_by')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <h5 class="mb-3 mt-4">Attachments</h5>
                        
                        <div class="mb-3">
                            <label for="file_path" class="form-label">Upload Document</label>
                            <input type="file" class="form-control @error('file_path') is-invalid @enderror" 
                                   id="file_path" name="file_path" accept=".pdf,.doc,.docx">
                            <small class="text-muted">Supported formats: PDF, DOC, DOCX (Max: 10MB)</small>
                            @error('file_path')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <h5 class="mb-3 mt-4">Additional Notes</h5>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="4" 
                                      placeholder="Any additional notes or comments">{{ old('notes') }}</textarea>
                            @error('notes')
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
                            <a href="{{ route('health-safety.rams') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancel
                            </a>
                            <div>
                                <button type="submit" name="action" value="save_draft" class="btn btn-outline-primary me-2">
                                    <i class="bi bi-save me-2"></i>Save as Draft
                                </button>
                                <button type="submit" name="action" value="save" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Create RAMS
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
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
        color: #667eea;
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
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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

    .btn-primary {
        background: #667eea;
        border-color: #667eea;
    }

    .btn-primary:hover {
        background: #5a67d8;
        border-color: #5a67d8;
        transform: translateY(-1px);
    }

    .btn-outline-primary {
        color: #667eea;
        border-color: #667eea;
    }

    .btn-outline-primary:hover {
        background: #667eea;
        border-color: #667eea;
    }

    .btn-secondary {
        background: #64748b;
        border-color: #64748b;
    }

    .btn-secondary:hover {
        background: #475569;
        border-color: #475569;
    }

    .risk-assessment-item {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef !important;
        transition: all 0.2s ease;
    }

    .risk-assessment-item:hover {
        border-color: #667eea !important;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
    }

    .remove-risk-assessment {
        opacity: 0.7;
        transition: all 0.2s ease;
    }

    .remove-risk-assessment:hover {
        opacity: 1;
    }

    #addRiskAssessment {
        transition: all 0.2s ease;
    }

    #addRiskAssessment:hover {
        transform: translateY(-1px);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let riskAssessmentIndex = 0;
    const container = document.getElementById('riskAssessmentsContainer');
    const addButton = document.getElementById('addRiskAssessment');

    // Function to update risk assessment numbers
    function updateRiskAssessmentNumbers() {
        const items = container.querySelectorAll('.risk-assessment-item');
        items.forEach((item, index) => {
            const header = item.querySelector('h6');
            header.textContent = `Risk Assessment #${index + 1}`;
            item.setAttribute('data-index', index);
            
            // Update form field names
            const inputs = item.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    const newName = name.replace(/\[\d+\]/, `[${index}]`);
                    input.setAttribute('name', newName);
                }
            });
        });
        
        // Show/hide remove buttons
        const removeButtons = container.querySelectorAll('.remove-risk-assessment');
        removeButtons.forEach((button, index) => {
            button.style.display = items.length > 1 ? 'inline-block' : 'none';
        });
    }

    // Function to create a new risk assessment item
    function createRiskAssessmentItem(index) {
        return `
            <div class="risk-assessment-item border rounded p-3 mb-3" data-index="${index}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 text-primary">Risk Assessment #${index + 1}</h6>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-risk-assessment">
                        <i class="bi bi-trash me-1"></i>Remove
                    </button>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Identified Hazards <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="risk_assessments[${index}][hazards]" rows="3" 
                              placeholder="List all potential hazards associated with this task" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Risk Level <span class="text-danger">*</span></label>
                        <select class="form-select" name="risk_assessments[${index}][risk_level]" required>
                            <option value="">Select Risk Level</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="very_high">Very High</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Likelihood</label>
                        <select class="form-select" name="risk_assessments[${index}][likelihood]">
                            <option value="">Select Likelihood</option>
                            <option value="rare">Rare</option>
                            <option value="unlikely">Unlikely</option>
                            <option value="possible">Possible</option>
                            <option value="likely">Likely</option>
                            <option value="almost_certain">Almost Certain</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Severity</label>
                        <select class="form-select" name="risk_assessments[${index}][severity]">
                            <option value="">Select Severity</option>
                            <option value="negligible">Negligible</option>
                            <option value="minor">Minor</option>
                            <option value="moderate">Moderate</option>
                            <option value="major">Major</option>
                            <option value="catastrophic">Catastrophic</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Control Measures</label>
                    <textarea class="form-control" name="risk_assessments[${index}][control_measures]" rows="2" 
                              placeholder="Describe specific control measures for this hazard"></textarea>
                </div>
            </div>
        `;
    }

    // Add new risk assessment
    addButton.addEventListener('click', function() {
        riskAssessmentIndex++;
        const newItem = createRiskAssessmentItem(riskAssessmentIndex);
        container.insertAdjacentHTML('beforeend', newItem);
        updateRiskAssessmentNumbers();
        
        // Scroll to the new item
        const newItemElement = container.lastElementChild;
        newItemElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Focus on the first input
        const firstInput = newItemElement.querySelector('textarea');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 300);
        }
    });

    // Remove risk assessment
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-risk-assessment')) {
            e.preventDefault();
            const item = e.target.closest('.risk-assessment-item');
            const itemCount = container.querySelectorAll('.risk-assessment-item').length;
            
            if (itemCount > 1) {
                // Add fade out animation
                item.style.opacity = '0.5';
                item.style.transform = 'scale(0.95)';
                
                setTimeout(() => {
                    item.remove();
                    updateRiskAssessmentNumbers();
                }, 200);
            }
        }
    });

    // Initialize
    updateRiskAssessmentNumbers();
});
</script>
@endpush
@endsection
