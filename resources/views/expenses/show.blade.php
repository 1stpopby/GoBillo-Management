@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Expense #{{ $expense->expense_number ?? $expense->id }}</h1>
        <div class="d-flex gap-2">
            @can('update', $expense)
                <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-outline-primary">Edit</a>
            @endcan
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Category</dt>
                        <dd class="col-sm-8">{{ $expense->category }}</dd>

                        <dt class="col-sm-4">Description</dt>
                        <dd class="col-sm-8">{{ $expense->description }}</dd>

                        <dt class="col-sm-4">Project</dt>
                        <dd class="col-sm-8">{{ optional($expense->project)->name ?: '—' }}</dd>

                        <dt class="col-sm-4">User</dt>
                        <dd class="col-sm-8">{{ optional($expense->user)->name ?: '—' }}</dd>

                        <dt class="col-sm-4">Amount</dt>
                        <dd class="col-sm-8">${{ number_format(($expense->amount + (($expense->mileage ?? 0) * ($expense->mileage_rate ?? 0))), 2) }}</dd>

                        <dt class="col-sm-4">Date</dt>
                        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') }}</dd>

                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8"><span class="badge bg-light text-dark text-capitalize">{{ $expense->status ?? 'draft' }}</span></dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">Actions</div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($expense->receipt_path)
                            <a class="btn btn-outline-secondary" href="{{ route('expenses.download-receipt', $expense) }}">Download Receipt</a>
                        @endif
                        @can('delete', $expense)
                            <form method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('Delete this expense?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger" type="submit">Delete</button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection







