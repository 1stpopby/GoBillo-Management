@extends('layouts.superadmin')

@section('title', 'Edit Page - ' . $page->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Edit Page</h1>
                    <p class="text-muted">Editing: <strong>{{ $page->title }}</strong></p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="btn btn-outline-info">
                        <i class="bi bi-eye me-1"></i>View Page
                    </a>
                    <a href="{{ route('superadmin.pages.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Pages
                    </a>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <h6><i class="bi bi-exclamation-triangle me-2"></i>Please correct the following errors:</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('superadmin.pages.update', $page) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-file-earmark-text me-2"></i>Page Content
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Page Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $page->title) }}" required>
                                    <div class="form-text">This will be the main heading of your page</div>
                                </div>

                                <div class="mb-3">
                                    <label for="slug" class="form-label">URL Slug</label>
                                    <div class="input-group">
                                        <span class="input-group-text">/page/</span>
                                        <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $page->slug) }}">
                                    </div>
                                    <div class="form-text">Leave blank to auto-generate from title</div>
                                </div>

                                <div class="mb-3">
                                    <label for="excerpt" class="form-label">Page Excerpt</label>
                                    <textarea class="form-control" id="excerpt" name="excerpt" rows="3" maxlength="500">{{ old('excerpt', $page->excerpt) }}</textarea>
                                    <div class="form-text">Short description shown in search results and previews</div>
                                </div>

                                <div class="mb-3">
                                    <label for="content" class="form-label">Page Content <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="content" name="content" rows="15" required>{{ old('content', $page->content) }}</textarea>
                                    <div class="form-text">Full HTML content for your page</div>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Settings -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-search me-2"></i>SEO Settings
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="meta_title" class="form-label">Meta Title</label>
                                    <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" maxlength="255">
                                    <div class="form-text">Leave blank to use page title</div>
                                </div>

                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="300">{{ old('meta_description', $page->meta_description) }}</textarea>
                                    <div class="form-text">Description shown in search results (160-300 characters)</div>
                                </div>

                                <div class="mb-3">
                                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                    <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $page->meta_keywords) }}">
                                    <div class="form-text">Comma-separated keywords for search engines</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Page Info -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-info-circle me-2"></i>Page Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <small class="text-muted">Created:</small><br>
                                    <span class="fw-medium">{{ $page->created_at->format('M j, Y g:i A') }}</span>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Last Updated:</small><br>
                                    <span class="fw-medium">{{ $page->updated_at->format('M j, Y g:i A') }}</span>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Current URL:</small><br>
                                    <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="text-decoration-none">
                                        /page/{{ $page->slug }} <i class="bi bi-box-arrow-up-right small"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Publish Settings -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-gear me-2"></i>Page Settings
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="template" class="form-label">Template <span class="text-danger">*</span></label>
                                    <select class="form-select" id="template" name="template" required>
                                        @foreach($templates as $key => $name)
                                            <option value="{{ $key }}" {{ old('template', $page->template) === $key ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Choose the design template for this page</div>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_published" name="is_published" {{ old('is_published', $page->is_published) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_published">
                                        <strong>Publish Page</strong>
                                    </label>
                                    <div class="form-text">Make this page visible to visitors</div>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="show_in_footer" name="show_in_footer" {{ old('show_in_footer', $page->show_in_footer) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_in_footer">
                                        <strong>Show in Footer</strong>
                                    </label>
                                    <div class="form-text">Add link to this page in footer</div>
                                </div>

                                <div class="mb-3" id="footer_section_group" style="{{ old('show_in_footer', $page->show_in_footer) ? '' : 'display: none;' }}">
                                    <label for="footer_section" class="form-label">Footer Section</label>
                                    <select class="form-select" id="footer_section" name="footer_section">
                                        <option value="">Select Section</option>
                                        @foreach($footerSections as $key => $name)
                                            <option value="{{ $key }}" {{ old('footer_section', $page->footer_section) === $key ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number" class="form-control" id="sort_order" name="sort_order" value="{{ old('sort_order', $page->sort_order) }}" min="0">
                                    <div class="form-text">Order in navigation (0 = first)</div>
                                </div>
                            </div>
                        </div>

                        <!-- Danger Zone -->
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-exclamation-triangle me-2"></i>Danger Zone
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">Once you delete this page, there is no going back. Please be certain.</p>
                                <form method="POST" action="{{ route('superadmin.pages.destroy', $page) }}" class="d-inline" onsubmit="return confirm('Are you absolutely sure you want to delete this page? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash me-1"></i>Delete Page
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('superadmin.pages.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </a>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i>Update Page
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show/hide footer section based on checkbox
    const showInFooterCheck = document.getElementById('show_in_footer');
    const footerSectionGroup = document.getElementById('footer_section_group');
    
    showInFooterCheck.addEventListener('change', function() {
        footerSectionGroup.style.display = this.checked ? 'block' : 'none';
    });
    
    // Auto-generate slug from title (only if slug is empty or matches current title)
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    const originalTitle = '{{ $page->title }}';
    const originalSlug = '{{ $page->slug }}';
    
    titleInput.addEventListener('input', function() {
        // Only auto-generate if slug matches the original pattern or is empty
        const currentSlug = slugInput.value;
        const expectedSlug = originalTitle
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
            
        if (!currentSlug || currentSlug === originalSlug || currentSlug === expectedSlug) {
            const newSlug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
            slugInput.value = newSlug;
        }
    });
});
</script>
@endsection
