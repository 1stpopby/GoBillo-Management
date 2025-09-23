@extends('layouts.app')

@section('title', 'Edit Invoice')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Edit Invoice {{ $invoice->invoice_number ?? ('#' . $invoice->id) }}</h1>
    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-secondary">Cancel</a>
    
</div>

@php($currency = old('currency', $invoice->currency))

<form action="{{ route('invoices.update', $invoice) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Invoice Information</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Client *</label>
                            <select class="form-select" name="client_id" required>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" @selected(old('client_id', $invoice->client_id)==$client->id)>
                                        {{ $client->display_name ?? $client->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Project (Optional)</label>
                            <select class="form-select" name="project_id">
                                <option value="">No Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" @selected(old('project_id', $invoice->project_id)==$project->id)>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Issue Date *</label>
                            <input type="date" class="form-control" name="issue_date" value="{{ old('issue_date', optional($invoice->issue_date)->toDateString()) }}" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Due Date *</label>
                            <input type="date" class="form-control" name="due_date" value="{{ old('due_date', optional($invoice->due_date)->toDateString()) }}" required />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tax Rate (%)</label>
                            <input type="number" step="0.01" min="0" max="100" class="form-control" name="tax_rate" value="{{ old('tax_rate', $invoice->tax_rate) }}" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Discount Amount</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="discount_amount" value="{{ old('discount_amount', $invoice->discount_amount) }}" />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Currency *</label>
                            <select class="form-select" name="currency" required>
                                <option value="GBP" @selected($currency==='GBP')>GBP</option>
                                <option value="USD" @selected($currency==='USD')>USD</option>
                                <option value="EUR" @selected($currency==='EUR')>EUR</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="3">{{ old('notes', $invoice->notes) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Terms & Conditions</label>
                            <textarea class="form-control" name="terms" rows="3">{{ old('terms', $invoice->terms) }}</textarea>
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
                        <!-- Existing Invoice items will be loaded here -->
                        @forelse($invoice->items as $index => $item)
                            <div class="invoice-item border rounded p-3 mb-3">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-5">
                                        <label class="form-label">Description *</label>
                                        <input type="text" class="form-control item-description" name="items[{{ $index }}][description]" 
                                               value="{{ old('items.'.$index.'.description', $item->description ?? '') }}" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Quantity *</label>
                                        <input type="number" class="form-control item-quantity" name="items[{{ $index }}][quantity]" 
                                               value="{{ old('items.'.$index.'.quantity', $item->quantity ?? 1) }}" min="0.01" step="0.01" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Unit *</label>
                                        <select class="form-select item-unit" name="items[{{ $index }}][unit]" required>
                                            @php $itemUnit = old('items.'.$index.'.unit', $item->unit ?? 'unit') @endphp
                                            <option value="unit" {{ $itemUnit === 'unit' ? 'selected' : '' }}>Unit</option>
                                            <option value="hour" {{ $itemUnit === 'hour' ? 'selected' : '' }}>Hour</option>
                                            <option value="day" {{ $itemUnit === 'day' ? 'selected' : '' }}>Day</option>
                                            <option value="week" {{ $itemUnit === 'week' ? 'selected' : '' }}>Week</option>
                                            <option value="month" {{ $itemUnit === 'month' ? 'selected' : '' }}>Month</option>
                                            <option value="sqft" {{ $itemUnit === 'sqft' ? 'selected' : '' }}>Sq Ft</option>
                                            <option value="sqm" {{ $itemUnit === 'sqm' ? 'selected' : '' }}>Sq M</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Unit Price *</label>
                                        <input type="number" step="0.01" class="form-control item-price" name="items[{{ $index }}][unit_price]" 
                                               value="{{ old('items.'.$index.'.unit_price', $item->unit_price ?? 0) }}" required>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-item">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <!-- No items yet, template will handle this -->
                        @endforelse
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6 offset-md-6">
                            @php
                                $currencySymbol = $invoice->currency === 'GBP' ? '£' : ($invoice->currency === 'USD' ? '$' : '€');
                            @endphp
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Subtotal:</strong></td>
                                    <td class="text-end"><span id="subtotal">{{ $currencySymbol }}{{ number_format($invoice->subtotal_amount ?? 0, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Tax:</strong></td>
                                    <td class="text-end"><span id="taxAmount">{{ $currencySymbol }}{{ number_format($invoice->tax_amount ?? 0, 2) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Discount:</strong></td>
                                    <td class="text-end"><span id="discountDisplay">-{{ $currencySymbol }}{{ number_format($invoice->discount_amount ?? 0, 2) }}</span></td>
                                </tr>
                                <tr class="table-primary">
                                    <td><strong>Total:</strong></td>
                                    <td class="text-end"><strong><span id="totalAmount">{{ $currencySymbol }}{{ number_format($invoice->total_amount ?? 0, 2) }}</span></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Actions</h5></div>
                <div class="card-body d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
</form>

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
                    <option value="week">Week</option>
                    <option value="month">Month</option>
                    <option value="sqft">Sq Ft</option>
                    <option value="sqm">Sq M</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Unit Price *</label>
                <input type="number" step="0.01" class="form-control item-price" name="items[INDEX][unit_price]" required>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger btn-sm remove-item">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const template = document.getElementById('itemTemplate');
    const itemsContainer = document.getElementById('invoiceItems');
    const addItemBtn = document.getElementById('addItemBtn');
    let itemIndex = {{ count($invoice->items) }};

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

    // Add event listeners to existing items
    document.querySelectorAll('.invoice-item').forEach(function(item) {
        const removeBtn = item.querySelector('.remove-item');
        const quantityInput = item.querySelector('.item-quantity');
        const priceInput = item.querySelector('.item-price');
        
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                if (itemsContainer.children.length > 1) {
                    item.remove();
                    calculateTotals();
                }
            });
        }
        
        if (quantityInput) quantityInput.addEventListener('input', calculateTotals);
        if (priceInput) priceInput.addEventListener('input', calculateTotals);
    });

    // Calculate totals
    function calculateTotals() {
        let subtotal = 0;
        
        // Get currency symbol
        const currencySelect = document.querySelector('select[name="currency"]');
        const currency = currencySelect ? currencySelect.value : 'GBP';
        const currencySymbol = currency === 'USD' ? '$' : (currency === 'GBP' ? '£' : '€');
        
        document.querySelectorAll('.invoice-item').forEach(function(item) {
            const quantity = parseFloat(item.querySelector('.item-quantity').value) || 0;
            const price = parseFloat(item.querySelector('.item-price').value) || 0;
            const total = quantity * price;
            
            subtotal += total;
        });
        
        const taxRate = parseFloat(document.querySelector('input[name="tax_rate"]').value) || 0;
        const discountAmount = parseFloat(document.querySelector('input[name="discount_amount"]').value) || 0;
        
        const taxAmount = subtotal * (taxRate / 100);
        const totalAmount = subtotal + taxAmount - discountAmount;
        
        document.getElementById('subtotal').textContent = currencySymbol + subtotal.toFixed(2);
        document.getElementById('taxAmount').textContent = currencySymbol + taxAmount.toFixed(2);
        document.getElementById('discountDisplay').textContent = '-' + currencySymbol + discountAmount.toFixed(2);
        document.getElementById('totalAmount').textContent = currencySymbol + totalAmount.toFixed(2);
    }

    // Recalculate when tax rate, discount, or currency changes
    document.querySelector('input[name="tax_rate"]').addEventListener('input', calculateTotals);
    document.querySelector('input[name="discount_amount"]').addEventListener('input', calculateTotals);
    document.querySelector('select[name="currency"]').addEventListener('change', calculateTotals);
    
    // Initial calculation
    calculateTotals();
});
</script>
@endsection







