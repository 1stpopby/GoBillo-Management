@extends('layouts.app')

@section('title', 'Create Invoice')

@section('content')
<div class="invoice-create-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="page-title">Create New Invoice</h1>
                <p class="page-subtitle">Generate a professional invoice for your client</p>
            </div>
            <div class="col-lg-4 text-end">
                <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Invoices
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('invoices.store') }}" method="POST">
        @csrf
        
        <div class="row g-4">
            <!-- Left Column - Invoice Details -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Invoice Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="client_id" class="form-label">Client *</label>
                                <select class="form-select @error('client_id') is-invalid @enderror" id="client_id" name="client_id" required>
                                    <option value="">Select Client</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->display_name ?? $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="project_id" class="form-label">Project (Optional)</label>
                                <select class="form-select @error('project_id') is-invalid @enderror" id="project_id" name="project_id">
                                    <option value="">No Project</option>
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

                            <div class="col-md-6">
                                <label for="issue_date" class="form-label">Issue Date *</label>
                                <input type="date" class="form-control @error('issue_date') is-invalid @enderror" 
                                       id="issue_date" name="issue_date" value="{{ old('issue_date', now()->toDateString()) }}" required>
                                @error('issue_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="due_date" class="form-label">Due Date *</label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                       id="due_date" name="due_date" value="{{ old('due_date', now()->addDays(30)->toDateString()) }}" required>
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                                <input type="number" class="form-control @error('tax_rate') is-invalid @enderror" 
                                       id="tax_rate" name="tax_rate" value="{{ old('tax_rate', '0.00') }}" min="0" max="100" step="0.01">
                                @error('tax_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="discount_amount" class="form-label">Discount Amount</label>
                                <input type="number" class="form-control @error('discount_amount') is-invalid @enderror" 
                                       id="discount_amount" name="discount_amount" value="{{ old('discount_amount', '0.00') }}" min="0" step="0.01">
                                @error('discount_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="currency" class="form-label">Currency *</label>
                                <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                                    @php($curr = old('currency', 'GBP'))
                                    <option value="GBP" {{ $curr === 'GBP' ? 'selected' : '' }}>GBP</option>
                                    <option value="USD" {{ $curr === 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="EUR" {{ $curr === 'EUR' ? 'selected' : '' }}>EUR</option>
                                </select>
                                @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3" placeholder="Additional notes for the client...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="terms" class="form-label">Terms & Conditions</label>
                                <textarea class="form-control @error('terms') is-invalid @enderror" 
                                          id="terms" name="terms" rows="3" placeholder="Payment terms and conditions...">{{ old('terms', 'Payment is due within 30 days of invoice date. Late payments may incur additional charges.') }}</textarea>
                                @error('terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Items -->
                <div class="card mt-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Invoice Items</h5>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addItemBtn">
                                <i class="bi bi-plus"></i> Add Item
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="invoiceItems">
                            <!-- Invoice items will be added here dynamically -->
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6 offset-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Subtotal:</strong></td>
                                        <td class="text-end"><span id="subtotal">$0.00</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tax:</strong></td>
                                        <td class="text-end"><span id="taxAmount">$0.00</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Discount:</strong></td>
                                        <td class="text-end"><span id="discountDisplay">-$0.00</span></td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td><strong>Total:</strong></td>
                                        <td class="text-end"><strong><span id="totalAmount">$0.00</span></strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Actions -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-3">
                            <button type="submit" name="action" value="draft" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-file-earmark me-2"></i>Save as Draft
                            </button>
                            
                            <button type="submit" name="action" value="send" class="btn btn-primary btn-lg">
                                <i class="bi bi-send me-2"></i>Create & Send
                            </button>
                            
                            <hr>
                            
                            <div class="text-center">
                                <small class="text-muted">Invoice will be automatically numbered</small>
                            </div>
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
                                Use clear, detailed descriptions for each item
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-lightbulb text-warning me-2"></i>
                                Set appropriate due dates (typically 30 days)
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-lightbulb text-warning me-2"></i>
                                Include your payment terms and conditions
                            </li>
                            <li>
                                <i class="bi bi-lightbulb text-warning me-2"></i>
                                Double-check all amounts before sending
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Item Template (hidden) -->
<template id="itemTemplate">
    <div class="invoice-item border rounded p-3 mb-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Description *</label>
                <input type="text" class="form-control item-description" name="items[INDEX][description]" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Quantity *</label>
                <input type="number" class="form-control item-quantity" name="items[INDEX][quantity]" 
                       value="1" min="0.01" step="0.01" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Unit *</label>
                <select class="form-select item-unit" name="items[INDEX][unit]" required>
                    <option value="unit">Unit</option>
                    <option value="hour">Hour</option>
                    <option value="day">Day</option>
                    <option value="sqm">Sq m</option>
                    <option value="item">Item</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Unit Price *</label>
                <input type="number" class="form-control item-price" name="items[INDEX][unit_price]" 
                       value="0.00" min="0" step="0.01" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Total</label>
                <input type="text" class="form-control item-total" readonly>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger btn-sm remove-item">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<style>
.invoice-create-container {
    max-width: 100%;
}

.invoice-item {
    background: #f8f9fa;
    border: 1px solid #dee2e6 !important;
}

.invoice-item:hover {
    background: #e9ecef;
}

.card-title {
    margin-bottom: 0;
}

.table-sm td {
    padding: 0.5rem 0.75rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = 0;
    const itemsContainer = document.getElementById('invoiceItems');
    const addItemBtn = document.getElementById('addItemBtn');
    const template = document.getElementById('itemTemplate');

    // Add initial item
    addItem();

    // Add item button click
    addItemBtn.addEventListener('click', addItem);

    function addItem() {
        const clone = template.content.cloneNode(true);
        const itemDiv = clone.querySelector('.invoice-item');
        
        // Replace INDEX placeholders
        itemDiv.innerHTML = itemDiv.innerHTML.replaceAll('INDEX', itemIndex);
        
        // Add event listeners
        const removeBtn = itemDiv.querySelector('.remove-item');
        const quantityInput = itemDiv.querySelector('.item-quantity');
        const priceInput = itemDiv.querySelector('.item-price');
        
        removeBtn.addEventListener('click', function() {
            if (itemsContainer.children.length > 1) {
                itemDiv.remove();
                calculateTotals();
            }
        });
        
        quantityInput.addEventListener('input', calculateTotals);
        priceInput.addEventListener('input', calculateTotals);
        
        itemsContainer.appendChild(itemDiv);
        itemIndex++;
        
        calculateTotals();
    }

    // Calculate totals
    function calculateTotals() {
        let subtotal = 0;
        
        document.querySelectorAll('.invoice-item').forEach(function(item) {
            const quantity = parseFloat(item.querySelector('.item-quantity').value) || 0;
            const price = parseFloat(item.querySelector('.item-price').value) || 0;
            const total = quantity * price;
            
            item.querySelector('.item-total').value = '$' + total.toFixed(2);
            subtotal += total;
        });
        
        const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
        const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
        
        const taxAmount = subtotal * (taxRate / 100);
        const totalAmount = subtotal + taxAmount - discountAmount;
        
        document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('taxAmount').textContent = '$' + taxAmount.toFixed(2);
        document.getElementById('discountDisplay').textContent = '-$' + discountAmount.toFixed(2);
        document.getElementById('totalAmount').textContent = '$' + totalAmount.toFixed(2);
    }

    // Recalculate when tax rate or discount changes
    document.getElementById('tax_rate').addEventListener('input', calculateTotals);
    document.getElementById('discount_amount').addEventListener('input', calculateTotals);
});
</script>
@endsection 