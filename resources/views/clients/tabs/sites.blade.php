<div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="text-muted mb-0">Client Sites</h6>
    <a href="{{ route('sites.create') }}?client_id={{ $client->id }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus me-1"></i>New Site
    </a>
</div>

@if($client->sites && $client->sites->count() > 0)
    <div class="row">
        @foreach($client->sites as $site)
            <div class="col-md-6 mb-4">
                <div class="site-card">
                    <div class="site-header">
                        <div class="site-info">
                            <h6 class="site-name">
                                <a href="{{ route('sites.show', $site) }}" class="text-decoration-none">
                                    {{ $site->name }}
                                </a>
                            </h6>
                            <div class="site-meta">
                                <span class="badge bg-{{ $site->status === 'active' ? 'success' : ($site->status === 'planning' ? 'warning' : ($site->status === 'completed' ? 'info' : 'secondary')) }}">
                                    {{ ucfirst($site->status) }}
                                </span>
                                @if($site->manager)
                                    <span class="text-muted ms-2">
                                        <i class="bi bi-person me-1"></i>{{ $site->manager->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="site-progress">
                            <div class="progress-circle" data-progress="{{ $site->progress }}">
                                <span class="progress-text">{{ $site->progress }}%</span>
                            </div>
                        </div>
                    </div>
                    
                    @if($site->description)
                        <div class="site-description">
                            {{ Str::limit($site->description, 120) }}
                        </div>
                    @endif

                    <div class="site-stats">
                        <div class="stat-item">
                            <i class="bi bi-folder text-info"></i>
                            <span>{{ $site->projects->count() }} Projects</span>
                        </div>
                        @if($site->address)
                            <div class="stat-item">
                                <i class="bi bi-geo-alt text-warning"></i>
                                <span>{{ $site->address }}</span>
                            </div>
                        @endif
                        @if($site->created_at)
                            <div class="stat-item">
                                <i class="bi bi-calendar text-secondary"></i>
                                <span>Created {{ $site->created_at->format('M j, Y') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="site-actions">
                        <a href="{{ route('sites.show', $site) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>View Details
                        </a>
                        @if($site->projects->count() > 0)
                            <span class="text-muted ms-2">{{ $site->projects->count() }} project(s)</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($client->sites->count() > 6)
        <div class="text-center mt-4">
            <a href="{{ route('sites.index') }}?client_id={{ $client->id }}" class="btn btn-outline-primary">
                View All Sites
            </a>
        </div>
    @endif
@else
    <div class="empty-state-large">
        <div class="empty-icon">
            <i class="bi bi-building"></i>
        </div>
        <h5>No Sites Yet</h5>
        <p class="text-muted">This client doesn't have any construction sites yet. Create the first site to get started.</p>
        <a href="{{ route('sites.create') }}?client_id={{ $client->id }}" class="btn btn-primary">
            <i class="bi bi-plus me-2"></i>Create First Site
        </a>
    </div>
@endif

<style>
.site-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: all 0.2s ease;
}

.site-card:hover {
    border-color: #d1d5db;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.site-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.site-info {
    flex: 1;
}

.site-name {
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.site-name a {
    color: #1f2937;
}

.site-name a:hover {
    color: #4f46e5;
}

.site-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.site-progress {
    flex-shrink: 0;
    margin-left: 1rem;
}

.progress-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: conic-gradient(#4f46e5 0deg, #4f46e5 calc(var(--progress, 0) * 3.6deg), #e5e7eb calc(var(--progress, 0) * 3.6deg));
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.progress-circle::before {
    content: '';
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: white;
    position: absolute;
}

.progress-text {
    font-size: 0.75rem;
    font-weight: 600;
    color: #4f46e5;
    z-index: 1;
}

.site-description {
    color: #6b7280;
    font-size: 0.875rem;
    line-height: 1.5;
    margin-bottom: 1rem;
    flex: 1;
}

.site-stats {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #f3f4f6;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.stat-item i {
    width: 16px;
}

.site-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set CSS custom properties for progress circles
    document.querySelectorAll('.progress-circle').forEach(circle => {
        const progress = circle.getAttribute('data-progress');
        circle.style.setProperty('--progress', progress);
    });
});
</script>


