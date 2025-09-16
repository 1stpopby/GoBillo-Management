@extends('layouts.app')

@section('title', 'Employee Profile - ' . $user->name)

@section('content')
<div class="operative-profile-container">
    <!-- Professional Header -->
    <div class="profile-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="profile-info d-flex align-items-center">
                    <div class="profile-avatar me-4">
                        <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi bi-person-fill" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="profile-name mb-1">{{ $user->name }}</h1>
                        <p class="profile-subtitle mb-2">
                            <span class="badge bg-primary me-2">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                            <span class="text-muted">User ID: {{ $user->id }}</span>
                        </p>
                        <div class="profile-quick-info">
                            <span class="me-3">
                                <i class="bi bi-envelope me-1"></i>{{ $user->email }}
                            </span>
                            @if($user->phone)
                            <span class="me-3">
                                <i class="bi bi-telephone me-1"></i>{{ $user->phone }}
                            </span>
                            @endif
                            <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-end">
                <div class="profile-actions">
                    <a href="{{ route('cis.employee-payments', $user) }}" class="btn btn-primary me-2">
                        <i class="bi bi-receipt me-2"></i>CIS Payments
                    </a>
                    <div class="btn-group">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('employees.edit', $user) }}"><i class="bi bi-pencil me-2"></i>Edit Profile</a></li>
                            <li><a class="dropdown-item" href="#" onclick="printProfile()"><i class="bi bi-printer me-2"></i>Print Profile</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="profile-tabs mb-4">
        <ul class="nav nav-pills nav-fill profile-nav" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab === 'general' ? 'active' : '' }}" 
                        onclick="switchTab('general')" type="button">
                    <i class="bi bi-person-lines-fill me-2"></i>General Information
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab === 'financial' ? 'active' : '' }}" 
                        onclick="switchTab('financial')" type="button">
                    <i class="bi bi-currency-pound me-2"></i>Financial Information
                    @if($financialSummary['payment_count'] > 0)
                        <span class="badge bg-primary ms-2">{{ $financialSummary['payment_count'] }}</span>
                    @endif
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab === 'operational' ? 'active' : '' }}" 
                        onclick="switchTab('operational')" type="button">
                    <i class="bi bi-geo-alt me-2"></i>Operational Information
                    @if($operationalSummary['active_sites'] > 0)
                        <span class="badge bg-success ms-2">{{ $operationalSummary['active_sites'] }}</span>
                    @endif
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab === 'attachments' ? 'active' : '' }}" 
                        onclick="switchTab('attachments')" type="button">
                    <i class="bi bi-paperclip me-2"></i>Attachments & Documents
                    @if($documentSummary['expired_documents'] > 0)
                        <span class="badge bg-danger ms-2">{{ $documentSummary['expired_documents'] }}</span>
                    @elseif($documentSummary['expiring_documents'] > 0)
                        <span class="badge bg-warning ms-2">{{ $documentSummary['expiring_documents'] }}</span>
                    @else
                        <span class="badge bg-secondary ms-2">{{ $documentSummary['total_documents'] }}</span>
                    @endif
                </button>
            </li>
        </ul>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- General Information Tab -->
        <div class="tab-pane {{ $activeTab === 'general' ? 'show active' : '' }}" id="general">
            <div class="row g-4">
                <!-- Personal Information -->
                <div class="col-lg-6">
                    <div class="info-card">
                        <div class="info-card-header">
                            <h5><i class="bi bi-person-badge me-2"></i>Personal Information</h5>
                        </div>
                        <div class="info-card-body">
                            <div class="info-row">
                                <label>Full Name:</label>
                                <span>{{ $user->name }}</span>
                            </div>
                            <div class="info-row">
                                <label>User ID:</label>
                                <span>{{ $user->id }}</span>
                            </div>
                            <div class="info-row">
                                <label>Email:</label>
                                <span><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></span>
                            </div>
                            <div class="info-row">
                                <label>Phone:</label>
                                <span>{{ $user->phone ?: 'Not provided' }}</span>
                            </div>
                            <div class="info-row">
                                <label>Role:</label>
                                <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                            </div>
                            <div class="info-row">
                                <label>Status:</label>
                                <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Information -->
                <div class="col-lg-6">
                    <div class="info-card">
                        <div class="info-card-header">
                            <h5><i class="bi bi-gear me-2"></i>System Information</h5>
                        </div>
                        <div class="info-card-body">
                            <div class="info-row">
                                <label>Account Created:</label>
                                <span>{{ $user->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="info-row">
                                <label>Last Updated:</label>
                                <span>{{ $user->updated_at->format('d M Y') }}</span>
                            </div>
                            <div class="info-row">
                                <label>Company:</label>
                                <span>{{ $user->company->name ?? 'Not assigned' }}</span>
                            </div>
                            <div class="info-row">
                                <label>Access Level:</label>
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Information Tab -->
        <div class="tab-pane {{ $activeTab === 'financial' ? 'show active' : '' }}" id="financial">
            <div class="row g-4">
                <!-- Financial Overview -->
                <div class="col-12">
                    <div class="financial-overview">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon bg-success">
                                        <i class="bi bi-currency-pound"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h4>£{{ number_format($financialSummary['total_gross'], 2) }}</h4>
                                        <p>Total Gross Payments</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon bg-warning">
                                        <i class="bi bi-percent"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h4>£{{ number_format($financialSummary['total_deductions'], 2) }}</h4>
                                        <p>Total CIS Deductions</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon bg-primary">
                                        <i class="bi bi-wallet2"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h4>£{{ number_format($financialSummary['total_net'], 2) }}</h4>
                                        <p>Total Net Payments</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon bg-info">
                                        <i class="bi bi-receipt"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h4>{{ $financialSummary['payment_count'] }}</h4>
                                        <p>Total Payments</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Payments -->
                <div class="col-12">
                    <div class="info-card">
                        <div class="info-card-header d-flex justify-content-between align-items-center">
                            <h5><i class="bi bi-clock-history me-2"></i>Recent CIS Payments</h5>
                            <a href="{{ route('cis.operative-payments', $user) }}" class="btn btn-sm btn-primary">
                                View All Payments
                            </a>
                        </div>
                        <div class="info-card-body">
                            @if($user->cisPayments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Project</th>
                                                <th>Gross Amount</th>
                                                <th>CIS Deduction</th>
                                                <th>Net Payment</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($user->cisPayments->take(5) as $payment)
                                            <tr>
                                                <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                                <td>{{ $payment->project->name ?? 'No Project' }}</td>
                                                <td>£{{ number_format($payment->gross_amount, 2) }}</td>
                                                <td>£{{ number_format($payment->cis_deduction, 2) }}</td>
                                                <td>£{{ number_format($payment->net_payment, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $payment->status_color }}">
                                                        {{ $payment->status_label }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="empty-state text-center py-4">
                                    <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                                    <h6 class="mt-2">No CIS Payments</h6>
                                    <p class="text-muted">No payment records found for this operative.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Operational Information Tab -->
        <div class="tab-pane {{ $activeTab === 'operational' ? 'show active' : '' }}" id="operational">
            <div class="row g-4">
                <!-- Operational Overview -->
                <div class="col-12">
                    <div class="operational-overview">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon bg-primary">
                                        <i class="bi bi-geo-alt"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h4>{{ $operationalSummary['site_allocations'] }}</h4>
                                        <p>Total Site Allocations</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon bg-success">
                                        <i class="bi bi-check-circle"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h4>{{ $operationalSummary['active_sites'] }}</h4>
                                        <p>Active Sites</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon bg-info">
                                        <i class="bi bi-folder2-open"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h4>{{ $operationalSummary['total_projects'] }}</h4>
                                        <p>Total Projects</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon bg-warning">
                                        <i class="bi bi-tools"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h4>{{ ucfirst(str_replace('_', ' ', $user->role)) }}</h4>
                                        <p>Current Role</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Managed Sites -->
                <div class="col-12">
                    <div class="info-card">
                        <div class="info-card-header">
                            <h5><i class="bi bi-geo-alt-fill me-2"></i>Managed Sites</h5>
                        </div>
                        <div class="info-card-body">
                            @if($user->managedSites && $user->managedSites->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Site</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>Assigned Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($user->managedSites as $site)
                                            <tr>
                                                <td>
                                                    <strong>{{ $site->name }}</strong><br>
                                                    <small class="text-muted">{{ $site->address }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ ucfirst($site->pivot->role ?? 'Manager') }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $site->pivot->is_active ? 'success' : 'secondary' }}">
                                                        {{ $site->pivot->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>{{ $site->pivot->created_at ? $site->pivot->created_at->format('d M Y') : 'N/A' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="empty-state text-center py-4">
                                    <i class="bi bi-geo-alt text-muted" style="font-size: 3rem;"></i>
                                    <h6 class="mt-2">No Managed Sites</h6>
                                    <p class="text-muted">This employee is not currently managing any sites.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Skills & Qualifications -->
                <div class="col-lg-6">
                    <div class="info-card">
                        <div class="info-card-header">
                            <h5><i class="bi bi-award me-2"></i>Skills & Qualifications</h5>
                        </div>
                        <div class="info-card-body">
                            @if(isset($user->skills) && $user->skills)
                                <div class="skills-list mb-3">
                                    <label class="fw-bold">Skills:</label>
                                    <div class="mt-2">
                                        <span class="badge bg-primary me-1 mb-1">{{ $user->skills }}</span>
                                    </div>
                                </div>
                            @endif

                            @if(isset($user->qualifications) && $user->qualifications)
                                <div class="qualifications-list mb-3">
                                    <label class="fw-bold">Qualifications:</label>
                                    <div class="mt-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>{{ $user->qualifications }}
                                    </div>
                                </div>
                            @endif

                            @if(isset($user->certifications) && $user->certifications)
                                <div class="certifications-list">
                                    <label class="fw-bold">Certifications:</label>
                                    <div class="mt-2">
                                        <i class="bi bi-patch-check text-primary me-2"></i>{{ $user->certifications }}
                                    </div>
                                </div>
                            @endif

                            @if((!isset($user->skills) || !$user->skills) && (!isset($user->qualifications) || !$user->qualifications) && (!isset($user->certifications) || !$user->certifications))
                                <div class="empty-state text-center py-3">
                                    <i class="bi bi-award text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mb-0">No skills or qualifications recorded.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="col-lg-6">
                    <div class="info-card">
                        <div class="info-card-header">
                            <h5><i class="bi bi-sticky me-2"></i>Notes</h5>
                        </div>
                        <div class="info-card-body">
                            @if(isset($user->notes) && $user->notes)
                                <p class="mb-0">{{ $user->notes }}</p>
                            @else
                                <div class="empty-state text-center py-3">
                                    <i class="bi bi-sticky text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mb-0">No notes recorded.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attachments & Documents Tab -->
        <div class="tab-pane {{ $activeTab === 'attachments' ? 'show active' : '' }}" id="attachments">
            <div class="row g-4">
                <!-- Document Overview -->
                <div class="col-12">
                    <div class="documents-overview">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon bg-primary">
                                        <i class="bi bi-files"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h4>{{ $documentSummary['total_documents'] }}</h4>
                                        <p>Total Documents</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon bg-success">
                                        <i class="bi bi-check-circle"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h4>{{ $documentSummary['active_documents'] }}</h4>
                                        <p>Active Documents</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon bg-warning">
                                        <i class="bi bi-exclamation-triangle"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h4>{{ $documentSummary['expiring_documents'] }}</h4>
                                        <p>Expiring Soon</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon bg-danger">
                                        <i class="bi bi-x-circle"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h4>{{ $documentSummary['expired_documents'] }}</h4>
                                        <p>Expired Documents</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload New Document -->
                <div class="col-12">
                    <div class="info-card">
                        <div class="info-card-header">
                            <h5><i class="bi bi-cloud-upload me-2"></i>Upload New Document</h5>
                        </div>
                        <div class="info-card-body">
                            <form id="documentUploadForm" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="attachable_type" value="User">
                                <input type="hidden" name="attachable_id" value="{{ $user->id }}">
                                
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Document Type</label>
                                        <select class="form-select" name="document_type" required>
                                            <option value="">Select Type</option>
                                            @foreach(\App\Models\DocumentAttachment::getDocumentTypes() as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Document Name</label>
                                        <input type="text" class="form-control" name="document_name" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Document File</label>
                                        <input type="file" class="form-control" name="file" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Issue Date</label>
                                        <input type="date" class="form-control" name="issue_date">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Expiry Date</label>
                                        <input type="date" class="form-control" name="expiry_date">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Document Number</label>
                                        <input type="text" class="form-control" name="document_number">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Issuing Authority</label>
                                        <input type="text" class="form-control" name="issuing_authority">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Notes</label>
                                        <textarea class="form-control" name="notes" rows="2"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-cloud-upload me-2"></i>Upload Document
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Documents List -->
                <div class="col-12">
                    <div class="info-card">
                        <div class="info-card-header">
                            <h5><i class="bi bi-folder2-open me-2"></i>Document Library</h5>
                        </div>
                        <div class="info-card-body">
                            <div id="documentsContainer">
                                @if($user->documentAttachments->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Document</th>
                                                    <th>Type</th>
                                                    <th>Issue Date</th>
                                                    <th>Expiry Date</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($user->documentAttachments as $document)
                                                <tr>
                                                    <td>
                                                        <div class="document-info">
                                                            <strong>{{ $document->document_name }}</strong><br>
                                                            <small class="text-muted">{{ $document->original_filename }}</small><br>
                                                            <small class="text-muted">{{ $document->file_size_human }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary">{{ $document->document_type_display }}</span>
                                                    </td>
                                                    <td>{{ $document->issue_date ? $document->issue_date->format('d M Y') : 'N/A' }}</td>
                                                    <td>
                                                        @if($document->expiry_date)
                                                            {{ $document->expiry_date->format('d M Y') }}
                                                            @if($document->isExpired())
                                                                <br><small class="text-danger">Expired</small>
                                                            @elseif($document->isExpiringSoon())
                                                                <br><small class="text-warning">Expiring Soon</small>
                                                            @endif
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $document->status_color }}">{{ $document->status_label }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('profiles.documents.download', $document) }}" class="btn btn-outline-primary" title="Download">
                                                                <i class="bi bi-download"></i>
                                                            </a>
                                                            <button class="btn btn-outline-danger" onclick="deleteDocument({{ $document->id }})" title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="empty-state text-center py-4">
                                        <i class="bi bi-folder2-open text-muted" style="font-size: 3rem;"></i>
                                        <h6 class="mt-2">No Documents</h6>
                                        <p class="text-muted">No documents have been uploaded for this operative yet.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Profile Header */
.profile-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    color: white;
    padding: 2rem;
    margin-bottom: 2rem;
}

.profile-avatar .avatar-lg {
    width: 80px;
    height: 80px;
    font-size: 2.5rem;
}

.profile-name {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
}

.profile-subtitle {
    font-size: 1rem;
    opacity: 0.9;
}

.profile-quick-info {
    font-size: 0.9rem;
    opacity: 0.8;
}

/* Navigation Tabs */
.profile-nav {
    background: white;
    border-radius: 15px;
    padding: 0.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.profile-nav .nav-link {
    border-radius: 10px;
    color: #6c757d;
    font-weight: 600;
    padding: 1rem 1.5rem;
    transition: all 0.3s ease;
}

.profile-nav .nav-link:hover {
    background: #f8f9fa;
    color: #495057;
}

.profile-nav .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

/* Info Cards */
.info-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border: none;
    overflow: hidden;
}

.info-card-header {
    background: #f8f9fa;
    padding: 1.5rem;
    border-bottom: 1px solid #dee2e6;
}

.info-card-header h5 {
    margin: 0;
    color: #495057;
    font-weight: 600;
}

.info-card-body {
    padding: 1.5rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.info-row:last-child {
    border-bottom: none;
}

.info-row label {
    font-weight: 600;
    color: #6c757d;
    margin: 0;
    width: 40%;
}

.info-row span {
    width: 60%;
    text-align: right;
}

/* Stat Cards */
.stat-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.stat-content h4 {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0;
    color: #2d3748;
}

.stat-content p {
    margin: 0;
    color: #718096;
    font-size: 0.9rem;
}

/* Empty States */
.empty-state {
    text-align: center;
    color: #6c757d;
}

/* Skills and badges */
.skills-list .badge {
    font-size: 0.8rem;
    padding: 0.5rem 0.75rem;
}

/* Responsive */
@media (max-width: 768px) {
    .profile-header {
        padding: 1.5rem;
    }
    
    .profile-info {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .profile-actions {
        text-align: center;
        margin-top: 1rem;
    }
    
    .profile-nav .nav-link {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
}
</style>

<script>
// Tab switching
function switchTab(tab) {
    // Update URL without reload
    const url = new URL(window.location);
    url.searchParams.set('tab', tab);
    window.history.pushState({}, '', url);
    
    // Update active tab
    document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(pane => {
        pane.classList.remove('show', 'active');
    });
    
    document.querySelector(`[onclick="switchTab('${tab}')"]`).classList.add('active');
    document.getElementById(tab).classList.add('show', 'active');
}

// Document upload
document.getElementById('documentUploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("profiles.documents.upload") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Document uploaded successfully!');
            location.reload();
        } else {
            alert('Error uploading document: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while uploading the document.');
    });
});

// Delete document
function deleteDocument(documentId) {
    if (confirm('Are you sure you want to delete this document?')) {
        fetch(`/profiles/documents/${documentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Document deleted successfully!');
                location.reload();
            } else {
                alert('Error deleting document: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the document.');
        });
    }
}

// Print profile
function printProfile() {
    window.print();
}
</script>
@endsection
