@extends('layouts.app')

@section('title', 'Upload Document')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Upload Document</h1>
                    <p class="text-muted">Add a new document to the system</p>
                </div>
                <div>
                    <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Documents
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
                    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Document Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
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
                                        <option value="blueprint" {{ old('category') == 'blueprint' ? 'selected' : '' }}>Blueprint</option>
                                        <option value="contract" {{ old('category') == 'contract' ? 'selected' : '' }}>Contract</option>
                                        <option value="permit" {{ old('category') == 'permit' ? 'selected' : '' }}>Permit</option>
                                        <option value="photo" {{ old('category') == 'photo' ? 'selected' : '' }}>Photo</option>
                                        <option value="report" {{ old('category') == 'report' ? 'selected' : '' }}>Report</option>
                                        <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="project_id" class="form-label">Project</label>
                                    <select class="form-select @error('project_id') is-invalid @enderror" id="project_id" name="project_id">
                                        <option value="">Select project (optional)...</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                                {{ $project->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Leave blank if document is not project-specific</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="file" class="form-label">File <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                           id="file" name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.txt,.zip,.rar">
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Maximum file size: 10MB. Supported formats: PDF, DOC, XLS, images, ZIP, etc.</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Brief description of the document...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Upload Guidelines:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Choose a descriptive name for easy identification</li>
                                    <li>Select the appropriate category for better organization</li>
                                    <li>Associate with a project if relevant</li>
                                    <li>Maximum file size is 10MB</li>
                                </ul>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-cloud-upload me-2"></i>Upload Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Auto-populate document name from file name
document.getElementById('file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file && !document.getElementById('name').value) {
        // Remove file extension and clean up the name
        let fileName = file.name.replace(/\.[^/.]+$/, "");
        fileName = fileName.replace(/[_-]/g, ' ');
        fileName = fileName.replace(/\b\w/g, l => l.toUpperCase());
        document.getElementById('name').value = fileName;
    }
});

// File size validation
document.getElementById('file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const maxSize = 10 * 1024 * 1024; // 10MB in bytes
    
    if (file && file.size > maxSize) {
        alert('File size exceeds 10MB limit. Please choose a smaller file.');
        this.value = '';
        return;
    }
    
    // Show file info
    if (file) {
        const fileSize = (file.size / (1024 * 1024)).toFixed(2);
        console.log(`Selected file: ${file.name} (${fileSize} MB)`);
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const category = document.getElementById('category').value;
    const file = document.getElementById('file').files[0];
    
    if (!name || !category || !file) {
        e.preventDefault();
        alert('Please fill in all required fields (Name, Category, and File).');
        return false;
    }
});
</script>
@endsection


