<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Construction Operative – Personal Data Capture Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .form-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin: 2rem auto;
            max-width: 800px;
            overflow: hidden;
        }
        
        .form-header {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .form-body {
            padding: 2rem;
        }
        
        .section-title {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(52, 152, 219, 0.3);
        }
        
        .form-check-input {
            border-radius: 5px;
            border: 2px solid #dee2e6;
        }
        
        .form-check-input:checked {
            background-color: #3498db;
            border-color: #3498db;
        }
        
        .required {
            color: #e74c3c;
        }
        
        @media (max-width: 768px) {
            .form-container {
                margin: 1rem;
                border-radius: 15px;
            }
            
            .form-header, .form-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h1><i class="bi bi-file-earmark-person me-2"></i>Construction Operative</h1>
                <h3>Personal Data Capture Form</h3>
                <p class="mb-0">Please fill out all required information accurately</p>
            </div>
            
            <div class="form-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <h6><i class="bi bi-exclamation-triangle me-2"></i>Please correct the following errors:</h6>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('operative-data-form.submit', $form->share_token) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- 1. Personal Information -->
                    <div class="mb-4">
                        <h4 class="section-title">
                            <i class="bi bi-person me-2"></i>1. Personal Information
                        </h4>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="full_name" class="form-label">Full Name <span class="required">*</span></label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="{{ old('full_name') }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="date_of_birth" class="form-label">Date of Birth <span class="required">*</span></label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                       value="{{ old('date_of_birth') }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="nationality" class="form-label">Nationality <span class="required">*</span></label>
                                <input type="text" class="form-control" id="nationality" name="nationality" 
                                       value="{{ old('nationality') }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="mobile_number" class="form-label">Mobile Number <span class="required">*</span></label>
                                <input type="tel" class="form-control" id="mobile_number" name="mobile_number" 
                                       value="{{ old('mobile_number') }}" required>
                            </div>
                            
                            <div class="col-12">
                                <label for="email_address" class="form-label">Email Address <span class="required">*</span></label>
                                <input type="email" class="form-control" id="email_address" name="email_address" 
                                       value="{{ old('email_address') }}" required>
                            </div>
                            
                            <div class="col-md-8">
                                <label for="home_address" class="form-label">Home Address <span class="required">*</span></label>
                                <textarea class="form-control" id="home_address" name="home_address" 
                                          rows="2" required>{{ old('home_address') }}</textarea>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="postcode" class="form-label">Postcode <span class="required">*</span></label>
                                <input type="text" class="form-control" id="postcode" name="postcode" 
                                       value="{{ old('postcode') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 2. Emergency Contact -->
                    <div class="mb-4">
                        <h4 class="section-title">
                            <i class="bi bi-telephone me-2"></i>2. Emergency Contact
                        </h4>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="emergency_contact_name" class="form-label">Name <span class="required">*</span></label>
                                <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" 
                                       value="{{ old('emergency_contact_name') }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="emergency_contact_relationship" class="form-label">Relationship <span class="required">*</span></label>
                                <input type="text" class="form-control" id="emergency_contact_relationship" name="emergency_contact_relationship" 
                                       value="{{ old('emergency_contact_relationship') }}" required>
                            </div>
                            
                            <div class="col-12">
                                <label for="emergency_contact_number" class="form-label">Contact Number <span class="required">*</span></label>
                                <input type="tel" class="form-control" id="emergency_contact_number" name="emergency_contact_number" 
                                       value="{{ old('emergency_contact_number') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 3. Work Documentation -->
                    <div class="mb-4">
                        <h4 class="section-title">
                            <i class="bi bi-file-earmark-text me-2"></i>3. Work Documentation
                        </h4>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="national_insurance_number" class="form-label">National Insurance Number (NINO) <span class="required">*</span></label>
                                <input type="text" class="form-control" id="national_insurance_number" name="national_insurance_number" 
                                       value="{{ old('national_insurance_number') }}" required placeholder="AB123456C">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="utr_number" class="form-label">UTR Number (if self-employed)</label>
                                <input type="text" class="form-control" id="utr_number" name="utr_number" 
                                       value="{{ old('utr_number') }}">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="cscs_card_type" class="form-label">CSCS Card Type</label>
                                <input type="text" class="form-control" id="cscs_card_type" name="cscs_card_type" 
                                       value="{{ old('cscs_card_type') }}">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="cscs_card_number" class="form-label">CSCS Card Number</label>
                                <input type="text" class="form-control" id="cscs_card_number" name="cscs_card_number" 
                                       value="{{ old('cscs_card_number') }}">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="cscs_card_expiry" class="form-label">CSCS Card Expiry Date</label>
                                <input type="date" class="form-control" id="cscs_card_expiry" name="cscs_card_expiry" 
                                       value="{{ old('cscs_card_expiry') }}">
                            </div>
                            
                            <!-- CSCS Card Images -->
                            <div class="col-12">
                                <h6 class="text-muted mb-3 mt-2">
                                    <i class="bi bi-camera me-2"></i>CSCS Card Images (Optional)
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="cscs_card_front_image" class="form-label">CSCS Card Front</label>
                                        <input type="file" class="form-control" id="cscs_card_front_image" name="cscs_card_front_image" 
                                               accept="image/*" capture="environment">
                                        <div class="form-text">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Take a photo or upload image of the front of your CSCS card
                                        </div>
                                        <div class="mt-2" id="front_preview"></div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="cscs_card_back_image" class="form-label">CSCS Card Back</label>
                                        <input type="file" class="form-control" id="cscs_card_back_image" name="cscs_card_back_image" 
                                               accept="image/*" capture="environment">
                                        <div class="form-text">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Take a photo or upload image of the back of your CSCS card
                                        </div>
                                        <div class="mt-2" id="back_preview"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Right to Work in the UK <span class="required">*</span></label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="right_to_work_uk" id="right_to_work_yes" value="1" 
                                                   {{ old('right_to_work_uk') == '1' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="right_to_work_yes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="right_to_work_uk" id="right_to_work_no" value="0" 
                                                   {{ old('right_to_work_uk') == '0' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="right_to_work_no">No</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label">Passport / ID Provided <span class="required">*</span></label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="passport_id_provided" id="passport_yes" value="1" 
                                                   {{ old('passport_id_provided') == '1' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="passport_yes">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="passport_id_provided" id="passport_no" value="0" 
                                                   {{ old('passport_id_provided') == '0' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="passport_no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 4. Bank Details -->
                    <div class="mb-4">
                        <h4 class="section-title">
                            <i class="bi bi-bank me-2"></i>4. Bank Details (For Payment Purposes)
                        </h4>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="bank_name" class="form-label">Bank Name <span class="required">*</span></label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name" 
                                       value="{{ old('bank_name') }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="account_holder_name" class="form-label">Account Holder's Name <span class="required">*</span></label>
                                <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" 
                                       value="{{ old('account_holder_name') }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="sort_code" class="form-label">Sort Code <span class="required">*</span></label>
                                <input type="text" class="form-control" id="sort_code" name="sort_code" 
                                       value="{{ old('sort_code') }}" required placeholder="12-34-56" maxlength="6">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="account_number" class="form-label">Account Number <span class="required">*</span></label>
                                <input type="text" class="form-control" id="account_number" name="account_number" 
                                       value="{{ old('account_number') }}" required maxlength="8">
                            </div>
                        </div>
                    </div>
                    
                    <!-- 5. Trade and Qualifications -->
                    <div class="mb-4">
                        <h4 class="section-title">
                            <i class="bi bi-tools me-2"></i>5. Trade and Qualifications
                        </h4>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="primary_trade" class="form-label">Primary Trade <span class="required">*</span></label>
                                <input type="text" class="form-control" id="primary_trade" name="primary_trade" 
                                       value="{{ old('primary_trade') }}" required placeholder="e.g., Labourer, Bricklayer">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="years_experience" class="form-label">Years of Experience <span class="required">*</span></label>
                                <input type="number" class="form-control" id="years_experience" name="years_experience" 
                                       value="{{ old('years_experience') }}" required min="0" max="50">
                            </div>
                            
                            <div class="col-12">
                                <label for="qualifications_certifications" class="form-label">List Any Relevant Qualifications or Certifications</label>
                                <textarea class="form-control" id="qualifications_certifications" name="qualifications_certifications" 
                                          rows="3">{{ old('qualifications_certifications') }}</textarea>
                            </div>
                            
                            <div class="col-12">
                                <label for="other_cards_licenses" class="form-label">Other Cards/Licenses (e.g., CPCS, NPORS)</label>
                                <textarea class="form-control" id="other_cards_licenses" name="other_cards_licenses" 
                                          rows="2">{{ old('other_cards_licenses') }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 6. Declaration -->
                    <div class="mb-4">
                        <h4 class="section-title">
                            <i class="bi bi-check-square me-2"></i>6. Declaration
                        </h4>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="declaration_confirmed" name="declaration_confirmed" 
                                   value="1" {{ old('declaration_confirmed') ? 'checked' : '' }} required>
                            <label class="form-check-label" for="declaration_confirmed">
                                <strong>I confirm that the above information is accurate and true to the best of my knowledge.</strong>
                            </label>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            By submitting this form, you agree to the processing of your personal data for employment purposes.
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-send me-2"></i>Submit Form
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Format sort code input
        document.getElementById('sort_code').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 6) {
                e.target.value = value;
            }
        });
        
        // Format account number input
        document.getElementById('account_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value;
        });
        
        // Image preview functionality
        function setupImagePreview(inputId, previewId) {
            document.getElementById(inputId).addEventListener('change', function(e) {
                const file = e.target.files[0];
                const preview = document.getElementById(previewId);
                
                if (file) {
                    // Validate file type
                    if (!file.type.startsWith('image/')) {
                        alert('Please select a valid image file.');
                        e.target.value = '';
                        return;
                    }
                    
                    // Validate file size (max 5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('Image size must be less than 5MB.');
                        e.target.value = '';
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = `
                            <div class="card" style="max-width: 200px;">
                                <img src="${e.target.result}" class="card-img-top" style="height: 120px; object-fit: cover;" alt="Preview">
                                <div class="card-body p-2">
                                    <small class="text-muted">${file.name}</small>
                                    <br><small class="text-muted">${(file.size / 1024).toFixed(1)} KB</small>
                                </div>
                            </div>
                        `;
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.innerHTML = '';
                }
            });
        }
        
        // Setup previews for both front and back images
        setupImagePreview('cscs_card_front_image', 'front_preview');
        setupImagePreview('cscs_card_back_image', 'back_preview');
    </script>
</body>
</html>
