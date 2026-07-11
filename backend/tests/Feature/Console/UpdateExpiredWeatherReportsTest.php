<?php

namespace App\Console\Commands;

use Log;
use App\Models\Event;
use Illuminate\Console\Command;



class UpdateExpiredWeatherReports extends Command
{
    protected $signature = 'weather:update-expired';
    protected $description = 'Varre e atualiza relatórios climáticos e de risco expirados no cache dos eventos ativos';

    /**
     * Executa a lógica de negócio injetando os serviços reais do projeto.
     */
    public function handle($weatherProvider, $riskEngine) // <-- Coloque os Type-hints exatos aqui (ex: OpenWeatherService $weatherProvider, RiskEngine $riskEngine)
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

        foreach ($eventsToUpdate as $event) {
            try {
                $this->comment("Atualizando evento: {$event->name} (#{$event->id})...");

                // Chame os métodos idênticos aos do seu EventController
                // Exemplo se for getForecast ou algo similar:
                $weatherData = $weatherProvider->getForecast(
                    $event->latitude,
                    $event->longitude,
                    $event->start_at
                );

                $assessment = $riskEngine->calculate($weatherData, $event->event_type);

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
            }
        }

        $this->info('Varredura e atualização finalizadas!');
        return self::SUCCESS;
    }
}