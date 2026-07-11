<?php

namespace App\Models;

use App\Enums\EventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasUuids; // Gera o UUID automaticamente no evento 'creating' do Eloquent

    protected $fillable = [
        'name',
        'description',
        'city',
        'state',
        'country',
        'latitude',
        'longitude',
        'event_type',
        'start_at',
        'user_id'
    ];


    protected function casts(): array
    {
        return [
            'event_type' => EventType::class, // Convert string values into native Enums
            'latitude' => 'float',
            'longitude' => 'float',
            'start_at' => 'datetime',
        ];
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function weatherReports(): HasMany
    {
        return $this->hasMany(WeatherReport::class);
    }
}