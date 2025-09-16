@extends('layouts.app')

@section('title', 'Snagging Item Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Snagging Item {{ $snagging->item_number }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a></li>
                            <li class="breadcrumb-item active">Snagging Details</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    @if(auth()->user()->canManageProjects())
                        <a href="{{ route('project.snagging.edit', ['project' => $project, 'snagging' => $snagging]) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-2"></i>Edit
                        </a>
                        @if($snagging->status === 'open')
                            <button class="btn btn-success ms-2" onclick="resolveSnagging()">
                                <i class="bi bi-check me-2"></i>Resolve
                            </button>
                        @endif
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $snagging->title }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <h6>Description</h6>
                                <p class="text-muted">{{ $snagging->description }}</p>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6>Location</h6>
                                    <p>{{ $snagging->location }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Category</h6>
                                    <p>
                                        <i class="{{ $snagging->category_icon }} me-2"></i>
                                        {{ ucfirst($snagging->category) }}
                                    </p>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6>Severity</h6>
                                    <p>
                                        <span class="badge bg-{{ $snagging->severity_color }} fs-6">
                                            {{ ucfirst($snagging->severity) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Status</h6>
                                    <p>
                                        <span class="badge bg-{{ $snagging->status_color }} fs-6">
                                            {{ ucfirst(str_replace('_', ' ', $snagging->status)) }}
                                        </span>
                                        @if($snagging->is_overdue)
                                            <br><small class="text-danger">⚠️ Overdue</small>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            @if($snagging->cost_to_fix)
                                <div class="mb-4">
                                    <h6>Estimated Cost to Fix</h6>
                                    <p class="h5 text-warning">{{ $snagging->formatted_cost_to_fix }}</p>
                                </div>
                            @endif

                            @if($snagging->trade_responsible)
                                <div class="mb-4">
                                    <h6>Trade Responsible</h6>
                                    <p>{{ $snagging->trade_responsible }}</p>
                                </div>
                            @endif

                            @if($snagging->resolution_notes)
                                <div class="mb-4">
                                    <h6>Resolution Notes</h6>
                                    <p class="text-muted">{{ $snagging->resolution_notes }}</p>
                                </div>
                            @endif

                            @if($snagging->photos_before && count($snagging->photos_before) > 0)
                                <div class="mb-4">
                                    <h6>Photos (Before)</h6>
                                    <div class="row g-2">
                                        @foreach($snagging->photos_before as $photo)
                                            <div class="col-md-3">
                                                <img src="{{ Storage::url($photo) }}" alt="Before photo" class="img-fluid rounded">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($snagging->photos_after && count($snagging->photos_after) > 0)
                                <div class="mb-4">
                                    <h6>Photos (After)</h6>
                                    <div class="row g-2">
                                        @foreach($snagging->photos_after as $photo)
                                            <div class="col-md-3">
                                                <img src="{{ Storage::url($photo) }}" alt="After photo" class="img-fluid rounded">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Snagging Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted">Reported By</small>
                                <p class="mb-0">{{ $snagging->reporter->name }}</p>
                                <small class="text-muted">{{ $snagging->created_at->format('M j, Y g:i A') }}</small>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Identified Date</small>
                                <p class="mb-0">{{ $snagging->identified_date->format('M j, Y') }}</p>
                            </div>

                            @if($snagging->target_completion_date)
                                <div class="mb-3">
                                    <small class="text-muted">Target Completion</small>
                                    <p class="mb-0">{{ $snagging->target_completion_date->format('M j, Y') }}</p>
                                </div>
                            @endif

                            @if($snagging->assignee)
                                <div class="mb-3">
                                    <small class="text-muted">Assigned To</small>
                                    <p class="mb-0">{{ $snagging->assignee->name }}</p>
                                </div>
                            @endif

                            @if($snagging->task)
                                <div class="mb-3">
                                    <small class="text-muted">Related Task</small>
                                    <p class="mb-0">
                                        <a href="{{ route('tasks.show', $snagging->task) }}" class="text-decoration-none">
                                            {{ $snagging->task->title }}
                                        </a>
                                    </p>
                                </div>
                            @endif

                            @if($snagging->resolved_by)
                                <div class="mb-3">
                                    <small class="text-muted">Resolved By</small>
                                    <p class="mb-0">{{ $snagging->resolver->name }}</p>
                                    <small class="text-muted">{{ $snagging->resolved_at->format('M j, Y g:i A') }}</small>
                                </div>
                            @endif

                            @if($snagging->actual_completion_date)
                                <div class="mb-3">
                                    <small class="text-muted">Actual Completion</small>
                                    <p class="mb-0">{{ $snagging->actual_completion_date->format('M j, Y') }}</p>
                                </div>
                            @endif

                            <div class="mb-3">
                                <small class="text-muted">Days Open</small>
                                <p class="mb-0">{{ $snagging->days_open }} days</p>
                            </div>

                            @if($snagging->client_reported)
                                <div class="mb-0">
                                    <small class="text-muted">Source</small>
                                    <p class="mb-0">
                                        <span class="badge bg-info">Client Reported</span>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function resolveSnagging() {
    const notes = prompt('Please enter resolution notes:');
    if (notes) {
        const formData = new FormData();
        formData.append('resolution_notes', notes);
        
        fetch(`/projects/{{ $project->id }}/snagging/{{ $snagging->id }}/resolve`, {
            method: 'PATCH',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error resolving snagging item: ' + (data.message || 'Unknown error'));
            }
        });
    }
}
</script>
@endsection 