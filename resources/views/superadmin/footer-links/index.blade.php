@extends('layouts.superadmin')

@section('title', 'Footer Links Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Footer Links Management</h1>
                    <p class="text-muted">Manage all footer navigation links and pages</p>
                </div>
                <div class="d-flex gap-2">
                    <form action="{{ route('superadmin.footer-links.initialize') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary" onclick="return confirm('This will create/update default footer links. Continue?')">
                            <i class="bi bi-arrow-clockwise me-1"></i>Initialize Defaults
                        </button>
                    </form>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLinkModal">
                        <i class="bi bi-plus-circle me-1"></i>Add New Link
                    </button>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                @forelse($sections as $sectionKey => $sectionName)
                <div class="col-lg-6 col-xl-3 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-{{ $sectionKey === 'product' ? 'box' : ($sectionKey === 'company' ? 'building' : ($sectionKey === 'support' ? 'life-preserver' : 'shield-check')) }} me-2"></i>
                                {{ $sectionName }}
                            </h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addLinkToSection('{{ $sectionKey }}')">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush" id="section-{{ $sectionKey }}">
                                @if(isset($links[$sectionKey]))
                                    @foreach($links[$sectionKey] as $link)
                                    <div class="list-group-item d-flex justify-content-between align-items-center" data-link-id="{{ $link->id }}">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-grip-vertical text-muted me-2 drag-handle" style="cursor: move;"></i>
                                                <div>
                                                    <div class="fw-semibold">{{ $link->title }}</div>
                                                    <small class="text-muted">
                                                        {{ $link->url }}
                                                        @if($link->target === '_blank')
                                                            <i class="bi bi-box-arrow-up-right ms-1"></i>
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-1">
                                            @if(!$link->is_active)
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="editLink({{ $link->id }}, '{{ $link->title }}', '{{ $link->url }}', '{{ $link->section }}', '{{ $link->target }}', {{ $link->sort_order }}, {{ $link->is_active ? 'true' : 'false' }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('superadmin.footer-links.destroy', $link) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this link?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="list-group-item text-center text-muted py-4">
                                        <i class="bi bi-link-45deg display-4 mb-2"></i>
                                        <p class="mb-0">No links in this section</p>
                                        <small>Click the + button to add links</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-link-45deg display-1 text-muted mb-3"></i>
                        <h4 class="text-muted">No Footer Links</h4>
                        <p class="text-muted mb-4">Initialize default links to get started.</p>
                        <form action="{{ route('superadmin.footer-links.initialize') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>Initialize Default Links
                            </button>
                        </form>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Preview Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-eye me-2"></i>Preview Links
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('get-started') }}#footer" target="_blank" class="btn btn-outline-primary w-100 mb-2">
                                <i class="bi bi-layout-text-window me-2"></i>View Footer on Get Started Page
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('welcome') }}#footer" target="_blank" class="btn btn-outline-primary w-100 mb-2">
                                <i class="bi bi-house me-2"></i>View Footer on Landing Page
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Link Modal -->
<div class="modal fade" id="addLinkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('superadmin.footer-links.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Footer Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_title" class="form-label">Link Title *</label>
                        <input type="text" class="form-control" id="add_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="add_url" class="form-label">URL *</label>
                        <input type="text" class="form-control" id="add_url" name="url" placeholder="https://example.com or /page" required>
                    </div>
                    <div class="mb-3">
                        <label for="add_section" class="form-label">Section *</label>
                        <select class="form-select" id="add_section" name="section" required>
                            @foreach($sections as $key => $name)
                                <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="add_target" class="form-label">Link Target</label>
                        <select class="form-select" id="add_target" name="target">
                            <option value="_self">Same Window</option>
                            <option value="_blank">New Window/Tab</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="add_sort_order" class="form-label">Sort Order</label>
                        <input type="number" class="form-control" id="add_sort_order" name="sort_order" value="0" min="0">
                        <small class="form-text text-muted">Lower numbers appear first</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Link</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Link Modal -->
<div class="modal fade" id="editLinkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editLinkForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Footer Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Link Title *</label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_url" class="form-label">URL *</label>
                        <input type="text" class="form-control" id="edit_url" name="url" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_section" class="form-label">Section *</label>
                        <select class="form-select" id="edit_section" name="section" required>
                            @foreach($sections as $key => $name)
                                <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_target" class="form-label">Link Target</label>
                        <select class="form-select" id="edit_target" name="target">
                            <option value="_self">Same Window</option>
                            <option value="_blank">New Window/Tab</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_sort_order" class="form-label">Sort Order</label>
                        <input type="number" class="form-control" id="edit_sort_order" name="sort_order" min="0">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                            <label class="form-check-label" for="edit_is_active">
                                Active (visible on website)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Link</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function addLinkToSection(section) {
        document.getElementById('add_section').value = section;
        new bootstrap.Modal(document.getElementById('addLinkModal')).show();
    }

    function editLink(id, title, url, section, target, sortOrder, isActive) {
        document.getElementById('editLinkForm').action = `/superadmin/footer-links/${id}`;
        document.getElementById('edit_title').value = title;
        document.getElementById('edit_url').value = url;
        document.getElementById('edit_section').value = section;
        document.getElementById('edit_target').value = target;
        document.getElementById('edit_sort_order').value = sortOrder;
        document.getElementById('edit_is_active').checked = isActive;
        
        new bootstrap.Modal(document.getElementById('editLinkModal')).show();
    }

    // Drag and drop functionality (basic implementation)
    document.addEventListener('DOMContentLoaded', function() {
        const sections = ['product', 'company', 'support', 'legal'];
        
        sections.forEach(section => {
            const sectionElement = document.getElementById(`section-${section}`);
            if (sectionElement) {
                new Sortable(sectionElement, {
                    handle: '.drag-handle',
                    animation: 150,
                    onEnd: function(evt) {
                        updateSortOrder(section);
                    }
                });
            }
        });
    });

    function updateSortOrder(section) {
        const sectionElement = document.getElementById(`section-${section}`);
        const items = sectionElement.querySelectorAll('[data-link-id]');
        const updates = [];
        
        items.forEach((item, index) => {
            const linkId = item.getAttribute('data-link-id');
            updates.push({
                id: linkId,
                sort_order: index
            });
        });

        fetch('/superadmin/footer-links/update-order', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                links: updates
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                console.log('Order updated successfully');
            }
        })
        .catch(error => {
            console.error('Error updating order:', error);
        });
    }
</script>

<!-- Include SortableJS for drag and drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
@endpush

@push('styles')
<style>
    .drag-handle:hover {
        color: #0d6efd !important;
    }
    
    .sortable-ghost {
        opacity: 0.4;
    }
    
    .list-group-item {
        border-left: none;
        border-right: none;
    }
    
    .list-group-item:first-child {
        border-top: none;
    }
    
    .list-group-item:last-child {
        border-bottom: none;
    }
</style>
@endpush
@endsection
