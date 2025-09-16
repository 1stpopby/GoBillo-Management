@extends('layouts.app')

@section('title', 'Add New Asset')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('assets.index') }}">Assets</a></li>
                <li class="breadcrumb-item active">Add New Asset</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">Add New Asset</h1>
        <p class="text-muted">Create a new asset record</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('assets.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="asset_code" class="form-label">Asset Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('asset_code') is-invalid @enderror" 
                                   id="asset_code" name="asset_code" value="{{ old('asset_code', \App\Models\Asset::generateAssetCode()) }}" required>
                            @error('asset_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Unique identifier for this asset</div>
                        </div>
                        <div class="col-md-6">
                            <label for="name" class="form-label">Asset Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                @foreach(\App\Models\Asset::getStatuses() as $key => $label)
                                    <option value="{{ $key }}" {{ old('status', 'IN_STOCK') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
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
                    </div>
                </div>
            </div>

            <!-- Location & Assignment -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Location & Assignment</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="location_id" class="form-label">Location</label>
                            <select class="form-select @error('location_id') is-invalid @enderror" id="location_id" name="location_id">
                                <option value="">Select Location</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('location_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="assignee_id" class="form-label">Assigned To</label>
                            <select class="form-select @error('assignee_id') is-invalid @enderror" id="assignee_id" name="assignee_id">
                                <option value="">Unassigned</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assignee_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assignee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="department" class="form-label">Department</label>
                            <input type="text" class="form-control @error('department') is-invalid @enderror" 
                                   id="department" name="department" value="{{ old('department') }}">
                            @error('department')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technical Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Technical Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="serial_number" class="form-label">Serial Number</label>
                            <input type="text" class="form-control @error('serial_number') is-invalid @enderror" 
                                   id="serial_number" name="serial_number" value="{{ old('serial_number') }}">
                            @error('serial_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="model_number" class="form-label">Model Number</label>
                            <input type="text" class="form-control @error('model_number') is-invalid @enderror" 
                                   id="model_number" name="model_number" value="{{ old('model_number') }}">
                            @error('model_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="vendor_id" class="form-label">Vendor</label>
                            <select class="form-select @error('vendor_id') is-invalid @enderror" id="vendor_id" name="vendor_id">
                                <option value="">Select Vendor</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vendor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Financial Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="purchase_date" class="form-label">Purchase Date</label>
                            <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" 
                                   id="purchase_date" name="purchase_date" value="{{ old('purchase_date') }}">
                            @error('purchase_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="purchase_cost" class="form-label">Purchase Cost</label>
                            <div class="input-group">
                                <span class="input-group-text">£</span>
                                <input type="number" step="0.01" class="form-control @error('purchase_cost') is-invalid @enderror" 
                                       id="purchase_cost" name="purchase_cost" value="{{ old('purchase_cost') }}">
                            </div>
                            @error('purchase_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="depreciation_method" class="form-label">Depreciation Method</label>
                            <select class="form-select @error('depreciation_method') is-invalid @enderror" 
                                    id="depreciation_method" name="depreciation_method">
                                @foreach(\App\Models\Asset::getDepreciationMethods() as $key => $label)
                                    <option value="{{ $key }}" {{ old('depreciation_method', 'NONE') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('depreciation_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6" id="depreciation_life_field" style="display: none;">
                            <label for="depreciation_life_months" class="form-label">Depreciation Life (Months)</label>
                            <input type="number" class="form-control @error('depreciation_life_months') is-invalid @enderror" 
                                   id="depreciation_life_months" name="depreciation_life_months" 
                                   value="{{ old('depreciation_life_months') }}">
                            @error('depreciation_life_months')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Warranty Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Warranty Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="warranty_expiry" class="form-label">Warranty Expiry Date</label>
                            <input type="date" class="form-control @error('warranty_expiry') is-invalid @enderror" 
                                   id="warranty_expiry" name="warranty_expiry" value="{{ old('warranty_expiry') }}">
                            @error('warranty_expiry')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Additional Notes</h5>
                </div>
                <div class="card-body">
                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                              id="notes" name="notes" rows="4" 
                              placeholder="Any additional notes about this asset...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('assets.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check me-1"></i>Create Asset
                </button>
            </div>
        </form>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Asset Creation Tips</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-lightbulb text-warning me-2"></i>
                        <strong>Asset Code:</strong> Use a consistent naming convention like AST-000001
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-lightbulb text-warning me-2"></i>
                        <strong>Serial Number:</strong> Record the manufacturer's serial number for warranty claims
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-lightbulb text-warning me-2"></i>
                        <strong>Categories:</strong> Group similar assets together for better organization
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-lightbulb text-warning me-2"></i>
                        <strong>Depreciation:</strong> Choose appropriate method for accurate book value tracking
                    </li>
                    <li>
                        <i class="bi bi-lightbulb text-warning me-2"></i>
                        <strong>QR Code:</strong> Will be automatically generated after creation
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Show/hide depreciation life field based on method selection
document.getElementById('depreciation_method').addEventListener('change', function() {
    const depreciationLifeField = document.getElementById('depreciation_life_field');
    if (this.value === 'STRAIGHT_LINE') {
        depreciationLifeField.style.display = 'block';
        document.getElementById('depreciation_life_months').setAttribute('required', 'required');
    } else {
        depreciationLifeField.style.display = 'none';
        document.getElementById('depreciation_life_months').removeAttribute('required');
    }
});

// Trigger on page load if straight line is already selected
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('depreciation_method').value === 'STRAIGHT_LINE') {
        document.getElementById('depreciation_life_field').style.display = 'block';
        document.getElementById('depreciation_life_months').setAttribute('required', 'required');
    }
});

// Auto-update status when assignee is selected
document.getElementById('assignee_id').addEventListener('change', function() {
    const statusField = document.getElementById('status');
    if (this.value && statusField.value === 'IN_STOCK') {
        statusField.value = 'ASSIGNED';
    } else if (!this.value && statusField.value === 'ASSIGNED') {
        statusField.value = 'IN_STOCK';
    }
});
</script>
@endpush
@endsection


