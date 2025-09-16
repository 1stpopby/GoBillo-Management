<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Site Allocations</h5>
    @if(auth()->user()->canManageCompanyUsers())
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#allocateModal">
            <i class="bi bi-plus-circle me-1"></i>Allocate to Site
        </button>
    @endif
</div>

@if($employee->activeSiteAllocations->count() > 0)
    <div class="row">
        @foreach($employee->activeSiteAllocations as $allocation)
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <a href="{{ route('sites.show', $allocation->site) }}" class="text-decoration-none">
                                {{ $allocation->site->name }}
                            </a>
                        </h6>
                        @if(auth()->user()->canManageCompanyUsers())
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" type="button" 
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('sites.show', $allocation->site) }}">
                                        <i class="bi bi-eye me-2"></i>View Site
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="editAllocation({{ $allocation->id }})">
                                        <i class="bi bi-pencil me-2"></i>Edit Allocation
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('employees.remove-from-site', [$employee, $allocation]) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" 
                                                    onclick="return confirm('Are you sure you want to remove this allocation?')">
                                                <i class="bi bi-trash me-2"></i>Remove Allocation
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="detail-group">
                                    <label class="detail-label">Allocation Type</label>
                                    <div class="detail-value">
                                        <span class="badge bg-{{ $allocation->allocation_type === 'primary' ? 'success' : ($allocation->allocation_type === 'secondary' ? 'warning' : 'info') }}">
                                            {{ ucfirst($allocation->allocation_type) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="detail-group">
                                    <label class="detail-label">Allocation %</label>
                                    <div class="detail-value">
                                        <span class="badge bg-light text-dark">{{ $allocation->allocation_percentage }}%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="detail-group">
                                    <label class="detail-label">Start Date</label>
                                    <div class="detail-value">{{ $allocation->allocated_from->format('M j, Y') }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="detail-group">
                                    <label class="detail-label">End Date</label>
                                    <div class="detail-value">
                                        @if($allocation->allocated_until)
                                            {{ $allocation->allocated_until->format('M j, Y') }}
                                        @else
                                            <span class="text-muted">Ongoing</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if($allocation->responsibilities)
                                <div class="col-12">
                                    <div class="detail-group">
                                        <label class="detail-label">Responsibilities</label>
                                        <div class="detail-value">{{ $allocation->responsibilities }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Site Quick Info -->
                        <div class="mt-3 pt-3 border-top">
                            <div class="row g-2 small text-muted">
                                @if($allocation->site->address)
                                    <div class="col-12">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $allocation->site->address }}
                                    </div>
                                @endif
                                @if($allocation->site->client)
                                    <div class="col-6">
                                        <i class="bi bi-building me-1"></i>{{ $allocation->site->client->name }}
                                    </div>
                                @endif
                                <div class="col-6">
                                    <i class="bi bi-calendar me-1"></i>Started {{ $allocation->site->start_date?->format('M j, Y') ?? 'TBD' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Site Performance Summary -->
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="mb-0">Site Performance Summary</h6>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="fs-4 fw-bold text-primary">{{ $employee->activeSiteAllocations->count() }}</div>
                        <div class="text-muted small">Active Sites</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="fs-4 fw-bold text-success">
                            {{ $employee->activeSiteAllocations->where('allocation_type', 'primary')->count() }}
                        </div>
                        <div class="text-muted small">Primary Allocations</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="fs-4 fw-bold text-warning">
                            {{ $employee->activeSiteAllocations->sum('allocation_percentage') }}%
                        </div>
                        <div class="text-muted small">Total Allocation</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="fs-4 fw-bold text-info">
                            {{ $employee->activeSiteAllocations->where('allocated_until', null)->count() }}
                        </div>
                        <div class="text-muted small">Ongoing</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-geo-alt display-1 text-muted"></i>
        <h5 class="mt-3">No Site Allocations</h5>
        <p class="text-muted">This employee is not currently allocated to any sites.</p>
        @if(auth()->user()->canManageCompanyUsers())
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#allocateModal">
                <i class="bi bi-plus-circle me-2"></i>Allocate to Site
            </button>
        @endif
    </div>
@endif

<script>
function editAllocation(allocationId) {
    // This would open an edit modal - placeholder for future implementation
    alert('Edit allocation functionality coming soon!');
}
</script>


