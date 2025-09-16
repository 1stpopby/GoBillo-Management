@extends('layouts.app')

@section('title', 'Create Task Category')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('task-categories.index') }}">Task Categories</a></li>
                        <li class="breadcrumb-item active">Create Category</li>
                    </ol>
                </nav>
                <h1 class="page-title">Create Task Category</h1>
                <p class="page-subtitle">Add a new category to organize your tasks</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Category Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('task-categories.store') }}" method="POST">
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="color" class="form-label">Color <span class="text-danger">*</span></label>
                                <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                       id="color" name="color" value="{{ old('color', '#6b7280') }}" required>
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                       id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="icon" class="form-label">Bootstrap Icon Class</label>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                       id="icon" name="icon" value="{{ old('icon') }}" 
                                       placeholder="e.g., bi-tools, bi-wrench, bi-palette">
                                <div class="form-text">Enter a Bootstrap Icon class name (optional)</div>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('task-categories.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Create Category
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Preview</h6>
                </div>
                <div class="card-body">
                    <div class="category-preview">
                        <div class="category-icon-preview" id="iconPreview">
                            <i class="bi bi-tag" id="iconElement"></i>
                        </div>
                        <div class="category-info-preview">
                            <h6 class="category-name-preview" id="namePreview">Category Name</h6>
                            <p class="category-description-preview" id="descriptionPreview">Category description will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.category-preview {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 12px;
}

.category-icon-preview {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #6b728015;
    border: 2px solid #6b7280;
}

.category-icon-preview i {
    font-size: 1.25rem;
    color: #6b7280;
}

.category-info-preview {
    flex: 1;
}

.category-name-preview {
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #1f2937;
}

.category-description-preview {
    color: #6b7280;
    font-size: 0.875rem;
    margin: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const descriptionInput = document.getElementById('description');
    const colorInput = document.getElementById('color');
    const iconInput = document.getElementById('icon');
    
    const namePreview = document.getElementById('namePreview');
    const descriptionPreview = document.getElementById('descriptionPreview');
    const iconPreview = document.getElementById('iconPreview');
    const iconElement = document.getElementById('iconElement');
    
    function updatePreview() {
        // Update name
        namePreview.textContent = nameInput.value || 'Category Name';
        
        // Update description
        descriptionPreview.textContent = descriptionInput.value || 'Category description will appear here';
        
        // Update color
        const color = colorInput.value;
        iconPreview.style.backgroundColor = color + '15';
        iconPreview.style.borderColor = color;
        iconElement.style.color = color;
        
        // Update icon
        const iconClass = iconInput.value || 'bi-tag';
        iconElement.className = iconClass.startsWith('bi-') ? iconClass : 'bi-' + iconClass;
    }
    
    nameInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);
    colorInput.addEventListener('input', updatePreview);
    iconInput.addEventListener('input', updatePreview);
    
    // Initial preview
    updatePreview();
});
</script>
@endsection 