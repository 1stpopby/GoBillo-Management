@extends('layouts.app')

@section('title', 'Expense Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="h3 mb-0">Expense Report</h1>
    <p class="text-muted mb-0">Expense breakdown by category, project, and team member</p>
  </div>
  <a href="{{ route('financial-reports.index') }}" class="btn btn-outline-secondary">Back to Reports</a>
</div>

<div class="card mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('financial-reports.expenses') }}" class="row g-3">
      <div class="col-md-3">
        <label class="form-label">Start Date</label>
        <input type="date" class="form-control" name="start_date" value="{{ $startDate->toDateString() }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">End Date</label>
        <input type="date" class="form-control" name="end_date" value="{{ $endDate->toDateString() }}">
      </div>
      <div class="col-md-2">
        <label class="form-label">&nbsp;</label>
        <div class="d-grid"><button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Update</button></div>
      </div>
    </form>
  </div>
</div>

<div class="row">
  <div class="col-lg-4">
    <div class="card mb-4">
      <div class="card-header"><h5 class="card-title mb-0">By Category</h5></div>
      <div class="card-body">
        @if(($expensesByCategory->count() ?? 0) > 0)
          <div class="table-responsive">
            <table class="table table-sm">
              <thead><tr><th>Category</th><th class="text-end">Amount</th></tr></thead>
              <tbody>
                @foreach($expensesByCategory as $row)
                  <tr><td>{{ ucfirst($row->category) }}</td><td class="text-end">${{ number_format($row->total_amount, 2) }}</td></tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-muted mb-0">No expenses in this period.</p>
        @endif
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card mb-4">
      <div class="card-header"><h5 class="card-title mb-0">By Project</h5></div>
      <div class="card-body">
        @if(($expensesByProject->count() ?? 0) > 0)
          <div class="table-responsive">
            <table class="table table-sm">
              <thead><tr><th>Project</th><th class="text-end">Amount</th></tr></thead>
              <tbody>
                @foreach($expensesByProject as $row)
                  <tr><td>{{ $row->project_name ?? 'Unknown' }}</td><td class="text-end">${{ number_format($row->total_amount, 2) }}</td></tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-muted mb-0">No expenses in this period.</p>
        @endif
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card mb-4">
      <div class="card-header"><h5 class="card-title mb-0">By User</h5></div>
      <div class="card-body">
        @if(($expensesByUser->count() ?? 0) > 0)
          <div class="table-responsive">
            <table class="table table-sm">
              <thead><tr><th>User</th><th class="text-end">Amount</th></tr></thead>
              <tbody>
                @foreach($expensesByUser as $row)
                  <tr><td>{{ $row->user_name ?? 'Unknown' }}</td><td class="text-end">${{ number_format($row->total_amount, 2) }}</td></tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-muted mb-0">No expenses in this period.</p>
        @endif
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card">
      <div class="card-header"><h5 class="card-title mb-0">Monthly Expenses</h5></div>
      <div class="card-body"><canvas id="monthlyExpensesChart" height="64"></canvas></div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const expCtx = document.getElementById('monthlyExpensesChart')?.getContext('2d');
if (expCtx) {
  new Chart(expCtx, {
    type: 'bar',
    data: {
      labels: {!! json_encode($monthlyExpenses->pluck('month')) !!},
      datasets: [{
        label: 'Expenses',
        data: {!! json_encode($monthlyExpenses->pluck('total_amount')) !!},
        backgroundColor: 'rgba(255, 99, 132, 0.5)'
      }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
  });
}
</script>
@endpush
@endsection


