@extends('layouts.app')

@section('title', 'Record Payment')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Record Payment</h1>
            <p class="text-muted mb-0">Record a client payment for an invoice</p>
        </div>
        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Payments
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">Payment Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('payments.store') }}" method="POST" id="payment-form">
                        @csrf
                        
                        <!-- Invoice Selection -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="invoice_id" class="form-label">Invoice <span class="text-danger">*</span></label>
                                <select class="form-select @error('invoice_id') is-invalid @enderror" 
                                        id="invoice_id" name="invoice_id" required>
                                    <option value="">Select an invoice...</option>
                                    @foreach($unpaidInvoices as $invoice)
                                        @php
                                            $totalPaid = $invoice->payments()->completed()->sum('amount');
                                            $remainingBalance = $invoice->total - $totalPaid;
                                        @endphp
                                        <option value="{{ $invoice->id }}" 
                                                data-total="{{ $invoice->total }}" 
                                                data-paid="{{ $totalPaid }}" 
                                                data-remaining="{{ $remainingBalance }}"
                                                data-client="{{ $invoice->client ? $invoice->client->display_name : 'No Client' }}"
                                                data-project="{{ $invoice->project ? $invoice->project->name : 'No Project' }}"
                                                {{ old('invoice_id', $selectedInvoice?->id) == $invoice->id ? 'selected' : '' }}>
                                            {{ $invoice->invoice_number }} - 
                                            {{ $invoice->client ? $invoice->client->display_name : 'No Client' }}
                                            (£{{ number_format($remainingBalance, 2) }} remaining)
                                        </option>
                                    @endforeach
                                </select>
                                @error('invoice_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Select the invoice this payment is for</div>
                            </div>
                        </div>

                        <!-- Invoice Details Preview -->
                        <div id="invoice-details" class="row mb-4" style="display: none;">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">Invoice Details</h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Client:</strong>
                                            <div id="invoice-client">-</div>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Project:</strong>
                                            <div id="invoice-project">-</div>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Total Amount:</strong>
                                            <div id="invoice-total">£0.00</div>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Remaining Balance:</strong>
                                            <div id="invoice-remaining" class="fw-bold text-primary">£0.00</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Amount -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Payment Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">£</span>
                                    <input type="number" step="0.01" min="0.01" 
                                           class="form-control @error('amount') is-invalid @enderror" 
                                           id="amount" name="amount" value="{{ old('amount') }}" required>
                                </div>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="payment_type" class="form-label">Payment Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('payment_type') is-invalid @enderror" 
                                        id="payment_type" name="payment_type" required>
                                    <option value="">Select payment type...</option>
                                    <option value="full_payment" {{ old('payment_type') == 'full_payment' ? 'selected' : '' }}>Full Payment</option>
                                    <option value="partial_payment" {{ old('payment_type') == 'partial_payment' ? 'selected' : '' }}>Partial Payment</option>
                                    <option value="deposit" {{ old('payment_type') == 'deposit' ? 'selected' : '' }}>Deposit</option>
                                </select>
                                @error('payment_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="payment_method_id" class="form-label">Payment Method</label>
                                <select class="form-select @error('payment_method_id') is-invalid @enderror" 
                                        id="payment_method_id" name="payment_method_id">
                                    <option value="">Select payment method...</option>
                                    @foreach($paymentMethods as $method)
                                        <option value="{{ $method->id }}" 
                                                data-fee-percentage="{{ $method->processing_fee_percentage }}"
                                                data-fee-fixed="{{ $method->processing_fee_fixed }}"
                                                {{ old('payment_method_id') == $method->id ? 'selected' : '' }}>
                                            {{ $method->name }}
                                            @if($method->processing_fee_percentage > 0 || $method->processing_fee_fixed > 0)
                                                ({{ $method->processing_fee_percentage }}% + £{{ number_format($method->processing_fee_fixed, 2) }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('payment_method_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Optional - select if using a configured payment gateway</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="payment_gateway" class="form-label">Payment Gateway <span class="text-danger">*</span></label>
                                <select class="form-select @error('payment_gateway') is-invalid @enderror" 
                                        id="payment_gateway" name="payment_gateway" required>
                                    <option value="">Select gateway...</option>
                                    <option value="manual" {{ old('payment_gateway') == 'manual' ? 'selected' : '' }}>Manual Entry</option>
                                    <option value="bank_transfer" {{ old('payment_gateway') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="cash" {{ old('payment_gateway') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="cheque" {{ old('payment_gateway') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                    <option value="stripe" {{ old('payment_gateway') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                                    <option value="paypal" {{ old('payment_gateway') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                    <option value="square" {{ old('payment_gateway') == 'square' ? 'selected' : '' }}>Square</option>
                                </select>
                                @error('payment_gateway')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Transaction Details -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="provider_transaction_id" class="form-label">Transaction ID</label>
                                <input type="text" class="form-control @error('provider_transaction_id') is-invalid @enderror" 
                                       id="provider_transaction_id" name="provider_transaction_id" 
                                       value="{{ old('provider_transaction_id') }}"
                                       placeholder="External transaction reference (optional)">
                                @error('provider_transaction_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Reference number from payment provider (if applicable)</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="status" class="form-label">Payment Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="">Select status...</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Processing Fee Display -->
                        <div id="processing-fee-display" class="row mb-3" style="display: none;">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <strong>Processing Fee:</strong> £<span id="fee-amount">0.00</span>
                                    <br>
                                    <strong>Net Amount:</strong> £<span id="net-amount">0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3" 
                                          placeholder="Additional notes about this payment (optional)">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Record Payment
                                    </button>
                                    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const invoiceSelect = document.getElementById('invoice_id');
    const amountInput = document.getElementById('amount');
    const paymentMethodSelect = document.getElementById('payment_method_id');
    const invoiceDetails = document.getElementById('invoice-details');
    const processingFeeDisplay = document.getElementById('processing-fee-display');
    
    // Invoice selection handler
    invoiceSelect.addEventListener('change', function() {
        const selectedOption = this.selectedOptions[0];
        
        if (selectedOption && selectedOption.value) {
            // Show invoice details
            document.getElementById('invoice-client').textContent = selectedOption.dataset.client;
            document.getElementById('invoice-project').textContent = selectedOption.dataset.project;
            document.getElementById('invoice-total').textContent = '£' + parseFloat(selectedOption.dataset.total).toFixed(2);
            document.getElementById('invoice-remaining').textContent = '£' + parseFloat(selectedOption.dataset.remaining).toFixed(2);
            
            invoiceDetails.style.display = 'block';
            
            // Set max amount to remaining balance
            amountInput.max = selectedOption.dataset.remaining;
            
            // Suggest full payment amount
            amountInput.value = parseFloat(selectedOption.dataset.remaining).toFixed(2);
            
            // Update payment type
            const remaining = parseFloat(selectedOption.dataset.remaining);
            const total = parseFloat(selectedOption.dataset.total);
            
            if (remaining === total) {
                document.getElementById('payment_type').value = 'full_payment';
            }
            
            calculateProcessingFee();
        } else {
            invoiceDetails.style.display = 'none';
            amountInput.removeAttribute('max');
            amountInput.value = '';
        }
    });
    
    // Processing fee calculation
    function calculateProcessingFee() {
        const amount = parseFloat(amountInput.value) || 0;
        const selectedMethod = paymentMethodSelect.selectedOptions[0];
        
        if (selectedMethod && selectedMethod.value && amount > 0) {
            const feePercentage = parseFloat(selectedMethod.dataset.feePercentage) || 0;
            const feeFixed = parseFloat(selectedMethod.dataset.feeFixed) || 0;
            
            const fee = (amount * feePercentage / 100) + feeFixed;
            const netAmount = amount - fee;
            
            document.getElementById('fee-amount').textContent = fee.toFixed(2);
            document.getElementById('net-amount').textContent = netAmount.toFixed(2);
            
            processingFeeDisplay.style.display = 'block';
        } else {
            processingFeeDisplay.style.display = 'none';
        }
    }
    
    amountInput.addEventListener('input', calculateProcessingFee);
    paymentMethodSelect.addEventListener('change', calculateProcessingFee);
    
    // Trigger initial calculation if invoice is pre-selected
    if (invoiceSelect.value) {
        invoiceSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection