@extends('layouts.app')

@section('title', 'Assets Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Assets Management</h1>
        <p class="text-muted">Track and manage your company assets</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('assets.create') }}" class="btn btn-primary">
            <i class="bi bi-plus me-1"></i>Add Asset
        </a>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots"></i>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('assets.export') }}">
                    <i class="bi bi-download me-2"></i>Export Assets
                </a></li>
                <li><a class="dropdown-item" href="{{ route('assets.import.form') }}">
                    <i class="bi bi-upload me-2"></i>Import Assets
                </a></li>
                <li><a class="dropdown-item" href="{{ route('assets.template') }}">
                    <i class="bi bi-file-spreadsheet me-2"></i>Download Template
                </a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('assets.index') }}" class="row g-3">
            <div class="col-md-3">
                <input type="text" class="form-control" name="search" placeholder="Search assets..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(\App\Models\Asset::getStatuses() as $key => $label)
                        <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="location_id" class="form-select">
                    <option value="">All Locations</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                            {{ $location->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="assignee_id" class="form-select">
                    <option value="">All Assignees</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('assignee_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Assets Table -->
<div class="card">
    <div class="card-body">
        @if($assets->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>Asset Code</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Location</th>
                            <th>Assigned To</th>
                            <th>Purchase Cost</th>
                            <th>Book Value</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assets as $asset)
                            <tr>
                                <td><input type="checkbox" class="asset-checkbox" value="{{ $asset->id }}"></td>
                                <td>
                                    <strong>{{ $asset->asset_code }}</strong>
                                    @if($asset->serial_number)
                                        <br><small class="text-muted">SN: {{ $asset->serial_number }}</small>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('assets.show', $asset) }}" class="text-decoration-none">
                                        <strong>{{ $asset->name }}</strong>
                                    </a>
                                    @if($asset->description)
                                        <br><small class="text-muted">{{ Str::limit($asset->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($asset->category)
                                        <span class="badge" style="background-color: {{ $asset->category->color }}">
                                            <i class="{{ $asset->category->icon }} me-1"></i>{{ $asset->category->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">No category</span>
                                    @endif
                                </td>
                                <td>
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
                                </td>
                                <td>{{ $asset->location?->name ?? 'Not assigned' }}</td>
                                <td>{{ $asset->assignee?->name ?? 'Unassigned' }}</td>
                                <td>{{ $asset->purchase_cost_formatted ?? 'N/A' }}</td>
                                <td>{{ $asset->book_value_formatted ?? 'N/A' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('assets.show', $asset) }}" class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('assets.qr', $asset) }}" class="btn btn-outline-info" title="QR Code">
                                            <i class="bi bi-qr-code"></i>
                                        </a>
                                        <a href="{{ route('assets.edit', $asset) }}" class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('assets.destroy', $asset) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete" 
                                                    onclick="return confirm('Are you sure?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Showing {{ $assets->firstItem() }} to {{ $assets->lastItem() }} of {{ $assets->total() }} assets
                </div>
                {{ $assets->links() }}
            </div>

            <!-- Bulk Actions -->
            <div class="mt-3" id="bulk-actions" style="display: none;">
                <div class="alert alert-info">
                    <span id="selected-count">0</span> assets selected
                    <div class="btn-group ms-3">
                        <button type="button" class="btn btn-sm btn-danger" onclick="bulkDelete()">
                            <i class="bi bi-trash me-1"></i>Delete Selected
                        </button>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-box display-4 text-muted"></i>
                <h5 class="mt-3">No Assets Found</h5>
                <p class="text-muted">Start by adding your first asset to the system.</p>
                <a href="{{ route('assets.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus me-1"></i>Add First Asset
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Select all functionality
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.asset-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateBulkActions();
});

// Individual checkbox functionality
document.querySelectorAll('.asset-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkActions);
});

function updateBulkActions() {
    const checkedBoxes = document.querySelectorAll('.asset-checkbox:checked');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    
    if (checkedBoxes.length > 0) {
        bulkActions.style.display = 'block';
        selectedCount.textContent = checkedBoxes.length;
    } else {
        bulkActions.style.display = 'none';
    }
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.asset-checkbox:checked');
    const ids = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (confirm(`Are you sure you want to delete ${ids.length} assets?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("assets.bulk-delete") }}';
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        // Add asset IDs
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'asset_ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection
