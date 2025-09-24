<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'New Variation Notification' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin: -30px -30px 20px;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            background: #ffc107;
            color: #333;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .detail-label {
            font-weight: bold;
            color: #666;
        }
        .detail-value {
            color: #333;
        }
        .description-box {
            background: white;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .btn:hover {
            background: #218838;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #666;
            font-size: 12px;
        }
        .alert {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin: 0;">{{ $title ?? 'New Variation Submitted' }}</h2>
        </div>

        <div class="badge">ACTION REQUIRED</div>
        
        <p>Dear Admin,</p>
        
        <p>{{ $message ?? 'A new project variation has been submitted for your review and cost agreement.' }}</p>

        <div class="details">
            <h3 style="margin-top: 0; color: #495057;">Variation Details</h3>
            <div class="detail-row">
                <span class="detail-label">Variation Number:</span>
                <span class="detail-value">{{ $variation_number ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Project:</span>
                <span class="detail-value">{{ $project_name ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Title:</span>
                <span class="detail-value">{{ $variation_title ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Type:</span>
                <span class="detail-value">{{ $type ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Submitted By:</span>
                <span class="detail-value">{{ $submitted_by ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Requested Date:</span>
                <span class="detail-value">{{ $requested_date ?? 'N/A' }}</span>
            </div>
        </div>

        @if(isset($description))
        <div class="description-box">
            <h4 style="margin-top: 0; color: #495057;">Description</h4>
            <p style="margin-bottom: 0;">{{ $description }}</p>
        </div>
        @endif

        @if(isset($reason))
        <div class="description-box">
            <h4 style="margin-top: 0; color: #495057;">Reason for Variation</h4>
            <p style="margin-bottom: 0;">{{ $reason }}</p>
        </div>
        @endif

        <div class="alert">
            <strong>⚠️ Important:</strong> This variation requires cost agreement with the client before approval. Please review and set the appropriate cost impact.
        </div>

        <div style="text-align: center;">
            @if(isset($action_url))
            <a href="{{ $action_url }}" class="btn">Review Variation</a>
            @endif
        </div>

        <div class="footer">
            <p>This is an automated notification from ProMax Team.</p>
            <p>© {{ date('Y') }} ProMax Team - Construction Management System</p>
        </div>
    </div>
</body>
</html>