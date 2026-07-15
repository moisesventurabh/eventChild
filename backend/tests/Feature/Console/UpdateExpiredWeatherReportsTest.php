<?php

namespace Tests\Feature\Console;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Enums\RiskLevel;
use Illuminate\Support\Facades\Http;
use App\Services\Weather\OpenWeatherProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateExpiredWeatherReportsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Configura a chave necessária para o OpenWeatherProvider
        config(['services.openweather.key' => 'fake-key']);

        // Registra o Singleton para resolver a dependência no container de testes
        $this->app->singleton(OpenWeatherProvider::class, function ($app) {
            return new OpenWeatherProvider('fake-key');
        });
    }

    public function test_it_applies_fallback_and_extends_cache_when_weather_api_fails()
    {
        // 1. Criação do cenário
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'user_id' => $user->id,
            'start_at' => now()->addDays(2),
        ]);

        // 2. Cria relatório expirado inicial
        $event->weatherReports()->create([
            'temperature' => 18.0,
            'feels_like' => 18.0,
            'humidity' => 70,
            'wind_speed' => 5.0,
            'rain_probability' => 10,
            'uv_index' => 2,
            'risk_score' => 15,
            'risk_level' => RiskLevel::LOW,
            'recommendations' => json_encode(['Aproveite']),
            'cached_until' => now()->subHour(),
            'raw_data' => []
        ]);

        // 3. Simula falha na API externa
        Http::fake([
            'api.openweathermap.org/*' => Http::response(['error' => 'fail'], 500)
        ]);

        // 4. Executa o comando
        $this->artisan('weather:update-expired')->assertExitCode(0);

        // 5. Validações
        // Deve ter 2 registros: o original expirado + o fallback gerado no catch
        $this->assertEquals(2, $event->weatherReports()->count());

        // Valida se o novo registro manteve os valores do anterior (fallback)
        $latestReport = $event->weatherReports()->latest()->first();
        $this->assertEquals(18.0, $latestReport->temperature);
        $this->assertEquals(18.0, $latestReport->feels_like);
    }
}