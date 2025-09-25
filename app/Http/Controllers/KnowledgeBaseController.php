<?php

namespace App\Http\Controllers;

use App\Models\KBCategory;
use App\Models\KBArticle;
use App\Models\KBTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KnowledgeBaseController extends Controller
{
    /**
     * Display the knowledge base home page
     */
    public function index()
    {
        // Get root categories with article counts
        $categories = KBCategory::roots()
            ->active()
            ->withCount(['articles' => function($query) {
                $query->published();
            }])
            ->orderBy('order')
            ->get();
        
        // Get popular articles
        $popularArticles = KBArticle::published()
            ->orderBy('view_count', 'desc')
            ->limit(10)
            ->get();
        
        // Get recent articles
        $recentArticles = KBArticle::published()
            ->orderBy('published_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('knowledge-base.index', compact('categories', 'popularArticles', 'recentArticles'));
    }
    
    /**
     * Display articles in a category
     */
    public function category($slug)
    {
        $category = KBCategory::where('slug', $slug)
            ->active()
            ->firstOrFail();
        
        $articles = $category->publishedArticles()
            ->with(['author', 'tags'])
            ->paginate(20);
        
        // Get subcategories
        $subcategories = $category->children()
            ->active()
            ->withCount(['articles' => function($query) {
                $query->published();
            }])
            ->orderBy('order')
            ->get();
        
        // Breadcrumbs
        $breadcrumbs = $this->getCategoryBreadcrumbs($category);
        
        return view('knowledge-base.category', compact('category', 'articles', 'subcategories', 'breadcrumbs'));
    }
    
    /**
     * Display a specific article
     */
    public function article($categorySlug, $articleSlug)
    {
        $article = KBArticle::where('slug', $articleSlug)
            ->published()
            ->with(['category', 'author', 'tags', 'currentVersion'])
            ->firstOrFail();
        
        // Verify category matches
        if ($article->category->slug !== $categorySlug) {
            return redirect()->route('kb.article', [
                'categorySlug' => $article->category->slug,
                'articleSlug' => $article->slug
            ]);
        }
        
        // Log the view
        $userId = auth()->check() ? auth()->id() : null;
        $companyId = auth()->check() && auth()->user()->company_id ? auth()->user()->company_id : null;
        $article->logView($userId, $companyId, request()->route()->getName());
        
        // Get related articles
        $relatedArticles = $this->getRelatedArticles($article);
        
        // Breadcrumbs
        $breadcrumbs = $this->getCategoryBreadcrumbs($article->category);
        $breadcrumbs[] = ['name' => $article->title, 'url' => null];
        
        return view('knowledge-base.article', compact('article', 'relatedArticles', 'breadcrumbs'));
    }
    
    /**
     * Search articles
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $categoryId = $request->get('category');
        $tag = $request->get('tag');
        
        if (empty($query) && empty($tag)) {
            return redirect()->route('kb.index');
        }
        
        $articlesQuery = KBArticle::published()
            ->with(['category', 'author', 'tags']);
        
        // Search by query
        if (!empty($query)) {
            // Use PostgreSQL full-text search if available
            if (DB::connection()->getDriverName() === 'pgsql') {
                $articlesQuery->whereRaw(
                    "to_tsvector('english', title || ' ' || COALESCE(summary, '')) @@ plainto_tsquery('english', ?)",
                    [$query]
                );
            } else {
                // Fallback to LIKE search
                $articlesQuery->where(function($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('summary', 'like', "%{$query}%");
                });
            }
        }
        
        // Filter by category
        if ($categoryId) {
            $articlesQuery->where('category_id', $categoryId);
        }
        
        // Filter by tag
        if ($tag) {
            $articlesQuery->whereHas('tags', function($q) use ($tag) {
                $q->where('slug', $tag);
            });
        }
        
        $articles = $articlesQuery->orderBy('priority', 'desc')
            ->orderBy('published_at', 'desc')
            ->paginate(20);
        
        $categories = KBCategory::active()->orderBy('name')->get();
        
        return view('knowledge-base.search', compact('articles', 'query', 'categories', 'categoryId', 'tag'));
    }
    
    /**
     * Display articles by tag
     */
    public function tag($slug)
    {
        $tag = KBTag::where('slug', $slug)->firstOrFail();
        
        $articles = $tag->publishedArticles()
            ->with(['category', 'author'])
            ->orderBy('published_at', 'desc')
            ->paginate(20);
        
        return view('knowledge-base.tag', compact('tag', 'articles'));
    }
    
    /**
     * Get category breadcrumbs
     */
    private function getCategoryBreadcrumbs($category)
    {
        $breadcrumbs = [];
        $current = $category;
        
        while ($current) {
            array_unshift($breadcrumbs, [
                'name' => $current->name,
                'url' => route('kb.category', $current->slug)
            ]);
            $current = $current->parent;
        }
        
        array_unshift($breadcrumbs, [
            'name' => 'Knowledge Base',
            'url' => route('kb.index')
        ]);
        
        return $breadcrumbs;
    }
    
    /**
     * Get related articles
     */
    private function getRelatedArticles($article)
    {
        // Get explicitly related articles
        $relatedArticles = $article->relatedArticles()
            ->published()
            ->limit(5)
            ->get();
        
        // If we need more, get articles with similar tags
        if ($relatedArticles->count() < 5) {
            $tagIds = $article->tags->pluck('id');
            
            if ($tagIds->isNotEmpty()) {
                $additionalArticles = KBArticle::published()
                    ->where('id', '!=', $article->id)
                    ->whereNotIn('id', $relatedArticles->pluck('id'))
                    ->whereHas('tags', function($q) use ($tagIds) {
                        $q->whereIn('kb_tags.id', $tagIds);
                    })
                    ->limit(5 - $relatedArticles->count())
                    ->get();
                
                $relatedArticles = $relatedArticles->concat($additionalArticles);
            }
        }
        
        // If still need more, get articles from same category
        if ($relatedArticles->count() < 5) {
            $categoryArticles = KBArticle::published()
                ->where('category_id', $article->category_id)
                ->where('id', '!=', $article->id)
                ->whereNotIn('id', $relatedArticles->pluck('id'))
                ->orderBy('priority', 'desc')
                ->limit(5 - $relatedArticles->count())
                ->get();
            
            $relatedArticles = $relatedArticles->concat($categoryArticles);
        }
        
        return $relatedArticles;
    }
    
    /**
     * Get context-aware help articles for current page
     */
    public function contextHelp(Request $request)
    {
        $routeName = $request->get('route');
        $featureKey = $request->get('feature');
        
        $articles = collect();
        
        // Get articles bound to this route
        if ($routeName) {
            $routeArticles = KBArticle::published()
                ->whereHas('bindings', function($q) use ($routeName) {
                    $q->where('route_name', $routeName);
                })
                ->orderBy('priority', 'desc')
                ->limit(3)
                ->get();
            
            $articles = $articles->concat($routeArticles);
        }
        
        // Get articles bound to this feature
        if ($featureKey && $articles->count() < 3) {
            $featureArticles = KBArticle::published()
                ->whereHas('bindings', function($q) use ($featureKey) {
                    $q->where('feature_key', $featureKey);
                })
                ->whereNotIn('id', $articles->pluck('id'))
                ->orderBy('priority', 'desc')
                ->limit(3 - $articles->count())
                ->get();
            
            $articles = $articles->concat($featureArticles);
        }
        
        return response()->json([
            'articles' => $articles->map(function($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->title,
                    'summary' => $article->summary,
                    'url' => route('kb.article', [
                        'categorySlug' => $article->category->slug,
                        'articleSlug' => $article->slug
                    ])
                ];
            })
        ]);
    }
    
    /**
     * API endpoint for help widget contextual articles
     */
    public function getContextualHelp(Request $request)
    {
        $page = $request->get('page', '/');
        $suggested = collect();
        $popular = KBArticle::published()
            ->where('is_featured', true)
            ->orderBy('view_count', 'desc')
            ->limit(3)
            ->get(['id', 'title', 'slug', 'summary as excerpt']);
        
        // Get articles bound to current page
        if ($page) {
            $suggested = KBArticle::whereHas('bindings', function($q) use ($page) {
                $q->where('route_name', 'LIKE', '%' . ltrim($page, '/') . '%');
            })
            ->published()
            ->orderBy('priority', 'desc')
            ->limit(5)
            ->get(['id', 'title', 'slug', 'summary as excerpt']);
        }
        
        // If no bound articles, get articles from related category
        if ($suggested->isEmpty()) {
            // Map common routes to categories
            $categoryMap = [
                '/projects' => 'projects',
                '/sites' => 'sites',
                '/clients' => 'clients',
                '/invoices' => 'invoicing',
                '/estimates' => 'estimates',
                '/assets' => 'assets',
                '/employees' => 'employees',
                '/dashboard' => 'getting-started',
            ];
            
            foreach ($categoryMap as $route => $categorySlug) {
                if (strpos($page, $route) === 0) {
                    $category = KBCategory::where('slug', $categorySlug)->first();
                    if ($category) {
                        $suggested = KBArticle::where('category_id', $category->id)
                            ->published()
                            ->orderBy('priority', 'desc')
                            ->limit(3)
                            ->get(['id', 'title', 'slug', 'summary as excerpt']);
                        break;
                    }
                }
            }
        }
        
        return response()->json([
            'suggested' => $suggested,
            'popular' => $popular
        ]);
    }
    
    /**
     * API endpoint for help widget search
     */
    public function searchApi(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['articles' => []]);
        }
        
        $articlesQuery = KBArticle::published();
        
        // Use PostgreSQL full-text search if available
        if (DB::connection()->getDriverName() === 'pgsql') {
            $articlesQuery->whereRaw(
                "to_tsvector('english', title || ' ' || COALESCE(summary, '')) @@ plainto_tsquery('english', ?)",
                [$query]
            )->orderByRaw(
                "ts_rank(to_tsvector('english', title || ' ' || COALESCE(summary, '')), plainto_tsquery('english', ?)) DESC",
                [$query]
            );
        } else {
            // Fallback to LIKE search
            $articlesQuery->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('summary', 'like', "%{$query}%");
            });
        }
        
        $articles = $articlesQuery
            ->limit(10)
            ->get(['id', 'title', 'slug', 'summary as excerpt']);
        
        return response()->json(['articles' => $articles]);
    }
}