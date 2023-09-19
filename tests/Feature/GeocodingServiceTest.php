<?php

namespace Tests\Feature;

use App\Services\GeocodingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeocodingServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Using Config facade to set an environment variable
        config('services.google_maps_api_key');

    }

    public function testGeocodeReturnsLocation()
    {
        // Mock the Http facade to return a response
        Http::fake([
            'https://maps.googleapis.com/*' => Http::response([
                'status' => 'OK',
                'results' => [
                    [
                        'geometry' => [
                            'location' => [
                                'lat' => 123.456,
                                'lng' => 789.012,
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        // Create an instance of the GeocodingService
        $geocodingService = new GeocodingService();

        // Call the geocode method
        $location = $geocodingService->geocode('Your Test Address');

        // Assert that the location is returned correctly
        $this->assertEquals(['lat' => 123.456, 'lng' => 789.012], $location);
    }

    public function testGeocodeReturnsNullOnError()
    {
        // Mock the Http facade to return an error response
        Http::fake([
            'https://maps.googleapis.com/*' => Http::response([
                'status' => 'ZERO_RESULTS', // Simulate an error status
            ], 404),
        ]);


        // Create an instance of the GeocodingService
        $geocodingService = new GeocodingService();

        // Call the geocode method
        $location = $geocodingService->geocode('Your Test Address');

        // Assert that the method returns null on error
        $this->assertNull($location);
    }

}
