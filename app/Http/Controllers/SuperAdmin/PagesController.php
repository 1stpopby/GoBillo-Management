<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\FooterLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagesController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->isSuperAdmin()) {
                abort(403, 'Access denied: SuperAdmin only.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of pages
     */
    public function index(Request $request)
    {
        $query = Page::orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } else {
                $query->where('is_published', false);
            }
        }

        $pages = $query->paginate(15);
        
        return view('superadmin.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new page
     */
    public function create()
    {
        $templates = Page::getTemplates();
        $footerSections = Page::getFooterSections();
        
        return view('superadmin.pages.create', compact('templates', 'footerSections'));
    }

    /**
     * Store a newly created page
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:300',
            'meta_keywords' => 'nullable|string|max:255',
            'template' => 'required|string|in:' . implode(',', array_keys(Page::getTemplates())),
            'footer_section' => 'nullable|string|in:' . implode(',', array_keys(Page::getFooterSections())),
            'sort_order' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $page = Page::create([
                'title' => $request->title,
                'slug' => $request->slug ?: Page::generateSlug($request->title),
                'content' => $request->content,
                'excerpt' => $request->excerpt,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'template' => $request->template,
                'is_published' => $request->has('is_published'),
                'show_in_footer' => $request->has('show_in_footer'),
                'footer_section' => $request->footer_section,
                'sort_order' => $request->sort_order ?? 0,
                'published_at' => $request->has('is_published') ? now() : null,
            ]);

            // Auto-create footer link if requested
            if ($request->has('show_in_footer') && $request->footer_section) {
                FooterLink::create([
                    'title' => $page->title,
                    'url' => '/page/' . $page->slug,
                    'section' => $request->footer_section,
                    'target' => '_self',
                    'sort_order' => $request->sort_order ?? 0,
                    'is_active' => $request->has('is_published'),
                ]);
            }

            DB::commit();

            return redirect()->route('superadmin.pages.index')
                ->with('success', 'Page created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating page: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified page
     */
    public function show(Page $page)
    {
        return view('superadmin.pages.show', compact('page'));
    }

    /**
     * Show the form for editing the specified page
     */
    public function edit(Page $page)
    {
        $templates = Page::getTemplates();
        $footerSections = Page::getFooterSections();
        
        return view('superadmin.pages.edit', compact('page', 'templates', 'footerSections'));
    }

    /**
     * Update the specified page
     */
    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:300',
            'meta_keywords' => 'nullable|string|max:255',
            'template' => 'required|string|in:' . implode(',', array_keys(Page::getTemplates())),
            'footer_section' => 'nullable|string|in:' . implode(',', array_keys(Page::getFooterSections())),
            'sort_order' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $oldSlug = $page->slug;
            
            $page->update([
                'title' => $request->title,
                'slug' => $request->slug ?: Page::generateSlug($request->title, $page->id),
                'content' => $request->content,
                'excerpt' => $request->excerpt,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'template' => $request->template,
                'is_published' => $request->has('is_published'),
                'show_in_footer' => $request->has('show_in_footer'),
                'footer_section' => $request->footer_section,
                'sort_order' => $request->sort_order ?? 0,
                'published_at' => $request->has('is_published') ? ($page->published_at ?: now()) : null,
            ]);

            // Update or create footer link
            $existingFooterLink = FooterLink::where('url', '/page/' . $oldSlug)->first();
            
            if ($request->has('show_in_footer') && $request->footer_section) {
                if ($existingFooterLink) {
                    $existingFooterLink->update([
                        'title' => $page->title,
                        'url' => '/page/' . $page->slug,
                        'section' => $request->footer_section,
                        'sort_order' => $request->sort_order ?? 0,
                        'is_active' => $request->has('is_published'),
                    ]);
                } else {
                    FooterLink::create([
                        'title' => $page->title,
                        'url' => '/page/' . $page->slug,
                        'section' => $request->footer_section,
                        'target' => '_self',
                        'sort_order' => $request->sort_order ?? 0,
                        'is_active' => $request->has('is_published'),
                    ]);
                }
            } else {
                // Remove footer link if no longer showing in footer
                if ($existingFooterLink) {
                    $existingFooterLink->delete();
                }
            }

            DB::commit();

            return redirect()->route('superadmin.pages.index')
                ->with('success', 'Page updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating page: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified page
     */
    public function destroy(Page $page)
    {
        try {
            // Remove associated footer link
            FooterLink::where('url', '/page/' . $page->slug)->delete();
            
            $page->delete();
            return redirect()->route('superadmin.pages.index')
                ->with('success', 'Page deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting page: ' . $e->getMessage());
        }
    }

    /**
     * Initialize default pages
     */
    public function initializeDefaults()
    {
        $defaultPages = [
            [
                'title' => 'Privacy Policy',
                'content' => $this->getPrivacyPolicyContent(),
                'template' => 'legal',
                'show_in_footer' => true,
                'footer_section' => 'legal',
                'sort_order' => 1,
            ],
            [
                'title' => 'Terms of Service',
                'content' => $this->getTermsOfServiceContent(),
                'template' => 'legal',
                'show_in_footer' => true,
                'footer_section' => 'legal',
                'sort_order' => 2,
            ],
            [
                'title' => 'Cookie Policy',
                'content' => $this->getCookiePolicyContent(),
                'template' => 'legal',
                'show_in_footer' => true,
                'footer_section' => 'legal',
                'sort_order' => 3,
            ],
            [
                'title' => 'GDPR Compliance',
                'content' => $this->getGDPRContent(),
                'template' => 'legal',
                'show_in_footer' => true,
                'footer_section' => 'legal',
                'sort_order' => 4,
            ],
            [
                'title' => 'About Us',
                'content' => $this->getAboutUsContent(),
                'template' => 'default',
                'show_in_footer' => true,
                'footer_section' => 'company',
                'sort_order' => 1,
            ],
        ];

        try {
            DB::beginTransaction();

            foreach ($defaultPages as $pageData) {
                $slug = Page::generateSlug($pageData['title']);
                
                $page = Page::updateOrCreate(
                    ['slug' => $slug],
                    array_merge($pageData, [
                        'slug' => $slug,
                        'is_published' => true,
                        'published_at' => now(),
                    ])
                );

                // Create footer link
                if ($pageData['show_in_footer']) {
                    FooterLink::updateOrCreate(
                        ['url' => '/page/' . $page->slug],
                        [
                            'title' => $page->title,
                            'url' => '/page/' . $page->slug,
                            'section' => $pageData['footer_section'],
                            'target' => '_self',
                            'sort_order' => $pageData['sort_order'],
                            'is_active' => true,
                        ]
                    );
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Default pages created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error creating default pages: ' . $e->getMessage());
        }
    }

    /**
     * Default page content methods
     */
    private function getPrivacyPolicyContent()
    {
        return '<h1>Privacy Policy</h1>
<p><strong>Last updated:</strong> ' . now()->format('F j, Y') . '</p>

<h2>Information We Collect</h2>
<p>We collect information you provide directly to us, such as when you create an account, use our services, or contact us for support.</p>

<h2>How We Use Your Information</h2>
<p>We use the information we collect to provide, maintain, and improve our services, process transactions, and communicate with you.</p>

<h2>Information Sharing</h2>
<p>We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except as described in this policy.</p>

<h2>Data Security</h2>
<p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>

<h2>Contact Us</h2>
<p>If you have questions about this Privacy Policy, please contact us at privacy@promaxteam.com.</p>';
    }

    private function getTermsOfServiceContent()
    {
        return '<h1>Terms of Service</h1>
<p><strong>Last updated:</strong> ' . now()->format('F j, Y') . '</p>

<h2>Acceptance of Terms</h2>
<p>By accessing and using ProMax Team, you accept and agree to be bound by the terms and provision of this agreement.</p>

<h2>Use License</h2>
<p>Permission is granted to temporarily use ProMax Team for personal, non-commercial transitory viewing only.</p>

<h2>Disclaimer</h2>
<p>The materials on ProMax Team are provided on an "as is" basis. ProMax Team makes no warranties, expressed or implied.</p>

<h2>Limitations</h2>
<p>In no event shall ProMax Team or its suppliers be liable for any damages arising out of the use or inability to use the materials on ProMax Team.</p>

<h2>Contact Information</h2>
<p>If you have questions about these Terms of Service, please contact us at legal@promaxteam.com.</p>';
    }

    private function getCookiePolicyContent()
    {
        return '<h1>Cookie Policy</h1>
<p><strong>Last updated:</strong> ' . now()->format('F j, Y') . '</p>

<h2>What Are Cookies</h2>
<p>Cookies are small text files that are stored on your computer or mobile device when you visit our website.</p>

<h2>How We Use Cookies</h2>
<p>We use cookies to improve your experience on our website, analyze usage patterns, and provide personalized content.</p>

<h2>Types of Cookies We Use</h2>
<ul>
<li><strong>Essential Cookies:</strong> Required for the website to function properly</li>
<li><strong>Analytics Cookies:</strong> Help us understand how visitors use our website</li>
<li><strong>Functional Cookies:</strong> Enable enhanced functionality and personalization</li>
</ul>

<h2>Managing Cookies</h2>
<p>You can control and/or delete cookies as you wish through your browser settings. However, removing cookies may affect the functionality of our website.</p>

<h2>Contact Us</h2>
<p>For questions about our use of cookies, please contact us at privacy@promaxteam.com.</p>';
    }

    private function getGDPRContent()
    {
        return '<h1>GDPR Compliance</h1>
<p><strong>Last updated:</strong> ' . now()->format('F j, Y') . '</p>

<h2>Your Rights Under GDPR</h2>
<p>Under the General Data Protection Regulation (GDPR), you have the following rights:</p>

<ul>
<li><strong>Right to Access:</strong> You can request access to your personal data</li>
<li><strong>Right to Rectification:</strong> You can request correction of inaccurate data</li>
<li><strong>Right to Erasure:</strong> You can request deletion of your personal data</li>
<li><strong>Right to Data Portability:</strong> You can request transfer of your data</li>
<li><strong>Right to Object:</strong> You can object to processing of your personal data</li>
</ul>

<h2>Legal Basis for Processing</h2>
<p>We process your personal data based on:</p>
<ul>
<li>Your consent</li>
<li>Performance of a contract</li>
<li>Legal obligations</li>
<li>Legitimate interests</li>
</ul>

<h2>Data Protection Officer</h2>
<p>For GDPR-related inquiries, please contact our Data Protection Officer at dpo@gobillo.com.</p>

<h2>Exercising Your Rights</h2>
<p>To exercise any of your GDPR rights, please contact us at gdpr@gobillo.com with your request.</p>';
    }

    private function getAboutUsContent()
    {
        return '<h1>About ProMax Team</h1>

<h2>Our Mission</h2>
<p>ProMax Team is dedicated to transforming the construction industry through innovative project management solutions that streamline operations and drive business growth.</p>

<h2>What We Do</h2>
<p>We provide comprehensive construction management software that helps construction companies:</p>
<ul>
<li>Manage projects from start to finish</li>
<li>Track teams and resources efficiently</li>
<li>Handle financial aspects seamlessly</li>
<li>Communicate effectively with clients</li>
<li>Generate insightful reports and analytics</li>
</ul>

<h2>Our Values</h2>
<ul>
<li><strong>Innovation:</strong> We continuously evolve our platform to meet industry needs</li>
<li><strong>Reliability:</strong> We provide dependable solutions you can count on</li>
<li><strong>Support:</strong> We\'re here to help you succeed every step of the way</li>
<li><strong>Growth:</strong> We help construction businesses scale and thrive</li>
</ul>

<h2>Contact Us</h2>
<p>Ready to transform your construction business? Get in touch with us today!</p>
<p>Email: hello@gobillo.com<br>
Phone: (555) 123-4567</p>';
    }
}
