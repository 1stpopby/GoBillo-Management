@extends('layouts.app')

@section('title', 'Induction Details - ' . $induction->inductee_name)

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="header-icon bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                        <i class="bi bi-person-check fs-3"></i>
                    </div>
                    <div>
                        <nav aria-label="breadcrumb" class="mb-2">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('health-safety.index') }}">Health & Safety</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('health-safety.inductions') }}">Site Inductions</a></li>
                                <li class="breadcrumb-item active">{{ $induction->inductee_name }}</li>
                            </ol>
                        </nav>
                        <h1 class="page-title mb-1 fw-bold">Site Induction Details</h1>
                        <p class="page-subtitle text-muted mb-0">Certificate: {{ $induction->certificate_number }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-end">
                <div class="btn-group">
                    <a href="{{ route('health-safety.inductions.certificate', $induction) }}" class="btn btn-primary">
                        <i class="bi bi-download me-2"></i>Download Certificate
                    </a>
                    <a href="{{ route('health-safety.inductions') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Alert -->
    @php
        $isExpired = $induction->valid_until < now();
        $isExpiringSoon = $induction->valid_until <= now()->addDays(30) && !$isExpired;
    @endphp
    
    @if($isExpired)
        <div class="alert alert-danger border-0 mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle fs-4 me-3"></i>
                <div>
                    <h6 class="alert-heading mb-1">Induction Expired</h6>
                    <p class="mb-0">This induction certificate expired on {{ $induction->valid_until->format('M j, Y') }}. The inductee must complete a new induction to access the site.</p>
                </div>
            </div>
        </div>
    @elseif($isExpiringSoon)
        <div class="alert alert-warning border-0 mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-clock fs-4 me-3"></i>
                <div>
                    <h6 class="alert-heading mb-1">Induction Expiring Soon</h6>
                    <p class="mb-0">This induction certificate will expire on {{ $induction->valid_until->format('M j, Y') }} ({{ $induction->valid_until->diffForHumans() }}).</p>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Inductee Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-person text-success me-2"></i>
                        Inductee Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="form-label text-muted">Full Name</label>
                                <div class="fw-bold">{{ $induction->inductee_name }}</div>
                            </div>
                            
                            @if($induction->inductee_company)
                                <div class="info-item mb-3">
                                    <label class="form-label text-muted">Company</label>
                                    <div class="fw-bold">{{ $induction->inductee_company }}</div>
                                </div>
                            @endif
                            
                            @if($induction->inductee_role)
                                <div class="info-item mb-3">
                                    <label class="form-label text-muted">Role/Position</label>
                                    <div class="fw-bold">{{ $induction->inductee_role }}</div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($induction->inductee_phone)
                                <div class="info-item mb-3">
                                    <label class="form-label text-muted">Phone Number</label>
                                    <div class="fw-bold">
                                        <i class="bi bi-telephone me-2"></i>{{ $induction->inductee_phone }}
                                    </div>
                                </div>
                            @endif
                            
                            @if($induction->inductee_email)
                                <div class="info-item mb-3">
                                    <label class="form-label text-muted">Email Address</label>
                                    <div class="fw-bold">
                                        <i class="bi bi-envelope me-2"></i>{{ $induction->inductee_email }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            @if($induction->emergency_contact_name || $induction->emergency_contact_phone)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light border-0">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-telephone text-danger me-2"></i>
                            Emergency Contact
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                @if($induction->emergency_contact_name)
                                    <div class="info-item mb-3">
                                        <label class="form-label text-muted">Contact Name</label>
                                        <div class="fw-bold">{{ $induction->emergency_contact_name }}</div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if($induction->emergency_contact_phone)
                                    <div class="info-item mb-3">
                                        <label class="form-label text-muted">Contact Phone</label>
                                        <div class="fw-bold">
                                            <i class="bi bi-telephone me-2"></i>{{ $induction->emergency_contact_phone }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Topics Covered -->
            @if($induction->topics_covered && count($induction->topics_covered) > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light border-0">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-list-check text-info me-2"></i>
                            Topics Covered
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($induction->topics_covered as $topic)
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <span>{{ ucwords(str_replace('_', ' ', $topic)) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Documents Provided -->
            @if($induction->documents_provided && count($induction->documents_provided) > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light border-0">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-file-earmark-text text-primary me-2"></i>
                            Documents Provided
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($induction->documents_provided as $document)
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-file-earmark text-primary me-2"></i>
                                        <span>{{ ucwords(str_replace('_', ' ', $document)) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Safety Confirmations -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-shield-check text-success me-2"></i>
                        Safety Confirmations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-{{ $induction->site_rules_acknowledged ? 'check-circle text-success' : 'x-circle text-danger' }} me-2"></i>
                                <span>Site Rules Acknowledged</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-{{ $induction->emergency_procedures_understood ? 'check-circle text-success' : 'x-circle text-danger' }} me-2"></i>
                                <span>Emergency Procedures Understood</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-{{ $induction->ppe_requirements_understood ? 'check-circle text-success' : 'x-circle text-danger' }} me-2"></i>
                                <span>PPE Requirements Understood</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-{{ $induction->hazards_communicated ? 'check-circle text-success' : 'x-circle text-danger' }} me-2"></i>
                                <span>Hazards Communicated</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Notes -->
            @if($induction->notes)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light border-0">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-chat-text text-info me-2"></i>
                            Additional Notes
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $induction->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Certificate Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success bg-opacity-10 border-0">
                    <h5 class="mb-0 fw-semibold text-success">
                        <i class="bi bi-award me-2"></i>
                        Certificate Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-item mb-3">
                        <label class="form-label text-muted">Certificate Number</label>
                        <div class="fw-bold text-primary">{{ $induction->certificate_number }}</div>
                    </div>
                    
                    <div class="info-item mb-3">
                        <label class="form-label text-muted">Status</label>
                        <div>
                            @php
                                $statusColors = [
                                    'active' => 'success',
                                    'expired' => 'danger',
                                    'suspended' => 'warning'
                                ];
                                $statusIcons = [
                                    'active' => 'bi-check-circle',
                                    'expired' => 'bi-x-circle',
                                    'suspended' => 'bi-pause-circle'
                                ];
                                $color = $statusColors[$induction->status] ?? 'secondary';
                                $icon = $statusIcons[$induction->status] ?? 'bi-circle';
                            @endphp
                            <span class="badge bg-{{ $color }} px-3 py-2">
                                <i class="bi {{ $icon }} me-1"></i>
                                {{ ucfirst($induction->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="info-item mb-3">
                        <label class="form-label text-muted">Valid Until</label>
                        <div class="fw-bold {{ $isExpired ? 'text-danger' : ($isExpiringSoon ? 'text-warning' : 'text-success') }}">
                            {{ $induction->valid_until->format('M j, Y') }}
                            @if($isExpired)
                                <small class="text-danger d-block">(Expired)</small>
                            @elseif($isExpiringSoon)
                                <small class="text-warning d-block">(Expires {{ $induction->valid_until->diffForHumans() }})</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Site Information -->
            @if($induction->site)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light border-0">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-geo-alt text-primary me-2"></i>
                            Site Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="info-item mb-3">
                            <label class="form-label text-muted">Site Name</label>
                            <div class="fw-bold">{{ $induction->site->name }}</div>
                        </div>
                        
                        @if($induction->site->address)
                            <div class="info-item mb-3">
                                <label class="form-label text-muted">Address</label>
                                <div>{{ $induction->site->address }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Induction Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-calendar text-info me-2"></i>
                        Induction Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-item mb-3">
                        <label class="form-label text-muted">Induction Date</label>
                        <div class="fw-bold">{{ $induction->inducted_at->format('M j, Y') }}</div>
                        <small class="text-muted">{{ $induction->inducted_at->format('g:i A') }}</small>
                    </div>
                    
                    <div class="info-item mb-3">
                        <label class="form-label text-muted">Conducted By</label>
                        <div class="fw-bold">{{ $induction->inductedBy->name }}</div>
                    </div>
                    
                    @if($induction->employee)
                        <div class="info-item mb-3">
                            <label class="form-label text-muted">Employee Record</label>
                            <div class="fw-bold">{{ $induction->employee->first_name }} {{ $induction->employee->last_name }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow-sm">
                <div class="card-header bg-light border-0">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-gear text-secondary me-2"></i>
                        Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('health-safety.inductions.certificate', $induction) }}" class="btn btn-primary">
                            <i class="bi bi-download me-2"></i>Download Certificate
                        </a>
                        
                        @if($induction->status === 'active')
                            <form action="{{ route('health-safety.inductions.renew', $induction) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Are you sure you want to renew this induction for another year?')">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Renew Induction
                                </button>
                            </form>
                            
                            <form action="{{ route('health-safety.inductions.suspend', $induction) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Are you sure you want to suspend this induction?')">
                                    <i class="bi bi-pause-circle me-2"></i>Suspend Induction
                                </button>
                            </form>
                        @endif
                        
                        @if($induction->status === 'expired')
                            <form action="{{ route('health-safety.inductions.renew', $induction) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Are you sure you want to renew this expired induction?')">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Renew Induction
                                </button>
                            </form>
                        @endif
                        
                        @if($induction->status === 'suspended')
                            <form action="{{ route('health-safety.inductions.reactivate', $induction) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Are you sure you want to reactivate this induction?')">
                                    <i class="bi bi-play-circle me-2"></i>Reactivate Induction
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .page-header {
        margin-bottom: 2rem;
    }

    .header-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1a202c;
    }

    .page-subtitle {
        font-size: 1rem;
        color: #64748b;
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        color: #94a3b8;
    }

    .breadcrumb-item a {
        color: #64748b;
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        color: #198754;
    }

    .info-item {
        margin-bottom: 1rem;
    }

    .info-item:last-child {
        margin-bottom: 0;
    }

    .form-label {
        font-weight: 500;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .card {
        border-radius: 12px;
    }

    .card-header {
        border-radius: 12px 12px 0 0 !important;
    }

    .btn {
        border-radius: 8px;
        padding: 0.625rem 1.25rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .alert {
        border-radius: 12px;
    }

    /* Status-specific styling */
    .text-success { color: #198754 !important; }
    .text-warning { color: #ffc107 !important; }
    .text-danger { color: #dc3545 !important; }
    .text-info { color: #0dcaf0 !important; }
    .text-primary { color: #0d6efd !important; }

    /* Responsive design */
    @media (max-width: 768px) {
        .page-title {
            font-size: 1.5rem;
        }
        
        .btn-group {
            flex-direction: column;
        }
        
        .btn-group .btn {
            margin-bottom: 0.5rem;
        }
    }
</style>
@endpush
@endsection


