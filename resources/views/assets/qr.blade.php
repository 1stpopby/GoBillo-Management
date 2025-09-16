@extends('layouts.app')

@section('title', 'QR Code - ' . $asset->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('assets.index') }}">Assets</a></li>
                <li class="breadcrumb-item"><a href="{{ route('assets.show', $asset) }}">{{ $asset->asset_code }}</a></li>
                <li class="breadcrumb-item active">QR Code</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">QR Code</h1>
        <p class="text-muted">{{ $asset->asset_code }} - {{ $asset->name }}</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('assets.download-qr', $asset) }}" class="btn btn-primary">
            <i class="bi bi-download me-1"></i>Download QR Code
        </a>
        <a href="{{ route('assets.show', $asset) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Asset
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title mb-4">Asset QR Code</h5>
                
                <!-- QR Code Display -->
                <div class="mb-4">
                    <div class="bg-white p-4 rounded shadow-sm d-inline-block">
                        {!! $qrCode !!}
                    </div>
                </div>

                <!-- Asset Information -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded">
                            <h6 class="text-muted mb-1">Asset Code</h6>
                            <div class="fw-bold">{{ $asset->asset_code }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded">
                            <h6 class="text-muted mb-1">Asset Name</h6>
                            <div class="fw-bold">{{ $asset->name }}</div>
                        </div>
                    </div>
                    @if($asset->location)
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded">
                            <h6 class="text-muted mb-1">Location</h6>
                            <div>{{ $asset->location->name }}</div>
                        </div>
                    </div>
                    @endif
                    @if($asset->assignee)
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded">
                            <h6 class="text-muted mb-1">Assigned To</h6>
                            <div>{{ $asset->assignee->name }}</div>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded">
                            <h6 class="text-muted mb-1">Status</h6>
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
                    </div>
                    @if($asset->category)
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded">
                            <h6 class="text-muted mb-1">Category</h6>
                            <div>
                                <span class="badge" style="background-color: {{ $asset->category->color }}">
                                    <i class="{{ $asset->category->icon }} me-1"></i>{{ $asset->category->name }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Instructions -->
                <div class="alert alert-info">
                    <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>How to use this QR Code</h6>
                    <ul class="mb-0 text-start">
                        <li>Scan this QR code with any smartphone camera or QR scanner app</li>
                        <li>The QR code will direct you to this asset's details page</li>
                        <li>Use for quick asset identification and tracking</li>
                        <li>Print and attach to the physical asset for easy scanning</li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('assets.download-qr', $asset) }}" class="btn btn-success">
                        <i class="bi bi-download me-1"></i>Download PNG
                    </a>
                    <button onclick="printQR()" class="btn btn-outline-primary">
                        <i class="bi bi-printer me-1"></i>Print QR Code
                    </button>
                    <a href="{{ route('assets.edit', $asset) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-pencil me-1"></i>Edit Asset
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar with QR Tips -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">QR Code Tips</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3">
                        <i class="bi bi-lightbulb text-warning me-2"></i>
                        <strong>Printing:</strong> Print on weather-resistant labels for outdoor assets
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-lightbulb text-warning me-2"></i>
                        <strong>Size:</strong> Ensure QR code is at least 1 inch (2.5cm) square for easy scanning
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-lightbulb text-warning me-2"></i>
                        <strong>Placement:</strong> Attach in a visible, accessible location on the asset
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-lightbulb text-warning me-2"></i>
                        <strong>Testing:</strong> Test the QR code after printing to ensure it scans correctly
                    </li>
                    <li>
                        <i class="bi bi-lightbulb text-warning me-2"></i>
                        <strong>Backup:</strong> Keep digital copies for reprinting if labels get damaged
                    </li>
                </ul>
            </div>
        </div>

        <!-- Asset Quick Info -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Asset Summary</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <i class="bi bi-box display-4 text-muted"></i>
                </div>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Created:</span>
                        <span>{{ $asset->created_at->format('M j, Y') }}</span>
                    </li>
                    @if($asset->purchase_date)
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Purchased:</span>
                        <span>{{ $asset->purchase_date->format('M j, Y') }}</span>
                    </li>
                    @endif
                    @if($asset->purchase_cost)
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Cost:</span>
                        <span>{{ $asset->purchase_cost_formatted }}</span>
                    </li>
                    @endif
                    @if($asset->serial_number)
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Serial:</span>
                        <span class="font-monospace">{{ $asset->serial_number }}</span>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }
}

.qr-code-container {
    page-break-inside: avoid;
}
</style>
@endpush

@push('scripts')
<script>
function printQR() {
    // Hide non-essential elements for printing
    const elementsToHide = document.querySelectorAll('.no-print, .breadcrumb, .btn-group, .alert, .card-header');
    elementsToHide.forEach(el => el.style.display = 'none');
    
    // Print the page
    window.print();
    
    // Restore hidden elements after printing
    setTimeout(() => {
        elementsToHide.forEach(el => el.style.display = '');
    }, 1000);
}

// Add print classes to elements that should be hidden during print
document.addEventListener('DOMContentLoaded', function() {
    const printHideElements = document.querySelectorAll('.btn-group, .alert-info, .breadcrumb');
    printHideElements.forEach(el => el.classList.add('no-print'));
});
</script>
@endpush
@endsection


