<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SuperAdmin') - {{ config('app.name', 'GoBillo') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">

    @stack('styles')
</head>
<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar bg-dark text-white" id="sidebar">
            <!-- Brand -->
            <div class="sidebar-brand p-3 border-bottom border-secondary">
                <a href="{{ route('dashboard') }}" class="text-white text-decoration-none d-flex align-items-center">
                    <i class="bi bi-shield-check text-danger me-2 fs-4"></i>
                    <div>
                        <div class="fw-bold">GoBillo</div>
                        <small class="text-danger">SuperAdmin</small>
                    </div>
                </a>
            </div>

            <!-- Navigation Menu -->
            <nav class="sidebar-nav p-3">
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('superadmin.companies.*') ? 'active' : '' }}" href="{{ route('superadmin.companies.index') }}">
                            <i class="bi bi-buildings me-2"></i>Companies
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('superadmin.subscriptions.*') ? 'active' : '' }}" href="{{ route('superadmin.subscriptions.index') }}">
                            <i class="bi bi-credit-card me-2"></i>Subscriptions
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('superadmin.plans.*') ? 'active' : '' }}" href="{{ route('superadmin.plans.index') }}">
                            <i class="bi bi-layers me-2"></i>Membership Plans
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('superadmin.settings.*') ? 'active' : '' }}" href="{{ route('superadmin.settings.index') }}">
                            <i class="bi bi-gear me-2"></i>Settings
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('superadmin.site-content.*') ? 'active' : '' }}" href="{{ route('superadmin.site-content.index') }}">
                            <i class="bi bi-file-text me-2"></i>Site Content
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('superadmin.footer-links.*') ? 'active' : '' }}" href="{{ route('superadmin.footer-links.index') }}">
                            <i class="bi bi-link-45deg me-2"></i>Footer Links
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('superadmin.pages.*') ? 'active' : '' }}" href="{{ route('superadmin.pages.index') }}">
                            <i class="bi bi-file-earmark-text me-2"></i>Pages
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('superadmin.analytics.*') ? 'active' : '' }}" href="{{ route('superadmin.analytics.index') }}">
                            <i class="bi bi-graph-up me-2"></i>Analytics
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- User Menu at Bottom -->
            <div class="sidebar-footer mt-auto p-3 border-top border-secondary">
                <div class="dropdown dropup">
                    <button class="btn btn-outline-light dropdown-toggle w-100 d-flex align-items-center justify-content-between" type="button" data-bs-toggle="dropdown">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="text-start">
                                <div class="small fw-bold">{{ auth()->user()->name }}</div>
                                <small class="text-danger">SuperAdmin</small>
                            </div>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end w-100">
                        <li><h6 class="dropdown-header">Account Options</h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('superadmin.settings.index') }}">
                                <i class="bi bi-gear me-2"></i>Settings
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('superadmin.analytics.index') }}">
                                <i class="bi bi-graph-up me-2"></i>Analytics
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content flex-grow-1">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-bottom p-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-secondary me-3 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <h4 class="mb-0 text-dark">@yield('page-title', 'SuperAdmin Dashboard')</h4>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-danger me-2">SuperAdmin</span>
                    <small class="text-muted">{{ now()->format('l, F j, Y') }}</small>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-4">
                <!-- Alerts -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar Offcanvas -->
    <div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="mobileSidebar">
        <div class="offcanvas-header border-bottom border-secondary">
            <div class="d-flex align-items-center">
                <i class="bi bi-shield-check text-danger me-2 fs-4"></i>
                <div>
                    <div class="fw-bold">GoBillo</div>
                    <small class="text-danger">SuperAdmin</small>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <nav>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('superadmin.companies.*') ? 'active' : '' }}" href="{{ route('superadmin.companies.index') }}">
                            <i class="bi bi-buildings me-2"></i>Companies
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('superadmin.subscriptions.*') ? 'active' : '' }}" href="{{ route('superadmin.subscriptions.index') }}">
                            <i class="bi bi-credit-card me-2"></i>Subscriptions
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('superadmin.plans.*') ? 'active' : '' }}" href="{{ route('superadmin.plans.index') }}">
                            <i class="bi bi-layers me-2"></i>Membership Plans
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('superadmin.settings.*') ? 'active' : '' }}" href="{{ route('superadmin.settings.index') }}">
                            <i class="bi bi-gear me-2"></i>Settings
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('superadmin.site-content.*') ? 'active' : '' }}" href="{{ route('superadmin.site-content.index') }}">
                            <i class="bi bi-file-text me-2"></i>Site Content
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('superadmin.footer-links.*') ? 'active' : '' }}" href="{{ route('superadmin.footer-links.index') }}">
                            <i class="bi bi-link-45deg me-2"></i>Footer Links
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('superadmin.pages.*') ? 'active' : '' }}" href="{{ route('superadmin.pages.index') }}">
                            <i class="bi bi-file-earmark-text me-2"></i>Pages
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a class="nav-link {{ request()->routeIs('superadmin.analytics.*') ? 'active' : '' }}" href="{{ route('superadmin.analytics.index') }}">
                            <i class="bi bi-graph-up me-2"></i>Analytics
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')

    <style>
    /* Sidebar Styles */
    .sidebar {
        width: 280px;
        min-height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .sidebar-brand {
        flex-shrink: 0;
    }

    .sidebar-nav {
        flex-grow: 1;
        overflow-y: auto;
    }

    .sidebar-footer {
        flex-shrink: 0;
    }

    /* Main Content */
    .main-content {
        margin-left: 280px;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Navigation Links */
    .nav-link {
        color: rgba(255, 255, 255, 0.8);
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
    }

    .nav-link:hover {
        color: white;
        background-color: rgba(255, 255, 255, 0.1);
    }

    .nav-link.active {
        color: white;
        background-color: rgba(220, 53, 69, 0.2);
        border-left: 3px solid #dc3545;
        font-weight: 600;
    }

    /* Avatar */
    .avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    /* Header */
    header {
        flex-shrink: 0;
    }

    /* Main content area */
    main {
        flex-grow: 1;
        background-color: #f8f9fa;
    }

    /* Alerts */
    .alert {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    /* Mobile Responsive */
    @media (max-width: 991.98px) {
        .sidebar {
            margin-left: -280px;
        }
        
        .main-content {
            margin-left: 0;
        }
    }

    /* Dropdown customization */
    .dropdown-header {
        font-size: 0.875rem;
        font-weight: 600;
        color: #6c757d;
    }

    /* Scrollbar styling for sidebar */
    .sidebar-nav::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar-nav::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
    }

    .sidebar-nav::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 2px;
    }

    .sidebar-nav::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }
    </style>
</body>
</html>
