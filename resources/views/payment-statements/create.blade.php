@extends('layouts.app')

@section('title', 'Generate Payment Statement')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Generate Payment Statement</h1>
        <p class="text-muted mb-0">Create a new payment statement for a client</p>
    </div>
    <a href="{{ route('payment-statements.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Statements
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Statement Details</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('payment-statements.generate') }}" method="POST" id="statement-form">
                    @csrf
                    
                    <!-- Client Selection -->
                    <div class="mb-4">
                        <label for="client_id" class="form-label">Select Client <span class="text-danger">*</span></label>
                        <select class="form-select @error('client_id') is-invalid @enderror" 
                                id="client_id" name="client_id" required>
                            <option value="">Choose a client...</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" 
                                        data-projects="{{ $client->projects->count() }}"
                                        data-invoices="{{ $client->invoices->count() }}"
                                        {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->company_name }}
                                    @if($client->contact_name)
                                        ({{ $client->contact_name }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Select the client to generate the statement for</div>
                    </div>

                    <!-- Client Info Display -->
                    <div id="client-info" class="alert alert-info mb-4" style="display: none;">
                        <h6 class="alert-heading">Client Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Projects:</strong> <span id="client-projects">0</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Invoices:</strong> <span id="client-invoices">0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control @error('date_from') is-invalid @enderror" 
                                   id="date_from" name="date_from" value="{{ old('date_from') }}">
                            @error('date_from')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Leave empty to include all historical data</div>
                        </div>
                        <div class="col-md-6">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control @error('date_to') is-invalid @enderror" 
                                   id="date_to" name="date_to" 
                                   value="{{ old('date_to', date('Y-m-d')) }}">
                            @error('date_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Defaults to today's date</div>
                        </div>
                    </div>

                    <!-- Include Options -->
                    <div class="mb-4">
                        <label class="form-label">Include in Statement</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="include_projects" 
                                   name="include_projects" value="1" checked>
                            <label class="form-check-label" for="include_projects">
                                <i class="bi bi-kanban text-primary me-1"></i>
                                Project Details & Budgets
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="include_invoices" 
                                   name="include_invoices" value="1" checked>
                            <label class="form-check-label" for="include_invoices">
                                <i class="bi bi-receipt text-success me-1"></i>
                                Invoice History
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="include_payments" 
                                   name="include_payments" value="1" checked>
                            <label class="form-check-label" for="include_payments">
                                <i class="bi bi-credit-card text-info me-1"></i>
                                Payment Transactions
                            </label>
                        </div>
                    </div>

                    <!-- Preview Options -->
                    <div class="alert alert-light border">
                        <h6 class="alert-heading">
                            <i class="bi bi-info-circle me-1"></i>Statement Preview
                        </h6>
                        <p class="mb-2">The generated statement will include:</p>
                        <ul class="mb-0">
                            <li>Total project budget vs amount invoiced</li>
                            <li>List of all invoices with their status</li>
                            <li>Complete payment history with dates and amounts</li>
                            <li>Outstanding balance and remaining budget</li>
                            <li>Summary of financial position</li>
                        </ul>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" name="action" value="generate" class="btn btn-primary">
                            <i class="bi bi-file-text me-2"></i>Generate Statement
                        </button>
                        <button type="submit" name="action" value="generate_pdf" class="btn btn-success">
                            <i class="bi bi-file-pdf me-2"></i>Generate & Download PDF
                        </button>
                        <a href="{{ route('payment-statements.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const clientSelect = document.getElementById('client_id');
    const clientInfo = document.getElementById('client-info');
    const clientProjects = document.getElementById('client-projects');
    const clientInvoices = document.getElementById('client-invoices');
    
    clientSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            clientInfo.style.display = 'block';
            clientProjects.textContent = selectedOption.getAttribute('data-projects') || '0';
            clientInvoices.textContent = selectedOption.getAttribute('data-invoices') || '0';
        } else {
            clientInfo.style.display = 'none';
        }
    });
    
    // Set max date to today
    document.getElementById('date_to').max = new Date().toISOString().split('T')[0];
});
</script>
@endpush