<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'ProMax Team') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    @stack('styles')
    
    <style>
        :root {
            --navbar-height: 60px;
            --sidebar-width: 260px;
            --content-gutter: 24px; /* extra spacing between sidebar and page */
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f8fafc;
            padding-top: var(--navbar-height);
            padding-left: var(--content-gutter);
            padding-right: var(--content-gutter);
        }

        .navbar {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1001;
            height: var(--navbar-height);
            padding: 0 1rem;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #1f2937;
        }

        .sidebar {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            position: fixed;
            top: var(--navbar-height);
            left: 0;
            width: var(--sidebar-width);
            height: calc(100vh - var(--navbar-height));
            z-index: 1000;
            overflow: hidden;
            padding: 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        /* Custom Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #1e293b;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }

        .sidebar .user-info {
            padding: 1.5rem;
            background: rgba(255,255,255,0.05);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 0.5rem;
        }

        .sidebar .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: 0.75rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .sidebar .user-name {
            color: #ffffff;
            font-weight: 600;
            margin: 0;
            font-size: 0.95rem;
        }

        .sidebar .user-role {
            color: #94a3b8;
            font-size: 0.8rem;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .sidebar .search-box {
            padding: 0 1rem 1rem;
        }

        .sidebar .search-box input {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: #ffffff;
            padding: 0.6rem 1rem 0.6rem 2.5rem;
            border-radius: 10px;
            width: 100%;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .sidebar .search-box input:focus {
            background: rgba(255,255,255,0.08);
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }

        .sidebar .search-box input::placeholder {
            color: #64748b;
        }

        .sidebar .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
        }

        /* Main Navigation Items */
        .sidebar-nav {
            padding: 0.5rem 0;
        }

        .nav-main-item {
            margin-bottom: 0.25rem;
        }

        .nav-main-link {
            color: #cbd5e1;
            padding: 0.875rem 1.5rem;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
            font-size: 0.95rem;
            font-weight: 500;
            position: relative;
        }

        .nav-main-link:hover {
            background: rgba(255,255,255,0.05);
            color: #ffffff;
            padding-left: 1.75rem;
        }

        .nav-main-link.active {
            background: linear-gradient(90deg, rgba(102,126,234,0.15) 0%, transparent 100%);
            color: #ffffff;
            border-left: 3px solid #667eea;
        }

        .nav-main-link i {
            margin-right: 0.875rem;
            width: 20px;
            font-size: 1.1rem;
        }

        .nav-main-link .badge {
            margin-left: auto;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            background: rgba(102,126,234,0.2);
            color: #a5b4fc;
        }

        /* Accordion Styling */
        .sidebar-accordion {
            background: transparent;
        }

        .sidebar-accordion .accordion-item {
            background: transparent;
            border: none;
            margin-bottom: 0.25rem;
        }

        .sidebar-accordion .accordion-button {
            background: transparent;
            color: #cbd5e1;
            padding: 0.875rem 1.5rem;
            font-size: 0.95rem;
            font-weight: 500;
            border: none;
            box-shadow: none;
            transition: all 0.3s;
        }

        .sidebar-accordion .accordion-button:not(.collapsed) {
            background: rgba(255,255,255,0.05);
            color: #ffffff;
            border-left: 3px solid #667eea;
            box-shadow: none;
        }

        .sidebar-accordion .accordion-button:hover {
            background: rgba(255,255,255,0.05);
            color: #ffffff;
        }

        .sidebar-accordion .accordion-button::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23cbd5e1'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
            width: 1rem;
            height: 1rem;
        }

        .sidebar-accordion .accordion-button:not(.collapsed)::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ffffff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        }

        .sidebar-accordion .accordion-button i {
            margin-right: 0.875rem;
            width: 20px;
            font-size: 1.1rem;
        }

        .sidebar-accordion .accordion-body {
            background: rgba(0,0,0,0.2);
            padding: 0.25rem 0;
        }

        .sidebar-accordion .sub-nav-link {
            color: #94a3b8;
            padding: 0.625rem 1.5rem 0.625rem 3.5rem;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 0.9rem;
            position: relative;
        }

        .sidebar-accordion .sub-nav-link:before {
            content: '';
            position: absolute;
            left: 2.25rem;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 4px;
            background: #64748b;
            border-radius: 50%;
        }

        .sidebar-accordion .sub-nav-link:hover {
            background: rgba(255,255,255,0.05);
            color: #ffffff;
            padding-left: 3.75rem;
        }

        .sidebar-accordion .sub-nav-link.active {
            background: rgba(102,126,234,0.1);
            color: #a5b4fc;
        }

        .sidebar-accordion .sub-nav-link.active:before {
            background: #667eea;
            width: 6px;
            height: 6px;
        }

        .sidebar-accordion .sub-nav-link i {
            margin-right: 0.75rem;
            width: 16px;
            font-size: 0.9rem;
        }

        /* Sidebar Footer User Menu */
        .sidebar-footer {
            flex-shrink: 0;
            background: rgba(0,0,0,0.2);
            border-top: 1px solid rgba(255,255,255,0.1) !important;
        }

        .sidebar-footer .btn-outline-light {
            border-color: rgba(255,255,255,0.2);
            color: #ffffff;
            background: transparent;
        }

        .sidebar-footer .btn-outline-light:hover {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,255,255,0.3);
            color: #ffffff;
        }

        .sidebar-footer .avatar-sm {
            width: 32px;
            height: 32px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .sidebar-footer .dropdown-menu {
            background: #ffffff;
            border: 1px solid rgba(0,0,0,0.15);
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }

        .sidebar-footer .dropdown-header {
            font-size: 0.875rem;
            font-weight: 600;
            color: #6c757d;
        }

        /* Offset all page content when sidebar is present */
        body.has-sidebar {
            padding-left: calc(var(--sidebar-width) + var(--content-gutter));
        }

        .main-content {
            padding: 2rem;
            min-height: calc(100vh - var(--navbar-height));
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            /* Remove left padding when sidebar is hidden on small screens */
            body.has-sidebar {
                padding-left: var(--content-gutter);
            }

            .main-content { padding: 1.5rem; }
        }

        /* Custom Tab Styling for Project Pages */
        .card-header-tabs {
            margin-bottom: 0;
            border-bottom: 1px solid #dee2e6;
        }

        .card-header-tabs .nav-link {
            padding: 0.75rem 1rem;
            min-height: 44px;
            border: 1px solid transparent;
            border-bottom: none;
            margin-bottom: -1px;
            background-color: transparent;
            border-radius: 0.375rem 0.375rem 0 0;
            color: #6c757d;
        }

        .card-header-tabs .nav-link:hover:not(.active) {
            border-color: #e9ecef #e9ecef #dee2e6;
            background-color: #f8f9fa;
            color: #495057;
        }

        .card-header-tabs .nav-link.active {
            color: #0d6efd !important;
            background-color: #ffffff !important;
            border-color: #0d6efd #0d6efd #ffffff !important;
            border-bottom: 1px solid #ffffff !important;
            margin-bottom: -1px;
            position: relative;
            z-index: 1;
            font-weight: 600;
        }

        .card-header-tabs .nav-link .badge-sm {
            font-size: 0.75rem;
            padding: 0.25rem 0.4rem;
            line-height: 1;
            vertical-align: middle;
        }

        .card-header-tabs .nav-link.d-flex {
            align-items: center !important;
        }

        /* Ensure proper card structure for tabs */
        .card-header.p-0 {
            background-color: transparent !important;
            border-bottom: none !important;
            padding: 0 !important;
        }

        .card .card-body {
            border-top: 1px solid #dee2e6;
            position: relative;
            z-index: 0;
        }
    </style>
</head>
<body class="@auth has-sidebar @endauth">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-building"></i> ProMax Team
            </a>

            <div class="d-flex align-items-center ms-auto">
                @auth
                    <!-- Notification icons or other top-right elements can go here -->
                @endauth
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    @auth
    <div class="sidebar d-flex flex-column">
        <!-- User Info -->
        <div class="user-info">
            <div class="d-flex align-items-center">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div>
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</div>
                </div>
            </div>
        </div>

        <!-- Search Box -->
        <div class="search-box position-relative">
            <i class="bi bi-search search-icon"></i>
            <input type="text" class="form-control" placeholder="Search menu...">
        </div>

        <!-- Professional Navigation Menu -->
        <nav class="sidebar-nav flex-grow-1 overflow-auto">
            @if(auth()->user()->isOperative())
                <!-- Operative Dashboard - Main Tab -->
                <div class="nav-main-item">
                    <a class="nav-main-link {{ request()->routeIs('operative-dashboard') ? 'active' : '' }}" href="{{ route('operative-dashboard') }}">
                        <i class="bi bi-kanban"></i> 
                        <span>Dashboard</span>
                    </a>
                </div>
            @else
                <!-- Admin Dashboard - Main Tab -->
                <div class="nav-main-item">
                    <a class="nav-main-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i> 
                        <span>Dashboard</span>
                    </a>
                </div>
            @endif

            @if(!auth()->user()->isOperative())
                <!-- Operational - Accordion -->
                <div class="accordion sidebar-accordion" id="operationalAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ request()->routeIs(['sites.*', 'manager.sites.*', 'task-categories.*', 'documents.*', 'team.*', 'employees.*', 'assets.*', 'health-safety.*', 'hire.*', 'tool-hire.*']) ? '' : 'collapsed' }}" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#operationalCollapse" 
                                    aria-expanded="{{ request()->routeIs(['sites.*', 'manager.sites.*', 'task-categories.*', 'documents.*', 'team.*', 'employees.*', 'assets.*', 'health-safety.*', 'hire.*', 'tool-hire.*']) ? 'true' : 'false' }}">
                                <i class="bi bi-gear-wide-connected"></i>
                                <span>Operational</span>
                            </button>
                        </h2>
                        <div id="operationalCollapse" 
                             class="accordion-collapse collapse {{ request()->routeIs(['sites.*', 'manager.sites.*', 'task-categories.*', 'documents.*', 'team.*', 'employees.*', 'assets.*', 'health-safety.*', 'hire.*', 'tool-hire.*']) ? 'show' : '' }}" 
                             data-bs-parent="#operationalAccordion">
                            <div class="accordion-body">
                                @if(auth()->user()->canViewProjects())
                                    <a class="sub-nav-link {{ request()->routeIs('sites.*') ? 'active' : '' }}" href="{{ route('sites.index') }}">
                                        <i class="bi bi-geo-alt"></i> Sites
                                    </a>
                                @elseif(in_array(auth()->user()->role, ['site_manager', 'project_manager']))
                                    <a class="sub-nav-link {{ request()->routeIs('manager.sites.*', 'sites.*') ? 'active' : '' }}" href="{{ route('manager.sites.index') }}">
                                        <i class="bi bi-geo-alt"></i> My Sites
                                    </a>
                                @endif
                                @if(auth()->user()->canManageTasks())
                                    <a class="sub-nav-link {{ request()->routeIs('task-categories.*') ? 'active' : '' }}" href="{{ route('task-categories.index') }}">
                                        <i class="bi bi-tags"></i> Task Categories
                                    </a>
                                @endif
                                @if(auth()->user()->canManageDocuments())
                                    <a class="sub-nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}" href="{{ route('documents.index') }}">
                                        <i class="bi bi-file-earmark-text"></i> Documents
                                    </a>
                                @endif
                                @if(auth()->user()->canManageUsers())
                                    <a class="sub-nav-link {{ request()->routeIs('team.*') ? 'active' : '' }}" href="{{ route('team.index') }}">
                                        <i class="bi bi-person-badge"></i> Operatives
                                    </a>
                                @endif
                                @if(auth()->user()->canManageUsers())
                                    <a class="sub-nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                                        <i class="bi bi-person-workspace"></i> Employees
                                    </a>
                                @endif
                                @if(auth()->user()->canManageProjects())
                                    <a class="sub-nav-link {{ request()->routeIs('hire.*') ? 'active' : '' }}" href="{{ route('hire.index') }}">
                                        <i class="bi bi-person-plus"></i> Hire
                                    </a>
                                @endif
                                @if(auth()->user()->canManageProjects())
                                    <a class="sub-nav-link {{ request()->routeIs('tool-hire.*') ? 'active' : '' }}" href="{{ route('tool-hire.index') }}">
                                        <i class="bi bi-tools"></i> Tools Hire
                                    </a>
                                @endif
                                @if(auth()->user()->canViewProjects())
                                    <a class="sub-nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}" href="{{ route('assets.index') }}">
                                        <i class="bi bi-box"></i> Assets
                                    </a>
                                @endif
                                <a class="sub-nav-link {{ request()->routeIs('health-safety.*') ? 'active' : '' }}" href="{{ route('health-safety.index') }}">
                                    <i class="bi bi-shield-check"></i> Health & Safety
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(!auth()->user()->isOperative())
                <!-- Commercial - Accordion -->
                <div class="accordion sidebar-accordion" id="commercialAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ request()->routeIs(['clients.*', 'invoices.*', 'estimates.*', 'expenses.*', 'admin.operative-invoices.*']) ? '' : 'collapsed' }}" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#commercialCollapse" 
                                    aria-expanded="{{ request()->routeIs(['clients.*', 'invoices.*', 'estimates.*', 'expenses.*', 'admin.operative-invoices.*']) ? 'true' : 'false' }}">
                                <i class="bi bi-currency-pound"></i>
                                <span>Commercial</span>
                            </button>
                        </h2>
                        <div id="commercialCollapse" 
                             class="accordion-collapse collapse {{ request()->routeIs(['clients.*', 'invoices.*', 'estimates.*', 'expenses.*', 'admin.operative-invoices.*']) ? 'show' : '' }}" 
                             data-bs-parent="#commercialAccordion">
                            <div class="accordion-body">
                                @if(auth()->user()->canManageClients())
                                    <a class="sub-nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}" href="{{ route('clients.index') }}">
                                        <i class="bi bi-people"></i> Clients
                                    </a>
                                @endif
                                <a class="sub-nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}" href="{{ route('invoices.index') }}">
                                    <i class="bi bi-receipt"></i> Invoicing
                                </a>
                                @if(auth()->user()->role === 'company_admin')
                                    <a class="sub-nav-link {{ request()->routeIs('admin.operative-invoices.*') ? 'active' : '' }}" href="{{ route('admin.operative-invoices.index') }}">
                                        <i class="bi bi-person-check"></i> Operative Invoices
                                    </a>
                                @endif
                                <a class="sub-nav-link {{ request()->routeIs('estimates.*') ? 'active' : '' }}" href="{{ route('estimates.index') }}">
                                    <i class="bi bi-calculator"></i> Estimates
                                </a>
                                <a class="sub-nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}">
                                    <i class="bi bi-credit-card"></i> Expenses
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(auth()->user()->canViewFinancials())
                <!-- Financial - Accordion -->
                <div class="accordion sidebar-accordion" id="financialAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ request()->routeIs(['cis.*', 'financial-reports.*', 'payments.*']) ? '' : 'collapsed' }}" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#financialCollapse" 
                                    aria-expanded="{{ request()->routeIs(['cis.*', 'financial-reports.*', 'payments.*']) ? 'true' : 'false' }}">
                                <i class="bi bi-graph-up-arrow"></i>
                                <span>Financial</span>
                            </button>
                        </h2>
                        <div id="financialCollapse" 
                             class="accordion-collapse collapse {{ request()->routeIs(['cis.*', 'financial-reports.*', 'payments.*']) ? 'show' : '' }}" 
                             data-bs-parent="#financialAccordion">
                            <div class="accordion-body">
                                <a class="sub-nav-link {{ request()->routeIs('cis.*') ? 'active' : '' }}" href="{{ route('cis.index') }}">
                                    <i class="bi bi-percent"></i> CIS Management
                                </a>
                                <a class="sub-nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}" href="{{ route('payments.index') }}">
                                    <i class="bi bi-credit-card-2-front"></i> Payments
                                </a>
                                <a class="sub-nav-link {{ request()->routeIs('financial-reports.*') ? 'active' : '' }}" href="{{ route('financial-reports.index') }}">
                                    <i class="bi bi-bar-chart-line"></i> Reports
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(auth()->user()->canManageProjects())
                <!-- Management - Accordion -->
                <div class="accordion sidebar-accordion" id="managementAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ request()->routeIs(['project-schedules.*', 'resources.*', 'time-entries.*', 'admin.operative-data-forms.*']) ? '' : 'collapsed' }}" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#managementCollapse" 
                                    aria-expanded="{{ request()->routeIs(['project-schedules.*', 'resources.*', 'time-entries.*', 'admin.operative-data-forms.*']) ? 'true' : 'false' }}">
                                <i class="bi bi-clipboard-data"></i>
                                <span>Management</span>
                            </button>
                        </h2>
                        <div id="managementCollapse" 
                             class="accordion-collapse collapse {{ request()->routeIs(['project-schedules.*', 'resources.*', 'time-entries.*', 'admin.operative-data-forms.*']) ? 'show' : '' }}" 
                             data-bs-parent="#managementAccordion">
                            <div class="accordion-body">
                                <a class="sub-nav-link {{ request()->routeIs('project-schedules.*') ? 'active' : '' }}" href="{{ route('project-schedules.index') }}">
                                    <i class="bi bi-calendar-event"></i> Project Schedule
                                </a>
                                <a class="sub-nav-link {{ request()->routeIs('resources.*') ? 'active' : '' }}" href="{{ route('resources.index') }}">
                                    <i class="bi bi-tools"></i> Field Operations
                                </a>
                                <a class="sub-nav-link {{ request()->routeIs('time-entries.*') ? 'active' : '' }}" href="{{ route('time-entries.index') }}">
                                    <i class="bi bi-clock"></i> Time Tracking
                                </a>
                                <a class="sub-nav-link {{ request()->routeIs('admin.operative-data-forms.*') ? 'active' : '' }}" href="{{ route('admin.operative-data-forms.index') }}">
                                    <i class="bi bi-file-earmark-person"></i> OP Data Forms
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Messages - Main Tab -->
            <div class="nav-main-item">
                <a class="nav-main-link {{ request()->routeIs('messages.*') ? 'active' : '' }}" href="{{ route('messages.index') }}">
                    <i class="bi bi-chat-dots"></i> 
                    <span>Messages</span>
                    @if(isset($unreadMessages) && $unreadMessages > 0)
                        <span class="badge">{{ $unreadMessages }}</span>
                    @endif
                </a>
            </div>

            <!-- Membership -->
            @if(!auth()->user()->isOperative() && auth()->user()->company_id)
                <div class="nav-main-item">
                    <a class="nav-main-link {{ request()->routeIs('membership.*') ? 'active' : '' }}" href="{{ route('membership.index') }}">
                        <i class="bi bi-star"></i> 
                        <span>Membership</span>
                    </a>
                </div>
            @endif

            <!-- Settings - Main Tab -->
            @if(!auth()->user()->isOperative() && auth()->user()->company_id)
                <div class="nav-main-item">
                    <a class="nav-main-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                        <i class="bi bi-gear"></i> 
                        <span>Company Settings</span>
                    </a>
                </div>

                <!-- Email Settings moved to Settings tab -->
            @endif
        </nav>

        <!-- User Menu at Bottom -->
        <div class="sidebar-footer mt-auto p-3 border-top">
            <div class="dropdown dropup">
                <button class="btn btn-outline-light dropdown-toggle w-100 d-flex align-items-center justify-content-between" 
                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="text-start">
                            <div class="small fw-bold text-white">{{ Auth::user()->name }}</div>
                            <small class="text-light">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</small>
                        </div>
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end w-100">
                    <li><h6 class="dropdown-header">Account Options</h6></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.show') }}">
                            <i class="bi bi-person me-2"></i>My Profile
                        </a>
                    </li>
                    @if(!auth()->user()->isOperative() && auth()->user()->company_id)
                        <li>
                            <a class="dropdown-item" href="{{ route('settings.index') }}">
                                <i class="bi bi-gear me-2"></i>Company Settings
                            </a>
                        </li>
                    @endif
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
    @endauth

    <!-- Main Content -->
    <main class="main-content">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>