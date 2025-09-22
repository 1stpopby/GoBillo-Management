{{-- Task Modal Content - Traditional Project Page Layout --}}
<div class="task-modal-content">
    <!-- Traditional Page Header like project page -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="page-title">{{ $task->title }}</h1>
                <p class="page-subtitle">
                    @if($task->project)
                        <i class="bi bi-folder me-2"></i>{{ $task->project->name }}
                    @endif
                    @if($task->project && $task->project->site)
                        <span class="text-muted ms-2">• {{ $task->project->site->name }}</span>
                    @endif
                </p>
            </div>
            <div class="col-lg-4 text-end">
                <span class="badge bg-{{ $task->delay_hold_status_color }} me-1">
                    @if($task->is_currently_on_hold)
                        🚫 On Hold
                    @elseif($task->is_currently_delayed)
                        ⏰ Delayed
                    @else
                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                    @endif
                </span>
                <span class="badge bg-{{ $task->priority_color }}">{{ ucfirst($task->priority) }} Priority</span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Content Column (like project page) -->
        <div class="col-lg-9">
            <!-- Task Information Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light text-dark">
                    <h5 class="mb-0 text-dark"><i class="bi bi-info-circle me-2"></i>Task Information</h5>
                </div>
                <div class="card-body">
                    @if($task->description)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Description</h6>
                            <p class="mb-0">{{ $task->description }}</p>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Assigned To</h6>
                            @if($task->assignedUser)
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 14px; font-weight: 600;">
                                        {{ substr($task->assignedUser->name, 0, 1) }}
                                    </div>
                                    <span class="text-dark">{{ $task->assignedUser->name }}</span>
                                </div>
                            @else
                                <span class="text-muted">Unassigned</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Progress</h6>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-{{ $task->status_color }}" role="progressbar" 
                                     style="width: {{ $task->progress ?? 0 }}%"></div>
                            </div>
                            <small class="text-muted">{{ $task->progress ?? 0 }}% Complete</small>
                        </div>
                    </div>

                    @if($task->start_date || $task->due_date)
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Timeline</h6>
                            <div class="row">
                                @if($task->start_date)
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-play-circle text-success me-2"></i>
                                            <div>
                                                <strong class="text-dark">Start Date</strong><br>
                                                <span class="text-muted">{{ $task->start_date->format('M j, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if($task->due_date)
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-calendar-event text-{{ $task->due_date->isPast() && $task->status !== 'completed' ? 'danger' : 'primary' }} me-2"></i>
                                            <div>
                                                <strong class="text-dark">Due Date</strong><br>
                                                <span class="text-{{ $task->due_date->isPast() && $task->status !== 'completed' ? 'danger' : 'muted' }}">{{ $task->due_date->format('M j, Y') }}</span>
                                                @if($task->due_date->isPast() && $task->status !== 'completed')
                                                    <small class="text-danger d-block">{{ $task->due_date->diffForHumans() }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($task->estimated_time || $task->estimated_cost)
                        <div class="row">
                            @if($task->estimated_time)
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-2">Estimated Time</h6>
                                    <p class="mb-3 text-dark">{{ $task->estimated_time }} {{ $task->estimated_time_unit ?? 'hours' }}</p>
                                </div>
                            @endif
                            @if($task->estimated_cost)
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-2">Estimated Cost</h6>
                                    <p class="mb-3 text-success fw-bold">{{ auth()->user()->company->formatCurrency($task->estimated_cost, 2) }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Column (like project page) -->
        <div class="col-lg-3">
            @if(auth()->user()->canManageTasks() || in_array(auth()->user()->role, ['site_manager', 'project_manager']) || $task->assigned_to === auth()->id())
                <!-- Quick Actions Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light text-dark">
                        <h5 class="mb-0 text-dark"><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if($task->status !== 'completed')
                                <button class="btn btn-success" onclick="updateTaskStatusInModal({{ $task->id }}, 'completed')">
                                    <i class="bi bi-check-circle me-2"></i>Mark Complete
                                </button>
                            @endif
                            
                            @if($task->status !== 'in_progress')
                                <button class="btn btn-primary" onclick="updateTaskStatusInModal({{ $task->id }}, 'in_progress')">
                                    <i class="bi bi-play-circle me-2"></i>Start Task
                                </button>
                            @endif
                            
                            @if($task->status === 'completed')
                                <button class="btn btn-warning" onclick="updateTaskStatusInModal({{ $task->id }}, 'in_progress')">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Reopen
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if(auth()->user()->canManageTasks() || in_array(auth()->user()->role, ['site_manager', 'project_manager']))
                <!-- Task Management Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light text-dark">
                        <h5 class="mb-0 text-dark"><i class="bi bi-gear me-2"></i>Task Management</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if($task->is_currently_on_hold)
                                <button class="btn btn-success" onclick="removeOnHold({{ $task->id }})">
                                    <i class="bi bi-play-fill me-2"></i>Remove Hold
                                </button>
                            @else
                                <button class="btn btn-danger" onclick="showOnHoldModal({{ $task->id }})">
                                    <i class="bi bi-pause-fill me-2"></i>Put On Hold
                                </button>
                            @endif

                            @if($task->is_currently_delayed)
                                <button class="btn btn-outline-warning" onclick="removeDelay({{ $task->id }})">
                                    <i class="bi bi-clock-history me-2"></i>Remove Delay
                                </button>
                            @else
                                <button class="btn btn-outline-warning" onclick="showDelayModal({{ $task->id }})">
                                    <i class="bi bi-clock me-2"></i>Apply Delay
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Task Details Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light text-dark">
                    <h5 class="mb-0 text-dark"><i class="bi bi-info-circle me-2"></i>Task Details</h5>
                </div>
                <div class="card-body">
                    <div class="detail-item d-flex justify-content-between align-items-start mb-3">
                        <span class="text-muted">Task ID</span>
                        <span class="text-dark">#{{ $task->id }}</span>
                    </div>
                    
                    <div class="detail-item d-flex justify-content-between align-items-start mb-3">
                        <span class="text-muted">Created By</span>
                        <span class="text-dark">
                            @if($task->createdBy)
                                {{ $task->createdBy->name }}
                            @else
                                <span class="text-muted">Unknown</span>
                            @endif
                        </span>
                    </div>

                    <div class="detail-item d-flex justify-content-between align-items-start mb-3">
                        <span class="text-muted">Last Updated</span>
                        <span class="text-dark text-end">
                            <div>{{ $task->updated_at->diffForHumans() }}</div>
                            <small class="text-muted">{{ $task->updated_at->format('M j, Y g:i A') }}</small>
                        </span>
                    </div>

                    @if($task->taskCategory)
                        <div class="detail-item d-flex justify-content-between align-items-start">
                            <span class="text-muted">Category</span>
                            <span class="text-dark">{{ $task->taskCategory->name }}</span>
                        </div>
                    @endif
                </div>
            </div>

            @if(auth()->user()->canManageTasks() || in_array(auth()->user()->role, ['site_manager', 'project_manager']))
                <!-- Actions Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light text-dark">
                        <h5 class="mb-0 text-dark"><i class="bi bi-link-45deg me-2"></i>Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-primary">
                                <i class="bi bi-pencil me-2"></i>Edit Task
                            </a>
                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-secondary" target="_blank">
                                <i class="bi bi-box-arrow-up-right me-2"></i>Full View
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Delay/Hold Information Alerts (outside grid like project page) -->
    @if($task->is_delayed || $task->delay_days || $task->delay_reason)
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-warning">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clock me-2"></i>
                        <div class="flex-grow-1">
                            <strong>Delay Information</strong>
                            @if($task->is_currently_delayed)
                                <span class="badge bg-warning ms-2">Active</span>
                            @else
                                <span class="badge bg-secondary ms-2">Resolved</span>
                            @endif
                        </div>
                    </div>
                    @if($task->delay_days)
                        <div class="mt-2"><strong>Duration:</strong> {{ $task->delay_days }} days</div>
                    @endif
                    @if($task->delay_reason)
                        <div class="mt-1"><strong>Reason:</strong> {{ $task->delay_reason }}</div>
                    @endif
                    @if($task->delay_applied_date)
                        <div class="mt-1">
                            <small class="text-muted">
                                Applied {{ $task->delay_applied_date->format('M j, Y') }}
                                @if($task->delayAppliedBy) by {{ $task->delayAppliedBy->name }}@endif
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if($task->is_on_hold || $task->on_hold_reason)
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-danger">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-pause-fill me-2"></i>
                        <div class="flex-grow-1">
                            <strong>On Hold Information</strong>
                            @if($task->is_currently_on_hold)
                                <span class="badge bg-danger ms-2">On Hold</span>
                            @else
                                <span class="badge bg-secondary ms-2">Resolved</span>
                            @endif
                        </div>
                    </div>
                    @if($task->on_hold_reason)
                        <div class="mt-2"><strong>Reason:</strong> {{ $task->on_hold_reason }}</div>
                    @endif
                    @if($task->on_hold_date)
                        <div class="mt-1">
                            <small class="text-muted">
                                Put on hold {{ $task->on_hold_date->format('M j, Y') }}
                                @if($task->onHoldAppliedBy) by {{ $task->onHoldAppliedBy->name }}@endif
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Traditional styling to match project page exactly --}}
<style>
.task-modal-content .page-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #212529;
}

.task-modal-content .page-subtitle {
    color: #6c757d;
    font-size: 0.875rem;
}

/* Ensure all text is readable - no white text on light backgrounds */
.task-modal-content .card-body {
    color: #212529;
}

.task-modal-content .detail-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.task-modal-content .detail-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

/* Make cards exactly match project page styling */
.task-modal-content .card-header {
    font-weight: 600;
    border-bottom: 1px solid rgba(0,0,0,0.125);
}

.task-modal-content .card-header.bg-light {
    background-color: #f8f9fa !important;
    color: #212529 !important;
    border-bottom: 1px solid #dee2e6;
}

.task-modal-content .card-header.bg-light h5 {
    color: #212529 !important;
}

.task-modal-content .card-body {
    padding: 1.25rem;
    background-color: #ffffff;
}

/* Fix text readability - ensure dark text on light backgrounds */
.task-modal-content .text-dark {
    color: #212529 !important;
}

.task-modal-content h6 {
    color: #6c757d;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .task-modal-content .col-lg-9,
    .task-modal-content .col-lg-3 {
        margin-bottom: 1rem;
    }
    
    .task-modal-content .page-title {
        font-size: 1.25rem;
    }
}
</style>