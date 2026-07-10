<?php

namespace Tests\Unit;

use App\Enums\EventType;
use App\Enums\RiskLevel;
use PHPUnit\Framework\TestCase;
use App\Services\RiskCalculatorService;

class RiskCalculatorServiceTest extends TestCase
{
    protected RiskCalculatorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RiskCalculatorService();
    }

    /** @test */
    public function it_calculates_low_risk_for_perfect_outdoor_weather()
    {
        $weatherData = [
            'temperature' => 22.0,
            'feels_like' => 22.0,
            'humidity' => 40, // 40 * 0.15 = 6
            'wind_speed' => 10.0, // (10/80)*100 = 12.5 * 0.25 = 3.125
            'rain_probability' => 0, // 0 * 0.40 = 0
            'uv_index' => 2.0, // (2/12)*100 = 16.6 * 0.20 = 3.33
        ]; // Total esperado aproximado: ~12-13% (Risco Baixo)

        $result = $this->service->calculate(EventType::OUTDOOR, $weatherData);

        $this->assertEquals(RiskLevel::LOW, $result['risk_level']);
        $this->assertLessThanOrEqual(30, $result['risk_score']);
        $this->assertEmpty($result['recommendations']);
    }

    /** @test */
    public function it_triggers_high_risk_and_insights_for_severe_weather_outdoor()
    {
        $weatherData = [
            'temperature' => 28.0,
            'feels_like' => 32.0,
            'humidity' => 85,
            'wind_speed' => 55.0, // Acima de 50km/h -> Gera Insight de vento
            'rain_probability' => 90, // Alta chance de chuva (90% * 0.40 = 36% direto no score)
            'uv_index' => 9.0, // Acima de 8 -> Gera Insight de UV
        ];

        $result = $this->service->calculate(EventType::OUTDOOR, $weatherData);

        $this->assertEquals(RiskLevel::HIGH, $result['risk_level']);
        $this->assertContains("Risco moderado de precipitação. Recomenda-se a contratação de tendas ou coberturas estruturais.", $result['recommendations']);
        $this->assertContains("Rajadas de vento severas detectadas. Evite o uso de banners aéreos, coberturas leves e palcos descobertos.", $result['recommendations']);
        $this->assertContains("Índice UV extremo. Certifique-se de disponibilizar pontos de hidratação e áreas de sombra para os participantes.", $result['recommendations']);
    }

    /** @test */
    public function it_mitigates_risk_completely_if_event_is_indoor()
    {
        // Mesmo com clima caótico lá fora, o evento indoor protege a operação
        $weatherData = [
            'temperature' => 10.0,
            'feels_like' => 8.0,
            'humidity' => 95,
            'wind_speed' => 70.0,
            'rain_probability' => 100,
            'uv_index' => 0.0,
        ];

        $result = $this->service->calculate(EventType::INDOOR, $weatherData);

        $this->assertEquals(RiskLevel::LOW, $result['risk_level']);
        $this->assertEquals(10, $result['risk_score']);
        $this->assertEmpty($result['recommendations']);
    }
}