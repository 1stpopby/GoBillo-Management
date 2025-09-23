<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Statement - {{ $statement_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            border-bottom: 3px solid #0066cc;
            margin-bottom: 30px;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #0066cc;
        }
        .statement-title {
            font-size: 20px;
            margin-top: 10px;
        }
        .info-section {
            margin-bottom: 30px;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-grid td {
            padding: 5px 0;
            vertical-align: top;
        }
        .section-title {
            background-color: #f5f5f5;
            padding: 10px;
            margin: 20px 0 10px 0;
            font-weight: bold;
            font-size: 16px;
            border-left: 4px solid #0066cc;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #e9ecef;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #dee2e6;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .summary-box {
            background-color: #f8f9fa;
            padding: 15px;
            margin: 20px 0;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
        .financial-summary {
            width: 100%;
            margin: 20px 0;
        }
        .financial-summary td {
            padding: 10px;
            text-align: center;
        }
        .amount-box {
            background-color: #f8f9fa;
            padding: 10px;
            border: 1px solid #dee2e6;
            margin: 5px 0;
        }
        .text-success {
            color: #28a745;
        }
        .text-danger {
            color: #dc3545;
        }
        .text-primary {
            color: #0066cc;
        }
        .text-info {
            color: #17a2b8;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 12px;
            border-radius: 3px;
            color: white;
        }
        .badge-success {
            background-color: #28a745;
        }
        .badge-danger {
            background-color: #dc3545;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">{{ $company->name ?? 'ProMax Team' }}</div>
        <div class="statement-title">PAYMENT STATEMENT</div>
        <div style="margin-top: 10px;">
            <strong>Statement Number:</strong> {{ $statement_number }}<br>
            <strong>Date:</strong> {{ $statement_date->format('F d, Y') }}
        </div>
    </div>

    <!-- Client Information -->
    <table class="info-grid">
        <tr>
            <td style="width: 50%;">
                <strong>TO:</strong><br>
                {{ $client->company_name }}<br>
                @if($client->contact_name)
                    Attn: {{ $client->contact_name }}<br>
                @endif
                @if($client->address)
                    {{ $client->address }}<br>
                @endif
                @if($client->email)
                    {{ $client->email }}<br>
                @endif
                @if($client->phone)
                    {{ $client->phone }}
                @endif
            </td>
            <td style="width: 50%; text-align: right;">
                <strong>STATEMENT PERIOD:</strong><br>
                @if($date_from && $date_to)
                    {{ $date_from->format('F d, Y') }} to<br>
                    {{ $date_to->format('F d, Y') }}
                @else
                    All Time
                @endif
            </td>
        </tr>
    </table>

    <!-- Financial Summary -->
    <div class="section-title">FINANCIAL SUMMARY</div>
    <table class="financial-summary">
        <tr>
            <td>
                <div class="amount-box">
                    <strong>TOTAL BUDGET</strong><br>
                    <span style="font-size: 18px;" class="text-primary">£{{ number_format($total_budget, 2) }}</span>
                </div>
            </td>
            <td>
                <div class="amount-box">
                    <strong>TOTAL INVOICED</strong><br>
                    <span style="font-size: 18px;" class="text-info">£{{ number_format($total_invoiced, 2) }}</span>
                </div>
            </td>
            <td>
                <div class="amount-box">
                    <strong>TOTAL PAID</strong><br>
                    <span style="font-size: 18px;" class="text-success">£{{ number_format($total_paid, 2) }}</span>
                </div>
            </td>
            <td>
                <div class="amount-box">
                    <strong>OUTSTANDING</strong><br>
                    <span style="font-size: 18px;" class="text-danger">£{{ number_format($outstanding_balance, 2) }}</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Projects & Budgets -->
    @if($projects->count() > 0)
        <div class="section-title">PROJECTS & BUDGETS</div>
        <table>
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Site</th>
                    <th>Status</th>
                    <th class="text-right">Budget</th>
                </tr>
            </thead>
            <tbody>
                @foreach($projects as $project)
                    <tr>
                        <td>{{ $project->name }}</td>
                        <td>{{ $project->site ? $project->site->name : 'N/A' }}</td>
                        <td>{{ ucfirst($project->status) }}</td>
                        <td class="text-right">£{{ number_format($project->budget, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3">TOTAL BUDGET</td>
                    <td class="text-right">£{{ number_format($total_budget, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <!-- Invoice History -->
    @if($invoices->count() > 0)
        <div class="section-title">INVOICE HISTORY</div>
        <table>
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th class="text-right">Amount</th>
                    <th class="text-right">Paid</th>
                    <th class="text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                    @php
                        $invoicePaid = $invoice->payments->where('status', 'completed')->sum('amount');
                        $invoiceBalance = $invoice->total_amount - $invoicePaid;
                    @endphp
                    <tr>
                        <td>{{ $invoice->invoice_number }}</td>
                        <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                        <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                        <td>
                            <span class="badge badge-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'overdue' ? 'danger' : 'warning') }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td class="text-right">£{{ number_format($invoice->total_amount, 2) }}</td>
                        <td class="text-right text-success">£{{ number_format($invoicePaid, 2) }}</td>
                        <td class="text-right {{ $invoiceBalance > 0 ? 'text-danger' : '' }}">
                            £{{ number_format($invoiceBalance, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4">TOTALS</td>
                    <td class="text-right">£{{ number_format($total_invoiced, 2) }}</td>
                    <td class="text-right text-success">£{{ number_format($total_paid, 2) }}</td>
                    <td class="text-right text-danger">£{{ number_format($outstanding_balance, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <!-- Payment Transactions -->
    @if($payments->count() > 0)
        <div class="section-title">PAYMENT TRANSACTIONS</div>
        <table>
            <thead>
                <tr>
                    <th>Payment #</th>
                    <th>Date</th>
                    <th>Invoice</th>
                    <th>Method</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_number }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment->processed_at)->format('M d, Y') }}</td>
                        <td>{{ $payment->invoice->invoice_number ?? 'N/A' }}</td>
                        <td>{{ ucfirst($payment->payment_gateway) }}</td>
                        <td class="text-right text-success">£{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4">TOTAL PAYMENTS RECEIVED</td>
                    <td class="text-right text-success">£{{ number_format($total_paid, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <!-- Summary Box -->
    <div class="summary-box">
        <h3>STATEMENT SUMMARY</h3>
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    <strong>Budget Analysis:</strong><br>
                    Total Project Budget: £{{ number_format($total_budget, 2) }}<br>
                    Amount Invoiced: £{{ number_format($total_invoiced, 2) }}<br>
                    Remaining Budget: <span class="{{ $remaining_budget >= 0 ? 'text-success' : 'text-danger' }}">
                        £{{ number_format($remaining_budget, 2) }}
                    </span>
                </td>
                <td style="width: 50%;">
                    <strong>Payment Status:</strong><br>
                    Total Invoiced: £{{ number_format($total_invoiced, 2) }}<br>
                    Total Paid: <span class="text-success">£{{ number_format($total_paid, 2) }}</span><br>
                    Outstanding Balance: <span class="text-danger">£{{ number_format($outstanding_balance, 2) }}</span>
                </td>
            </tr>
        </table>
        
        @if($outstanding_balance > 0)
            <div style="margin-top: 15px; padding: 10px; background-color: #fff3cd; border: 1px solid #ffc107;">
                <strong>PAYMENT DUE:</strong> There is an outstanding balance of £{{ number_format($outstanding_balance, 2) }}.
                Please arrange payment at your earliest convenience.
            </div>
        @else
            <div style="margin-top: 15px; padding: 10px; background-color: #d4edda; border: 1px solid #28a745;">
                <strong>ACCOUNT UP TO DATE:</strong> All invoices have been paid in full. Thank you for your prompt payment.
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This statement was generated on {{ $statement_date->format('F d, Y') }}</p>
        <p>{{ $company->name ?? 'ProMax Team' }} | {{ $company->email ?? '' }} | {{ $company->phone ?? '' }}</p>
        <p>Thank you for your business!</p>
    </div>
</body>
</html>