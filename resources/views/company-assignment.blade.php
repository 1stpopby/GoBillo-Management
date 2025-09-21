<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Company Assignment - {{ config('app.name', 'ProMax Team') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2c5aa0;
            --secondary-color: #f8f9fa;
            --accent-color: #17a2b8;
        }
        
        .company-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .company-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .company-card.selected {
            border-color: var(--primary-color);
            background-color: rgba(44, 90, 160, 0.05);
        }
        
        .plan-badge {
            background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-10 col-lg-8">
                <div class="text-center mb-5">
                    <h1 class="h2 text-primary">Welcome to {{ config('app.name', 'ProMax Team') }}!</h1>
                    <p class="lead text-muted">
                        Hi {{ $user->name }}, to get started you need to join a company. 
                        Please select a company below to request access.
                    </p>
                </div>

                <!-- Logout Option -->
                <div class="text-center mb-4">
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>

                <!-- Alert Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($companies->count() > 0)
                    <form method="POST" action="{{ route('company.assign') }}">
                        @csrf
                        
                        <div class="row">
                            @foreach($companies as $company)
                                <div class="col-md-6 mb-4">
                                    <div class="card company-card h-100" onclick="selectCompany({{ $company->id }})">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                         style="width: 50px; height: 50px;">
                                                        {{ strtoupper(substr($company->name, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <h5 class="card-title mb-1">{{ $company->name }}</h5>
                                                        <span class="plan-badge">{{ ucfirst($company->subscription_plan) }}</span>
                                                    </div>
                                                </div>
                                                <input type="radio" name="company_id" value="{{ $company->id }}" 
                                                       class="form-check-input" style="transform: scale(1.2);">
                                            </div>
                                            
                                            @if($company->description)
                                                <p class="card-text text-muted mb-3">{{ Str::limit($company->description, 100) }}</p>
                                            @endif
                                            
                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <div class="small text-muted">Users</div>
                                                    <div class="fw-bold">{{ $company->users_count ?? 0 }}/{{ $company->max_users }}</div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="small text-muted">Projects</div>
                                                    <div class="fw-bold">{{ $company->projects_count ?? 0 }}</div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="small text-muted">Location</div>
                                                    <div class="fw-bold">{{ $company->city ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                            
                                            @if($company->website)
                                                <div class="mt-3">
                                                    <a href="{{ $company->website }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-globe"></i> Visit Website
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="text-center mt-4">
                            <div class="mb-3">
                                <label for="message" class="form-label">Optional message to company admin:</label>
                                <textarea name="message" id="message" class="form-control" rows="3" 
                                          placeholder="Introduce yourself or explain why you want to join this company..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
                                <i class="bi bi-building"></i> Request to Join Company
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center">
                        <div class="mb-4">
                            <i class="bi bi-building text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h3>No Companies Available</h3>
                        <p class="text-muted mb-4">
                            There are currently no companies accepting new members. 
                            Please contact your administrator or try again later.
                        </p>
                        
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-secondary">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function selectCompany(companyId) {
            // Remove selected class from all cards
            document.querySelectorAll('.company-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            event.currentTarget.classList.add('selected');
            
            // Check the radio button
            document.querySelector(`input[value="${companyId}"]`).checked = true;
            
            // Enable submit button
            document.getElementById('submitBtn').disabled = false;
        }
        
        // Handle radio button changes
        document.querySelectorAll('input[name="company_id"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('submitBtn').disabled = false;
            });
        });
    </script>
</body>
</html> 