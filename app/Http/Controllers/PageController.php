<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display the specified page
     */
    public function show($slug)
    {
        $page = Page::findBySlug($slug);
        
        if (!$page) {
            abort(404, 'Page not found');
        }

        // Choose template based on page template setting
        $template = 'pages.' . $page->template;
        
        // Fallback to default if template doesn't exist
        if (!view()->exists($template)) {
            $template = 'pages.default';
        }

        return view($template, compact('page'));
    }
}