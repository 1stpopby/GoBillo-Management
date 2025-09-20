@extends('layouts.app')

@section('title', 'Create Invoice')

@section('content')
<div class="container-fluid px-2 px-md-4">
    <!-- Mobile-Optimized Page Header -->
    <div class="page-header mb-3 mb-md-4">
        <div class="row align-items-center g-2">
            <div class="col-auto">
                <a href="{{ route('operative-dashboard') }}" class="btn btn-outline-secondary btn-sm d-md-none">
                    <i class="bi bi-arrow-left"></i>
                </a>
            </div>
            <div class="col">
                <nav aria-label="breadcrumb" class="d-none d-md-block">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('operative-dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Create Invoice</li>
                    </ol>
                </nav>
                <h1 class="page-title mb-1">
                    <span class="d-none d-sm-inline">Create Invoice</span>
                    <span class="d-sm-none">New Invoice</span>
                </h1>
                <p class="page-subtitle mb-0 d-none d-sm-block">Create a new invoice for work carried out</p>
            </div>
            <div class="col-auto d-none d-md-block">
                <a href="{{ route('operative-dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile-First Form Layout -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <!-- Desktop Card Wrapper -->
            <div class="card d-none d-md-block">
                <div class="card-header">
                    <h5 class="card-title mb-0">Invoice Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('operative-invoices.store') }}" method="POST" id="invoice-form-desktop">
                        @csrf
                        
                        <div class="row">
                            <!-- Manager Selection -->
                            <div class="col-md-6 mb-3">
                                <label for="manager_id_desktop" class="form-label">Site Manager <span class="text-danger">*</span></label>
                                <select class="form-select @error('manager_id') is-invalid @enderror" 
                                        id="manager_id_desktop" name="manager_id" required>
                                    <option value="">Select site manager...</option>
                                    @foreach($managers as $manager)
                                        <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('manager_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Site Selection -->
                            <div class="col-md-6 mb-3">
                                <label for="site_id_desktop" class="form-label">Site <span class="text-danger">*</span></label>
                                <select class="form-select @error('site_id') is-invalid @enderror" 
                                        id="site_id_desktop" name="site_id" required disabled>
                                    <option value="">Select site...</option>
                                </select>
                                @error('site_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Project Selection Row -->
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="project_id_desktop" class="form-label">Project <span class="text-danger">*</span></label>
                                <select class="form-select @error('project_id') is-invalid @enderror" 
                                        id="project_id_desktop" name="project_id" required disabled>
                                    <option value="">Select project...</option>
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Select the specific project this work relates to for proper cost allocation
                                </div>
                            </div>
                        </div>

                        <!-- Week Period -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="week_period_start_desktop" class="form-label">Week Starting <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('week_period_start') is-invalid @enderror" 
                                       id="week_period_start_desktop" name="week_period_start" 
                                       value="{{ old('week_period_start', now()->startOfWeek()->format('Y-m-d')) }}" required>
                                @error('week_period_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="week_period_end_desktop" class="form-label">Week Ending</label>
                                <input type="date" class="form-control" 
                                       id="week_period_end_desktop" name="week_period_end" 
                                       value="{{ old('week_period_end', now()->endOfWeek()->format('Y-m-d')) }}" readonly>
                            </div>
                        </div>

                        <!-- Desktop Weekly Timesheet -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="bi bi-calendar-week me-2"></i>Weekly Timesheet
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Day</th>
                                                <th>Date</th>
                                                <th>Worked</th>
                                                <th>Hours</th>
                                                <th>Description</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody id="timesheet-body-desktop">
                                            @php
                                                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                                $startDate = old('week_period_start', now()->startOfWeek());
                                                if (is_string($startDate)) {
                                                    $startDate = \Carbon\Carbon::parse($startDate);
                                                }
                                            @endphp
                                            @foreach($days as $index => $day)
                                                @php
                                                    $currentDate = $startDate->copy()->addDays($index);
                                                @endphp
                                                <tr>
                                                    <td><strong>{{ $day }}</strong></td>
                                                    <td>{{ $currentDate->format('M j') }}</td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input day-worked-desktop" type="checkbox" 
                                                                   id="worked_desktop_{{ $index }}" name="items[{{ $index }}][worked]" value="1"
                                                                   {{ old("items.{$index}.worked") ? 'checked' : '' }}>
                                                            <input type="hidden" name="items[{{ $index }}][day_of_week]" value="{{ $day }}">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.5" min="0" max="24" 
                                                               class="form-control hours-input-desktop" 
                                                               name="items[{{ $index }}][hours_worked]" 
                                                               value="{{ old("items.{$index}.hours_worked", 8) }}"
                                                               disabled>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control description-input-desktop" 
                                                               name="items[{{ $index }}][description]" 
                                                               value="{{ old("items.{$index}.description") }}"
                                                               placeholder="Work description..."
                                                               disabled>
                                                    </td>
                                                    <td>
                                                        <span class="amount-display-desktop">£0.00</span>
                                                        <input type="hidden" name="items[{{ $index }}][amount]" class="amount-input-desktop" value="0">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="notes_desktop" class="form-label">Notes (Optional)</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                         id="notes_desktop" name="notes" rows="3"
                                         placeholder="Add any additional notes or comments about this invoice...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Desktop Financial Summary -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="bi bi-calculator me-2"></i>Financial Summary
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Day Rate</label>
                                            <div class="input-group">
                                                <span class="input-group-text">£</span>
                                                <input type="text" class="form-control" id="day_rate_display_desktop" readonly>
                                                <input type="hidden" id="day_rate_desktop" value="{{ $employee->day_rate ?? 0 }}">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Total Hours</label>
                                            <input type="text" class="form-control" id="total_hours_display_desktop" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Gross Amount</label>
                                            <div class="input-group">
                                                <span class="input-group-text">£</span>
                                                <input type="text" class="form-control" id="gross_amount_display_desktop" readonly>
                                                <input type="hidden" name="gross_amount" id="gross_amount_desktop">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="cis_applicable_desktop" 
                                                       name="cis_applicable" {{ ($employee->cis_applicable ?? false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="cis_applicable_desktop">
                                                    <strong>CIS Applicable</strong>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3" id="cis_section_desktop" style="display: none;">
                                            <label class="form-label">CIS Rate (%)</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" id="cis_rate_desktop" 
                                                       name="cis_rate" value="{{ $employee->cis_rate ?? 20 }}">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                        <div class="mb-3" id="cis_deduction_section_desktop" style="display: none;">
                                            <label class="form-label">CIS Deduction</label>
                                            <div class="input-group">
                                                <span class="input-group-text">£</span>
                                                <input type="text" class="form-control" id="cis_deduction_display_desktop" readonly>
                                                <input type="hidden" name="cis_deduction" id="cis_deduction_desktop">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Net Amount</strong></label>
                                            <div class="input-group">
                                                <span class="input-group-text">£</span>
                                                <input type="text" class="form-control fw-bold" id="net_amount_display_desktop" readonly>
                                                <input type="hidden" name="net_amount" id="net_amount_desktop">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Desktop Form Actions -->
                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('operative-dashboard') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Create Invoice
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Mobile Form (No Card Wrapper) -->
            <div class="mobile-form d-block d-md-none">
                <form action="{{ route('operative-invoices.store') }}" method="POST" id="invoice-form-mobile">
                    @csrf
                    
                    <!-- Mobile Form Steps -->
                    <div class="mobile-form-section mb-4">
                        <h6 class="mobile-section-title">
                            <i class="bi bi-1-circle me-2"></i>Basic Details
                        </h6>
                        
                        <!-- Manager Selection -->
                        <div class="mb-3">
                            <label for="manager_id" class="form-label">Site Manager <span class="text-danger">*</span></label>
                            <select class="form-select @error('manager_id') is-invalid @enderror" 
                                    id="manager_id" name="manager_id" required>
                                <option value="">Select site manager...</option>
                                @foreach($managers as $manager)
                                    <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                        {{ $manager->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('manager_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Site Selection -->
                        <div class="mb-3">
                            <label for="site_id" class="form-label">Site <span class="text-danger">*</span></label>
                            <select class="form-select @error('site_id') is-invalid @enderror" 
                                    id="site_id" name="site_id" required disabled>
                                <option value="">Select site...</option>
                            </select>
                            @error('site_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Project Selection -->
                        <div class="mb-3">
                            <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                            <select class="form-select @error('project_id') is-invalid @enderror" 
                                    id="project_id" name="project_id" required disabled>
                                <option value="">Select project...</option>
                            </select>
                            @error('project_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Select the specific project this work relates to
                            </div>
                        </div>
                    </div>

                    <!-- Week Period Section -->
                    <div class="mobile-form-section mb-4">
                        <h6 class="mobile-section-title">
                            <i class="bi bi-2-circle me-2"></i>Week Period
                        </h6>
                        
                        <div class="mb-3">
                            <label for="week_period_start" class="form-label">Week Starting <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('week_period_start') is-invalid @enderror" 
                                   id="week_period_start" name="week_period_start" 
                                   value="{{ old('week_period_start', now()->startOfWeek()->format('Y-m-d')) }}" required>
                            @error('week_period_start')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="week_period_end" class="form-label">Week Ending</label>
                            <input type="date" class="form-control" 
                                   id="week_period_end" name="week_period_end" 
                                   value="{{ old('week_period_end', now()->endOfWeek()->format('Y-m-d')) }}" readonly>
                        </div>
                    </div>

                    <!-- Mobile Weekly Timesheet -->
                    <div class="mobile-form-section mb-4">
                        <h6 class="mobile-section-title">
                            <i class="bi bi-3-circle me-2"></i>Weekly Timesheet
                        </h6>
                        
                        @php
                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                            $startDate = old('week_period_start', now()->startOfWeek());
                            if (is_string($startDate)) {
                                $startDate = \Carbon\Carbon::parse($startDate);
                            }
                        @endphp
                        
                        <div class="timesheet-cards" id="timesheet-body">
                            @foreach($days as $index => $day)
                                @php
                                    $currentDate = $startDate->copy()->addDays($index);
                                @endphp
                                <div class="day-card mb-3">
                                    <div class="day-card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">{{ $day }}</h6>
                                                <small class="text-muted">{{ $currentDate->format('M j, Y') }}</small>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input day-worked" type="checkbox" 
                                                       id="worked_{{ $index }}" name="items[{{ $index }}][worked]" value="1"
                                                       {{ old("items.{$index}.worked") ? 'checked' : '' }}>
                                                <label class="form-check-label" for="worked_{{ $index }}">
                                                    <span class="worked-label">{{ old("items.{$index}.worked") ? 'Worked' : 'Off' }}</span>
                                                </label>
                                                <input type="hidden" name="items[{{ $index }}][day_of_week]" value="{{ $day }}">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="day-card-body" style="{{ old("items.{$index}.worked") ? '' : 'display: none;' }}">
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <label class="form-label small">Hours Worked</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.5" min="0" max="24" 
                                                           class="form-control hours-input" 
                                                           name="items[{{ $index }}][hours_worked]" 
                                                           value="{{ old("items.{$index}.hours_worked", 8) }}"
                                                           {{ old("items.{$index}.worked") ? '' : 'disabled' }}>
                                                    <span class="input-group-text">hrs</span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small">Amount</label>
                                                <div class="amount-display-mobile">£0.00</div>
                                                <input type="hidden" name="items[{{ $index }}][amount]" class="amount-input" value="0">
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <label class="form-label small">Work Description</label>
                                            <textarea class="form-control description-input" 
                                                     name="items[{{ $index }}][description]" 
                                                     rows="2"
                                                     placeholder="Describe the work carried out..."
                                                     {{ old("items.{$index}.worked") ? '' : 'disabled' }}>{{ old("items.{$index}.description") }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Mobile Notes Section -->
                    <div class="mobile-form-section mb-4">
                        <h6 class="mobile-section-title">
                            <i class="bi bi-4-circle me-2"></i>Notes (Optional)
                        </h6>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                     id="notes" name="notes" rows="3"
                                     placeholder="Add any additional notes or comments about this invoice...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Mobile Financial Summary -->
                    <div class="mobile-form-section mb-4">
                        <h6 class="mobile-section-title">
                            <i class="bi bi-5-circle me-2"></i>Financial Summary
                        </h6>
                        
                        <div class="financial-summary-mobile">
                            <!-- Day Rate & Hours -->
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <div class="summary-item">
                                        <label class="form-label small">Day Rate</label>
                                        <div class="summary-value">
                                            £<span id="day_rate_display">{{ number_format($employee->day_rate ?? 0, 2) }}</span>
                                            <input type="hidden" id="day_rate" value="{{ $employee->day_rate ?? 0 }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="summary-item">
                                        <label class="form-label small">Total Hours</label>
                                        <div class="summary-value" id="total_hours_display">0.0</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Gross Amount -->
                            <div class="mb-3">
                                <div class="summary-item-large">
                                    <label class="form-label">Gross Amount</label>
                                    <div class="summary-value-large">
                                        £<span id="gross_amount_display">0.00</span>
                                        <input type="hidden" name="gross_amount" id="gross_amount">
                                    </div>
                                </div>
                            </div>

                            <!-- CIS Section -->
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="cis_applicable" 
                                           name="cis_applicable" {{ ($employee->cis_applicable ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="cis_applicable">
                                        CIS Applicable
                                    </label>
                                </div>
                            </div>

                            <div id="cis_section" style="display: none;">
                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small">CIS Rate</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control" id="cis_rate" 
                                                   name="cis_rate" value="{{ $employee->cis_rate ?? 20 }}">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <div class="col-6" id="cis_deduction_section">
                                        <label class="form-label small">CIS Deduction</label>
                                        <div class="summary-value text-warning">
                                            -£<span id="cis_deduction_display">0.00</span>
                                            <input type="hidden" name="cis_deduction" id="cis_deduction">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Net Amount -->
                            <div class="net-amount-section">
                                <div class="summary-item-large border-top pt-3">
                                    <label class="form-label fw-bold">Net Amount</label>
                                    <div class="summary-value-large text-success fw-bold">
                                        £<span id="net_amount_display">0.00</span>
                                        <input type="hidden" name="net_amount" id="net_amount">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Form Actions -->
                    <div class="mobile-form-actions">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-2"></i>Create Invoice
                            </button>
                            <a href="{{ route('operative-dashboard') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        </div>
    </div>
</div>

@push('styles')
<style>
/* Mobile Form Styles */
.mobile-form {
    padding: 1rem;
}

.mobile-form-section {
    background: #ffffff;
    border-radius: 12px;
    padding: 1.25rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.mobile-section-title {
    color: #374151;
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #f3f4f6;
}

/* Day Cards */
.day-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
    transition: all 0.2s ease;
}

.day-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.day-card-header {
    background: #f8fafc;
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.day-card-body {
    padding: 1rem;
    background: #ffffff;
}

.form-check-input:checked {
    background-color: #10b981;
    border-color: #10b981;
}

.worked-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
}

.amount-display-mobile {
    font-size: 1.125rem;
    font-weight: 600;
    color: #059669;
    padding: 0.5rem 0;
}

/* Financial Summary Mobile */
.financial-summary-mobile {
    background: #f8fafc;
    border-radius: 10px;
    padding: 1.25rem;
    border: 1px solid #e5e7eb;
}

.summary-item {
    text-align: center;
    padding: 0.75rem;
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.summary-item-large {
    text-align: center;
    padding: 1rem;
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.summary-value {
    font-size: 1.25rem;
    font-weight: 600;
    color: #374151;
    margin-top: 0.25rem;
}

.summary-value-large {
    font-size: 1.5rem;
    font-weight: 700;
    color: #374151;
    margin-top: 0.25rem;
}

/* Mobile Form Actions */
.mobile-form-actions {
    position: sticky;
    bottom: 0;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    padding: 1rem;
    margin: -1rem;
    margin-top: 2rem;
    border-top: 1px solid #e5e7eb;
    border-radius: 0 0 12px 12px;
}

/* Touch-friendly form elements */
@media (max-width: 768px) {
    .form-control,
    .form-select {
        padding: 0.75rem 1rem;
        font-size: 1rem;
        border-radius: 8px;
        border: 2px solid #e5e7eb;
        transition: all 0.2s ease;
    }
    
    .form-control:focus,
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .btn {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .btn-lg {
        padding: 1rem 2rem;
        font-size: 1.125rem;
    }
    
    .form-check-input {
        width: 1.5rem;
        height: 1.5rem;
    }
    
    .form-switch .form-check-input {
        width: 2.5rem;
        height: 1.25rem;
    }
    
    /* Hide desktop form on mobile */
    .card.d-none.d-md-block {
        display: none !important;
    }
}

/* Desktop-only styles */
@media (min-width: 769px) {
    .mobile-form {
        display: none !important;
    }
}

/* Loading states */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Form validation */
.is-invalid {
    border-color: #dc3545 !important;
}

.invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Smooth animations */
.day-card-body {
    transition: all 0.3s ease;
}

/* Success states */
.text-success {
    color: #059669 !important;
}

.text-warning {
    color: #d97706 !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const managerSelect = document.getElementById('manager_id');
    const siteSelect = document.getElementById('site_id');
    const projectSelect = document.getElementById('project_id');
    
    // Desktop selectors
    const managerSelectDesktop = document.getElementById('manager_id_desktop');
    const siteSelectDesktop = document.getElementById('site_id_desktop');
    const projectSelectDesktop = document.getElementById('project_id_desktop');
    const weekStartInput = document.getElementById('week_period_start');
    const weekEndInput = document.getElementById('week_period_end');
    const dayWorkedCheckboxes = document.querySelectorAll('.day-worked');
    const hoursInputs = document.querySelectorAll('.hours-input');
    const descriptionInputs = document.querySelectorAll('.description-input');
    const cisApplicableCheckbox = document.getElementById('cis_applicable');
    const cisSection = document.getElementById('cis_section');
    const cisDeductionSection = document.getElementById('cis_deduction_section');
    
    const dayRate = parseFloat(document.getElementById('day_rate').value) || 0;
    if (document.getElementById('day_rate_display')) {
        document.getElementById('day_rate_display').textContent = dayRate.toFixed(2);
    }
    
    // Also populate desktop day rate display
    const dayRateDesktop = parseFloat(document.getElementById('day_rate_desktop').value) || 0;
    if (document.getElementById('day_rate_display_desktop')) {
        document.getElementById('day_rate_display_desktop').value = dayRateDesktop.toFixed(2);
    }

    // Helper function to load sites for a manager
    function loadSitesForManager(managerId, targetSiteSelect, targetProjectSelect) {
        if (managerId) {
            console.log('Manager selected:', managerId);
            fetch(`{{ route('ajax.sites-for-manager') }}?manager_id=${managerId}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Sites received:', data);
                    targetSiteSelect.innerHTML = '<option value="">Select site...</option>';
                    data.forEach(site => {
                        targetSiteSelect.innerHTML += `<option value="${site.id}">${site.name}</option>`;
                    });
                    targetSiteSelect.disabled = false;
                    // Reset project selection when sites change
                    targetProjectSelect.innerHTML = '<option value="">Select project...</option>';
                    targetProjectSelect.disabled = true;
                })
                .catch(error => {
                    console.error('Error fetching sites:', error);
                    targetSiteSelect.innerHTML = '<option value="">Error loading sites</option>';
                });
        } else {
            targetSiteSelect.innerHTML = '<option value="">Select site...</option>';
            targetSiteSelect.disabled = true;
            targetProjectSelect.innerHTML = '<option value="">Select project...</option>';
            targetProjectSelect.disabled = true;
        }
    }

    // Manager selection change - mobile
    if (managerSelect) {
        managerSelect.addEventListener('change', function() {
            loadSitesForManager(this.value, siteSelect, projectSelect);
        });
    }

    // Manager selection change - desktop
    if (managerSelectDesktop) {
        managerSelectDesktop.addEventListener('change', function() {
            loadSitesForManager(this.value, siteSelectDesktop, projectSelectDesktop);
        });
    }

    // Helper function to load projects for a site
    function loadProjectsForSite(siteId, targetProjectSelect) {
        if (siteId) {
            console.log('Site selected:', siteId);
            fetch(`{{ route('ajax.projects-for-site') }}?site_id=${siteId}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Projects received:', data);
                    targetProjectSelect.innerHTML = '<option value="">Select project...</option>';
                    data.forEach(project => {
                        targetProjectSelect.innerHTML += `<option value="${project.id}">${project.name}</option>`;
                    });
                    targetProjectSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error fetching projects:', error);
                    targetProjectSelect.innerHTML = '<option value="">Error loading projects</option>';
                });
        } else {
            targetProjectSelect.innerHTML = '<option value="">Select project...</option>';
            targetProjectSelect.disabled = true;
        }
    }

    // Site selection change - mobile
    if (siteSelect) {
        siteSelect.addEventListener('change', function() {
            loadProjectsForSite(this.value, projectSelect);
        });
    }

    // Site selection change - desktop
    if (siteSelectDesktop) {
        siteSelectDesktop.addEventListener('change', function() {
            loadProjectsForSite(this.value, projectSelectDesktop);
        });
    }

    // Week period calculation
    weekStartInput.addEventListener('change', function() {
        if (this.value) {
            const startDate = new Date(this.value);
            const endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + 6);
            weekEndInput.value = endDate.toISOString().split('T')[0];
            updateTimesheetDates();
        }
    });

    // Day worked checkbox changes (Mobile)
    dayWorkedCheckboxes.forEach((checkbox, index) => {
        checkbox.addEventListener('change', function() {
            const hoursInput = hoursInputs[index];
            const descriptionInput = descriptionInputs[index];
            const dayCardBody = this.closest('.day-card').querySelector('.day-card-body');
            const workedLabel = this.closest('.form-check').querySelector('.worked-label');
            
            if (this.checked) {
                hoursInput.disabled = false;
                descriptionInput.disabled = false;
                if (dayCardBody) {
                    dayCardBody.style.display = 'block';
                }
                if (workedLabel) {
                    workedLabel.textContent = 'Worked';
                }
            } else {
                hoursInput.disabled = true;
                hoursInput.value = 8;
                descriptionInput.disabled = true;
                descriptionInput.value = '';
                if (dayCardBody) {
                    dayCardBody.style.display = 'none';
                }
                if (workedLabel) {
                    workedLabel.textContent = 'Off';
                }
            }
            calculateAmounts();
        });
    });

    // Desktop day worked checkbox changes
    const dayWorkedCheckboxesDesktop = document.querySelectorAll('.day-worked-desktop');
    const hoursInputsDesktop = document.querySelectorAll('.hours-input-desktop');
    const descriptionInputsDesktop = document.querySelectorAll('.description-input-desktop');
    
    dayWorkedCheckboxesDesktop.forEach((checkbox, index) => {
        checkbox.addEventListener('change', function() {
            const hoursInput = hoursInputsDesktop[index];
            const descriptionInput = descriptionInputsDesktop[index];
            
            if (this.checked) {
                hoursInput.disabled = false;
                descriptionInput.disabled = false;
            } else {
                hoursInput.disabled = true;
                hoursInput.value = 8;
                descriptionInput.disabled = true;
                descriptionInput.value = '';
            }
            calculateAmountsDesktop();
        });
    });

    // Hours input changes
    hoursInputs.forEach(input => {
        input.addEventListener('input', calculateAmounts);
    });

    // CIS applicable checkbox
    cisApplicableCheckbox.addEventListener('change', function() {
        if (this.checked) {
            cisSection.style.display = 'block';
            cisDeductionSection.style.display = 'block';
        } else {
            cisSection.style.display = 'none';
            cisDeductionSection.style.display = 'none';
        }
        calculateAmounts();
    });

    // CIS rate change
    document.getElementById('cis_rate').addEventListener('input', calculateAmounts);

    // Initial CIS display
    if (cisApplicableCheckbox.checked) {
        cisSection.style.display = 'block';
        cisDeductionSection.style.display = 'block';
    }

    function updateTimesheetDates() {
        const startDate = new Date(weekStartInput.value);
        const rows = document.querySelectorAll('#timesheet-body tr');
        
        rows.forEach((row, index) => {
            const currentDate = new Date(startDate);
            currentDate.setDate(startDate.getDate() + index);
            const dateCell = row.cells[1];
            dateCell.textContent = currentDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        });
    }

    function calculateAmounts() {
        let totalHours = 0;
        let grossAmount = 0;

        // Calculate individual day amounts and totals (Mobile)
        dayWorkedCheckboxes.forEach((checkbox, index) => {
            const hoursInput = hoursInputs[index];
            const amountDisplay = document.querySelectorAll('.amount-display-mobile')[index];
            const amountInput = document.querySelectorAll('.amount-input')[index];
            
            if (checkbox.checked) {
                const hours = parseFloat(hoursInput.value) || 0;
                const amount = hours * (dayRate / 8); // Assuming 8-hour day rate
                
                totalHours += hours;
                grossAmount += amount;
                
                if (amountDisplay) {
                    amountDisplay.textContent = '£' + amount.toFixed(2);
                }
                if (amountInput) {
                    amountInput.value = amount.toFixed(2);
                }
            } else {
                if (amountDisplay) {
                    amountDisplay.textContent = '£0.00';
                }
                if (amountInput) {
                    amountInput.value = '0';
                }
            }
        });

        // Update totals
        const totalHoursDisplay = document.getElementById('total_hours_display');
        const grossAmountDisplay = document.getElementById('gross_amount_display');
        const grossAmountInput = document.getElementById('gross_amount');
        
        if (totalHoursDisplay) {
            totalHoursDisplay.textContent = totalHours.toFixed(1);
        }
        if (grossAmountDisplay) {
            grossAmountDisplay.textContent = grossAmount.toFixed(2);
        }
        if (grossAmountInput) {
            grossAmountInput.value = grossAmount.toFixed(2);
        }

        // Calculate CIS deduction
        let cisDeduction = 0;
        if (cisApplicableCheckbox && cisApplicableCheckbox.checked) {
            const cisRateInput = document.getElementById('cis_rate');
            const cisRate = parseFloat(cisRateInput ? cisRateInput.value : 0) || 0;
            cisDeduction = grossAmount * (cisRate / 100);
        }

        const cisDeductionDisplay = document.getElementById('cis_deduction_display');
        const cisDeductionInput = document.getElementById('cis_deduction');
        
        if (cisDeductionDisplay) {
            cisDeductionDisplay.textContent = cisDeduction.toFixed(2);
        }
        if (cisDeductionInput) {
            cisDeductionInput.value = cisDeduction.toFixed(2);
        }

        // Calculate net amount
        const netAmount = grossAmount - cisDeduction;
        const netAmountDisplay = document.getElementById('net_amount_display');
        const netAmountInput = document.getElementById('net_amount');
        
        if (netAmountDisplay) {
            netAmountDisplay.textContent = netAmount.toFixed(2);
        }
        if (netAmountInput) {
            netAmountInput.value = netAmount.toFixed(2);
        }
    }

    function calculateAmountsDesktop() {
        let totalHours = 0;
        let grossAmount = 0;
        const dayRateDesktop = parseFloat(document.getElementById('day_rate_desktop').value) || 0;

        // Calculate individual day amounts and totals (Desktop)
        dayWorkedCheckboxesDesktop.forEach((checkbox, index) => {
            const hoursInput = hoursInputsDesktop[index];
            const amountDisplay = document.querySelectorAll('.amount-display-desktop')[index];
            const amountInput = document.querySelectorAll('.amount-input-desktop')[index];
            
            if (checkbox.checked) {
                const hours = parseFloat(hoursInput.value) || 0;
                const amount = hours * (dayRateDesktop / 8); // Assuming 8-hour day rate
                
                totalHours += hours;
                grossAmount += amount;
                
                amountDisplay.textContent = '£' + amount.toFixed(2);
                amountInput.value = amount.toFixed(2);
            } else {
                amountDisplay.textContent = '£0.00';
                amountInput.value = '0';
            }
        });

        // Update totals
        document.getElementById('total_hours_display_desktop').value = totalHours.toFixed(1);
        document.getElementById('gross_amount_display_desktop').value = grossAmount.toFixed(2);
        document.getElementById('gross_amount_desktop').value = grossAmount.toFixed(2);

        // Calculate CIS deduction
        let cisDeduction = 0;
        const cisApplicableDesktop = document.getElementById('cis_applicable_desktop');
        if (cisApplicableDesktop && cisApplicableDesktop.checked) {
            const cisRate = parseFloat(document.getElementById('cis_rate_desktop').value) || 0;
            cisDeduction = grossAmount * (cisRate / 100);
        }

        document.getElementById('cis_deduction_display_desktop').value = cisDeduction.toFixed(2);
        document.getElementById('cis_deduction_desktop').value = cisDeduction.toFixed(2);

        // Calculate net amount
        const netAmount = grossAmount - cisDeduction;
        document.getElementById('net_amount_display_desktop').value = netAmount.toFixed(2);
        document.getElementById('net_amount_desktop').value = netAmount.toFixed(2);
    }

    // Initial calculation
    calculateAmounts();
});
</script>
@endpush

@endsection
