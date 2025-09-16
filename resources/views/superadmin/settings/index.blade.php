@extends('layouts.superadmin')

@section('title', 'System Settings')
@section('page-title', 'System Settings')

@section('content')
<div class="mb-4">
    <p class="text-muted mb-0">Configure system-wide settings, API keys, and integrations</p>
</div>

<form method="POST" action="{{ route('superadmin.settings.update') }}">
    @csrf
    
    <div class="row g-4">
        <!-- General Settings -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-gear me-2"></i>General Settings
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($settings['general']) && $settings['general']->count() > 0)
                        @foreach($settings['general'] as $setting)
                            <div class="mb-3">
                                <label for="{{ $setting->key }}" class="form-label">
                                    {{ $setting->label }}
                                    @if($setting->is_required)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                
                                @if($setting->type === 'boolean')
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="{{ $setting->key }}" 
                                               name="{{ $setting->key }}" value="1" 
                                               {{ $setting->value ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $setting->key }}">
                                            {{ $setting->description }}
                                        </label>
                                    </div>
                                @else
                                    <input type="{{ $setting->type === 'string' ? 'text' : $setting->type }}" 
                                           class="form-control" id="{{ $setting->key }}" 
                                           name="{{ $setting->key }}" value="{{ $setting->value }}"
                                           {{ $setting->is_required ? 'required' : '' }}>
                                    @if($setting->description)
                                        <div class="form-text">{{ $setting->description }}</div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No general settings configured.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Payment Settings -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-credit-card me-2"></i>Payment Settings
                    </h5>
                    <button type="button" class="btn btn-light btn-sm" onclick="testStripe()">
                        <i class="bi bi-lightning me-1"></i>Test Connection
                    </button>
                </div>
                <div class="card-body">
                    @if(isset($settings['payment']) && $settings['payment']->count() > 0)
                        @foreach($settings['payment'] as $setting)
                            <div class="mb-3">
                                <label for="{{ $setting->key }}" class="form-label">
                                    {{ $setting->label }}
                                    @if($setting->is_required)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                
                                @if($setting->type === 'boolean')
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="{{ $setting->key }}" 
                                               name="{{ $setting->key }}" value="1" 
                                               {{ $setting->value ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $setting->key }}">
                                            {{ $setting->description }}
                                        </label>
                                    </div>
                                @else
                                    <input type="{{ $setting->is_encrypted ? 'password' : 'text' }}" 
                                           class="form-control payment-field" id="{{ $setting->key }}" 
                                           name="{{ $setting->key }}" 
                                           value="{{ $setting->value }}"
                                           placeholder="{{ $setting->is_encrypted ? '••••••••••••••••' : '' }}"
                                           data-service="stripe">
                                    @if($setting->description)
                                        <div class="form-text">{{ $setting->description }}</div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No payment settings configured.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Google Integrations -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-google me-2"></i>Google Integrations
                    </h5>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-light btn-sm" onclick="testGoogle('maps')">
                            <i class="bi bi-map me-1"></i>Test Maps
                        </button>
                        <button type="button" class="btn btn-light btn-sm" onclick="testGoogle('recaptcha')">
                            <i class="bi bi-shield-check me-1"></i>Test reCAPTCHA
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($settings['integrations']) && $settings['integrations']->count() > 0)
                        <div class="row g-3">
                            @foreach($settings['integrations'] as $setting)
                                <div class="col-md-6">
                                    <label for="{{ $setting->key }}" class="form-label">
                                        {{ $setting->label }}
                                        @if($setting->is_required)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    
                                    <input type="{{ $setting->is_encrypted ? 'password' : 'text' }}" 
                                           class="form-control integration-field" id="{{ $setting->key }}" 
                                           name="{{ $setting->key }}" 
                                           value="{{ $setting->value }}"
                                           placeholder="{{ $setting->is_encrypted ? '••••••••••••••••' : '' }}"
                                           data-service="google">
                                    @if($setting->description)
                                        <div class="form-text">{{ $setting->description }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No integration settings configured.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-check-circle me-2"></i>Save Settings
        </button>
    </div>
</form>

<!-- Test Results Modal -->
<div class="modal fade" id="testResultModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Connection Test Result</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="testResultBody">
                <!-- Test results will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function testStripe() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.disabled = true;
    button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Testing...';
    
    fetch('{{ route("superadmin.settings.test-stripe") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        showTestResult(data);
    })
    .catch(error => {
        showTestResult({
            success: false,
            message: 'Error testing connection: ' + error.message
        });
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

function testGoogle(service) {
    const button = event.target;
    const originalText = button.innerHTML;
    
    button.disabled = true;
    button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Testing...';
    
    fetch('{{ route("superadmin.settings.test-google") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ service: service })
    })
    .then(response => response.json())
    .then(data => {
        showTestResult(data);
    })
    .catch(error => {
        showTestResult({
            success: false,
            message: 'Error testing connection: ' + error.message
        });
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

function showTestResult(result) {
    const modal = new bootstrap.Modal(document.getElementById('testResultModal'));
    const body = document.getElementById('testResultBody');
    
    const alertClass = result.success ? 'alert-success' : 'alert-danger';
    const icon = result.success ? 'bi-check-circle' : 'bi-exclamation-triangle';
    
    body.innerHTML = `
        <div class="alert ${alertClass}" role="alert">
            <i class="bi ${icon} me-2"></i>${result.message}
        </div>
    `;
    
    modal.show();
}

// Handle conditional field requirements
document.addEventListener('DOMContentLoaded', function() {
    // Handle Stripe payments toggle
    const enableStripeCheckbox = document.querySelector('input[name="stripe_enabled"]');
    const stripeFields = document.querySelectorAll('.payment-field[data-service="stripe"]');
    
    if (enableStripeCheckbox && stripeFields.length > 0) {
        function toggleStripeFields() {
            const isEnabled = enableStripeCheckbox.checked;
            const paymentCard = enableStripeCheckbox.closest('.card');
            
            stripeFields.forEach(field => {
                if (isEnabled) {
                    field.setAttribute('required', 'required');
                    field.disabled = false;
                } else {
                    field.removeAttribute('required');
                    field.disabled = false; // Keep enabled so users can still enter keys
                }
            });
            
            // Visual feedback
            if (paymentCard) {
                paymentCard.style.opacity = isEnabled ? '1' : '0.7';
                const cardBody = paymentCard.querySelector('.card-body');
                if (cardBody) {
                    if (isEnabled) {
                        cardBody.style.background = '';
                    } else {
                        cardBody.style.background = 'rgba(0,0,0,0.02)';
                    }
                }
            }
            
            // Update required indicators
            stripeFields.forEach(field => {
                const label = document.querySelector(`label[for="${field.id}"]`);
                if (label) {
                    const requiredSpan = label.querySelector('.text-danger');
                    if (isEnabled && !requiredSpan) {
                        label.insertAdjacentHTML('beforeend', ' <span class="text-danger">*</span>');
                    } else if (!isEnabled && requiredSpan) {
                        requiredSpan.remove();
                    }
                }
            });
        }
        
        enableStripeCheckbox.addEventListener('change', toggleStripeFields);
        toggleStripeFields(); // Set initial state
    }
    
    // Google integrations don't have an enable/disable toggle, so they're always optional
    // Remove any required attributes from Google fields since they should be optional
    const googleFields = document.querySelectorAll('.integration-field[data-service="google"]');
    googleFields.forEach(field => {
        field.removeAttribute('required');
    });
});
</script>
@endpush

@push('styles')
<style>
.card-header h5 {
    font-weight: 600;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-group .btn {
    font-size: 0.875rem;
}
</style>
@endpush
