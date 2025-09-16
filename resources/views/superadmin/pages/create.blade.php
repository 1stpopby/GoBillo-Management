@extends('layouts.superadmin')

@section('title', 'Create New Page')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Create New Page</h1>
                    <p class="text-muted">Create a new page for your website</p>
                </div>
                <a href="{{ route('superadmin.pages.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Pages
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <h6><i class="bi bi-exclamation-triangle me-2"></i>Please correct the following errors:</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('superadmin.pages.store') }}">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-file-earmark-text me-2"></i>Page Content
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Page Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                                    <div class="form-text">This will be the main heading of your page</div>
                                </div>

                                <div class="mb-3">
                                    <label for="slug" class="form-label">URL Slug</label>
                                    <div class="input-group">
                                        <span class="input-group-text">/page/</span>
                                        <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug') }}">
                                    </div>
                                    <div class="form-text">Leave blank to auto-generate from title</div>
                                </div>

                                <div class="mb-3">
                                    <label for="excerpt" class="form-label">Page Excerpt</label>
                                    <textarea class="form-control" id="excerpt" name="excerpt" rows="3" maxlength="500">{{ old('excerpt') }}</textarea>
                                    <div class="form-text">Short description shown in search results and previews</div>
                                </div>

                                <div class="mb-3">
                                    <label for="content" class="form-label">Page Content <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="content" name="content" rows="15" required>{{ old('content') }}</textarea>
                                    <div class="form-text">Full HTML content for your page</div>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Settings -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-search me-2"></i>SEO Settings
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="meta_title" class="form-label">Meta Title</label>
                                    <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ old('meta_title') }}" maxlength="255">
                                    <div class="form-text">Leave blank to use page title</div>
                                </div>

                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="300">{{ old('meta_description') }}</textarea>
                                    <div class="form-text">Description shown in search results (160-300 characters)</div>
                                </div>

                                <div class="mb-3">
                                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                    <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords') }}">
                                    <div class="form-text">Comma-separated keywords for search engines</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Publish Settings -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-gear me-2"></i>Page Settings
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="template" class="form-label">Template <span class="text-danger">*</span></label>
                                    <select class="form-select" id="template" name="template" required>
                                        @foreach($templates as $key => $name)
                                            <option value="{{ $key }}" {{ old('template') === $key ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Choose the design template for this page</div>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_published" name="is_published" {{ old('is_published') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_published">
                                        <strong>Publish Page</strong>
                                    </label>
                                    <div class="form-text">Make this page visible to visitors</div>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="show_in_footer" name="show_in_footer" {{ old('show_in_footer') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_in_footer">
                                        <strong>Show in Footer</strong>
                                    </label>
                                    <div class="form-text">Add link to this page in footer</div>
                                </div>

                                <div class="mb-3" id="footer_section_group" style="{{ old('show_in_footer') ? '' : 'display: none;' }}">
                                    <label for="footer_section" class="form-label">Footer Section</label>
                                    <select class="form-select" id="footer_section" name="footer_section">
                                        <option value="">Select Section</option>
                                        @foreach($footerSections as $key => $name)
                                            <option value="{{ $key }}" {{ old('footer_section') === $key ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number" class="form-control" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                                    <div class="form-text">Order in navigation (0 = first)</div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Templates -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-lightning me-2"></i>Quick Templates
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadTemplate('privacy')">
                                        <i class="bi bi-shield-check me-1"></i>Privacy Policy
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadTemplate('terms')">
                                        <i class="bi bi-file-text me-1"></i>Terms of Service
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadTemplate('cookies')">
                                        <i class="bi bi-cookie me-1"></i>Cookie Policy
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadTemplate('about')">
                                        <i class="bi bi-info-circle me-1"></i>About Us
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('superadmin.pages.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </a>
                            <div>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-1"></i>Create Page
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate slug from title
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    
    titleInput.addEventListener('input', function() {
        if (!slugInput.dataset.manual) {
            const slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
            slugInput.value = slug;
        }
    });
    
    slugInput.addEventListener('input', function() {
        this.dataset.manual = 'true';
    });
    
    // Show/hide footer section based on checkbox
    const showInFooterCheck = document.getElementById('show_in_footer');
    const footerSectionGroup = document.getElementById('footer_section_group');
    
    showInFooterCheck.addEventListener('change', function() {
        footerSectionGroup.style.display = this.checked ? 'block' : 'none';
    });
});

function loadTemplate(type) {
    const templates = {
        privacy: {
            title: 'Privacy Policy',
            excerpt: 'Learn how we collect, use, and protect your personal information.',
            content: `<h1>Privacy Policy</h1>
<p><strong>Last updated:</strong> ${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>

<h2>Information We Collect</h2>
<p>We collect information you provide directly to us, such as when you create an account, use our services, or contact us for support.</p>

<h2>How We Use Your Information</h2>
<p>We use the information we collect to provide, maintain, and improve our services, process transactions, and communicate with you.</p>

<h2>Information Sharing</h2>
<p>We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except as described in this policy.</p>

<h2>Contact Us</h2>
<p>If you have questions about this Privacy Policy, please contact us at privacy@gobillo.com.</p>`,
            template: 'legal',
            footer_section: 'legal'
        },
        terms: {
            title: 'Terms of Service',
            excerpt: 'The terms and conditions for using our services.',
            content: `<h1>Terms of Service</h1>
<p><strong>Last updated:</strong> ${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>

<h2>Acceptance of Terms</h2>
<p>By accessing and using GoBillo, you accept and agree to be bound by the terms and provision of this agreement.</p>

<h2>Use License</h2>
<p>Permission is granted to temporarily use GoBillo for personal, non-commercial transitory viewing only.</p>

<h2>Contact Information</h2>
<p>If you have questions about these Terms of Service, please contact us at legal@gobillo.com.</p>`,
            template: 'legal',
            footer_section: 'legal'
        },
        cookies: {
            title: 'Cookie Policy',
            excerpt: 'How we use cookies to improve your experience.',
            content: `<h1>Cookie Policy</h1>
<p><strong>Last updated:</strong> ${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>

<h2>What Are Cookies</h2>
<p>Cookies are small text files that are stored on your computer or mobile device when you visit our website.</p>

<h2>How We Use Cookies</h2>
<p>We use cookies to improve your experience on our website, analyze usage patterns, and provide personalized content.</p>

<h2>Contact Us</h2>
<p>For questions about our use of cookies, please contact us at privacy@gobillo.com.</p>`,
            template: 'legal',
            footer_section: 'legal'
        },
        about: {
            title: 'About Us',
            excerpt: 'Learn more about our company and mission.',
            content: `<h1>About GoBillo</h1>

<h2>Our Mission</h2>
<p>GoBillo is dedicated to transforming the construction industry through innovative project management solutions.</p>

<h2>What We Do</h2>
<p>We provide comprehensive construction management software that helps construction companies manage projects efficiently.</p>

<h2>Contact Us</h2>
<p>Ready to transform your construction business? Get in touch with us today!</p>
<p>Email: hello@gobillo.com</p>`,
            template: 'default',
            footer_section: 'company'
        }
    };
    
    if (templates[type]) {
        const template = templates[type];
        document.getElementById('title').value = template.title;
        document.getElementById('excerpt').value = template.excerpt;
        document.getElementById('content').value = template.content;
        document.getElementById('template').value = template.template;
        document.getElementById('footer_section').value = template.footer_section;
        document.getElementById('show_in_footer').checked = true;
        document.getElementById('footer_section_group').style.display = 'block';
        
        // Auto-generate slug
        const slug = template.title
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        document.getElementById('slug').value = slug;
    }
}
</script>
@endsection
