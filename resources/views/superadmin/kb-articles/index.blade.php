@extends('layouts.superadmin')

@section('title', 'Knowledge Base Articles')
@section('page-title', 'Knowledge Base Articles')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="h3 mb-0 text-dark">Manage Articles</h2>
            <p class="text-muted">Create and manage knowledge base content</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('superadmin.kb.categories.index') }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-folder2 me-2"></i>Manage Categories
            </a>
            <a href="{{ route('superadmin.kb.articles.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add New Article
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('superadmin.kb.articles.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="category" class="form-label">Category</label>
                    <select name="category" id="category" class="form-control">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Search title or summary..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('superadmin.kb.articles.index') }}" class="btn btn-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Articles Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if($articles->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Views</th>
                                <th>Last Updated</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($articles as $article)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $article->title }}</div>
                                        @if($article->summary)
                                            <small class="text-muted">{{ Str::limit($article->summary, 60) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($article->category)
                                            <span class="badge bg-info">{{ $article->category->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($article->status == 'published')
                                            <span class="badge bg-success">Published</span>
                                        @elseif($article->status == 'draft')
                                            <span class="badge bg-warning">Draft</span>
                                        @else
                                            <span class="badge bg-secondary">Archived</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($article->priority >= 8)
                                            <span class="badge bg-danger">High ({{ $article->priority }})</span>
                                        @elseif($article->priority >= 5)
                                            <span class="badge bg-warning">Medium ({{ $article->priority }})</span>
                                        @else
                                            <span class="badge bg-secondary">Normal ({{ $article->priority ?? 0 }})</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $article->views_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $article->updated_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('kb.article', ['categorySlug' => $article->category->slug ?? 'uncategorized', 'articleSlug' => $article->slug]) }}" 
                                               target="_blank" 
                                               class="btn btn-outline-info" 
                                               title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('superadmin.kb.articles.edit', $article) }}" 
                                               class="btn btn-outline-primary" 
                                               title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('superadmin.kb.articles.duplicate', $article) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-outline-secondary" 
                                                        title="Duplicate">
                                                    <i class="bi bi-files"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('superadmin.kb.articles.destroy', $article) }}" 
                                                  method="POST" 
                                                  class="d-inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete this article?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger" 
                                                        title="Delete">
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

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $articles->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
                    <h4 class="mt-3 text-muted">No articles found</h4>
                    <p class="text-muted">Create your first article to start building your knowledge base</p>
                    <a href="{{ route('superadmin.kb.articles.create') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-plus-circle me-2"></i>Create First Article
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection