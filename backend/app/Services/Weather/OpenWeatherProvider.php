<?php

namespace App\Services\Weather;

use RuntimeException;
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
        // Em produção, as credenciais virão via config/env
        $response = Http::get($baseUrl ?? 'https://api.openweathermap.org/data/2.5/weather', [
            'lat' => $latitude,
            'lon' => $longitude,
            'appid' => $this->apiKey,
            'units' => 'metric',
            'lang' => 'pt_br'
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Falha ao consumir provedor de clima externo.');
        }

        $data = $response->json();

        // Normalização dos dados: independente da API, o nosso motor recebe este formato
        return [
            'temperature' => (float) ($data['main']['temp'] ?? 0),
            'feels_like' => (float) ($data['main']['feels_like'] ?? 0),
            'humidity' => (int) ($data['main']['humidity'] ?? 0),
            'wind_speed' => (float) (($data['wind']['speed'] ?? 0) * 3.6), // Convertendo m/s para km/h
            'rain_probability' => (int) (($data['pop'] ?? 0) * 100), // Algumas APIs trazem de 0 a 1
            'uv_index' => (float) ($data['current']['uvi'] ?? 0.0),
        ];
    }
}