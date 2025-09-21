@component('emails.layout', ['company' => $company])

<h2 style="color: #2c3e50; margin-bottom: 20px;">New Task Assigned</h2>

<p>Dear {{ $assignee->name }},</p>

<p>You have been assigned a new task on the {{ $project->name }} project. Please review the details below:</p>

<div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Task:</td>
            <td style="padding: 8px 0;">{{ $task->title }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Project:</td>
            <td style="padding: 8px 0;">{{ $project->name }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Due Date:</td>
            <td style="padding: 8px 0; 
                @if($task->priority === 'high') color: #e74c3c; font-weight: bold; @endif">
                {{ $task->due_date }}
            </td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Priority:</td>
            <td style="padding: 8px 0;">
                <span style="
                    padding: 4px 8px; 
                    border-radius: 4px; 
                    font-size: 12px; 
                    font-weight: bold;
                    @if($task->priority === 'high') 
                        background-color: #e74c3c; color: white;
                    @elseif($task->priority === 'medium')
                        background-color: #f39c12; color: white;
                    @else
                        background-color: #95a5a6; color: white;
                    @endif
                ">
                    {{ strtoupper($task->priority) }}
                </span>
            </td>
        </tr>
    </table>
</div>

@if($task->description)
<div style="background-color: #fff; border-left: 4px solid #3498db; padding: 15px; margin: 20px 0;">
    <h4 style="margin-top: 0; color: #2c3e50;">Task Description:</h4>
    <p style="margin-bottom: 0;">{{ $task->description }}</p>
</div>
@endif

<p>Please log in to your ProMax Team account to view full task details and update progress as needed.</p>

<div style="margin: 30px 0;">
    <a href="#" style="background-color: #3498db; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">View Task Details</a>
</div>

<p>If you have any questions about this task, please contact your project manager.</p>

@endcomponent
