<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Event;
use App\Enums\EventType;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Cria um usuário automaticamente se nenhum for passado
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'city' => $this->faker->city,
            'state' => 'MG',
            'country' => 'BRA',
            'latitude' => $this->faker->latitude(-22, -19),
            'longitude' => $this->faker->longitude(-45, -42),
            'event_type' => EventType::OUTDOOR, 
            'start_at' => now()->addDays($this->faker->numberBetween(1, 30)),
        ];
    }
}