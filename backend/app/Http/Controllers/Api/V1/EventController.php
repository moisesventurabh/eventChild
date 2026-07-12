<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\RiskCalculatorService;
use App\Http\Requests\StoreEventRequest;
use App\Http\Resources\V1\EventResource;
use App\Contracts\WeatherProviderInterface;
use App\Services\Weather\OpenWeatherProvider;


class EventController extends Controller
{
    public function __construct(
        protected WeatherProviderInterface $weatherProvider,
        protected RiskCalculatorService $riskCalculator
    ) {}

    public function index(Request $request)
    {
        try {
            $events = $request->user()->events()
                ->with(['weatherReports' => function ($query) {
                    $query->orderBy('id', 'desc');
                }])
                ->latest()
                ->paginate(15);

            return EventResource::collection($events);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Falha ao recuperar a lista de eventos.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreEventRequest $request): JsonResponse
    {
        try {
            $event = DB::transaction(function () use ($request) {
                $data = $request->validated();
                $data['user_id'] = $request->user()?->id ?? 1;

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
                    'cached_until' => now()->addHours(2),
                    'raw_data' => $weatherData,
                ]);

                return $event;
            });

            return response()->json([
                'message' => 'Evento criado com sucesso e risco avaliado.',
                'data' => new EventResource($event->load('weatherReports')),
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Falha ao processar avaliação de risco do evento.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    
    public function show(Request $request, Event $event): EventResource
    {
        if ($event->user_id !== $request->user()->id) {
            abort(403, 'Acesso não autorizado a este evento.');
        }

        return new EventResource($event);
    }

    public function refresh(Request $request, Event $event): JsonResponse
    {
        if ($event->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Acesso não autorizado a este evento.'], 403);
        }

        try {
            $weatherProvider = app(OpenWeatherProvider::class);
            $riskEngine = app(RiskCalculatorService::class);

            $weatherData = $weatherProvider->getWeatherCoordinates(
                $event->latitude,
                $event->longitude
            );

            $assessment = $riskEngine->calculate($event->event_type, $weatherData);

            $report = $event->weatherReports()->create([
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

            return response()->json([
                'message' => 'Relatório climático atualizado com sucesso.',
                'data' => new EventResource($event->refresh())
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Falha ao atualizar o relatório do evento.',
                'details' => $e->getMessage()
            ], 500);
        }
    }


}