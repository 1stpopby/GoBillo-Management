@extends('layouts.app')

@section('title', 'Edit Tool Hire Request')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Edit Tool Hire Request</h1>
            <p class="page-subtitle">Update your tool hire request</p>
        </div>
        <div>
            <a href="{{ route('tool-hire.show', $toolHireRequest) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Request
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('tool-hire.update', $toolHireRequest) }}">
                @csrf
                @method('PUT')
                
                <!-- Tool Request Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-tools me-2"></i>Tool Request
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="title" class="form-label">What do you need? <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $toolHireRequest->title) }}" 
                                       placeholder="e.g., Mini Excavator for Foundation Work">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="tool_category" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select @error('tool_category') is-invalid @enderror" 
                                        id="tool_category" name="tool_category">
                                    <option value="">Select Category</option>
                                    @foreach(\App\Models\ToolHireRequest::getCategoryOptions() as $key => $label)
                                        <option value="{{ $key }}" {{ old('tool_category', $toolHireRequest->tool_category) === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tool_category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                       id="quantity" name="quantity" value="{{ old('quantity', $toolHireRequest->quantity) }}" 
                                       min="1" max="100">
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hire Period Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar me-2"></i>Hire Period
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="hire_start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('hire_start_date') is-invalid @enderror" 
                                       id="hire_start_date" name="hire_start_date" 
                                       value="{{ old('hire_start_date', $toolHireRequest->hire_start_date?->format('Y-m-d')) }}">
                                @error('hire_start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="hire_end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('hire_end_date') is-invalid @enderror" 
                                       id="hire_end_date" name="hire_end_date" 
                                       value="{{ old('hire_end_date', $toolHireRequest->hire_end_date?->format('Y-m-d')) }}">
                                @error('hire_end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tool-hire.show', $toolHireRequest) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Request
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Help Sidebar -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Edit Information
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">
                        You can only edit requests that are in <strong>Draft</strong> status or have been <strong>Rejected</strong>.
                    </p>
                    
                    <div class="mb-3">
                        <strong>Current Status:</strong>
                        <span class="badge {{ $toolHireRequest->status_color }} ms-2">
                            {{ $toolHireRequest->status_display }}
                        </span>
                    </div>
                    
                    @if($toolHireRequest->status !== 'draft')
                    <div class="alert alert-warning small">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Once submitted, only basic information can be updated.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Date validation
    const startDate = document.getElementById('hire_start_date');
    const endDate = document.getElementById('hire_end_date');
    
    startDate.addEventListener('change', function() {
        if (this.value) {
            endDate.min = this.value;
        }
    });
    
    // Set initial minimum for end date
    if (startDate.value) {
        endDate.min = startDate.value;
    }
});
</script>

<style>
.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #6c757d;
    margin-bottom: 0;
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.card-title {
    color: #495057;
    font-weight: 600;
}

.form-label {
    font-weight: 500;
    color: #495057;
}

.text-danger {
    color: #dc3545 !important;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    font-weight: 500;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #667eea 100%);
    transform: translateY(-1px);
}

.btn-outline-secondary {
    border-color: #6c757d;
    color: #6c757d;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
}
</style>
@endsection
