<?php

// Static content for immediate deployment without database
$GLOBALS['static_site_content'] = [
    'landing_hero_title' => 'Professional Construction Management Made Simple',
    'landing_hero_subtitle' => 'Streamline your construction projects with our comprehensive management platform. From project planning to team collaboration, we\'ve got you covered.',
    'landing_hero_cta_text' => 'Start Free Trial',
    'landing_features_title' => 'Everything You Need to Manage Construction Projects',
    'landing_pricing_title' => 'Choose Your Plan',
    'get_started_hero_title' => 'Transform Your Construction Business',
    'get_started_hero_subtitle' => 'Join thousands of construction companies using GoBillo to streamline operations, manage projects, and grow their business.',
    'get_started_form_title' => 'Get Started Today',
    'get_started_form_subtitle' => 'Create your company account and start your free trial',
    'footer_company_description' => 'The complete construction management platform trusted by thousands of construction professionals worldwide.',
    'footer_copyright' => 'GoBillo. All rights reserved.',
    'footer_tagline' => 'Made with ❤️ for construction professionals',
];

// Override SiteContent model temporarily
if (!class_exists('App\Models\SiteContent')) {
    class_alias('App\Models\StaticSiteContent', 'App\Models\SiteContent');
}

class StaticSiteContent {
    public static function get($key, $default = null) {
        return $GLOBALS['static_site_content'][$key] ?? $default;
    }
}
