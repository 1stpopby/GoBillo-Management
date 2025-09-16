<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'GoBillo') }} - Professional Construction Management</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --secondary-color: #64748b;
            --accent-color: #10b981;
            --dark-color: #1f2937;
            --light-bg: #f8fafc;
            --white: #ffffff;
            --border-color: #e5e7eb;
            --text-muted: #6b7280;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: var(--dark-color);
        }

        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            color: var(--dark-color);
            margin: 0 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            background: var(--light-bg);
            color: var(--primary-color);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: var(--hover-shadow);
        }

        /* Hero Section */
        .hero {
            background: var(--gradient-primary);
            color: white;
            padding: 8rem 0 6rem;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='7' cy='7' r='7'/%3E%3Ccircle cx='53' cy='7' r='7'/%3E%3Ccircle cx='7' cy='53' r='7'/%3E%3Ccircle cx='53' cy='53' r='7'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.5;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero .lead {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .hero-buttons {
            margin-top: 2rem;
        }

        .hero-buttons .btn {
            margin: 0.5rem;
            font-size: 1.1rem;
            padding: 0.75rem 2rem;
        }

        .btn-white {
            background: white;
            color: var(--primary-color);
            border: none;
            font-weight: 600;
        }

        .btn-white:hover {
            background: var(--light-bg);
            color: var(--primary-hover);
        }

        /* Features Section */
        .features {
            padding: 6rem 0;
            background: var(--light-bg);
        }

        .feature-card {
            background: white;
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            height: 100%;
            border: none;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--hover-shadow);
        }

        .feature-icon {
            width: 4rem;
            height: 4rem;
            background: var(--gradient-primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            color: white;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }

        .feature-card p {
            color: var(--text-muted);
            line-height: 1.7;
        }

        /* Stats Section */
        .stats {
            background: var(--dark-color);
            color: white;
            padding: 4rem 0;
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            color: var(--accent-color);
            display: block;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.8;
            margin-top: 0.5rem;
        }

        /* CTA Section */
        .cta {
            background: var(--gradient-secondary);
            color: white;
            padding: 6rem 0;
            text-align: center;
        }

        .cta h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .cta p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        /* Footer */
        .footer {
            background: var(--dark-color);
            color: white;
            padding: 3rem 0 1rem;
        }

        .footer h5 {
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--accent-color);
        }

        .footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: var(--accent-color);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1rem;
            margin-top: 2rem;
            text-align: center;
            opacity: 0.8;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .hero .lead {
                font-size: 1.1rem;
            }

            .hero-buttons .btn {
                display: block;
                width: 100%;
                margin: 0.5rem 0;
            }

            .feature-card {
                margin-bottom: 2rem;
            }

            .stat-number {
                font-size: 2.5rem;
            }

            .cta h2 {
                font-size: 2rem;
            }
        }

        /* Animations */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: var(--card-shadow);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-buildings me-2"></i>GoBillo
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
                
                <div class="navbar-nav">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-outline-primary me-2">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Login</a>
                        @if (Route::has('get-started'))
                            <a href="{{ route('get-started') }}" class="btn btn-primary">Get Started</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content fade-in">
                        <h1>{{ \App\Models\SiteContent::get('landing_hero_title', 'Professional Construction Management Made Simple') }}</h1>
                        <p class="lead">
                            {{ \App\Models\SiteContent::get('landing_hero_subtitle', 'Streamline your construction projects with our comprehensive management platform. From project planning to team collaboration, we\'ve got you covered.') }}
                        </p>
                        <div class="hero-buttons">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn btn-white btn-lg">
                                    <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
                                </a>
                            @else
                                <a href="{{ route('get-started') }}" class="btn btn-white btn-lg">
                                    <i class="bi bi-rocket-takeoff me-2"></i>{{ \App\Models\SiteContent::get('landing_hero_cta_text', 'Start Free Trial') }}
                                </a>
                                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image fade-in">
                        <div style="background: rgba(255,255,255,0.1); border-radius: 20px; padding: 2rem; backdrop-filter: blur(10px);">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div style="background: rgba(255,255,255,0.2); padding: 1.5rem; border-radius: 12px; text-align: center;">
                                    <i class="bi bi-graph-up" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                                    <div style="font-weight: 600;">Project Analytics</div>
                                </div>
                                <div style="background: rgba(255,255,255,0.2); padding: 1.5rem; border-radius: 12px; text-align: center;">
                                    <i class="bi bi-people" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                                    <div style="font-weight: 600;">Team Management</div>
                                </div>
                                <div style="background: rgba(255,255,255,0.2); padding: 1.5rem; border-radius: 12px; text-align: center;">
                                    <i class="bi bi-shield-check" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                                    <div style="font-weight: 600;">Safety Compliance</div>
                                </div>
                                <div style="background: rgba(255,255,255,0.2); padding: 1.5rem; border-radius: 12px; text-align: center;">
                                    <i class="bi bi-currency-dollar" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                                    <div style="font-weight: 600;">Financial Tracking</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center mb-5">
                    <h2 class="display-4 fw-bold mb-3">Everything You Need</h2>
                    <p class="lead text-muted">Comprehensive tools for modern construction management</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card fade-in">
                        <div class="feature-icon">
                            <i class="bi bi-kanban"></i>
                        </div>
                        <h3>Project Management</h3>
                        <p>Plan, track, and manage your construction projects from start to finish with intuitive project boards and timeline views.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card fade-in">
                        <div class="feature-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h3>Team Collaboration</h3>
                        <p>Keep your entire team connected with real-time communication, task assignments, and progress updates.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card fade-in">
                        <div class="feature-icon">
                            <i class="bi bi-calculator"></i>
                        </div>
                        <h3>Financial Tracking</h3>
                        <p>Monitor budgets, track expenses, and generate detailed financial reports to keep your projects profitable.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card fade-in">
                        <div class="feature-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <h3>Site Management</h3>
                        <p>Manage multiple construction sites with location-based project organization and resource allocation.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card fade-in">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3>Health & Safety</h3>
                        <p>Ensure compliance with safety regulations through digital forms, incident reporting, and safety training management.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card fade-in">
                        <div class="feature-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h3>Analytics & Reports</h3>
                        <p>Make data-driven decisions with comprehensive analytics and customizable reporting tools.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item fade-in">
                        <span class="stat-number">500+</span>
                        <div class="stat-label">Projects Completed</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item fade-in">
                        <span class="stat-number">50+</span>
                        <div class="stat-label">Active Companies</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item fade-in">
                        <span class="stat-number">1000+</span>
                        <div class="stat-label">Team Members</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item fade-in">
                        <span class="stat-number">99.9%</span>
                        <div class="stat-label">Uptime</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h2>Ready to Transform Your Construction Management?</h2>
                    <p>Join hundreds of construction companies who trust GoBillo to manage their projects efficiently.</p>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-white btn-lg">
                            <i class="bi bi-speedometer2 me-2"></i>Access Dashboard
                        </a>
                    @else
                        <a href="{{ route('get-started') }}" class="btn btn-white btn-lg">
                            <i class="bi bi-rocket-takeoff me-2"></i>Start Your Free Trial
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5><i class="bi bi-buildings me-2"></i>GoBillo</h5>
                    <p>Professional construction management platform designed to streamline your projects and boost productivity.</p>
                    <div class="social-links">
                        <a href="#" class="me-3"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="me-3"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="me-3"><i class="bi bi-linkedin"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Product</h5>
                    <ul class="list-unstyled">
                        <li><a href="#features">Features</a></li>
                        <li><a href="#">Pricing</a></li>
                        <li><a href="#">Security</a></li>
                        <li><a href="#">Integrations</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Company</h5>
                    <ul class="list-unstyled">
                        <li><a href="#about">About</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Press</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Support</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">Status</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Legal</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                        <li><a href="#">GDPR</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} GoBillo. All rights reserved. Built with ❤️ for construction professionals.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });

        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Counter animation
        const animateCounters = () => {
            const counters = document.querySelectorAll('.stat-number');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent.replace(/\D/g, ''));
                const suffix = counter.textContent.replace(/[\d,]/g, '');
                let current = 0;
                const increment = target / 100;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    counter.textContent = Math.floor(current).toLocaleString() + suffix;
                }, 20);
            });
        };

        // Trigger counter animation when stats section is visible
        const statsSection = document.querySelector('.stats');
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        statsObserver.observe(statsSection);
    </script>
</body>
</html>
