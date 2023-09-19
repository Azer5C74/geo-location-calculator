# Geo Location Calculator

The Geo Location Calculator is a Laravel-based application that resolves geolocations for a list of addresses and calculates the distance in kilometers from each address to a specific location. It also provides the ability to export the results to a CSV file.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Running the Application](#running-the-application)
- [Testing](#testing)

## Features

- Resolve geolocations for a list of addresses.
- Calculate the distance in kilometers from each address to a specific location.
- Export the results to a CSV file.

## Requirements

To run the Geo Location Calculator, you need to have the following software installed on your system:

- PHP 8
- Composer
- Laravel 8
- A web server (e.g., Apache, Nginx)
- MySQL or another compatible database system

## Installation

1. Clone the repository to your local machine:

   ```bash
   git clone git@github.com:Azer5C74/geo-location-calculator.git

2. Navigate to the project directory
    ```
    cd geo-location-calculator
    ```
3. Install the project dependencies
    ```
    composer install
    ```
4. Create your copy of the .env file based on .env.example
    ```
    cp .env.example .env
    ```
5. Generate application key
    ```
   php artisan key:generate
   ```

## Configuration
Before running the application, you need to configure the following settings in your .env file:
    Google Maps API key (GOOGLE_MAPS_API_KEY) for geocoding. You can obtain an API key from the Google Cloud Console.

## Usage
### Running the Application

To run the Geo Location Calculator, follow these steps:

1. Start your web server to serve the Laravel application.

2. Run the following command to start the development server:
    ```
    php artisan serve
    ```
3. Access the application in your web browser at http://localhost:8000.
4. In the config/addresses file you will find two arrays one for the references the start address and the addresses array
is for the - end addresses.
5. Make a call to http://localhost:8000/calculate-distances endpoint to trigger the service and get the results.
6. You will see the results dumped to the console without stopping the server and a new file within the public/csv file
will be created to save the output.
7. To run the unit tests
   ```
   php artisan test
   ```
