@extends('layouts.app')

@section('title', 'Variation Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Enhanced Header with Status Badge -->
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <h1 class="h3 mb-0">Variation {{ $variation->variation_number }}</h1>
                        <span class="badge bg-{{ $variation->status_color }} fs-6 px-3 py-2">
                            <i class="bi bi-{{ $variation->status === 'approved' ? 'check-circle' : ($variation->status === 'rejected' ? 'x-circle' : 'clock') }} me-1"></i>
                            {{ ucfirst(str_replace('_', ' ', $variation->status)) }}
                        </span>
                    </div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a></li>
                            <li class="breadcrumb-item active">Variation Details</li>
                        </ol>
                    </nav>
                    <p class="text-muted mb-0">{{ $variation->title }}</p>
                </div>
                <div class="btn-toolbar gap-2">
                    <!-- Email Button -->
                    @if(auth()->user()->canManageProjects() && $project->client)
                        <div class="btn-group">
                            <button class="btn btn-outline-primary" onclick="sendEmailToClient()">
                                <i class="bi bi-envelope me-2"></i>Send to Client
                            </button>
                            <button type="button" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="sendEmailToClient()">
                                    <i class="bi bi-envelope me-2"></i>Send Variation to Client
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="previewEmail()">
                                    <i class="bi bi-eye me-2"></i>Preview Email
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" onclick="downloadPDF()">
                                    <i class="bi bi-file-pdf me-2"></i>Download PDF
                                </a></li>
                            </ul>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    @if(auth()->user()->canManageProjects())
                        <div class="btn-group">
                            @if(!in_array($variation->status, ['approved', 'implemented']))
                                <a href="{{ route('project.variations.edit', ['project' => $project, 'variation' => $variation]) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-pencil me-2"></i>Edit
                                </a>
                            @endif
                            @if($variation->status === 'submitted')
                                <button class="btn btn-success" onclick="approveVariation()">
                                    <i class="bi bi-check me-2"></i>Approve
                                </button>
                                <button class="btn btn-outline-danger" onclick="rejectVariation()">
                                    <i class="bi bi-x me-2"></i>Reject
                                </button>
                            @endif
                            @if($variation->status === 'approved')
                                <button class="btn btn-info" onclick="implementVariation()">
                                    <i class="bi bi-gear me-2"></i>Implement
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Impact Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 bg-light">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="{{ $variation->type_icon }} text-primary fs-4 me-2"></i>
                                <h6 class="mb-0">Type</h6>
                            </div>
                            <p class="mb-0 fw-bold">{{ ucfirst(str_replace('_', ' ', $variation->type)) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 {{ $variation->cost_impact >= 0 ? 'bg-success-subtle' : 'bg-danger-subtle' }}">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="bi bi-currency-pound text-{{ $variation->cost_impact >= 0 ? 'success' : 'danger' }} fs-4 me-2"></i>
                                <h6 class="mb-0">Cost Impact</h6>
                            </div>
                            <p class="mb-0 fw-bold fs-5 text-{{ $variation->cost_impact >= 0 ? 'success' : 'danger' }}">
                                {{ $variation->formatted_cost_impact }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 bg-info-subtle">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="bi bi-clock text-info fs-4 me-2"></i>
                                <h6 class="mb-0">Time Impact</h6>
                            </div>
                            <p class="mb-0 fw-bold text-info">{{ $variation->formatted_time_impact }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 bg-warning-subtle">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="bi bi-calendar-event text-warning fs-4 me-2"></i>
                                <h6 class="mb-0">Required By</h6>
                            </div>
                            <p class="mb-0 fw-bold {{ $variation->is_overdue ? 'text-danger' : 'text-warning' }}">
                                @if($variation->required_by_date)
                                    {{ $variation->required_by_date->format('M j, Y') }}
                                    @if($variation->is_overdue)
                                        <br><small class="text-danger">⚠️ Overdue</small>
                                    @endif
                                @else
                                    Not specified
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <!-- Main Details Card -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-file-text me-2"></i>Variation Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="bi bi-card-text me-2"></i>Description
                                </h6>
                                <div class="bg-light rounded p-3">
                                    <p class="mb-0">{{ $variation->description }}</p>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="bi bi-question-circle me-2"></i>Reason for Variation
                                </h6>
                                <div class="bg-light rounded p-3">
                                    <p class="mb-0">{{ $variation->reason }}</p>
                                </div>
                            </div>

                            @if($variation->client_reference)
                                <div class="mb-4">
                                    <h6 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="bi bi-bookmark me-2"></i>Client Reference
                                    </h6>
                                    <div class="bg-light rounded p-3">
                                        <p class="mb-0 fw-bold">{{ $variation->client_reference }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($variation->approval_notes)
                                <div class="mb-4">
                                    <h6 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="bi bi-chat-square-text me-2"></i>Approval Notes
                                    </h6>
                                    <div class="alert alert-{{ $variation->status === 'approved' ? 'success' : 'warning' }} mb-0">
                                        <p class="mb-0">{{ $variation->approval_notes }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($variation->affected_tasks_details && count($variation->affected_tasks_details) > 0)
                                <div class="mb-4">
                                    <h6 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="bi bi-list-task me-2"></i>Affected Tasks
                                    </h6>
                                    <div class="row g-2">
                                        @foreach($variation->affected_tasks_details as $task)
                                            <div class="col-md-6">
                                                <div class="card card-body border-start border-primary border-3 py-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-check-square me-2 text-primary"></i>
                                                        <span class="fw-medium">{{ $task->title }}</span>
                                                        <span class="badge bg-{{ $task->status === 'completed' ? 'success' : 'secondary' }} ms-auto">
                                                            {{ ucfirst($task->status) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Variation Information Card -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-info-circle me-2"></i>Variation Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 p-3 bg-light rounded">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-person-circle text-primary me-2"></i>
                                    <small class="text-muted fw-bold">Created By</small>
                                </div>
                                <p class="mb-1 fw-medium">{{ $variation->creator->name }}</p>
                                <small class="text-muted">{{ $variation->created_at->format('M j, Y g:i A') }}</small>
                            </div>

                            <div class="mb-3 p-3 bg-light rounded">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-calendar-check text-primary me-2"></i>
                                    <small class="text-muted fw-bold">Requested Date</small>
                                </div>
                                <p class="mb-0 fw-medium">{{ $variation->requested_date->format('M j, Y') }}</p>
                            </div>

                            @if($variation->approved_by)
                                <div class="mb-3 p-3 bg-{{ $variation->status === 'approved' ? 'success' : 'warning' }}-subtle rounded">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-{{ $variation->status === 'approved' ? 'check-circle' : 'x-circle' }} text-{{ $variation->status === 'approved' ? 'success' : 'warning' }} me-2"></i>
                                        <small class="text-muted fw-bold">{{ $variation->status === 'approved' ? 'Approved' : 'Rejected' }} By</small>
                                    </div>
                                    <p class="mb-1 fw-medium">{{ $variation->approver->name }}</p>
                                    <small class="text-muted">{{ $variation->approved_at->format('M j, Y g:i A') }}</small>
                                </div>
                            @endif

                            <div class="mb-3 p-3 bg-light rounded">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-building text-primary me-2"></i>
                                    <small class="text-muted fw-bold">Project</small>
                                </div>
                                <p class="mb-0">
                                    <a href="{{ route('projects.show', $project) }}" class="text-decoration-none fw-medium">
                                        {{ $project->name }}
                                    </a>
                                </p>
                            </div>

                            @if($variation->client_approved)
                                <div class="mb-0 p-3 bg-success-subtle rounded">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-patch-check text-success me-2"></i>
                                        <small class="text-muted fw-bold">Client Status</small>
                                    </div>
                                    <span class="badge bg-success mb-1">Client Approved</span>
                                    <br><small class="text-muted">{{ $variation->client_approved_at->format('M j, Y') }}</small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Client Information Card -->
                    @if($project->client)
                        <div class="card shadow-sm mt-3">
                            <div class="card-header bg-success text-white">
                                <h6 class="card-title mb-0">
                                    <i class="bi bi-person-badge me-2"></i>Client Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-building-check text-success me-2"></i>
                                    <span class="fw-bold">{{ $project->client->company_name }}</span>
                                </div>
                                @if($project->client->contact_person_name)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-person text-muted me-2"></i>
                                        <span>{{ $project->client->contact_person_name }}</span>
                                    </div>
                                @endif
                                @if($project->client->email || $project->client->contact_person_email)
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-envelope text-muted me-2"></i>
                                        <span class="small">{{ $project->client->contact_person_email ?: $project->client->email }}</span>
                                    </div>
                                @endif
                                @if($project->client->phone || $project->client->contact_person_phone)
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-telephone text-muted me-2"></i>
                                        <span class="small">{{ $project->client->contact_person_phone ?: $project->client->phone }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Email Modal -->
<div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="emailModalLabel">
                    <i class="bi bi-envelope me-2"></i>Send Variation to Client
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="emailForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="client_email" class="form-label">Client Email</label>
                            <input type="email" class="form-control" id="client_email" name="client_email" 
                                   value="{{ $project->client->contact_person_email ?: $project->client->email }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="client_name" class="form-label">Client Name</label>
                            <input type="text" class="form-control" id="client_name" name="client_name" 
                                   value="{{ $project->client->contact_person_name ?: $project->client->company_name }}" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email_subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="email_subject" name="email_subject" 
                               value="Project Variation {{ $variation->variation_number }} - {{ $variation->title }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email_message" class="form-label">Message</label>
                        <textarea class="form-control" id="email_message" name="email_message" rows="6" required>Dear {{ $project->client->contact_person_name ?: 'Client' }},

We have created a project variation that requires your review and approval:

Variation: {{ $variation->variation_number }}
Title: {{ $variation->title }}
Cost Impact: {{ $variation->formatted_cost_impact }}
Time Impact: {{ $variation->formatted_time_impact }}

Description:
{{ $variation->description }}

Please review the variation details and let us know if you have any questions.

Best regards,
{{ auth()->user()->name }}
{{ auth()->user()->company->name }}</textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="include_pdf" name="include_pdf" checked>
                        <label class="form-check-label" for="include_pdf">
                            Include variation details as PDF attachment
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="sendEmailBtn">
                    <i class="bi bi-send me-2"></i>Send Email
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Email Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Email Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="emailPreviewContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading email preview...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="sendEmailToClient()">
                    <i class="bi bi-send me-2"></i>Send This Email
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Email functionality
function sendEmailToClient() {
    const emailModal = new bootstrap.Modal(document.getElementById('emailModal'));
    emailModal.show();
}

function previewEmail() {
    const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
    previewModal.show();
    
    // Load email preview
    fetch(`/projects/{{ $project->id }}/variations/{{ $variation->id }}/email-preview`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('emailPreviewContent').innerHTML = data.html;
        } else {
            document.getElementById('emailPreviewContent').innerHTML = 
                '<div class="alert alert-danger">Error loading preview: ' + (data.message || 'Unknown error') + '</div>';
        }
    })
    .catch(error => {
        document.getElementById('emailPreviewContent').innerHTML = 
            '<div class="alert alert-danger">Error loading preview: ' + error.message + '</div>';
    });
}

function downloadPDF() {
    window.open(`/projects/{{ $project->id }}/variations/{{ $variation->id }}/pdf`, '_blank');
}

// Send email functionality
document.getElementById('sendEmailBtn').addEventListener('click', function() {
    const form = document.getElementById('emailForm');
    const formData = new FormData(form);
    const sendBtn = this;
    
    // Show loading state
    sendBtn.disabled = true;
    sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
    
    fetch(`/projects/{{ $project->id }}/variations/{{ $variation->id }}/send-email`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show';
            alert.innerHTML = `
                <i class="bi bi-check-circle me-2"></i>${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.container-fluid .row .col-12').prepend(alert);
            
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('emailModal')).hide();
        } else {
            alert('Error sending email: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Error sending email: ' + error.message);
    })
    .finally(() => {
        sendBtn.disabled = false;
        sendBtn.innerHTML = '<i class="bi bi-send me-2"></i>Send Email';
    });
});

// Existing functions
function approveVariation() {
    if (confirm('Are you sure you want to approve this variation?')) {
        fetch(`/projects/{{ $project->id }}/variations/{{ $variation->id }}/approve`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error approving variation: ' + (data.message || 'Unknown error'));
            }
        });
    }
}

function rejectVariation() {
    const reason = prompt('Please provide a reason for rejection (optional):');
    if (reason !== null) { // User didn't cancel
        fetch(`/projects/{{ $project->id }}/variations/{{ $variation->id }}/reject`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ approval_notes: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error rejecting variation: ' + (data.message || 'Unknown error'));
            }
        });
    }
}

function implementVariation() {
    if (confirm('Are you sure you want to implement this variation? This will update the project budget and timeline.')) {
        fetch(`/projects/{{ $project->id }}/variations/{{ $variation->id }}/implement`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error implementing variation: ' + (data.message || 'Unknown error'));
            }
        });
    }
}
</script>
@endsection 