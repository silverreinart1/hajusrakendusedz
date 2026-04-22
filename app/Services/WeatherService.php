<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.openweathermap.org/data/2.5';

    public function __construct()
    {
        $this->apiKey = config('services.openweather.key');
    }

    public function getByCity(string $city): array
    {
        $cacheKey = 'weather_' . strtolower(str_replace(' ', '_', $city));

        return Cache::remember($cacheKey, 1800, function () use ($city) {
            $response = Http::get("{$this->baseUrl}/weather", [
                'q' => $city,
                'appid' => $this->apiKey,
                'units' => 'metric',
                'lang' => 'et',
            ]);

            if ($response->failed()) {
                throw new \Exception("Linna '{$city}' ilmaandmeid ei leitud.");
            }

            $data = $response->json();

            return [
                'city' => $data['name'],
                'country' => $data['sys']['country'],
                'temp' => round($data['main']['temp']),
                'feels_like' => round($data['main']['feels_like']),
                'description' => ucfirst($data['weather'][0]['description']),
                'icon' => $data['weather'][0]['icon'],
                'icon_url' => "https://openweathermap.org/img/wn/{$data['weather'][0]['icon']}@2x.png",
                'humidity' => $data['main']['humidity'],
                'wind_speed' => round($data['wind']['speed'] * 3.6, 1), // m/s -> km/h
                'pressure' => $data['main']['pressure'],
                'visibility' => isset($data['visibility']) ? round($data['visibility'] / 1000, 1) : null,
                'sunrise' => date('H:i', $data['sys']['sunrise']),
                'sunset' => date('H:i', $data['sys']['sunset']),
                'cached_at' => now()->toIso8601String(),
            ];
        });
    }
}
