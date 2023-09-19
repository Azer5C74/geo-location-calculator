<?php

namespace Tests\Feature;

use App\Http\Controllers\GeoLocationController;
use App\Services\GeocodingService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GeoLocationControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        // Mock the GeocodingService
        $this->geocodingServiceMock = $this->mock(GeocodingService::class);

        // Set up test addresses in the configuration
        Config::set('addresses', [
            'referenceAddresses' => [
                [
                    'address' => 'Reference Address 1',
                ],
            ],
            'addresses' => [
                [
                    'name' => 'Address 1',
                    'address' => 'Address 1',
                ],
                [
                    'name' => 'Address 2',
                    'address' => 'Address 2',
                ],
            ],
        ]);
    }

    public function testCalculateDistances()
    {
        // Mock the geocode method to return predefined values
        $this->geocodingServiceMock->shouldReceive('geocode')
            ->with('Reference Address 1')
            ->andReturn(['lat' => 1.23, 'lng' => 4.56]);
        $this->geocodingServiceMock->shouldReceive('geocode')
            ->with('Address 1')
            ->andReturn(['lat' => 2.34, 'lng' => 5.67]);
        $this->geocodingServiceMock->shouldReceive('geocode')
            ->with('Address 2')
            ->andReturn(['lat' => 3.45, 'lng' => 6.78]);

        // Call the calculateDistances method
        $response = $this->get('/calculate-distances');

        // Assert that the response is successful
        $response->assertStatus(200);

    }

    public function testExportToCSV()
    {
        // Create a test data array
        $testData = [
            [
                'name' => 'Test 1',
                'address' => 'Test Address 1',
                'distance' => '1.23 km',
            ],
            [
                'name' => 'Test 2',
                'address' => 'Test Address 2',
                'distance' => '2.34 km',
            ],
        ];


        // Create an instance of the GeoLocationController
        $controller = new GeoLocationController(new GeocodingService());

        // Call the exportToCSV method with test data
        $csvFileName = $controller->exportToCSV($testData);
        $csvFilePath = public_path('csv');

        $this->assertTrue(file_exists($csvFilePath));

        $csvFileContents = file_get_contents($csvFileName);
        // Define the expected CSV file content
        $expectedCsvContent = "Sortnumber,Distance,Name,Address\n1,1.23 km,Test 1,Test Address 1\n2,2.34 km,Test 2,Test Address 2\n";

        $this->assertEquals($expectedCsvContent, $csvFileContents);
    }

}
