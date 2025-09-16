<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\FooterLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FooterLinksController extends Controller
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
     * Display footer links management
     */
    public function index()
    {
        $links = FooterLink::orderBy('section')->orderBy('sort_order')->get()->groupBy('section');
        $sections = FooterLink::getSections();
        
        return view('superadmin.footer-links.index', compact('links', 'sections'));
    }

    /**
     * Store a new footer link
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:500',
            'section' => 'required|string|in:product,company,support,legal',
            'target' => 'required|in:_self,_blank',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        try {
            FooterLink::create([
                'title' => $request->title,
                'url' => $request->url,
                'section' => $request->section,
                'target' => $request->target,
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => true,
            ]);

            return redirect()->back()->with('success', 'Footer link added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error adding link: ' . $e->getMessage());
        }
    }

    /**
     * Update a footer link
     */
    public function update(Request $request, FooterLink $footerLink)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:500',
            'section' => 'required|string|in:product,company,support,legal',
            'target' => 'required|in:_self,_blank',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            $footerLink->update([
                'title' => $request->title,
                'url' => $request->url,
                'section' => $request->section,
                'target' => $request->target,
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->back()->with('success', 'Footer link updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating link: ' . $e->getMessage());
        }
    }

    /**
     * Delete a footer link
     */
    public function destroy(FooterLink $footerLink)
    {
        try {
            $footerLink->delete();
            return redirect()->back()->with('success', 'Footer link deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting link: ' . $e->getMessage());
        }
    }

    /**
     * Initialize default footer links
     */
    public function initializeDefaults()
    {
        $defaultLinks = [
            // Product Section
            ['title' => 'Features', 'url' => '/get-started#features', 'section' => 'product', 'sort_order' => 1],
            ['title' => 'Pricing', 'url' => '/get-started#pricing', 'section' => 'product', 'sort_order' => 2],
            ['title' => 'Integrations', 'url' => '#', 'section' => 'product', 'sort_order' => 3],
            ['title' => 'API', 'url' => '#', 'section' => 'product', 'sort_order' => 4],

            // Company Section
            ['title' => 'About Us', 'url' => '#', 'section' => 'company', 'sort_order' => 1],
            ['title' => 'Careers', 'url' => '#', 'section' => 'company', 'sort_order' => 2],
            ['title' => 'Blog', 'url' => '#', 'section' => 'company', 'sort_order' => 3],
            ['title' => 'News', 'url' => '#', 'section' => 'company', 'sort_order' => 4],

            // Support Section
            ['title' => 'Help Center', 'url' => '#', 'section' => 'support', 'sort_order' => 1],
            ['title' => 'Documentation', 'url' => '#', 'section' => 'support', 'sort_order' => 2],
            ['title' => 'Contact Us', 'url' => '#', 'section' => 'support', 'sort_order' => 3],
            ['title' => 'Status', 'url' => '#', 'section' => 'support', 'sort_order' => 4],

            // Legal Section
            ['title' => 'Privacy Policy', 'url' => '#', 'section' => 'legal', 'sort_order' => 1],
            ['title' => 'Terms of Service', 'url' => '#', 'section' => 'legal', 'sort_order' => 2],
            ['title' => 'Cookie Policy', 'url' => '#', 'section' => 'legal', 'sort_order' => 3],
            ['title' => 'GDPR', 'url' => '#', 'section' => 'legal', 'sort_order' => 4],
        ];

        try {
            DB::beginTransaction();
            
            foreach ($defaultLinks as $link) {
                FooterLink::updateOrCreate(
                    ['title' => $link['title'], 'section' => $link['section']],
                    array_merge($link, ['target' => '_self', 'is_active' => true])
                );
            }
            
            DB::commit();
            return redirect()->back()->with('success', 'Default footer links initialized successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error initializing links: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update sort order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'links' => 'required|array',
            'links.*.id' => 'required|exists:footer_links,id',
            'links.*.sort_order' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();
            
            foreach ($request->links as $linkData) {
                FooterLink::where('id', $linkData['id'])
                    ->update(['sort_order' => $linkData['sort_order']]);
            }
            
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Order updated successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error updating order: ' . $e->getMessage()]);
        }
    }
}