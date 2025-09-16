@extends('layouts.app')

@section('title', $document->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ $document->name }}</h1>
                    <p class="text-muted">{{ $document->original_name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Documents
                    </a>
                    <a href="{{ route('documents.download', $document) }}" class="btn btn-outline-primary">
                        <i class="bi bi-download me-2"></i>Download
                    </a>
                    @if(auth()->user()->canManageProjects())
                        <a href="{{ route('documents.edit', $document) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i>Edit
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Document Preview -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Document Preview</h6>
                </div>
                <div class="card-body text-center">
                    @php
                        $fileExtension = strtolower(pathinfo($document->original_name, PATHINFO_EXTENSION));
                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                        $isImage = in_array($fileExtension, $imageExtensions);
                    @endphp

                    @if($isImage)
                        <img src="{{ asset('storage/' . $document->file_path) }}" 
                             alt="{{ $document->name }}" 
                             class="img-fluid rounded shadow"
                             style="max-height: 500px;">
                    @elseif($fileExtension === 'pdf')
                        <div class="pdf-preview">
                            <i class="bi bi-file-pdf text-danger display-1"></i>
                            <h5 class="mt-3">PDF Document</h5>
                            <p class="text-muted">{{ $document->original_name }}</p>
                            <a href="{{ asset('storage/' . $document->file_path) }}" 
                               target="_blank" class="btn btn-outline-primary">
                                <i class="bi bi-eye me-2"></i>View PDF
                            </a>
                        </div>
                    @else
                        <div class="file-preview">
                            @switch($fileExtension)
                                @case('doc')
                                @case('docx')
                                    <i class="bi bi-file-word text-primary display-1"></i>
                                    @break
                                @case('xls')
                                @case('xlsx')
                                    <i class="bi bi-file-excel text-success display-1"></i>
                                    @break
                                @case('ppt')
                                @case('pptx')
                                    <i class="bi bi-file-ppt text-warning display-1"></i>
                                    @break
                                @case('zip')
                                @case('rar')
                                @case('7z')
                                    <i class="bi bi-file-zip text-info display-1"></i>
                                    @break
                                @case('txt')
                                    <i class="bi bi-file-text text-secondary display-1"></i>
                                    @break
                                @default
                                    <i class="bi bi-file-earmark text-muted display-1"></i>
                            @endswitch
                            <h5 class="mt-3">{{ strtoupper($fileExtension) }} File</h5>
                            <p class="text-muted">{{ $document->original_name }}</p>
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('documents.download', $document) }}" class="btn btn-primary">
                            <i class="bi bi-download me-2"></i>Download File
                        </a>
                    </div>
                </div>
            </div>

            <!-- Description -->
            @if($document->description)
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Description</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $document->description }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Document Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Document Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label text-muted">Category</label>
                            <div class="fw-bold">
                                <span class="badge bg-info">{{ ucfirst($document->category) }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Project</label>
                            <div class="fw-bold">
                                @if($document->project)
                                    <a href="{{ route('projects.show', $document->project) }}" class="text-decoration-none">
                                        {{ $document->project->name }}
                                    </a>
                                @else
                                    <span class="text-muted">No project assigned</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">File Size</label>
                            <div class="fw-bold">{{ number_format($document->file_size / 1024 / 1024, 2) }} MB</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">File Type</label>
                            <div class="fw-bold">{{ $document->mime_type }}</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Uploaded By</label>
                            <div class="fw-bold">{{ $document->uploadedBy->name }}</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Upload Date</label>
                            <div class="fw-bold">{{ $document->created_at->format('M j, Y \a\t g:i A') }}</div>
                        </div>
                        @if($document->updated_at != $document->created_at)
                            <div class="col-12">
                                <label class="form-label text-muted">Last Modified</label>
                                <div class="fw-bold">{{ $document->updated_at->format('M j, Y \a\t g:i A') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('documents.download', $document) }}" class="btn btn-outline-primary">
                            <i class="bi bi-download me-2"></i>Download File
                        </a>
                        @if(auth()->user()->canManageProjects())
                            <a href="{{ route('documents.edit', $document) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-pencil me-2"></i>Edit Details
                            </a>
                            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                <i class="bi bi-trash me-2"></i>Delete Document
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
        // Create a form to submit DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("documents.destroy", $document) }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
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
</script>
@endsection


