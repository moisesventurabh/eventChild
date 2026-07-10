<?php

namespace App\Models;

use App\Enums\RiskLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeatherReport extends Model
{
    protected $fillable = [
        'event_id',
        'temperature',
        'feels_like',
        'humidity',
        'wind_speed',
        'rain_probability',
        'uv_index',
        'risk_score',
        'risk_level',
        'recommendations',
        'cached_until'
    ];

    protected function casts(): array
    {
        return [
            'risk_level' => RiskLevel::class,
            'recommendations' => 'array',
            'temperature' => 'float',
            'feels_like' => 'float',
            'wind_speed' => 'float',
            'uv_index' => 'float',
            'cached_until' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}