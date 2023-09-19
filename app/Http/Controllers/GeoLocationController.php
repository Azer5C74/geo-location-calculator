<?php

namespace App\Http\Controllers;

use App\Services\GeocodingService;
use League\Csv\Writer;


/**
 * @OA\Info(
 *     title="Geo Location Calculator ",
 *     version="1.0.0",
 *     description="This API helps to resolve geolocations and calculate the distance in kilometers
 *     from each address to a specific location.",
 *     @OA\Contact(
 *         name="Azer",
 *         email="taboubi.azer@email.com"
 *     )
 * )
 */
class GeoLocationController extends Controller
{
    /**
     * @OA\PathItem(
     *      path="/calculate-distances/",
     *      @OA\Get(
     *          operationId="calculate-dsitances",
     *          @OA\Response(
     *              response=200,
     *              description="Successful response"
     *          ),
     *          @OA\Response(
     *              response=404,
     *              description="Not found"
     *          )
     *      )
     * )
     */
    private GeocodingService $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }

    /**
     * @return string
     */
    public function calculateDistances(): string
    {
        $addresses = config('addresses');
        $results = [];


        foreach ($addresses['referenceAddresses'] as $referenceAddress) {

            // Resolving the reference address and getting its location
            $referenceAddressLocation = $this->geocodingService->geocode($referenceAddress['address']);

            foreach ($addresses['addresses'] as $addressInfo) {
                $address = $addressInfo['address'];
                $name = $addressInfo['name'];

                // Resolving the end address location and getting its location
                $location = $this->geocodingService->geocode($address);

                // Calculating the distance difference between two locations
                $distance = $this->calculateDistance($referenceAddressLocation, $location);
                $results[] = [
                    'address' => $address,
                    'location' => $location,
                    'distance' => $distance,
                    'name' => $name,
                ];
            }
        }

        // Sort results by distance
        $results = $this->sortResultsByDistance($results);

        // Export results to CSV
        return 'Distances calculated and exported to: ' . $this->exportToCSV($results);
    }

    /**
     * @param $data
     * Creates .csv file if not found and overrides it if it's already in the specified directory.
     * @return string
     */
    public function exportToCSV($data): string
    {
        $csvFilePath = public_path('csv');

        if (!file_exists($csvFilePath)) {
            mkdir($csvFilePath, 0755, true);
        }

        // Generate a unique file name by adding an incremental index
        $index = 1;
        $csvFileName = $csvFilePath . DIRECTORY_SEPARATOR . 'distances.csv';
        while (file_exists($csvFileName)) {
            $csvFileName = $csvFilePath . DIRECTORY_SEPARATOR . 'distances_' . $index . '.csv';
            $index++;
        }


        $csvFile = fopen($csvFileName, 'w');
        fputcsv($csvFile, ['Sortnumber', 'Distance', 'Name', 'Address']);

        $sortNumber = 1;
        foreach ($data as $item) {
            fputcsv($csvFile, [
                $sortNumber++,
                $item['distance'] . ' km',
                $item['name'],
                $item['address'],
            ]);
        }

        fclose($csvFile);
        return $csvFileName;
    }

    /**
     * Sorts an array of results by distance.
     *
     * @param array $results
     * @return array
     */
    private function sortResultsByDistance(array $results): array
    {
        usort($results, function ($a, $b) {
            return $a['distance'] - $b['distance'];
        });

        return $results;
    }

    /**
     * @param $location1
     * @param $location2
     * @return float
     */
    private function calculateDistance($location1, $location2): float
    {
        // Calculate the distance between two sets of coordinates (latitude and longitude)
        $lat1 = deg2rad($location1['lat']);
        $lon1 = deg2rad($location1['lng']);
        $lat2 = deg2rad($location2['lat']);
        $lon2 = deg2rad($location2['lng']);

        $earthRadius = 6371; // Radius of the Earth in kilometers

        $latDelta = $lat2 - $lat1;
        $lonDelta = $lon2 - $lon1;

        $distance = 2 * $earthRadius * asin(sqrt(
                pow(sin($latDelta / 2), 2) +
                cos($lat1) * cos($lat2) * pow(sin($lonDelta / 2), 2)
            ));

        return round($distance, 2); // Round to 2 decimal places
    }


}
