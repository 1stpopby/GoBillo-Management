@extends('layouts.app')

@section('title', 'Document Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Document Details</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a></li>
                            <li class="breadcrumb-item active">Document Details</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('project.documents.download', ['project' => $project, 'document' => $document]) }}" class="btn btn-primary">
                        <i class="bi bi-download me-2"></i>Download
                    </a>
                    @if(auth()->user()->canManageProjects())
                        <a href="{{ route('project.documents.edit', ['project' => $project, 'document' => $document]) }}" class="btn btn-outline-primary ms-2">
                            <i class="bi bi-pencil me-2"></i>Edit
                        </a>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <i class="{{ $document->file_type_icon }} fs-2 me-3 text-primary"></i>
                                <div>
                                    <h5 class="card-title mb-0">{{ $document->title }}</h5>
                                    <small class="text-muted">{{ $document->original_filename }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($document->description)
                                <div class="mb-4">
                                    <h6>Description</h6>
                                    <p class="text-muted">{{ $document->description }}</p>
                                </div>
                            @endif

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6>Category</h6>
                                    <p>
                                        <i class="{{ $document->category_icon }} me-2"></i>
                                        {{ ucfirst($document->category) }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6>File Size</h6>
                                    <p>{{ $document->formatted_file_size }}</p>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6>File Type</h6>
                                    <p>{{ $document->mime_type }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Version</h6>
                                    <p>{{ $document->version }}</p>
                                </div>
                            </div>

                            @if($document->tags && count($document->tags) > 0)
                                <div class="mb-4">
                                    <h6>Tags</h6>
                                    <div>
                                        @foreach($document->tags as $tag)
                                            <span class="badge bg-secondary me-1">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6>Client Visibility</h6>
                                    <p>
                                        @if($document->is_public)
                                            <span class="badge bg-success">
                                                <i class="bi bi-eye me-1"></i>Visible to client
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-eye-slash me-1"></i>Internal only
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            @if($document->is_image)
                                <div class="mb-4">
                                    <h6>Preview</h6>
                                    <img src="{{ Storage::url($document->file_path) }}" alt="{{ $document->title }}" class="img-fluid rounded" style="max-height: 400px;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Document Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted">Uploaded By</small>
                                <p class="mb-0">{{ $document->uploader->name }}</p>
                                <small class="text-muted">{{ $document->created_at->format('M j, Y g:i A') }}</small>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Project</small>
                                <p class="mb-0">
                                    <a href="{{ route('projects.show', $project) }}" class="text-decoration-none">
                                        {{ $project->name }}
                                    </a>
                                </p>
                            </div>

                            @if($document->parent_document_id)
                                <div class="mb-3">
                                    <small class="text-muted">Parent Document</small>
                                    <p class="mb-0">
                                        <a href="{{ route('project.documents.show', ['project' => $project, 'document' => $document->parent_document_id]) }}" class="text-decoration-none">
                                            View Previous Version
                                        </a>
                                    </p>
                                </div>
                            @endif

                            @if($document->childDocuments->count() > 0)
                                <div class="mb-3">
                                    <small class="text-muted">Newer Versions</small>
                                    @foreach($document->childDocuments as $child)
                                        <p class="mb-1">
                                            <a href="{{ route('project.documents.show', ['project' => $project, 'document' => $child->id]) }}" class="text-decoration-none">
                                                Version {{ $child->version }}
                                            </a>
                                            <small class="text-muted d-block">{{ $child->created_at->format('M j, Y') }}</small>
                                        </p>
                                    @endforeach
                                </div>
                            @endif

                            @if($document->updated_at != $document->created_at)
                                <div class="mb-0">
                                    <small class="text-muted">Last Updated</small>
                                    <p class="mb-0">{{ $document->updated_at->format('M j, Y g:i A') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if(auth()->user()->canManageProjects())
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Actions</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-primary" onclick="uploadNewVersion()">
                                        <i class="bi bi-arrow-up-circle me-2"></i>Upload New Version
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteDocument()">
                                        <i class="bi bi-trash me-2"></i>Delete Document
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function uploadNewVersion() {
    // Implementation for uploading new version
    alert('Upload new version functionality will be implemented');
}

function deleteDocument() {
    if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
        fetch(`/projects/{{ $project->id }}/documents/{{ $document->id }}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("projects.show", $project) }}';
            } else {
                alert('Error deleting document: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting document');
        });
    }
}
</script>
@endsection 