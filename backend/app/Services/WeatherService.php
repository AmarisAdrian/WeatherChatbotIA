<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;

class WeatherService
{
    public function getForecast(string $city): ?array
    {
        $geoResponse = Http::get('https://geocoding-api.open-meteo.com/v1/search', [
            'name' => $city,
            'count' => 1,
            'language' => 'es',
        ]);

        if (!$geoResponse->successful()) {
            return null;
        }

        $geo = $geoResponse->json();
        if (empty($geo['results'][0])) {
            return null;
        }

        $lat = $geo['results'][0]['latitude'];
        $lon = $geo['results'][0]['longitude'];

        $weatherResponse = Http::get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $lat,
            'longitude' => $lon,
            'daily' => 'temperature_2m_max,precipitation_sum',
            'timezone' => 'auto'
        ]);

        if (!$weatherResponse->successful()) {
            return null;
        }

        return $weatherResponse->json();
    }
}
