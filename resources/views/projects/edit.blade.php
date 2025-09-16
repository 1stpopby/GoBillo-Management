@extends('layouts.app')

@section('title', 'Edit Project')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
                <li class="breadcrumb-item"><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">Edit Project</h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-pencil"></i> Update Project Information
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('projects.update', $project) }}">
                    @csrf
                    @method('PUT')

                    <!-- Basic Information -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label">Project Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $project->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                <option value="">Select Priority</option>
                                <option value="low" {{ old('priority', $project->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', $project->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority', $project->priority) == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority', $project->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" 
                                  placeholder="Describe the project scope, objectives, and key details...">{{ old('description', $project->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Client and Manager -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="client_id" class="form-label">Client <span class="text-danger">*</span></label>
                            @if($fromSite && $selectedClient)
                                <!-- Read-only client field when editing from site -->
                                <div class="form-control bg-light d-flex align-items-center">
                                    <i class="bi bi-building me-2 text-primary"></i>
                                    <span class="fw-medium">{{ $selectedClient->display_name }}</span>
                                    <small class="text-muted ms-2">(from {{ $project->site->name }})</small>
                                </div>
                                <input type="hidden" name="client_id" value="{{ $selectedClient->id }}">
                                <small class="form-text text-muted">Client cannot be changed as it's linked to the site</small>
                            @else
                                <!-- Normal client dropdown -->
                                <select class="form-select @error('client_id') is-invalid @enderror" id="client_id" name="client_id" required>
                                    <option value="">Select Client</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
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
                            <label for="manager_id" class="form-label">Project Manager <span class="text-danger">*</span></label>
                            <select class="form-select @error('manager_id') is-invalid @enderror" id="manager_id" name="manager_id" required>
                                <option value="">Select Manager</option>
                                @foreach($managers as $manager)
                                    <option value="{{ $manager->id }}" {{ old('manager_id', $project->manager_id) == $manager->id ? 'selected' : '' }}>
                                        {{ $manager->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('manager_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Status and Progress -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="planning" {{ old('status', $project->status) == 'planning' ? 'selected' : '' }}>Planning</option>
                                <option value="in_progress" {{ old('status', $project->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="on_hold" {{ old('status', $project->status) == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $project->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="progress" class="form-label">Progress (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('progress') is-invalid @enderror" 
                                   id="progress" name="progress" value="{{ old('progress', $project->progress) }}" 
                                   min="0" max="100" required>
                            @error('progress')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Timeline and Budget -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" 
                                   value="{{ old('start_date', $project->start_date ? $project->start_date->format('Y-m-d') : '') }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" name="end_date" 
                                   value="{{ old('end_date', $project->end_date ? $project->end_date->format('Y-m-d') : '') }}">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="budget" class="form-label">Budget ($)</label>
                            <input type="number" class="form-control @error('budget') is-invalid @enderror" 
                                   id="budget" name="budget" value="{{ old('budget', $project->budget) }}" 
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
                                   id="address" name="address" value="{{ old('address', $project->address) }}" 
                                   placeholder="Street address">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="postcode" class="form-label">Postcode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('postcode') is-invalid @enderror" 
                                   id="postcode" name="postcode" value="{{ old('postcode', $project->postcode) }}" 
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
                                   id="city" name="city" value="{{ old('city', $project->city) }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="state" class="form-label">State</label>
                            <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                   id="state" name="state" value="{{ old('state', $project->state) }}" 
                                   placeholder="e.g., CA">
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="zip_code" class="form-label">ZIP Code</label>
                            <input type="text" class="form-control @error('zip_code') is-invalid @enderror" 
                                   id="zip_code" name="zip_code" value="{{ old('zip_code', $project->zip_code) }}">
                            @error('zip_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Project
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Project
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Current Status Card -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle"></i> Current Status
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Status:</strong>
                    <span class="badge bg-{{ $project->status_color }} ms-2">
                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                    </span>
                </div>
                <div class="mb-3">
                    <strong>Progress:</strong>
                    <div class="progress mt-1" style="height: 8px;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ $project->progress }}%">
                        </div>
                    </div>
                    <small class="text-muted">{{ $project->progress }}% complete</small>
                </div>
                <div class="mb-3">
                    <strong>Tasks:</strong>
                    <ul class="list-unstyled mb-0 small">
                        <li>Total: {{ $project->tasks->count() }}</li>
                        <li>Completed: {{ $project->tasks->where('status', 'completed')->count() }}</li>
                        <li>In Progress: {{ $project->tasks->where('status', 'in_progress')->count() }}</li>
                    </ul>
                </div>
                <div>
                    <strong>Team Members:</strong> {{ $project->users->count() }}
                </div>
            </div>
        </div>

        <!-- Update Tips -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-lightbulb"></i> Update Tips
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled small">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Update progress regularly to keep stakeholders informed
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Change status to reflect current project phase
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Adjust timeline if needed based on progress
                    </li>
                    <li class="mb-0">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Keep budget updated with actual costs
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Update progress bar in real-time
document.getElementById('progress').addEventListener('input', function() {
    const value = this.value;
    const progressBar = document.querySelector('.progress-bar');
    const progressText = document.querySelector('.progress + small');
    
    if (progressBar && progressText) {
        progressBar.style.width = value + '%';
        progressText.textContent = value + '% complete';
    }
});

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
</script>
@endpush 