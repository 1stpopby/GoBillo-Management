<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SettingsController extends Controller
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
     * Display system settings
     */
    public function index()
    {
        $settings = [
            'general' => SystemSetting::byGroup('general')->ordered()->get(),
            'payment' => SystemSetting::byGroup('payment')->ordered()->get(),
            'integrations' => SystemSetting::byGroup('integrations')->ordered()->get(),
        ];

        return view('superadmin.settings.index', compact('settings'));
    }

    /**
     * Update system settings
     */
    public function update(Request $request)
    {
        // Get current settings to check what's enabled
        $enableStripe = $request->boolean('stripe_enabled');
        
        // Build validation rules conditionally
        $rules = [];
        
        // Only require Stripe keys if Stripe is enabled
        if ($enableStripe) {
            $rules['stripe_public_key'] = 'required|string';
            $rules['stripe_secret_key'] = 'required|string';
        }
        
        // Google integrations are optional - no conditional validation needed
        // since they don't have an enable/disable toggle
        
        // Validate with conditional rules
        $request->validate($rules);

        $settings = $request->except(['_token', '_method']);

        foreach ($settings as $key => $value) {
            SystemSetting::set($key, $value);
        }

        return redirect()->route('superadmin.settings.index')
                        ->with('success', 'Settings updated successfully.');
    }

    /**
     * Test Stripe connection
     */
    public function testStripe(Request $request)
    {
        try {
            $secretKey = SystemSetting::get('stripe_secret_key');
            
            if (!$secretKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stripe secret key not configured.'
                ]);
            }

            // Test Stripe API connection
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secretKey,
                'Content-Type' => 'application/x-www-form-urlencoded'
            ])->get('https://api.stripe.com/v1/balance');

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stripe connection successful!',
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Stripe connection failed: ' . $response->body()
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error testing Stripe connection: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Test Google services
     */
    public function testGoogle(Request $request)
    {
        try {
            $service = $request->input('service', 'maps');
            
            switch ($service) {
                case 'maps':
                    return $this->testGoogleMaps();
                case 'recaptcha':
                    return $this->testGoogleRecaptcha();
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Unknown service specified.'
                    ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error testing Google service: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Test Google Maps API
     */
    private function testGoogleMaps()
    {
        $apiKey = SystemSetting::get('google_maps_api_key');
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Google Maps API key not configured.'
            ]);
        }

        // Test with a simple geocoding request
        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => 'London, UK',
            'key' => $apiKey
        ]);

        if ($response->successful()) {
            $data = $response->json();
            
            if ($data['status'] === 'OK') {
                return response()->json([
                    'success' => true,
                    'message' => 'Google Maps API connection successful!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Google Maps API error: ' . $data['status']
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to Google Maps API.'
            ]);
        }
    }

    /**
     * Test Google reCAPTCHA
     */
    private function testGoogleRecaptcha()
    {
        $secretKey = SystemSetting::get('google_recaptcha_secret_key');
        
        if (!$secretKey) {
            return response()->json([
                'success' => false,
                'message' => 'Google reCAPTCHA secret key not configured.'
            ]);
        }

        // This is a basic test - in real implementation you'd need a reCAPTCHA response
        return response()->json([
            'success' => true,
            'message' => 'Google reCAPTCHA configuration appears valid. Test with actual form submission to verify fully.'
        ]);
    }
}