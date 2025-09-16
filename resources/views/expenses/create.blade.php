@extends('layouts.app')

@section('title', 'Create Expense')

@section('content')
<div class="expense-create-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="page-title">Create New Expense</h1>
                <p class="page-subtitle">Record a business expense for tracking and reimbursement</p>
            </div>
            <div class="col-lg-4 text-end">
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Expenses
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row g-4">
            <!-- Left Column - Expense Details -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Expense Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="category" class="form-label">Category *</label>
                                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="office_supplies" {{ old('category') == 'office_supplies' ? 'selected' : '' }}>Office Supplies</option>
                                    <option value="travel" {{ old('category') == 'travel' ? 'selected' : '' }}>Travel</option>
                                    <option value="meals" {{ old('category') == 'meals' ? 'selected' : '' }}>Meals & Entertainment</option>
                                    <option value="equipment" {{ old('category') == 'equipment' ? 'selected' : '' }}>Equipment</option>
                                    <option value="materials" {{ old('category') == 'materials' ? 'selected' : '' }}>Materials</option>
                                    <option value="fuel" {{ old('category') == 'fuel' ? 'selected' : '' }}>Fuel</option>
                                    <option value="maintenance" {{ old('category') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    <option value="professional_services" {{ old('category') == 'professional_services' ? 'selected' : '' }}>Professional Services</option>
                                    <option value="utilities" {{ old('category') == 'utilities' ? 'selected' : '' }}>Utilities</option>
                                    <option value="insurance" {{ old('category') == 'insurance' ? 'selected' : '' }}>Insurance</option>
                                    <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="site_id" class="form-label">Site</label>
                                <select class="form-select" id="site_id" name="site_id">
                                    <option value="">No Site</option>
                                    @foreach(\App\Models\Site::forCompany()->orderBy('name')->get() as $site)
                                        <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="project_id" class="form-label">Project (Optional)</label>
                                <select class="form-select @error('project_id') is-invalid @enderror" id="project_id" name="project_id">
                                    <option value="">No Project</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" data-site="{{ $project->site_id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="amount" class="form-label">Amount *</label>
                                <div class="input-group">
                                    <span class="input-group-text">£</span>
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                           id="amount" name="amount" value="{{ old('amount') }}" min="0.01" step="0.01" required>
                                </div>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="expense_date" class="form-label">Expense Date *</label>
                                <input type="date" class="form-control @error('expense_date') is-invalid @enderror" 
                                       id="expense_date" name="expense_date" value="{{ old('expense_date', now()->toDateString()) }}" required>
                                @error('expense_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="vendor" class="form-label">Vendor/Merchant</label>
                                <input type="text" class="form-control @error('vendor') is-invalid @enderror" 
                                       id="vendor" name="vendor" value="{{ old('vendor') }}" placeholder="e.g., Home Depot, Shell Gas Station">
                                @error('vendor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method">
                                    <option value="">Select Method</option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                    <option value="debit_card" {{ old('payment_method') == 'debit_card' ? 'selected' : '' }}>Debit Card</option>
                                    <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="petty_cash" {{ old('payment_method') == 'petty_cash' ? 'selected' : '' }}>Petty Cash</option>
                                </select>
                                @error('payment_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Description *</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" required 
                                          placeholder="Describe what this expense was for...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="2" 
                                          placeholder="Any additional information...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mileage Section (if applicable) -->
                <div class="card mt-4" id="mileageCard" style="display: none;">
                    <div class="card-header">
                        <h5 class="card-title">Mileage Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="mileage" class="form-label">Miles Driven</label>
                                <input type="number" class="form-control @error('mileage') is-invalid @enderror" 
                                       id="mileage" name="mileage" value="{{ old('mileage') }}" min="0" step="0.1">
                                @error('mileage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="mileage_rate" class="form-label">Rate per Mile</label>
                                <div class="input-group">
                                    <span class="input-group-text">£</span>
                                    <input type="number" class="form-control @error('mileage_rate') is-invalid @enderror" 
                                           id="mileage_rate" name="mileage_rate" value="{{ old('mileage_rate', '0.56') }}" min="0" step="0.01">
                                </div>
                                @error('mileage_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Receipt Upload -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title">Receipt</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="receipt" class="form-label">Upload Receipt</label>
                            <input type="file" class="form-control @error('receipt') is-invalid @enderror" 
                                   id="receipt" name="receipt" accept="image/*,.pdf">
                            <div class="form-text">Accepted formats: JPG, PNG, PDF (Max 5MB)</div>
                            @error('receipt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Options & Actions -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Options</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_billable" name="is_billable" value="1" {{ old('is_billable') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_billable">
                                <strong>Billable to Client</strong>
                                <small class="d-block text-muted">This expense can be billed to a client</small>
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="is_reimbursable" name="is_reimbursable" value="1" {{ old('is_reimbursable') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_reimbursable">
                                <strong>Reimbursable</strong>
                                <small class="d-block text-muted">Request reimbursement for this expense</small>
                            </label>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="is_mileage" name="is_mileage" value="1" {{ old('is_mileage') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_mileage">
                                <strong>Mileage Expense</strong>
                                <small class="d-block text-muted">This is a mileage-based expense</small>
                            </label>
                        </div>

                        <hr>

                        <div class="d-grid gap-3">
                            <button type="submit" name="action" value="draft" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-file-earmark me-2"></i>Save as Draft
                            </button>
                            
                            <button type="submit" name="action" value="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-2"></i>Submit for Approval
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="card-title">Quick Tips</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled small">
                            <li class="mb-2">
                                <i class="bi bi-lightbulb text-warning me-2"></i>
                                Always upload receipts when possible
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-lightbulb text-warning me-2"></i>
                                Be specific in your descriptions
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-lightbulb text-warning me-2"></i>
                                Mark billable expenses for client invoicing
                            </li>
                            <li>
                                <i class="bi bi-lightbulb text-warning me-2"></i>
                                Submit promptly for faster reimbursement
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.expense-create-container {
    max-width: 100%;
}

.card-title {
    margin-bottom: 0;
}

.form-check-label strong {
    color: #374151;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const siteSelect = document.getElementById('site_id');
    const projectSelect = document.getElementById('project_id');
    const categorySelect = document.getElementById('category');
    const mileageCheckbox = document.getElementById('is_mileage');
    const mileageCard = document.getElementById('mileageCard');
    const mileageInput = document.getElementById('mileage');
    const mileageRateInput = document.getElementById('mileage_rate');
    const amountInput = document.getElementById('amount');

    // Show/hide mileage section
    function toggleMileageSection() {
        if (mileageCheckbox.checked || categorySelect.value === 'travel') {
            mileageCard.style.display = 'block';
        } else {
            mileageCard.style.display = 'none';
            mileageInput.value = '';
            mileageRateInput.value = '0.56';
        }
    }

    // Auto-calculate mileage amount
    function calculateMileageAmount() {
        if (mileageCheckbox.checked) {
            const miles = parseFloat(mileageInput.value) || 0;
            const rate = parseFloat(mileageRateInput.value) || 0;
            const total = miles * rate;
            if (total > 0) {
                amountInput.value = total.toFixed(2);
            }
        }
    }

    // Event listeners
    categorySelect.addEventListener('change', toggleMileageSection);
    mileageCheckbox.addEventListener('change', toggleMileageSection);
    mileageInput.addEventListener('input', calculateMileageAmount);
    mileageRateInput.addEventListener('input', calculateMileageAmount);

    // Initial setup
    toggleMileageSection();

    // Filter projects by selected site
    function filterProjectsBySite() {
        const siteId = siteSelect.value;
        Array.from(projectSelect.options).forEach(opt => {
            if (!opt.value) return; // skip placeholder
            const match = !siteId || opt.getAttribute('data-site') === siteId;
            opt.hidden = !match;
        });
        // If current selection is hidden, reset to placeholder
        const selected = projectSelect.selectedOptions[0];
        if (selected && selected.hidden) {
            projectSelect.value = '';
        }
    }
    siteSelect?.addEventListener('change', filterProjectsBySite);
    filterProjectsBySite();
});
</script>
@endsection 