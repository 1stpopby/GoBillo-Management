@component('emails.layout', ['company' => null, 'system' => true])

<h2 style="color: #e74c3c; margin-bottom: 20px;">⚠️ Subscription Expiry Warning</h2>

<p>Dear {{ optional($company)->name ?? 'ProMax Team' }} Team,</p>

<p>This is a reminder that your ProMax Team subscription is approaching its expiry date. Please take action to avoid service interruption.</p>

<div style="background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Plan:</td>
            <td style="padding: 8px 0;">{{ $subscription->plan_name }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Expires On:</td>
            <td style="padding: 8px 0; color: #e74c3c; font-weight: bold;">{{ $subscription->expires_at }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Monthly Cost:</td>
            <td style="padding: 8px 0; font-size: 18px; font-weight: bold;">£{{ number_format($subscription->amount, 2) }}</td>
        </tr>
    </table>
</div>

<div style="background-color: #f8f9fa; border-left: 4px solid #e74c3c; padding: 15px; margin: 20px 0;">
    <h4 style="margin-top: 0; color: #e74c3c;">What happens if you don't renew?</h4>
    <ul style="margin-bottom: 0; padding-left: 20px;">
        <li>Access to your ProMax Team account will be suspended</li>
        <li>Your team will not be able to log in or access project data</li>
        <li>All automated features and notifications will stop</li>
        <li>Your data will be preserved for 30 days after expiry</li>
    </ul>
</div>

<p>To continue using ProMax Team without interruption, please renew your subscription before the expiry date.</p>

<div style="margin: 30px 0; text-align: center;">
    <a href="{{ $renewal_url }}" style="background-color: #27ae60; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-size: 16px; font-weight: bold;">Renew Subscription Now</a>
</div>

<p>If you have any questions about your subscription or need assistance with renewal, please contact our support team.</p>

<p>Thank you for choosing ProMax Team!</p>

@endcomponent
