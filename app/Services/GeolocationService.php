<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeolocationService
{
    const EARTH_RADIUS_METERS = 6371000; // Earth's radius in meters
    const MAX_ALLOWED_DISTANCE = 500; // 500 meters

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    public function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1) * cos($lat2) *
             sin($deltaLon / 2) * sin($deltaLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_METERS * $c;
    }

    /**
     * Get coordinates from postcode using Google Geocoding API
     */
    public function getCoordinatesFromPostcode($postcode): ?array
    {
        $apiKey = SystemSetting::get('google_maps_api_key');
        
        if (!$apiKey) {
            Log::error('Google Maps API key not configured');
            return null;
        }

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $postcode . ', UK',
                'key' => $apiKey,
                'region' => 'uk'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'OK' && !empty($data['results'])) {
                    $location = $data['results'][0]['geometry']['location'];
                    
                    return [
                        'latitude' => $location['lat'],
                        'longitude' => $location['lng'],
                        'formatted_address' => $data['results'][0]['formatted_address']
                    ];
                }
            }

            Log::warning('Failed to geocode postcode', [
                'postcode' => $postcode,
                'response' => $response->json()
            ]);

        } catch (\Exception $e) {
            Log::error('Geocoding API error', [
                'postcode' => $postcode,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Get address from coordinates using Google Reverse Geocoding API
     */
    public function getAddressFromCoordinates($latitude, $longitude): ?string
    {
        $apiKey = SystemSetting::get('google_maps_api_key');
        
        if (!$apiKey) {
            Log::error('Google Maps API key not configured');
            return null;
        }

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'latlng' => $latitude . ',' . $longitude,
                'key' => $apiKey,
                'region' => 'uk'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'OK' && !empty($data['results'])) {
                    return $data['results'][0]['formatted_address'];
                }
            }

        } catch (\Exception $e) {
            Log::error('Reverse geocoding API error', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Validate if operative is within allowed distance from project location
     */
    public function validateOperativeLocation($operativeLatitude, $operativeLongitude, $projectLatitude, $projectLongitude): array
    {
        if (!$operativeLatitude || !$operativeLongitude || !$projectLatitude || !$projectLongitude) {
            return [
                'valid' => false,
                'distance' => null,
                'error' => 'Missing location coordinates'
            ];
        }

        $distance = $this->calculateDistance(
            $operativeLatitude, 
            $operativeLongitude, 
            $projectLatitude, 
            $projectLongitude
        );

        $isValid = $distance <= self::MAX_ALLOWED_DISTANCE;

        return [
            'valid' => $isValid,
            'distance' => round($distance, 2),
            'max_distance' => self::MAX_ALLOWED_DISTANCE,
            'error' => $isValid ? null : "You are {$distance}m from the project site. You must be within " . self::MAX_ALLOWED_DISTANCE . "m to clock in/out."
        ];
    }

    /**
     * Update project coordinates from postcode
     */
    public function updateProjectCoordinates($project): bool
    {
        if (!$project->postcode) {
            return false;
        }

        $coordinates = $this->getCoordinatesFromPostcode($project->postcode);
        
        if ($coordinates) {
            $project->update([
                'latitude' => $coordinates['latitude'],
                'longitude' => $coordinates['longitude']
            ]);
            
            return true;
        }

        return false;
    }

    /**
     * Test Google Maps API connection
     */
    public function testConnection(): array
    {
        $apiKey = SystemSetting::get('google_maps_api_key');
        
        if (!$apiKey) {
            return [
                'success' => false,
                'message' => 'Google Maps API key not configured'
            ];
        }

        try {
            // Test with a simple geocoding request
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => 'London, UK',
                'key' => $apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'OK') {
                    return [
                        'success' => true,
                        'message' => 'Google Maps API connection successful'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'API Error: ' . ($data['error_message'] ?? $data['status'])
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'HTTP Error: ' . $response->status()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ];
        }
    }
}
