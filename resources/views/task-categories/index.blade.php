@extends('layouts.app')

@section('title', 'Task Categories')

@section('content')
<div class="task-categories-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="page-title">Task Categories</h1>
                <p class="page-subtitle">Manage task categories for better organization and workflow</p>
            </div>
            <div class="col-lg-4 text-end">
                <a href="{{ route('task-categories.create') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle me-2"></i>New Category
                </a>
            </div>
        </div>
    </div>

    <!-- Categories List -->
    @if($categories->count() > 0)
        <div class="card mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:50px"></th>
                                <th>Name</th>
                                <th style="width:140px">Status</th>
                                <th style="width:120px">Order</th>
                                <th style="width:120px">Tasks</th>
                                <th style="width:200px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td>
                                        <span class="color-swatch" style="background-color: {{ $category->color }};"></span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $category->name }}</div>
                                        @if($category->description)
                                            <small class="text-muted d-block">{{ Str::limit($category->description, 80) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($category->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $category->sort_order }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $category->tasks_count }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('task-categories.show', $category) }}" class="btn btn-outline-secondary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('task-categories.edit', $category) }}" class="btn btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('task-categories.toggle', $category) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-warning">
                                                    @if($category->is_active)
                                                        <i class="bi bi-pause"></i>
                                                    @else
                                                        <i class="bi bi-play"></i>
                                                    @endif
                                                </button>
                                            </form>
                                            @if($category->tasks_count == 0)
                                                <form action="{{ route('task-categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Category Management Tools -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Category Management</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Quick Stats</h6>
                        <ul class="list-unstyled">
                            <li><strong>{{ $categories->where('is_active', true)->count() }}</strong> active categories</li>
                            <li><strong>{{ $categories->where('is_active', false)->count() }}</strong> inactive categories</li>
                            <li><strong>{{ $categories->sum('tasks_count') }}</strong> total tasks across all categories</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Management Actions</h6>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="enableSorting()">
                                <i class="bi bi-arrow-up-down me-1"></i>Reorder Categories
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="bulkActivate()">
                                <i class="bi bi-check-all me-1"></i>Activate All
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-content">
                <i class="bi bi-tags display-1 text-muted"></i>
                <h4 class="mt-3">No task categories found</h4>
                <p class="text-muted">Create your first task category to start organizing tasks by type.</p>
                <a href="{{ route('task-categories.create') }}" class="btn btn-primary btn-lg mt-3">
                    <i class="bi bi-plus-circle me-2"></i>Create Your First Category
                </a>
            </div>
        </div>
    @endif
</div>

<style>
.task-categories-container {
    max-width: 100%;
}

/* Category list helpers */
.color-swatch { width: 20px; height: 20px; border-radius: 4px; border: 1px solid #e5e7eb; display:inline-block; }

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-state-content {
    max-width: 400px;
    margin: 0 auto;
}

.empty-state h4 {
    color: #1f2937;
    font-weight: 600;
    margin-bottom: 1rem;
}

.empty-state p {
    font-size: 1.1rem;
    line-height: 1.6;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .category-card-header,
    .category-card-body,
    .category-card-footer {
        padding: 1.25rem;
    }
    
    .category-card-body {
        padding-top: 0;
    }
    
    .category-card-footer {
        padding-top: 0;
    }
    
    .category-color {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
}
</style>

<script>
function enableSorting() {
    alert('Drag and drop reordering will be implemented in a future update.');
}

function bulkActivate() {
    if (confirm('Are you sure you want to activate all categories?')) {
        // Implementation for bulk activation
        alert('Bulk activation will be implemented in a future update.');
    }
}
</script>
@endsection 