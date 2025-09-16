@component('emails.layout', ['company' => $company])

<h2 style="color: #2c3e50; margin-bottom: 20px;">New Invoice Created</h2>

<p>Dear {{ $client->contact_name }},</p>

<p>We have created a new invoice for your recent project. Please find the details below:</p>

<div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Invoice Number:</td>
            <td style="padding: 8px 0;">{{ $invoice->invoice_number }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Total Amount:</td>
            <td style="padding: 8px 0; color: #27ae60; font-size: 18px; font-weight: bold;">£{{ number_format($invoice->total_amount, 2) }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Due Date:</td>
            <td style="padding: 8px 0;">{{ $invoice->due_date }}</td>
        </tr>
    </table>
</div>

<p>Please review the invoice and arrange payment by the due date. If you have any questions or concerns, please don't hesitate to contact us.</p>

<div style="margin: 30px 0;">
    <a href="#" style="background-color: #3498db; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">View Invoice</a>
</div>

<p>Thank you for your business!</p>

@endcomponent
