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
@endsection







