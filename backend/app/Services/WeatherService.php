<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;

class WeatherService
{
    public function getForecast(string $city): ?array
    {
        $geoResponse = Http::get('https://geocoding-api.open-meteo.com/v1/search', [
            'name' => $city,
            'count' => 5,
            'language' => 'es',
        ]);

        if (!$geoResponse->successful() || empty($geoResponse->json()['results'])) {
            return null;
        }

        $results = $geoResponse->json()['results'];
        $bestMatch = $this->findBestCityMatch($results, $city);

        if (!$bestMatch) {
            return null;
        }

        $weatherResponse = Http::get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $bestMatch['latitude'],
            'longitude' => $bestMatch['longitude'],
            'daily' => 'temperature_2m_max,precipitation_sum',
            'timezone' => 'auto',
            'forecast_days' => 2 
        ]);

        if (!$weatherResponse->successful()) {
            return null;
        }

        return [
            'weather' => $weatherResponse->json(),
            'location' => $bestMatch
        ];
    }

    protected function findBestCityMatch(array $results, string $searchCity): ?array
    {
        $searchCity = strtolower($searchCity);
        
        foreach ($results as $city) {
            if (strtolower($city['name']) === $searchCity) {
                return $city;
            }
        }
        return $results[0] ?? null;
    }
}
