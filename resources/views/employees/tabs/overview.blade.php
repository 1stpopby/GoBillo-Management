<div class="row">
    <!-- Personal Information -->
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Personal Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-group">
                            <label class="detail-label">Email</label>
                            <div class="detail-value">
                                <a href="mailto:{{ $employee->email }}">{{ $employee->email }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-group">
                            <label class="detail-label">Phone</label>
                            <div class="detail-value">
                                @if($employee->phone)
                                    <a href="tel:{{ $employee->phone }}">{{ $employee->phone }}</a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-group">
                            <label class="detail-label">Date of Birth</label>
                            <div class="detail-value">
                                @if($employee->date_of_birth)
                                    {{ $employee->date_of_birth->format('F j, Y') }}
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-group">
                            <label class="detail-label">Gender</label>
                            <div class="detail-value">
                                @if($employee->gender)
                                    {{ ucfirst($employee->gender) }}
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($employee->full_address)
                        <div class="col-12">
                            <div class="detail-group">
                                <label class="detail-label">Address</label>
                                <div class="detail-value">{{ $employee->full_address }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Employment Information -->
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Employment Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-group">
                            <label class="detail-label">Role</label>
                            <div class="detail-value">
                                <span class="badge bg-{{ $employee->role_color }}">{{ $employee->role_display }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-group">
                            <label class="detail-label">Department</label>
                            <div class="detail-value">
                                {{ $employee->department ?: 'Not assigned' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-group">
                            <label class="detail-label">Hire Date</label>
                            <div class="detail-value">{{ $employee->hire_date->format('F j, Y') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-group">
                            <label class="detail-label">Employment Type</label>
                            <div class="detail-value">{{ ucfirst(str_replace('_', ' ', $employee->employment_type)) }}</div>
                        </div>
                    </div>
                    @if($employee->salary)
                        <div class="col-md-6">
                            <div class="detail-group">
                                <label class="detail-label">Salary</label>
                                <div class="detail-value">
                                    ${{ number_format($employee->salary, 2) }} / {{ $employee->salary_type }}
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($employee->termination_date)
                        <div class="col-md-6">
                            <div class="detail-group">
                                <label class="detail-label">Termination Date</label>
                                <div class="detail-value text-danger">
                                    {{ $employee->termination_date->format('F j, Y') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Skills & Qualifications -->
    @if($employee->skills || $employee->certifications || $employee->qualifications)
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Skills & Qualifications</h5>
                </div>
                <div class="card-body">
                    @if($employee->skills)
                        <div class="detail-group mb-3">
                            <label class="detail-label">Skills</label>
                            <div class="detail-value">
                                @foreach($employee->skills as $skill)
                                    <span class="badge bg-primary me-1 mb-1">{{ $skill }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($employee->certifications)
                        <div class="detail-group mb-3">
                            <label class="detail-label">Certifications</label>
                            <div class="detail-value">
                                @foreach($employee->certifications as $cert)
                                    <span class="badge bg-success me-1 mb-1">{{ $cert }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($employee->qualifications)
                        <div class="detail-group">
                            <label class="detail-label">Qualifications</label>
                            <div class="detail-value">
                                @foreach($employee->qualifications as $qualification)
                                    <span class="badge bg-info me-1 mb-1">{{ $qualification }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Emergency Contact -->
    @if($employee->emergency_contact_name)
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Emergency Contact</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="detail-group">
                                <label class="detail-label">Name</label>
                                <div class="detail-value">{{ $employee->emergency_contact_name }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-group">
                                <label class="detail-label">Phone</label>
                                <div class="detail-value">
                                    @if($employee->emergency_contact_phone)
                                        <a href="tel:{{ $employee->emergency_contact_phone }}">{{ $employee->emergency_contact_phone }}</a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-group">
                                <label class="detail-label">Relationship</label>
                                <div class="detail-value">{{ $employee->emergency_contact_relationship ?: 'Not specified' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Notes -->
    @if($employee->notes)
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Notes</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $employee->notes }}</p>
                </div>
            </div>
        </div>
    @endif
</div>


