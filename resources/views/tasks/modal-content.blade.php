{{-- Compact Task Modal Content --}}
<div class="task-modal-content p-2">
    <!-- Compact Header -->
    <div class="modal-task-header mb-4 p-4 bg-light rounded">
        <div class="d-flex align-items-start justify-content-between">
            <div class="flex-grow-1">
                <h5 class="task-modal-title mb-2 fw-bold text-dark">{{ $task->title }}</h5>
                <div class="task-badges d-flex flex-wrap gap-1 mb-2">
                    @if($task->project)
                        <span class="badge bg-primary badge-sm">{{ $task->project->name }}</span>
                    @endif
                    @if($task->project && $task->project->site)
                        <span class="badge bg-info badge-sm">{{ $task->project->site->name }}</span>
                    @endif
                    <span class="badge bg-{{ $task->delay_hold_status_color }} badge-sm">
                        @if($task->is_currently_on_hold)
                            🚫 On Hold
                        @elseif($task->is_currently_delayed)
                            ⏰ Delayed
                        @else
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        @endif
                    </span>
                    <span class="badge bg-{{ $task->priority_color }} badge-sm">{{ ucfirst($task->priority) }}</span>
                    @if($task->taskCategory)
                        <span class="badge bg-secondary badge-sm">{{ $task->taskCategory->name }}</span>
                    @endif
                </div>
            </div>
            @if($task->due_date)
                <div class="due-date-compact text-end">
                    <div class="due-date-value {{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-danger' : 'text-success' }}">
                        <small class="text-muted d-block">Due Date</small>
                        <strong class="small">{{ $task->due_date->format('M j, Y') }}</strong>
                        @if($task->due_date->isPast() && $task->status !== 'completed')
                            <small class="text-danger d-block">{{ $task->due_date->diffForHumans() }}</small>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Main Content in Compact Grid -->
    <div class="row g-4 px-2">
        <!-- Left Column - Task Info -->
        <div class="col-md-7">
            <!-- Description -->
            @if($task->description)
                <div class="info-section mb-3">
                    <h6 class="section-title">Description</h6>
                    <div class="info-content">{{ $task->description }}</div>
                </div>
            @endif

            <!-- Assignment & Progress -->
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <div class="info-section">
                        <h6 class="section-title">Assigned To</h6>
                        <div class="info-content">
                            @if($task->assignedUser)
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                        {{ substr($task->assignedUser->name, 0, 1) }}
                                    </div>
                                    <span class="small">{{ $task->assignedUser->name }}</span>
                                </div>
                            @else
                                <span class="text-muted small">Unassigned</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="info-section">
                        <h6 class="section-title">Progress</h6>
                        <div class="info-content">
                            <div class="progress progress-sm mb-1">
                                <div class="progress-bar bg-{{ $task->status_color }}" role="progressbar" 
                                     style="width: {{ $task->progress ?? 0 }}%"></div>
                            </div>
                            <small class="text-muted">{{ $task->progress ?? 0 }}% Complete</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            @if($task->start_date || $task->due_date)
                <div class="info-section mb-4">
                    <h6 class="section-title">Timeline</h6>
                    <div class="timeline-compact">
                        @if($task->start_date)
                            <div class="timeline-item">
                                <span class="timeline-icon bg-success"></span>
                                <span class="timeline-content">
                                    <strong>Start:</strong> {{ $task->start_date->format('M j, Y') }}
                                </span>
                            </div>
                        @endif
                        @if($task->due_date)
                            <div class="timeline-item">
                                <span class="timeline-icon bg-{{ $task->due_date->isPast() && $task->status !== 'completed' ? 'danger' : 'primary' }}"></span>
                                <span class="timeline-content">
                                    <strong>Due:</strong> {{ $task->due_date->format('M j, Y') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Time & Cost Estimates -->
            @if($task->estimated_time || $task->estimated_cost)
                <div class="row g-3 mb-4">
                    @if($task->estimated_time)
                        <div class="col-6">
                            <div class="info-section">
                                <h6 class="section-title">Est. Time</h6>
                                <div class="info-content">
                                    <span class="fw-medium">{{ $task->estimated_time }}</span>
                                    <small class="text-muted">{{ $task->estimated_time_unit ?? 'hours' }}</small>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($task->estimated_cost)
                        <div class="col-6">
                            <div class="info-section">
                                <h6 class="section-title">Est. Cost</h6>
                                <div class="info-content">
                                    <span class="fw-medium text-success">£{{ number_format($task->estimated_cost, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Right Column - Actions & Status -->
        <div class="col-md-5">
            <!-- Quick Actions -->
            @if(auth()->user()->canManageTasks() || in_array(auth()->user()->role, ['site_manager', 'project_manager']) || $task->assigned_to === auth()->id())
                <div class="actions-section mb-4">
                    <h6 class="section-title">Quick Actions</h6>
                    <div class="d-grid gap-2">
                        @if($task->status !== 'completed')
                            <button class="btn btn-success btn-sm" onclick="updateTaskStatusInModal({{ $task->id }}, 'completed')">
                                <i class="bi bi-check-circle me-1"></i>Mark Complete
                            </button>
                        @endif
                        
                        @if($task->status !== 'in_progress')
                            <button class="btn btn-primary btn-sm" onclick="updateTaskStatusInModal({{ $task->id }}, 'in_progress')">
                                <i class="bi bi-play-circle me-1"></i>Start Task
                            </button>
                        @endif
                        
                        @if($task->status === 'completed')
                            <button class="btn btn-warning btn-sm" onclick="updateTaskStatusInModal({{ $task->id }}, 'in_progress')">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reopen
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Delay/Hold Management -->
            @if(auth()->user()->canManageTasks() || in_array(auth()->user()->role, ['site_manager', 'project_manager']))
                <div class="actions-section mb-4">
                    <h6 class="section-title">Task Management</h6>
                    <div class="d-grid gap-2">
                        @if($task->is_currently_on_hold)
                            <button class="btn btn-outline-success btn-sm" onclick="removeOnHold({{ $task->id }})">
                                <i class="bi bi-play-fill me-1"></i>Remove Hold
                            </button>
                        @else
                            <button class="btn btn-outline-danger btn-sm" onclick="showOnHoldModal({{ $task->id }})">
                                <i class="bi bi-pause-fill me-1"></i>Put On Hold
                            </button>
                        @endif

                        @if($task->is_currently_delayed)
                            <button class="btn btn-outline-warning btn-sm" onclick="removeDelay({{ $task->id }})">
                                <i class="bi bi-clock-history me-1"></i>Remove Delay
                            </button>
                        @else
                            <button class="btn btn-outline-warning btn-sm" onclick="showDelayModal({{ $task->id }})">
                                <i class="bi bi-clock me-1"></i>Apply Delay
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Task Status Info -->
            <div class="status-section">
                <h6 class="section-title">Status Information</h6>
                
                <!-- Created By -->
                <div class="status-item">
                    <small class="text-muted">Created by</small>
                    <div>
                        @if($task->createdBy)
                            <span class="fw-medium">{{ $task->createdBy->name }}</span>
                        @else
                            <span class="text-muted">Unknown</span>
                        @endif
                    </div>
                </div>

                <!-- Last Updated -->
                <div class="status-item">
                    <small class="text-muted">Last updated</small>
                    <div>
                        <span class="fw-medium">{{ $task->updated_at->diffForHumans() }}</span>
                        <br><small class="text-muted">{{ $task->updated_at->format('M j, Y g:i A') }}</small>
                    </div>
                </div>

                <!-- Task ID -->
                <div class="status-item">
                    <small class="text-muted">Task ID</small>
                    <div><code class="small">#{{ $task->id }}</code></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delay Information (if applicable) -->
    @if($task->is_delayed || $task->delay_days || $task->delay_reason)
        <div class="alert alert-warning mt-3">
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
                <div class="mt-2">
                    <strong>Duration:</strong> {{ $task->delay_days }} days
                </div>
            @endif
            @if($task->delay_reason)
                <div class="mt-1">
                    <strong>Reason:</strong> {{ $task->delay_reason }}
                </div>
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
    @endif

    <!-- On Hold Information (if applicable) -->
    @if($task->is_on_hold || $task->on_hold_reason)
        <div class="alert alert-danger mt-3">
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
                <div class="mt-2">
                    <strong>Reason:</strong> {{ $task->on_hold_reason }}
                </div>
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
    @endif

    <!-- Additional Navigation -->
    @if(auth()->user()->canManageTasks() || in_array(auth()->user()->role, ['site_manager', 'project_manager']))
        <div class="modal-footer-actions mt-3 pt-3 border-top">
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-pencil me-1"></i>Edit Task
                </a>
                <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-secondary btn-sm" target="_blank">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Full View
                </a>
            </div>
        </div>
    @endif
</div>

<style>
/* Compact Modal Styles */
.task-modal-content {
    font-size: 14px;
}

.modal-task-header {
    border-left: 4px solid #007bff;
}

.badge-sm {
    font-size: 10px;
    padding: 0.25rem 0.5rem;
}

.section-title {
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.info-section {
    background: #f8f9fa;
    padding: 1rem 1.25rem;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.info-content {
    font-size: 13px;
    color: #495057;
}

.actions-section {
    background: #fff;
    padding: 1.25rem;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.status-section {
    background: #f8f9fa;
    padding: 1.25rem;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.status-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #e9ecef;
}

.status-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.avatar-sm {
    width: 24px;
    height: 24px;
    font-size: 11px;
    font-weight: 600;
}

.progress-sm {
    height: 6px;
}

.timeline-compact {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.timeline-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.timeline-icon {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}

.timeline-content {
    font-size: 12px;
}

.due-date-compact {
    min-width: 80px;
}

.due-date-value {
    background: #f8f9fa;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.btn-sm {
    font-size: 12px;
    padding: 0.375rem 0.75rem;
}

.alert {
    padding: 1rem 1.25rem;
    margin-bottom: 0;
    border-radius: 8px;
}

.modal-footer-actions {
    background: #f8f9fa;
    margin: 1rem -0.5rem -0.5rem -0.5rem;
    padding: 1.25rem;
    border-radius: 8px;
    border-top: 1px solid #e9ecef;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .task-modal-content {
        font-size: 13px;
    }
    
    .col-md-7, .col-md-5 {
        margin-bottom: 1rem;
    }
}
</style>