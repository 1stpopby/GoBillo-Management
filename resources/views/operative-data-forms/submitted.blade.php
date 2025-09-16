<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Submitted - Thank You</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .status-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin: 2rem auto;
            max-width: 600px;
            overflow: hidden;
        }
        
        .status-header {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .status-body {
            padding: 2rem;
            text-align: center;
        }
        
        .status-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            margin: 1rem 0;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 2px solid #ffeaa7;
        }
        
        .status-approved {
            background: #d1edff;
            color: #0c5460;
            border: 2px solid #74c0fc;
        }
        
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .status-container {
                margin: 1rem;
                border-radius: 15px;
            }
            
            .status-header, .status-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="status-container">
            @if(session('success'))
                <div class="status-header">
                    <div class="status-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <h1>Form Submitted Successfully!</h1>
                    <p class="mb-0">Thank you for providing your information</p>
                </div>
            @else
                <div class="status-header">
                    <div class="status-icon">
                        <i class="bi bi-file-earmark-check"></i>
                    </div>
                    <h1>Form Status</h1>
                    <p class="mb-0">Your submission details</p>
                </div>
            @endif
            
            <div class="status-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif
                
                <h4>Hello {{ $form->full_name }}</h4>
                
                <div class="status-badge status-{{ $form->status }}">
                    @if($form->status === 'pending')
                        <i class="bi bi-clock me-2"></i>Pending Review
                    @elseif($form->status === 'approved')
                        <i class="bi bi-check-circle me-2"></i>Approved
                    @elseif($form->status === 'rejected')
                        <i class="bi bi-x-circle me-2"></i>Rejected
                    @endif
                </div>
                
                @if($form->status === 'pending')
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle me-2"></i>What happens next?</h6>
                        <p class="mb-0">Your information is currently being reviewed by our team. You will be contacted once the review is complete.</p>
                    </div>
                    
                    <p><strong>Submitted:</strong> {{ $form->submitted_at->format('M j, Y \a\t H:i') }}</p>
                    
                @elseif($form->status === 'approved')
                    <div class="alert alert-success">
                        <h6><i class="bi bi-check-circle me-2"></i>Congratulations!</h6>
                        <p class="mb-0">Your information has been approved. You will be contacted shortly regarding next steps.</p>
                    </div>
                    
                    <p><strong>Approved:</strong> {{ $form->approved_at->format('M j, Y \a\t H:i') }}</p>
                    
                @elseif($form->status === 'rejected')
                    <div class="alert alert-danger">
                        <h6><i class="bi bi-exclamation-triangle me-2"></i>Form Rejected</h6>
                        @if($form->rejection_reason)
                            <p><strong>Reason:</strong> {{ $form->rejection_reason }}</p>
                        @endif
                        <p class="mb-0">Please contact us for more information about resubmitting your application.</p>
                    </div>
                    
                    <p><strong>Rejected:</strong> {{ $form->rejected_at->format('M j, Y \a\t H:i') }}</p>
                @endif
                
                <hr class="my-4">
                
                <div class="row text-start">
                    <div class="col-md-6">
                        <h6>Contact Information</h6>
                        <p class="mb-1"><strong>Email:</strong> {{ $form->email_address }}</p>
                        <p class="mb-1"><strong>Phone:</strong> {{ $form->mobile_number }}</p>
                        <p class="mb-3"><strong>Trade:</strong> {{ $form->primary_trade }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Application Details</h6>
                        <p class="mb-1"><strong>Form ID:</strong> #{{ $form->id }}</p>
                        <p class="mb-1"><strong>Submitted:</strong> {{ $form->submitted_at->format('M j, Y') }}</p>
                        @if($form->approvedBy)
                            <p class="mb-3"><strong>Reviewed by:</strong> {{ $form->approvedBy->name }}</p>
                        @endif
                    </div>
                </div>
                
                <div class="alert alert-light">
                    <small class="text-muted">
                        <i class="bi bi-shield-check me-2"></i>
                        Your personal information is secure and will only be used for employment purposes.
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
