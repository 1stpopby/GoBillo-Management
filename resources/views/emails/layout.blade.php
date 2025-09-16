<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 40px;
            text-align: center;
        }
        .email-logo {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .email-tagline {
            font-size: 14px;
            opacity: 0.9;
        }
        .email-body {
            padding: 40px;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 30px 40px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .company-info {
            margin-bottom: 20px;
        }
        .company-name {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .signature {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            font-size: 14px;
            color: #666;
        }
        .social-links {
            margin-top: 20px;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #667eea;
            text-decoration: none;
        }
        .disclaimer {
            font-size: 12px;
            color: #999;
            margin-top: 20px;
            line-height: 1.4;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="email-logo">
                @if(isset($system) && $system)
                    GoBillo
                @elseif($company && $company->logo_url)
                    <img src="{{ $company->logo_url }}" alt="{{ $company->name }}" style="max-height: 40px;">
                @else
                    {{ $company->name ?? 'GoBillo' }}
                @endif
            </div>
            <div class="email-tagline">
                @if(isset($system) && $system)
                    Construction Management Platform
                @else
                    Professional Construction Management
                @endif
            </div>
        </div>

        <!-- Body -->
        <div class="email-body">
            {{ $slot }}

            <!-- Company Information -->
            @if($company && !isset($system))
            <div class="company-info">
                <div class="company-name">{{ $company->name }}</div>
                @if($company->email)
                    <div>Email: {{ $company->email }}</div>
                @endif
                @if($company->phone)
                    <div>Phone: {{ $company->phone }}</div>
                @endif
                @if($company->address)
                    <div>Address: {{ $company->address }}</div>
                @endif
            </div>
            @endif

            <!-- Email Signature -->
            @php
                $emailSetting = null;
                if (isset($system) && $system) {
                    $emailSetting = \App\Models\EmailSetting::system()->first();
                } elseif ($company) {
                    $emailSetting = \App\Models\EmailSetting::getActiveForCompany($company->id);
                }
            @endphp

            @if($emailSetting && $emailSetting->email_signature)
            <div class="signature">
                {!! nl2br(e($emailSetting->email_signature)) !!}
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="email-footer">
            @if(isset($system) && $system)
                <div>
                    <strong>GoBillo Team</strong><br>
                    Construction Management Platform<br>
                    <a href="mailto:support@gobillo.com">support@gobillo.com</a>
                </div>
            @else
                <div>
                    This email was sent by {{ $company->name ?? 'GoBillo' }}<br>
                    Powered by <strong>GoBillo</strong> - Construction Management Platform
                </div>
            @endif

            <div class="social-links">
                <a href="#">Website</a>
                <a href="#">Support</a>
                <a href="#">Privacy Policy</a>
            </div>

            <div class="disclaimer">
                This email was sent to you because you are registered with our construction management system.
                If you believe you received this email in error, please contact support.
                <br><br>
                © {{ date('Y') }} {{ isset($system) && $system ? 'GoBillo' : ($company->name ?? 'GoBillo') }}. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
