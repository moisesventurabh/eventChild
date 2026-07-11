<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\RiskCalculatorService;
use App\Http\Requests\StoreEventRequest;
use App\Contracts\WeatherProviderInterface;

class EventController extends Controller
{
    public function __construct(
        protected WeatherProviderInterface $weatherProvider,
        protected RiskCalculatorService $riskCalculator
    ) {}


    public function store(StoreEventRequest $request): JsonResponse
    {
        try {
            $event = DB::transaction(function () use ($request) {

                $data = $request->validated();
                $data['user_id'] = $request->user()?->id ?? 1; // Fallback para dev local

                $event = Event::create($data);

                $weatherData = $this->weatherProvider->getWeatherCoordinates(
                    $event->latitude,
                    $event->longitude
                );

                $assessment = $this->riskCalculator->calculate($event->event_type, $weatherData);

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
                    'cached_until' => now()->addHours(2), // <- Adicione esta linha para satisfazer a constraint
                    'raw_data' => $weatherData,
                ]);

                return $event;
            });

            return response()->json([
                'message' => 'Evento criado com sucesso e risco avaliado.',
                'data' => $event->load('weatherReports'),
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Falha ao processar avaliação de risco do evento.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    
    public function show(Event $event): JsonResponse
    {
        try {
            // Carrega o evento junto com os relatórios ordenados pelo mais recente
            $event->load(['weatherReports' => function ($query) {
                $query->latest();
            }]);

            return response()->json([
                'data' => $event
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Falha ao recuperar os detalhes do evento.',
                'details' => $e->getMessage()
            ], 500);
        }
    }


}