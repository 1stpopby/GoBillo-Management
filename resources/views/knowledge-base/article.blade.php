@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('kb.index') }}">Knowledge Base</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('kb.category', $article->category->slug) }}">
                    {{ $article->category->name }}
                </a>
            </li>
            <li class="breadcrumb-item active">{{ $article->title }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <article class="card">
                <div class="card-body">
                    <h1 class="h2 mb-3">{{ $article->title }}</h1>
                    
                    <div class="text-muted mb-4">
                        <i class="bi bi-folder me-1"></i> {{ $article->category->name }}
                        <span class="mx-2">•</span>
                        <i class="bi bi-clock me-1"></i> {{ $article->created_at->format('F d, Y') }}
                        <span class="mx-2">•</span>
                        <i class="bi bi-eye me-1"></i> {{ $article->view_count }} views
                    </div>

                    @if($article->tags->count() > 0)
                        <div class="mb-3">
                            @foreach($article->tags as $tag)
                                <span class="badge bg-secondary me-1">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif

                    <div class="article-content">
                        {!! $article->content !!}
                    </div>

                    <!-- Helpful Feedback -->
                    <div class="card bg-light mt-5">
                        <div class="card-body">
                            <h5 class="card-title">Was this article helpful?</h5>
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @else
                                <div class="btn-group" role="group">
                                    <form method="POST" action="{{ route('kb.article.feedback', [$article->category->slug, $article->slug]) }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="helpful" value="1">
                                        <button type="submit" class="btn btn-outline-success">
                                            <i class="bi bi-hand-thumbs-up me-1"></i> Yes
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('kb.article.feedback', [$article->category->slug, $article->slug]) }}" class="d-inline ms-2">
                                        @csrf
                                        <input type="hidden" name="helpful" value="0">
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="bi bi-hand-thumbs-down me-1"></i> No
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Related Articles -->
                    @if(isset($relatedArticles) && $relatedArticles->count() > 0)
                        <div class="mt-5">
                            <h4 class="mb-3">Related Articles</h4>
                            <div class="list-group">
                                @foreach($relatedArticles as $related)
                                    <a href="{{ route('kb.article', [$related->category->slug, $related->slug]) }}" 
                                       class="list-group-item list-group-item-action">
                                        <h6 class="mb-1">{{ $related->title }}</h6>
                                        <small class="text-muted">{{ $related->category->name }}</small>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </article>
        </div>
    </div>
</div>

<style>
.article-content {
    font-size: 1.1rem;
    line-height: 1.7;
}
.article-content h1,
.article-content h2,
.article-content h3,
.article-content h4,
.article-content h5,
.article-content h6 {
    margin-top: 2rem;
    margin-bottom: 1rem;
}
.article-content p {
    margin-bottom: 1rem;
}
.article-content ul,
.article-content ol {
    margin-bottom: 1rem;
    padding-left: 2rem;
}
.article-content pre {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.375rem;
    overflow-x: auto;
}
.article-content code {
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}
</style>
@endsection