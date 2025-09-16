@extends('layouts.app')

@section('title', 'Documents')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Documents</h1>
    <a href="{{ route('documents.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Upload Document
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('documents.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Search documents...">
            </div>
            <div class="col-md-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category">
                    <option value="">All Categories</option>
                    <option value="blueprint" {{ request('category') == 'blueprint' ? 'selected' : '' }}>Blueprint</option>
                    <option value="contract" {{ request('category') == 'contract' ? 'selected' : '' }}>Contract</option>
                    <option value="permit" {{ request('category') == 'permit' ? 'selected' : '' }}>Permit</option>
                    <option value="photo" {{ request('category') == 'photo' ? 'selected' : '' }}>Photo</option>
                    <option value="report" {{ request('category') == 'report' ? 'selected' : '' }}>Report</option>
                    <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="col-md-3">
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
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Documents List -->
@if($documents->count() > 0)
    <div class="row">
        @foreach($documents as $document)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3">
                                @if($document->isImage())
                                    <i class="bi bi-image text-primary" style="font-size: 2rem;"></i>
                                @else
                                    <i class="bi bi-file-earmark text-secondary" style="font-size: 2rem;"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="card-title mb-1">{{ Str::limit($document->original_name, 25) }}</h6>
                                <span class="badge bg-info">{{ ucfirst($document->category) }}</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">
                                <i class="bi bi-folder me-1"></i> 
                                @if($document->project)
                                    <a href="{{ route('projects.show', $document->project) }}" class="text-decoration-none">
                                        {{ $document->project->name }}
                                    </a>
                                @else
                                    <span class="text-muted">No project assigned</span>
                                @endif
                            </small>
                            <small class="text-muted d-block">
                                <i class="bi bi-person me-1"></i> {{ $document->uploadedBy->name }}
                            </small>
                            <small class="text-muted d-block">
                                <i class="bi bi-calendar me-1"></i> {{ $document->created_at->format('M j, Y') }}
                            </small>
                            <small class="text-muted d-block">
                                <i class="bi bi-file-earmark me-1"></i> {{ $document->formatted_file_size }}
                            </small>
                        </div>

                        @if($document->description)
                            <p class="card-text small text-muted">{{ Str::limit($document->description, 80) }}</p>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="btn-group w-100">
                            <a href="{{ route('documents.show', $document) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <a href="{{ route('documents.download', $document) }}" class="btn btn-outline-success btn-sm">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
            Showing {{ $documents->firstItem() }} to {{ $documents->lastItem() }} of {{ $documents->total() }} documents
        </div>
        {{ $documents->withQueryString()->links() }}
    </div>
@else
    <div class="card">
        <div class="card-body">
            <div class="text-center py-5">
                <i class="bi bi-file-earmark text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 text-muted">No documents found</h5>
                <p class="text-muted">
                    @if(request()->hasAny(['search', 'category', 'project_id']))
                        Try adjusting your filters or 
                        <a href="{{ route('documents.index') }}">clear all filters</a>
                    @else
                        Upload your first document to get started
                    @endif
                </p>
                <a href="{{ route('documents.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Upload First Document
                </a>
            </div>
        </div>
    </div>
@endif
@endsection 