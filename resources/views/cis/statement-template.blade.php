<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CIS Statement - {{ $employee->full_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.4;
            color: #333;
            background: white;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
        }
        
        /* Header */
        .header {
            width: 100%;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #1e3a8a;
            overflow: hidden;
        }
        
        .header-content {
            width: 100%;
        }
        
        .company-info {
            float: left;
            width: 50%;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 5px;
        }
        
        .company-address {
            font-size: 12px;
            color: #666;
            line-height: 1.3;
        }
        
        .statement-title {
            float: right;
            width: 50%;
            text-align: right;
        }
        
        .statement-title h1 {
            font-size: 28px;
            color: #1e3a8a;
            margin-bottom: 5px;
        }
        
        .statement-subtitle {
            font-size: 14px;
            color: #666;
        }
        
        /* Statement Info */
        .statement-info {
            width: 100%;
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .info-section {
            float: left;
            width: 48%;
            margin-right: 4%;
        }
        
        .info-section:last-child {
            margin-right: 0;
        }
        
        .info-section h3 {
            font-size: 14px;
            color: #1e3a8a;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 13px;
        }
        
        .info-label {
            color: #666;
            font-weight: 500;
        }
        
        .info-value {
            font-weight: 600;
            color: #333;
        }
        
        /* Summary Section */
        .summary-section {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #e2e8f0;
        }
        
        .summary-title {
            font-size: 16px;
            color: #1e3a8a;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            text-align: center;
        }
        
        .summary-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
        }
        
        .summary-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
        }
        
        .summary-value.gross { color: #059669; }
        .summary-value.deductions { color: #dc2626; }
        .summary-value.materials { color: #7c3aed; }
        .summary-value.net { color: #1e3a8a; }
        
        /* Payments Table */
        .payments-section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 16px;
            color: #1e3a8a;
            margin-bottom: 15px;
            font-weight: bold;
        }
        
        .payments-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            background: white;
            border: 1px solid #e2e8f0;
        }
        
        .payments-table th {
            background: linear-gradient(135deg, #1e3a8a 0%, #3730a3 100%);
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .payments-table th:first-child { border-top-left-radius: 4px; }
        .payments-table th:last-child { border-top-right-radius: 4px; }
        
        .payments-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        
        .payments-table tr:nth-child(even) {
            background: #f8fafc;
        }
        
        .payments-table tr:hover {
            background: #e2e8f0;
        }
        
        .amount {
            text-align: right;
            font-weight: 600;
        }
        
        .amount.gross { color: #059669; }
        .amount.deduction { color: #dc2626; }
        .amount.materials { color: #7c3aed; }
        .amount.net { color: #1e3a8a; }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            font-size: 11px;
            color: #666;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .footer-section h4 {
            font-size: 12px;
            color: #1e3a8a;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .footer p {
            margin-bottom: 4px;
            line-height: 1.3;
        }
        
        /* HMRC Compliance */
        .compliance-notice {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            font-size: 11px;
            color: #92400e;
        }
        
        .compliance-notice strong {
            color: #78350f;
        }
        
        /* Print Styles */
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            
            .container {
                padding: 20px;
            }
            
            .summary-section {
                background: #f8fafc !important;
            }
            
            .payments-table th {
                background: #1e3a8a !important;
                color: white !important;
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
            }
            
            .statement-info {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="company-info">
                    <div class="company-name">{{ $company->name ?? 'Company Name' }}</div>
                    <div class="company-address">
                        @if($company->address)
                            {{ $company->address }}<br>
                        @endif
                        @if($company->city || $company->postal_code)
                            {{ $company->city }} {{ $company->postal_code }}<br>
                        @endif
                        @if($company->phone)
                            Tel: {{ $company->phone }}<br>
                        @endif
                        @if($company->email)
                            Email: {{ $company->email }}
                        @endif
                    </div>
                </div>
                <div class="statement-title">
                    <h1>CIS STATEMENT</h1>
                    <div class="statement-subtitle">Construction Industry Scheme</div>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>

        <!-- Statement Information -->
        <div class="statement-info">
            <div class="info-section">
                <h3>Contractor Details</h3>
                <div class="info-row">
                    <span class="info-label">Full Name:</span>
                    <span class="info-value">{{ $employee->full_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">CIS Number:</span>
                    <span class="info-value">{{ $employee->cis_number ?? 'Not Assigned' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">CIS Status:</span>
                    <span class="info-value">{{ ucfirst($employee->cis_status ?? 'Unverified') }}</span>
                </div>
                @if($employee->address)
                <div class="info-row">
                    <span class="info-label">Address:</span>
                    <span class="info-value">{{ $employee->address }}</span>
                </div>
                @endif
            </div>
            
            <div class="info-section">
                <h3>Statement Details</h3>
                <div class="info-row">
                    <span class="info-label">Statement Period:</span>
                    <span class="info-value">{{ $summary['period_label'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Generated Date:</span>
                    <span class="info-value">{{ now()->format('j F Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Payments:</span>
                    <span class="info-value">{{ $summary['payment_count'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Average CIS Rate:</span>
                    <span class="info-value">{{ number_format($summary['average_rate'], 1) }}%</span>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-title">PAYMENT SUMMARY</div>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-label">Gross Payments</div>
                    <div class="summary-value gross">£{{ number_format($summary['total_gross'], 2) }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">CIS Deductions</div>
                    <div class="summary-value deductions">£{{ number_format($summary['total_deductions'], 2) }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Materials</div>
                    <div class="summary-value materials">£{{ number_format($summary['total_materials'], 2) }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Net Payments</div>
                    <div class="summary-value net">£{{ number_format($summary['total_net'], 2) }}</div>
                </div>
            </div>
        </div>

        <!-- HMRC Compliance Notice -->
        <div class="compliance-notice">
            <strong>HMRC Compliance Notice:</strong> This statement shows Construction Industry Scheme (CIS) deductions made from payments during the specified period. These deductions are advance payments towards your tax and National Insurance liabilities. Keep this statement for your records and provide it to your accountant for tax return purposes.
        </div>

        <!-- Payments Detail Table -->
        <div class="payments-section">
            <div class="section-title">PAYMENT DETAILS</div>
            <table class="payments-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Project</th>
                        <th>Gross Amount</th>
                        <th>CIS Rate</th>
                        <th>CIS Deduction</th>
                        <th>Materials</th>
                        <th>Net Payment</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                        <td>{{ $payment->project->name ?? 'No Project' }}</td>
                        <td class="amount gross">£{{ number_format($payment->gross_amount, 2) }}</td>
                        <td class="amount">{{ number_format($payment->cis_rate, 1) }}%</td>
                        <td class="amount deduction">£{{ number_format($payment->cis_deduction, 2) }}</td>
                        <td class="amount materials">£{{ number_format($payment->materials_cost, 2) }}</td>
                        <td class="amount net">£{{ number_format($payment->net_payment, 2) }}</td>
                        <td>{{ ucfirst($payment->status) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-grid">
                <div class="footer-section">
                    <h4>Important Information</h4>
                    <p>• This statement is for your records and tax return purposes</p>
                    <p>• CIS deductions are advance payments towards your tax liabilities</p>
                    <p>• Contact your accountant if you need assistance with tax returns</p>
                    <p>• Keep this statement with your other tax documents</p>
                </div>
                <div class="footer-section">
                    <h4>Contact Information</h4>
                    <p><strong>{{ $company->name ?? 'Company Name' }}</strong></p>
                    @if($company->phone)
                    <p>Phone: {{ $company->phone }}</p>
                    @endif
                    @if($company->email)
                    <p>Email: {{ $company->email }}</p>
                    @endif
                    <p>Generated: {{ now()->format('j F Y \a\t H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
