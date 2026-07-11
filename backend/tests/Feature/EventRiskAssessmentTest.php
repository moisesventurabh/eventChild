<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventRiskAssessmentTest extends TestCase
{
    use RefreshDatabase; // Garante que o banco de dados de testes seja limpo a cada execução

    /** @test */
    public function test_it_can_create_an_outdoor_event_and_retrieve_the_calculated_risk_report()
    {
        // 1. Criar um utilizador fake para ser o dono do evento
        $user = User::factory()->create();

        // 2. Mocar a resposta da API externa do OpenWeather
        Http::fake([
            'api.openweathermap.org/*' => Http::response([
                'main' => [
                    'temp' => 25.5,
                    'feels_like' => 26.0,
                    'humidity' => 60,
                ],
                'wind' => [
                    'speed' => 5.5, // 5.5 m/s * 3.6 = ~19.8 km/h
                ],
                'pop' => 0.20, // 20% de probabilidade de chuva
                'current' => [
                    'uvi' => 4.5,
                ]
            ], 200)
        ]);

        // 3. Montar o payload do evento (Outdoor)
        $payload = [
            'user_id' => $user->id,
            'name' => 'Festival Tecnológico de BH',
            'description' => 'Evento ao ar livre na Esplanada do Mineirão.',
            'city' => 'Belo Horizonte',
            'state' => 'MG',
            'country' => 'BRA',
            'latitude' => -19.860012,
            'longitude' => -43.971234,
            'event_type' => 'outdoor',
            'start_at' => now()->addDays(2)->toDateTimeString(),
        ];

        // 4. No futuro, dispararemos isso via POST /api/events. 
        // Para este teste de integração da camada de persistência/lógica, salvamos diretamente e rodamos o motor:
        $event = Event::create($payload);

        $this->assertDatabaseHas('events', [
            'name' => 'Festival Tecnológico de BH',
            'city' => 'Belo Horizonte'
        ]);

        // 5. Instanciar o motor e calcular usando nosso mock amarrado ao container
        $provider = app(\App\Contracts\WeatherProviderInterface::class);
        $weatherData = $provider->getWeatherCoordinates($event->latitude, $event->longitude);
        
        $calculator = app(\App\Services\RiskCalculatorService::class);
        $assessment = $calculator->calculate($event->event_type, $weatherData);

        // 6. Validar asserções de negócio estruturadas
        $this->assertArrayHasKey('risk_score', $assessment);
        $this->assertArrayHasKey('risk_level', $assessment);
        $this->assertArrayHasKey('recommendations', $assessment);
        
        // Com POp de 20%, o risco deve ser classificado como LOW ou MODERATE, nunca HIGH
        $this->assertNotEquals(\App\Enums\RiskLevel::HIGH, $assessment['risk_level']);
    }
}