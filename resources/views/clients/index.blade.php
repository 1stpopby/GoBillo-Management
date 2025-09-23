@extends('layouts.app')

@section('title', 'Clients')

@section('content')
<div class="clients-container">
    <!-- Professional Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center">
                    <div class="header-icon bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                    <div>
                        <h1 class="page-title mb-1 fw-bold">Client Management</h1>
                        <p class="page-subtitle text-muted mb-0">Manage your construction clients, contacts, and business relationships</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-end">
                @if(auth()->user()->canManageClients())
                    <a href="{{ route('clients.create') }}" class="btn btn-primary btn-lg shadow-sm">
                        <i class="bi bi-plus-circle me-2"></i>Add New Client
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    @php
        $totalClients = $clients->total();
        $activeClients = \App\Models\Client::forCompany(auth()->user()->company_id)->where('is_active', true)->count();
        $totalProjects = \App\Models\Client::forCompany(auth()->user()->company_id)->withCount('projects')->get()->sum('projects_count');
        $totalValue = \App\Models\Project::whereHas('client', function($q) {
            $q->forCompany(auth()->user()->company_id);
        })->sum('budget');
    @endphp
    
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Total Clients</p>
                            <h3 class="mb-0 fw-bold">{{ $totalClients }}</h3>
                            <small class="text-muted">All companies</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                            <i class="bi bi-check-circle fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Active Clients</p>
                            <h3 class="mb-0 fw-bold text-success">{{ $activeClients }}</h3>
                            <small class="text-muted">Currently active</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 text-info rounded-circle p-3 me-3">
                            <i class="bi bi-folder2 fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Total Projects</p>
                            <h3 class="mb-0 fw-bold text-info">{{ $totalProjects }}</h3>
                            <small class="text-muted">All projects</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded-circle p-3 me-3">
                            <i class="bi bi-currency-pound fs-4"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Total Value</p>
                            <h3 class="mb-0 fw-bold text-warning">£{{ number_format($totalValue, 0) }}</h3>
                            <small class="text-muted">Project value</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light border-0 py-3">
            <div class="d-flex align-items-center">
                <i class="bi bi-funnel text-primary me-2"></i>
                <h5 class="mb-0 fw-semibold">Filter Clients</h5>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('clients.index') }}" class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label for="search" class="form-label small text-muted">
                        <i class="bi bi-search me-1"></i>Search Companies
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Company name, industry, or contact...">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="status" class="form-label small text-muted">
                        <i class="bi bi-flag me-1"></i>Status
                    </label>
                    <select class="form-select filter-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>✅ Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>⏸️ Inactive</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="industry" class="form-label small text-muted">
                        <i class="bi bi-briefcase me-1"></i>Industry
                    </label>
                    <select class="form-select filter-select" id="industry" name="industry">
                        <option value="">All Industries</option>
                        <option value="construction" {{ request('industry') == 'construction' ? 'selected' : '' }}>🏗️ Construction</option>
                        <option value="real_estate" {{ request('industry') == 'real_estate' ? 'selected' : '' }}>🏢 Real Estate</option>
                        <option value="government" {{ request('industry') == 'government' ? 'selected' : '' }}>🏛️ Government</option>
                        <option value="commercial" {{ request('industry') == 'commercial' ? 'selected' : '' }}>🏪 Commercial</option>
                        <option value="residential" {{ request('industry') == 'residential' ? 'selected' : '' }}>🏠 Residential</option>
                        <option value="other" {{ request('industry') == 'other' ? 'selected' : '' }}>📦 Other</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="sort" class="form-label small text-muted">
                        <i class="bi bi-sort-down me-1"></i>Sort By
                    </label>
                    <select class="form-select filter-select" id="sort" name="sort">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Most Recent</option>
                        <option value="projects" {{ request('sort') == 'projects' ? 'selected' : '' }}>Most Projects</option>
                        <option value="value" {{ request('sort') == 'value' ? 'selected' : '' }}>Highest Value</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="bi bi-funnel-fill me-1"></i>Apply
                        </button>
                        <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- View Toggle -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-semibold">{{ $clients->total() }} {{ Str::plural('Client', $clients->total()) }} Found</h5>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-secondary active" id="tableViewBtn" onclick="switchView('table')">
                <i class="bi bi-list-ul"></i> Table
            </button>
            <button type="button" class="btn btn-outline-secondary" id="cardViewBtn" onclick="switchView('cards')">
                <i class="bi bi-grid-3x3-gap"></i> Cards
            </button>
        </div>
    </div>

    <!-- Clients Table View -->
    <div class="card border-0 shadow-sm" id="tableView">
        <div class="card-body p-0">
            @if($clients->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover clients-table mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 ps-4">
                                    <i class="bi bi-building me-2"></i>Company Details
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-person me-2"></i>Primary Contact
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-telephone me-2"></i>Contact Info
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-geo-alt me-2"></i>Location
                                </th>
                                <th class="border-0 text-center">
                                    <i class="bi bi-building me-2"></i>Sites
                                </th>
                                <th class="border-0 text-center">
                                    <i class="bi bi-folder me-2"></i>Projects
                                </th>
                                <th class="border-0">
                                    <i class="bi bi-flag me-2"></i>Status
                                </th>
                                <th class="border-0 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clients as $client)
                                <tr class="client-row">
                                    <td class="ps-4">
                                        <div class="client-info d-flex align-items-center">
                                            <div class="client-icon-wrapper bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                                <i class="bi bi-building text-primary fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-semibold">
                                                    <a href="{{ route('clients.show', $client) }}" class="text-decoration-none text-dark hover-primary">
                                                        {{ $client->display_name }}
                                                    </a>
                                                </h6>
                                                @if($client->industry)
                                                    <small class="text-muted">
                                                        <i class="bi bi-briefcase me-1"></i>{{ $client->industry }}
                                                    </small>
                                                @endif
                                                @if($client->business_type)
                                                    <span class="badge bg-info bg-opacity-10 text-info ms-2">{{ $client->business_type }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="contact-person">
                                            @if($client->primary_contact)
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-secondary text-white rounded-circle me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">
                                                        {{ substr($client->primary_contact, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $client->primary_contact }}</div>
                                                        @if($client->contact_title)
                                                            <small class="text-muted">{{ $client->contact_title }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="contact-details">
                                            @if($client->contact_email)
                                                <div class="mb-1">
                                                    <i class="bi bi-envelope text-muted me-1"></i>
                                                    <a href="mailto:{{ $client->contact_email }}" class="text-decoration-none">
                                                        {{ Str::limit($client->contact_email, 25) }}
                                                    </a>
                                                </div>
                                            @endif
                                            @if($client->contact_phone)
                                                <div>
                                                    <i class="bi bi-phone text-muted me-1"></i>
                                                    <a href="tel:{{ $client->contact_phone }}" class="text-decoration-none">
                                                        {{ $client->contact_phone }}
                                                    </a>
                                                </div>
                                            @endif
                                            @if(!$client->contact_email && !$client->contact_phone)
                                                <span class="text-muted">No contact info</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="location-info">
                                            @if($client->city)
                                                <div>
                                                    <i class="bi bi-geo-alt text-muted me-1"></i>
                                                    {{ $client->city }}
                                                </div>
                                                @if($client->state)
                                                    <small class="text-muted">{{ $client->state }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted fst-italic">No location</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($client->sites && $client->sites->count() > 0)
                                            <span class="badge bg-primary rounded-pill px-3 py-2">
                                                <i class="bi bi-building me-1"></i>{{ $client->sites->count() }}
                                            </span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="projects-info">
                                            @if($client->total_projects_count > 0)
                                                <span class="badge bg-info rounded-pill px-3 py-2">
                                                    <i class="bi bi-folder me-1"></i>{{ $client->total_projects_count }}
                                                </span>
                                                @if($client->active_projects_count > 0)
                                                    <div class="small text-success mt-1">
                                                        {{ $client->active_projects_count }} active
                                                    </div>
                                                @endif
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($client->is_active ?? true)
                                            <span class="badge rounded-pill bg-success px-3 py-2">
                                                <i class="bi bi-check-circle me-1"></i>Active
                                            </span>
                                        @else
                                            <span class="badge rounded-pill bg-secondary px-3 py-2">
                                                <i class="bi bi-pause-circle me-1"></i>Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('clients.show', $client) }}">
                                                        <i class="bi bi-eye me-2"></i>View Details
                                                    </a>
                                                </li>
                                                @if(auth()->user()->canManageClients())
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('clients.edit', $client) }}">
                                                            <i class="bi bi-pencil me-2"></i>Edit Client
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('sites.create', ['client_id' => $client->id]) }}">
                                                            <i class="bi bi-building-add me-2"></i>Create Site
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('projects.create', ['client_id' => $client->id]) }}">
                                                            <i class="bi bi-folder-plus me-2"></i>Create Project
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('invoices.create') }}?client_id={{ $client->id }}">
                                                            <i class="bi bi-receipt me-2"></i>Create Invoice
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button type="button" class="dropdown-item text-danger delete-client-btn"
                                                                data-client-id="{{ $client->id }}"
                                                                data-client-name="{{ $client->display_name }}"
                                                                data-projects-count="{{ $client->total_projects_count }}"
                                                                data-sites-count="{{ $client->sites->count() }}"
                                                                data-invoices-count="{{ $client->invoices->count() ?? 0 }}">
                                                            <i class="bi bi-trash me-2"></i>Delete Client
                                                        </button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-white border-0 py-3">
                    <div class="d-flex justify-content-center">
                        {{ $clients->links() }}
                    </div>
                </div>
            @else
                <div class="empty-state text-center py-5">
                    <div class="empty-icon-wrapper mx-auto mb-4" style="width: 120px; height: 120px;">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center h-100">
                            <i class="bi bi-person-badge display-1 text-muted"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-2">No Clients Found</h4>
                    <p class="text-muted mb-4">
                        {{ request()->hasAny(['search', 'status', 'industry']) 
                            ? 'Try adjusting your filters to find what you\'re looking for.' 
                            : 'Get started by adding your first client to the system.' }}
                    </p>
                    @if(auth()->user()->canManageClients() && !request()->hasAny(['search', 'status', 'industry']))
                        <a href="{{ route('clients.create') }}" class="btn btn-primary btn-lg shadow-sm">
                            <i class="bi bi-plus-circle me-2"></i>Add Your First Client
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Clients Card View (Hidden by default) -->
    <div class="row g-3" id="cardView" style="display: none;">
        @foreach($clients as $client)
            <div class="col-lg-4 col-md-6">
                <div class="card client-card border-0 shadow-sm h-100">
                    <div class="card-header bg-gradient-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-building me-2"></i>{{ $client->display_name }}
                            </h5>
                            @if($client->is_active ?? true)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if($client->industry || $client->business_type)
                            <div class="mb-3">
                                @if($client->industry)
                                    <span class="badge bg-light text-dark me-1">
                                        <i class="bi bi-briefcase me-1"></i>{{ $client->industry }}
                                    </span>
                                @endif
                                @if($client->business_type)
                                    <span class="badge bg-info bg-opacity-10 text-info">{{ $client->business_type }}</span>
                                @endif
                            </div>
                        @endif

                        <div class="client-details">
                            @if($client->primary_contact)
                                <div class="detail-item mb-2">
                                    <i class="bi bi-person text-muted me-2"></i>
                                    <strong>{{ $client->primary_contact }}</strong>
                                    @if($client->contact_title)
                                        <br><small class="text-muted ms-4">{{ $client->contact_title }}</small>
                                    @endif
                                </div>
                            @endif
                            
                            @if($client->contact_email)
                                <div class="detail-item mb-2">
                                    <i class="bi bi-envelope text-muted me-2"></i>
                                    <a href="mailto:{{ $client->contact_email }}" class="text-decoration-none">
                                        {{ Str::limit($client->contact_email, 30) }}
                                    </a>
                                </div>
                            @endif
                            
                            @if($client->contact_phone)
                                <div class="detail-item mb-2">
                                    <i class="bi bi-phone text-muted me-2"></i>
                                    <a href="tel:{{ $client->contact_phone }}" class="text-decoration-none">
                                        {{ $client->contact_phone }}
                                    </a>
                                </div>
                            @endif

                            @if($client->city)
                                <div class="detail-item mb-3">
                                    <i class="bi bi-geo-alt text-muted me-2"></i>
                                    {{ $client->city }}{{ $client->state ? ', ' . $client->state : '' }}
                                </div>
                            @endif
                        </div>

                        <div class="stats-section pt-3 border-top">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="stat-item">
                                        <div class="stat-value fw-bold text-primary">{{ $client->sites ? $client->sites->count() : 0 }}</div>
                                        <div class="stat-label small text-muted">Sites</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item">
                                        <div class="stat-value fw-bold text-info">{{ $client->total_projects_count ?? 0 }}</div>
                                        <div class="stat-label small text-muted">Projects</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-item">
                                        <div class="stat-value fw-bold text-success">{{ $client->active_projects_count ?? 0 }}</div>
                                        <div class="stat-label small text-muted">Active</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <div class="d-grid">
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-primary">
                                <i class="bi bi-eye me-1"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
