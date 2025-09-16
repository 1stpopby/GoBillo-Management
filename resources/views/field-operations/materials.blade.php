@extends('layouts.app')

@section('title', 'Material Tracking')

@section('content')
<div class="materials-container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('field-operations.index') }}">Field Operations</a></li>
                        <li class="breadcrumb-item active">Materials</li>
                    </ol>
                </nav>
                <h1 class="page-title">
                    <i class="bi bi-boxes me-3 text-warning"></i>Material Tracking
                </h1>
                <p class="page-subtitle">Track inventory, orders, and material deliveries</p>
            </div>
            <div class="col-lg-6 text-end">
                <div class="btn-group">
                    <button class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Add Material
                    </button>
                    <button class="btn btn-outline-success">
                        <i class="bi bi-truck me-2"></i>New Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Material Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stats-card bg-success">
                <div class="stats-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $materials->where('status', 'delivered')->count() }}</h3>
                    <p>Delivered</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-warning">
                <div class="stats-icon">
                    <i class="bi bi-truck"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $materials->where('status', 'ordered')->count() }}</h3>
                    <p>On Order</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-info">
                <div class="stats-icon">
                    <i class="bi bi-currency-pound"></i>
                </div>
                <div class="stats-content">
                    <h3>£{{ number_format($materials->sum('total_cost'), 0) }}</h3>
                    <p>Total Value</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-primary">
                <div class="stats-icon">
                    <i class="bi bi-boxes"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $materials->count() }}</h3>
                    <p>Total Items</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Materials List -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-list me-2"></i>Material Inventory
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Material</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Location</th>
                            <th>Supplier</th>
                            <th>Cost</th>
                            <th>Delivery Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materials as $material)
                            <tr>
                                <td>
                                    <div class="material-info">
                                        <h6 class="mb-1">{{ $material->name }}</h6>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $material->category }}</span>
                                </td>
                                <td>
                                    <strong>{{ number_format($material->quantity) }}</strong> {{ $material->unit }}
                                </td>
                                <td>
                                    <div class="location-info">
                                        <i class="bi bi-geo-alt text-muted me-1"></i>
                                        <span class="text-muted small">{{ $material->location }}</span>
                                    </div>
                                </td>
                                <td>{{ $material->supplier }}</td>
                                <td>
                                    <div class="cost-info">
                                        <div class="fw-bold">£{{ number_format($material->total_cost, 2) }}</div>
                                        <small class="text-muted">£{{ number_format($material->cost_per_unit, 2) }}/{{ $material->unit }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="{{ $material->delivery_date->isPast() ? 'text-success' : 'text-warning' }}">
                                        {{ $material->delivery_date->format('M j, Y') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $material->status === 'delivered' ? 'success' : 'warning' }}">
                                        {{ ucfirst($material->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i>View Details</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="bi bi-truck me-2"></i>Track Delivery</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash me-2"></i>Remove</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.materials-container {
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

.material-info h6 {
    font-size: 0.875rem;
    font-weight: 600;
    margin: 0;
}

.location-info {
    font-size: 0.875rem;
}

.cost-info {
    text-align: right;
}
</style>
@endsection
