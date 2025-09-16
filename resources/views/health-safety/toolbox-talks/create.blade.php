@extends('layouts.app')

@section('title', 'Record Toolbox Talk')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="page-header-section mb-4">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="page-icon bg-info">
                    <i class="bi bi-megaphone"></i>
                </div>
            </div>
            <div class="col">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('health-safety.index') }}">Health & Safety</a></li>
                        <li class="breadcrumb-item active">Record Toolbox Talk</li>
                    </ol>
                </nav>
                <h1 class="page-title mb-0">Record Toolbox Talk</h1>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('health-safety.toolbox-talks.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <!-- Main Form Section -->
                    <div class="col-lg-8">
                        <h5 class="mb-3">Talk Details</h5>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Talk Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" 
                                   placeholder="e.g., Working at Heights Safety" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="site_id" class="form-label">Site <span class="text-danger">*</span></label>
                                <select class="form-select @error('site_id') is-invalid @enderror" 
                                        id="site_id" name="site_id" required>
                                    <option value="">Select Site</option>
                                    @foreach($sites as $site)
                                        <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>
                                            {{ $site->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('site_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="project_id" class="form-label">Project</label>
                                <select class="form-select @error('project_id') is-invalid @enderror" 
                                        id="project_id" name="project_id">
                                    <option value="">Select Project (Optional)</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="talk_date" class="form-label">Talk Date <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('talk_date') is-invalid @enderror" 
                                       id="talk_date" name="talk_date" 
                                       value="{{ old('talk_date', now()->format('Y-m-d\TH:i')) }}" required>
                                @error('talk_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="duration_minutes" class="form-label">Duration (minutes) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" 
                                       id="duration_minutes" name="duration_minutes" 
                                       value="{{ old('duration_minutes', 15) }}" min="1" required>
                                @error('duration_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="conducted_by" class="form-label">Conducted By <span class="text-danger">*</span></label>
                            <select class="form-select @error('conducted_by') is-invalid @enderror" 
                                    id="conducted_by" name="conducted_by" required>
                                <option value="">Select Person</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('conducted_by', auth()->id()) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('conducted_by')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Topics Covered Section -->
                        <h5 class="mb-3 mt-4">Topics Covered</h5>
                        
                        <div class="mb-3">
                            <label for="topics_covered" class="form-label">Main Topics <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('topics_covered') is-invalid @enderror" 
                                      id="topics_covered" name="topics_covered" rows="4" 
                                      placeholder="List the main safety topics discussed during the talk" required>{{ old('topics_covered') }}</textarea>
                            @error('topics_covered')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="key_points" class="form-label">Key Safety Points</label>
                            <textarea class="form-control @error('key_points') is-invalid @enderror" 
                                      id="key_points" name="key_points" rows="4" 
                                      placeholder="Important safety points emphasized during the talk">{{ old('key_points') }}</textarea>
                            @error('key_points')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="hazards_discussed" class="form-label">Hazards Discussed</label>
                            <textarea class="form-control @error('hazards_discussed') is-invalid @enderror" 
                                      id="hazards_discussed" name="hazards_discussed" rows="3" 
                                      placeholder="Specific hazards that were discussed">{{ old('hazards_discussed') }}</textarea>
                            @error('hazards_discussed')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Action Items Section -->
                        <h5 class="mb-3 mt-4">Follow-up Actions</h5>
                        
                        <div class="mb-3">
                            <label for="action_items" class="form-label">Action Items</label>
                            <textarea class="form-control @error('action_items') is-invalid @enderror" 
                                      id="action_items" name="action_items" rows="3" 
                                      placeholder="Any action items or follow-ups from the discussion">{{ old('action_items') }}</textarea>
                            @error('action_items')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Any additional notes or observations">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Side Panel -->
                    <div class="col-lg-4">
                        <!-- Attendees Section -->
                        <h5 class="mb-3">Attendees</h5>
                        
                        <div class="mb-3">
                            <label for="attendees" class="form-label">Select Attendees <span class="text-danger">*</span></label>
                            <div class="attendees-list border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                @foreach($employees as $employee)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" 
                                               name="attendees[]" value="{{ $employee->id }}" 
                                               id="attendee_{{ $employee->id }}"
                                               {{ in_array($employee->id, old('attendees', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="attendee_{{ $employee->id }}">
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                            @if($employee->job_title)
                                                <small class="text-muted">({{ $employee->job_title }})</small>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted">Check all employees who attended this talk</small>
                            @error('attendees')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="attendees_count" class="form-label">Total Attendees</label>
                            <input type="number" class="form-control" id="attendees_count" readonly value="0">
                            <small class="text-muted">Auto-calculated based on selection</small>
                        </div>

                        <!-- Talk Type Section -->
                        <h5 class="mb-3 mt-4">Talk Type</h5>
                        
                        <div class="mb-3">
                            <label for="talk_type" class="form-label">Type of Talk</label>
                            <select class="form-select @error('talk_type') is-invalid @enderror" 
                                    id="talk_type" name="talk_type">
                                <option value="daily" {{ old('talk_type') == 'daily' ? 'selected' : '' }}>Daily Briefing</option>
                                <option value="weekly" {{ old('talk_type') == 'weekly' ? 'selected' : '' }}>Weekly Safety Meeting</option>
                                <option value="incident" {{ old('talk_type') == 'incident' ? 'selected' : '' }}>Incident Review</option>
                                <option value="special" {{ old('talk_type') == 'special' ? 'selected' : '' }}>Special Topic</option>
                                <option value="emergency" {{ old('talk_type') == 'emergency' ? 'selected' : '' }}>Emergency Response</option>
                            </select>
                            @error('talk_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Attachments Section -->
                        <h5 class="mb-3 mt-4">Attachments</h5>
                        
                        <div class="mb-3">
                            <label for="attendance_sheet" class="form-label">Attendance Sheet</label>
                            <input type="file" class="form-control @error('attendance_sheet') is-invalid @enderror" 
                                   id="attendance_sheet" name="attendance_sheet" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Upload signed attendance sheet (PDF, JPG, PNG)</small>
                            @error('attendance_sheet')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="presentation_file" class="form-label">Presentation/Materials</label>
                            <input type="file" class="form-control @error('presentation_file') is-invalid @enderror" 
                                   id="presentation_file" name="presentation_file" accept=".pdf,.ppt,.pptx,.doc,.docx">
                            <small class="text-muted">Upload presentation or handout materials</small>
                            @error('presentation_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Weather Conditions -->
                        <h5 class="mb-3 mt-4">Conditions</h5>
                        
                        <div class="mb-3">
                            <label for="weather_conditions" class="form-label">Weather Conditions</label>
                            <input type="text" class="form-control @error('weather_conditions') is-invalid @enderror" 
                                   id="weather_conditions" name="weather_conditions" 
                                   value="{{ old('weather_conditions') }}" 
                                   placeholder="e.g., Clear, Rainy, Windy">
                            @error('weather_conditions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('health-safety.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancel
                            </a>
                            <div>
                                <button type="submit" name="action" value="save_and_new" class="btn btn-outline-info me-2">
                                    <i class="bi bi-save me-2"></i>Save & New
                                </button>
                                <button type="submit" name="action" value="save" class="btn btn-info">
                                    <i class="bi bi-check-circle me-2"></i>Record Talk
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .page-header-section {
        margin-bottom: 2rem;
    }

    .page-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }

    .page-icon.bg-info {
        background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%);
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1a202c;
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        color: #94a3b8;
    }

    .breadcrumb-item a {
        color: #64748b;
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        color: #0dcaf0;
    }

    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .form-label {
        font-weight: 500;
        color: #475569;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 0.625rem 0.875rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: #0dcaf0;
        box-shadow: 0 0 0 3px rgba(13, 202, 240, 0.1);
    }

    .form-check-input:checked {
        background-color: #0dcaf0;
        border-color: #0dcaf0;
    }

    .form-check-input:focus {
        border-color: #0dcaf0;
        box-shadow: 0 0 0 0.25rem rgba(13, 202, 240, 0.25);
    }

    h5 {
        color: #1e293b;
        font-weight: 600;
        font-size: 1.125rem;
    }

    textarea.form-control {
        resize: vertical;
    }

    .text-danger {
        color: #ef4444 !important;
    }

    .attendees-list {
        background-color: #f8fafc;
    }

    .attendees-list::-webkit-scrollbar {
        width: 6px;
    }

    .attendees-list::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .attendees-list::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .attendees-list::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .btn {
        border-radius: 8px;
        padding: 0.625rem 1.25rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-info {
        background: #0dcaf0;
        border-color: #0dcaf0;
    }

    .btn-info:hover {
        background: #0aa2c0;
        border-color: #0aa2c0;
        transform: translateY(-1px);
    }

    .btn-outline-info {
        color: #0dcaf0;
        border-color: #0dcaf0;
    }

    .btn-outline-info:hover {
        background: #0dcaf0;
        border-color: #0dcaf0;
    }

    .btn-secondary {
        background: #64748b;
        border-color: #64748b;
    }

    .btn-secondary:hover {
        background: #475569;
        border-color: #475569;
    }
</style>
@endpush

@push('scripts')
<script>
    // Count selected attendees
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('input[name="attendees[]"]');
        const countField = document.getElementById('attendees_count');
        
        function updateCount() {
            const checkedCount = document.querySelectorAll('input[name="attendees[]"]:checked').length;
            countField.value = checkedCount;
        }
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateCount);
        });
        
        updateCount(); // Initial count
    });
</script>
@endpush
@endsection


