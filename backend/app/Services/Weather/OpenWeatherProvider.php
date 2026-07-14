<?php

namespace App\Services\Weather;

use RuntimeException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Contracts\WeatherProviderInterface;

class OpenWeatherProvider implements WeatherProviderInterface
{
    public function __construct(
        protected string $apiKey,
        protected string $baseUrl = 'https://api.openweathermap.org/data/3.0/onecall'
    ) {}

    public function getWeatherCoordinates(float $latitude, float $longitude): array
    {
        $response = Http::get($this->baseUrl, [
            'lat' => $latitude,
            'lon' => $longitude,
            'appid' => $this->apiKey,
            'units' => 'metric',
            'lang' => 'pt_br',
            'exclude' => 'minutely,hourly,alerts'
        ]);

        if ($response->failed()) {
            Log::error('Erro OpenWeather API 3.0:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            throw new RuntimeException('Falha ao consumir provedor de clima externo.');
        }

        $data = $response->json();

        return [
            'temperature' => (float) ($data['current']['temp'] ?? 0),
            'feels_like' => (float) ($data['current']['feels_like'] ?? 0),
            'humidity' => (int) ($data['current']['humidity'] ?? 0),
            'wind_speed' => (float) (($data['current']['wind_speed'] ?? 0) * 3.6), // Convertendo m/s para km/h
            'rain_probability' => (int) (($data['daily'][0]['pop'] ?? 0) * 100), // Percentual real de chuva do dia atual
            'uv_index' => (float) ($data['current']['uvi'] ?? 0.0),
        ];
    }
}
