<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'template',
        'is_published',
        'show_in_footer',
        'footer_section',
        'sort_order',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'show_in_footer' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Generate slug from title
     */
    public static function generateSlug($title, $id = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get published pages
     */
    public static function published()
    {
        return static::where('is_published', true)
            ->where(function ($query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Get pages for footer
     */
    public static function getFooterPages()
    {
        return static::published()
            ->where('show_in_footer', true)
            ->orderBy('footer_section')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('footer_section');
    }

    /**
     * Get page by slug
     */
    public static function findBySlug($slug)
    {
        return static::published()->where('slug', $slug)->first();
    }

    /**
     * Get available footer sections
     */
    public static function getFooterSections()
    {
        return [
            'product' => 'Product',
            'company' => 'Company',
            'support' => 'Support',
            'legal' => 'Legal',
        ];
    }

    /**
     * Get available templates
     */
    public static function getTemplates()
    {
        return [
            'default' => 'Default Page',
            'legal' => 'Legal Page',
            'simple' => 'Simple Page',
        ];
    }

    /**
     * Get the page URL
     */
    public function getUrlAttribute()
    {
        return route('page.show', $this->slug);
    }

    /**
     * Get the page's meta title or fallback to title
     */
    public function getMetaTitleAttribute($value)
    {
        return $value ?: $this->title;
    }

    /**
     * Get excerpt or generate from content
     */
    public function getExcerptAttribute($value)
    {
        if ($value) {
            return $value;
        }

        // Generate excerpt from content
        $text = strip_tags($this->content);
        return Str::limit($text, 160);
    }

    /**
     * Boot method to handle slug generation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            if (!$page->slug) {
                $page->slug = static::generateSlug($page->title);
            }
            if (!$page->published_at) {
                $page->published_at = now();
            }
        });

        static::updating(function ($page) {
            if ($page->isDirty('title') && !$page->isDirty('slug')) {
                $page->slug = static::generateSlug($page->title, $page->id);
            }
        });
    }
}