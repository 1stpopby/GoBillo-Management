@extends('layouts.app')

@section('title', 'Profit & Loss')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="h3 mb-0">Profit & Loss</h1>
    <p class="text-muted mb-0">Statement with margins and monthly trend</p>
  </div>
  <a href="{{ route('financial-reports.index') }}" class="btn btn-outline-secondary">Back to Reports</a>
</div>

<div class="card mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('financial-reports.profit-loss') }}" class="row g-3">
      <div class="col-md-3"><label class="form-label">Start Date</label><input type="date" name="start_date" class="form-control" value="{{ $startDate->toDateString() }}"></div>
      <div class="col-md-3"><label class="form-label">End Date</label><input type="date" name="end_date" class="form-control" value="{{ $endDate->toDateString() }}"></div>
      <div class="col-md-2"><label class="form-label">&nbsp;</label><div class="d-grid"><button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Update</button></div></div>
    </form>
  </div>
</div>

<div class="row mb-4">
  <div class="col-md-3"><div class="card border-left-success h-100"><div class="card-body"><div class="text-xs text-success fw-bold text-uppercase mb-1">Revenue</div><div class="h5 mb-0">${{ number_format($revenue, 2) }}</div></div></div></div>
  <div class="col-md-3"><div class="card border-left-danger h-100"><div class="card-body"><div class="text-xs text-danger fw-bold text-uppercase mb-1">Direct Costs</div><div class="h5 mb-0">${{ number_format($directCosts, 2) }}</div></div></div></div>
  <div class="col-md-3"><div class="card border-left-primary h-100"><div class="card-body"><div class="text-xs text-primary fw-bold text-uppercase mb-1">Operating Expenses</div><div class="h5 mb-0">${{ number_format($operatingExpenses, 2) }}</div></div></div></div>
  <div class="col-md-3"><div class="card border-left-info h-100"><div class="card-body"><div class="text-xs text-info fw-bold text-uppercase mb-1">Net Profit</div><div class="h5 mb-0">${{ number_format($netProfit, 2) }} <small class="text-muted">({{ number_format($netMargin, 1) }}%)</small></div></div></div></div>
</div>

<div class="card">
  <div class="card-header"><h5 class="card-title mb-0">Monthly P&L</h5></div>
  <div class="card-body"><canvas id="plChart" height="64"></canvas></div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const plCtx = document.getElementById('plChart')?.getContext('2d');
if (plCtx) {
  new Chart(plCtx, {
    type: 'line',
    data: {
      labels: {!! json_encode(collect($monthlyPL)->pluck('month_name')) !!},
      datasets: [
        { label: 'Revenue', data: {!! json_encode(collect($monthlyPL)->pluck('revenue')) !!}, borderColor: 'rgb(75, 192, 192)', tension: 0.1 },
        { label: 'Expenses', data: {!! json_encode(collect($monthlyPL)->pluck('expenses')) !!}, borderColor: 'rgb(255, 99, 132)', tension: 0.1 },
        { label: 'Profit', data: {!! json_encode(collect($monthlyPL)->pluck('profit')) !!}, borderColor: 'rgb(54, 162, 235)', tension: 0.1 }
      ]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
  });
}
</script>
@endpush
@endsection







