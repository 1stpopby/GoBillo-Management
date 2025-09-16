@extends('layouts.app')

@section('title', 'Project Schedule')

@section('content')
<div class="container-fluid project-schedule-page">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="page-title mb-0">
                    <i class="bi bi-calendar3 me-2"></i>Project Schedule
                </h1>
                <p class="page-subtitle text-muted mb-0">Manage project timelines, tasks, and milestones</p>
            </div>
            <div class="col-lg-4 text-end">
                <button type="button" class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                    <i class="bi bi-plus-circle me-2"></i>New Task
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4 g-3">
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="bi bi-list-task"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $stats['total_tasks'] }}</div>
                    <div class="stat-label">Total Tasks</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $stats['in_progress'] }}</div>
                    <div class="stat-label">In Progress</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $stats['completed'] }}</div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon bg-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $stats['overdue'] }}</div>
                    <div class="stat-label">Overdue</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="bi bi-calendar-week"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $stats['upcoming'] }}</div>
                    <div class="stat-label">Next 7 Days</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon bg-purple">
                    <i class="bi bi-flag"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $stats['milestones'] }}</div>
                    <div class="stat-label">Milestones</div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Switcher and Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-lg-2">
                    <div class="btn-group w-100" role="group">
                        <a href="?view=calendar" class="btn {{ $view === 'calendar' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="bi bi-calendar3"></i> Calendar
                        </a>
                        <a href="?view=gantt" class="btn {{ $view === 'gantt' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="bi bi-bar-chart-steps"></i> Gantt
                        </a>
                        <a href="?view=timeline" class="btn {{ $view === 'timeline' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="bi bi-clock-history"></i> Timeline
                        </a>
                        <a href="?view=list" class="btn {{ $view === 'list' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="bi bi-list-ul"></i> List
                        </a>
                    </div>
                </div>
                <div class="col-lg-2">
                    <select class="form-select" id="projectFilter">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <select class="form-select" id="assigneeFilter">
                        <option value="">All Assignees</option>
                        @foreach($teamMembers as $member)
                            <option value="{{ $member->id }}" {{ request('assigned_to') == $member->id ? 'selected' : '' }}>
                                {{ $member->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="not_started" {{ request('status') == 'not_started' ? 'selected' : '' }}>Not Started</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="delayed" {{ request('status') == 'delayed' ? 'selected' : '' }}>Delayed</option>
                        <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <select class="form-select" id="priorityFilter">
                        <option value="">All Priorities</option>
                        <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <button type="button" class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                        <i class="bi bi-x-circle me-1"></i>Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($view === 'calendar')
                <!-- Calendar View -->
                <div id="calendar" style="min-height: 600px;"></div>
            @elseif($view === 'gantt')
                <!-- Gantt Chart View -->
                <div id="gantt" style="width:100%; height:600px;"></div>
            @elseif($view === 'timeline')
                <!-- Timeline View -->
                <div id="timeline" style="height: 600px;"></div>
            @else
                <!-- List View -->
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="30"></th>
                                <th>Task Name</th>
                                <th>Project</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Assigned To</th>
                                <th>Progress</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($schedules as $schedule)
                                <tr class="task-row" data-id="{{ $schedule->id }}">
                                    <td>
                                        @if($schedule->subtasks->count() > 0)
                                            <button class="btn btn-sm btn-link p-0 toggle-subtasks" data-id="{{ $schedule->id }}">
                                                <i class="bi bi-chevron-right"></i>
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($schedule->is_milestone)
                                                <i class="bi bi-flag-fill text-purple me-2"></i>
                                            @endif
                                            <strong>{{ $schedule->task_name }}</strong>
                                        </div>
                                        @if($schedule->description)
                                            <small class="text-muted">{{ Str::limit($schedule->description, 100) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('projects.show', $schedule->project) }}" class="text-decoration-none">
                                            {{ $schedule->project->name }}
                                        </a>
                                    </td>
                                    <td>{{ $schedule->start_date->format('M d, Y') }}</td>
                                    <td>
                                        {{ $schedule->end_date->format('M d, Y') }}
                                        @if($schedule->is_overdue)
                                            <span class="badge bg-danger ms-1">Overdue</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($schedule->assignedTo)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    {{ strtoupper(substr($schedule->assignedTo->name, 0, 2)) }}
                                                </div>
                                                {{ $schedule->assignedTo->name }}
                                            </div>
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ $schedule->progress }}%; background-color: {{ $schedule->color }};"
                                                 aria-valuenow="{{ $schedule->progress }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ $schedule->formatted_progress }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm status-select" data-id="{{ $schedule->id }}">
                                            <option value="not_started" {{ $schedule->status === 'not_started' ? 'selected' : '' }}>Not Started</option>
                                            <option value="in_progress" {{ $schedule->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="completed" {{ $schedule->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="delayed" {{ $schedule->status === 'delayed' ? 'selected' : '' }}>Delayed</option>
                                            <option value="on_hold" {{ $schedule->status === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                            <option value="cancelled" {{ $schedule->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill" style="background-color: {{ $schedule->priority_color }}20; color: {{ $schedule->priority_color }};">
                                            {{ $schedule->priority_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="editTask({{ $schedule->id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info" onclick="viewTask({{ $schedule->id }})">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteTask({{ $schedule->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                
                                @if($schedule->subtasks->count() > 0)
                                    @foreach($schedule->subtasks as $subtask)
                                        <tr class="subtask-row d-none" data-parent="{{ $schedule->id }}">
                                            <td></td>
                                            <td class="ps-5">
                                                <i class="bi bi-arrow-return-right text-muted me-2"></i>
                                                {{ $subtask->task_name }}
                                            </td>
                                            <td>-</td>
                                            <td>{{ $subtask->start_date->format('M d, Y') }}</td>
                                            <td>{{ $subtask->end_date->format('M d, Y') }}</td>
                                            <td>
                                                @if($subtask->assignedTo)
                                                    {{ $subtask->assignedTo->name }}
                                                @else
                                                    <span class="text-muted">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 15px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ $subtask->progress }}%;"
                                                         aria-valuenow="{{ $subtask->progress }}">
                                                        {{ $subtask->formatted_progress }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $subtask->status === 'completed' ? 'success' : 'secondary' }}">
                                                    {{ $subtask->status_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ $subtask->priority_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editTask({{ $subtask->id }})">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <i class="bi bi-calendar-x fs-1 text-muted"></i>
                                        <p class="mt-2 text-muted">No scheduled tasks found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($view === 'list' && $schedules->hasPages())
                    <div class="card-footer">
                        {{ $schedules->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Create Task Modal -->
<div class="modal fade" id="createTaskModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createTaskForm" action="{{ route('project-schedules.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Task Name *</label>
                            <input type="text" class="form-control" name="task_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Project *</label>
                            <select class="form-select" name="project_id" id="modalProjectSelect" required>
                                <option value="">Select Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Assigned To</label>
                            <select class="form-select" name="assigned_to">
                                <option value="">Unassigned</option>
                                @foreach($teamMembers as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Start Date *</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Date *</label>
                            <input type="date" class="form-control" name="end_date" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Priority *</label>
                            <select class="form-select" name="priority" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Parent Task</label>
                            <select class="form-select" name="parent_task_id" id="modalParentSelect">
                                <option value="">None (Root Task)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estimated Hours</label>
                            <input type="number" class="form-control" name="estimated_hours" step="0.5" min="0">
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_milestone" id="isMilestone" value="1">
                                <label class="form-check-label" for="isMilestone">
                                    This is a milestone
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Task Color</label>
                            <input type="color" class="form-control form-control-color" name="color" value="#3B82F6">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' />
<style>
    .project-schedule-page {
        padding: 1.5rem;
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
    }

    .page-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        transition: all 0.3s;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin-right: 1rem;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    .bg-purple {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .avatar-sm {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .task-row {
        transition: background 0.2s;
    }

    .task-row:hover {
        background: #f9fafb;
    }

    .subtask-row {
        background: #f9fafb;
    }

    .toggle-subtasks {
        transition: transform 0.2s;
    }

    .toggle-subtasks.expanded i {
        transform: rotate(90deg);
    }

    /* Calendar Styles */
    .fc-event {
        border: none;
        padding: 2px 5px;
        border-radius: 4px;
        cursor: pointer;
    }

    .fc-daygrid-event {
        white-space: normal;
    }

    /* Status Select Styling */
    .status-select {
        border: none;
        background: transparent;
        cursor: pointer;
    }

    .status-select:focus {
        box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2);
    }

    /* Gantt Chart Container */
    #gantt {
        border: 1px solid #e5e7eb;
    }

    /* Timeline Container */
    #timeline {
        border: 1px solid #e5e7eb;
        background: white;
    }
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const view = '{{ $view }}';
    
    // Initialize based on view
    if (view === 'calendar') {
        initCalendar();
    } else if (view === 'gantt') {
        loadGanttLibrary();
    } else if (view === 'timeline') {
        loadTimelineLibrary();
    }
    
    // Toggle subtasks
    document.querySelectorAll('.toggle-subtasks').forEach(btn => {
        btn.addEventListener('click', function() {
            const parentId = this.dataset.id;
            const subtasks = document.querySelectorAll(`tr[data-parent="${parentId}"]`);
            
            subtasks.forEach(row => {
                row.classList.toggle('d-none');
            });
            
            this.classList.toggle('expanded');
        });
    });
    
    // Status change handler
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            updateTaskStatus(this.dataset.id, this.value);
        });
    });
    
    // Project select change handler for modal
    document.getElementById('modalProjectSelect').addEventListener('change', function() {
        loadParentTasks(this.value);
    });
});

// Initialize Calendar View
function initCalendar() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        events: '{{ route("project-schedules.calendar-events") }}',
        eventClick: function(info) {
            viewTask(info.event.id);
        },
        eventDidMount: function(info) {
            // Add tooltip
            info.el.setAttribute('title', `${info.event.extendedProps.status} - ${info.event.extendedProps.progress}% complete`);
        }
    });
    calendar.render();
}

// Load Gantt Chart Library
function loadGanttLibrary() {
    const script = document.createElement('script');
    script.src = 'https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js';
    script.onload = function() {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css';
        document.head.appendChild(link);
        
        setTimeout(initGantt, 100);
    };
    document.head.appendChild(script);
}

// Initialize Gantt Chart
function initGantt() {
    gantt.config.date_format = "%Y-%m-%d";
    gantt.config.columns = [
        {name: "text", label: "Task", tree: true, width: 200},
        {name: "start_date", label: "Start", align: "center", width: 80},
        {name: "duration", label: "Duration", align: "center", width: 60},
        {name: "progress", label: "Progress", align: "center", width: 80, template: function(task) {
            return Math.round(task.progress * 100) + "%";
        }}
    ];
    
    gantt.init("gantt");
    gantt.load('{{ route("project-schedules.gantt-data") }}');
}

// Load Timeline Library
function loadTimelineLibrary() {
    // Implementation for timeline view
    // You can use vis.js or another timeline library
    document.getElementById('timeline').innerHTML = '<div class="text-center py-5">Timeline view coming soon...</div>';
}

// Filter handlers
function clearFilters() {
    window.location.href = '{{ route("project-schedules.index") }}?view={{ $view }}';
}

// Apply filters
document.querySelectorAll('#projectFilter, #assigneeFilter, #statusFilter, #priorityFilter').forEach(filter => {
    filter.addEventListener('change', function() {
        const params = new URLSearchParams(window.location.search);
        
        if (this.value) {
            params.set(this.id.replace('Filter', ''), this.value);
        } else {
            params.delete(this.id.replace('Filter', ''));
        }
        
        window.location.href = '{{ route("project-schedules.index") }}?' + params.toString();
    });
});

// Task operations
function viewTask(id) {
    window.location.href = `/project-schedules/${id}`;
}

function editTask(id) {
    window.location.href = `/project-schedules/${id}/edit`;
}

function deleteTask(id) {
    if (confirm('Are you sure you want to delete this task?')) {
        fetch(`/project-schedules/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function updateTaskStatus(id, status) {
    fetch(`/project-schedules/${id}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            console.log('Status updated successfully');
        }
    });
}

function loadParentTasks(projectId) {
    if (!projectId) {
        document.getElementById('modalParentSelect').innerHTML = '<option value="">None (Root Task)</option>';
        return;
    }
    
    // Fetch parent tasks for selected project
    fetch(`/project-schedules/create?project_id=${projectId}`, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        let options = '<option value="">None (Root Task)</option>';
        data.parentTasks.forEach(task => {
            options += `<option value="${task.id}">${task.task_name}</option>`;
        });
        document.getElementById('modalParentSelect').innerHTML = options;
    });
}

// Form submission
document.getElementById('createTaskForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
});
</script>
@endpush

@endsection


