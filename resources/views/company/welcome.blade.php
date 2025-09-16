<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Welcome to GoBillo - {{ $company->name }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0066cc;
            --secondary-color: #f8f9fa;
            --accent-color: #28a745;
            --text-dark: #2c3e50;
            --text-muted: #6c757d;
            --gradient-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            background: linear-gradient(45deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }

        .welcome-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem 0;
        }

        .welcome-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
        }

        .welcome-header {
            background: var(--gradient-bg);
            color: white;
            text-align: center;
            padding: 3rem 2rem;
            position: relative;
        }

        .welcome-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .welcome-content {
            position: relative;
            z-index: 2;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        .welcome-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .welcome-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 0;
        }

        .welcome-body {
            padding: 3rem 2rem;
        }

        .trial-info {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            text-align: center;
        }

        .trial-info h4 {
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .trial-info p {
            margin-bottom: 0;
            opacity: 0.9;
        }

        .onboarding-form {
            margin-bottom: 2rem;
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .form-section-title i {
            background: var(--gradient-bg);
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            font-size: 0.9rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.1);
            background-color: white;
        }

        .btn-complete {
            background: var(--gradient-bg);
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-complete:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .btn-skip {
            background: transparent;
            border: 2px solid #dee2e6;
            border-radius: 12px;
            padding: 0.75rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-muted);
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-skip:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .action-card {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--text-dark);
        }

        .action-card:hover {
            border-color: var(--primary-color);
            background: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            color: var(--text-dark);
        }

        .action-icon {
            width: 50px;
            height: 50px;
            background: var(--gradient-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: white;
        }

        .action-title {
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .action-description {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 0;
        }

        .skip-section {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid #e9ecef;
        }

        /* Loading State */
        .btn-loading {
            position: relative;
            color: transparent !important;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid transparent;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .welcome-header {
                padding: 2rem 1.5rem;
            }
            
            .welcome-title {
                font-size: 2rem;
            }
            
            .welcome-body {
                padding: 2rem 1.5rem;
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="container">
            <div class="welcome-card">
                <!-- Welcome Header -->
                <div class="welcome-header">
                    <div class="welcome-content">
                        <div class="success-icon">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <h1 class="welcome-title">Welcome to GoBillo!</h1>
                        <p class="welcome-subtitle">
                            Congratulations {{ auth()->user()->first_name }}! Your account for <strong>{{ $company->name }}</strong> has been created successfully.
                        </p>
                    </div>
                </div>

                <!-- Welcome Body -->
                <div class="welcome-body">
                    <!-- Trial Information -->
                    <div class="trial-info">
                        <h4><i class="bi bi-gift me-2"></i>Your 30-Day Free Trial Starts Now!</h4>
                        <p>
                            Explore all features with no limitations. Your trial expires on 
                            <strong>{{ $company->trial_ends_at->format('F j, Y') }}</strong>
                        </p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success border-0" style="border-radius: 12px;">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger border-0" style="border-radius: 12px;">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <!-- Onboarding Form -->
                    <form id="onboardingForm" action="{{ route('company.onboarding') }}" method="POST" class="onboarding-form">
                        @csrf
                        
                        <div class="form-section">
                            <h3 class="form-section-title">
                                <i class="bi bi-building"></i>
                                Complete Your Company Profile
                            </h3>
                            <p class="text-muted mb-3">Help us customize your experience by providing some additional details about your company.</p>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="business_type" class="form-label">Business Type</label>
                                        <select class="form-control @error('business_type') is-invalid @enderror" 
                                                id="business_type" name="business_type">
                                            <option value="">Select business type</option>
                                            <option value="General Contractor" {{ old('business_type') == 'General Contractor' ? 'selected' : '' }}>General Contractor</option>
                                            <option value="Subcontractor" {{ old('business_type') == 'Subcontractor' ? 'selected' : '' }}>Subcontractor</option>
                                            <option value="Construction Management" {{ old('business_type') == 'Construction Management' ? 'selected' : '' }}>Construction Management</option>
                                            <option value="Architecture Firm" {{ old('business_type') == 'Architecture Firm' ? 'selected' : '' }}>Architecture Firm</option>
                                            <option value="Engineering Firm" {{ old('business_type') == 'Engineering Firm' ? 'selected' : '' }}>Engineering Firm</option>
                                            <option value="Developer" {{ old('business_type') == 'Developer' ? 'selected' : '' }}>Developer</option>
                                            <option value="Other" {{ old('business_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('business_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="industry_sector" class="form-label">Industry Sector</label>
                                        <select class="form-control @error('industry_sector') is-invalid @enderror" 
                                                id="industry_sector" name="industry_sector">
                                            <option value="">Select industry sector</option>
                                            <option value="Residential" {{ old('industry_sector') == 'Residential' ? 'selected' : '' }}>Residential</option>
                                            <option value="Commercial" {{ old('industry_sector') == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                                            <option value="Industrial" {{ old('industry_sector') == 'Industrial' ? 'selected' : '' }}>Industrial</option>
                                            <option value="Infrastructure" {{ old('industry_sector') == 'Infrastructure' ? 'selected' : '' }}>Infrastructure</option>
                                            <option value="Renovation" {{ old('industry_sector') == 'Renovation' ? 'selected' : '' }}>Renovation</option>
                                            <option value="Mixed" {{ old('industry_sector') == 'Mixed' ? 'selected' : '' }}>Mixed</option>
                                        </select>
                                        @error('industry_sector')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="website" class="form-label">Company Website</label>
                                <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                       id="website" name="website" value="{{ old('website') }}" 
                                       placeholder="https://www.yourcompany.com">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-section">
                            <h3 class="form-section-title">
                                <i class="bi bi-geo-alt"></i>
                                Business Address
                            </h3>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Street Address</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                       id="address" name="address" value="{{ old('address') }}" 
                                       placeholder="123 Main Street">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                               id="city" name="city" value="{{ old('city') }}" 
                                               placeholder="New York">
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="state" class="form-label">State</label>
                                        <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                               id="state" name="state" value="{{ old('state') }}" 
                                               placeholder="NY">
                                        @error('state')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="zip_code" class="form-label">ZIP Code</label>
                                        <input type="text" class="form-control @error('zip_code') is-invalid @enderror" 
                                               id="zip_code" name="zip_code" value="{{ old('zip_code') }}" 
                                               placeholder="10001">
                                        @error('zip_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="country" class="form-label">Country</label>
                                <select class="form-control @error('country') is-invalid @enderror" 
                                        id="country" name="country">
                                    <option value="United States" {{ old('country', 'United States') == 'United States' ? 'selected' : '' }}>United States</option>
                                    <option value="Canada" {{ old('country') == 'Canada' ? 'selected' : '' }}>Canada</option>
                                    <option value="United Kingdom" {{ old('country') == 'United Kingdom' ? 'selected' : '' }}>United Kingdom</option>
                                    <option value="Australia" {{ old('country') == 'Australia' ? 'selected' : '' }}>Australia</option>
                                    <option value="Other" {{ old('country') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="mb-3">
                                <label for="description" class="form-label">Company Description (Optional)</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" 
                                          placeholder="Tell us about your company, services, and specializations...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-complete" id="completeBtn">
                            <i class="bi bi-check-circle me-2"></i>Complete Setup & Enter Dashboard
                        </button>
                    </form>

                    <!-- Skip Section -->
                    <div class="skip-section">
                        <p class="text-muted mb-3">You can complete this information later in your company settings.</p>
                        <a href="{{ route('dashboard') }}" class="btn btn-skip">
                            <i class="bi bi-arrow-right me-2"></i>Skip for Now & Go to Dashboard
                        </a>
                    </div>

                    <!-- Quick Actions -->
                    <div class="quick-actions">
                        <a href="{{ route('dashboard') }}" class="action-card">
                            <div class="action-icon">
                                <i class="bi bi-speedometer2"></i>
                            </div>
                            <div class="action-title">Dashboard</div>
                            <div class="action-description">Get an overview of your business</div>
                        </a>
                        
                        <a href="{{ route('projects.create') }}" class="action-card">
                            <div class="action-icon">
                                <i class="bi bi-plus-circle"></i>
                            </div>
                            <div class="action-title">Create Project</div>
                            <div class="action-description">Start your first project</div>
                        </a>
                        
                        <a href="{{ route('settings.index') }}" class="action-card">
                            <div class="action-icon">
                                <i class="bi bi-gear"></i>
                            </div>
                            <div class="action-title">Settings</div>
                            <div class="action-description">Configure your account</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form submission with loading state
        document.getElementById('onboardingForm').addEventListener('submit', function() {
            const completeBtn = document.getElementById('completeBtn');
            completeBtn.classList.add('btn-loading');
            completeBtn.disabled = true;
        });

        // Auto-format ZIP code
        document.getElementById('zip_code').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.substring(0, 5);
            }
            this.value = value;
        });
    </script>
</body>
</html>
