<?php

namespace App\Console\Commands;

use Log;
use App\Models\Event;
use Illuminate\Console\Command; 
use App\Services\RiskAssessmentEngine;
use App\Services\RiskCalculatorService;
use App\Services\WeatherProviderInterface;
use App\Services\Weather\OpenWeatherProvider;

class UpdateExpiredWeatherReports extends Command
{

    protected $signature = 'weather:update-expired';

    protected $description = 'Varre e atualiza relatórios climáticos e de risco expirados no cache dos eventos ativos';

    public function handle()
    {
        $this->info('Iniciando varredura de relatórios expirados...');

        $eventsToUpdate = Event::where('start_at', '>=', now())
            ->where(function ($query) {
                $query->whereHas('weatherReports', function ($q) {
                    $q->where('cached_until', '<', now());
                })->orWhereDoesntHave('weatherReports');
            })
            ->with('weatherReports')
            ->get();

        if ($eventsToUpdate->isEmpty()) {
            $this->info('Nenhum evento precisa de atualização no momento.');
            return self::SUCCESS;
        }

        $this->info("Encontrado(s) {$eventsToUpdate->count()} evento(s) para atualizar.");

        $weatherProvider = app(OpenWeatherProvider::class);
        $riskEngine = app(RiskCalculatorService::class);

        foreach ($eventsToUpdate as $event) {
            try {
                $this->comment("Atualizando evento: {$event->name} (#{$event->id})...");

                $weatherData = $weatherProvider->getWeatherCoordinates(
                    $event->latitude,
                    $event->longitude
                );

                $assessment = $riskEngine->calculate($event->event_type, $weatherData);
                $event->weatherReports()->create([
                    'temperature' => $weatherData['temperature'] ?? null,
                    'feels_like' => $weatherData['feels_like'] ?? $weatherData['temperature'] ?? null,
                    'humidity' => $weatherData['humidity'] ?? null,
                    'wind_speed' => $weatherData['wind_speed'] ?? null,
                    'rain_probability' => $weatherData['rain_probability'] ?? 0,
                    'uv_index' => $weatherData['uv_index'] ?? null,
                    'risk_score' => $assessment['risk_score'],
                    'risk_level' => $assessment['risk_level'],
                    'recommendations' => $assessment['recommendations'],
                    'cached_until' => now()->addHours(2),
                    'raw_data' => $weatherData,
                ]);

                $this->info("Evento #{$event->id} atualizado com sucesso. Novo Score: {$assessment['risk_score']}");

            } catch (\Exception $e) {
                $this->error("Erro ao atualizar o evento #{$event->id}: {$e->getMessage()}");
                Log::error("Schedule Weather Update Error for event #{$event->id}: " . $e->getMessage());

                $lastValidReport = $event->weatherReports()->latest()->first();

                if ($lastValidReport) {
                    $event->weatherReports()->create([
                        'temperature' => $lastValidReport->temperature,
                        'feels_like' => $lastValidReport->feels_like,
                        'humidity' => $lastValidReport->humidity,
                        'wind_speed' => $lastValidReport->wind_speed,
                        'rain_probability' => $lastValidReport->rain_probability,
                        'uv_index' => $lastValidReport->uv_index,
                        'risk_score' => $lastValidReport->risk_score,
                        'risk_level' => $lastValidReport->risk_level,
                        'recommendations' => $lastValidReport->recommendations,
                        'cached_until' => now()->addHours(2),
                        'raw_data' => array_merge($lastValidReport->raw_data ?? [], ['fallback_applied' => true])
                    ]);

                    $this->warn("Fallback aplicado para o evento #{$event->id}: Mantido o último relatório válido.");
                }
            }
        }

        $this->info('Varredura e atualização finalizadas!');
        return self::SUCCESS;
    }
}