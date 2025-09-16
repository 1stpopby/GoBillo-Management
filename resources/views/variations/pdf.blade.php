<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Project Variation {{ $variation->variation_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }
        
        .header {
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        
        .company-info {
            text-align: right;
            margin-bottom: 20px;
        }
        
        .variation-title {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .variation-number {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }
        
        .section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
            margin-bottom: 8px;
        }
        
        .info-row {
            margin-bottom: 5px;
        }
        
        .label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
            color: #555;
        }
        
        .value {
            display: inline-block;
        }
        
        .impact-box {
            background-color: #f8f9fa;
            padding: 15px;
            border: 1px solid #ddd;
            margin: 15px 0;
        }
        
        .cost-impact {
            font-size: 16px;
            font-weight: bold;
        }
        
        .cost-positive {
            color: #28a745;
        }
        
        .cost-negative {
            color: #dc3545;
        }
        
        .description-box {
            background-color: #fff;
            border: 1px solid #ddd;
            border-left: 4px solid #007bff;
            padding: 12px;
            margin: 10px 0;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-info">
            <strong>{{ $company->name }}</strong><br>
            @if($company->address)
                {{ $company->address }}<br>
            @endif
            @if($company->phone)
                Tel: {{ $company->phone }}<br>
            @endif
            @if($company->email)
                Email: {{ $company->email }}
            @endif
        </div>
        
        <div class="variation-title">Project Variation</div>
        <div class="variation-number">{{ $variation->variation_number }}</div>
    </div>

    <!-- Client Information -->
    <div class="section">
        <div class="section-title">Client Information</div>
        <div class="info-row">
            <span class="label">Company:</span>
            <span class="value">{{ $client->company_name }}</span>
        </div>
        @if($client->contact_name && $client->contact_name !== $client->company_name)
        <div class="info-row">
            <span class="label">Contact:</span>
            <span class="value">{{ $client->contact_name }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="label">Email:</span>
            <span class="value">{{ $client->email }}</span>
        </div>
    </div>

    <!-- Project Information -->
    <div class="section">
        <div class="section-title">Project Information</div>
        <div class="info-row">
            <span class="label">Project Name:</span>
            <span class="value">{{ $project->name }}</span>
        </div>
        @if($project->description)
        <div class="info-row">
            <span class="label">Description:</span>
            <span class="value">{{ $project->description }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="label">Status:</span>
            <span class="value">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
        </div>
    </div>

    <!-- Variation Details -->
    <div class="section">
        <div class="section-title">Variation Details</div>
        <div class="info-row">
            <span class="label">Title:</span>
            <span class="value"><strong>{{ $variation->title }}</strong></span>
        </div>
        <div class="info-row">
            <span class="label">Type:</span>
            <span class="value">{{ ucfirst(str_replace('_', ' ', $variation->type)) }}</span>
        </div>
        <div class="info-row">
            <span class="label">Status:</span>
            <span class="value">{{ ucfirst(str_replace('_', ' ', $variation->status)) }}</span>
        </div>
        <div class="info-row">
            <span class="label">Requested:</span>
            <span class="value">{{ $variation->requested_date->format('F j, Y') }}</span>
        </div>
        @if($variation->required_by_date)
        <div class="info-row">
            <span class="label">Required By:</span>
            <span class="value">{{ $variation->required_by_date->format('F j, Y') }}</span>
        </div>
        @endif
        @if($variation->client_reference)
        <div class="info-row">
            <span class="label">Your Reference:</span>
            <span class="value">{{ $variation->client_reference }}</span>
        </div>
        @endif
    </div>

    <!-- Impact Summary -->
    <div class="impact-box">
        <div class="info-row">
            <span class="label">Cost Impact:</span>
            <span class="value cost-impact {{ $variation->cost_impact >= 0 ? 'cost-positive' : 'cost-negative' }}">
                {{ $variation->formatted_cost_impact }}
            </span>
        </div>
        <div class="info-row">
            <span class="label">Time Impact:</span>
            <span class="value"><strong>{{ $variation->formatted_time_impact }}</strong></span>
        </div>
    </div>

    <!-- Description -->
    <div class="section">
        <div class="section-title">Description</div>
        <div class="description-box">
            {{ $variation->description }}
        </div>
    </div>

    <!-- Reason -->
    <div class="section">
        <div class="section-title">Reason for Variation</div>
        <div class="description-box">
            {{ $variation->reason }}
        </div>
    </div>

    @if($variation->approval_notes)
    <!-- Approval Notes -->
    <div class="section">
        <div class="section-title">Approval Notes</div>
        <div class="description-box">
            {{ $variation->approval_notes }}
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This variation document was generated on {{ now()->format('F j, Y \a\t g:i A') }}.</p>
        <p>Please review this variation and contact us with any questions or to proceed with approval.</p>
    </div>
</body>
</html>