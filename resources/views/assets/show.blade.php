@extends('layouts.app')

@section('title', $asset->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('assets.index') }}">Assets</a></li>
                <li class="breadcrumb-item active">{{ $asset->asset_code }}</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">{{ $asset->name }}</h1>
        <p class="text-muted">{{ $asset->asset_code }}</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('assets.qr', $asset) }}" class="btn btn-outline-info">
            <i class="bi bi-qr-code me-1"></i>QR Code
        </a>
        <div class="btn-group">
            <!-- Quick Actions -->
            <a href="{{ route('assets.edit', $asset) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i>Edit Asset
            </a>
            <a href="{{ route('assets.qr', $asset) }}" class="btn btn-info">
                <i class="bi bi-qr-code me-1"></i>QR Code
            </a>
            <button class="btn btn-success" onclick="assignAsset()">
                <i class="bi bi-person-plus me-1"></i>Assign
            </button>
            <button class="btn btn-warning" onclick="maintenanceMode()">
                <i class="bi bi-tools me-1"></i>Maintenance
            </button>
            
            <!-- More Actions Dropdown -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('assets.download-qr', $asset) }}">
                            <i class="bi bi-download me-2"></i>Download QR Code
                        </a>
                    </li>
                    <li>
                        <button class="dropdown-item" onclick="duplicateAsset()">
                            <i class="bi bi-copy me-2"></i>Duplicate Asset
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item" onclick="printLabel()">
                            <i class="bi bi-printer me-2"></i>Print Label
                        </button>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <button class="dropdown-item" onclick="retireAsset()">
                            <i class="bi bi-archive me-2"></i>Retire Asset
                        </button>
                    </li>
                    <li>
                        <form action="{{ route('assets.destroy', $asset) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this asset?')">
                                <i class="bi bi-trash me-2"></i>Delete Asset
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Asset Details -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Asset Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted">Asset Code</label>
                        <div class="fw-bold">{{ $asset->asset_code }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Name</label>
                        <div class="fw-bold">{{ $asset->name }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Category</label>
                        <div>
                            @if($asset->category)
                                <span class="badge" style="background-color: {{ $asset->category->color }}">
                                    <i class="{{ $asset->category->icon }} me-1"></i>{{ $asset->category->name }}
                                </span>
                            @else
                                <span class="text-muted">No category</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Status</label>
                        <div>
                            <span class="badge bg-{{ match($asset->status) {
                                'IN_STOCK' => 'primary',
                                'ASSIGNED' => 'success',
                                'MAINTENANCE' => 'warning',
                                'RETIRED' => 'secondary',
                                'LOST' => 'danger',
                                default => 'secondary'
                            } }}">
                                {{ $asset->status_label }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Location</label>
                        <div>{{ $asset->location?->name ?? 'Not assigned' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">Assigned To</label>
                        <div>{{ $asset->assignee?->name ?? 'Unassigned' }}</div>
                    </div>
                    @if($asset->serial_number)
                    <div class="col-md-6">
                        <label class="form-label text-muted">Serial Number</label>
                        <div class="fw-bold">{{ $asset->serial_number }}</div>
                    </div>
                    @endif
                    @if($asset->model_number)
                    <div class="col-md-6">
                        <label class="form-label text-muted">Model Number</label>
                        <div>{{ $asset->model_number }}</div>
                    </div>
                    @endif
                    @if($asset->vendor)
                    <div class="col-md-6">
                        <label class="form-label text-muted">Vendor</label>
                        <div>{{ $asset->vendor->name }}</div>
                    </div>
                    @endif
                    @if($asset->department)
                    <div class="col-md-6">
                        <label class="form-label text-muted">Department</label>
                        <div>{{ $asset->department }}</div>
                    </div>
                    @endif
                    @if($asset->description)
                    <div class="col-12">
                        <label class="form-label text-muted">Description</label>
                        <div>{{ $asset->description }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Financial Information -->
        @if($asset->purchase_cost || $asset->purchase_date)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Financial Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @if($asset->purchase_date)
                    <div class="col-md-6">
                        <label class="form-label text-muted">Purchase Date</label>
                        <div>{{ $asset->purchase_date->format('M j, Y') }}</div>
                    </div>
                    @endif
                    @if($asset->purchase_cost)
                    <div class="col-md-6">
                        <label class="form-label text-muted">Purchase Cost</label>
                        <div class="fw-bold text-success">{{ $asset->purchase_cost_formatted }}</div>
                    </div>
                    @endif
                    @if($asset->depreciation_method !== 'NONE')
                    <div class="col-md-6">
                        <label class="form-label text-muted">Depreciation Method</label>
                        <div>{{ $asset->depreciation_method === 'STRAIGHT_LINE' ? 'Straight Line' : 'None' }}</div>
                    </div>
                    @endif
                    @if($asset->book_value)
                    <div class="col-md-6">
                        <label class="form-label text-muted">Current Book Value</label>
                        <div class="fw-bold text-primary">{{ $asset->book_value_formatted }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Warranty Information -->
        @if($asset->warranty_expiry)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Warranty Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted">Warranty Expires</label>
                        <div>{{ $asset->warranty_expiry->format('M j, Y') }}</div>
                    </div>
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
                </div>
            </div>
        </div>
        @endif

        <!-- Notes -->
        @if($asset->notes)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Notes</h5>
            </div>
            <div class="card-body">
                <div class="bg-light p-3 rounded">
                    {!! nl2br(e($asset->notes)) !!}
                </div>
            </div>
        </div>
        @endif

        <!-- Tags -->
        @if($asset->tags->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Tags</h5>
            </div>
            <div class="card-body">
                @foreach($asset->tags as $tag)
                    <span class="badge me-2 mb-2" style="background-color: {{ $tag->color }}">
                        {{ $tag->name }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($asset->status === 'IN_STOCK')
                        <button class="btn btn-success btn-sm" onclick="assignAsset()">
                            <i class="bi bi-person-plus me-1"></i>Assign Asset
                        </button>
                    @elseif($asset->status === 'ASSIGNED')
                        <button class="btn btn-warning btn-sm" onclick="returnAsset()">
                            <i class="bi bi-arrow-return-left me-1"></i>Return Asset
                        </button>
                    @endif
                    <button class="btn btn-info btn-sm" onclick="addMaintenance()">
                        <i class="bi bi-tools me-1"></i>Add Maintenance
                    </button>
                    <a href="{{ route('assets.qr', $asset) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-qr-code me-1"></i>View QR Code
                    </a>
                </div>
            </div>
        </div>

        <!-- Asset History -->
        @if($asset->activities->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Recent Activity</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($asset->activities->take(5) as $activity)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">{{ $activity->description }}</h6>
                                <p class="timeline-text text-muted mb-1">
                                    {{ $activity->created_at->diffForHumans() }}
                                </p>
                                @if($activity->causer)
                                    <small class="text-muted">by {{ $activity->causer->name }}</small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Attachments section temporarily disabled until attachments table is created
        <!-- Attachments -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">Attachments</h6>
                <button class="btn btn-sm btn-outline-primary" onclick="uploadAttachment()">
                    <i class="bi bi-plus"></i>
                </button>
            </div>
            <div class="card-body">
                <p class="text-muted text-center">Attachments feature will be available soon</p>
            </div>
        </div>
        --}}
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -1.5rem;
    top: 1.5rem;
    bottom: -1.5rem;
    width: 2px;
    background-color: #dee2e6;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    top: 0.25rem;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
}

.timeline-content {
    margin-left: 0.5rem;
}

.timeline-title {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.timeline-text {
    font-size: 0.75rem;
}
</style>
@endpush

@section('scripts')
<script>
// Test if JavaScript is loading
console.log('Asset show page JavaScript loaded');
console.log('Bootstrap available:', typeof bootstrap !== 'undefined');

// Quick Actions for Asset Management
function assignAsset() {
    console.log('assignAsset() called');
    const currentStatus = '{{ $asset->status }}';
    console.log('Current status:', currentStatus);
    
    if (currentStatus === 'ASSIGNED') {
        if (confirm('This asset is already assigned. Do you want to reassign it?')) {
            showAssignModal();
        }
    } else {
        showAssignModal();
    }
}

function showAssignModal() {
    // Get users data from server-side
    const users = @json(\App\Models\User::forCompany()->select('id', 'name')->get());
    
    // Create and show a simple assign modal
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'assignModal';
    
    let userOptions = '<option value="">Select User</option>';
    users.forEach(user => {
        userOptions += `<option value="${user.id}">${user.name}</option>`;
    });
    
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Asset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('assets.update', $asset) }}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    
                    <!-- Include all required fields with current values -->
                    <input type="hidden" name="asset_code" value="{{ $asset->asset_code }}">
                    <input type="hidden" name="name" value="{{ $asset->name }}">
                    <input type="hidden" name="description" value="{{ $asset->description }}">
                    <input type="hidden" name="category_id" value="{{ $asset->category_id }}">
                    <input type="hidden" name="location_id" value="{{ $asset->location_id }}">
                    <input type="hidden" name="vendor_id" value="{{ $asset->vendor_id }}">
                    <input type="hidden" name="purchase_date" value="{{ $asset->purchase_date?->format('Y-m-d') }}">
                    <input type="hidden" name="purchase_cost" value="{{ $asset->purchase_cost }}">
                    <input type="hidden" name="depreciation_method" value="{{ $asset->depreciation_method }}">
                    <input type="hidden" name="depreciation_life_months" value="{{ $asset->depreciation_life_months }}">
                    <input type="hidden" name="warranty_expiry" value="{{ $asset->warranty_expiry?->format('Y-m-d') }}">
                    <input type="hidden" name="notes" value="{{ $asset->notes }}">
                    <input type="hidden" name="department" value="{{ $asset->department }}">
                    <input type="hidden" name="serial_number" value="{{ $asset->serial_number }}">
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Assign To</label>
                            <select class="form-select" name="assignee_id" required>
                                ${userOptions}
                            </select>
                        </div>
                        <input type="hidden" name="status" value="ASSIGNED">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Assign Asset</button>
                    </div>
                </form>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    modal.addEventListener('hidden.bs.modal', () => {
        modal.remove();
    });
}

function maintenanceMode() {
    console.log('maintenanceMode() called');
    
    if (confirm('Set this asset to maintenance mode?')) {
        // Show loading state
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Updating...';
        btn.disabled = true;
        
        updateAssetStatus('MAINTENANCE');
    }
}

function retireAsset() {
    if (confirm('Are you sure you want to retire this asset? This action can be reversed later.')) {
        updateAssetStatus('RETIRED');
    }
}

function updateAssetStatus(status) {
    console.log('updateAssetStatus() called with status:', status);
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("assets.update", $asset) }}';
    
    console.log('Form action:', form.action);
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    // Add method override
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'PUT';
    form.appendChild(methodInput);
    
    // Add all required fields with current values
    const fields = {
        'asset_code': '{{ $asset->asset_code }}',
        'name': '{{ $asset->name }}',
        'description': '{{ $asset->description }}',
        'category_id': '{{ $asset->category_id }}',
        'location_id': '{{ $asset->location_id }}',
        'vendor_id': '{{ $asset->vendor_id }}',
        'purchase_date': '{{ $asset->purchase_date?->format("Y-m-d") }}',
        'purchase_cost': '{{ $asset->purchase_cost }}',
        'depreciation_method': '{{ $asset->depreciation_method }}',
        'depreciation_life_months': '{{ $asset->depreciation_life_months }}',
        'warranty_expiry': '{{ $asset->warranty_expiry?->format("Y-m-d") }}',
        'notes': '{{ $asset->notes }}',
        'department': '{{ $asset->department }}',
        'serial_number': '{{ $asset->serial_number }}',
        'assignee_id': '{{ $asset->assignee_id }}',
        'status': status
    };
    
    Object.entries(fields).forEach(([name, value]) => {
        if (value !== '' && value !== null && value !== 'null') {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        }
    });
    
    console.log('Submitting form with all required fields');
    
    document.body.appendChild(form);
    form.submit();
}

function duplicateAsset() {
    if (confirm('Create a duplicate of this asset?')) {
        window.location.href = '{{ route("assets.create") }}?duplicate={{ $asset->id }}';
    }
}

function printLabel() {
    // Open QR code in new window for printing
    window.open('{{ route("assets.qr", $asset) }}', '_blank', 'width=600,height=700');
}

function returnAsset() {
    if (confirm('Mark this asset as returned and available?')) {
        updateAssetStatus('IN_STOCK');
    }
}

function addMaintenance() {
    // Create maintenance log modal
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Maintenance Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Maintenance Type</label>
                        <select class="form-select">
                            <option>Routine Maintenance</option>
                            <option>Repair</option>
                            <option>Inspection</option>
                            <option>Upgrade</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" rows="3" placeholder="Describe the maintenance work..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cost</label>
                        <input type="number" class="form-control" step="0.01" placeholder="0.00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="alert('Maintenance logging will be implemented')">Save Record</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    new bootstrap.Modal(modal).show();
    modal.addEventListener('hidden.bs.modal', () => modal.remove());
}

function uploadAttachment() {
    alert('Attachment functionality is temporarily disabled until the attachments table is created.');
}

// Status change notifications
@if(session('success'))
    // Show toast notification
    setTimeout(() => {
        const toast = document.createElement('div');
        toast.className = 'toast show position-fixed top-0 end-0 m-3';
        toast.innerHTML = `
            <div class="toast-header">
                <strong class="me-auto text-success">Success</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">{{ session('success') }}</div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }, 100);
@endif
</script>
@endsection
