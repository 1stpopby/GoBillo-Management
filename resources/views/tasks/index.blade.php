@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
<div class="tasks-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="page-title">Tasks</h1>
                <p class="page-subtitle">Manage and track all tasks across your projects</p>
            </div>
            <div class="col-lg-4 text-end">
                @if(auth()->user()->canManageProjects())
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle me-2"></i>New Task
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('tasks.index') }}" class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label for="search" class="form-label">Search Tasks</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Task title or description...">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="review" {{ request('status') == 'review' ? 'selected' : '' }}>Review</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="priority" class="form-label">Priority</label>
                    <select class="form-select" id="priority" name="priority">
                        <option value="">All Priorities</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="project_id" class="form-label">Project</label>
                    <select class="form-select" id="project_id" name="project_id">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>Filter
                        </button>
                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="card">
        <div class="card-body">
            @if($tasks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Category</th>
                                <th>Project</th>
                                <th>Site</th>
                                <th>Assigned To</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Due Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                                <tr class="
                                    @if($task->status == 'completed')
                                        table-success task-completed
                                    @elseif($task->is_delayed_or_on_hold)
                                        task-delayed-or-on-hold
                                    @elseif($task->due_date && $task->due_date->isPast() && $task->status !== 'completed')
                                        table-danger
                                    @elseif($task->due_date && $task->due_date->diffInDays(now()) <= 3 && $task->status !== 'completed')
                                        table-warning
                                    @endif
                                ">
                                    <td>
                                        <div class="task-info">
                                            <h6 class="mb-1">
                                                <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none">
                                                    {{ $task->title }}
                                                    @if($task->is_currently_on_hold)
                                                        <span class="badge bg-danger ms-2">On Hold</span>
                                                    @elseif($task->is_currently_delayed)
                                                        <span class="badge bg-warning ms-2">Delayed</span>
                                                    @elseif($task->due_date && $task->due_date->isPast() && $task->status !== 'completed')
                                                        <span class="badge bg-danger ms-2">Overdue</span>
                                                    @endif
                                                </a>
                                            </h6>
                                            @if($task->description)
                                                <small class="text-muted">{{ Str::limit($task->description, 60) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($task->taskCategory)
                                            <span class="badge" style="background-color: {{ $task->taskCategory->color }}; color: white;">
                                                @if($task->taskCategory->icon)
                                                    <i class="{{ $task->taskCategory->icon }} me-1"></i>
                                                @endif
                                                {{ $task->taskCategory->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">No category</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('projects.show', $task->project) }}" class="text-decoration-none">
                                            {{ $task->project->name }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($task->project->site)
                                            <a href="{{ route('sites.show', $task->project->site) }}" class="text-decoration-none">
                                                {{ $task->project->site->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">No site</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($task->assignedUser)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    {{ substr($task->assignedUser->name, 0, 1) }}
                                                </div>
                                                {{ $task->assignedUser->name }}
                                            </div>
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(auth()->user()->canManageTasks())
                                            <select class="form-select form-select-sm task-status-dropdown" 
                                                    data-task-id="{{ $task->id }}" 
                                                    data-current-status="{{ $task->status }}"
                                                    style="width: auto; min-width: 110px;">
                                                <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="review" {{ $task->status == 'review' ? 'selected' : '' }}>Review</option>
                                                <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="cancelled" {{ $task->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        @else
                                            <span class="badge bg-{{ $task->status_color }}">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $task->priority_color }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($task->due_date)
                                            <span class="{{ $task->is_overdue ? 'text-danger fw-bold' : 'text-muted' }}">
                                                {{ $task->due_date->format('M j, Y') }}
                                                @if($task->is_overdue)
                                                    <i class="bi bi-exclamation-triangle ms-1"></i>
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-muted">No due date</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="javascript:void(0)" onclick="openTaskModal({{ $task->id }})">
                                                        <i class="bi bi-eye me-2"></i>View Details
                                                    </a>
                                                </li>
                                                @if(auth()->user()->canManageProjects())
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('tasks.edit', $task) }}">
                                                            <i class="bi bi-pencil me-2"></i>Edit
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('tasks.update-status', $task) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="{{ $task->status == 'completed' ? 'pending' : 'completed' }}">
                                                            <button type="submit" class="dropdown-item">
                                                                @if($task->status == 'completed')
                                                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Mark as Pending
                                                                @else
                                                                    <i class="bi bi-check-circle me-2"></i>Mark as Complete
                                                                @endif
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button type="button" class="dropdown-item text-danger" onclick="confirmDeleteTask({{ $task->id }}, '{{ addslashes($task->title) }}')">
                                                            <i class="bi bi-trash me-2"></i>Delete Task
                                                        </button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $tasks->links() }}
                </div>
            @else
                <div class="empty-state text-center py-5">
                    <i class="bi bi-list-task display-1 text-muted"></i>
                    <h4 class="mt-3">No tasks found</h4>
                    <p class="text-muted">{{ request()->hasAny(['search', 'status', 'priority', 'project_id']) ? 'Try adjusting your filters to find what you\'re looking for.' : 'Get started by creating your first task.' }}</p>
                    @if(auth()->user()->canManageProjects() && !request()->hasAny(['search', 'status', 'priority', 'project_id']))
                        <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-lg mt-3">
                            <i class="bi bi-plus-circle me-2"></i>Create Your First Task
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<script>
// Function to confirm and delete a task
function confirmDeleteTask(taskId, taskTitle) {
    if (confirm(`Are you sure you want to delete the task "${taskTitle}"?\n\nThis action cannot be undone and will permanently remove the task and all its data.`)) {
        // Create a form to submit DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/tasks/${taskId}`;
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Add method spoofing for DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Handle task status changes with simple GET requests
    document.querySelectorAll('.task-status-dropdown').forEach(function(dropdown) {
        dropdown.addEventListener('change', function() {
            const taskId = this.dataset.taskId;
            const newStatus = this.value;
            const currentStatus = this.dataset.currentStatus;
            
            if (newStatus !== currentStatus) {
                // Show loading state
                this.disabled = true;
                
                // Use simple GET request to update status
                window.location.href = `/tasks/${taskId}/status/${newStatus}`;
            }
        });
    });
});
</script>

<!-- Task Modal -->
<div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title fw-bold" id="taskModalLabel">Task Details</h6>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="taskModalContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading task details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Task Modal Functions
let currentTaskId = null;

function openTaskModal(taskId) {
    // Store current task ID for floating action button
    currentTaskId = taskId;
    
    const modal = document.getElementById('taskModal');
    const title = document.getElementById('taskModalLabel');
    const content = document.getElementById('taskModalContent');
    
    // Show modal with loading state
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    title.textContent = 'Loading Task...';
    content.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Loading task details...</p>
        </div>
    `;
    
    // Load task content via AJAX
    fetch(`/tasks/${taskId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html',
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load task');
            }
            return response.text();
        })
        .then(html => {
            // Since we're now returning modal content directly, just set it
            content.innerHTML = html;
            
            // Update the modal title from the content
            const modalTitle = content.querySelector('.task-modal-title');
            if (modalTitle) {
                title.textContent = modalTitle.textContent;
            } else {
                title.textContent = 'Task Details';
            }
            
            // Re-initialize any Bootstrap components in the loaded content
            if (typeof bootstrap !== 'undefined') {
                // Initialize tooltips
                const tooltipTriggerList = content.querySelectorAll('[data-bs-toggle="tooltip"]');
                tooltipTriggerList.forEach(tooltipTriggerEl => {
                    new bootstrap.Tooltip(tooltipTriggerEl);
                });
                
                // Initialize dropdowns
                const dropdownElementList = content.querySelectorAll('.dropdown-toggle');
                dropdownElementList.forEach(dropdownToggleEl => {
                    new bootstrap.Dropdown(dropdownToggleEl);
                });
                
                // Initialize progress bars
                const progressBars = content.querySelectorAll('.progress-bar');
                progressBars.forEach(bar => {
                    const width = bar.style.width;
                    bar.style.width = '0%';
                    setTimeout(() => {
                        bar.style.width = width;
                    }, 100);
                });
            }
            
            // Handle status update buttons within the modal
            const statusButtons = content.querySelectorAll('button[onclick*="updateStatus"]');
            statusButtons.forEach(button => {
                const onclick = button.getAttribute('onclick');
                if (onclick) {
                    const status = onclick.match(/'([^']+)'/)?.[1];
                    if (status) {
                        button.setAttribute('onclick', `updateTaskStatusInModal(${taskId}, '${status}')`);
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error loading task:', error);
            title.textContent = 'Error Loading Task';
            content.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-exclamation-triangle display-4 text-danger"></i>
                    <h6 class="mt-3">Failed to Load Task</h6>
                    <p class="text-muted">There was an error loading the task details.</p>
                    <button class="btn btn-outline-primary" onclick="openTaskModal(${taskId})">
                        <i class="bi bi-arrow-clockwise me-2"></i>Try Again
                    </button>
                </div>
            `;
        });
}

function closeTaskModal() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('taskModal'));
    if (modal) {
        modal.hide();
    }
}

function updateTaskStatus(taskId, status) {
    fetch(`/tasks/${taskId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the modal content to show updated status
            openTaskModal(taskId);
            
            // Show success message
            showSuccessMessage(data.message || 'Task status updated successfully');
            
            // Refresh the page to show updated task list
            setTimeout(() => location.reload(), 1500);
        } else {
            alert('Error updating task status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating task status. Please try again.');
    });
}

function updateTaskStatusInModal(taskId, status) {
    fetch(`/tasks/${taskId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the modal content to show updated status
            openTaskModal(taskId);
            
            // Show success message
            showSuccessMessage(data.message || 'Task status updated successfully');
            
            // Refresh the page to show updated task list
            setTimeout(() => location.reload(), 1500);
        } else {
            alert('Error updating task status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating task status. Please try again.');
    });
}

// Delay and Hold Functions
function showDelayModal(taskId) {
    const html = `
        <div class="modal fade" id="delayModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Apply Delay to Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="delayForm">
                            <div class="mb-3">
                                <label class="form-label">Delay Days <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="delayDays" min="1" max="365" required>
                                <small class="text-muted">Number of days to delay the task</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Delay Reason <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="delayReason" rows="3" required placeholder="Explain why this task needs to be delayed..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-warning" onclick="applyDelay(${taskId})">Apply Delay</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', html);
    const modal = new bootstrap.Modal(document.getElementById('delayModal'));
    modal.show();
    
    document.getElementById('delayModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

function showOnHoldModal(taskId) {
    const html = `
        <div class="modal fade" id="onHoldModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Put Task On Hold</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="onHoldForm">
                            <div class="mb-3">
                                <label class="form-label">Hold Reason <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="onHoldReason" rows="3" required placeholder="Explain why this task needs to be put on hold..."></textarea>
                            </div>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Note:</strong> Putting a task on hold will pause all work until the hold is removed.
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" onclick="applyOnHold(${taskId})">Put On Hold</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', html);
    const modal = new bootstrap.Modal(document.getElementById('onHoldModal'));
    modal.show();
    
    document.getElementById('onHoldModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

function applyDelay(taskId) {
    const delayDays = document.getElementById('delayDays').value;
    const delayReason = document.getElementById('delayReason').value;
    
    if (!delayDays || !delayReason) {
        alert('Please fill in all required fields.');
        return;
    }
    
    fetch(`/tasks/${taskId}/apply-delay`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            delay_days: parseInt(delayDays),
            delay_reason: delayReason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('delayModal')).hide();
            showSuccessMessage(data.message);
            // Refresh the modal content
            openTaskModal(taskId);
            // Refresh the page to show updated task list
            setTimeout(() => location.reload(), 1500);
        } else {
            alert('Error applying delay: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error applying delay. Please try again.');
    });
}

function applyOnHold(taskId) {
    const onHoldReason = document.getElementById('onHoldReason').value;
    
    if (!onHoldReason) {
        alert('Please provide a reason for putting the task on hold.');
        return;
    }
    
    fetch(`/tasks/${taskId}/apply-on-hold`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            on_hold_reason: onHoldReason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('onHoldModal')).hide();
            showSuccessMessage(data.message);
            // Refresh the modal content
            openTaskModal(taskId);
            // Refresh the page to show updated task list
            setTimeout(() => location.reload(), 1500);
        } else {
            alert('Error putting task on hold: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error putting task on hold. Please try again.');
    });
}

function removeDelay(taskId) {
    if (!confirm('Are you sure you want to remove the delay from this task?')) {
        return;
    }
    
    fetch(`/tasks/${taskId}/remove-delay`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessMessage(data.message);
            // Refresh the modal content
            openTaskModal(taskId);
            // Refresh the page to show updated task list
            setTimeout(() => location.reload(), 1500);
        } else {
            alert('Error removing delay: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error removing delay. Please try again.');
    });
}

function removeOnHold(taskId) {
    if (!confirm('Are you sure you want to remove the hold from this task?')) {
        return;
    }
    
    fetch(`/tasks/${taskId}/remove-on-hold`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessMessage(data.message);
            // Refresh the modal content
            openTaskModal(taskId);
            // Refresh the page to show updated task list
            setTimeout(() => location.reload(), 1500);
        } else {
            alert('Error removing hold: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error removing hold. Please try again.');
    });
}

function showSuccessMessage(message) {
    const alertHtml = `
        <div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="bi bi-check-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert-success');
        if (alert) {
            bootstrap.Alert.getOrCreateInstance(alert).close();
        }
    }, 5000);
}
</script>

<style>
/* Task styling for delayed/on hold tasks */
.task-delayed-or-on-hold {
    background-color: rgba(220, 53, 69, 0.05) !important;
    border-left: 4px solid #dc3545 !important;
}

.task-delayed-or-on-hold:hover {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.task-completed {
    opacity: 0.7;
    background-color: rgba(25, 135, 84, 0.05);
}

.task-completed .task-title {
    text-decoration: line-through !important;
}

/* Enhanced badge styling */
.badge {
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.5px;
}

/* Modal enhancements */
.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

.modal-header {
    border-bottom: 1px solid #e9ecef;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px 12px 0 0;
}

.modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

.form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Compact modal specific styles */
.modal-lg {
    max-width: 900px;
}

@media (max-width: 768px) {
    .modal-lg {
        max-width: 95%;
        margin: 1rem auto;
    }
}
</style>

@endsection