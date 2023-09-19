<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GeocodingService
{
    /**
     * @param $address
     * Send call to Google Maps APi to resolve the provided address and send back its
     * @return mixed|null
     */
    public function geocode($address): array|null
    {

        // Define a unique cache key for each address
        $cacheKey = 'geocode_' . md5($address);

        // Attempt to retrieve the data from the cache
        $cachedData = Cache::get($cacheKey);

        if ($cachedData !== null) {
            return $cachedData;
        }

        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $address,
            'key' => config('services.google_maps_api_key'),
        ]);
        $data = $response->json();

        if ($data['status'] === 'OK') {
            return $data['results'][0]['geometry']['location'];
        }

        return null;
    }
}
