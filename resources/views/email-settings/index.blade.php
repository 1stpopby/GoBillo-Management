@extends('layouts.app')

@section('title', 'Email Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">Email Settings</h1>
                    <p class="text-muted mb-0">Configure SMTP settings and email notifications for your company</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary" id="testEmailBtn">
                        <i class="bi bi-envelope-check me-2"></i>Test Email
                    </button>
                    <button type="button" class="btn btn-info" id="usageStatsBtn">
                        <i class="bi bi-graph-up me-2"></i>Usage Stats
                    </button>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('email-settings.store') }}" method="POST" id="emailSettingsForm">
                @csrf
                
                <div class="row">
                    <!-- SMTP Configuration -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-gear me-2"></i>SMTP Configuration</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Configuration Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $emailSetting->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="smtp_host" class="form-label">SMTP Host <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('smtp_host') is-invalid @enderror" 
                                               id="smtp_host" name="smtp_host" value="{{ old('smtp_host', $emailSetting->smtp_host) }}" 
                                               placeholder="smtp.gmail.com" required>
                                        @error('smtp_host')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="smtp_port" class="form-label">SMTP Port <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('smtp_port') is-invalid @enderror" 
                                               id="smtp_port" name="smtp_port" value="{{ old('smtp_port', $emailSetting->smtp_port) }}" 
                                               min="1" max="65535" required>
                                        @error('smtp_port')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="smtp_encryption" class="form-label">Encryption <span class="text-danger">*</span></label>
                                        <select class="form-select @error('smtp_encryption') is-invalid @enderror" 
                                                id="smtp_encryption" name="smtp_encryption" required>
                                            <option value="tls" {{ old('smtp_encryption', $emailSetting->smtp_encryption) == 'tls' ? 'selected' : '' }}>TLS</option>
                                            <option value="ssl" {{ old('smtp_encryption', $emailSetting->smtp_encryption) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                            <option value="none" {{ old('smtp_encryption', $emailSetting->smtp_encryption) == null ? 'selected' : '' }}>None</option>
                                        </select>
                                        @error('smtp_encryption')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                                   {{ old('is_active', $emailSetting->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                <strong>Active Configuration</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="smtp_username" class="form-label">SMTP Username <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('smtp_username') is-invalid @enderror" 
                                               id="smtp_username" name="smtp_username" value="{{ old('smtp_username', $emailSetting->smtp_username) }}" required>
                                        @error('smtp_username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="smtp_password" class="form-label">SMTP Password</label>
                                        <input type="password" class="form-control @error('smtp_password') is-invalid @enderror" 
                                               id="smtp_password" name="smtp_password" placeholder="Leave blank to keep current password">
                                        @error('smtp_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if($emailSetting->smtp_password)
                                            <small class="text-muted">Password is currently set. Leave blank to keep existing password.</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Email Configuration -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-envelope me-2"></i>Email Configuration</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="from_email" class="form-label">From Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('from_email') is-invalid @enderror" 
                                               id="from_email" name="from_email" value="{{ old('from_email', $emailSetting->from_email) }}" required>
                                        @error('from_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="from_name" class="form-label">From Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('from_name') is-invalid @enderror" 
                                               id="from_name" name="from_name" value="{{ old('from_name', $emailSetting->from_name) }}" required>
                                        @error('from_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="reply_to_email" class="form-label">Reply-To Email</label>
                                        <input type="email" class="form-control @error('reply_to_email') is-invalid @enderror" 
                                               id="reply_to_email" name="reply_to_email" value="{{ old('reply_to_email', $emailSetting->reply_to_email) }}">
                                        @error('reply_to_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="reply_to_name" class="form-label">Reply-To Name</label>
                                        <input type="text" class="form-control @error('reply_to_name') is-invalid @enderror" 
                                               id="reply_to_name" name="reply_to_name" value="{{ old('reply_to_name', $emailSetting->reply_to_name) }}">
                                        @error('reply_to_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="company_logo_url" class="form-label">Company Logo URL</label>
                                    <input type="url" class="form-control @error('company_logo_url') is-invalid @enderror" 
                                           id="company_logo_url" name="company_logo_url" value="{{ old('company_logo_url', $emailSetting->company_logo_url) }}"
                                           placeholder="https://yourcompany.com/logo.png">
                                    @error('company_logo_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Optional: URL to your company logo for email templates</small>
                                </div>

                                <div class="mb-3">
                                    <label for="email_signature" class="form-label">Email Signature</label>
                                    <textarea class="form-control @error('email_signature') is-invalid @enderror" 
                                              id="email_signature" name="email_signature" rows="4" 
                                              placeholder="Best regards,&#10;Your Company Name&#10;Phone: +44 123 456 7890&#10;Email: info@yourcompany.com">{{ old('email_signature', $emailSetting->email_signature) }}</textarea>
                                    @error('email_signature')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Notifications -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="bi bi-bell me-2"></i>Email Notifications</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">Select which email notifications to send automatically:</p>
                                
                                @php
                                    $enabledNotifications = old('enabled_notifications', $emailSetting->enabled_notifications ?: []);
                                @endphp

                                @foreach($notificationCategories as $category => $notifications)
                                    <div class="mb-4">
                                        <h6 class="text-primary border-bottom pb-1 mb-2">{{ $category }}</h6>
                                        @foreach($notifications as $key => $label)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="notification_{{ $key }}" name="enabled_notifications[]" 
                                                       value="{{ $key }}" {{ in_array($key, $enabledNotifications) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="notification_{{ $key }}">
                                                    {{ $label }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Status Card -->
                        <div class="card shadow-sm mt-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Status</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-{{ $emailSetting->is_verified ? 'success' : 'warning' }} me-2">
                                        {{ $emailSetting->is_verified ? 'Verified' : 'Not Verified' }}
                                    </span>
                                    <small class="text-muted">Configuration Status</small>
                                </div>
                                
                                @if($emailSetting->last_tested_at)
                                    <small class="text-muted d-block">
                                        Last tested: {{ $emailSetting->last_tested_at->format('M j, Y g:i A') }}
                                    </small>
                                @endif

                                @if($emailSetting->test_results && !$emailSetting->is_verified)
                                    <small class="text-danger d-block mt-2">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        {{ $emailSetting->test_results }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                            </a>
                            @if(auth()->user()->isCompanyAdmin())
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-2"></i>Save Email Settings
                                </button>
                            @else
                                <div class="alert alert-warning mb-0 ms-3">
                                    <i class="bi bi-lock me-2"></i>Only Company Administrators can modify email settings.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Test Email Modal -->
<div class="modal fade" id="testEmailModal" tabindex="-1" aria-labelledby="testEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="testEmailModalLabel">Test Email Configuration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="testEmailForm">
                    <div class="mb-3">
                        <label for="test_email" class="form-label">Test Email Address</label>
                        <input type="email" class="form-control" id="test_email" name="test_email" required 
                               placeholder="Enter email address to receive test email">
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        This will send a test email using your current configuration to verify it's working correctly.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="sendTestEmailBtn">
                    <i class="bi bi-envelope me-2"></i>Send Test Email
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Usage Stats Modal -->
<div class="modal fade" id="usageStatsModal" tabindex="-1" aria-labelledby="usageStatsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="usageStatsModalLabel">Email Usage Statistics</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row" id="usageStatsContent">
                    <div class="col-12 text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading usage statistics...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Test Email functionality
    const testEmailBtn = document.getElementById('testEmailBtn');
    const testEmailModal = new bootstrap.Modal(document.getElementById('testEmailModal'));
    const sendTestEmailBtn = document.getElementById('sendTestEmailBtn');
    const testEmailForm = document.getElementById('testEmailForm');

    testEmailBtn.addEventListener('click', function() {
        testEmailModal.show();
    });

    sendTestEmailBtn.addEventListener('click', function() {
        const formData = new FormData(testEmailForm);
        const testEmail = formData.get('test_email');
        
        if (!testEmail) {
            alert('Please enter a test email address');
            return;
        }

        // Show loading state
        sendTestEmailBtn.disabled = true;
        sendTestEmailBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';

        fetch('{{ route("email-settings.test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ test_email: testEmail })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Test email sent successfully! Check your inbox.');
                testEmailModal.hide();
                // Reload page to update verification status
                setTimeout(() => window.location.reload(), 1000);
            } else {
                alert('Test failed: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error sending test email: ' + error.message);
        })
        .finally(() => {
            sendTestEmailBtn.disabled = false;
            sendTestEmailBtn.innerHTML = '<i class="bi bi-envelope me-2"></i>Send Test Email';
        });
    });

    // Usage Stats functionality
    const usageStatsBtn = document.getElementById('usageStatsBtn');
    const usageStatsModal = new bootstrap.Modal(document.getElementById('usageStatsModal'));
    const usageStatsContent = document.getElementById('usageStatsContent');

    usageStatsBtn.addEventListener('click', function() {
        usageStatsModal.show();
        loadUsageStats();
    });

    function loadUsageStats() {
        fetch('{{ route("email-settings.usage") }}')
        .then(response => response.json())
        .then(data => {
            usageStatsContent.innerHTML = `
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <h3 class="text-primary">${data.emails_sent_today}</h3>
                            <p class="mb-0">Emails Sent Today</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <h3 class="text-info">${data.emails_sent_month}</h3>
                            <p class="mb-0">Emails Sent This Month</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-3">
                    <div class="card">
                        <div class="card-body">
                            <h6>Configuration Status</h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Verification Status:</span>
                                <span class="badge bg-${data.is_verified ? 'success' : 'warning'}">
                                    ${data.is_verified ? 'Verified' : 'Not Verified'}
                                </span>
                            </div>
                            ${data.last_tested ? `
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span>Last Tested:</span>
                                    <span class="text-muted">${data.last_tested}</span>
                                </div>
                            ` : ''}
                            ${data.test_results && !data.is_verified ? `
                                <div class="mt-2">
                                    <small class="text-danger">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        ${data.test_results}
                                    </small>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            usageStatsContent.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Error loading usage statistics: ${error.message}
                    </div>
                </div>
            `;
        });
    }

    // Form validation - temporarily disabled for debugging
    const form = document.getElementById('emailSettingsForm');
    form.addEventListener('submit', function(e) {
        console.log('Form submitted - validation disabled for debugging');
        // Validation temporarily disabled
    });
});
</script>
@endpush
