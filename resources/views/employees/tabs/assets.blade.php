<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Assigned Assets</h5>
    @if(auth()->user()->canManageCompanyUsers())
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignAssetModal">
            <i class="bi bi-plus-circle me-1"></i>Assign Asset
        </button>
    @endif
</div>

@if($assignedAssets->count() > 0)
    <div class="row">
        @foreach($assignedAssets as $asset)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <a href="{{ route('assets.show', $asset) }}" class="text-decoration-none">
                                {{ $asset->name }}
                            </a>
                        </h6>
                        <span class="badge bg-{{ $asset->status === 'ASSIGNED' ? 'success' : ($asset->status === 'MAINTENANCE' ? 'warning' : 'secondary') }}">
                            {{ $asset->status_label }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="detail-group">
                                    <label class="detail-label">Asset Code</label>
                                    <div class="detail-value font-monospace">{{ $asset->asset_code }}</div>
                                </div>
                            </div>
                            @if($asset->category)
                                <div class="col-6">
                                    <div class="detail-group">
                                        <label class="detail-label">Category</label>
                                        <div class="detail-value">
                                            <span class="badge bg-primary">{{ $asset->category->name }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($asset->location)
                                <div class="col-6">
                                    <div class="detail-group">
                                        <label class="detail-label">Location</label>
                                        <div class="detail-value">{{ $asset->location->name }}</div>
                                    </div>
                                </div>
                            @endif
                            @if($asset->serial_number)
                                <div class="col-12">
                                    <div class="detail-group">
                                        <label class="detail-label">Serial Number</label>
                                        <div class="detail-value font-monospace">{{ $asset->serial_number }}</div>
                                    </div>
                                </div>
                            @endif
                            @if($asset->purchase_cost)
                                <div class="col-6">
                                    <div class="detail-group">
                                        <label class="detail-label">Value</label>
                                        <div class="detail-value">${{ $asset->purchase_cost_formatted }}</div>
                                    </div>
                                </div>
                            @endif
                            @if($asset->warranty_expiry)
                                <div class="col-6">
                                    <div class="detail-group">
                                        <label class="detail-label">Warranty</label>
                                        <div class="detail-value">
                                            @if($asset->warranty_expiry->isFuture())
                                                <span class="text-success">{{ $asset->warranty_expiry->format('M j, Y') }}</span>
                                            @else
                                                <span class="text-danger">Expired</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        @if($asset->description)
                            <div class="mt-3 pt-3 border-top">
                                <small class="text-muted">{{ $asset->description }}</small>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Assigned {{ $asset->updated_at->diffForHumans() }}
                            </small>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('assets.show', $asset) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('assets.qr', $asset) }}" class="btn btn-outline-secondary btn-sm" 
                                   title="QR Code">
                                    <i class="bi bi-qr-code"></i>
                                </a>
                                @if(auth()->user()->canManageCompanyUsers())
                                    <button class="btn btn-outline-danger btn-sm" 
                                            onclick="unassignAsset({{ $asset->id }})" title="Unassign">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Asset Summary -->
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="mb-0">Asset Summary</h6>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="fs-4 fw-bold text-primary">{{ $assignedAssets->count() }}</div>
                        <div class="text-muted small">Total Assets</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="fs-4 fw-bold text-success">
                            {{ $assignedAssets->where('status', 'ASSIGNED')->count() }}
                        </div>
                        <div class="text-muted small">Active</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="fs-4 fw-bold text-warning">
                            {{ $assignedAssets->where('status', 'MAINTENANCE')->count() }}
                        </div>
                        <div class="text-muted small">In Maintenance</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="fs-4 fw-bold text-info">
                            ${{ number_format($assignedAssets->sum('purchase_cost'), 0) }}
                        </div>
                        <div class="text-muted small">Total Value</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-4">
        <a href="{{ route('assets.index') }}?assignee={{ $employee->user_id }}" class="btn btn-outline-primary">
            View All Assets
        </a>
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-laptop display-1 text-muted"></i>
        <h5 class="mt-3">No Assigned Assets</h5>
        <p class="text-muted">This employee doesn't have any assets assigned to them yet.</p>
        @if(auth()->user()->canManageCompanyUsers())
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignAssetModal">
                <i class="bi bi-plus-circle me-2"></i>Assign First Asset
            </button>
        @endif
    </div>
@endif

<!-- Assign Asset Modal -->
@if(auth()->user()->canManageCompanyUsers())
    <div class="modal fade" id="assignAssetModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Asset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('assets.assign') }}" method="POST">
                    @csrf
                    <input type="hidden" name="assignee_id" value="{{ $employee->user_id }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="asset_id" class="form-label">Asset <span class="text-danger">*</span></label>
                            <select class="form-select" id="asset_id" name="asset_id" required>
                                <option value="">Select asset...</option>
                                @foreach(\App\Models\Asset::forCompany()->where('status', 'IN_STOCK')->orderBy('name')->get() as $availableAsset)
                                    <option value="{{ $availableAsset->id }}">
                                        {{ $availableAsset->name }} ({{ $availableAsset->asset_code }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Only available assets are shown.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="assignment_notes" class="form-label">Assignment Notes</label>
                            <textarea class="form-control" id="assignment_notes" name="notes" rows="3" 
                                      placeholder="Optional notes about this assignment"></textarea>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="send_notification" name="send_notification" checked>
                            <label class="form-check-label" for="send_notification">
                                Send notification to employee
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Assign Asset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<script>
function unassignAsset(assetId) {
    if (confirm('Are you sure you want to unassign this asset? It will be returned to inventory.')) {
        fetch(`/assets/${assetId}/unassign`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                status: 'IN_STOCK',
                assignee_id: null
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error unassigning asset: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while unassigning the asset.');
        });
    }
}
</script>


