@extends('layouts.superadmin')

@section('title', 'Pages Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Pages Management</h1>
                    <p class="text-muted">Create and manage website pages like Privacy Policy, Terms, About Us, etc.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('superadmin.pages.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Create New Page
                    </a>
                    <form method="POST" action="{{ route('superadmin.pages.initialize') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-success" onclick="return confirm('This will create default pages (Privacy Policy, Terms, etc.). Continue?')">
                            <i class="bi bi-magic me-1"></i>Create Default Pages
                        </button>
                    </form>
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

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search pages...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">All Pages</option>
                                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="bi bi-search me-1"></i>Filter
                            </button>
                            <a href="{{ route('superadmin.pages.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Pages Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark-text me-2"></i>Website Pages
                        <span class="badge bg-secondary ms-2">{{ $pages->total() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($pages->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Title</th>
                                        <th>URL</th>
                                        <th>Template</th>
                                        <th>Status</th>
                                        <th>Footer</th>
                                        <th>Updated</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pages as $page)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <h6 class="mb-0">{{ $page->title }}</h6>
                                                        @if($page->excerpt)
                                                            <small class="text-muted">{{ Str::limit($page->excerpt, 60) }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <code>/page/{{ $page->slug }}</code>
                                                <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="ms-2">
                                                    <i class="bi bi-box-arrow-up-right small"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ ucfirst($page->template) }}</span>
                                            </td>
                                            <td>
                                                @if($page->is_published)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>Published
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-clock me-1"></i>Draft
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($page->show_in_footer)
                                                    <span class="badge bg-primary">
                                                        <i class="bi bi-link me-1"></i>{{ ucfirst($page->footer_section ?? 'Footer') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $page->updated_at->format('M j, Y') }}<br>
                                                    {{ $page->updated_at->format('g:i A') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('superadmin.pages.show', $page) }}" class="btn btn-outline-info" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('superadmin.pages.edit', $page) }}" class="btn btn-outline-primary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('superadmin.pages.destroy', $page) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this page?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-file-earmark-text display-1 text-muted mb-3"></i>
                            <h5 class="text-muted">No Pages Found</h5>
                            <p class="text-muted mb-4">Get started by creating your first page or initializing default pages.</p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('superadmin.pages.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i>Create New Page
                                </a>
                                <form method="POST" action="{{ route('superadmin.pages.initialize') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success">
                                        <i class="bi bi-magic me-1"></i>Create Default Pages
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>

                @if($pages->hasPages())
                    <div class="card-footer">
                        {{ $pages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.badge {
    font-size: 0.75rem;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.table-responsive {
    border-radius: 0.375rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}
</style>
@endsection
