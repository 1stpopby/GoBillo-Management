<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\KBCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KBCategoryController extends Controller
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
     * Display a listing of the categories
     */
    public function index()
    {
        $categories = KBCategory::with('parent')
            ->withCount('articles')
            ->orderBy('parent_id')
            ->orderBy('order')
            ->get();
        
        // Build category tree
        $categoryTree = $this->buildCategoryTree($categories);
        
        return view('superadmin.kb-categories.index', compact('categoryTree'));
    }
    
    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        $categories = KBCategory::active()->orderBy('name')->get();
        return view('superadmin.kb-categories.create', compact('categories'));
    }
    
    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:kb_categories',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:kb_categories,id',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);
        
        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        $category = KBCategory::create($data);
        
        return redirect()->route('superadmin.kb-categories.index')
            ->with('success', 'Category created successfully');
    }
    
    /**
     * Show the form for editing the specified category
     */
    public function edit(KBCategory $category)
    {
        $categories = KBCategory::where('id', '!=', $category->id)
            ->active()
            ->orderBy('name')
            ->get();
            
        return view('superadmin.kb-categories.edit', compact('category', 'categories'));
    }
    
    /**
     * Update the specified category
     */
    public function update(Request $request, KBCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:kb_categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:kb_categories,id|not_in:' . $category->id,
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);
        
        // Prevent circular parent reference
        if ($request->parent_id) {
            $parent = KBCategory::find($request->parent_id);
            $ancestors = [];
            while ($parent) {
                if ($parent->id === $category->id) {
                    return back()->withErrors(['parent_id' => 'Cannot set a descendant as parent']);
                }
                $ancestors[] = $parent->id;
                $parent = $parent->parent;
            }
        }
        
        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        $category->update($data);
        
        return redirect()->route('superadmin.kb-categories.index')
            ->with('success', 'Category updated successfully');
    }
    
    /**
     * Remove the specified category
     */
    public function destroy(KBCategory $category)
    {
        // Check if category has articles
        if ($category->articles()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete category with articles']);
        }
        
        // Check if category has children
        if ($category->children()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete category with subcategories']);
        }
        
        $category->delete();
        
        return redirect()->route('superadmin.kb-categories.index')
            ->with('success', 'Category deleted successfully');
    }
    
    /**
     * Update category order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:kb_categories,id',
            'categories.*.order' => 'required|integer'
        ]);
        
        foreach ($request->categories as $categoryData) {
            KBCategory::where('id', $categoryData['id'])
                ->update(['order' => $categoryData['order']]);
        }
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Build hierarchical category tree
     */
    private function buildCategoryTree($categories, $parentId = null)
    {
        $tree = [];
        
        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $category->children = $this->buildCategoryTree($categories, $category->id);
                $tree[] = $category;
            }
        }
        
        return $tree;
    }
}