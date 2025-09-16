@extends('layouts.app')

@section('title', 'OP Data Forms')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="page-title">OP Data Forms</h1>
                <p class="page-subtitle">Manage operative personal data capture forms</p>
            </div>
            <div class="col-lg-4 text-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateLinkModal">
                    <i class="bi bi-plus-circle me-2"></i>Generate New Form Link
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            @if(session('share_url'))
                <hr>
                <strong>Share Link:</strong>
                <div class="input-group mt-2">
                    <input type="text" class="form-control" value="{{ session('share_url') }}" id="shareUrl" readonly>
                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard()">
                        <i class="bi bi-copy"></i> Copy
                    </button>
                </div>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Forms List -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Submitted Forms</h5>
        </div>
        <div class="card-body">
            @if($forms->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Primary Trade</th>
                                <th>Status</th>
                                <th>Account</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($forms as $form)
                                <tr>
                                    <td>
                                        <strong>{{ $form->full_name ?: 'Not provided' }}</strong>
                                        @if($form->mobile_number)
                                            <br><small class="text-muted">{{ $form->mobile_number }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $form->email_address ?: 'Not provided' }}</td>
                                    <td>{{ $form->primary_trade ?: 'Not specified' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $form->status_color }}">
                                            {{ ucfirst($form->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($form->account_created)
                                            <span class="badge bg-success">
                                                <i class="bi bi-person-check me-1"></i>Created
                                            </span>
                                        @elseif($form->status === 'approved')
                                            <span class="badge bg-warning">
                                                <i class="bi bi-person-plus me-1"></i>Pending
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($form->submitted_at)
                                            <small>{{ $form->submitted_at->format('M j, Y H:i') }}</small>
                                        @else
                                            <span class="text-muted">Not submitted</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.operative-data-forms.show', $form) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            @if($form->status === 'pending' && $form->submitted_at)
                                                <button type="button" class="btn btn-outline-success btn-sm" 
                                                        onclick="approveForm({{ $form->id }})">
                                                    <i class="bi bi-check-circle"></i> Approve
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                                        onclick="rejectForm({{ $form->id }})">
                                                    <i class="bi bi-x-circle"></i> Reject
                                                </button>
                                            @endif
                                            @if(!$form->submitted_at)
                                                <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                        onclick="copyShareLink('{{ $form->share_url }}')">
                                                    <i class="bi bi-link-45deg"></i> Copy Link
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $forms->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">No forms found</h5>
                    <p class="text-muted">Generate a new form link to get started</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Generate Link Modal -->
<div class="modal fade" id="generateLinkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate New Form Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>This will generate a new shareable link that operatives can use to fill out their personal data form.</p>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    The link will be unique and can be shared with new operatives via email, SMS, or other communication methods.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.operative-data-forms.generate-link') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">Generate Link</button>
                </form>
            </div>
        </div>
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
@endsection

@push('scripts')
<script>
function copyToClipboard() {
    const shareUrl = document.getElementById('shareUrl');
    shareUrl.select();
    shareUrl.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(shareUrl.value);
    
    // Show feedback
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="bi bi-check"></i> Copied!';
    button.classList.add('btn-success');
    button.classList.remove('btn-outline-secondary');
    
    setTimeout(() => {
        button.innerHTML = originalText;
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-secondary');
    }, 2000);
}

function copyShareLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        // Show feedback
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="bi bi-check"></i> Copied!';
        button.classList.add('btn-success');
        button.classList.remove('btn-outline-secondary');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
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
</script>
@endpush
