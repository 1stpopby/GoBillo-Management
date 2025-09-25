@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('knowledge-base.index') }}">Knowledge Base</a>
            </li>
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">
                <i class="{{ $category->icon ?? 'bi bi-folder' }} me-2"></i>
                {{ $category->name }}
            </h1>
            @if($category->description)
                <p class="text-muted">{{ $category->description }}</p>
            @endif
        </div>
    </div>

    @if($articles->count() > 0)
        <div class="row">
            @foreach($articles as $article)
                <div class="col-md-6 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="{{ route('knowledge-base.article', $article) }}" class="text-decoration-none">
                                    {{ $article->title }}
                                </a>
                            </h5>
                            <p class="card-text text-muted">
                                {{ Str::limit(strip_tags($article->content), 150) }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $article->created_at->format('M d, Y') }}
                                </small>
                                <a href="{{ route('knowledge-base.article', $article) }}" class="btn btn-sm btn-outline-primary">
                                    Read More <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $articles->links() }}
    @else
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            No articles available in this category yet.
        </div>
    @endif
</div>
@endsection