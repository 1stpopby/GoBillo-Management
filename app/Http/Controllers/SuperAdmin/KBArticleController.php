<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\KBArticle;
use App\Models\KBCategory;
use App\Models\KBTag;
use App\Models\KBArticleVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KBArticleController extends Controller
{
    /**
     * Constructor - Ensure only superadmin can access
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'superadmin') {
                abort(403, 'Unauthorized access');
            }
            return $next($request);
        });
    }
    
    /**
     * Display a listing of the articles
     */
    public function index(Request $request)
    {
        $query = KBArticle::with(['category', 'author', 'currentVersion'])
            ->withCount('views');
        
        // Filter by category
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }
        
        $articles = $query->orderBy('priority', 'desc')
            ->orderBy('order')
            ->paginate(20);
        
        $categories = KBCategory::active()->orderBy('name')->get();
        
        return view('superadmin.kb-articles.index', compact('articles', 'categories'));
    }
    
    /**
     * Show the form for creating a new article
     */
    public function create()
    {
        $categories = KBCategory::active()->orderBy('name')->get();
        $tags = KBTag::orderBy('name')->get();
        
        return view('superadmin.kb-articles.create', compact('categories', 'tags'));
    }
    
    /**
     * Store a newly created article
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:kb_articles',
            'category_id' => 'required|exists:kb_categories,id',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'priority' => 'nullable|integer|min:0|max:10',
            'order' => 'nullable|integer',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:kb_tags,id'
        ]);
        
        // Create article
        $articleData = $request->only(['title', 'slug', 'category_id', 'summary', 'status', 'priority', 'order']);
        if (empty($articleData['slug'])) {
            $articleData['slug'] = Str::slug($articleData['title']);
        }
        
        $articleData['created_by'] = auth()->id();
        
        $article = KBArticle::create($articleData);
        
        // Create initial version
        $version = KBArticleVersion::create([
            'article_id' => $article->id,
            'version' => 1,
            'title' => $article->title,
            'content_html' => $request->content,
            'content_plain' => strip_tags($request->content),
            'change_summary' => 'Initial creation',
            'edited_by' => auth()->id()
        ]);
        
        // Update article with current version
        $article->update(['current_version_id' => $version->id]);
        
        // Attach tags
        if ($request->has('tags')) {
            $article->tags()->attach($request->tags);
        }
        
        return redirect()->route('superadmin.kb.articles.index')
            ->with('success', 'Article created successfully');
    }
    
    /**
     * Display the specified article
     */
    public function show(KBArticle $article)
    {
        $article->load(['category', 'author', 'currentVersion', 'tags', 'versions' => function($query) {
            $query->orderBy('version', 'desc');
        }]);
        
        $relatedArticles = $article->relatedArticles()->published()->limit(5)->get();
        
        return view('superadmin.kb-articles.show', compact('article', 'relatedArticles'));
    }
    
    /**
     * Show the form for editing the specified article
     */
    public function edit(KBArticle $article)
    {
        $article->load(['category', 'currentVersion', 'tags']);
        
        $categories = KBCategory::active()->orderBy('name')->get();
        $tags = KBTag::orderBy('name')->get();
        $availableArticles = KBArticle::where('id', '!=', $article->id)
            ->published()
            ->orderBy('title')
            ->get();
        
        return view('superadmin.kb-articles.edit', compact('article', 'categories', 'tags', 'availableArticles'));
    }
    
    /**
     * Update the specified article
     */
    public function update(Request $request, KBArticle $article)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:kb_articles,slug,' . $article->id,
            'category_id' => 'required|exists:kb_categories,id',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'priority' => 'nullable|integer|min:0|max:10',
            'order' => 'nullable|integer',
            'change_summary' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:kb_tags,id',
            'related_articles' => 'nullable|array',
            'related_articles.*' => 'exists:kb_articles,id'
        ]);
        
        // Check if content has changed
        $contentChanged = false;
        if ($article->currentVersion) {
            $contentChanged = $article->currentVersion->content_html !== $request->content ||
                            $article->currentVersion->title !== $request->title;
        }
        
        // Create new version if content changed
        if ($contentChanged) {
            $lastVersion = $article->versions()->orderBy('version', 'desc')->first();
            $newVersionNumber = $lastVersion ? $lastVersion->version + 1 : 1;
            
            $version = KBArticleVersion::create([
                'article_id' => $article->id,
                'version' => $newVersionNumber,
                'title' => $request->title,
                'content_html' => $request->content,
                'content_plain' => strip_tags($request->content),
                'change_summary' => $request->change_summary ?: 'Content updated',
                'edited_by' => auth()->id()
            ]);
            
            $article->current_version_id = $version->id;
        }
        
        // Update article metadata
        $articleData = $request->only(['title', 'slug', 'category_id', 'summary', 'status', 'priority', 'order']);
        if (empty($articleData['slug'])) {
            $articleData['slug'] = Str::slug($articleData['title']);
        }
        
        $articleData['updated_by'] = auth()->id();
        
        $article->update($articleData);
        
        // Update tags
        if ($request->has('tags')) {
            $article->tags()->sync($request->tags);
        }
        
        // Update related articles
        if ($request->has('related_articles')) {
            $article->relatedArticles()->sync($request->related_articles);
        }
        
        return redirect()->route('superadmin.kb.articles.show', $article)
            ->with('success', 'Article updated successfully');
    }
    
    /**
     * Remove the specified article
     */
    public function destroy(KBArticle $article)
    {
        // Delete all related data (versions, views, bindings will cascade)
        $article->tags()->detach();
        $article->relatedArticles()->detach();
        $article->delete();
        
        return redirect()->route('superadmin.kb.articles.index')
            ->with('success', 'Article deleted successfully');
    }
    
    /**
     * Restore a specific version
     */
    public function restoreVersion(KBArticle $article, KBArticleVersion $version)
    {
        if ($version->article_id !== $article->id) {
            abort(404);
        }
        
        // Create a new version based on the restored one
        $lastVersion = $article->versions()->orderBy('version', 'desc')->first();
        $newVersionNumber = $lastVersion ? $lastVersion->version + 1 : 1;
        
        $newVersion = KBArticleVersion::create([
            'article_id' => $article->id,
            'version' => $newVersionNumber,
            'title' => $version->title,
            'content_html' => $version->content_html,
            'content_plain' => $version->content_plain,
            'change_summary' => 'Restored from version ' . $version->version,
            'edited_by' => auth()->id()
        ]);
        
        $article->update([
            'title' => $version->title,
            'current_version_id' => $newVersion->id,
            'updated_by' => auth()->id()
        ]);
        
        return redirect()->route('superadmin.kb.articles.show', $article)
            ->with('success', 'Version restored successfully');
    }
    
    /**
     * Duplicate an article
     */
    public function duplicate(KBArticle $article)
    {
        $newArticle = $article->replicate();
        $newArticle->title = $article->title . ' (Copy)';
        $newArticle->slug = Str::slug($newArticle->title);
        $newArticle->status = 'draft';
        $newArticle->created_by = auth()->id();
        $newArticle->updated_by = auth()->id();
        $newArticle->published_at = null;
        $newArticle->view_count = 0;
        $newArticle->save();
        
        // Copy current version
        if ($article->currentVersion) {
            $version = KBArticleVersion::create([
                'article_id' => $newArticle->id,
                'version' => 1,
                'title' => $newArticle->title,
                'content_html' => $article->currentVersion->content_html,
                'content_plain' => $article->currentVersion->content_plain,
                'change_summary' => 'Duplicated from article #' . $article->id,
                'edited_by' => auth()->id()
            ]);
            
            $newArticle->update(['current_version_id' => $version->id]);
        }
        
        // Copy tags
        $newArticle->tags()->attach($article->tags->pluck('id'));
        
        return redirect()->route('superadmin.kb.articles.edit', $newArticle)
            ->with('success', 'Article duplicated successfully');
    }
}