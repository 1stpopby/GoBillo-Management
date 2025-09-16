@extends('layouts.app')

@section('title', 'Create Tool Hire Request')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Create Tool Hire Request</h1>
            <p class="page-subtitle">Request tools and equipment for your project</p>
        </div>
        <div>
            <a href="{{ route('tool-hire.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Tool Hire List
            </a>
        </div>
    </div>

    <!-- Create Form -->
    <div class="row">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('tool-hire.store') }}">
                @csrf
                
                <!-- Tool Request Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-tools me-2"></i>Tool Request
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="title" class="form-label">What do you need? <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" 
                                       placeholder="e.g., Mini Excavator for Foundation Work">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="tool_category" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select @error('tool_category') is-invalid @enderror" 
                                        id="tool_category" name="tool_category">
                                    <option value="">Select Category</option>
                                    @foreach(\App\Models\ToolHireRequest::getCategoryOptions() as $key => $label)
                                        <option value="{{ $key }}" {{ old('tool_category') === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tool_category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                       id="quantity" name="quantity" value="{{ old('quantity', 1) }}" 
                                       min="1" max="100">
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hire Period Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar me-2"></i>Hire Period
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="hire_start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('hire_start_date') is-invalid @enderror" 
                                       id="hire_start_date" name="hire_start_date" 
                                       value="{{ old('hire_start_date') }}" 
                                       min="{{ date('Y-m-d') }}">
                                @error('hire_start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="hire_end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('hire_end_date') is-invalid @enderror" 
                                       id="hire_end_date" name="hire_end_date" 
                                       value="{{ old('hire_end_date') }}">
                                @error('hire_end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="urgency" class="form-label">Urgency <span class="text-danger">*</span></label>
                                <select class="form-select @error('urgency') is-invalid @enderror" 
                                        id="urgency" name="urgency">
                                    @foreach(\App\Models\ToolHireRequest::getUrgencyOptions() as $key => $label)
                                        <option value="{{ $key }}" {{ old('urgency', 'normal') === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('urgency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="delivery_method" class="form-label">Delivery Method <span class="text-danger">*</span></label>
                                <select class="form-select @error('delivery_method') is-invalid @enderror" 
                                        id="delivery_method" name="delivery_method">
                                    @foreach(\App\Models\ToolHireRequest::getDeliveryMethodOptions() as $key => $label)
                                        <option value="{{ $key }}" {{ old('delivery_method', 'site_delivery') === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('delivery_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Card -->
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
                                        <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>
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
                                        id="project_id" name="project_id" disabled>
                                    <option value="">Select Project (Optional)</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" 
                                                data-site-id="{{ $project->site_id }}"
                                                {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12" id="delivery_address_section" style="display: none;">
                                <label for="delivery_address" class="form-label">Delivery Address</label>
                                <textarea class="form-control @error('delivery_address') is-invalid @enderror" 
                                          id="delivery_address" name="delivery_address" rows="3" 
                                          placeholder="Full delivery address if different from site address...">{{ old('delivery_address') }}</textarea>
                                @error('delivery_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cost Estimates Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-currency-pound me-2"></i>Cost Estimates
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="estimated_daily_rate" class="form-label">Estimated Daily Rate (£)</label>
                                <input type="number" class="form-control @error('estimated_daily_rate') is-invalid @enderror" 
                                       id="estimated_daily_rate" name="estimated_daily_rate" 
                                       value="{{ old('estimated_daily_rate') }}" 
                                       step="0.01" min="0" placeholder="e.g., 120.00">
                                @error('estimated_daily_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="deposit_amount" class="form-label">Expected Deposit (£)</label>
                                <input type="number" class="form-control @error('deposit_amount') is-invalid @enderror" 
                                       id="deposit_amount" name="deposit_amount" 
                                       value="{{ old('deposit_amount') }}" 
                                       step="0.01" min="0" placeholder="e.g., 500.00">
                                @error('deposit_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           id="insurance_required" name="insurance_required" value="1"
                                           {{ old('insurance_required') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="insurance_required">
                                        Insurance Required
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6" id="insurance_cost_section" style="display: none;">
                                <label for="insurance_cost" class="form-label">Insurance Cost (£)</label>
                                <input type="number" class="form-control @error('insurance_cost') is-invalid @enderror" 
                                       id="insurance_cost" name="insurance_cost" 
                                       value="{{ old('insurance_cost') }}" 
                                       step="0.01" min="0" placeholder="e.g., 25.00">
                                @error('insurance_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-sticky me-2"></i>Additional Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="special_requirements" class="form-label">Special Requirements</label>
                                <textarea class="form-control @error('special_requirements') is-invalid @enderror" 
                                          id="special_requirements" name="special_requirements" rows="3" 
                                          placeholder="Any special requirements, attachments needed, operator requirements, etc...">{{ old('special_requirements') }}</textarea>
                                @error('special_requirements')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12">
                                <label for="preferred_supplier" class="form-label">Preferred Supplier</label>
                                <input type="text" class="form-control @error('preferred_supplier') is-invalid @enderror" 
                                       id="preferred_supplier" name="preferred_supplier" 
                                       value="{{ old('preferred_supplier') }}" 
                                       placeholder="e.g., HSS Hire, Speedy Services">
                                @error('preferred_supplier')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-12">
                                <label for="notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="4" 
                                          placeholder="Any additional information for management or suppliers...">{{ old('notes') }}</textarea>
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
                            <a href="{{ route('tool-hire.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Submit Tool Hire Request
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
                        <i class="bi bi-question-circle me-2"></i>Help & Tips
                    </h5>
                </div>
                <div class="card-body">
                    <div class="help-section mb-4">
                        <h6 class="text-primary">🔧 Tool Categories</h6>
                        <ul class="small">
                            <li><strong>Excavation:</strong> Diggers, dumpers, skid steers</li>
                            <li><strong>Power Tools:</strong> Drills, grinders, saws</li>
                            <li><strong>Lifting:</strong> Cranes, telehandlers, hoists</li>
                            <li><strong>Safety:</strong> Harnesses, barriers, helmets</li>
                            <li><strong>Access:</strong> Scissor lifts, scaffolding</li>
                        </ul>
                    </div>
                    
                    <div class="help-section mb-4">
                        <h6 class="text-primary">📋 Request Process</h6>
                        <ol class="small">
                            <li>Fill out all required fields marked with <span class="text-danger">*</span></li>
                            <li>Your request will be submitted for approval</li>
                            <li>Management will review and approve/reject</li>
                            <li>Once approved, procurement will get quotes</li>
                            <li>Tools will be ordered and delivered</li>
                        </ol>
                    </div>
                    
                    <div class="help-section mb-4">
                        <h6 class="text-primary">💡 Best Practices</h6>
                        <ul class="small">
                            <li><strong>Be Specific:</strong> Include model numbers if known</li>
                            <li><strong>Plan Ahead:</strong> Allow time for procurement</li>
                            <li><strong>Check Availability:</strong> Popular tools book up quickly</li>
                            <li><strong>Include Requirements:</strong> Attachments, operators needed</li>
                        </ul>
                    </div>
                    
                    <div class="help-section">
                        <h6 class="text-primary">⚡ Urgency Levels</h6>
                        <div class="small">
                            <div class="mb-2">
                                <span class="badge bg-success me-2">Low</span>
                                <span>2+ weeks lead time</span>
                            </div>
                            <div class="mb-2">
                                <span class="badge bg-info me-2">Normal</span>
                                <span>1-2 weeks lead time</span>
                            </div>
                            <div class="mb-2">
                                <span class="badge bg-warning me-2">High</span>
                                <span>Few days lead time</span>
                            </div>
                            <div>
                                <span class="badge bg-danger me-2">Urgent</span>
                                <span>ASAP - same/next day</span>
                            </div>
                        </div>
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
    
    siteSelect.addEventListener('change', function() {
        const siteId = this.value;
        const projectOptions = projectSelect.querySelectorAll('option');
        
        // Reset project selection
        projectSelect.value = '';
        
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
                }
            });
        } else {
            projectSelect.disabled = true;
            projectOptions.forEach(option => {
                option.style.display = 'block';
            });
        }
    });
    
    // Delivery method dependency
    const deliveryMethodSelect = document.getElementById('delivery_method');
    const deliveryAddressSection = document.getElementById('delivery_address_section');
    
    deliveryMethodSelect.addEventListener('change', function() {
        if (this.value === 'delivery' || this.value === 'site_delivery') {
            deliveryAddressSection.style.display = 'block';
        } else {
            deliveryAddressSection.style.display = 'none';
        }
    });
    
    // Insurance dependency
    const insuranceCheckbox = document.getElementById('insurance_required');
    const insuranceCostSection = document.getElementById('insurance_cost_section');
    
    insuranceCheckbox.addEventListener('change', function() {
        if (this.checked) {
            insuranceCostSection.style.display = 'block';
        } else {
            insuranceCostSection.style.display = 'none';
        }
    });
    
    // Date validation
    const startDate = document.getElementById('hire_start_date');
    const endDate = document.getElementById('hire_end_date');
    
    startDate.addEventListener('change', function() {
        if (this.value) {
            endDate.min = this.value;
        }
    });
    
    // Initialize on page load
    if (deliveryMethodSelect.value === 'delivery' || deliveryMethodSelect.value === 'site_delivery') {
        deliveryAddressSection.style.display = 'block';
    }
    
    if (insuranceCheckbox.checked) {
        insuranceCostSection.style.display = 'block';
    }
    
    // Trigger site change to initialize project dropdown
    siteSelect.dispatchEvent(new Event('change'));
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
    margin-bottom: 0;
}

.help-section h6 {
    font-weight: 600;
    margin-bottom: 0.75rem;
}

.help-section ul,
.help-section ol {
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
