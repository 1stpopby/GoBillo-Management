@extends('layouts.app')

@section('title', 'Create Task')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        @if($fromProject && $selectedProject)
                            <li class="breadcrumb-item"><a href="{{ route('projects.show', $selectedProject->id) }}">{{ $selectedProject->name }}</a></li>
                            <li class="breadcrumb-item active">Create Task</li>
                        @else
                            <li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">Tasks</a></li>
                            <li class="breadcrumb-item active">Create Task</li>
                        @endif
                    </ol>
                </nav>
                <h1 class="page-title">Create New Task</h1>
                <p class="page-subtitle">Add a new task to a project</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Task Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tasks.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="title" class="form-label">Task Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                                @if($fromProject && $selectedProject)
                                    <!-- Read-only project field when creating from project -->
                                    <div class="form-control bg-light d-flex align-items-center">
                                        <i class="bi bi-folder me-2 text-primary"></i>
                                        <span class="fw-medium">{{ $selectedProject->name }}</span>
                                        @if($selectedProject->site)
                                            <small class="text-muted ms-2">({{ $selectedProject->site->name }})</small>
                                        @endif
                                    </div>
                                    <input type="hidden" name="project_id" value="{{ $selectedProject->id }}">
                                    <small class="form-text text-muted">Project is auto-selected</small>
                                @else
                                    <!-- Normal project dropdown -->
                                    <select class="form-select @error('project_id') is-invalid @enderror" 
                                            id="project_id" name="project_id" required>
                                        <option value="">Select a project...</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                                {{ $project->site ? $project->site->name . ' - ' : '' }}{{ $project->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label for="task_category_id" class="form-label">Category</label>
                                <select class="form-select @error('task_category_id') is-invalid @enderror" 
                                        id="task_category_id" name="task_category_id">
                                    <option value="">Select a category...</option>
                                    @foreach($taskCategories as $category)
                                        <option value="{{ $category->id }}" {{ old('task_category_id') == $category->id ? 'selected' : '' }}>
                                            @if($category->icon)
                                                {{ $category->name }}
                                            @else
                                                {{ $category->name }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('task_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="assigned_to" class="form-label">Assigned To</label>
                                <select class="form-select @error('assigned_to') is-invalid @enderror" 
                                        id="assigned_to" name="assigned_to">
                                    <option value="">Unassigned</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="review" {{ old('status') == 'review' ? 'selected' : '' }}>Review</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                        id="priority" name="priority" required>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="progress" class="form-label">Progress (%)</label>
                                <input type="number" class="form-control @error('progress') is-invalid @enderror" 
                                       id="progress" name="progress" value="{{ old('progress', 0) }}" 
                                       min="0" max="100">
                                @error('progress')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                       id="due_date" name="due_date" value="{{ old('due_date') }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date') }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="estimated_time" class="form-label">Estimated Time</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('estimated_time') is-invalid @enderror" 
                                           id="estimated_time" name="estimated_time" value="{{ old('estimated_time') }}" 
                                           min="0" step="0.5" placeholder="Enter time">
                                    <select class="form-select @error('estimated_time_unit') is-invalid @enderror" 
                                            name="estimated_time_unit" style="max-width: 100px;">
                                        <option value="hours" {{ old('estimated_time_unit', 'hours') === 'hours' ? 'selected' : '' }}>Hours</option>
                                        <option value="days" {{ old('estimated_time_unit') === 'days' ? 'selected' : '' }}>Days</option>
                                    </select>
                                </div>
                                @error('estimated_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('estimated_time_unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">How long do you estimate this task will take?</small>
                            </div>

                            <div class="col-md-6">
                                <label for="actual_time" class="form-label">Actual Time</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('actual_time') is-invalid @enderror" 
                                           id="actual_time" name="actual_time" value="{{ old('actual_time') }}" 
                                           min="0" step="0.5" placeholder="Enter time">
                                    <select class="form-select @error('actual_time_unit') is-invalid @enderror" 
                                            name="actual_time_unit" style="max-width: 100px;">
                                        <option value="hours" {{ old('actual_time_unit', 'hours') === 'hours' ? 'selected' : '' }}>Hours</option>
                                        <option value="days" {{ old('actual_time_unit') === 'days' ? 'selected' : '' }}>Days</option>
                                    </select>
                                </div>
                                @error('actual_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @error('actual_time_unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">How much time was actually spent? (optional)</small>
                            </div>

                            <div class="col-md-6">
                                <label for="estimated_cost" class="form-label">Estimated Cost (£)</label>
                                <div class="input-group">
                                    <span class="input-group-text">£</span>
                                    <input type="number" class="form-control @error('estimated_cost') is-invalid @enderror" 
                                           id="estimated_cost" name="estimated_cost" value="{{ old('estimated_cost') }}" 
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
                                           id="actual_cost" name="actual_cost" value="{{ old('actual_cost') }}" 
                                           min="0" step="0.01" placeholder="0.00">
                                </div>
                                @error('actual_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">What was the actual cost incurred? (optional)</small>
                            </div>

                            <!-- Delay Information -->
                            <div class="col-12">
                                <hr class="my-4">
                                <h6 class="text-muted mb-3">
                                    <i class="bi bi-clock-history me-2"></i>Delay Information (Optional)
                                </h6>
                            </div>

                            <div class="col-md-6">
                                <label for="delay_days" class="form-label">Delay Days</label>
                                <input type="number" class="form-control @error('delay_days') is-invalid @enderror" 
                                       id="delay_days" name="delay_days" value="{{ old('delay_days') }}" 
                                       min="0" placeholder="0" onchange="updateDelayStatus()">
                                @error('delay_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Number of days this task is delayed (if any)</small>
                                <input type="hidden" id="is_delayed" name="is_delayed" value="{{ old('is_delayed', '0') }}">
                            </div>

                            <div class="col-md-6">
                                <label for="original_due_date" class="form-label">Original Due Date</label>
                                <input type="date" class="form-control @error('original_due_date') is-invalid @enderror" 
                                       id="original_due_date" name="original_due_date" value="{{ old('original_due_date') }}">
                                @error('original_due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Original planned due date (before any delays)</small>
                            </div>

                            <div class="col-12">
                                <label for="delay_reason" class="form-label">Delay Reason</label>
                                <textarea class="form-control @error('delay_reason') is-invalid @enderror" 
                                          id="delay_reason" name="delay_reason" rows="2" 
                                          placeholder="Explain the reason for the delay...">{{ old('delay_reason') }}</textarea>
                                @error('delay_reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Provide details about why the task is delayed</small>
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    @if($fromProject && $selectedProject)
                                        <a href="{{ route('projects.show', $selectedProject->id) }}#tasks" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-left me-2"></i>Back to Project
                                        </a>
                                    @else
                                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-x-circle me-2"></i>Cancel
                                        </a>
                                    @endif
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Create Task
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Category Preview -->
            <div class="card mt-4" id="categoryPreview" style="display: none;">
                <div class="card-header">
                    <h6 class="card-title mb-0">Selected Category</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="category-icon me-3" id="previewIcon">
                            <i class="bi bi-tag"></i>
                        </div>
                        <div>
                            <h6 class="mb-1" id="previewName">Category Name</h6>
                            <p class="text-muted mb-0" id="previewDescription">Category description</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.category-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid;
}

.category-icon i {
    font-size: 1.25rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('task_category_id');
    const categoryPreview = document.getElementById('categoryPreview');
    const previewIcon = document.getElementById('previewIcon');
    const previewName = document.getElementById('previewName');
    const previewDescription = document.getElementById('previewDescription');
    
    const categories = {!! json_encode($taskCategories->map(function($cat) {
        return [
            'id' => $cat->id,
            'name' => $cat->name,
            'description' => $cat->description,
            'color' => $cat->color,
            'icon' => $cat->icon
        ];
    })) !!};
    
    categorySelect.addEventListener('change', function() {
        const selectedId = this.value;
        
        if (selectedId) {
            const category = categories.find(cat => cat.id == selectedId);
            if (category) {
                previewIcon.style.backgroundColor = category.color + '15';
                previewIcon.style.borderColor = category.color;
                previewIcon.querySelector('i').style.color = category.color;
                previewIcon.querySelector('i').className = category.icon || 'bi bi-tag';
                
                previewName.textContent = category.name;
                previewDescription.textContent = category.description || 'No description available';
                
                categoryPreview.style.display = 'block';
            }
        } else {
            categoryPreview.style.display = 'none';
        }
    });
});

function updateDelayStatus() {
    const delayDays = document.getElementById('delay_days').value;
    const isDelayedInput = document.getElementById('is_delayed');
    
    // Set is_delayed to true if delay_days > 0, false otherwise
    isDelayedInput.value = (delayDays && parseInt(delayDays) > 0) ? '1' : '0';
}
</script>