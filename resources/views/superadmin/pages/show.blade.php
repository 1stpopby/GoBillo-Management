@extends('layouts.superadmin')

@section('title', 'View Page - ' . $page->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ $page->title }}</h1>
                    <p class="text-muted">
                        <span class="badge bg-{{ $page->is_published ? 'success' : 'secondary' }} me-2">
                            {{ $page->is_published ? 'Published' : 'Draft' }}
                        </span>
                        <span class="badge bg-info me-2">{{ ucfirst($page->template) }}</span>
                        @if($page->show_in_footer)
                            <span class="badge bg-primary">Footer: {{ ucfirst($page->footer_section) }}</span>
                        @endif
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="btn btn-outline-info">
                        <i class="bi bi-eye me-1"></i>View Live Page
                    </a>
                    <a href="{{ route('superadmin.pages.edit', $page) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i>Edit Page
                    </a>
                    <a href="{{ route('superadmin.pages.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Pages
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Page Content Preview -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-file-earmark-text me-2"></i>Page Content
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($page->excerpt)
                                <div class="alert alert-light border">
                                    <h6 class="mb-1">Page Excerpt:</h6>
                                    <p class="mb-0 text-muted">{{ $page->excerpt }}</p>
                                </div>
                            @endif
                            
                            <div class="content-preview">
                                {!! $page->content !!}
                            </div>
                        </div>
                    </div>

                    <!-- SEO Information -->
                    @if($page->meta_title || $page->meta_description || $page->meta_keywords)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-search me-2"></i>SEO Information
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($page->meta_title)
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-1">Meta Title:</h6>
                                        <p class="mb-0">{{ $page->meta_title }}</p>
                                    </div>
                                @endif
                                
                                @if($page->meta_description)
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-1">Meta Description:</h6>
                                        <p class="mb-0">{{ $page->meta_description }}</p>
                                    </div>
                                @endif
                                
                                @if($page->meta_keywords)
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-1">Meta Keywords:</h6>
                                        <p class="mb-0">{{ $page->meta_keywords }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <!-- Page Details -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-info-circle me-2"></i>Page Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="text-muted mb-1">URL:</h6>
                                <div class="d-flex align-items-center">
                                    <code class="me-2">/page/{{ $page->slug }}</code>
                                    <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h6 class="text-muted mb-1">Template:</h6>
                                <span class="badge bg-info">{{ ucfirst($page->template) }}</span>
                            </div>

                            <div class="mb-3">
                                <h6 class="text-muted mb-1">Status:</h6>
                                @if($page->is_published)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Published
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-clock me-1"></i>Draft
                                    </span>
                                @endif
                            </div>

                            @if($page->show_in_footer)
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Footer Section:</h6>
                                    <span class="badge bg-primary">{{ ucfirst($page->footer_section) }}</span>
                                </div>

                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Sort Order:</h6>
                                    <span class="fw-medium">{{ $page->sort_order }}</span>
                                </div>
                            @endif

                            <div class="mb-3">
                                <h6 class="text-muted mb-1">Created:</h6>
                                <span class="fw-medium">{{ $page->created_at->format('M j, Y g:i A') }}</span>
                            </div>

                            <div class="mb-3">
                                <h6 class="text-muted mb-1">Last Updated:</h6>
                                <span class="fw-medium">{{ $page->updated_at->format('M j, Y g:i A') }}</span>
                            </div>

                            @if($page->published_at)
                                <div class="mb-3">
                                    <h6 class="text-muted mb-1">Published:</h6>
                                    <span class="fw-medium">{{ $page->published_at->format('M j, Y g:i A') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-lightning me-2"></i>Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('superadmin.pages.edit', $page) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil me-1"></i>Edit Page
                                </a>
                                
                                <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="btn btn-outline-info">
                                    <i class="bi bi-eye me-1"></i>View Live Page
                                </a>

                                @if($page->is_published)
                                    <form method="POST" action="{{ route('superadmin.pages.update', $page) }}" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="title" value="{{ $page->title }}">
                                        <input type="hidden" name="content" value="{{ $page->content }}">
                                        <button type="submit" class="btn btn-outline-warning w-100">
                                            <i class="bi bi-eye-slash me-1"></i>Unpublish
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('superadmin.pages.update', $page) }}" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="title" value="{{ $page->title }}">
                                        <input type="hidden" name="content" value="{{ $page->content }}">
                                        <input type="hidden" name="is_published" value="1">
                                        <button type="submit" class="btn btn-outline-success w-100">
                                            <i class="bi bi-check-circle me-1"></i>Publish
                                        </button>
                                    </form>
                                @endif

                                <hr>

                                <form method="POST" action="{{ route('superadmin.pages.destroy', $page) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this page? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="bi bi-trash me-1"></i>Delete Page
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.content-preview {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 2rem;
    background: #f8f9fa;
    max-height: 600px;
    overflow-y: auto;
}

.content-preview h1, .content-preview h2, .content-preview h3, 
.content-preview h4, .content-preview h5, .content-preview h6 {
    color: #2c3e50;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
}

.content-preview h1:first-child, .content-preview h2:first-child, 
.content-preview h3:first-child, .content-preview h4:first-child, 
.content-preview h5:first-child, .content-preview h6:first-child {
    margin-top: 0;
}

.content-preview p {
    margin-bottom: 1rem;
    line-height: 1.6;
}

.content-preview ul, .content-preview ol {
    margin-bottom: 1rem;
    padding-left: 2rem;
}

.content-preview li {
    margin-bottom: 0.25rem;
}

.content-preview strong {
    font-weight: 600;
    color: #2c3e50;
}

.content-preview code {
    background: #e9ecef;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}

.content-preview pre {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 1rem;
    overflow-x: auto;
}

.content-preview blockquote {
    border-left: 4px solid #0066cc;
    padding-left: 1rem;
    margin: 1rem 0;
    color: #6c757d;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.badge {
    font-size: 0.75rem;
}
</style>
@endsection
