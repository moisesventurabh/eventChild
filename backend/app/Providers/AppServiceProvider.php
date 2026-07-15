<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\WeatherProviderInterface;
use App\Services\Weather\OpenWeatherProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(WeatherProviderInterface::class, function ($app) {
            return new OpenWeatherProvider(
                apiKey: config('services.openweather.key', 'mock-key'),
                baseUrl: config('services.openweather.base_url', 'https://api.openweathermap.org/data/3.0/onecall')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
