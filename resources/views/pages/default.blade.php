<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $page->meta_title ?: $page->title }} - {{ config('app.name', 'GoBillo') }}</title>
    
    @if($page->meta_description)
        <meta name="description" content="{{ $page->meta_description }}">
    @endif
    
    @if($page->meta_keywords)
        <meta name="keywords" content="{{ $page->meta_keywords }}">
    @endif
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #2c3e50;
        }
        
        .navbar {
            background: white !important;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0066cc !important;
        }

        .navbar-nav .nav-link {
            color: #2c3e50 !important;
            font-weight: 500;
            margin: 0 0.5rem;
        }

        .navbar-nav .nav-link:hover {
            color: #0066cc !important;
        }
        
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0 2rem;
            margin-top: 76px;
        }
        
        .page-content {
            padding: 3rem 0;
        }
        
        .page-content h1, .page-content h2, .page-content h3, .page-content h4, .page-content h5, .page-content h6 {
            color: #2c3e50;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        
        .page-content h1:first-child {
            margin-top: 0;
        }
        
        .page-content p {
            margin-bottom: 1.5rem;
        }
        
        .page-content ul, .page-content ol {
            margin-bottom: 1.5rem;
        }
        
        .page-content li {
            margin-bottom: 0.5rem;
        }
        
        .footer {
            background: #2c3e50;
            color: white;
            padding: 3rem 0 1rem;
            margin-top: 4rem;
        }
        
        .footer a {
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
        }
        
        .footer a:hover {
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-building me-2"></i>{{ \App\Models\SiteContent::get('site_name', 'GoBillo') }}
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
                        <a class="nav-link" href="/#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/#pricing">Pricing</a>
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

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">{{ $page->title }}</h1>
                    @if($page->excerpt)
                        <p class="lead opacity-90">{{ $page->excerpt }}</p>
                    @endif
                    <p class="small opacity-75">
                        <i class="bi bi-calendar me-1"></i>Last updated: {{ $page->updated_at->format('F j, Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="content">
                        {!! $page->content !!}
                    </div>
                    
                    <div class="mt-5 pt-4 border-top">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="/" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-left me-1"></i>Back to Home
                                </a>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p class="text-muted small mb-0">
                                    Have questions? <a href="mailto:{{ \App\Models\SiteContent::get('company_email', 'hello@gobillo.com') }}">Contact us</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-building me-2"></i>{{ \App\Models\SiteContent::get('site_name', 'GoBillo') }}
                    </h5>
                    <p class="opacity-75 mb-3">
                        {{ \App\Models\SiteContent::get('footer_company_description', 'The complete construction management platform trusted by thousands of construction professionals worldwide.') }}
                    </p>
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
                                        <a href="{{ $link->url }}" target="{{ $link->target }}" class="opacity-75">
                                            {{ $link->title }}
                                            @if($link->target === '_blank')
                                                <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                @endforeach
            </div>
            
            <hr class="my-4 opacity-25">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="opacity-75 mb-0">
                        © {{ date('Y') }} {{ \App\Models\SiteContent::get('footer_copyright', 'GoBillo. All rights reserved.') }}
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="opacity-75 mb-0">
                        {{ \App\Models\SiteContent::get('footer_tagline', 'Made with ❤️ for construction professionals') }}
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
