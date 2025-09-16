<!-- Company Information -->
<div class="row mb-4">
    <div class="col-md-6">
        <h6 class="text-muted mb-3">Company Information</h6>
        <div class="info-grid">
            <div class="info-item">
                <label class="info-label">Company Name</label>
                <div class="info-value">{{ $client->company_name }}</div>
            </div>
            @if($client->legal_name && $client->legal_name !== $client->company_name)
                <div class="info-item">
                    <label class="info-label">Legal Name</label>
                    <div class="info-value">{{ $client->legal_name }}</div>
                </div>
            @endif
            @if($client->business_type)
                <div class="info-item">
                    <label class="info-label">Business Type</label>
                    <div class="info-value">{{ $client->business_type }}</div>
                </div>
            @endif
            @if($client->industry)
                <div class="info-item">
                    <label class="info-label">Industry</label>
                    <div class="info-value">{{ $client->industry }}</div>
                </div>
            @endif
            @if($client->tax_id)
                <div class="info-item">
                    <label class="info-label">Tax ID / EIN</label>
                    <div class="info-value">{{ $client->tax_id }}</div>
                </div>
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <h6 class="text-muted mb-3">Business Details</h6>
        <div class="info-grid">
            @if($client->website)
                <div class="info-item">
                    <label class="info-label">Website</label>
                    <div class="info-value">
                        <a href="{{ $client->website }}" target="_blank" class="text-decoration-none">
                            {{ $client->website }} <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                </div>
            @endif
            @if($client->business_description)
                <div class="info-item">
                    <label class="info-label">Business Description</label>
                    <div class="info-value">{{ $client->business_description }}</div>
                </div>
            @endif
            <div class="info-item">
                <label class="info-label">Client Since</label>
                <div class="info-value">{{ $client->created_at->format('F j, Y') }}</div>
            </div>
            <div class="info-item">
                <label class="info-label">Last Updated</label>
                <div class="info-value">{{ $client->updated_at->diffForHumans() }}</div>
            </div>
        </div>
    </div>
</div>

@if($client->notes)
<!-- Notes Section -->
<div class="mb-4">
    <h6 class="text-muted mb-3">Notes</h6>
    <div class="notes-content">
        {{ $client->notes }}
    </div>
</div>
@endif

<!-- Recent Activity Summary -->
<div class="row">
    <div class="col-md-6">
        <h6 class="text-muted mb-3">Recent Sites</h6>
        @if($client->sites && $client->sites->count() > 0)
            <div class="activity-list">
                @foreach($client->sites->take(3) as $site)
                    <div class="activity-item">
                        <div class="activity-icon bg-primary">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">
                                <a href="{{ route('sites.show', $site) }}" class="text-decoration-none">
                                    {{ $site->name }}
                                </a>
                            </div>
                            <div class="activity-meta">
                                <span class="badge bg-{{ $site->status === 'active' ? 'success' : ($site->status === 'planning' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($site->status) }}
                                </span>
                                <span class="text-muted ms-2">{{ $site->progress }}% complete</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($client->sites->count() > 3)
                <div class="text-center mt-3">
                    <button class="btn btn-sm btn-outline-primary" onclick="document.getElementById('sites-tab').click()">
                        View All Sites ({{ $client->sites->count() }})
                    </button>
                </div>
            @endif
        @else
            <div class="empty-state">
                <i class="bi bi-building text-muted"></i>
                <p class="text-muted mb-0">No sites yet</p>
            </div>
        @endif
    </div>

    <div class="col-md-6">
        <h6 class="text-muted mb-3">Recent Projects</h6>
        @if($client->projects && $client->projects->count() > 0)
            <div class="activity-list">
                @foreach($client->projects->take(3) as $project)
                    <div class="activity-item">
                        <div class="activity-icon bg-info">
                            <i class="bi bi-folder"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">
                                <a href="{{ route('projects.show', $project) }}" class="text-decoration-none">
                                    {{ $project->name }}
                                </a>
                            </div>
                            <div class="activity-meta">
                                <span class="badge bg-{{ $project->status === 'in_progress' ? 'success' : ($project->status === 'planning' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                </span>
                                @if($project->budget)
                                    <span class="text-muted ms-2">${{ number_format($project->budget, 0) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($client->projects->count() > 3)
                <div class="text-center mt-3">
                    <button class="btn btn-sm btn-outline-primary" onclick="document.getElementById('projects-tab').click()">
                        View All Projects ({{ $client->projects->count() }})
                    </button>
                </div>
            @endif
        @else
            <div class="empty-state">
                <i class="bi bi-folder text-muted"></i>
                <p class="text-muted mb-0">No projects yet</p>
            </div>
        @endif
    </div>
</div>

<style>
.info-grid {
    display: grid;
    gap: 1rem;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.info-value {
    color: #1f2937;
    font-size: 0.95rem;
}

.notes-content {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    color: #374151;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
    min-width: 0;
}

.activity-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.activity-title a {
    color: #1f2937;
}

.activity-title a:hover {
    color: #4f46e5;
}

.activity-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.empty-state {
    text-align: center;
    padding: 2rem 1rem;
}

.empty-state i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}
</style>


