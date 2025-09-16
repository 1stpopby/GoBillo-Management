@extends('layouts.app')

@section('title', 'Edit Asset - ' . $asset->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('assets.index') }}">Assets</a></li>
                <li class="breadcrumb-item"><a href="{{ route('assets.show', $asset) }}">{{ $asset->asset_code }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">Edit Asset</h1>
        <p class="text-muted">{{ $asset->asset_code }} - {{ $asset->name }}</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <form action="{{ route('assets.update', $asset) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
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
                                   id="asset_code" name="asset_code" value="{{ old('asset_code', $asset->asset_code) }}" required>
                            @error('asset_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Unique identifier for this asset</div>
                        </div>
                        <div class="col-md-6">
                            <label for="name" class="form-label">Asset Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $asset->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id', $asset->category_id) == $category->id ? 'selected' : '' }}>
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
                                    <option value="{{ $key }}" {{ old('status', $asset->status) === $key ? 'selected' : '' }}>
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
                                      id="description" name="description" rows="3">{{ old('description', $asset->description) }}</textarea>
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
                                    <option value="{{ $location->id }}" 
                                            {{ old('location_id', $asset->location_id) == $location->id ? 'selected' : '' }}>
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
                                    <option value="{{ $user->id }}" 
                                            {{ old('assignee_id', $asset->assignee_id) == $user->id ? 'selected' : '' }}>
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
                                   id="department" name="department" value="{{ old('department', $asset->department) }}">
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
                                   id="serial_number" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}">
                            @error('serial_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="model_number" class="form-label">Model Number</label>
                            <input type="text" class="form-control @error('model_number') is-invalid @enderror" 
                                   id="model_number" name="model_number" value="{{ old('model_number', $asset->model_number) }}">
                            @error('model_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="vendor_id" class="form-label">Vendor</label>
                            <select class="form-select @error('vendor_id') is-invalid @enderror" id="vendor_id" name="vendor_id">
                                <option value="">Select Vendor</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" 
                                            {{ old('vendor_id', $asset->vendor_id) == $vendor->id ? 'selected' : '' }}>
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
                                   id="purchase_date" name="purchase_date" 
                                   value="{{ old('purchase_date', $asset->purchase_date?->format('Y-m-d')) }}">
                            @error('purchase_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="purchase_cost" class="form-label">Purchase Cost</label>
                            <div class="input-group">
                                <span class="input-group-text">£</span>
                                <input type="number" step="0.01" class="form-control @error('purchase_cost') is-invalid @enderror" 
                                       id="purchase_cost" name="purchase_cost" value="{{ old('purchase_cost', $asset->purchase_cost) }}">
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
                                    <option value="{{ $key }}" 
                                            {{ old('depreciation_method', $asset->depreciation_method) === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('depreciation_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6" id="depreciation_life_field" 
                             style="display: {{ old('depreciation_method', $asset->depreciation_method) === 'STRAIGHT_LINE' ? 'block' : 'none' }};">
                            <label for="depreciation_life_months" class="form-label">Depreciation Life (Months)</label>
                            <input type="number" class="form-control @error('depreciation_life_months') is-invalid @enderror" 
                                   id="depreciation_life_months" name="depreciation_life_months" 
                                   value="{{ old('depreciation_life_months', $asset->depreciation_life_months) }}">
                            @error('depreciation_life_months')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @if($asset->purchase_cost && $asset->depreciation_method !== 'NONE')
                        <div class="col-md-6">
                            <label class="form-label text-muted">Current Book Value</label>
                            <div class="fw-bold text-primary fs-5">{{ $asset->book_value_formatted }}</div>
                        </div>
                        @endif
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
                                   id="warranty_expiry" name="warranty_expiry" 
                                   value="{{ old('warranty_expiry', $asset->warranty_expiry?->format('Y-m-d')) }}">
                            @error('warranty_expiry')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @if($asset->warranty_expiry)
                        <div class="col-md-6">
                            <label class="form-label text-muted">Warranty Status</label>
                            <div>
                                @php $warranty = $asset->warranty_status @endphp
                                <span class="badge bg-{{ match($warranty['status']) {
                                    'active' => 'success',
                                    'expiring' => 'warning',
                                    'expired' => 'danger',
                                    default => 'secondary'
                                } }}">
                                    {{ $warranty['message'] }}
                                </span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tags -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tags</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="tags" class="form-label">Asset Tags</label>
                            <select class="form-select" id="tags" name="tags[]" multiple>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}" 
                                            {{ in_array($tag->id, old('tags', $asset->tags->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Hold Ctrl/Cmd to select multiple tags</div>
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
                              placeholder="Any additional notes about this asset...">{{ old('notes', $asset->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('assets.show', $asset) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check me-1"></i>Update Asset
                </button>
            </div>
        </form>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Asset Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Asset Summary</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="bg-light rounded p-3">
                        <i class="bi bi-box display-4 text-muted"></i>
                    </div>
                </div>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Created:</span>
                        <span>{{ $asset->created_at->format('M j, Y') }}</span>
                    </li>
                    @if($asset->createdBy)
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Created by:</span>
                        <span>{{ $asset->createdBy->name }}</span>
                    </li>
                    @endif
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Last updated:</span>
                        <span>{{ $asset->updated_at->format('M j, Y') }}</span>
                    </li>
                    @if($asset->updatedBy)
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Updated by:</span>
                        <span>{{ $asset->updatedBy->name }}</span>
                    </li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('assets.show', $asset) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-eye me-1"></i>View Asset Details
                    </a>
                    <a href="{{ route('assets.qr', $asset) }}" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-qr-code me-1"></i>View QR Code
                    </a>
                    <a href="{{ route('assets.download-qr', $asset) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-download me-1"></i>Download QR Code
                    </a>
                </div>
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

// Auto-update status when assignee is selected/deselected
document.getElementById('assignee_id').addEventListener('change', function() {
    const statusField = document.getElementById('status');
    if (this.value && statusField.value === 'IN_STOCK') {
        if (confirm('This asset will be marked as ASSIGNED. Continue?')) {
            statusField.value = 'ASSIGNED';
        }
    } else if (!this.value && statusField.value === 'ASSIGNED') {
        if (confirm('This asset will be marked as IN_STOCK. Continue?')) {
            statusField.value = 'IN_STOCK';
        }
    }
});
</script>
@endpush
@endsection


