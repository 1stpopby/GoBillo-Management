@extends('layouts.superadmin')

@section('title', 'Knowledge Base Categories')
@section('page-title', 'Knowledge Base Categories')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="h3 mb-0 text-dark">Manage Categories</h2>
            <p class="text-muted">Organize your knowledge base content into categories</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('superadmin.kb.articles.index') }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-file-earmark-text me-2"></i>Manage Articles
            </a>
            <a href="{{ route('superadmin.kb.categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add New Category
            </a>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if(count($categoryTree) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Articles</th>
                                <th>Status</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categoryTree as $category)
                                @include('superadmin.kb-categories.partials.category-row', ['category' => $category, 'level' => 0])
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-folder2-open text-muted" style="font-size: 3rem;"></i>
                    <h4 class="mt-3 text-muted">No categories yet</h4>
                    <p class="text-muted">Create your first category to start organizing knowledge base articles</p>
                    <a href="{{ route('superadmin.kb.categories.create') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-plus-circle me-2"></i>Create First Category
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteCategory(categoryId) {
    if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
        document.getElementById('delete-form-' + categoryId).submit();
    }
}
</script>
@endpush