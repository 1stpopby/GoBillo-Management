<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'page',
        'section',
        'type',
        'label',
        'value',
        'default_value',
        'description',
        'options',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get content by key with fallback to default
     */
    public static function get($key, $default = null)
    {
        $content = static::where('key', $key)->where('is_active', true)->first();
        
        if (!$content) {
            return $default;
        }
        
        return $content->value ?: $content->default_value ?: $default;
    }

    /**
     * Get all content for a specific page
     */
    public static function getPageContent($page)
    {
        return static::where('page', $page)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Get all content for a specific page and section
     */
    public static function getSectionContent($page, $section)
    {
        return static::where('page', $page)
            ->where('section', $section)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Set content value
     */
    public static function set($key, $value)
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get content with metadata for admin
     */
    public static function getForAdmin($page = null)
    {
        $query = static::orderBy('page')->orderBy('section')->orderBy('sort_order');
        
        if ($page) {
            $query->where('page', $page);
        }
        
        return $query->get()->groupBy(['page', 'section']);
    }
}