@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Expense</h1>
        <a href="{{ route('expenses.show', $expense) }}" class="btn btn-outline-secondary">Cancel</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('expenses.update', $expense) }}" enctype="multipart/form-data" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}" @selected($expense->category===$cat->name)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-8">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" value="{{ old('description', $expense->description) }}" required />
                </div>

                <div class="col-md-4">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-select">
                        <option value="">—</option>
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}" @selected($expense->project_id===$proj->id)>{{ $proj->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Amount</label>
                    <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', $expense->amount) }}" required />
                </div>

                <div class="col-md-4">
                    <label class="form-label">Date</label>
                    <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', \Carbon\Carbon::parse($expense->expense_date)->toDateString()) }}" required />
                </div>

                <div class="col-md-4">
                    <label class="form-label">Vendor</label>
                    <input type="text" name="vendor" class="form-control" value="{{ old('vendor', $expense->vendor) }}" />
                </div>

                <div class="col-md-4">
                    <label class="form-label">Payment Method</label>
                    <input type="text" name="payment_method" class="form-control" value="{{ old('payment_method', $expense->payment_method) }}" />
                </div>

                <div class="col-md-2">
                    <label class="form-label">Mileage</label>
                    <input type="number" step="0.01" name="mileage" class="form-control" value="{{ old('mileage', $expense->mileage) }}" />
                </div>

                <div class="col-md-2">
                    <label class="form-label">Mileage Rate</label>
                    <input type="number" step="0.01" name="mileage_rate" class="form-control" value="{{ old('mileage_rate', $expense->mileage_rate) }}" />
                </div>

                <div class="col-md-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="3" class="form-control">{{ old('notes', $expense->notes) }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Receipt (PDF/JPG/PNG)</label>
                    <input type="file" name="receipt" accept=".pdf,.jpg,.jpeg,.png" class="form-control" />
                    @if($expense->receipt_path)
                        <div class="form-text">Current: {{ $expense->receipt_path }}</div>
                    @endif
                </div>

                <div class="col-12">
                    <button class="btn btn-primary" type="submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection







