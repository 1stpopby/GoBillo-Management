<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Get Started - GoBillo Construction Management</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0066cc;
            --secondary-color: #f8f9fa;
            --accent-color: #28a745;
            --text-dark: #2c3e50;
            --text-muted: #6c757d;
            --gradient-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Navigation */
        .navbar {
            background: white !important;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-color) !important;
        }

        .navbar-nav .nav-link {
            color: var(--text-dark) !important;
            font-weight: 500;
            margin: 0 0.5rem;
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-color) !important;
        }

        /* Hero Section */
        .hero-section {
            background: var(--gradient-bg);
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding-top: 2rem;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
            font-weight: 400;
        }

        .hero-features {
            list-style: none;
            margin-bottom: 3rem;
        }

        .hero-features li {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 0.75rem;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
        }

        .hero-features li i {
            color: #28a745;
            margin-right: 0.75rem;
            font-size: 1.2rem;
        }

        /* Registration Form */
        .registration-form {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            z-index: 10;
            display: block !important;
            visibility: visible !important;
        }

        .registration-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-bg);
        }

        .form-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .form-subtitle {
            color: var(--text-muted);
            text-align: center;
            margin-bottom: 2rem;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.1);
            background-color: white;
        }

        .btn-get-started {
            background: var(--gradient-bg);
            border: none;
            border-radius: 12px;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-get-started:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .btn-get-started:active {
            transform: translateY(0);
        }

        /* Features Section */
        .features-section {
            padding: 5rem 0;
            background: linear-gradient(45deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .feature-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient-bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: white;
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .feature-description {
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* Animations */
        .fade-in {
            opacity: 1 !important;
            transform: translateY(0px) !important;
            animation: none !important;
        }

        .fade-in-delay-1 { animation-delay: 0.2s; }
        .fade-in-delay-2 { animation-delay: 0.4s; }
        .fade-in-delay-3 { animation-delay: 0.6s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Floating Animation */
        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        /* Loading State */
        .btn-loading {
            position: relative;
            color: transparent !important;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid transparent;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Footer Styles */
        .hover-opacity-100:hover {
            opacity: 1 !important;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .registration-form {
                padding: 2rem 1.5rem;
                margin-top: 2rem;
            }
            
            .form-title {
                font-size: 1.75rem;
            }
        }

        /* Success Message */
        .alert-success {
            border: none;
            border-radius: 12px;
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            border: none;
            border-radius: 12px;
            background: linear-gradient(45deg, #dc3545, #fd7e14);
            color: white;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-building me-2"></i>GoBillo
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Pricing</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Sign In
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold text-primary" href="{{ route('get-started') }}">
                            <i class="bi bi-rocket-takeoff me-1"></i>Get Started
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" style="padding-top: 100px;">
        <div class="container">
            <div class="row align-items-center" style="min-height: 80vh; padding: 2rem 0;">
                <div class="col-lg-6 hero-content">
                    <h1 class="hero-title fade-in">
                        {{ \App\Models\SiteContent::get('get_started_hero_title', 'Transform Your Construction Business') }}
                    </h1>
                    <p class="hero-subtitle fade-in fade-in-delay-1">
                        {{ \App\Models\SiteContent::get('get_started_hero_subtitle', 'Join thousands of construction companies using GoBillo to streamline operations, manage projects, and grow their business.') }}
                    </p>
                    
                    <ul class="hero-features fade-in fade-in-delay-2">
                        <li><i class="bi bi-check-circle-fill"></i> Complete Project Management</li>
                        <li><i class="bi bi-check-circle-fill"></i> Team & Resource Tracking</li>
                        <li><i class="bi bi-check-circle-fill"></i> Financial Management</li>
                        <li><i class="bi bi-check-circle-fill"></i> Client Portal & Communication</li>
                        <li><i class="bi bi-check-circle-fill"></i> Real-time Reporting & Analytics</li>
                    </ul>

                    <div class="fade-in fade-in-delay-3">
                        <p class="text-white-50 mb-0">
                            <i class="bi bi-shield-check me-2"></i>30-day free trial • No credit card required • Setup in minutes
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="registration-form floating fade-in fade-in-delay-2" style="margin-top: 2rem; border: 2px solid #007bff; background: white !important;">
                        <h2 class="form-title">{{ \App\Models\SiteContent::get('get_started_form_title', 'Get Started Today') }}</h2>
                        <p class="form-subtitle">{{ \App\Models\SiteContent::get('get_started_form_subtitle', 'Create your company account and start your free trial') }}</p>

                        @if(session('success'))
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                            </div>
                        @endif

                        <form id="registrationForm" action="{{ route('company.register') }}" method="POST">
                            @csrf
                            
                            <div class="form-group">
                                <label for="company_name" class="form-label">Company Name *</label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                       id="company_name" name="company_name" value="{{ old('company_name') }}" 
                                       placeholder="Enter your company name" required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name" class="form-label">First Name *</label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                               id="first_name" name="first_name" value="{{ old('first_name') }}" 
                                               placeholder="First name" required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name" class="form-label">Last Name *</label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                               id="last_name" name="last_name" value="{{ old('last_name') }}" 
                                               placeholder="Last name" required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Business Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" 
                                       placeholder="you@company.com" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}" 
                                       placeholder="(555) 123-4567">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" placeholder="Create a secure password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">Confirm Password *</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" 
                                       placeholder="Confirm your password" required>
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="#" class="text-primary">Terms of Service</a> and 
                                        <a href="#" class="text-primary">Privacy Policy</a>
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-get-started" id="submitBtn">
                                <i class="bi bi-rocket-takeoff me-2"></i>Start Free Trial
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <p class="text-muted small">
                                Already have an account? <a href="{{ route('login') }}" class="text-primary fw-semibold">Sign In</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 fw-bold text-dark mb-3">Why Choose GoBillo?</h2>
                    <p class="lead text-muted">Everything you need to manage your construction business in one powerful platform</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-kanban"></i>
                        </div>
                        <h3 class="feature-title">Project Management</h3>
                        <p class="feature-description">
                            Organize projects, track progress, manage tasks, and collaborate with your team in real-time.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <h3 class="feature-title">Team Management</h3>
                        <p class="feature-description">
                            Manage your workforce, track time, assign roles, and monitor productivity across all sites.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h3 class="feature-title">Financial Tracking</h3>
                        <p class="feature-description">
                            Monitor budgets, track expenses, generate invoices, and get insights into your profitability.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <h3 class="feature-title">Site Management</h3>
                        <p class="feature-description">
                            Manage multiple construction sites, track locations, and ensure compliance across all projects.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-chat-dots"></i>
                        </div>
                        <h3 class="feature-title">Client Portal</h3>
                        <p class="feature-description">
                            Keep clients informed with real-time updates, progress reports, and seamless communication.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3 class="feature-title">Secure & Reliable</h3>
                        <p class="feature-description">
                            Enterprise-grade security, regular backups, and 99.9% uptime to keep your business running.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="py-5 bg-light" id="pricing">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-5 fw-bold text-dark mb-3">Simple, Transparent Pricing</h2>
                    <p class="lead text-muted">Start with a free trial, then choose the plan that fits your business</p>
                </div>
            </div>
            
            <div class="row justify-content-center">
                @foreach($subscriptionPlans as $planKey => $plan)
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 position-relative">
                        @if($plan['popular'])
                        <div class="position-absolute top-0 start-50 translate-middle">
                            <span class="badge bg-primary px-3 py-2">Most Popular</span>
                        </div>
                        @endif
                        <div class="card-body text-center p-4">
                            <h3 class="card-title text-primary">{{ $plan['name'] }}</h3>
                            <div class="display-4 fw-bold text-dark mb-3">
                                @if($plan['price'] === null)
                                    Custom
                                @elseif($plan['price'] === 0)
                                    Free
                                @else
                                    £{{ $plan['price'] }}<small class="fs-6 text-muted">/{{ $plan['period'] }}</small>
                                @endif
                            </div>
                            <p class="text-muted mb-4">{{ $plan['description'] }}</p>
                            <ul class="list-unstyled mb-4">
                                @foreach($plan['features'] as $feature)
                                <li class="mb-2"><i class="bi bi-check text-success me-2"></i>{{ $feature }}</li>
                                @endforeach
                            </ul>
                            @if($planKey === 'enterprise')
                                <a href="mailto:sales@gobillo.com" class="btn {{ $plan['button_class'] }}">{{ $plan['button_text'] }}</a>
                            @else
                                <a href="{{ route('get-started') }}" class="btn {{ $plan['button_class'] }}">{{ $plan['button_text'] }}</a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-5" style="background: #2c3e50; color: white;">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-building me-2"></i>GoBillo
                    </h5>
                    <p class="text-light opacity-75 mb-3">
                        {{ \App\Models\SiteContent::get('footer_company_description', 'The complete construction management platform trusted by thousands of construction professionals worldwide.') }}
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light opacity-75 hover-opacity-100">
                            <i class="bi bi-facebook fs-5"></i>
                        </a>
                        <a href="#" class="text-light opacity-75 hover-opacity-100">
                            <i class="bi bi-twitter fs-5"></i>
                        </a>
                        <a href="#" class="text-light opacity-75 hover-opacity-100">
                            <i class="bi bi-linkedin fs-5"></i>
                        </a>
                        <a href="#" class="text-light opacity-75 hover-opacity-100">
                            <i class="bi bi-instagram fs-5"></i>
                        </a>
                    </div>
                </div>
                
                @php
                    $footerLinks = \App\Models\FooterLink::getGroupedLinks();
                    $sections = \App\Models\FooterLink::getSections();
                @endphp
                
                @foreach($sections as $sectionKey => $sectionName)
                    <div class="col-lg-2 col-md-6 mb-4">
                        <h6 class="fw-semibold mb-3">{{ $sectionName }}</h6>
                        <ul class="list-unstyled">
                            @if(isset($footerLinks[$sectionKey]))
                                @foreach($footerLinks[$sectionKey] as $link)
                                    <li class="mb-2">
                                        <a href="{{ $link->url }}" 
                                           target="{{ $link->target }}" 
                                           class="text-light opacity-75 text-decoration-none hover-opacity-100">
                                            {{ $link->title }}
                                            @if($link->target === '_blank')
                                                <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                <li class="mb-2">
                                    <span class="text-light opacity-50">No links</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endforeach
            </div>
            
            <hr class="my-4 opacity-25">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-light opacity-75 mb-0">
                        © {{ date('Y') }} {{ \App\Models\SiteContent::get('footer_copyright', 'GoBillo. All rights reserved.') }}
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-light opacity-75 mb-0">
                        {{ \App\Models\SiteContent::get('footer_tagline', 'Made with ❤️ for construction professionals') }}
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form submission with loading state
        document.getElementById('registrationForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.classList.add('btn-loading');
            submitBtn.disabled = true;
        });

        // Animate elements on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all feature cards
        document.querySelectorAll('.feature-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });

        // Password strength indicator (optional enhancement)
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = getPasswordStrength(password);
            // You can add visual feedback here
        });

        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword && confirmPassword.length > 0) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });

        function getPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            return strength;
        }

        // Auto-format phone number
        document.getElementById('phone').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 6) {
                value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
            } else if (value.length >= 3) {
                value = value.replace(/(\d{3})(\d{3})/, '($1) $2');
            }
            this.value = value;
        });
    </script>
</body>
</html>
