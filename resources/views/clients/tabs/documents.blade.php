<div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="text-muted mb-0">Client Documents</h6>
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
        <i class="bi bi-cloud-upload me-1"></i>Upload Document
    </button>
</div>

@php
    // For now, we'll use a placeholder for documents since the Document model relationship might not be set up
    $documents = collect(); // Placeholder - replace with $client->documents when available
@endphp

@if($documents && $documents->count() > 0)
    <div class="row">
        @foreach($documents as $document)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="document-card">
                    <div class="document-icon">
                        <i class="bi bi-{{ $document->icon ?? 'file-earmark' }}"></i>
                    </div>
                    <div class="document-info">
                        <h6 class="document-name">{{ $document->name }}</h6>
                        <div class="document-meta">
                            <span class="document-size">{{ $document->formatted_size ?? '0 KB' }}</span>
                            <span class="document-date">{{ $document->created_at->format('M j, Y') }}</span>
                        </div>
                        <div class="document-category">
                            <span class="badge bg-light text-dark">{{ $document->category ?? 'General' }}</span>
                        </div>
                    </div>
                    <div class="document-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="viewDocument({{ $document->id }})">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="downloadDocument({{ $document->id }})">
                            <i class="bi bi-download"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteDocument({{ $document->id }})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="empty-state-large">
        <div class="empty-icon">
            <i class="bi bi-file-earmark-text"></i>
        </div>
        <h5>No Documents Yet</h5>
        <p class="text-muted">Upload contracts, plans, permits, or other important documents for this client.</p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
            <i class="bi bi-cloud-upload me-2"></i>Upload First Document
        </button>
    </div>
@endif

<!-- Document Categories -->
<div class="document-categories mt-4">
    <h6 class="text-muted mb-3">Document Categories</h6>
    <div class="row">
        <div class="col-md-3">
            <div class="category-card">
                <div class="category-icon bg-primary">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="category-info">
                    <div class="category-name">Contracts</div>
                    <div class="category-count">0 files</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="category-card">
                <div class="category-icon bg-info">
                    <i class="bi bi-blueprint"></i>
                </div>
                <div class="category-info">
                    <div class="category-name">Plans & Drawings</div>
                    <div class="category-count">0 files</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="category-card">
                <div class="category-icon bg-warning">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div class="category-info">
                    <div class="category-name">Permits</div>
                    <div class="category-count">0 files</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="category-card">
                <div class="category-icon bg-success">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="category-info">
                    <div class="category-name">Invoices & Receipts</div>
                    <div class="category-count">0 files</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Document Modal -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="client_id" value="{{ $client->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Document Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category">
                                <option value="general">General</option>
                                <option value="contracts">Contracts</option>
                                <option value="plans">Plans & Drawings</option>
                                <option value="permits">Permits</option>
                                <option value="invoices">Invoices & Receipts</option>
                                <option value="photos">Photos</option>
                                <option value="reports">Reports</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File</label>
                        <input type="file" class="form-control" name="file" required>
                        <div class="form-text">Supported formats: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF (Max: 10MB)</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_private" id="isPrivate">
                                <label class="form-check-label" for="isPrivate">
                                    Private document (only visible to team members)
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="requires_signature" id="requiresSignature">
                                <label class="form-check-label" for="requiresSignature">
                                    Requires client signature
                                </label>
                            </div>
                        </div>
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

<style>
.document-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: all 0.2s ease;
}

.document-card:hover {
    border-color: #d1d5db;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.document-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #6b7280;
    margin-bottom: 1rem;
}

.document-info {
    flex: 1;
    margin-bottom: 1rem;
}

.document-name {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #1f2937;
}

.document-meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.document-category {
    margin-bottom: 0.5rem;
}

.document-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
    margin-top: auto;
}

.category-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.2s ease;
}

.category-card:hover {
    border-color: #d1d5db;
    transform: translateY(-1px);
}

.category-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.category-info {
    flex: 1;
}

.category-name {
    font-weight: 600;
    color: #1f2937;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.category-count {
    font-size: 0.75rem;
    color: #6b7280;
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

<script>
function viewDocument(documentId) {
    // TODO: Implement document viewer
    alert('Document viewer will be implemented');
}

function downloadDocument(documentId) {
    // TODO: Implement document download
    alert('Document download will be implemented');
}

function deleteDocument(documentId) {
    if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
        // TODO: Implement document deletion
        alert('Document deletion will be implemented');
    }
}
</script>


