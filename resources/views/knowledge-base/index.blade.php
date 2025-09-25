@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">
                <i class="bi bi-book me-2"></i>Knowledge Base
            </h1>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="row mb-4">
        <div class="col-md-8 mx-auto">
            <form method="GET" action="{{ route('kb.index') }}">
                <div class="input-group">
                    <input type="text" 
                           class="form-control" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search articles...">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i> Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('kb.index') }}" class="btn btn-secondary">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Categories Grid -->
    @if(!request('search'))
        <div class="row mb-4">
            @foreach($categories as $category)
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-2">
                                <div class="me-3">
                                    <i class="{{ $category->icon ?? 'bi bi-folder' }} fs-2 text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1">
                                        <a href="{{ route('kb.category', $category->slug) }}" class="text-decoration-none">
                                            {{ $category->name }}
                                        </a>
                                    </h5>
                                    <p class="text-muted small mb-2">
                                        {{ $category->description }}
                                    </p>
                                    <span class="badge bg-secondary">
                                        {{ $category->articles_count ?? 0 }} Articles
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Search Results -->
    @if(request('search'))
        @if($articles->count() > 0)
            <div class="row">
                <div class="col-md-10 mx-auto">
                    <h4 class="mb-3">Search Results ({{ $articles->total() }})</h4>
                    @foreach($articles as $article)
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="{{ route('kb.article', [$article->category->slug, $article->slug]) }}">
                                        {{ $article->title }}
                                    </a>
                                </h5>
                                <p class="text-muted small">
                                    <i class="bi bi-folder me-1"></i> {{ $article->category->name }}
                                    <span class="mx-2">•</span>
                                    <i class="bi bi-clock me-1"></i> {{ $article->created_at->format('M d, Y') }}
                                </p>
                                <p class="card-text">
                                    {{ Str::limit(strip_tags($article->content), 200) }}
                                </p>
                                @if($article->tags->count() > 0)
                                    <div>
                                        @foreach($article->tags as $tag)
                                            <span class="badge bg-light text-dark me-1">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    {{ $articles->links() }}
                </div>
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                No articles found matching your search.
            </div>
        @endif
    @endif

    <!-- Popular Articles -->
    @if(!request('search') && isset($popularArticles) && $popularArticles->count() > 0)
        <div class="row">
            <div class="col-md-10 mx-auto">
                <h4 class="mb-3">
                    <i class="bi bi-star me-2"></i>Popular Articles
                </h4>
                <div class="list-group">
                    @foreach($popularArticles as $article)
                        <a href="{{ route('kb.article', [$article->category->slug, $article->slug]) }}" 
                           class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $article->title }}</h6>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-folder me-1"></i> {{ $article->category->name }}
                                        <span class="mx-2">•</span>
                                        <i class="bi bi-eye me-1"></i> {{ $article->view_count }} views
                                    </p>
                                </div>
                                <i class="bi bi-chevron-right"></i>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection