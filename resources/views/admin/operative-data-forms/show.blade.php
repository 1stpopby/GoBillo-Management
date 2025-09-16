@extends('layouts.app')

@section('title', 'OP Data Form Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <a href="{{ route('admin.operative-data-forms.index') }}" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <div>
                        <h1 class="page-title">{{ $form->full_name ?: 'Form Details' }}</h1>
                        <p class="page-subtitle">
                            <span class="badge bg-{{ $form->status_color }} me-2">{{ ucfirst($form->status) }}</span>
                            @if($form->submitted_at)
                                Submitted {{ $form->submitted_at->format('M j, Y \a\t H:i') }}
                            @else
                                Form not yet submitted
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-end">
                @if($form->status === 'pending' && $form->submitted_at)
                    <button type="button" class="btn btn-success me-2" onclick="approveForm({{ $form->id }})">
                        <i class="bi bi-check-circle me-2"></i>Approve
                    </button>
                    <button type="button" class="btn btn-danger" onclick="rejectForm({{ $form->id }})">
                        <i class="bi bi-x-circle me-2"></i>Reject
                    </button>
                @elseif($form->status === 'approved' && !$form->account_created)
                    <button type="button" class="btn btn-primary me-2" onclick="createAccount({{ $form->id }})">
                        <i class="bi bi-person-plus me-2"></i>Create Account
                    </button>
                @elseif($form->account_created)
                    <span class="badge bg-info fs-6">
                        <i class="bi bi-person-check me-1"></i>Account Created
                    </span>
                    @if($form->createdUser)
                        <div class="small text-muted mt-1">
                            Login: {{ $form->createdUser->email }}
                        </div>
                    @endif
                @elseif(!$form->submitted_at)
                    <button type="button" class="btn btn-primary" onclick="copyShareLink('{{ $form->share_url }}')">
                        <i class="bi bi-link-45deg me-2"></i>Copy Share Link
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if(!$form->submitted_at)
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Form Not Submitted:</strong> This form has been generated but not yet filled out by the operative.
            <hr>
            <strong>Share Link:</strong>
            <div class="input-group mt-2">
                <input type="text" class="form-control" value="{{ $form->share_url }}" id="shareUrl" readonly>
                <button class="btn btn-outline-secondary" type="button" onclick="copyShareLink('{{ $form->share_url }}')">
                    <i class="bi bi-copy"></i> Copy
                </button>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Personal Information -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person me-2"></i>Personal Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label text-muted">Full Name</label>
                            <p class="mb-0">{{ $form->full_name ?: 'Not provided' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted">Date of Birth</label>
                            <p class="mb-0">{{ $form->date_of_birth ? $form->date_of_birth->format('d/m/Y') : 'Not provided' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted">Nationality</label>
                            <p class="mb-0">{{ $form->nationality ?: 'Not provided' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted">Mobile Number</label>
                            <p class="mb-0">{{ $form->mobile_number ?: 'Not provided' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Email Address</label>
                            <p class="mb-0">{{ $form->email_address ?: 'Not provided' }}</p>
                        </div>
                        <div class="col-sm-8">
                            <label class="form-label text-muted">Home Address</label>
                            <p class="mb-0">{{ $form->home_address ?: 'Not provided' }}</p>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label text-muted">Postcode</label>
                            <p class="mb-0">{{ $form->postcode ?: 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Emergency Contact -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-telephone me-2"></i>Emergency Contact
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label text-muted">Name</label>
                            <p class="mb-0">{{ $form->emergency_contact_name ?: 'Not provided' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted">Relationship</label>
                            <p class="mb-0">{{ $form->emergency_contact_relationship ?: 'Not provided' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Contact Number</label>
                            <p class="mb-0">{{ $form->emergency_contact_number ?: 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Work Documentation -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark-text me-2"></i>Work Documentation
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label text-muted">National Insurance Number</label>
                            <p class="mb-0">{{ $form->national_insurance_number ?: 'Not provided' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted">UTR Number</label>
                            <p class="mb-0">{{ $form->utr_number ?: 'Not applicable' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted">CSCS Card Type</label>
                            <p class="mb-0">{{ $form->cscs_card_type ?: 'Not provided' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted">CSCS Card Number</label>
                            <p class="mb-0">{{ $form->cscs_card_number ?: 'Not provided' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted">CSCS Card Expiry</label>
                            <p class="mb-0">
                                @if($form->cscs_card_expiry)
                                    {{ $form->cscs_card_expiry->format('d/m/Y') }}
                                    @if($form->cscs_card_expiry->isPast())
                                        <span class="badge bg-danger ms-2">Expired</span>
                                    @elseif($form->cscs_card_expiry->diffInDays() <= 30)
                                        <span class="badge bg-warning ms-2">Expires Soon</span>
                                    @endif
                                @else
                                    Not provided
                                @endif
                            </p>
                        </div>
                        
                        <!-- CSCS Card Images -->
                        @if($form->cscs_card_front_image || $form->cscs_card_back_image)
                            <div class="col-12">
                                <label class="form-label text-muted">CSCS Card Images</label>
                                <div class="row g-3 mt-1">
                                    @if($form->cscs_card_front_image)
                                        <div class="col-md-6">
                                            <div class="card">
                                                <img src="{{ Storage::url($form->cscs_card_front_image) }}" 
                                                     class="card-img-top" 
                                                     style="height: 200px; object-fit: cover; cursor: pointer;" 
                                                     alt="CSCS Card Front"
                                                     onclick="showImageModal('{{ Storage::url($form->cscs_card_front_image) }}', 'CSCS Card Front')">
                                                <div class="card-body p-2 text-center">
                                                    <small class="text-muted">Front</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($form->cscs_card_back_image)
                                        <div class="col-md-6">
                                            <div class="card">
                                                <img src="{{ Storage::url($form->cscs_card_back_image) }}" 
                                                     class="card-img-top" 
                                                     style="height: 200px; object-fit: cover; cursor: pointer;" 
                                                     alt="CSCS Card Back"
                                                     onclick="showImageModal('{{ Storage::url($form->cscs_card_back_image) }}', 'CSCS Card Back')">
                                                <div class="card-body p-2 text-center">
                                                    <small class="text-muted">Back</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <div class="col-sm-6">
                            <label class="form-label text-muted">Right to Work in UK</label>
                            <p class="mb-0">
                                @if($form->right_to_work_uk)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-danger">No</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Passport / ID Provided</label>
                            <p class="mb-0">
                                @if($form->passport_id_provided)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-warning">No</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bank Details -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bank me-2"></i>Bank Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label text-muted">Bank Name</label>
                            <p class="mb-0">{{ $form->bank_name ?: 'Not provided' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted">Account Holder Name</label>
                            <p class="mb-0">{{ $form->account_holder_name ?: 'Not provided' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted">Sort Code</label>
                            <p class="mb-0">{{ $form->sort_code ?: 'Not provided' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted">Account Number</label>
                            <p class="mb-0">
                                @if($form->account_number)
                                    {{ '****' . substr($form->account_number, -4) }}
                                @else
                                    Not provided
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trade and Qualifications -->
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-tools me-2"></i>Trade and Qualifications
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label text-muted">Primary Trade</label>
                            <p class="mb-0">{{ $form->primary_trade ?: 'Not provided' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label text-muted">Years of Experience</label>
                            <p class="mb-0">{{ $form->years_experience ?: 'Not provided' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Qualifications & Certifications</label>
                            <p class="mb-0">{{ $form->qualifications_certifications ?: 'None provided' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted">Other Cards/Licenses</label>
                            <p class="mb-0">{{ $form->other_cards_licenses ?: 'None provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Status -->
        @if($form->submitted_at)
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>Form Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-sm-3">
                                <label class="form-label text-muted">Status</label>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $form->status_color }}">{{ ucfirst($form->status) }}</span>
                                </p>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label text-muted">Submitted</label>
                                <p class="mb-0">{{ $form->submitted_at->format('M j, Y H:i') }}</p>
                            </div>
                            @if($form->approved_at)
                                <div class="col-sm-3">
                                    <label class="form-label text-muted">Approved</label>
                                    <p class="mb-0">{{ $form->approved_at->format('M j, Y H:i') }}</p>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label text-muted">Approved By</label>
                                    <p class="mb-0">{{ $form->approvedBy->name ?? 'Unknown' }}</p>
                                </div>
                            @elseif($form->rejected_at)
                                <div class="col-sm-6">
                                    <label class="form-label text-muted">Rejected</label>
                                    <p class="mb-0">{{ $form->rejected_at->format('M j, Y H:i') }}</p>
                                </div>
                            @endif
                            @if($form->rejection_reason)
                                <div class="col-12">
                                    <label class="form-label text-muted">Rejection Reason</label>
                                    <div class="alert alert-danger">
                                        {{ $form->rejection_reason }}
                                    </div>
                                </div>
                            @endif
                            <div class="col-12">
                                <label class="form-label text-muted">Declaration</label>
                                <p class="mb-0">
                                    @if($form->declaration_confirmed)
                                        <span class="badge bg-success">Confirmed</span>
                                        Information confirmed as accurate and true
                                    @else
                                        <span class="badge bg-warning">Not confirmed</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Reason for rejection</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" 
                                  rows="3" required placeholder="Please provide a reason for rejecting this form..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Form</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Account Modal -->
<div class="modal fade" id="createAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus me-2"></i>Create Operative Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createAccountForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        This will create a User account and Employee record for <strong>{{ $form->full_name }}</strong> 
                        with the email <strong>{{ $form->email_address }}</strong>.
                    </div>
                    
                    <div class="mb-3">
                        <label for="temporary_password" class="form-label">Temporary Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="temporary_password" name="temporary_password" 
                               required minlength="8" placeholder="Enter a temporary password (min 8 characters)">
                        <div class="form-text">
                            The operative will use this password to log in initially. They should change it after first login.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="confirm_creation" required>
                            <label class="form-check-label" for="confirm_creation">
                                I confirm that I want to create an account for this operative
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i>Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalTitle">Image View</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="imageModalImg" src="" class="img-fluid" alt="Full size image">
            </div>
            <div class="modal-footer">
                <a id="imageDownloadLink" href="" download class="btn btn-primary">
                    <i class="bi bi-download me-2"></i>Download
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyShareLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        // Show feedback
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="bi bi-check"></i> Copied!';
        button.classList.add('btn-success');
        button.classList.remove('btn-primary', 'btn-outline-secondary');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('btn-success');
            button.classList.add('btn-primary');
        }, 2000);
    });
}

function approveForm(formId) {
    if (confirm('Are you sure you want to approve this form?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/operative-data-forms/${formId}/approve`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

function rejectForm(formId) {
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    const form = document.getElementById('rejectForm');
    form.action = `/admin/operative-data-forms/${formId}/reject`;
    modal.show();
}

function showImageModal(imageUrl, title) {
    document.getElementById('imageModalTitle').textContent = title;
    document.getElementById('imageModalImg').src = imageUrl;
    document.getElementById('imageDownloadLink').href = imageUrl;
    
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}

function createAccount(formId) {
    const modal = new bootstrap.Modal(document.getElementById('createAccountModal'));
    const form = document.getElementById('createAccountForm');
    form.action = `/admin/operative-data-forms/${formId}/create-account`;
    modal.show();
}
</script>
@endpush
