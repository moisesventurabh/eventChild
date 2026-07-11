<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Enums\RiskLevel;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_an_event_via_api_and_returns_risk_assessment()
    {
        $user = User::factory()->create();

        Http::fake([
            'api.openweathermap.org/*' => Http::response([
                'main' => ['temp' => 22.0, 'humidity' => 65],
                'wind' => ['speed' => 4.2],
                'pop' => 0.15,
                'current' => ['uvi' => 3.0]
            ], 200)
        ]);

        $payload = [
            'name' => 'Conferência de Tecnologia BH',
            'description' => 'Discutindo o futuro do desenvolvimento.',
            'city' => 'Belo Horizonte',
            'state' => 'MG',
            'country' => 'BRA',
            'latitude' => -19.9167,
            'longitude' => -43.9345,
            'event_type' => 'outdoor',
            'start_at' => now()->addDays(5)->toDateTimeString(),
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/v1/events', $payload);
        
        $response->dump();

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'event_type',
                    'weather_reports' => [
                        '*' => [
                            'risk_score',
                            'risk_level',
                            'recommendations'
                        ]
                    ]
                ]
            ]);

        $this->assertDatabaseHas('events', [
            'name' => 'Conferência de Tecnologia BH',
            'city' => 'Belo Horizonte'
        ]);
    }

    public function test_it_validates_required_fields_when_creating_an_event()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/events', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'city', 'state', 'country', 'latitude', 'longitude', 'event_type', 'start_at']);
    }

    public function test_it_can_retrieve_a_specific_event_details_with_weather_reports()
    {
        $user = User::factory()->create();
        
        // Substituído Event::create pelo uso limpo da Factory
        $event = Event::factory()->create([
            'user_id' => $user->id,
            'name' => 'Festival de Inverno',
        ]);

        // 2. Vincular um relatório climático fake a ele[cite: 3]
        $event->weatherReports()->create([
            'temperature' => 15.0,
            'feels_like' => 14.0,
            'humidity' => 80,
            'wind_speed' => 12.5,
            'rain_probability' => 40,
            'uv_index' => 1,
            'risk_score' => 50,
            'risk_level' => RiskLevel::MODERATE,
            'recommendations' => json_encode(['Levar guarda-chuva']),
            'cached_until' => now()->addHours(2),
            'raw_data' => []
        ]);

        // 3. Executar o GET na API[cite: 3]
        $response = $this->actingAs($user)
            ->getJson("/api/v1/events/{$event->id}");

        // 4. Asserções[cite: 3]
        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Festival de Inverno')
            ->assertJsonCount(1, 'data.weather_reports');
    }

    public function test_it_returns_404_if_event_not_found()
    {
        $user = User::factory()->create();

        // Tenta buscar um ID inexistente
        $response = $this->actingAs($user)
            ->getJson('/api/v1/events/99999');

        $response->assertStatus(404);
    }

}