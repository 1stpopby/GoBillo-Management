@extends('layouts.superadmin')

@section('title', 'Create Article')
@section('page-title', 'Create Knowledge Base Article')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Back Button -->
            <div class="mb-3">
                <a href="{{ route('superadmin.kb.articles.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Articles
                </a>
            </div>

            <!-- Form Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">New Article Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('superadmin.kb.articles.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <!-- Title -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Article Title <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title') }}" 
                                           placeholder="e.g., How to Create Your First Project"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Slug -->
                                <div class="mb-3">
                                    <label for="slug" class="form-label">URL Slug</label>
                                    <input type="text" 
                                           class="form-control @error('slug') is-invalid @enderror" 
                                           id="slug" 
                                           name="slug" 
                                           value="{{ old('slug') }}" 
                                           placeholder="Leave empty to auto-generate from title">
                                    <small class="form-text text-muted">Used in URLs. Will be auto-generated if left empty.</small>
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Summary -->
                                <div class="mb-3">
                                    <label for="summary" class="form-label">Summary</label>
                                    <textarea class="form-control @error('summary') is-invalid @enderror" 
                                              id="summary" 
                                              name="summary" 
                                              rows="2"
                                              placeholder="Brief description that appears in search results and article listings">{{ old('summary') }}</textarea>
                                    @error('summary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Content -->
                                <div class="mb-3">
                                    <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('content') is-invalid @enderror" 
                                              id="content" 
                                              name="content" 
                                              rows="15"
                                              placeholder="Article content (supports Markdown formatting)"
                                              required>{{ old('content') }}</textarea>
                                    <small class="form-text text-muted">
                                        Supports Markdown formatting: **bold**, *italic*, # Heading, - bullet points, [link](url), etc.
                                    </small>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Category -->
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-control @error('category_id') is-invalid @enderror" 
                                            id="category_id" 
                                            name="category_id"
                                            required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Status -->
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status"
                                            required>
                                        <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                        <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Priority -->
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority (0-10)</label>
                                    <input type="number" 
                                           class="form-control @error('priority') is-invalid @enderror" 
                                           id="priority" 
                                           name="priority" 
                                           value="{{ old('priority', 5) }}" 
                                           min="0"
                                           max="10">
                                    <small class="form-text text-muted">Higher priority articles appear first</small>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Order -->
                                <div class="mb-3">
                                    <label for="order" class="form-label">Display Order</label>
                                    <input type="number" 
                                           class="form-control @error('order') is-invalid @enderror" 
                                           id="order" 
                                           name="order" 
                                           value="{{ old('order', 0) }}" 
                                           min="0">
                                    <small class="form-text text-muted">Lower numbers appear first within same priority</small>
                                    @error('order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Tags -->
                                @if(isset($tags) && $tags->count() > 0)
                                <div class="mb-3">
                                    <label class="form-label">Tags</label>
                                    <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                                        @foreach($tags as $tag)
                                            <div class="form-check">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="tags[]" 
                                                       value="{{ $tag->id }}" 
                                                       id="tag_{{ $tag->id }}"
                                                       {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="tag_{{ $tag->id }}">
                                                    {{ $tag->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('superadmin.kb.articles.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" name="action" value="save_draft" class="btn btn-outline-primary">
                                <i class="bi bi-save me-2"></i>Save as Draft
                            </button>
                            <button type="submit" name="action" value="save_publish" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Save & Publish
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-generate slug from title
document.getElementById('title').addEventListener('input', function() {
    const slug = document.getElementById('slug');
    if (slug.value === '') {
        slug.value = this.value.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '');
    }
});

// Handle save actions
document.querySelectorAll('button[name="action"]').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const form = this.closest('form');
        const statusSelect = document.getElementById('status');
        
        if (this.value === 'save_publish') {
            statusSelect.value = 'published';
        } else if (this.value === 'save_draft') {
            statusSelect.value = 'draft';
        }
        
        form.submit();
    });
});
</script>
@endpush