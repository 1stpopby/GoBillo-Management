@extends('layouts.superadmin')

@section('title', 'Site Content Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Site Content Management</h1>
                    <p class="text-muted">Manage content for landing pages, get started page, and footer</p>
                </div>
                <div class="d-flex gap-2">
                    <form action="{{ route('superadmin.site-content.initialize') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary" onclick="return confirm('This will create/update default content. Continue?')">
                            <i class="bi bi-arrow-clockwise me-1"></i>Initialize Defaults
                        </button>
                    </form>
                    <form action="{{ route('superadmin.site-content.reset') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="page" value="{{ $page }}">
                        <button type="submit" class="btn btn-outline-warning" onclick="return confirm('This will reset {{ $pages[$page] }} content to defaults. Continue?')">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset {{ $pages[$page] }}
                        </button>
                    </form>
                </div>
            </div>

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

            <!-- Page Tabs -->
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        @foreach($pages as $pageKey => $pageName)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $page === $pageKey ? 'active' : '' }}" 
                                   href="{{ route('superadmin.site-content.index', ['page' => $pageKey]) }}">
                                    <i class="bi bi-{{ $pageKey === 'landing' ? 'house' : ($pageKey === 'get_started' ? 'rocket-takeoff' : ($pageKey === 'footer' ? 'layout-text-window' : 'gear')) }} me-1"></i>
                                    {{ $pageName }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card-body">
                    @if(empty($contents))
                        <div class="text-center py-5">
                            <i class="bi bi-file-text display-1 text-muted mb-3"></i>
                            <h4 class="text-muted">No Content Found</h4>
                            <p class="text-muted mb-4">Initialize default content to get started.</p>
                            <form action="{{ route('superadmin.site-content.initialize') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i>Initialize Default Content
                                </button>
                            </form>
                        </div>
                    @else
                        <form action="{{ route('superadmin.site-content.update') }}" method="POST">
                            @csrf
                            
                            @foreach($contents as $pageName => $sections)
                                @if($pageName === $page)
                                    @foreach($sections as $sectionName => $sectionContents)
                                        <div class="mb-5">
                                            <h5 class="border-bottom pb-2 mb-3">
                                                <i class="bi bi-collection me-2"></i>{{ ucfirst(str_replace('_', ' ', $sectionName)) }} Section
                                            </h5>
                                            
                                            <div class="row">
                                                @foreach($sectionContents as $content)
                                                    <div class="col-lg-6 mb-4">
                                                        <div class="card h-100">
                                                            <div class="card-body">
                                                                <label for="content_{{ $content->key }}" class="form-label fw-semibold">
                                                                    {{ $content->label }}
                                                                    @if($content->description)
                                                                        <i class="bi bi-info-circle text-muted ms-1" 
                                                                           data-bs-toggle="tooltip" 
                                                                           title="{{ $content->description }}"></i>
                                                                    @endif
                                                                </label>
                                                                
                                                                @if($content->type === 'textarea')
                                                                    <textarea name="contents[{{ $content->key }}]" 
                                                                              id="content_{{ $content->key }}" 
                                                                              class="form-control" 
                                                                              rows="4" 
                                                                              placeholder="{{ $content->default_value }}">{{ $content->value ?: $content->default_value }}</textarea>
                                                                @elseif($content->type === 'html')
                                                                    <textarea name="contents[{{ $content->key }}]" 
                                                                              id="content_{{ $content->key }}" 
                                                                              class="form-control html-editor" 
                                                                              rows="6" 
                                                                              placeholder="{{ $content->default_value }}">{{ $content->value ?: $content->default_value }}</textarea>
                                                                @else
                                                                    <input type="{{ $content->type === 'email' ? 'email' : 'text' }}" 
                                                                           name="contents[{{ $content->key }}]" 
                                                                           id="content_{{ $content->key }}" 
                                                                           class="form-control" 
                                                                           value="{{ $content->value ?: $content->default_value }}" 
                                                                           placeholder="{{ $content->default_value }}">
                                                                @endif
                                                                
                                                                @if($content->default_value)
                                                                    <small class="form-text text-muted mt-1">
                                                                        <strong>Default:</strong> {{ Str::limit($content->default_value, 100) }}
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            @endforeach
                            
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <div class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Changes will be visible immediately on the website
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-secondary me-2" onclick="window.location.reload()">
                                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-1"></i>Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Preview Section -->
            @if(!empty($contents))
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-eye me-2"></i>Quick Preview Links
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <a href="{{ route('welcome') }}" target="_blank" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="bi bi-house me-2"></i>View Landing Page
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('get-started') }}" target="_blank" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="bi bi-rocket-takeoff me-2"></i>View Get Started Page
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('get-started') }}#footer" target="_blank" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="bi bi-layout-text-window me-2"></i>View Footer
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Auto-save functionality (optional)
    let autoSaveTimeout;
    document.querySelectorAll('input, textarea').forEach(function(element) {
        element.addEventListener('input', function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(function() {
                // Show auto-save indicator
                const indicator = document.createElement('span');
                indicator.className = 'badge bg-secondary ms-2';
                indicator.textContent = 'Auto-saving...';
                element.parentNode.appendChild(indicator);
                
                setTimeout(function() {
                    if (indicator.parentNode) {
                        indicator.remove();
                    }
                }, 2000);
            }, 2000);
        });
    });
</script>
@endpush

@push('styles')
<style>
    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 2px solid transparent;
    }
    
    .nav-tabs .nav-link:hover {
        border-color: transparent;
        border-bottom-color: #dee2e6;
    }
    
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        background-color: transparent;
        border-color: transparent;
        border-bottom-color: #0d6efd;
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .html-editor {
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 0.875rem;
    }
</style>
@endpush
@endsection
