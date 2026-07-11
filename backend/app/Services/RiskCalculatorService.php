<?php

namespace App\Services;

use App\Enums\EventType;
use App\Enums\RiskLevel;

class RiskCalculatorService
{
    /**
     * Calcula o Score de Risco (0 a 100) e gera recomendações estruturadas.
     */
    public function calculate(EventType $type, array $weatherData): array
    {
        $score = 0;
        $recommendations = [];

        if ($type === EventType::OUTDOOR) {
            // 1. Probabilidade de Chuva (Peso 40%)
            $score += ($weatherData['rain_probability'] ?? 0) * 0.40;

            // 2. Velocidade do Vento (Peso 25%) - teto estipulado em 80km/h para atingir 100% do peso
            $windSpeed = $weatherData['wind_speed'] ?? 0;
            $windFactor = min(($windSpeed / 80) * 100, 100);
            $score += $windFactor * 0.25;

            // Penalidade Multiplicadora para rajadas extremas acima de 40 km/h
            if ($windSpeed > 40) {
                $score = min($score * 1.2, 100);
            }

            // 3. Índice UV (Peso 20%) - teto estipulado em índice 12
            $uv = $weatherData['uv_index'] ?? 0;
            $uvFactor = min(($uv / 12) * 100, 100);
            $score += $uvFactor * 0.20;

            // 4. Umidade (Peso 15%)
            $humidity = $weatherData['humidity'] ?? 0;
            $score += $humidity * 0.15;
        } else {
            // Eventos INDOOR mitigam riscos climáticos severamente por padrão de infraestrutura
            $score = 10; 
        }

        $score = (int) round($score);
        $level = $this->determineRiskLevel($score);
        $recommendations = $this->generateInsights($type, $weatherData);

        return [
            'risk_score' => $score,
            'risk_level' => $level,
            'recommendations' => $recommendations
        ];
    }

    protected function determineRiskLevel(int $score): RiskLevel
    {
        return match (true) {
            $score <= 30 => RiskLevel::LOW,
            $score <= 60 => RiskLevel::MODERATE,
            default => RiskLevel::HIGH,
        };
    }

    protected function generateInsights(EventType $type, array $weatherData): array
    {
        $insights = [];

        if ($type === EventType::OUTDOOR) {
            if (($weatherData['rain_probability'] ?? 0) > 40) {
                $insights[] = "Risco moderado de precipitação. Recomenda-se a contratação de tendas ou coberturas estruturais.";
            }
            if (($weatherData['wind_speed'] ?? 0) > 50) {
                $insights[] = "Rajadas de vento severas detectadas. Evite o uso de banners aéreos, coberturas leves e palcos descobertos.";
            }
            if (($weatherData['uv_index'] ?? 0) >= 8) {
                $insights[] = "Índice UV extremo. Certifique-se de disponibilizar pontos de hidratação e áreas de sombra para os participantes.";
            }
        }

        return $insights;
    }
}