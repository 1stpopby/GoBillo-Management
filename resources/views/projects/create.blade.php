@extends('layouts.app')

@section('title', 'Create Project')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
                <li class="breadcrumb-item active">Create New Project</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">Create New Project</h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-plus-circle"></i> Project Information
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('projects.store') }}">
                    @csrf

                    <!-- Basic Information -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label">Project Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                <option value="">Select Priority</option>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Status and Progress -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="planning" {{ old('status', 'planning') == 'planning' ? 'selected' : '' }}>Planning</option>
                                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="progress" class="form-label">Progress (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('progress') is-invalid @enderror" 
                                   id="progress" name="progress" value="{{ old('progress', '0') }}" 
                                   min="0" max="100" required>
                            @error('progress')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" 
                                  placeholder="Describe the project scope, objectives, and key details...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Client and Site -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                            @if($fromSite && $selectedClient)
                                <!-- Read-only client field when creating from site -->
                                <div class="form-control bg-light d-flex align-items-center">
                                    <i class="bi bi-building me-2 text-primary"></i>
                                    <span class="fw-medium">{{ $selectedClient->display_name }}</span>
                                    <small class="text-muted ms-2">(from {{ $selectedSite->name }})</small>
                                </div>
                                <input type="hidden" name="client_id" value="{{ $selectedClient->id }}">
                                <small class="form-text text-muted">Client is auto-selected from the site</small>
                            @else
                                <!-- Normal client dropdown -->
                                <select class="form-select @error('client_id') is-invalid @enderror" id="client_id" name="client_id" required>
                                    <option value="">Select Client</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label for="site_id" class="form-label">Site <span class="text-danger">*</span></label>
                            <select class="form-select @error('site_id') is-invalid @enderror" id="site_id" name="site_id" required>
                                <option value="">Select Site</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}" 
                                        {{ (old('site_id') == $site->id) || ($fromSite && $selectedSite && $selectedSite->id == $site->id) ? 'selected' : '' }}>
                                        {{ $site->name }}
                                        @if($site->client)
                                            <small class="text-muted"> - {{ $site->client->display_name }}</small>
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('site_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Project Managers -->
                    <div class="mb-3">
                        <label class="form-label">Project Managers</label>
                        <div class="card">
                            <div class="card-body">
                                <div id="managers-container">
                                    <div class="alert alert-info" id="site-selection-notice">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Select a site above to auto-populate managers from that site. You can then modify the selection as needed.
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="add-manager" style="display: none;">
                                    <i class="bi bi-plus-circle me-2"></i>Add Additional Manager
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline and Budget -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" value="{{ old('start_date') }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" name="end_date" value="{{ old('end_date') }}">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="budget" class="form-label">Budget ($)</label>
                            <input type="number" class="form-control @error('budget') is-invalid @enderror" 
                                   id="budget" name="budget" value="{{ old('budget') }}" 
                                   step="0.01" min="0" placeholder="0.00">
                            @error('budget')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Location -->
                    <h6 class="border-bottom pb-2 mb-3">Project Location</h6>
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                   id="address" name="address" value="{{ old('address') }}" 
                                   placeholder="Street address">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="postcode" class="form-label">Postcode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('postcode') is-invalid @enderror" 
                                   id="postcode" name="postcode" value="{{ old('postcode') }}" 
                                   placeholder="SW1A 1AA" required>
                            @error('postcode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Required for location-based clock in/out</small>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-5">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                   id="city" name="city" value="{{ old('city') }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="state" class="form-label">State</label>
                            <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                   id="state" name="state" value="{{ old('state') }}" 
                                   placeholder="e.g., CA">
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="zip_code" class="form-label">ZIP Code</label>
                            <input type="text" class="form-control @error('zip_code') is-invalid @enderror" 
                                   id="zip_code" name="zip_code" value="{{ old('zip_code') }}">
                            @error('zip_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Projects
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Create Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Help Card -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle"></i> Project Creation Tips
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Project Name:</strong> Use a clear, descriptive name
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Description:</strong> Include scope, objectives, and key requirements
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Timeline:</strong> Set realistic start and end dates
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Budget:</strong> Include all estimated costs
                    </li>
                    <li class="mb-0">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Location:</strong> Provide complete address for site visits
                    </li>
                </ul>
            </div>
        </div>

        <!-- Next Steps Card -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-list-ol"></i> After Creating
                </h6>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-2">Once your project is created, you can:</p>
                <ul class="list-unstyled small">
                    <li class="mb-1">✓ Add team members</li>
                    <li class="mb-1">✓ Create tasks and milestones</li>
                    <li class="mb-1">✓ Upload project documents</li>
                    <li class="mb-1">✓ Track progress and updates</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-update status based on progress
    document.getElementById('progress').addEventListener('change', function() {
        const progress = parseInt(this.value);
        const statusSelect = document.getElementById('status');
        
        if (progress === 0 && statusSelect.value === 'in_progress') {
            statusSelect.value = 'planning';
        } else if (progress > 0 && progress < 100 && statusSelect.value === 'planning') {
            statusSelect.value = 'in_progress';
        } else if (progress === 100 && statusSelect.value !== 'completed') {
            if (confirm('Progress is 100%. Would you like to mark this project as completed?')) {
                statusSelect.value = 'completed';
            }
        }
    });

    // Auto-populate end date when start date is selected
    document.getElementById('start_date').addEventListener('change', function() {
    const startDate = new Date(this.value);
    const endDateInput = document.getElementById('end_date');
    
    if (!endDateInput.value && startDate) {
        // Set default end date to 3 months after start date
        const endDate = new Date(startDate);
        endDate.setMonth(endDate.getMonth() + 3);
        endDateInput.value = endDate.toISOString().split('T')[0];
    }
});

// Format budget input
document.getElementById('budget').addEventListener('input', function() {
    let value = this.value.replace(/[^\d.]/g, '');
    if (value) {
        this.value = parseFloat(value).toFixed(2);
    }
});

// Manager inheritance from site selection
const siteSelect = document.getElementById('site_id');
const managersContainer = document.getElementById('managers-container');
const addManagerBtn = document.getElementById('add-manager');

let allManagers = @json($managers); // All available managers

function createManagerRow(manager, isPrimary = false, isSelected = true) {
    const rowDiv = document.createElement('div');
    rowDiv.className = 'manager-row mb-3';
    rowDiv.innerHTML = `
        <div class="row align-items-center">
            <div class="col-md-1">
                <div class="form-check">
                    <input class="form-check-input manager-checkbox" type="checkbox" 
                           value="${manager.id}" name="managers[]" id="manager_${manager.id}"
                           ${isSelected ? 'checked' : ''}>
                </div>
            </div>
            <div class="col-md-7">
                <label for="manager_${manager.id}" class="form-check-label">
                    <strong>${manager.name}</strong><br>
                    <small class="text-muted">${manager.email}</small>
                </label>
            </div>
            <div class="col-md-2">
                <span class="badge ${isPrimary ? 'bg-primary' : 'bg-secondary'}">
                    ${manager.role || (isPrimary ? 'Primary' : 'Secondary')}
                </span>
            </div>
            <div class="col-md-2">
                ${!isPrimary ? '<button type="button" class="btn btn-outline-danger btn-sm remove-manager"><i class="bi bi-trash"></i></button>' : ''}
            </div>
        </div>
    `;

    // Add remove functionality for secondary managers
    if (!isPrimary) {
        const removeBtn = rowDiv.querySelector('.remove-manager');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                rowDiv.remove();
            });
        }
    }

    return rowDiv;
}

function loadSiteManagers(siteId) {
    if (!siteId) {
        managersContainer.innerHTML = '<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Select a site above to auto-populate managers from that site.</div>';
        addManagerBtn.style.display = 'none';
        return;
    }

    // Show loading state
    managersContainer.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Loading site managers...</div>';

    fetch(`{{ route('ajax.managers-for-site') }}?site_id=${siteId}`)
        .then(response => response.json())
        .then(data => {
            managersContainer.innerHTML = '';
            
            if (data.managers && data.managers.length > 0) {
                // Add inherited managers
                data.managers.forEach((manager, index) => {
                    const isPrimary = manager.role === 'primary' || index === 0;
                    const managerRow = createManagerRow(manager, isPrimary, true);
                    managersContainer.appendChild(managerRow);
                });

                addManagerBtn.style.display = 'inline-block';
            } else {
                managersContainer.innerHTML = '<div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>This site has no assigned managers. You can select managers manually below.</div>';
                
                // Show all available managers as options
                allManagers.forEach((manager, index) => {
                    const isPrimary = index === 0;
                    const managerRow = createManagerRow(manager, isPrimary, false);
                    managersContainer.appendChild(managerRow);
                });
                
                addManagerBtn.style.display = 'inline-block';
            }
        })
        .catch(error => {
            console.error('Error loading site managers:', error);
            managersContainer.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i>Error loading site managers.</div>';
        });
}

// Site selection change event
if (siteSelect) {
    siteSelect.addEventListener('change', function() {
        loadSiteManagers(this.value);
    });
}

// Add additional manager functionality
if (addManagerBtn) {
    addManagerBtn.addEventListener('click', function() {
        const selectedManagerIds = Array.from(document.querySelectorAll('.manager-checkbox:checked'))
            .map(cb => cb.value);
        
        const availableManagers = allManagers.filter(manager => 
            !selectedManagerIds.includes(manager.id.toString())
        );

        if (availableManagers.length > 0) {
            const manager = availableManagers[0];
            const managerRow = createManagerRow(manager, false, true);
            managersContainer.appendChild(managerRow);
        } else {
            alert('All available managers have been assigned to this project.');
        }
    });
}
</script>