@php
    $emailSetting = auth()->user()->company->activeEmailSetting ?? auth()->user()->company->emailSettings()->first();
@endphp

<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-envelope-gear me-2"></i>Email Settings</h5>
    </div>
    <div class="card-body">
        <!-- Action Buttons -->
        <div class="d-flex gap-2 mb-4">
            <button type="button" class="btn btn-outline-primary btn-sm" id="testEmailBtn">
                <i class="bi bi-envelope-check me-2"></i>Test Email
            </button>
            <button type="button" class="btn btn-outline-info btn-sm" id="usageStatsBtn">
                <i class="bi bi-graph-up me-2"></i>Usage Stats
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="previewTemplateBtn">
                <i class="bi bi-eye me-2"></i>Preview Template
            </button>
        </div>

        <div class="row">
            <!-- SMTP Configuration -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="bi bi-gear me-2"></i>SMTP Configuration</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtp_host" class="form-label">SMTP Host <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('smtp_host') is-invalid @enderror" 
                                           id="smtp_host" name="smtp_host" 
                                           value="{{ old('smtp_host', $emailSetting->smtp_host ?? '') }}" required>
                                    @error('smtp_host')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtp_port" class="form-label">SMTP Port <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('smtp_port') is-invalid @enderror" 
                                           id="smtp_port" name="smtp_port" 
                                           value="{{ old('smtp_port', $emailSetting->smtp_port ?? 587) }}" required>
                                    @error('smtp_port')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtp_username" class="form-label">SMTP Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('smtp_username') is-invalid @enderror" 
                                           id="smtp_username" name="smtp_username" 
                                           value="{{ old('smtp_username', $emailSetting->smtp_username ?? '') }}" required>
                                    @error('smtp_username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtp_password" class="form-label">SMTP Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('smtp_password') is-invalid @enderror" 
                                           id="smtp_password" name="smtp_password" 
                                           placeholder="{{ $emailSetting && $emailSetting->smtp_password ? '••••••••' : 'Enter password' }}">
                                    @error('smtp_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtp_encryption" class="form-label">Encryption</label>
                                    <select class="form-select @error('smtp_encryption') is-invalid @enderror" 
                                            id="smtp_encryption" name="smtp_encryption">
                                        <option value="">None</option>
                                        <option value="tls" {{ old('smtp_encryption', $emailSetting->smtp_encryption ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ old('smtp_encryption', $emailSetting->smtp_encryption ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    </select>
                                    @error('smtp_encryption')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Details -->
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-envelope me-2"></i>Email Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="from_email" class="form-label">From Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('from_email') is-invalid @enderror" 
                                           id="from_email" name="from_email" 
                                           value="{{ old('from_email', $emailSetting->from_email ?? '') }}" required>
                                    @error('from_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="from_name" class="form-label">From Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('from_name') is-invalid @enderror" 
                                           id="from_name" name="from_name" 
                                           value="{{ old('from_name', $emailSetting->from_name ?? auth()->user()->company->name) }}" required>
                                    @error('from_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reply_to_email" class="form-label">Reply To Email</label>
                                    <input type="email" class="form-control @error('reply_to_email') is-invalid @enderror" 
                                           id="reply_to_email" name="reply_to_email" 
                                           value="{{ old('reply_to_email', $emailSetting->reply_to_email ?? '') }}">
                                    @error('reply_to_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="reply_to_name" class="form-label">Reply To Name</label>
                                    <input type="text" class="form-control @error('reply_to_name') is-invalid @enderror" 
                                           id="reply_to_name" name="reply_to_name" 
                                           value="{{ old('reply_to_name', $emailSetting->reply_to_name ?? '') }}">
                                    @error('reply_to_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications & Settings -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-bell me-2"></i>Email Notifications</h6>
                    </div>
                    <div class="card-body">
                        @php
                            // Temporarily disable JSON decoding to fix the error
                            $enabledNotifications = old('enabled_notifications', []);
                            $notificationTypes = \App\Models\EmailSetting::getNotificationTypes();
                        @endphp

                        @foreach($notificationTypes as $key => $label)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" 
                                       id="notification_{{ $key }}" 
                                       name="enabled_notifications[]" 
                                       value="{{ $key }}"
                                       {{ in_array($key, $enabledNotifications) ? 'checked' : '' }}>
                                <label class="form-check-label" for="notification_{{ $key }}">
                                    {{ $label }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Email Settings -->
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" 
                                   id="is_active" name="is_active" value="1"
                                   {{ old('is_active', $emailSetting && $emailSetting->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <strong>Active</strong>
                                <small class="d-block text-muted">Enable email sending for this company</small>
                            </label>
                        </div>

                        <div class="mb-3">
                            <label for="email_signature" class="form-label">Email Signature</label>
                            <textarea class="form-control @error('email_signature') is-invalid @enderror" 
                                      id="email_signature" name="email_signature" rows="4"
                                      placeholder="Best regards,&#10;{{ auth()->user()->company->name }}">{{ old('email_signature', $emailSetting->email_signature ?? '') }}</textarea>
                            @error('email_signature')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="logo_url" class="form-label">Logo URL</label>
                            <input type="url" class="form-control @error('logo_url') is-invalid @enderror" 
                                   id="logo_url" name="logo_url" 
                                   value="{{ old('logo_url', $emailSetting->logo_url ?? '') }}"
                                   placeholder="https://example.com/logo.png">
                            @error('logo_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Email Modal -->
<div class="modal fade" id="testEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-envelope-check me-2"></i>Test Email Configuration
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Send a test email to verify your configuration is working correctly.</p>
                <div class="mb-3">
                    <label for="testEmailAddress" class="form-label">Test Email Address</label>
                    <input type="email" class="form-control" id="testEmailAddress" 
                           placeholder="Enter email to send test to"
                           value="{{ auth()->user()->email }}">
                    <small class="text-muted">A test email will be sent to this address</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="sendTestEmail">
                    <i class="bi bi-send me-2"></i>Send Test Email
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Include the JavaScript for email settings -->
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Test Email functionality - Open Modal
    document.getElementById('testEmailBtn').addEventListener('click', function() {
        // Open the test email modal
        const modal = new bootstrap.Modal(document.getElementById('testEmailModal'));
        modal.show();
    });
    
    // Send Test Email
    document.getElementById('sendTestEmail').addEventListener('click', function() {
        const btn = this;
        const originalText = btn.innerHTML;
        const testEmail = document.getElementById('testEmailAddress').value;
        
        if (!testEmail) {
            alert('Please enter a test email address');
            return;
        }
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
        
        fetch('{{ route("settings.email.test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                smtp_host: document.getElementById('smtp_host').value,
                smtp_port: document.getElementById('smtp_port').value,
                smtp_username: document.getElementById('smtp_username').value,
                smtp_password: document.getElementById('smtp_password').value,
                smtp_encryption: document.getElementById('smtp_encryption').value,
                from_email: document.getElementById('from_email').value,
                from_name: document.getElementById('from_name').value,
                test_email: testEmail
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Test email sent successfully to ' + testEmail);
                // Close the modal
                bootstrap.Modal.getInstance(document.getElementById('testEmailModal')).hide();
            } else {
                showAlert('danger', 'Failed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            showAlert('danger', 'Failed to test email configuration: ' + error.message);
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });

    // Usage Stats functionality
    document.getElementById('usageStatsBtn').addEventListener('click', function() {
        fetch('{{ route("settings.email.usage") }}')
        .then(response => response.json())
        .then(data => {
            const today = data.emails_sent_today || 0;
            const month = data.emails_sent_month || 0;
            const lastTested = data.last_tested || 'Never';
            const isVerified = data.is_verified ? 'Yes' : 'No';
            
            const message = `Email Usage Statistics:\n\nEmails Today: ${today}\nEmails This Month: ${month}\n\nLast Tested: ${lastTested}\nVerified: ${isVerified}`;
            alert(message);
        })
        .catch(error => {
            showAlert('danger', 'Failed to load usage statistics');
        });
    });

    // Preview Template functionality
    document.getElementById('previewTemplateBtn').addEventListener('click', function() {
        // Show a modal or dropdown to select template type
        const templates = [
            'invoice_created',
            'project_variation_created', 
            'task_assigned',
            'payment_received'
        ];
        
        // For simplicity, let's preview invoice_created template
        const templateType = 'invoice_created';
        
        fetch('{{ route("settings.email.preview") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                template_type: templateType  // Changed from 'template' to 'template_type'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Open preview in new window
                const newWindow = window.open('', '_blank');
                newWindow.document.write(data.html);
                newWindow.document.close();
            } else {
                showAlert('danger', 'Failed to generate preview: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            showAlert('danger', 'Failed to generate preview: ' + error.message);
        });
    });

    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.card-body');
        container.insertBefore(alertDiv, container.firstChild);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
});
</script>
@endpush
