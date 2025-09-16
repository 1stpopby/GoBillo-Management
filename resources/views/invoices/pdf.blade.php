<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number ?? $invoice->id }}</title>
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
        
        .status-sent {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-draft {
            background: #f8f9fa;
            color: #6c757d;
        }
        
        .status-overdue {
            background: #f8d7da;
            color: #721c24;
        }
        
        .billing-section {
            display: table;
            width: 100%;
            margin: 40px 0;
        }
        
        .bill-from, .bill-to {
            display: table-cell;
            width: 48%;
            vertical-align: top;
        }
        
        .bill-to {
            text-align: right;
        }
        
        .billing-header {
            font-size: 16px;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .billing-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        
        .billing-details {
            color: #666;
            font-size: 13px;
            line-height: 1.5;
        }
        
        .project-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
            border-left: 4px solid #0066cc;
        }
        
        .project-label {
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 5px;
        }
        
        .project-name {
            font-size: 16px;
            color: #333;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 40px 0;
        }
        
        .items-table th {
            background: #0066cc;
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .items-table th:last-child,
        .items-table td:last-child {
            text-align: right;
        }
        
        .items-table th:nth-child(2),
        .items-table td:nth-child(2),
        .items-table th:nth-child(3),
        .items-table td:nth-child(3) {
            text-align: center;
        }
        
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        
        .items-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .item-description {
            font-weight: 500;
            color: #333;
        }
        
        .item-quantity {
            font-weight: bold;
            color: #0066cc;
        }
        
        .item-unit {
            color: #666;
            font-size: 12px;
        }
        
        .item-price {
            font-weight: 500;
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
        
        .page-break {
            page-break-after: always;
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
                <div class="company-name">{{ $invoice->company->name ?? 'Company Name' }}</div>
                <div class="company-details">
                    @if($invoice->company && $invoice->company->address)
                        {{ $invoice->company->address }}<br>
                    @endif
                    @if($invoice->company && $invoice->company->email)
                        Email: {{ $invoice->company->email }}<br>
                    @endif
                    @if($invoice->company && $invoice->company->phone)
                        Phone: {{ $invoice->company->phone }}
                    @endif
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-number">#{{ $invoice->invoice_number ?? $invoice->id }}</div>
                <div class="invoice-date">Date: {{ $invoice->issue_date->format('M d, Y') }}</div>
                <div class="invoice-date">Due: {{ $invoice->due_date->format('M d, Y') }}</div>
                <div class="status-badge status-{{ $invoice->status }}">
                    {{ ucfirst($invoice->status) }}
                </div>
            </div>
        </div>

        <!-- Billing Information -->
        <div class="billing-section">
            <div class="bill-from">
                <div class="billing-header">Bill From</div>
                <div class="billing-name">{{ $invoice->company->name ?? 'Company Name' }}</div>
                <div class="billing-details">
                    @if($invoice->company && $invoice->company->address)
                        {{ $invoice->company->address }}<br>
                    @endif
                    @if($invoice->company && $invoice->company->email)
                        {{ $invoice->company->email }}<br>
                    @endif
                    @if($invoice->company && $invoice->company->phone)
                        {{ $invoice->company->phone }}
                    @endif
                </div>
            </div>
            <div class="bill-to">
                <div class="billing-header">Bill To</div>
                <div class="billing-name">{{ $invoice->client->name ?? 'Client Name' }}</div>
                <div class="billing-details">
                    @if($invoice->client && $invoice->client->contact_person_email)
                        {{ $invoice->client->contact_person_email }}<br>
                    @endif
                    @if($invoice->client && $invoice->client->contact_person_phone)
                        {{ $invoice->client->contact_person_phone }}<br>
                    @endif
                    @if($invoice->client && $invoice->client->address)
                        {{ $invoice->client->address }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Project Information -->
        @if($invoice->project)
            <div class="project-info">
                <div class="project-label">Project:</div>
                <div class="project-name">{{ $invoice->project->name }}</div>
            </div>
        @endif

        <!-- Invoice Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->items as $item)
                    <tr>
                        <td class="item-description">{{ $item->description }}</td>
                        <td class="item-quantity">{{ number_format($item->quantity, 2) }}</td>
                        <td class="item-unit">{{ $item->unit }}</td>
                        <td class="item-price">£{{ number_format($item->unit_price, 2) }}</td>
                        <td class="item-price">£{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #999;">
                            No items found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="amount">£{{ number_format($invoice->subtotal_amount ?? 0, 2) }}</td>
                </tr>
                @if($invoice->tax_rate > 0)
                    <tr>
                        <td class="label">Tax ({{ number_format($invoice->tax_rate, 2) }}%):</td>
                        <td class="amount">£{{ number_format($invoice->tax_amount ?? 0, 2) }}</td>
                    </tr>
                @endif
                @if($invoice->discount_amount > 0)
                    <tr>
                        <td class="label">Discount:</td>
                        <td class="amount" style="color: #28a745;">-£{{ number_format($invoice->discount_amount, 2) }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td>Total ({{ $invoice->currency }}):</td>
                    <td>£{{ number_format($invoice->total_amount ?? 0, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Notes and Terms -->
        @if($invoice->notes || $invoice->terms)
            <div class="notes-section">
                @if($invoice->notes)
                    <div style="margin-bottom: 30px;">
                        <div class="notes-title">Notes:</div>
                        <div class="notes-content">{{ $invoice->notes }}</div>
                    </div>
                @endif
                @if($invoice->terms)
                    <div>
                        <div class="notes-title">Terms & Conditions:</div>
                        <div class="notes-content">{{ $invoice->terms }}</div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div class="footer-company">{{ $invoice->company->name ?? 'Company Name' }}</div>
            <div>Thank you for your business!</div>
        </div>
    </div>
</body>
</html>