/* Professional Clients Page Styling */
.stat-card {
    transition: transform 0.3s, box-shadow 0.3s;
    border-left: 4px solid transparent;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.stat-card:nth-child(1) { border-left-color: #0d6efd; }
.stat-card:nth-child(2) { border-left-color: #198754; }
.stat-card:nth-child(3) { border-left-color: #0dcaf0; }
.stat-card:nth-child(4) { border-left-color: #ffc107; }

.stat-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.filter-select {
    border: 1px solid #dee2e6;
    transition: border-color 0.3s, box-shadow 0.3s;
}

.filter-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}

.clients-table thead {
    background: linear-gradient(180deg, #f8f9fa 0%, #e9ecef 100%);
}

.clients-table thead th {
    font-weight: 600;
    color: #495057;
    padding: 1rem;
    white-space: nowrap;
}

.client-row {
    transition: background-color 0.2s, transform 0.2s;
}

.client-row:hover {
    background-color: rgba(0, 123, 255, 0.05);
    transform: scale(1.005);
}

.client-row td {
    padding: 1rem;
    vertical-align: middle;
}

.client-icon-wrapper {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.hover-primary:hover {
    color: #0d6efd !important;
}

.contact-details {
    font-size: 0.9rem;
}

.contact-details a {
    color: #6c757d;
}

.contact-details a:hover {
    color: #0d6efd;
}

/* Card View Styling */
.client-card {
    transition: transform 0.3s, box-shadow 0.3s;
    overflow: hidden;
}

.client-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.client-card .detail-item {
    font-size: 0.9rem;
    line-height: 1.5;
}

.client-card .stat-value {
    font-size: 1.25rem;
}

.client-card .stat-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.empty-icon-wrapper {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* View Toggle Buttons */
.btn-group .btn {
    padding: 0.5rem 1rem;
}

.btn-group .btn.active {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}

/* Responsive Design */
@media (max-width: 768px) {
    .stat-card .card-body {
        padding: 1rem;
    }
    
    .client-info h6 {
        font-size: 0.95rem;
    }
    
    .table-responsive {
        font-size: 0.9rem;
    }
}

/* Dropdown improvements */
.dropdown-item {
    padding: 0.5rem 1rem;
    transition: background-color 0.2s;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.dropdown-item i {
    width: 20px;
}
</style>

<script>
// View Toggle Function
function switchView(view) {
    const tableView = document.getElementById('tableView');
    const cardView = document.getElementById('cardView');
    const tableBtn = document.getElementById('tableViewBtn');
    const cardBtn = document.getElementById('cardViewBtn');
    
    if (view === 'cards') {
        tableView.style.display = 'none';
        cardView.style.display = 'flex';
        tableBtn.classList.remove('active');
        cardBtn.classList.add('active');
    } else {
        tableView.style.display = 'block';
        cardView.style.display = 'none';
        tableBtn.classList.add('active');
        cardBtn.classList.remove('active');
    }
    
    // Save preference
    localStorage.setItem('clientsViewPreference', view);
}

// Load view preference on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('clientsViewPreference');
    if (savedView === 'cards') {
        switchView('cards');
    }
});
</script>
<!-- Delete Client Confirmation Modal -->
<div class="modal fade" id="deleteClientModal" tabindex="-1" aria-labelledby="deleteClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteClientModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirm Client Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone and will permanently delete all related data.
                </div>
                
                <p class="mb-3">You are about to delete <strong id="clientName"></strong> and all associated data:</p>
                
                <ul class="list-group list-group-flush mb-3" id="dataList">
                    <!-- Dynamic content will be populated here -->
                </ul>
                
                <div class="form-group">
                    <label for="confirmationText" class="form-label">
                        To confirm deletion, please type the client name exactly as shown above:
                    </label>
                    <input type="text" class="form-control" id="confirmationText" placeholder="Enter client name to confirm">
                    <small class="text-muted">This helps prevent accidental deletions.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                    <i class="bi bi-trash me-1"></i>Delete Client & All Data
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for delete submission -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
let currentClientId = null;
let currentClientName = null;

function confirmDelete(button) {
    const clientId = button.dataset.clientId;
    const clientName = button.dataset.clientName;
    const projectsCount = button.dataset.projectsCount;
    const sitesCount = button.dataset.sitesCount;
    const invoicesCount = button.dataset.invoicesCount;
    
    currentClientId = clientId;
    currentClientName = clientName;
    
    // Update modal content
    document.getElementById('clientName').textContent = clientName;
    
    // Build data list
    const dataList = document.getElementById('dataList');
    dataList.innerHTML = `
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><i class="bi bi-folder text-info me-2"></i>Projects</span>
            <span class="badge bg-info rounded-pill">${projectsCount}</span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><i class="bi bi-building text-primary me-2"></i>Sites</span>
            <span class="badge bg-primary rounded-pill">${sitesCount}</span>
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><i class="bi bi-receipt text-warning me-2"></i>Invoices</span>
            <span class="badge bg-warning rounded-pill">${invoicesCount}</span>
        </li>
    `;
    
    // Reset confirmation input
    document.getElementById('confirmationText').value = '';
    document.getElementById('confirmDeleteBtn').disabled = true;
    
    // Show modal
    new bootstrap.Modal(document.getElementById('deleteClientModal')).show();
}

// Add event listeners for delete buttons and confirm button
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-client-btn') || e.target.closest('.delete-client-btn')) {
            const button = e.target.classList.contains('delete-client-btn') ? e.target : e.target.closest('.delete-client-btn');
            confirmDelete(button);
        }
        
        if (e.target.id === 'confirmDeleteBtn') {
            executeDelete();
        }
    });
});

function executeDelete() {
    if (currentClientId) {
        const form = document.getElementById('deleteForm');
        form.action = `{{ route('clients.index') }}/${currentClientId}`;
        form.submit();
    }
}

// Enable delete button only when correct client name is entered
document.getElementById('confirmationText').addEventListener('input', function(e) {
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    const isCorrect = e.target.value.trim() === currentClientName;
    deleteBtn.disabled = !isCorrect;
    
    if (isCorrect) {
        deleteBtn.classList.remove('btn-danger');
        deleteBtn.classList.add('btn-success');
        deleteBtn.innerHTML = '<i class="bi bi-check me-1"></i>Confirmed - Delete Now';
    } else {
        deleteBtn.classList.remove('btn-success');
        deleteBtn.classList.add('btn-danger');
        deleteBtn.innerHTML = '<i class="bi bi-trash me-1"></i>Delete Client & All Data';
    }
});
</script>

@endsection