<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'section',
        'target',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get footer links grouped by section
     */
    public static function getGroupedLinks()
    {
        return static::where('is_active', true)
            ->orderBy('section')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('section');
    }

    /**
     * Get links for a specific section
     */
    public static function getBySection($section)
    {
        return static::where('section', $section)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Available sections
     */
    public static function getSections()
    {
        return [
            'product' => 'Product',
            'company' => 'Company',
            'support' => 'Support',
            'legal' => 'Legal',
        ];
    }

    /**
     * Get section display name
     */
    public function getSectionNameAttribute()
    {
        $sections = self::getSections();
        return $sections[$this->section] ?? $this->section;
    }
}