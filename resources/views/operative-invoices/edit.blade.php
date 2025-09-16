@extends('layouts.app')

@section('title', 'Edit Invoice #' . $operativeInvoice->invoice_number)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('operative-dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('operative-invoices.index') }}">Invoices</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('operative-invoices.show', $operativeInvoice) }}">#{{ $operativeInvoice->invoice_number }}</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
                <h1 class="page-title">Edit Invoice #{{ $operativeInvoice->invoice_number }}</h1>
                <p class="page-subtitle">Update invoice details and timesheet</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Invoice Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('operative-invoices.update', $operativeInvoice) }}" method="POST" id="invoice-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Manager Selection -->
                            <div class="col-md-4 mb-3">
                                <label for="manager_id" class="form-label">Manager <span class="text-danger">*</span></label>
                                <select class="form-select @error('manager_id') is-invalid @enderror" 
                                        id="manager_id" name="manager_id" required>
                                    <option value="">Select manager...</option>
                                    @foreach($managers as $manager)
                                        <option value="{{ $manager->id }}" 
                                                {{ old('manager_id', $operativeInvoice->manager_id) == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('manager_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Site Selection -->
                            <div class="col-md-4 mb-3">
                                <label for="site_id" class="form-label">Site <span class="text-danger">*</span></label>
                                <select class="form-select @error('site_id') is-invalid @enderror" 
                                        id="site_id" name="site_id" required>
                                    <option value="">Select site...</option>
                                    @foreach($sites as $site)
                                        <option value="{{ $site->id }}" 
                                                {{ old('site_id', $operativeInvoice->site_id) == $site->id ? 'selected' : '' }}>
                                            {{ $site->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('site_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Project Selection -->
                            <div class="col-md-4 mb-3">
                                <label for="project_id" class="form-label">Project <span class="text-danger">*</span></label>
                                <select class="form-select @error('project_id') is-invalid @enderror" 
                                        id="project_id" name="project_id" required>
                                    <option value="">Select project...</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" 
                                                {{ old('project_id', $operativeInvoice->project_id) == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Week Period -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="week_period_start" class="form-label">Week Starting <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('week_period_start') is-invalid @enderror" 
                                       id="week_period_start" name="week_period_start" 
                                       value="{{ old('week_period_start', $operativeInvoice->week_period_start->format('Y-m-d')) }}" required>
                                @error('week_period_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="week_period_end" class="form-label">Week Ending</label>
                                <input type="date" class="form-control" 
                                       id="week_period_end" name="week_period_end" 
                                       value="{{ old('week_period_end', $operativeInvoice->week_period_end->format('Y-m-d')) }}" readonly>
                            </div>
                        </div>

                        <!-- Weekly Timesheet -->
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
                                        <tbody id="timesheet-body">
                                            @foreach($operativeInvoice->items as $index => $item)
                                                <tr>
                                                    <td><strong>{{ $item->day_of_week }}</strong></td>
                                                    <td>{{ $operativeInvoice->week_period_start->addDays($index)->format('M j') }}</td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input day-worked" type="checkbox" 
                                                                   id="worked_{{ $index }}" name="items[{{ $index }}][worked]" value="1"
                                                                   {{ old("items.{$index}.worked", $item->worked) ? 'checked' : '' }}>
                                                            <input type="hidden" name="items[{{ $index }}][day_of_week]" value="{{ $item->day_of_week }}">
                                                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.5" min="0" max="24" 
                                                               class="form-control hours-input" 
                                                               name="items[{{ $index }}][hours_worked]" 
                                                               value="{{ old("items.{$index}.hours_worked", $item->hours_worked) }}"
                                                               {{ $item->worked ? '' : 'disabled' }}>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control description-input" 
                                                               name="items[{{ $index }}][description]" 
                                                               value="{{ old("items.{$index}.description", $item->description) }}"
                                                               placeholder="Work description..."
                                                               {{ $item->worked ? '' : 'disabled' }}>
                                                    </td>
                                                    <td>
                                                        <span class="amount-display">£{{ number_format($item->amount, 2) }}</span>
                                                        <input type="hidden" name="items[{{ $index }}][amount]" class="amount-input" value="{{ $item->amount }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Summary -->
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
                                                <input type="text" class="form-control" id="day_rate_display" readonly>
                                                <input type="hidden" id="day_rate" value="{{ $employee->day_rate ?? 0 }}">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Total Hours</label>
                                            <input type="text" class="form-control" id="total_hours_display" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Gross Amount</label>
                                            <div class="input-group">
                                                <span class="input-group-text">£</span>
                                                <input type="text" class="form-control" id="gross_amount_display" readonly>
                                                <input type="hidden" name="gross_amount" id="gross_amount">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="cis_applicable" 
                                                       name="cis_applicable" {{ old('cis_applicable', $operativeInvoice->cis_applicable) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="cis_applicable">
                                                    <strong>CIS Applicable</strong>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-3" id="cis_section" style="display: none;">
                                            <label class="form-label">CIS Rate (%)</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" id="cis_rate" 
                                                       name="cis_rate" value="{{ old('cis_rate', $operativeInvoice->cis_rate) }}">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                        <div class="mb-3" id="cis_deduction_section" style="display: none;">
                                            <label class="form-label">CIS Deduction</label>
                                            <div class="input-group">
                                                <span class="input-group-text">£</span>
                                                <input type="text" class="form-control" id="cis_deduction_display" readonly>
                                                <input type="hidden" name="cis_deduction" id="cis_deduction">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label"><strong>Net Amount</strong></label>
                                            <div class="input-group">
                                                <span class="input-group-text">£</span>
                                                <input type="text" class="form-control fw-bold" id="net_amount_display" readonly>
                                                <input type="hidden" name="net_amount" id="net_amount">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('operative-invoices.show', $operativeInvoice) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Invoice
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const managerSelect = document.getElementById('manager_id');
    const siteSelect = document.getElementById('site_id');
    const projectSelect = document.getElementById('project_id');
    const weekStartInput = document.getElementById('week_period_start');
    const weekEndInput = document.getElementById('week_period_end');
    const dayWorkedCheckboxes = document.querySelectorAll('.day-worked');
    const hoursInputs = document.querySelectorAll('.hours-input');
    const descriptionInputs = document.querySelectorAll('.description-input');
    const cisApplicableCheckbox = document.getElementById('cis_applicable');
    const cisSection = document.getElementById('cis_section');
    const cisDeductionSection = document.getElementById('cis_deduction_section');
    
    const dayRate = parseFloat(document.getElementById('day_rate').value) || 0;
    document.getElementById('day_rate_display').value = dayRate.toFixed(2);

    // Manager selection change - load sites
    managerSelect.addEventListener('change', function() {
        if (this.value) {
            fetch(`{{ route('ajax.sites-for-manager') }}?manager_id=${this.value}`)
                .then(response => response.json())
                .then(data => {
                    const currentSiteId = siteSelect.value;
                    siteSelect.innerHTML = '<option value="">Select site...</option>';
                    data.forEach(site => {
                        const selected = site.id == currentSiteId ? 'selected' : '';
                        siteSelect.innerHTML += `<option value="${site.id}" ${selected}>${site.name}</option>`;
                    });
                    siteSelect.disabled = false;
                    
                    if (siteSelect.value) {
                        siteSelect.dispatchEvent(new Event('change'));
                    }
                });
        }
    });

    // Site selection change - load projects
    siteSelect.addEventListener('change', function() {
        if (this.value) {
            fetch(`{{ route('ajax.projects-for-site') }}?site_id=${this.value}`)
                .then(response => response.json())
                .then(data => {
                    const currentProjectId = projectSelect.value;
                    projectSelect.innerHTML = '<option value="">Select project...</option>';
                    data.forEach(project => {
                        const selected = project.id == currentProjectId ? 'selected' : '';
                        projectSelect.innerHTML += `<option value="${project.id}" ${selected}>${project.name}</option>`;
                    });
                    projectSelect.disabled = false;
                });
        }
    });

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

    // Day worked checkbox changes
    dayWorkedCheckboxes.forEach((checkbox, index) => {
        checkbox.addEventListener('change', function() {
            const hoursInput = hoursInputs[index];
            const descriptionInput = descriptionInputs[index];
            
            if (this.checked) {
                hoursInput.disabled = false;
                descriptionInput.disabled = false;
            } else {
                hoursInput.disabled = true;
                hoursInput.value = 8;
                descriptionInput.disabled = true;
                descriptionInput.value = '';
            }
            calculateAmounts();
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

    // Initial setup
    if (cisApplicableCheckbox.checked) {
        cisSection.style.display = 'block';
        cisDeductionSection.style.display = 'block';
    }

    // Load initial data
    if (managerSelect.value) {
        managerSelect.dispatchEvent(new Event('change'));
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

        // Calculate individual day amounts and totals
        dayWorkedCheckboxes.forEach((checkbox, index) => {
            const hoursInput = hoursInputs[index];
            const amountDisplay = document.querySelectorAll('.amount-display')[index];
            const amountInput = document.querySelectorAll('.amount-input')[index];
            
            if (checkbox.checked) {
                const hours = parseFloat(hoursInput.value) || 0;
                const amount = hours * (dayRate / 8); // Assuming 8-hour day rate
                
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
        document.getElementById('total_hours_display').value = totalHours.toFixed(1);
        document.getElementById('gross_amount_display').value = grossAmount.toFixed(2);
        document.getElementById('gross_amount').value = grossAmount.toFixed(2);

        // Calculate CIS deduction
        let cisDeduction = 0;
        if (cisApplicableCheckbox.checked) {
            const cisRate = parseFloat(document.getElementById('cis_rate').value) || 0;
            cisDeduction = grossAmount * (cisRate / 100);
        }

        document.getElementById('cis_deduction_display').value = cisDeduction.toFixed(2);
        document.getElementById('cis_deduction').value = cisDeduction.toFixed(2);

        // Calculate net amount
        const netAmount = grossAmount - cisDeduction;
        document.getElementById('net_amount_display').value = netAmount.toFixed(2);
        document.getElementById('net_amount').value = netAmount.toFixed(2);
    }

    // Initial calculation
    calculateAmounts();
});
</script>
@endpush

@endsection

