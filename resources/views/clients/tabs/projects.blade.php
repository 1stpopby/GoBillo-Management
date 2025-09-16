<div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="text-muted mb-0">Client Projects</h6>
    <a href="{{ route('projects.create') }}?client_id={{ $client->id }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus me-1"></i>New Project
    </a>
</div>

@if($client->projects && $client->projects->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Project Name</th>
                    <th>Site</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Budget</th>
                    <th>Manager</th>
                    <th>Timeline</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($client->projects as $project)
                    <tr>
                        <td>
                            <div class="project-info">
                                <a href="{{ route('projects.show', $project) }}" class="project-name text-decoration-none">
                                    {{ $project->name }}
                                </a>
                                @if($project->description)
                                    <div class="project-description">
                                        {{ Str::limit($project->description, 60) }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($project->site)
                                <a href="{{ route('sites.show', $project->site) }}" class="text-decoration-none">
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-building me-1"></i>{{ $project->site->name }}
                                    </span>
                                </a>
                            @else
                                <span class="text-muted">No site</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $project->status === 'in_progress' ? 'success' : ($project->status === 'planning' ? 'warning' : ($project->status === 'completed' ? 'info' : ($project->status === 'on_hold' ? 'secondary' : 'danger'))) }}">
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </span>
                        </td>
                        <td>
                            <div class="progress-container">
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-{{ $project->progress >= 75 ? 'success' : ($project->progress >= 50 ? 'info' : ($project->progress >= 25 ? 'warning' : 'danger')) }}" 
                                         style="width: {{ $project->progress }}%"></div>
                                </div>
                                <small class="text-muted">{{ $project->progress }}%</small>
                            </div>
                        </td>
                        <td>
                            @if($project->budget)
                                <span class="budget-amount">${{ number_format($project->budget, 0) }}</span>
                            @else
                                <span class="text-muted">Not set</span>
                            @endif
                        </td>
                        <td>
                            @if($project->manager)
                                <div class="manager-info">
                                    <i class="bi bi-person-circle me-1"></i>
                                    {{ $project->manager->name }}
                                </div>
                            @else
                                <span class="text-muted">Unassigned</span>
                            @endif
                        </td>
                        <td>
                            <div class="timeline-info">
                                @if($project->start_date)
                                    <div class="timeline-item">
                                        <i class="bi bi-play-circle text-success me-1"></i>
                                        <span class="timeline-date">{{ $project->start_date->format('M j') }}</span>
                                    </div>
                                @endif
                                @if($project->end_date)
                                    <div class="timeline-item">
                                        <i class="bi bi-flag text-danger me-1"></i>
                                        <span class="timeline-date">{{ $project->end_date->format('M j') }}</span>
                                    </div>
                                @endif
                                @if(!$project->start_date && !$project->end_date)
                                    <span class="text-muted">Not scheduled</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-primary" title="View Project">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-secondary" title="Edit Project">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('invoices.create') }}?project_id={{ $project->id }}" class="btn btn-outline-success" title="Create Invoice">
                                    <i class="bi bi-receipt"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Project Summary -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-number text-info">{{ $client->projects->where('status', 'planning')->count() }}</div>
                <div class="summary-label">Planning</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-number text-success">{{ $client->projects->where('status', 'in_progress')->count() }}</div>
                <div class="summary-label">In Progress</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-number text-warning">{{ $client->projects->where('status', 'on_hold')->count() }}</div>
                <div class="summary-label">On Hold</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-number text-primary">{{ $client->projects->where('status', 'completed')->count() }}</div>
                <div class="summary-label">Completed</div>
            </div>
        </div>
    </div>
@else
    <div class="empty-state-large">
        <div class="empty-icon">
            <i class="bi bi-folder"></i>
        </div>
        <h5>No Projects Yet</h5>
        <p class="text-muted">This client doesn't have any projects yet. Create the first project to get started.</p>
        <a href="{{ route('projects.create') }}?client_id={{ $client->id }}" class="btn btn-primary">
            <i class="bi bi-plus me-2"></i>Create First Project
        </a>
    </div>
@endif

<style>
.project-info {
    min-width: 200px;
}

.project-name {
    font-weight: 600;
    color: #1f2937;
    display: block;
    margin-bottom: 0.25rem;
}

.project-name:hover {
    color: #4f46e5;
}

.project-description {
    font-size: 0.875rem;
    color: #6b7280;
    line-height: 1.4;
}

.progress-container {
    min-width: 100px;
}

.progress {
    margin-bottom: 0.25rem;
}

.budget-amount {
    font-weight: 600;
    color: #059669;
}

.manager-info {
    display: flex;
    align-items: center;
    font-size: 0.875rem;
    color: #374151;
}

.timeline-info {
    font-size: 0.875rem;
}

.timeline-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.25rem;
}

.timeline-date {
    font-size: 0.8125rem;
}

.summary-card {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
}

.summary-number {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.summary-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

.empty-state-large {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: #9ca3af;
}

.empty-state-large h5 {
    margin-bottom: 0.5rem;
    color: #374151;
}

.empty-state-large p {
    max-width: 400px;
    margin: 0 auto 2rem;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}
</style>


