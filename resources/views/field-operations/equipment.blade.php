@extends('layouts.app')

@section('title', 'Equipment Management')

@section('content')
<div class="equipment-container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('field-operations.index') }}">Field Operations</a></li>
                        <li class="breadcrumb-item active">Equipment</li>
                    </ol>
                </nav>
                <h1 class="page-title">
                    <i class="bi bi-gear-fill me-3 text-primary"></i>Equipment Management
                </h1>
                <p class="page-subtitle">Track and manage all field equipment and machinery</p>
            </div>
            <div class="col-lg-6 text-end">
                <div class="btn-group">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                        <i class="bi bi-plus-circle me-2"></i>Add Equipment
                    </button>
                    <button class="btn btn-outline-success">
                        <i class="bi bi-wrench me-2"></i>Schedule Maintenance
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipment Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stats-card bg-success">
                <div class="stats-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $equipment->where('status', 'active')->count() }}</h3>
                    <p>Active Equipment</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-warning">
                <div class="stats-icon">
                    <i class="bi bi-wrench"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $equipment->where('status', 'maintenance')->count() }}</h3>
                    <p>In Maintenance</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-info">
                <div class="stats-icon">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $equipment->sum('hours_used') }}</h3>
                    <p>Total Hours Used</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-primary">
                <div class="stats-icon">
                    <i class="bi bi-gear"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $equipment->count() }}</h3>
                    <p>Total Equipment</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipment List -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list me-2"></i>Equipment Inventory
                    </h5>
                </div>
                <div class="col-auto">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary active" data-filter="all">All</button>
                        <button class="btn btn-outline-secondary" data-filter="active">Active</button>
                        <button class="btn btn-outline-secondary" data-filter="maintenance">Maintenance</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-4">
                @foreach($equipment as $item)
                    <div class="col-lg-6 equipment-item" data-status="{{ $item->status }}">
                        <div class="equipment-card">
                            <div class="equipment-header">
                                <div class="equipment-status">
                                    <span class="status-indicator bg-{{ $item->status === 'active' ? 'success' : ($item->status === 'maintenance' ? 'warning' : 'danger') }}"></span>
                                    <span class="status-text text-{{ $item->status === 'active' ? 'success' : ($item->status === 'maintenance' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </div>
                                <div class="equipment-actions">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i>View Details</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="bi bi-wrench me-2"></i>Schedule Maintenance</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash me-2"></i>Remove</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="equipment-body">
                                <div class="equipment-icon">
                                    <i class="bi bi-{{ $item->type === 'Heavy Machinery' ? 'gear-fill' : 'truck' }} text-primary"></i>
                                </div>
                                <div class="equipment-info">
                                    <h5 class="equipment-name">{{ $item->name }}</h5>
                                    <p class="equipment-type text-muted">{{ $item->type }}</p>
                                    <div class="equipment-location">
                                        <i class="bi bi-geo-alt text-muted me-1"></i>
                                        <span class="text-muted">{{ $item->location }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="equipment-details">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Operator</div>
                                            <div class="detail-value">
                                                @if($item->operator)
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                            {{ substr($item->operator, 0, 1) }}
                                                        </div>
                                                        <span class="small">{{ $item->operator }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Not assigned</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Hours Used</div>
                                            <div class="detail-value">{{ number_format($item->hours_used) }}h</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Fuel Level</div>
                                            <div class="detail-value">
                                                <div class="fuel-indicator">
                                                    <div class="fuel-bar">
                                                        <div class="fuel-fill bg-{{ $item->fuel_level > 50 ? 'success' : ($item->fuel_level > 25 ? 'warning' : 'danger') }}" 
                                                             style="width: {{ $item->fuel_level }}%"></div>
                                                    </div>
                                                    <span class="fuel-text">{{ $item->fuel_level }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Next Maintenance</div>
                                            <div class="detail-value">
                                                <span class="text-{{ $item->next_maintenance->isPast() ? 'danger' : ($item->next_maintenance->diffInDays() <= 7 ? 'warning' : 'success') }}">
                                                    {{ $item->next_maintenance->format('M j, Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($item->next_maintenance->diffInDays() <= 7)
                                <div class="equipment-alert">
                                    <div class="alert alert-warning mb-0">
                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                        <strong>Maintenance Due:</strong> 
                                        @if($item->next_maintenance->isPast())
                                            Overdue by {{ $item->next_maintenance->diffInDays() }} days
                                        @else
                                            Due in {{ $item->next_maintenance->diffInDays() }} days
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($equipment->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-gear text-muted" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mt-3">No Equipment Found</h4>
                    <p class="text-muted">Add your first piece of equipment to get started</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                        <i class="bi bi-plus-circle me-2"></i>Add Equipment
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.equipment-container {
    max-width: 100%;
}

.stats-card {
    padding: 1.5rem;
    border-radius: 12px;
    color: white;
    text-decoration: none;
    display: block;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transform: translate(30px, -30px);
}

.stats-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    opacity: 0.8;
}

.stats-content h3 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 0.25rem 0;
}

.stats-content p {
    margin: 0;
    font-size: 0.875rem;
    opacity: 0.9;
}

.equipment-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: white;
    overflow: hidden;
    transition: all 0.2s ease;
    height: 100%;
}

.equipment-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.equipment-header {
    display: flex;
    justify-content: between;
    align-items: center;
    padding: 1rem 1.5rem 0.5rem;
}

.equipment-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.status-text {
    font-size: 0.875rem;
    font-weight: 600;
}

.equipment-actions {
    margin-left: auto;
}

.equipment-body {
    padding: 0.5rem 1.5rem 1rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.equipment-icon {
    font-size: 2.5rem;
    opacity: 0.8;
}

.equipment-info {
    flex: 1;
}

.equipment-name {
    font-size: 1.125rem;
    font-weight: 600;
    margin: 0 0 0.25rem 0;
    color: #1f2937;
}

.equipment-type {
    font-size: 0.875rem;
    margin: 0 0 0.5rem 0;
}

.equipment-location {
    font-size: 0.875rem;
}

.equipment-details {
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    border-top: 1px solid #e5e7eb;
}

.detail-item {
    margin-bottom: 0.75rem;
}

.detail-item:last-child {
    margin-bottom: 0;
}

.detail-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.detail-value {
    font-size: 0.875rem;
    color: #1f2937;
}

.fuel-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.fuel-bar {
    flex: 1;
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
}

.fuel-fill {
    height: 100%;
    transition: width 0.3s ease;
}

.fuel-text {
    font-size: 0.75rem;
    font-weight: 600;
    min-width: 35px;
}

.equipment-alert {
    padding: 0 1.5rem 1rem;
}

.avatar-sm {
    width: 20px;
    height: 20px;
    font-size: 10px;
    font-weight: 600;
}

/* Filter functionality */
.equipment-item.hidden {
    display: none;
}

@media (max-width: 768px) {
    .equipment-body {
        flex-direction: column;
        text-align: center;
    }
    
    .equipment-details .row {
        text-align: left;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Equipment filtering
    const filterButtons = document.querySelectorAll('[data-filter]');
    const equipmentItems = document.querySelectorAll('.equipment-item');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter equipment items
            equipmentItems.forEach(item => {
                const status = item.getAttribute('data-status');
                if (filter === 'all' || status === filter) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        });
    });
});
</script>
@endsection
