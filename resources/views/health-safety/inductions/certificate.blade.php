<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Site Induction Certificate - {{ $induction->certificate_number }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 40px;
            background-color: #ffffff;
            color: #333;
            line-height: 1.6;
        }
        
        .certificate {
            max-width: 800px;
            margin: 0 auto;
            border: 8px solid #198754;
            padding: 60px;
            text-align: center;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .header {
            margin-bottom: 40px;
        }
        
        .company-logo {
            font-size: 28px;
            font-weight: bold;
            color: #198754;
            margin-bottom: 10px;
        }
        
        .certificate-title {
            font-size: 36px;
            font-weight: bold;
            color: #198754;
            margin: 30px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .subtitle {
            font-size: 18px;
            color: #666;
            margin-bottom: 40px;
        }
        
        .recipient-section {
            margin: 40px 0;
            padding: 30px;
            background-color: rgba(25, 135, 84, 0.05);
            border-radius: 10px;
        }
        
        .recipient-name {
            font-size: 32px;
            font-weight: bold;
            color: #198754;
            margin: 20px 0;
            text-decoration: underline;
        }
        
        .completion-text {
            font-size: 16px;
            margin: 20px 0;
            line-height: 1.8;
        }
        
        .site-info {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin: 20px 0;
        }
        
        .details-section {
            margin: 40px 0;
            text-align: left;
        }
        
        .details-grid {
            display: table;
            width: 100%;
            margin: 20px 0;
        }
        
        .detail-row {
            display: table-row;
        }
        
        .detail-label {
            display: table-cell;
            font-weight: bold;
            padding: 8px 20px 8px 0;
            width: 40%;
            color: #666;
        }
        
        .detail-value {
            display: table-cell;
            padding: 8px 0;
            color: #333;
        }
        
        .topics-section {
            margin: 30px 0;
            text-align: left;
        }
        
        .topics-title {
            font-size: 18px;
            font-weight: bold;
            color: #198754;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .topics-grid {
            display: table;
            width: 100%;
        }
        
        .topic-item {
            display: table-row;
        }
        
        .topic-check {
            display: table-cell;
            width: 20px;
            padding: 5px;
            color: #198754;
            font-weight: bold;
        }
        
        .topic-text {
            display: table-cell;
            padding: 5px 0;
        }
        
        .signatures-section {
            margin: 50px 0 30px 0;
            display: table;
            width: 100%;
        }
        
        .signature-block {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 0 20px;
        }
        
        .signature-line {
            border-bottom: 2px solid #333;
            height: 50px;
            margin-bottom: 10px;
            position: relative;
        }
        
        .signature-label {
            font-weight: bold;
            color: #666;
        }
        
        .certificate-footer {
            margin-top: 40px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        
        .certificate-number {
            font-size: 14px;
            font-weight: bold;
            color: #198754;
            margin-top: 20px;
        }
        
        .validity-section {
            margin: 30px 0;
            padding: 20px;
            background-color: rgba(25, 135, 84, 0.1);
            border-radius: 8px;
            border-left: 4px solid #198754;
        }
        
        .validity-text {
            font-size: 16px;
            font-weight: bold;
            color: #198754;
        }
        
        @media print {
            body {
                padding: 20px;
            }
            
            .certificate {
                border: 6px solid #198754;
                padding: 40px;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="header">
            <div class="company-logo">{{ config('app.name', 'GoBillo') }}</div>
            <div class="certificate-title">Site Safety Induction Certificate</div>
            <div class="subtitle">This certifies that the below named person has successfully completed site safety induction</div>
        </div>

        <div class="recipient-section">
            <div class="completion-text">This is to certify that</div>
            <div class="recipient-name">{{ $induction->inductee_name }}</div>
            
            @if($induction->inductee_company || $induction->inductee_role)
                <div class="completion-text">
                    @if($induction->inductee_company)
                        of <strong>{{ $induction->inductee_company }}</strong>
                    @endif
                    @if($induction->inductee_role)
                        {{ $induction->inductee_company ? 'as' : 'in the role of' }} <strong>{{ $induction->inductee_role }}</strong>
                    @endif
                </div>
            @endif
            
            <div class="completion-text">has successfully completed the mandatory site safety induction for</div>
            
            @if($induction->site)
                <div class="site-info">{{ $induction->site->name }}</div>
            @else
                <div class="site-info">General Site Safety</div>
            @endif
        </div>

        <div class="details-section">
            <div class="details-grid">
                <div class="detail-row">
                    <div class="detail-label">Induction Date:</div>
                    <div class="detail-value">{{ $induction->inducted_at->format('F j, Y \a\t g:i A') }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Conducted By:</div>
                    <div class="detail-value">{{ $induction->inductedBy->name }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Certificate Number:</div>
                    <div class="detail-value">{{ $induction->certificate_number }}</div>
                </div>
                @if($induction->site)
                    <div class="detail-row">
                        <div class="detail-label">Site Location:</div>
                        <div class="detail-value">{{ $induction->site->name }}</div>
                    </div>
                @endif
            </div>
        </div>

        @if($induction->topics_covered && count($induction->topics_covered) > 0)
            <div class="topics-section">
                <div class="topics-title">Topics Covered During Induction</div>
                <div class="topics-grid">
                    @foreach($induction->topics_covered as $topic)
                        <div class="topic-item">
                            <div class="topic-check">✓</div>
                            <div class="topic-text">{{ ucwords(str_replace('_', ' ', $topic)) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="validity-section">
            <div class="validity-text">
                This certificate is valid until {{ $induction->valid_until->format('F j, Y') }}
            </div>
            @if($induction->valid_until < now())
                <div style="color: #dc3545; margin-top: 10px; font-weight: bold;">
                    ⚠️ This certificate has expired and is no longer valid
                </div>
            @elseif($induction->valid_until <= now()->addDays(30))
                <div style="color: #ffc107; margin-top: 10px; font-weight: bold;">
                    ⚠️ This certificate will expire soon
                </div>
            @endif
        </div>

        <div class="signatures-section">
            <div class="signature-block">
                <div class="signature-line"></div>
                <div class="signature-label">Inductee Signature</div>
            </div>
            <div class="signature-block">
                <div class="signature-line"></div>
                <div class="signature-label">Instructor Signature</div>
            </div>
        </div>

        <div class="certificate-footer">
            <div>This certificate confirms that the named person has received and understood the site-specific safety information, emergency procedures, and health and safety requirements.</div>
            <div class="certificate-number">Certificate ID: {{ $induction->certificate_number }}</div>
            <div style="margin-top: 10px;">Generated on {{ now()->format('F j, Y \a\t g:i A') }}</div>
        </div>
    </div>
</body>
</html>


