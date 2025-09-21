            <!-- Contact Details Tab -->
            <div class="tab-pane fade" id="contact" role="tabpanel">
                <div class="row g-4">
                    <!-- Primary Contact -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Primary Contact</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="primary_contact_name" class="form-label">Contact Name</label>
                                        <input type="text" class="form-control" id="primary_contact_name" name="primary_contact_name" value="{{ old('primary_contact_name', $company->primary_contact_name) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="primary_contact_title" class="form-label">Job Title</label>
                                        <input type="text" class="form-control" id="primary_contact_title" name="primary_contact_title" value="{{ old('primary_contact_title', $company->primary_contact_title) }}">
                                    </div>
                                    <div class="col-12">
                                        <label for="primary_contact_email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="primary_contact_email" name="primary_contact_email" value="{{ old('primary_contact_email', $company->primary_contact_email) }}">
                                    </div>
                                    <div class="col-12">
                                        <label for="primary_contact_phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="primary_contact_phone" name="primary_contact_phone" value="{{ old('primary_contact_phone', $company->primary_contact_phone) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Secondary Contact -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Secondary Contact</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="secondary_contact_name" class="form-label">Contact Name</label>
                                        <input type="text" class="form-control" id="secondary_contact_name" name="secondary_contact_name" value="{{ old('secondary_contact_name', $company->secondary_contact_name) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="secondary_contact_title" class="form-label">Job Title</label>
                                        <input type="text" class="form-control" id="secondary_contact_title" name="secondary_contact_title" value="{{ old('secondary_contact_title', $company->secondary_contact_title) }}">
                                    </div>
                                    <div class="col-12">
                                        <label for="secondary_contact_email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="secondary_contact_email" name="secondary_contact_email" value="{{ old('secondary_contact_email', $company->secondary_contact_email) }}">
                                    </div>
                                    <div class="col-12">
                                        <label for="secondary_contact_phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="secondary_contact_phone" name="secondary_contact_phone" value="{{ old('secondary_contact_phone', $company->secondary_contact_phone) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- General Company Contact -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="bi bi-building me-2"></i>General Company Contact</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="company_email" class="form-label">Main Email Address</label>
                                        <input type="email" class="form-control" id="company_email" name="email" value="{{ old('email', $company->email) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="company_phone" class="form-label">Main Phone Number</label>
                                        <input type="tel" class="form-control" id="company_phone" name="phone" value="{{ old('phone', $company->phone) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="company_website" class="form-label">Website</label>
                                        <input type="url" class="form-control" id="company_website" name="website" value="{{ old('website', $company->website) }}" placeholder="https://example.com">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Addresses Tab -->
            <div class="tab-pane fade" id="address" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Business Address</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="address" class="form-label">Street Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="2">{{ old('address', $company->address) }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $company->city) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="state" class="form-label">County/State</label>
                                        <input type="text" class="form-control" id="state" name="state" value="{{ old('state', $company->state) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="zip_code" class="form-label">Postcode</label>
                                        <input type="text" class="form-control" id="zip_code" name="zip_code" value="{{ old('zip_code', $company->zip_code) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="country" class="form-label">Country</label>
                                        <select class="form-select" id="country" name="country">
                                            <option value="">Select Country</option>
                                            <option value="GB" {{ old('country', $company->country) === 'GB' ? 'selected' : '' }}>United Kingdom</option>
                                            <option value="IE" {{ old('country', $company->country) === 'IE' ? 'selected' : '' }}>Ireland</option>
                                            <option value="US" {{ old('country', $company->country) === 'US' ? 'selected' : '' }}>United States</option>
                                            <option value="CA" {{ old('country', $company->country) === 'CA' ? 'selected' : '' }}>Canada</option>
                                            <option value="AU" {{ old('country', $company->country) === 'AU' ? 'selected' : '' }}>Australia</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-building me-2"></i>Registered Address</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="same_as_business" onchange="copyBusinessAddress()">
                                    <label class="form-check-label" for="same_as_business">
                                        Same as business address
                                    </label>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="registered_address" class="form-label">Street Address</label>
                                        <textarea class="form-control" id="registered_address" name="registered_address" rows="2">{{ old('registered_address', $company->registered_address) }}</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="registered_city" class="form-label">City</label>
                                        <input type="text" class="form-control" id="registered_city" name="registered_city" value="{{ old('registered_city', $company->registered_city) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="registered_state" class="form-label">County/State</label>
                                        <input type="text" class="form-control" id="registered_state" name="registered_state" value="{{ old('registered_state', $company->registered_state) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="registered_zip_code" class="form-label">Postcode</label>
                                        <input type="text" class="form-control" id="registered_zip_code" name="registered_zip_code" value="{{ old('registered_zip_code', $company->registered_zip_code) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="registered_country" class="form-label">Country</label>
                                        <select class="form-select" id="registered_country" name="registered_country">
                                            <option value="">Select Country</option>
                                            <option value="GB" {{ old('registered_country', $company->registered_country) === 'GB' ? 'selected' : '' }}>United Kingdom</option>
                                            <option value="IE" {{ old('registered_country', $company->registered_country) === 'IE' ? 'selected' : '' }}>Ireland</option>
                                            <option value="US" {{ old('registered_country', $company->registered_country) === 'US' ? 'selected' : '' }}>United States</option>
                                            <option value="CA" {{ old('registered_country', $company->registered_country) === 'CA' ? 'selected' : '' }}>Canada</option>
                                            <option value="AU" {{ old('registered_country', $company->registered_country) === 'AU' ? 'selected' : '' }}>Australia</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banking Details Tab -->
            <div class="tab-pane fade" id="banking" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-bank me-2"></i>Banking Information</h5>
                    </div>
                    <div class="card-body">
                        @if(!auth()->user()->isCompanyAdmin())
                            <div class="alert alert-warning" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>View Only:</strong> Banking details can only be modified by Company Administrators for security reasons.
                            </div>
                        @else
                            <div class="alert alert-info" role="alert">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Secure Information:</strong> Your banking details are encrypted and stored securely. This information is used for payment processing and financial reporting.
                            </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="bank_name" class="form-label">Bank Name</label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name" 
                                       value="{{ old('bank_name', $company->bank_name) }}"
                                       {{ !auth()->user()->isCompanyAdmin() ? 'readonly' : '' }}>
                            </div>
                            <div class="col-md-6">
                                <label for="bank_account_name" class="form-label">Account Name</label>
                                <input type="text" class="form-control" id="bank_account_name" name="bank_account_name" 
                                       value="{{ old('bank_account_name', $company->bank_account_name) }}"
                                       {{ !auth()->user()->isCompanyAdmin() ? 'readonly' : '' }}>
                            </div>
                            <div class="col-md-6">
                                <label for="bank_account_number" class="form-label">Account Number</label>
                                <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" 
                                       value="{{ old('bank_account_number', $company->bank_account_number) }}"
                                       {{ !auth()->user()->isCompanyAdmin() ? 'readonly' : '' }}>
                            </div>
                            <div class="col-md-6">
                                <label for="bank_sort_code" class="form-label">Sort Code</label>
                                <input type="text" class="form-control" id="bank_sort_code" name="bank_sort_code" 
                                       value="{{ old('bank_sort_code', $company->bank_sort_code) }}" 
                                       placeholder="12-34-56"
                                       {{ !auth()->user()->isCompanyAdmin() ? 'readonly' : '' }}>
                            </div>
                            <div class="col-md-6">
                                <label for="iban" class="form-label">IBAN</label>
                                <input type="text" class="form-control" id="iban" name="iban" 
                                       value="{{ old('iban', $company->iban) }}" 
                                       placeholder="GB29 NWBK 6016 1331 9268 19"
                                       {{ !auth()->user()->isCompanyAdmin() ? 'readonly' : '' }}>
                            </div>
                            <div class="col-md-6">
                                <label for="swift_code" class="form-label">SWIFT/BIC Code</label>
                                <input type="text" class="form-control" id="swift_code" name="swift_code" 
                                       value="{{ old('swift_code', $company->swift_code) }}"
                                       {{ !auth()->user()->isCompanyAdmin() ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Insurance Tab -->
            <div class="tab-pane fade" id="insurance" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="bi bi-shield me-2"></i>Public Liability Insurance</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="public_liability_insurer" class="form-label">Insurance Company</label>
                                        <input type="text" class="form-control" id="public_liability_insurer" name="public_liability_insurer" value="{{ old('public_liability_insurer', $company->public_liability_insurer) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="public_liability_policy_number" class="form-label">Policy Number</label>
                                        <input type="text" class="form-control" id="public_liability_policy_number" name="public_liability_policy_number" value="{{ old('public_liability_policy_number', $company->public_liability_policy_number) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="public_liability_expiry" class="form-label">Expiry Date</label>
                                        <input type="date" class="form-control" id="public_liability_expiry" name="public_liability_expiry" value="{{ old('public_liability_expiry', $company->public_liability_expiry?->format('Y-m-d')) }}">
                                    </div>
                                    <div class="col-12">
                                        <label for="public_liability_amount" class="form-label">Coverage Amount (£)</label>
                                        <input type="number" class="form-control" id="public_liability_amount" name="public_liability_amount" value="{{ old('public_liability_amount', $company->public_liability_amount) }}" step="0.01" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0"><i class="bi bi-people me-2"></i>Employers Liability Insurance</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="employers_liability_insurer" class="form-label">Insurance Company</label>
                                        <input type="text" class="form-control" id="employers_liability_insurer" name="employers_liability_insurer" value="{{ old('employers_liability_insurer', $company->employers_liability_insurer) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="employers_liability_policy_number" class="form-label">Policy Number</label>
                                        <input type="text" class="form-control" id="employers_liability_policy_number" name="employers_liability_policy_number" value="{{ old('employers_liability_policy_number', $company->employers_liability_policy_number) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="employers_liability_expiry" class="form-label">Expiry Date</label>
                                        <input type="date" class="form-control" id="employers_liability_expiry" name="employers_liability_expiry" value="{{ old('employers_liability_expiry', $company->employers_liability_expiry?->format('Y-m-d')) }}">
                                    </div>
                                    <div class="col-12">
                                        <label for="employers_liability_amount" class="form-label">Coverage Amount (£)</label>
                                        <input type="number" class="form-control" id="employers_liability_amount" name="employers_liability_amount" value="{{ old('employers_liability_amount', $company->employers_liability_amount) }}" step="0.01" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Compliance Tab -->
            <div class="tab-pane fade" id="compliance" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>Health & Safety Policies</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="health_safety_policy" class="form-label">Health & Safety Policy</label>
                                        <input type="file" class="form-control" id="health_safety_policy" name="health_safety_policy" accept=".pdf,.doc,.docx">
                                        @if($company->health_safety_policy)
                                            <div class="mt-2">
                                                <small class="text-success">
                                                    <i class="bi bi-file-earmark-check me-1"></i>Current file uploaded
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-12">
                                        <label for="health_safety_policy_date" class="form-label">Policy Date</label>
                                        <input type="date" class="form-control" id="health_safety_policy_date" name="health_safety_policy_date" value="{{ old('health_safety_policy_date', $company->health_safety_policy_date?->format('Y-m-d')) }}">
                                    </div>
                                    <div class="col-12">
                                        <label for="risk_assessment_policy" class="form-label">Risk Assessment Policy</label>
                                        <input type="file" class="form-control" id="risk_assessment_policy" name="risk_assessment_policy" accept=".pdf,.doc,.docx">
                                        @if($company->risk_assessment_policy)
                                            <div class="mt-2">
                                                <small class="text-success">
                                                    <i class="bi bi-file-earmark-check me-1"></i>Current file uploaded
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-12">
                                        <label for="risk_assessment_policy_date" class="form-label">Policy Date</label>
                                        <input type="date" class="form-control" id="risk_assessment_policy_date" name="risk_assessment_policy_date" value="{{ old('risk_assessment_policy_date', $company->risk_assessment_policy_date?->format('Y-m-d')) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-award me-2"></i>Certifications</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="construction_line_number" class="form-label">Constructionline Number</label>
                                        <input type="text" class="form-control" id="construction_line_number" name="construction_line_number" value="{{ old('construction_line_number', $company->construction_line_number) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="construction_line_expiry" class="form-label">Expiry Date</label>
                                        <input type="date" class="form-control" id="construction_line_expiry" name="construction_line_expiry" value="{{ old('construction_line_expiry', $company->construction_line_expiry?->format('Y-m-d')) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="chas_number" class="form-label">CHAS Number</label>
                                        <input type="text" class="form-control" id="chas_number" name="chas_number" value="{{ old('chas_number', $company->chas_number) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="chas_expiry" class="form-label">CHAS Expiry</label>
                                        <input type="date" class="form-control" id="chas_expiry" name="chas_expiry" value="{{ old('chas_expiry', $company->chas_expiry?->format('Y-m-d')) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="safe_contractor_number" class="form-label">SafeContractor Number</label>
                                        <input type="text" class="form-control" id="safe_contractor_number" name="safe_contractor_number" value="{{ old('safe_contractor_number', $company->safe_contractor_number) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="safe_contractor_expiry" class="form-label">SafeContractor Expiry</label>
                                        <input type="date" class="form-control" id="safe_contractor_expiry" name="safe_contractor_expiry" value="{{ old('safe_contractor_expiry', $company->safe_contractor_expiry?->format('Y-m-d')) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preferences Tab -->
            <div class="tab-pane fade" id="preferences" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="bi bi-gear me-2"></i>System Preferences</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="timezone" class="form-label">Timezone <span class="text-danger">*</span></label>
                                <select class="form-select" id="timezone" name="timezone" required>
                                    <option value="Europe/London" {{ old('timezone', $company->timezone) === 'Europe/London' ? 'selected' : '' }}>London (GMT/BST)</option>
                                    <option value="Europe/Dublin" {{ old('timezone', $company->timezone) === 'Europe/Dublin' ? 'selected' : '' }}>Dublin (GMT/IST)</option>
                                    <option value="America/New_York" {{ old('timezone', $company->timezone) === 'America/New_York' ? 'selected' : '' }}>New York (EST/EDT)</option>
                                    <option value="America/Los_Angeles" {{ old('timezone', $company->timezone) === 'America/Los_Angeles' ? 'selected' : '' }}>Los Angeles (PST/PDT)</option>
                                    <option value="Australia/Sydney" {{ old('timezone', $company->timezone) === 'Australia/Sydney' ? 'selected' : '' }}>Sydney (AEST/AEDT)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                                <select class="form-select" id="currency" name="currency" required>
                                    <option value="GBP" {{ old('currency', $company->currency) === 'GBP' ? 'selected' : '' }}>GBP (£) - British Pound</option>
                                    <option value="EUR" {{ old('currency', $company->currency) === 'EUR' ? 'selected' : '' }}>EUR (€) - Euro</option>
                                    <option value="USD" {{ old('currency', $company->currency) === 'USD' ? 'selected' : '' }}>USD ($) - US Dollar</option>
                                    <option value="CAD" {{ old('currency', $company->currency) === 'CAD' ? 'selected' : '' }}>CAD ($) - Canadian Dollar</option>
                                    <option value="AUD" {{ old('currency', $company->currency) === 'AUD' ? 'selected' : '' }}>AUD ($) - Australian Dollar</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email Settings Tab -->
            <div class="tab-pane fade" id="email" role="tabpanel">
                @include('email-settings.form')
            </div>
