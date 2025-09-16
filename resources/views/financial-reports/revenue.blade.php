@extends('layouts.app')

@section('title', 'Revenue Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="h3 mb-0">Revenue Report</h1>
    <p class="text-muted mb-0">Revenue analysis by client, project, and time period</p>
  </div>
  <a href="{{ route('financial-reports.index') }}" class="btn btn-outline-secondary">Back to Reports</a>
  </div>

<!-- Date Range Filter -->
<div class="card mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('financial-reports.revenue') }}" class="row g-3">
      <div class="col-md-3">
        <label for="start_date" class="form-label">Start Date</label>
        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate->toDateString() }}">
      </div>
      <div class="col-md-3">
        <label for="end_date" class="form-label">End Date</label>
        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate->toDateString() }}">
      </div>
      <div class="col-md-2">
        <label class="form-label">&nbsp;</label>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Update</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="card border-left-success h-100"><div class="card-body"><div class="d-flex justify-content-between align-items-center"><div><div class="text-xs text-success fw-bold text-uppercase mb-1">Invoices (Paid)</div><div class="h5 mb-0">${{ number_format($revenueBySource['invoices'] ?? 0, 2) }}</div></div><i class="bi bi-receipt" style="font-size:2rem"></i></div></div></div>
  </div>
  <div class="col-md-3">
    <div class="card border-left-info h-100"><div class="card-body"><div class="d-flex justify-content-between align-items-center"><div><div class="text-xs text-info fw-bold text-uppercase mb-1">Estimates Converted</div><div class="h5 mb-0">${{ number_format($revenueBySource['estimates_converted'] ?? 0, 2) }}</div></div><i class="bi bi-check2-circle" style="font-size:2rem"></i></div></div></div>
  </div>
</div>

<div class="row">
  <div class="col-lg-6">
    <div class="card mb-4">
      <div class="card-header"><h5 class="card-title mb-0">Revenue by Client</h5></div>
      <div class="card-body">
        @if($revenueByClient->count())
          <div class="table-responsive">
            <table class="table table-sm align-middle">
              <thead><tr><th>Client</th><th class="text-end">Total Revenue</th></tr></thead>
              <tbody>
                @foreach($revenueByClient as $row)
                  <tr>
                    <td>{{ optional($row->client)->display_name ?? 'Unknown' }}</td>
                    <td class="text-end">${{ number_format($row->total_revenue, 2) }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-muted mb-0">No revenue recorded in this period.</p>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card mb-4">
      <div class="card-header"><h5 class="card-title mb-0">Revenue by Project</h5></div>
      <div class="card-body">
        @if($revenueByProject->count())
          <div class="table-responsive">
            <table class="table table-sm align-middle">
              <thead><tr><th>Project</th><th class="text-end">Total Revenue</th></tr></thead>
              <tbody>
                @foreach($revenueByProject as $row)
                  <tr>
                    <td>{{ optional($row->project)->name ?? 'Unknown' }}</td>
                    <td class="text-end">${{ number_format($row->total_revenue, 2) }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <p class="text-muted mb-0">No revenue recorded in this period.</p>
        @endif
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card">
      <div class="card-header"><h5 class="card-title mb-0">Monthly Revenue Trend</h5></div>
      <div class="card-body"><canvas id="monthlyRevenueChart" height="64"></canvas></div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const revCtx = document.getElementById('monthlyRevenueChart')?.getContext('2d');
if (revCtx) {
  new Chart(revCtx, {
    type: 'bar',
    data: {
      labels: {!! json_encode($monthlyRevenue->pluck('month')) !!},
      datasets: [{
        label: 'Revenue',
        data: {!! json_encode($monthlyRevenue->pluck('total_revenue')) !!},
        backgroundColor: 'rgba(75, 192, 192, 0.5)'
      }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
  });
}
</script>
@endpush
@endsection







