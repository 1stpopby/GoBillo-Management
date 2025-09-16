@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">Tasks</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tasks.show', $task) }}">{{ $task->title }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">Edit Task</h1>
    </div>
    <div class="btn-group">
        <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-info">
            <i class="bi bi-eye"></i> View Task
        </a>
        @if($task->project)
            <a href="{{ route('projects.show', $task->project) }}#tasks" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Project Tasks
            </a>
        @else
            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Tasks
            </a>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Task Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('tasks.update', $task) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="title" class="form-label">Task Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $task->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                            @if($fromProject && $selectedProject)
                                <!-- Read-only project field when editing from project -->
                                <div class="form-control bg-light d-flex align-items-center">
                                    <i class="bi bi-folder me-2 text-primary"></i>
                                    <span class="fw-medium">{{ $selectedProject->name }}</span>
                                    @if($selectedProject->site)
                                        <small class="text-muted ms-2">({{ $selectedProject->site->name }})</small>
                                    @endif
                                </div>
                                <input type="hidden" name="project_id" value="{{ $selectedProject->id }}">
                                <small class="form-text text-muted">Project cannot be changed as task belongs to this project</small>
                            @else
                                <!-- Normal project dropdown -->
                                <select class="form-select @error('project_id') is-invalid @enderror" 
                                        id="project_id" name="project_id" required>
                                    <option value="">Select Project</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" 
                                                {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                                            {{ $project->site ? $project->site->name . ' - ' : '' }}{{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="task_category_id" class="form-label">Task Category</label>
                            <select class="form-select @error('task_category_id') is-invalid @enderror" 
                                    id="task_category_id" name="task_category_id">
                                <option value="">Select Category (Optional)</option>
                                @foreach(\App\Models\TaskCategory::forCompany()->orderBy('name')->get() as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('task_category_id', $task->task_category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('task_category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                   id="due_date" name="due_date" 
                                   value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}">
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4">{{ old('description', $task->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status and Priority -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="review" {{ old('status', $task->status) == 'review' ? 'selected' : '' }}>Review</option>
                                <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $task->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority', $task->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="assigned_to" class="form-label">Assign To</label>
                            <select class="form-select @error('assigned_to') is-invalid @enderror" 
                                    id="assigned_to" name="assigned_to">
                                <option value="">Select User</option>
                                @foreach(\App\Models\User::where('is_active', true)->get() as $user)
                                    <option value="{{ $user->id }}" 
                                            {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ ucfirst(str_replace('_', ' ', $user->role)) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                   id="due_date" name="due_date" 
                                   value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}">
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="estimated_time" class="form-label">Estimated Time</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('estimated_time') is-invalid @enderror" 
                                       id="estimated_time" name="estimated_time" 
                                       value="{{ old('estimated_time', $task->estimated_time) }}" 
                                       min="0" step="0.5" placeholder="Enter time">
                                <select class="form-select @error('estimated_time_unit') is-invalid @enderror" 
                                        name="estimated_time_unit" style="max-width: 100px;">
                                    <option value="hours" {{ old('estimated_time_unit', $task->estimated_time_unit ?? 'hours') === 'hours' ? 'selected' : '' }}>Hours</option>
                                    <option value="days" {{ old('estimated_time_unit', $task->estimated_time_unit) === 'days' ? 'selected' : '' }}>Days</option>
                                </select>
                            </div>
                            @error('estimated_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('estimated_time_unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="actual_time" class="form-label">Actual Time</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('actual_time') is-invalid @enderror" 
                                       id="actual_time" name="actual_time" 
                                       value="{{ old('actual_time', $task->actual_time) }}" 
                                       min="0" step="0.5" placeholder="Enter time">
                                <select class="form-select @error('actual_time_unit') is-invalid @enderror" 
                                        name="actual_time_unit" style="max-width: 100px;">
                                    <option value="hours" {{ old('actual_time_unit', $task->actual_time_unit ?? 'hours') === 'hours' ? 'selected' : '' }}>Hours</option>
                                    <option value="days" {{ old('actual_time_unit', $task->actual_time_unit) === 'days' ? 'selected' : '' }}>Days</option>
                                </select>
                            </div>
                            @error('actual_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('actual_time_unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="progress" class="form-label">Progress (%)</label>
                            <input type="range" class="form-range" id="progress" name="progress" 
                                   min="0" max="100" value="{{ old('progress', $task->progress) }}" 
                                   oninput="updateProgressValue(this.value)">
                            <div class="d-flex justify-content-between">
                                <span>0%</span>
                                <span id="progress-value">{{ old('progress', $task->progress) }}%</span>
                                <span>100%</span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="estimated_cost" class="form-label">Estimated Cost (£)</label>
                            <div class="input-group">
                                <span class="input-group-text">£</span>
                                <input type="number" class="form-control @error('estimated_cost') is-invalid @enderror" 
                                       id="estimated_cost" name="estimated_cost" 
                                       value="{{ old('estimated_cost', $task->estimated_cost) }}" 
                                       min="0" step="0.01" placeholder="0.00">
                            </div>
                            @error('estimated_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">What is the estimated cost for this task?</small>
                        </div>
                        <div class="col-md-6">
                            <label for="actual_cost" class="form-label">Actual Cost (£)</label>
                            <div class="input-group">
                                <span class="input-group-text">£</span>
                                <input type="number" class="form-control @error('actual_cost') is-invalid @enderror" 
                                       id="actual_cost" name="actual_cost" 
                                       value="{{ old('actual_cost', $task->actual_cost) }}" 
                                       min="0" step="0.01" placeholder="0.00">
                            </div>
                            @error('actual_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">What was the actual cost incurred? (optional)</small>
                        </div>
                    </div>

                    <!-- Delay Information -->
                    <div class="row g-3 mt-4">
                        <div class="col-12">
                            <hr>
                            <h6 class="text-muted mb-3">
                                <i class="bi bi-clock-history me-2"></i>Delay Information
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <label for="delay_days" class="form-label">Delay Days</label>
                            <input type="number" class="form-control @error('delay_days') is-invalid @enderror" 
                                   id="delay_days" name="delay_days" 
                                   value="{{ old('delay_days', $task->delay_days) }}" 
                                   min="0" placeholder="0" onchange="updateDelayStatus()">
                            @error('delay_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Number of days this task is delayed</small>
                            <input type="hidden" id="is_delayed" name="is_delayed" value="{{ old('is_delayed', $task->is_delayed ? '1' : '0') }}">
                        </div>

                        <div class="col-md-6">
                            <label for="original_due_date" class="form-label">Original Due Date</label>
                            <input type="date" class="form-control @error('original_due_date') is-invalid @enderror" 
                                   id="original_due_date" name="original_due_date" 
                                   value="{{ old('original_due_date', $task->original_due_date?->format('Y-m-d')) }}">
                            @error('original_due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Original planned due date</small>
                        </div>

                        <div class="col-12">
                            <label for="delay_reason" class="form-label">Delay Reason</label>
                            <textarea class="form-control @error('delay_reason') is-invalid @enderror" 
                                      id="delay_reason" name="delay_reason" rows="2" 
                                      placeholder="Explain the reason for the delay...">{{ old('delay_reason', $task->delay_reason) }}</textarea>
                            @error('delay_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Provide details about why the task is delayed</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <div>
                            @if(auth()->user()->isSuperAdmin() || auth()->user()->isCompanyAdmin())
                                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                    <i class="bi bi-trash"></i> Delete Task
                                </button>
                            @endif
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Task
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Task History -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Task History</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Task Created</h6>
                            <p class="timeline-text">
                                Created by {{ $task->createdBy->name }}<br>
                                <small class="text-muted">{{ $task->created_at->format('M j, Y g:i A') }}</small>
                            </p>
                        </div>
                    </div>
                    
                    @if($task->assignedUser)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Task Assigned</h6>
                                <p class="timeline-text">
                                    Assigned to {{ $task->assignedUser->name }}<br>
                                    <small class="text-muted">{{ $task->updated_at->format('M j, Y g:i A') }}</small>
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($task->status === 'completed')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Task Completed</h6>
                                <p class="timeline-text">
                                    <small class="text-muted">{{ $task->updated_at->format('M j, Y g:i A') }}</small>
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tips -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Tips</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled small text-muted">
                    <li class="mb-2">
                        <i class="bi bi-info-circle text-info"></i>
                        <strong>Status Changes:</strong> Progress will automatically update based on status changes.
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-clock text-warning"></i>
                        <strong>Time Tracking:</strong> Update actual hours to track time spent on tasks.
                    </li>
                    <li>
                        <i class="bi bi-graph-up text-success"></i>
                        <strong>Progress:</strong> Use the progress slider to show completion percentage.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if(auth()->user()->isSuperAdmin() || auth()->user()->isCompanyAdmin())
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this task? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <strong>Warning:</strong> This will permanently remove the task and all associated data.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Task</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
}

.timeline-content {
    padding-left: 15px;
}

.timeline-title {
    font-size: 0.9rem;
    margin-bottom: 5px;
}

.timeline-text {
    font-size: 0.85rem;
    margin-bottom: 0;
}
</style>

<script>
function updateProgressValue(value) {
    document.getElementById('progress-value').textContent = value + '%';
}

function updateProgressBasedOnStatus() {
    const status = document.getElementById('status').value;
    const progressSlider = document.getElementById('progress');
    
    switch(status) {
        case 'pending':
            progressSlider.value = 0;
            break;
        case 'in_progress':
            if (progressSlider.value == 0) {
                progressSlider.value = 25;
            }
            break;
        case 'review':
            if (progressSlider.value < 75) {
                progressSlider.value = 75;
            }
            break;
        case 'completed':
            progressSlider.value = 100;
            break;
        case 'cancelled':
            // Keep current progress
            break;
    }
    
    updateProgressValue(progressSlider.value);
}

function confirmDelete() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function updateDelayStatus() {
    const delayDays = document.getElementById('delay_days').value;
    const isDelayedInput = document.getElementById('is_delayed');
    
    // Set is_delayed to true if delay_days > 0, false otherwise
    isDelayedInput.value = (delayDays && parseInt(delayDays) > 0) ? '1' : '0';
}
</script>