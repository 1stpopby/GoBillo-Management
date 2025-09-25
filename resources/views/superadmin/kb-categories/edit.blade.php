@extends('layouts.superadmin')

@section('title', 'Edit Category')
@section('page-title', 'Edit Knowledge Base Category')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Back Button -->
            <div class="mb-3">
                <a href="{{ route('superadmin.kb.categories.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Categories
                </a>
            </div>

            <!-- Form Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edit Category: {{ $category->name }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('superadmin.kb.categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $category->name) }}" 
                                   placeholder="e.g., Getting Started, Billing, API Documentation"
                                   required>
                            @error('name')
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
                                   value="{{ old('slug', $category->slug) }}" 
                                   placeholder="Leave empty to auto-generate from name">
                            <small class="form-text text-muted">Used in URLs. Will be auto-generated if left empty.</small>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3"
                                      placeholder="Brief description of what this category contains">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Parent Category -->
                        <div class="mb-3">
                            <label for="parent_id" class="form-label">Parent Category</label>
                            <select class="form-control @error('parent_id') is-invalid @enderror" 
                                    id="parent_id" 
                                    name="parent_id">
                                <option value="">None (Top Level)</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" 
                                            {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Icon -->
                        <div class="mb-3">
                            <label for="icon" class="form-label">Icon (Bootstrap Icon)</label>
                            <div class="input-group">
                                <span class="input-group-text">bi bi-</span>
                                <input type="text" 
                                       class="form-control @error('icon') is-invalid @enderror" 
                                       id="icon" 
                                       name="icon" 
                                       value="{{ old('icon', $category->icon) }}" 
                                       placeholder="e.g., book, folder, gear">
                            </div>
                            <small class="form-text text-muted">
                                Choose from <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a>. 
                                Enter only the icon name without 'bi bi-' prefix.
                            </small>
                            @error('icon')
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
                                   value="{{ old('order', $category->order ?? 0) }}" 
                                   min="0">
                            <small class="form-text text-muted">Lower numbers appear first</small>
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active (visible to users)
                                </label>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <div>
                                @if($category->articles_count == 0 && $category->children()->count() == 0)
                                    <form action="{{ route('superadmin.kb.categories.destroy', $category) }}" 
                                          method="POST" 
                                          class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bi bi-trash me-2"></i>Delete Category
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('superadmin.kb.categories.index') }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Update Category
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Category Info -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">Category Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Articles in this category:</small>
                            <div class="fw-bold">{{ $category->articles_count ?? 0 }}</div>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Subcategories:</small>
                            <div class="fw-bold">{{ $category->children()->count() }}</div>
                        </div>
                    </div>
                    @if($category->articles_count > 0 || $category->children()->count() > 0)
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            This category cannot be deleted because it contains 
                            {{ $category->articles_count ?? 0 }} article(s) and/or 
                            {{ $category->children()->count() }} subcategory(ies).
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection