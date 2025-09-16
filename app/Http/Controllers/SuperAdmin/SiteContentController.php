<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SiteContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiteContentController extends Controller
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
     * Display site content management
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 'landing');
        $contents = SiteContent::getForAdmin($page);
        
        $pages = [
            'landing' => 'Landing Page',
            'get_started' => 'Get Started Page', 
            'footer' => 'Footer',
            'global' => 'Global Settings'
        ];

        return view('superadmin.site-content.index', compact('contents', 'pages', 'page'));
    }

    /**
     * Update site content
     */
    public function update(Request $request)
    {
        $request->validate([
            'contents' => 'required|array',
            'contents.*' => 'nullable|string|max:10000',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->contents as $key => $value) {
                SiteContent::where('key', $key)->update(['value' => $value]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Site content updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating content: ' . $e->getMessage());
        }
    }

    /**
     * Initialize default content
     */
    public function initializeDefaults()
    {
        $defaultContent = [
            // Landing Page Hero
            [
                'key' => 'landing_hero_title',
                'page' => 'landing',
                'section' => 'hero',
                'type' => 'text',
                'label' => 'Hero Title',
                'default_value' => 'Professional Construction Management Platform',
                'description' => 'Main heading on the landing page',
                'sort_order' => 1,
            ],
            [
                'key' => 'landing_hero_subtitle',
                'page' => 'landing',
                'section' => 'hero',
                'type' => 'textarea',
                'label' => 'Hero Subtitle',
                'default_value' => 'Streamline your construction projects, manage teams, and grow your business with GoBillo - the complete construction management solution.',
                'description' => 'Subtitle text below the main heading',
                'sort_order' => 2,
            ],
            [
                'key' => 'landing_hero_cta_text',
                'page' => 'landing',
                'section' => 'hero',
                'type' => 'text',
                'label' => 'Hero CTA Button Text',
                'default_value' => 'Start Free Trial',
                'description' => 'Text for the main call-to-action button',
                'sort_order' => 3,
            ],

            // Get Started Page
            [
                'key' => 'get_started_hero_title',
                'page' => 'get_started',
                'section' => 'hero',
                'type' => 'text',
                'label' => 'Get Started Hero Title',
                'default_value' => 'Transform Your Construction Business',
                'description' => 'Main title on the get started page',
                'sort_order' => 1,
            ],
            [
                'key' => 'get_started_hero_subtitle',
                'page' => 'get_started',
                'section' => 'hero',
                'type' => 'textarea',
                'label' => 'Get Started Hero Subtitle',
                'default_value' => 'Join thousands of construction companies using GoBillo to streamline operations, manage projects, and grow their business.',
                'description' => 'Subtitle text on the get started page',
                'sort_order' => 2,
            ],
            [
                'key' => 'get_started_form_title',
                'page' => 'get_started',
                'section' => 'form',
                'type' => 'text',
                'label' => 'Registration Form Title',
                'default_value' => 'Get Started Today',
                'description' => 'Title above the registration form',
                'sort_order' => 1,
            ],
            [
                'key' => 'get_started_form_subtitle',
                'page' => 'get_started',
                'section' => 'form',
                'type' => 'text',
                'label' => 'Registration Form Subtitle',
                'default_value' => 'Create your company account and start your free trial',
                'description' => 'Subtitle below the form title',
                'sort_order' => 2,
            ],

            // Footer
            [
                'key' => 'footer_company_description',
                'page' => 'footer',
                'section' => 'company',
                'type' => 'textarea',
                'label' => 'Company Description',
                'default_value' => 'The complete construction management platform trusted by thousands of construction professionals worldwide.',
                'description' => 'Company description in footer',
                'sort_order' => 1,
            ],
            [
                'key' => 'footer_copyright',
                'page' => 'footer',
                'section' => 'legal',
                'type' => 'text',
                'label' => 'Copyright Text',
                'default_value' => 'GoBillo. All rights reserved.',
                'description' => 'Copyright text (year will be added automatically)',
                'sort_order' => 1,
            ],
            [
                'key' => 'footer_tagline',
                'page' => 'footer',
                'section' => 'legal',
                'type' => 'text',
                'label' => 'Footer Tagline',
                'default_value' => 'Made with ❤️ for construction professionals',
                'description' => 'Tagline text in footer',
                'sort_order' => 2,
            ],

            // Global Settings
            [
                'key' => 'site_name',
                'page' => 'global',
                'section' => 'branding',
                'type' => 'text',
                'label' => 'Site Name',
                'default_value' => 'GoBillo',
                'description' => 'Site name used throughout the application',
                'sort_order' => 1,
            ],
            [
                'key' => 'company_email',
                'page' => 'global',
                'section' => 'contact',
                'type' => 'email',
                'label' => 'Company Email',
                'default_value' => 'sales@gobillo.com',
                'description' => 'Main contact email for the company',
                'sort_order' => 1,
            ],
        ];

        foreach ($defaultContent as $content) {
            SiteContent::updateOrCreate(
                ['key' => $content['key']],
                $content
            );
        }

        return redirect()->back()->with('success', 'Default content initialized successfully!');
    }

    /**
     * Reset content to defaults
     */
    public function resetToDefaults(Request $request)
    {
        $request->validate([
            'page' => 'required|string|in:landing,get_started,footer,global'
        ]);

        try {
            SiteContent::where('page', $request->page)->update([
                'value' => DB::raw('default_value')
            ]);

            return redirect()->back()->with('success', 'Content reset to defaults successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error resetting content: ' . $e->getMessage());
        }
    }
}