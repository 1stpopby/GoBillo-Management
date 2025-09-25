<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Summary Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Financial Summary (Test View)</h1>
        <div class="alert alert-info">
            Logged in as: {{ auth()->user()->name }} (Company ID: {{ auth()->user()->company_id }})
        </div>
        
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Revenue</h5>
                        <h2 class="text-success">£{{ number_format($revenue, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Expenses</h5>
                        <h2 class="text-danger">£{{ number_format($totalExpenses, 2) }}</h2>
                        <small>Operative: £{{ number_format($operativeWages, 2) }}<br>
                        Regular: £{{ number_format($regularExpenses, 2) }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Gross Profit</h5>
                        <h2 class="text-primary">£{{ number_format($grossProfit, 2) }}</h2>
                        <small>Margin: {{ $profitMargin }}%</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Active Projects</h5>
                        <h2>{{ $activeProjects }}</h2>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <h3>Recent Invoices</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Client</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Paid Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentInvoices as $invoice)
                    <tr>
                        <td>{{ $invoice->invoice_number }}</td>
                        <td>{{ $invoice->client ? $invoice->client->name : 'N/A' }}</td>
                        <td>£{{ number_format($invoice->total_amount, 2) }}</td>
                        <td>{{ $invoice->status }}</td>
                        <td>{{ $invoice->paid_at ? $invoice->paid_at->format('Y-m-d') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            <a href="/financial-reports" class="btn btn-primary">Go to Full Financial Reports</a>
            <a href="/dashboard" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>