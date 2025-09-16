<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operative Invoice {{ $operativeInvoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background: #fff;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }
        
        .header {
            display: table;
            width: 100%;
            margin-bottom: 40px;
            border-bottom: 3px solid #0066cc;
            padding-bottom: 20px;
        }
        
        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: top;
        }
        
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 10px;
        }
        
        .company-details {
            color: #666;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .invoice-title {
            font-size: 36px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .invoice-subtitle {
            font-size: 16px;
            color: #0066cc;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .invoice-number {
            font-size: 18px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .invoice-date {
            font-size: 14px;
            color: #666;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            margin-top: 10px;
        }
        
        .status-paid {
            background: #d4edda;
            color: #155724;
        }
        
        .status-approved {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-submitted {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        
        .info-section {
            display: table;
            width: 100%;
            margin: 40px 0;
        }
        
        .info-left, .info-right {
            display: table-cell;
            width: 48%;
            vertical-align: top;
        }
        
        .info-right {
            text-align: right;
        }
        
        .info-header {
            font-size: 16px;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .info-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        
        .info-details {
            color: #666;
            font-size: 13px;
            line-height: 1.5;
        }
        
        .summary-cards {
            display: table;
            width: 100%;
            margin: 30px 0;
        }
        
        .summary-card {
            display: table-cell;
            width: 25%;
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            margin-right: 10px;
        }
        
        .summary-card:last-child {
            margin-right: 0;
        }
        
        .summary-value {
            font-size: 24px;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 5px;
        }
        
        .summary-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .daily-table {
            width: 100%;
            border-collapse: collapse;
            margin: 40px 0;
        }
        
        .daily-table th {
            background: #0066cc;
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .daily-table th:last-child {
            text-align: right;
        }
        
        .daily-table th:nth-child(3) {
            text-align: center;
        }
        
        .daily-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        
        .daily-table td:last-child {
            text-align: right;
        }
        
        .daily-table td:nth-child(3) {
            text-align: center;
        }
        
        .worked-day {
            background: #d4edda;
            font-weight: bold;
        }
        
        .not-worked-day {
            background: #f8f9fa;
            color: #6c757d;
        }
        
        .hours-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            color: white;
        }
        
        .hours-worked {
            background: #28a745;
        }
        
        .hours-not-worked {
            background: #6c757d;
        }
        
        .totals-section {
            width: 100%;
            margin-top: 30px;
        }
        
        .totals-table {
            width: 300px;
            margin-left: auto;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #eee;
        }
        
        .totals-table .label {
            color: #666;
            font-weight: 500;
        }
        
        .totals-table .amount {
            text-align: right;
            font-weight: 500;
        }
        
        .totals-table .total-row {
            border-top: 2px solid #0066cc;
            background: #f8f9fa;
        }
        
        .totals-table .total-row td {
            padding: 12px;
            font-weight: bold;
            font-size: 16px;
            color: #0066cc;
        }
        
        .notes-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        
        .notes-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .notes-content {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            color: #666;
            line-height: 1.6;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 2px solid #0066cc;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        
        .footer-company {
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 5px;
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <div class="company-name">{{ $operativeInvoice->company->name ?? 'Company Name' }}</div>
                <div class="company-details">
                    @if($operativeInvoice->company && $operativeInvoice->company->address)
                        {{ $operativeInvoice->company->address }}<br>
                    @endif
                    @if($operativeInvoice->company && $operativeInvoice->company->email)
                        Email: {{ $operativeInvoice->company->email }}<br>
                    @endif
                    @if($operativeInvoice->company && $operativeInvoice->company->phone)
                        Phone: {{ $operativeInvoice->company->phone }}
                    @endif
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-title">OPERATIVE INVOICE</div>
                <div class="invoice-subtitle">Weekly Timesheet</div>
                <div class="invoice-number">#{{ $operativeInvoice->invoice_number }}</div>
                <div class="invoice-date">Period: {{ $operativeInvoice->week_starting->format('M d') }} - {{ $operativeInvoice->week_ending->format('M d, Y') }}</div>
                <div class="status-badge status-{{ $operativeInvoice->status }}">
                    {{ ucfirst($operativeInvoice->status) }}
                </div>
            </div>
        </div>

        <!-- Information Section -->
        <div class="info-section">
            <div class="info-left">
                <div class="info-header">Operative Information</div>
                <div class="info-name">{{ $operativeInvoice->operative->name }}</div>
                <div class="info-details">
                    Email: {{ $operativeInvoice->operative->email }}<br>
                    Day Rate: £{{ number_format($operativeInvoice->day_rate, 2) }}
                </div>
            </div>
            <div class="info-right">
                <div class="info-header">Work Location</div>
                <div class="info-name">{{ $operativeInvoice->site->name ?? 'N/A' }}</div>
                <div class="info-details">
                    @if($operativeInvoice->project)
                        Project: {{ $operativeInvoice->project->name }}<br>
                    @endif
                    Manager: {{ $operativeInvoice->manager->name ?? 'N/A' }}
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="summary-value">{{ $operativeInvoice->total_hours }}</div>
                <div class="summary-label">Total Hours</div>
            </div>
            <div class="summary-card">
                <div class="summary-value">{{ number_format($operativeInvoice->total_hours / 8, 1) }}</div>
                <div class="summary-label">Days Worked</div>
            </div>
            <div class="summary-card">
                <div class="summary-value">£{{ number_format($operativeInvoice->gross_amount, 2) }}</div>
                <div class="summary-label">Gross Amount</div>
            </div>
            <div class="summary-card">
                <div class="summary-value">£{{ number_format($operativeInvoice->net_amount, 2) }}</div>
                <div class="summary-label">Net Amount</div>
            </div>
        </div>

        <!-- Daily Breakdown -->
        @if($operativeInvoice->items->count() > 0)
            <table class="daily-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Day</th>
                        <th>Hours</th>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($operativeInvoice->items as $item)
                        <tr class="{{ $item->worked ? 'worked-day' : 'not-worked-day' }}">
                            <td>{{ $item->work_date->format('M d, Y') }}</td>
                            <td>{{ $item->work_date->format('l') }}</td>
                            <td>
                                <span class="hours-badge {{ $item->worked ? 'hours-worked' : 'hours-not-worked' }}">
                                    {{ $item->worked ? $item->hours_worked : 0 }}h
                                </span>
                            </td>
                            <td>{{ $item->worked ? ($item->description ?: 'Regular work') : 'Not worked' }}</td>
                            <td>£{{ number_format($item->total_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Totals -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="label">Gross Amount:</td>
                    <td class="amount">£{{ number_format($operativeInvoice->gross_amount, 2) }}</td>
                </tr>
                @if($operativeInvoice->cis_applicable && $operativeInvoice->cis_deduction > 0)
                    <tr>
                        <td class="label">CIS Deduction ({{ number_format($operativeInvoice->cis_rate, 2) }}%):</td>
                        <td class="amount">-£{{ number_format($operativeInvoice->cis_deduction, 2) }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td>Net Amount:</td>
                    <td>£{{ number_format($operativeInvoice->net_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Notes -->
        @if($operativeInvoice->notes)
            <div class="notes-section">
                <div class="notes-title">Notes:</div>
                <div class="notes-content">{!! nl2br(e($operativeInvoice->notes)) !!}</div>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div class="footer-company">{{ $operativeInvoice->company->name ?? 'Company Name' }}</div>
            <div>Generated on {{ now()->format('M d, Y \a\t g:i A') }}</div>
        </div>
    </div>
</body>
</html>
