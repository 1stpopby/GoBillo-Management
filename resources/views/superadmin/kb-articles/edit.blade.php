@extends('layouts.superadmin')

@section('title', 'Edit Article')
@section('page-title', 'Edit Knowledge Base Article')

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
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Article: {{ $article->title }}</h5>
                    <div>
                        @if($article->status == 'published')
                            <a href="{{ route('kb.article', ['categorySlug' => $article->category->slug ?? 'uncategorized', 'articleSlug' => $article->slug]) }}" 
                               target="_blank" 
                               class="btn btn-sm btn-light">
                                <i class="bi bi-eye me-1"></i>View Live
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('superadmin.kb.articles.update', $article) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <!-- Title -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Article Title <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $article->title) }}" 
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
                                           value="{{ old('slug', $article->slug) }}" 
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
                                              placeholder="Brief description that appears in search results and article listings">{{ old('summary', $article->summary) }}</textarea>
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
                                              required>{{ old('content', $article->currentVersion->content ?? '') }}</textarea>
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
                                            <option value="{{ $category->id }}" {{ old('category_id', $article->category_id) == $category->id ? 'selected' : '' }}>
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
                                        <option value="draft" {{ old('status', $article->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="published" {{ old('status', $article->status) == 'published' ? 'selected' : '' }}>Published</option>
                                        <option value="archived" {{ old('status', $article->status) == 'archived' ? 'selected' : '' }}>Archived</option>
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
                                           value="{{ old('priority', $article->priority ?? 5) }}" 
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
                                           value="{{ old('order', $article->order ?? 0) }}" 
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
                                                       {{ in_array($tag->id, old('tags', $article->tags->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="tag_{{ $tag->id }}">
                                                    {{ $tag->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <!-- Article Info -->
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Article Information</h6>
                                        <small class="text-muted d-block">Created: {{ $article->created_at->format('M d, Y H:i') }}</small>
                                        <small class="text-muted d-block">Last Updated: {{ $article->updated_at->format('M d, Y H:i') }}</small>
                                        <small class="text-muted d-block">Views: {{ $article->views()->count() }}</small>
                                        @if($article->versions()->count() > 1)
                                            <small class="text-muted d-block">Versions: {{ $article->versions()->count() }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <div>
                                <form action="{{ route('superadmin.kb.articles.destroy', $article) }}" 
                                      method="POST" 
                                      class="d-inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this article?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-trash me-2"></i>Delete Article
                                    </button>
                                </form>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('superadmin.kb.articles.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" name="action" value="save_draft" class="btn btn-outline-primary">
                                    <i class="bi bi-save me-2"></i>Save as Draft
                                </button>
                                <button type="submit" name="action" value="save_publish" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Update & Publish
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Version History -->
            @if($article->versions()->count() > 1)
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Version History</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Version</th>
                                    <th>Created</th>
                                    <th>Author</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($article->versions()->orderBy('version', 'desc')->take(5)->get() as $version)
                                    <tr @if($version->id == $article->currentVersion->id) class="table-active" @endif>
                                        <td>v{{ $version->version }}</td>
                                        <td>{{ $version->created_at->format('M d, Y H:i') }}</td>
                                        <td>{{ $version->author->name ?? 'System' }}</td>
                                        <td>
                                            @if($version->id != $article->currentVersion->id)
                                                <form action="{{ route('superadmin.kb.articles.restore-version', [$article, $version]) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">Restore</button>
                                                </form>
                                            @else
                                                <span class="badge bg-primary">Current</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Handle save actions
document.querySelectorAll('button[name="action"]').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const form = this.closest('form');
        const statusSelect = document.getElementById('status');
        
        if (this.value === 'save_publish') {
            statusSelect.value = 'published';
        } else if (this.value === 'save_draft' && statusSelect.value === 'published') {
            // Keep current status if published, unless explicitly changing to draft
        }
        
        form.submit();
    });
});
</script>
@endpush