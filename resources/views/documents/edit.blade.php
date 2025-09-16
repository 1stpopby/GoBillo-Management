@extends('layouts.app')

@section('title', 'Edit Document - ' . $document->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Edit Document</h1>
                    <p class="text-muted">Update document information</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('documents.show', $document) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Document
                    </a>
                    <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-list me-2"></i>All Documents
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Document Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('documents.update', $document) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Document Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $document->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                        <option value="">Select category...</option>
                                        <option value="blueprint" {{ old('category', $document->category) == 'blueprint' ? 'selected' : '' }}>Blueprint</option>
                                        <option value="contract" {{ old('category', $document->category) == 'contract' ? 'selected' : '' }}>Contract</option>
                                        <option value="permit" {{ old('category', $document->category) == 'permit' ? 'selected' : '' }}>Permit</option>
                                        <option value="photo" {{ old('category', $document->category) == 'photo' ? 'selected' : '' }}>Photo</option>
                                        <option value="report" {{ old('category', $document->category) == 'report' ? 'selected' : '' }}>Report</option>
                                        <option value="other" {{ old('category', $document->category) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="project_id" class="form-label">Project</label>
                            <select class="form-select @error('project_id') is-invalid @enderror" id="project_id" name="project_id">
                                <option value="">Select project (optional)...</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id', $document->project_id) == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Leave blank if document is not project-specific</div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Brief description of the document...">{{ old('description', $document->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Note:</strong> You can only update the document's information. To replace the file, you'll need to upload a new document.
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('documents.show', $document) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Current File Info -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Current File</h6>
                </div>
                <div class="card-body">
                    @php
                        $fileExtension = strtolower(pathinfo($document->original_name, PATHINFO_EXTENSION));
                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                        $isImage = in_array($fileExtension, $imageExtensions);
                    @endphp

                    <div class="text-center">
                        @if($isImage)
                            <img src="{{ asset('storage/' . $document->file_path) }}" 
                                 alt="{{ $document->name }}" 
                                 class="img-fluid rounded shadow mb-3"
                                 style="max-height: 200px;">
                        @else
                            <div class="file-icon mb-3">
                                @switch($fileExtension)
                                    @case('pdf')
                                        <i class="bi bi-file-pdf text-danger display-4"></i>
                                        @break
                                    @case('doc')
                                    @case('docx')
                                        <i class="bi bi-file-word text-primary display-4"></i>
                                        @break
                                    @case('xls')
                                    @case('xlsx')
                                        <i class="bi bi-file-excel text-success display-4"></i>
                                        @break
                                    @case('ppt')
                                    @case('pptx')
                                        <i class="bi bi-file-ppt text-warning display-4"></i>
                                        @break
                                    @case('zip')
                                    @case('rar')
                                    @case('7z')
                                        <i class="bi bi-file-zip text-info display-4"></i>
                                        @break
                                    @case('txt')
                                        <i class="bi bi-file-text text-secondary display-4"></i>
                                        @break
                                    @default
                                        <i class="bi bi-file-earmark text-muted display-4"></i>
                                @endswitch
                            </div>
                        @endif

                        <h6 class="mb-1">{{ $document->original_name }}</h6>
                        <small class="text-muted d-block">{{ number_format($document->file_size / 1024 / 1024, 2) }} MB</small>
                        <small class="text-muted d-block">{{ $document->mime_type }}</small>
                        
                        <div class="mt-3">
                            <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download me-1"></i>Download
                            </a>
                        </div>
                    </div>

                    <hr>

                    <div class="row g-2 small">
                        <div class="col-6">
                            <strong>Uploaded:</strong>
                        </div>
                        <div class="col-6">
                            {{ $document->created_at->format('M j, Y') }}
                        </div>
                        <div class="col-6">
                            <strong>By:</strong>
                        </div>
                        <div class="col-6">
                            {{ $document->uploadedBy->name }}
                        </div>
                        @if($document->updated_at != $document->created_at)
                            <div class="col-6">
                                <strong>Modified:</strong>
                            </div>
                            <div class="col-6">
                                {{ $document->updated_at->format('M j, Y') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


