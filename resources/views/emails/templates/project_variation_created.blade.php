@component('emails.layout', ['company' => $company])

<div style="text-align: center; margin-bottom: 30px;">
    <h1 style="color: #2c3e50; font-size: 28px; margin-bottom: 10px;">Project Variation Notice</h1>
    <p style="color: #7f8c8d; font-size: 16px; margin: 0;">{{ $variation->variation_number }}</p>
</div>

<p style="font-size: 16px; line-height: 1.6; color: #34495e;">Dear {{ $client->contact_name ?? $client->name }},</p>

<p style="font-size: 16px; line-height: 1.6; color: #34495e;">
    We have created a project variation that requires your review and approval for the following project:
</p>

<!-- Project Information -->
<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; margin: 25px 0;">
    <h3 style="margin: 0 0 10px 0; font-size: 20px;">{{ $project->name }}</h3>
    <p style="margin: 0; opacity: 0.9;">{{ $project->description ?? 'Project variation details' }}</p>
</div>

<!-- Variation Summary -->
<div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; font-weight: bold; color: #495057;">Variation Number:</td>
            <td style="padding: 8px 0; color: #007bff; font-weight: bold;">{{ $variation->variation_number }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold; color: #495057;">Title:</td>
            <td style="padding: 8px 0;">{{ $variation->title }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold; color: #495057;">Type:</td>
            <td style="padding: 8px 0;">{{ ucfirst(str_replace('_', ' ', $variation->type)) }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold; color: #495057;">Cost Impact:</td>
            <td style="padding: 8px 0; color: {{ $variation->cost_impact >= 0 ? '#28a745' : '#dc3545' }}; font-size: 18px; font-weight: bold;">
                {{ $variation->formatted_cost_impact }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold; color: #495057;">Time Impact:</td>
            <td style="padding: 8px 0; font-weight: bold;">{{ $variation->formatted_time_impact }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold; color: #495057;">Requested Date:</td>
            <td style="padding: 8px 0;">{{ $variation->requested_date->format('M j, Y') }}</td>
        </tr>
        @if($variation->required_by_date)
        <tr>
            <td style="padding: 8px 0; font-weight: bold; color: #495057;">Required By:</td>
            <td style="padding: 8px 0; {{ $variation->is_overdue ? 'color: #dc3545; font-weight: bold;' : '' }}">
                {{ $variation->required_by_date->format('M j, Y') }}
                @if($variation->is_overdue)
                    <span style="color: #dc3545;">⚠️ Overdue</span>
                @endif
            </td>
        </tr>
        @endif
    </table>
</div>

<!-- Description -->
<div style="background-color: #fff; border-left: 4px solid #3498db; padding: 15px; margin: 20px 0; border-radius: 0 8px 8px 0;">
    <h4 style="margin-top: 0; color: #2c3e50;">Description:</h4>
    <p style="margin-bottom: 0; line-height: 1.6;">{{ $variation->description }}</p>
</div>

<!-- Reason -->
<div style="background-color: #fff; border-left: 4px solid #f39c12; padding: 15px; margin: 20px 0; border-radius: 0 8px 8px 0;">
    <h4 style="margin-top: 0; color: #2c3e50;">Reason for Variation:</h4>
    <p style="margin-bottom: 0; line-height: 1.6;">{{ $variation->reason }}</p>
</div>

@if($variation->client_reference)
<!-- Client Reference -->
<div style="background-color: #e8f4fd; padding: 15px; margin: 20px 0; border-radius: 8px;">
    <h4 style="margin-top: 0; color: #2c3e50;">Your Reference:</h4>
    <p style="margin-bottom: 0; font-weight: bold; color: #007bff;">{{ $variation->client_reference }}</p>
</div>
@endif

<p style="font-size: 16px; line-height: 1.6; color: #34495e;">
    This variation requires your review and approval before we can proceed. Please contact us to discuss the details or if you have any questions.
</p>

<!-- Contact Information -->
<div style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 25px; border-radius: 10px; text-align: center; margin: 30px 0;">
    <h3 style="margin: 0 0 15px 0; font-size: 20px;">Contact Us</h3>
    <div style="background-color: rgba(255,255,255,0.2); padding: 15px; border-radius: 8px;">
        <p style="margin: 0; font-size: 14px; opacity: 0.9;">
            <strong>{{ $company->name }}</strong><br>
            @if($company->email)
                Email: {{ $company->email }}<br>
            @endif
            @if($company->phone)
                Phone: {{ $company->phone }}
            @endif
        </p>
    </div>
</div>

<p style="font-size: 16px; line-height: 1.6; color: #34495e;">
    Thank you for your attention to this matter. We look forward to your response.
</p>

@endcomponent
