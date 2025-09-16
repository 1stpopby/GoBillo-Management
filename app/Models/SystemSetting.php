<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'is_encrypted',
        'is_required',
        'validation_rules',
        'sort_order',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
        'is_required' => 'boolean',
        'validation_rules' => 'array',
    ];

    /**
     * Accessors
     */
    public function getValueAttribute($value)
    {
        if (!$value) return null;
        
        // Decrypt if encrypted
        if ($this->is_encrypted) {
            try {
                $value = Crypt::decryptString($value);
            } catch (\Exception $e) {
                return null;
            }
        }
        
        // Cast to appropriate type
        return match($this->type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'float' => (float) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Mutators
     */
    public function setValueAttribute($value)
    {
        if ($value === null) {
            $this->attributes['value'] = null;
            return;
        }
        
        // Convert to string based on type
        $stringValue = match($this->type) {
            'boolean' => $value ? '1' : '0',
            'json' => json_encode($value),
            default => (string) $value,
        };
        
        // Encrypt if needed
        if ($this->is_encrypted) {
            $stringValue = Crypt::encryptString($stringValue);
        }
        
        $this->attributes['value'] = $stringValue;
    }

    /**
     * Scopes
     */
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('label');
    }

    /**
     * Static methods
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        $setting = static::where('key', $key)->first();
        
        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            // Create new setting if it doesn't exist
            static::create([
                'key' => $key,
                'value' => $value,
                'type' => 'string',
                'group' => 'general',
                'label' => ucwords(str_replace('_', ' ', $key)),
            ]);
        }
        
        return $setting;
    }

    public static function getByGroup($group)
    {
        return static::byGroup($group)->ordered()->get()->pluck('value', 'key');
    }

    public static function getStripeSettings()
    {
        return [
            'public_key' => static::get('stripe_public_key'),
            'secret_key' => static::get('stripe_secret_key'),
            'webhook_secret' => static::get('stripe_webhook_secret'),
            'enabled' => static::get('stripe_enabled', false),
        ];
    }

    public static function getGoogleSettings()
    {
        return [
            'maps_api_key' => static::get('google_maps_api_key'),
            'recaptcha_site_key' => static::get('google_recaptcha_site_key'),
            'recaptcha_secret_key' => static::get('google_recaptcha_secret_key'),
            'analytics_id' => static::get('google_analytics_id'),
        ];
    }

    public static function seedDefaultSettings()
    {
        $defaultSettings = [
            // Stripe Payment Settings
            [
                'key' => 'stripe_public_key',
                'value' => null,
                'type' => 'string',
                'group' => 'payment',
                'label' => 'Stripe Publishable Key',
                'description' => 'Your Stripe publishable key for processing payments',
                'is_encrypted' => false,
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'stripe_secret_key',
                'value' => null,
                'type' => 'string',
                'group' => 'payment',
                'label' => 'Stripe Secret Key',
                'description' => 'Your Stripe secret key for processing payments',
                'is_encrypted' => true,
                'is_required' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'stripe_webhook_secret',
                'value' => null,
                'type' => 'string',
                'group' => 'payment',
                'label' => 'Stripe Webhook Secret',
                'description' => 'Stripe webhook endpoint secret for secure webhooks',
                'is_encrypted' => true,
                'is_required' => false,
                'sort_order' => 3,
            ],
            [
                'key' => 'stripe_enabled',
                'value' => false,
                'type' => 'boolean',
                'group' => 'payment',
                'label' => 'Enable Stripe Payments',
                'description' => 'Enable Stripe payment processing',
                'is_encrypted' => false,
                'is_required' => false,
                'sort_order' => 4,
            ],
            
            // Google Services
            [
                'key' => 'google_maps_api_key',
                'value' => null,
                'type' => 'string',
                'group' => 'integrations',
                'label' => 'Google Maps API Key',
                'description' => 'API key for Google Maps integration',
                'is_encrypted' => true,
                'is_required' => false,
                'sort_order' => 1,
            ],
            [
                'key' => 'google_recaptcha_site_key',
                'value' => null,
                'type' => 'string',
                'group' => 'integrations',
                'label' => 'Google reCAPTCHA Site Key',
                'description' => 'Site key for Google reCAPTCHA',
                'is_encrypted' => false,
                'is_required' => false,
                'sort_order' => 2,
            ],
            [
                'key' => 'google_recaptcha_secret_key',
                'value' => null,
                'type' => 'string',
                'group' => 'integrations',
                'label' => 'Google reCAPTCHA Secret Key',
                'description' => 'Secret key for Google reCAPTCHA',
                'is_encrypted' => true,
                'is_required' => false,
                'sort_order' => 3,
            ],
            [
                'key' => 'google_analytics_id',
                'value' => null,
                'type' => 'string',
                'group' => 'integrations',
                'label' => 'Google Analytics ID',
                'description' => 'Google Analytics tracking ID',
                'is_encrypted' => false,
                'is_required' => false,
                'sort_order' => 4,
            ],
            
            // General Settings
            [
                'key' => 'app_name',
                'value' => 'GoBillo',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Application Name',
                'description' => 'The name of your application',
                'is_encrypted' => false,
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'app_url',
                'value' => config('app.url'),
                'type' => 'string',
                'group' => 'general',
                'label' => 'Application URL',
                'description' => 'The base URL of your application',
                'is_encrypted' => false,
                'is_required' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'support_email',
                'value' => 'support@gobillo.com',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Support Email',
                'description' => 'Email address for customer support',
                'is_encrypted' => false,
                'is_required' => true,
                'sort_order' => 3,
            ],
        ];
        
        foreach ($defaultSettings as $setting) {
            static::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}