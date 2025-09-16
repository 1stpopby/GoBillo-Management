@extends('layouts.app')

@section('title', 'Cash Flow')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="h3 mb-0">Cash Flow</h1>
    <p class="text-muted mb-0">Cash in/out and outstanding balances</p>
  </div>
  <a href="{{ route('financial-reports.index') }}" class="btn btn-outline-secondary">Back to Reports</a>
</div>

<div class="card mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('financial-reports.cash-flow') }}" class="row g-3">
      <div class="col-md-3"><label class="form-label">Start Date</label><input type="date" name="start_date" class="form-control" value="{{ $startDate->toDateString() }}"></div>
      <div class="col-md-3"><label class="form-label">End Date</label><input type="date" name="end_date" class="form-control" value="{{ $endDate->toDateString() }}"></div>
      <div class="col-md-2"><label class="form-label">&nbsp;</label><div class="d-grid"><button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Update</button></div></div>
    </form>
  </div>
</div>

<div class="row mb-4">
  <div class="col-md-4"><div class="card border-left-primary h-100"><div class="card-body"><div class="text-xs text-primary fw-bold text-uppercase mb-1">Outstanding Invoices</div><div class="h5 mb-0">${{ number_format($outstandingInvoices, 2) }}</div></div></div></div>
  <div class="col-md-4"><div class="card border-left-warning h-100"><div class="card-body"><div class="text-xs text-warning fw-bold text-uppercase mb-1">Pending Reimbursements</div><div class="h5 mb-0">${{ number_format($pendingExpenses, 2) }}</div></div></div></div>
</div>

<div class="card">
  <div class="card-header"><h5 class="card-title mb-0">Monthly Cash Flow</h5></div>
  <div class="card-body"><canvas id="cashFlowChart" height="64"></canvas></div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const cfCtx = document.getElementById('cashFlowChart')?.getContext('2d');
if (cfCtx) {
  new Chart(cfCtx, {
    type: 'bar',
    data: {
      labels: {!! json_encode(collect($monthlyCashFlow)->pluck('month_name')) !!},
      datasets: [
        { label: 'Cash In', data: {!! json_encode(collect($monthlyCashFlow)->pluck('cash_in')) !!}, backgroundColor: 'rgba(75, 192, 192, 0.5)' },
        { label: 'Cash Out', data: {!! json_encode(collect($monthlyCashFlow)->pluck('cash_out')) !!}, backgroundColor: 'rgba(255, 99, 132, 0.5)' }
      ]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
  });
}
</script>
@endpush
@endsection







