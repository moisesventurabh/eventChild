<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $latestReport = $this->weatherReports()->orderBy('id', 'desc')->first();

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'description' => $this->description,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
            'event_type' => $this->event_type,
            'start_at' => $this->start_at->toIso8601String(),
            'weather_assessment' => $latestReport ? [
                'temperature' => (float) $latestReport->temperature,
                'feels_like' => (float) $latestReport->feels_like,
                'humidity' => (int) $latestReport->humidity,
                'wind_speed' => (float) $latestReport->wind_speed,
                'rain_probability' => (int) $latestReport->rain_probability,
                'uv_index' => (float) $latestReport->uv_index,
                'risk_score' => (int) $latestReport->risk_score,
                'risk_level' => $latestReport->risk_level, // Mantém o Enum formatado
                'recommendations' => is_string($latestReport->recommendations) 
                    ? json_decode($latestReport->recommendations, true) 
                    : $latestReport->recommendations,
                'updated_at' => $latestReport->created_at->toIso8601String(),
            ] : null,
        ];
    }
}