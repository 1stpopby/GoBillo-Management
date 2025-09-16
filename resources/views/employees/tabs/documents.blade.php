<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Documents</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
        <i class="bi bi-plus-circle me-1"></i>Upload Document
    </button>
</div>

@if($documents->count() > 0)
    <div class="row">
        @foreach($documents as $document)
            <div class="col-lg-6 col-xl-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                @if($document->isImage())
                                    <i class="bi bi-image text-success fs-4 me-2"></i>
                                @else
                                    <i class="bi bi-file-earmark text-primary fs-4 me-2"></i>
                                @endif
                                <div>
                                    <h6 class="mb-1">{{ $document->name }}</h6>
                                    <small class="text-muted">{{ $document->extension }} • {{ $document->formatted_file_size }}</small>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" type="button" 
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ asset('storage/' . $document->file_path) }}" target="_blank">
                                        <i class="bi bi-eye me-2"></i>View
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ asset('storage/' . $document->file_path) }}" download>
                                        <i class="bi bi-download me-2"></i>Download
                                    </a></li>
                                    @if(auth()->user()->canManageCompanyUsers())
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" 
                                               onclick="deleteDocument({{ $document->id }})">
                                            <i class="bi bi-trash me-2"></i>Delete
                                        </a></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <span class="badge bg-{{ $document->category === 'contract' ? 'success' : ($document->category === 'photo' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($document->category) }}
                            </span>
                        </div>
                        
                        @if($document->description)
                            <p class="text-muted small mb-2">{{ $document->description }}</p>
                        @endif
                        
                        <div class="text-muted small">
                            @if($document->project)
                                <div><i class="bi bi-folder me-1"></i>{{ $document->project->name }}</div>
                            @endif
                            @if($document->task)
                                <div><i class="bi bi-check-circle me-1"></i>{{ $document->task->title }}</div>
                            @endif
                            <div><i class="bi bi-calendar me-1"></i>{{ $document->created_at->format('M j, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <div class="text-center mt-4">
        <a href="{{ route('documents.index') }}?uploaded_by={{ $employee->user_id }}" class="btn btn-outline-primary">
            View All Documents
        </a>
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-file-earmark display-1 text-muted"></i>
        <h5 class="mt-3">No Documents</h5>
        <p class="text-muted">This employee hasn't uploaded any documents yet.</p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
            <i class="bi bi-plus-circle me-2"></i>Upload First Document
        </button>
    </div>
@endif

<!-- Upload Document Modal -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="file" name="file" required>
                        <div class="form-text">Max file size: 10MB. Supported formats: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, etc.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="other">Other</option>
                            <option value="contract">Contract</option>
                            <option value="permit">Permit</option>
                            <option value="blueprint">Blueprint</option>
                            <option value="photo">Photo</option>
                            <option value="report">Report</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" 
                                  placeholder="Optional description of the document"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload Document</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteDocument(documentId) {
    if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
        fetch(`/documents/${documentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting document: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the document.');
        });
    }
}
</script>


