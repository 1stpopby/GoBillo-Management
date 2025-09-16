@extends('layouts.app')

@section('title', 'Edit Employee')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employees.show', $employee) }}">{{ $employee->full_name }}</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
                <h1 class="page-title">Edit Employee</h1>
                <p class="page-subtitle">Update {{ $employee->full_name }}'s information</p>
            </div>
        </div>
    </div>

    <form action="{{ route('employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Personal Information -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="employee_id" class="form-label">Employee ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('employee_id') is-invalid @enderror" 
                                       id="employee_id" name="employee_id" value="{{ old('employee_id', $employee->employee_id) }}" required>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="avatar" class="form-label">Avatar</label>
                                <input type="file" class="form-control @error('avatar') is-invalid @enderror" 
                                       id="avatar" name="avatar" accept="image/*">
                                @if($employee->avatar)
                                    <div class="mt-2">
                                        <small class="text-muted">Current avatar:</small><br>
                                        <img src="{{ $employee->avatar_url }}" alt="Current avatar" class="rounded" width="50" height="50">
                                    </div>
                                @endif
                                @error('avatar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                       id="first_name" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                       id="last_name" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $employee->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $employee->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                       id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth?->format('Y-m-d')) }}">
                                @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                    <option value="">Select gender...</option>
                                    <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $employee->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="nationality" class="form-label">Nationality</label>
                                <input type="text" class="form-control @error('nationality') is-invalid @enderror" 
                                       id="nationality" name="nationality" value="{{ old('nationality', $employee->nationality) }}" 
                                       placeholder="e.g., British, American, Canadian">
                                @error('nationality')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="country" class="form-label">Country</label>
                                <select class="form-select @error('country') is-invalid @enderror" id="country" name="country">
                                    <option value="US" {{ old('country', $employee->country) == 'US' ? 'selected' : '' }}>United States</option>
                                    <option value="CA" {{ old('country', $employee->country) == 'CA' ? 'selected' : '' }}>Canada</option>
                                    <option value="GB" {{ old('country', $employee->country) == 'GB' ? 'selected' : '' }}>United Kingdom</option>
                                    <option value="AU" {{ old('country', $employee->country) == 'AU' ? 'selected' : '' }}>Australia</option>
                                </select>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="2">{{ old('address', $employee->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                       id="city" name="city" value="{{ old('city', $employee->city) }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="state" class="form-label">State/Province</label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                       id="state" name="state" value="{{ old('state', $employee->state) }}">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="postcode" class="form-label">Postcode</label>
                                <input type="text" class="form-control @error('postcode') is-invalid @enderror" 
                                       id="postcode" name="postcode" value="{{ old('postcode', $employee->postcode) }}" 
                                       placeholder="e.g., SW1A 1AA">
                                @error('postcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="zip_code" class="form-label">ZIP/Postal Code (Legacy)</label>
                                <input type="text" class="form-control @error('zip_code') is-invalid @enderror" 
                                       id="zip_code" name="zip_code" value="{{ old('zip_code', $employee->zip_code) }}">
                                @error('zip_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Use Postcode field above for UK addresses</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employment Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Employment Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="">Select role...</option>
                                    @foreach($roles as $value => $label)
                                        <option value="{{ $value }}" {{ old('role', $employee->role) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="department" class="form-label">Department</label>
                                <input type="text" class="form-control @error('department') is-invalid @enderror" 
                                       id="department" name="department" value="{{ old('department', $employee->department) }}" 
                                       placeholder="e.g., Construction, Engineering, Administration">
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="job_title" class="form-label">Job Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('job_title') is-invalid @enderror" 
                                       id="job_title" name="job_title" value="{{ old('job_title', $employee->job_title) }}" required>
                                @error('job_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="hire_date" class="form-label">Hire Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('hire_date') is-invalid @enderror" 
                                       id="hire_date" name="hire_date" value="{{ old('hire_date', $employee->hire_date->format('Y-m-d')) }}" required>
                                @error('hire_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="termination_date" class="form-label">Termination Date</label>
                                <input type="date" class="form-control @error('termination_date') is-invalid @enderror" 
                                       id="termination_date" name="termination_date" value="{{ old('termination_date', $employee->termination_date?->format('Y-m-d')) }}">
                                @error('termination_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="employment_status" class="form-label">Employment Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('employment_status') is-invalid @enderror" id="employment_status" name="employment_status" required>
                                    <option value="active" {{ old('employment_status', $employee->employment_status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('employment_status', $employee->employment_status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="terminated" {{ old('employment_status', $employee->employment_status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                                    <option value="on_leave" {{ old('employment_status', $employee->employment_status) == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                                </select>
                                @error('employment_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="employment_type" class="form-label">Employment Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('employment_type') is-invalid @enderror" id="employment_type" name="employment_type" required>
                                    <option value="full_time" {{ old('employment_type', $employee->employment_type) == 'full_time' ? 'selected' : '' }}>Full Time</option>
                                    <option value="part_time" {{ old('employment_type', $employee->employment_type) == 'part_time' ? 'selected' : '' }}>Part Time</option>
                                    <option value="contract" {{ old('employment_type', $employee->employment_type) == 'contract' ? 'selected' : '' }}>Contract</option>
                                    <option value="consultant" {{ old('employment_type', $employee->employment_type) == 'consultant' ? 'selected' : '' }}>Consultant</option>
                                </select>
                                @error('employment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label for="salary" class="form-label">Salary</label>
                                <div class="input-group">
                                    <span class="input-group-text">£</span>
                                    <input type="number" class="form-control @error('salary') is-invalid @enderror" 
                                           id="salary" name="salary" value="{{ old('salary', $employee->salary) }}" step="0.01" min="0">
                                    @error('salary')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="salary_type" class="form-label">Salary Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('salary_type') is-invalid @enderror" id="salary_type" name="salary_type" required>
                                    <option value="hourly" {{ old('salary_type', $employee->salary_type) == 'hourly' ? 'selected' : '' }}>Hourly</option>
                                    <option value="monthly" {{ old('salary_type', $employee->salary_type) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="yearly" {{ old('salary_type', $employee->salary_type) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                </select>
                                @error('salary_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trade & Qualifications -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Trade & Qualifications</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="primary_trade" class="form-label">Primary Trade</label>
                                <input type="text" class="form-control @error('primary_trade') is-invalid @enderror" 
                                       id="primary_trade" name="primary_trade" value="{{ old('primary_trade', $employee->primary_trade) }}" 
                                       placeholder="e.g., Labourer, Bricklayer, Electrician, Plumber">
                                @error('primary_trade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="years_experience" class="form-label">Years of Experience</label>
                                <input type="number" class="form-control @error('years_experience') is-invalid @enderror" 
                                       id="years_experience" name="years_experience" value="{{ old('years_experience', $employee->years_experience) }}" 
                                       min="0" max="50" placeholder="e.g., 5">
                                @error('years_experience')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="skills" class="form-label">Skills</label>
                                <input type="text" class="form-control @error('skills') is-invalid @enderror" 
                                       id="skills" name="skills" value="{{ old('skills', $employee->skills ? implode(', ', $employee->skills) : '') }}" 
                                       placeholder="e.g., Project Management, AutoCAD, Safety Protocols (comma separated)">
                                @error('skills')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Separate skills with commas</small>
                            </div>

                            <div class="col-12">
                                <label for="qualifications" class="form-label">Relevant Qualifications or Certifications</label>
                                <input type="text" class="form-control @error('qualifications') is-invalid @enderror" 
                                       id="qualifications" name="qualifications" value="{{ old('qualifications', $employee->qualifications ? implode(', ', $employee->qualifications) : '') }}" 
                                       placeholder="e.g., Bachelor's in Civil Engineering, NVQ Level 3 Construction (comma separated)">
                                @error('qualifications')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Separate qualifications with commas</small>
                            </div>

                            <div class="col-12">
                                <label for="other_cards_licenses" class="form-label">Other Cards/Licenses</label>
                                <input type="text" class="form-control @error('other_cards_licenses') is-invalid @enderror" 
                                       id="other_cards_licenses" name="other_cards_licenses" value="{{ old('other_cards_licenses', $employee->other_cards_licenses ? implode(', ', $employee->other_cards_licenses) : '') }}" 
                                       placeholder="e.g., CPCS, NPORS, Driving License (comma separated)">
                                @error('other_cards_licenses')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Separate cards/licenses with commas (e.g., CPCS, NPORS)</small>
                            </div>

                            <div class="col-12">
                                <label for="certifications" class="form-label">Industry Certifications</label>
                                <input type="text" class="form-control @error('certifications') is-invalid @enderror" 
                                       id="certifications" name="certifications" value="{{ old('certifications', $employee->certifications ? implode(', ', $employee->certifications) : '') }}" 
                                       placeholder="e.g., PMP, OSHA 30, LEED AP, First Aid (comma separated)">
                                @error('certifications')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Separate certifications with commas</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Emergency Contact</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="emergency_contact_name" class="form-label">Contact Name</label>
                                <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                       id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}">
                                @error('emergency_contact_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="emergency_contact_phone" class="form-label">Contact Phone</label>
                                <input type="tel" class="form-control @error('emergency_contact_phone') is-invalid @enderror" 
                                       id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}">
                                @error('emergency_contact_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="emergency_contact_relationship" class="form-label">Relationship</label>
                                <input type="text" class="form-control @error('emergency_contact_relationship') is-invalid @enderror" 
                                       id="emergency_contact_relationship" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) }}" 
                                       placeholder="e.g., Spouse, Parent, Sibling">
                                @error('emergency_contact_relationship')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Work Documentation -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Work Documentation</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="national_insurance_number" class="form-label">National Insurance Number (NINO)</label>
                                <input type="text" class="form-control @error('national_insurance_number') is-invalid @enderror" 
                                       id="national_insurance_number" name="national_insurance_number" value="{{ old('national_insurance_number', $employee->national_insurance_number) }}" 
                                       placeholder="e.g., QQ 12 34 56 C">
                                @error('national_insurance_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="utr_number" class="form-label">UTR Number (if self-employed)</label>
                                <input type="text" class="form-control @error('utr_number') is-invalid @enderror" 
                                       id="utr_number" name="utr_number" value="{{ old('utr_number', $employee->utr_number) }}" 
                                       placeholder="e.g., 1234567890">
                                @error('utr_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="cscs_card_type" class="form-label">CSCS Card Type</label>
                                <input type="text" class="form-control @error('cscs_card_type') is-invalid @enderror" 
                                       id="cscs_card_type" name="cscs_card_type" value="{{ old('cscs_card_type', $employee->cscs_card_type) }}" 
                                       placeholder="e.g., Green Labourer, Blue Skilled Worker">
                                @error('cscs_card_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="cscs_card_number" class="form-label">CSCS Card Number</label>
                                <input type="text" class="form-control @error('cscs_card_number') is-invalid @enderror" 
                                       id="cscs_card_number" name="cscs_card_number" value="{{ old('cscs_card_number', $employee->cscs_card_number) }}" 
                                       placeholder="e.g., 123456789">
                                @error('cscs_card_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="cscs_card_expiry" class="form-label">CSCS Card Expiry Date</label>
                                <input type="date" class="form-control @error('cscs_card_expiry') is-invalid @enderror" 
                                       id="cscs_card_expiry" name="cscs_card_expiry" value="{{ old('cscs_card_expiry', $employee->cscs_card_expiry?->format('Y-m-d')) }}">
                                @error('cscs_card_expiry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="right_to_work_uk" class="form-label">Right to Work in the UK</label>
                                <select class="form-select @error('right_to_work_uk') is-invalid @enderror" id="right_to_work_uk" name="right_to_work_uk">
                                    <option value="">Select...</option>
                                    <option value="1" {{ old('right_to_work_uk', $employee->right_to_work_uk) == '1' ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ old('right_to_work_uk', $employee->right_to_work_uk) == '0' ? 'selected' : '' }}>No</option>
                                </select>
                                @error('right_to_work_uk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="passport_id_provided" class="form-label">Passport / ID Provided</label>
                                <select class="form-select @error('passport_id_provided') is-invalid @enderror" id="passport_id_provided" name="passport_id_provided">
                                    <option value="">Select...</option>
                                    <option value="1" {{ old('passport_id_provided', $employee->passport_id_provided) == '1' ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ old('passport_id_provided', $employee->passport_id_provided) == '0' ? 'selected' : '' }}>No</option>
                                </select>
                                @error('passport_id_provided')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank Details (For Payment Purposes) -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Bank Details (For Payment Purposes)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="bank_name" class="form-label">Bank Name</label>
                                <input type="text" class="form-control @error('bank_name') is-invalid @enderror" 
                                       id="bank_name" name="bank_name" value="{{ old('bank_name', $employee->bank_name) }}" 
                                       placeholder="e.g., Barclays, HSBC, Lloyds">
                                @error('bank_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="account_holder_name" class="form-label">Account Holder's Name</label>
                                <input type="text" class="form-control @error('account_holder_name') is-invalid @enderror" 
                                       id="account_holder_name" name="account_holder_name" value="{{ old('account_holder_name', $employee->account_holder_name) }}" 
                                       placeholder="Full name as on bank account">
                                @error('account_holder_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="sort_code" class="form-label">Sort Code</label>
                                <input type="text" class="form-control @error('sort_code') is-invalid @enderror" 
                                       id="sort_code" name="sort_code" value="{{ old('sort_code', $employee->sort_code) }}" 
                                       placeholder="e.g., 12-34-56" pattern="[0-9]{2}-[0-9]{2}-[0-9]{2}">
                                @error('sort_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="account_number" class="form-label">Account Number</label>
                                <input type="text" class="form-control @error('account_number') is-invalid @enderror" 
                                       id="account_number" name="account_number" value="{{ old('account_number', $employee->account_number) }}" 
                                       placeholder="e.g., 12345678" maxlength="8">
                                @error('account_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Additional Notes</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="4">{{ old('notes', $employee->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Current Site Allocations -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Current Site Allocations</h5>
                        <small class="text-muted">Manage allocations from the employee details page</small>
                    </div>
                    <div class="card-body">
                        @if($employee->activeSiteAllocations->count() > 0)
                            @foreach($employee->activeSiteAllocations as $allocation)
                                <div class="allocation-item p-3 border rounded mb-3">
                                    <h6 class="mb-2">{{ $allocation->site->name }}</h6>
                                    <div class="small text-muted">
                                        <div class="mb-1">
                                            <span class="badge bg-{{ $allocation->type_color }} me-2">{{ $allocation->type_display }}</span>
                                            <span class="badge bg-light text-dark">{{ $allocation->allocation_percentage }}%</span>
                                        </div>
                                        <div>
                                            {{ $allocation->allocated_from->format('M j, Y') }}
                                            @if($allocation->allocated_until)
                                                - {{ $allocation->allocated_until->format('M j, Y') }}
                                            @else
                                                - Ongoing
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-geo-alt display-4 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">No site allocations</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Submit Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-2"></i>Update Employee
                            </button>
                            <a href="{{ route('employees.show', $employee) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.allocation-item {
    background: #f8fafc;
    border: 1px solid #e5e7eb !important;
}
</style>
@endsection 