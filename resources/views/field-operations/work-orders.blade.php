@extends('layouts.app')

@section('title', 'Work Orders')

@section('content')
<div class="work-orders-container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('field-operations.index') }}">Field Operations</a></li>
                        <li class="breadcrumb-item active">Work Orders</li>
                    </ol>
                </nav>
                <h1 class="page-title">
                    <i class="bi bi-clipboard-check me-3 text-success"></i>Work Orders
                </h1>
                <p class="page-subtitle">Manage and track work assignments and progress</p>
            </div>
            <div class="col-lg-6 text-end">
                <div class="btn-group">
                    <button class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Work Order
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="bi bi-funnel me-2"></i>Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Work Order Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stats-card bg-primary">
                <div class="stats-icon">
                    <i class="bi bi-play-circle"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $workOrders->where('status', 'in_progress')->count() }}</h3>
                    <p>In Progress</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-warning">
                <div class="stats-icon">
                    <i class="bi bi-clock"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $workOrders->where('status', 'pending')->count() }}</h3>
                    <p>Pending</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-success">
                <div class="stats-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $workOrders->where('status', 'completed')->count() }}</h3>
                    <p>Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card bg-info">
                <div class="stats-icon">
                    <i class="bi bi-clipboard"></i>
                </div>
                <div class="stats-content">
                    <h3>{{ $workOrders->count() }}</h3>
                    <p>Total Orders</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Work Orders List -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-list me-2"></i>Work Orders
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                @foreach($workOrders as $order)
                    <div class="col-lg-6">
                        <div class="work-order-card">
                            <div class="order-header">
                                <div class="order-priority">
                                    <span class="priority-indicator priority-{{ $order->priority }}">
                                        {{ ucfirst($order->priority) }} Priority
                                    </span>
                                </div>
                                <div class="order-status">
                                    <span class="status-badge status-{{ str_replace(' ', '_', $order->status) }}">
                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="order-body">
                                <h5 class="order-title">{{ $order->title }}</h5>
                                <div class="order-project">
                                    <i class="bi bi-building text-muted me-2"></i>
                                    <span class="text-muted">{{ $order->project }}</span>
                                </div>
                                
                                <div class="order-details mt-3">
                                    <div class="detail-row">
                                        <div class="detail-item">
                                            <i class="bi bi-people text-muted me-2"></i>
                                            <span class="text-muted">{{ $order->assigned_to }}</span>
                                        </div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-item">
                                            <i class="bi bi-calendar text-muted me-2"></i>
                                            <span class="text-muted">Due: {{ $order->due_date->format('M j, Y') }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="bi bi-clock text-muted me-2"></i>
                                            <span class="text-muted">{{ $order->actual_hours }}h / {{ $order->estimated_hours }}h</span>
                                        </div>
                                    </div>
                                </div>

                                @if($order->description)
                                    <div class="order-description mt-3">
                                        <p class="text-muted">{{ $order->description }}</p>
                                    </div>
                                @endif

                                <!-- Progress Bar -->
                                <div class="progress-section mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small text-muted">Progress</span>
                                        <span class="small fw-bold">{{ round(($order->actual_hours / $order->estimated_hours) * 100) }}%</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'in_progress' ? 'primary' : 'secondary') }}" 
                                             style="width: {{ min(100, ($order->actual_hours / $order->estimated_hours) * 100) }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="order-footer">
                                <div class="order-dates">
                                    <small class="text-muted">
                                        Created {{ $order->created_date->format('M j') }}
                                        @if($order->due_date->isPast() && $order->status !== 'completed')
                                            <span class="text-danger ms-2">• Overdue</span>
                                        @endif
                                    </small>
                                </div>
                                <div class="order-actions">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i>View
                                        </button>
                                        <button class="btn btn-outline-secondary">
                                            <i class="bi bi-pencil me-1"></i>Edit
                                        </button>
                                        @if($order->status !== 'completed')
                                            <button class="btn btn-outline-success">
                                                <i class="bi bi-check me-1"></i>Complete
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($workOrders->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-clipboard-check text-muted" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mt-3">No Work Orders</h4>
                    <p class="text-muted">Create your first work order to get started</p>
                    <button class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Work Order
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.work-orders-container {
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

.work-order-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: white;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 1.5rem 1rem;
    border-bottom: 1px solid #f3f4f6;
}

.priority-indicator {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
}

.priority-low {
    background: #d1fae5;
    color: #065f46;
}

.priority-medium {
    background: #fef3c7;
    color: #92400e;
}

.priority-high {
    background: #fee2e2;
    color: #991b1b;
}

.status-badge {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
}

.status-pending {
    background: #f59e0b;
    color: white;
}

.status-in_progress {
    background: #3b82f6;
    color: white;
}

.status-completed {
    background: #10b981;
    color: white;
}

.order-body {
    padding: 1rem 1.5rem;
    flex: 1;
}

.order-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.order-project {
    margin-bottom: 1rem;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.detail-item {
    display: flex;
    align-items: center;
    font-size: 0.875rem;
}

.order-description p {
    font-size: 0.875rem;
    line-height: 1.5;
    margin: 0;
}

.progress-section {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
}

.order-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-top: 1px solid #f3f4f6;
    background: #f8f9fa;
}

@media (max-width: 768px) {
    .detail-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .order-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
}
</style>
@endsection
